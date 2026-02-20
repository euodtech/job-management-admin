# E-FMS Flutter App — Traxroot Integration & API Changes Guide

This document describes the backend API contract for integrating Traxroot GPS tracking into the Flutter mobile app. The backend handles all Traxroot credential management server-side — the Flutter app never sees or stores Traxroot usernames/passwords.

---

## 1. Authentication Change (IMPORTANT)

All authenticated API requests must now send the API key via **HTTP header** instead of query parameter.

### Before (deprecated, still works temporarily):
```dart
final response = await http.get(
  Uri.parse('$baseUrl/myapi/get-job?x-key=$apiKey'),
);
```

### After (required):
```dart
final response = await http.get(
  Uri.parse('$baseUrl/myapi/get-job'),
  headers: {'X-API-Key': apiKey},
);
```

**Apply this to ALL authenticated requests**, not just Traxroot endpoints. The old `x-key` query parameter still works for backward compatibility but will be removed in a future update.

---

## 2. Login Response (Updated)

**Endpoint:** `POST {baseUrl}/myapi/login`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword",
  "firebasetoken": "firebase_token_here"
}
```

**Successful Response (200):**
```json
{
  "Success": true,
  "Data": {
    "UserID": 123,
    "ApiKey": "base64_encoded_api_key",
    "Company": "Company Name",
    "CompanyID": 5,
    "CompanyType": 1,
    "CompanyLabel": "Basic",
    "HasTraxroot": true,
    "UserRole": 2,
    "CompanyLogo": "http://host/be-fms/internal/assets/dist/img/company_logo/logo.png"
  }
}
```

### New field: `HasTraxroot` (boolean)
- `true` = this company has Traxroot GPS tracking configured. Show the map/tracking features.
- `false` = no Traxroot configured. Hide or disable GPS tracking features.

**Store `HasTraxroot` alongside the other login data in your local state/storage.** Use it to conditionally show/hide the vehicle tracking UI.

---

## 3. Traxroot Proxy Endpoints

All three endpoints require authentication via `X-API-Key` header. The backend automatically looks up the user's company and uses the company's stored (encrypted) Traxroot credentials — the Flutter app just calls these endpoints.

### 3.1. Get Traxroot Token

The Flutter app must first obtain a Traxroot access token before calling the other endpoints.

**Endpoint:** `POST {baseUrl}/myapi/traxroot/token`

**Request:**
```dart
final response = await http.post(
  Uri.parse('$baseUrl/myapi/traxroot/token'),
  headers: {'X-API-Key': apiKey},
);
```

No request body needed. The backend fetches credentials from the database automatically based on the authenticated user's company.

**Success Response (200) — proxied directly from Traxroot:**
```json
{
  "accessToken": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Error Responses:**

| Status | Body | Meaning |
|--------|------|---------|
| 401 | `{"Status": 401, "Message": "Unauthorized"}` | Invalid or missing API key |
| 404 | `{"Success": false, "Message": "Traxroot credentials not configured for this company"}` | Company has no Traxroot account |
| 502 | `{"Success": false, "Message": "Failed to connect to Traxroot"}` | Traxroot API is unreachable |

**Important:** The `accessToken` expires. If subsequent calls return 401, request a new token.

---

### 3.2. Get Vehicle/Objects Status (Live Tracking)

Returns real-time GPS positions of all tracked vehicles.

**Endpoint:** `GET {baseUrl}/myapi/traxroot/objects-status?token={traxrootToken}`

**Request:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/myapi/traxroot/objects-status?token=$traxrootToken'),
  headers: {'X-API-Key': apiKey},
);
```

**Success Response (200) — proxied directly from Traxroot:**
```json
{
  "points": [
    {
      "trackerid": 12345,
      "lat": 14.5995,
      "lng": 120.9842,
      "speed": 45.2,
      "sat": 12
    },
    {
      "trackerid": 12346,
      "lat": 14.6010,
      "lng": 120.9900,
      "speed": 0,
      "sat": 8
    }
  ]
}
```

**Field descriptions:**

| Field | Type | Description |
|-------|------|-------------|
| `trackerid` | int | Unique GPS tracker/vehicle identifier |
| `lat` | double | Latitude coordinate |
| `lng` | double | Longitude coordinate |
| `speed` | double | Current speed (km/h). `0` = vehicle is stopped |
| `sat` | int | Number of GPS satellites. Higher = better signal |

**Error Responses:**

| Status | Body | Meaning |
|--------|------|---------|
| 400 | `{"Success": false, "Message": "Traxroot token is required"}` | Missing `token` query parameter |
| 401 | (from Traxroot) | Token expired — request a new one via `/traxroot/token` |
| 502 | `{"Success": false, "Message": "Failed to connect to Traxroot"}` | Traxroot API unreachable |

---

### 3.3. Get Geozones

Returns configured geographic zones (service areas, restricted zones, etc.).

**Endpoint:** `GET {baseUrl}/myapi/traxroot/geozones?token={traxrootToken}`

**Request:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/myapi/traxroot/geozones?token=$traxrootToken'),
  headers: {'X-API-Key': apiKey},
);
```

**Success Response (200) — proxied directly from Traxroot:**
```json
[
  {
    "id": 1001,
    "name": "Manila Service Area",
    "description": "Main service coverage zone",
    "type": 1
  },
  {
    "id": 1002,
    "name": "Warehouse Zone",
    "description": "Central warehouse perimeter",
    "type": 2
  }
]
```

**Error responses:** Same pattern as objects-status (400, 401, 502).

---

## 4. Recommended Flutter Implementation

### 4.1. Data Models

```dart
class LoginData {
  final int userId;
  final String apiKey;
  final String company;
  final int companyId;
  final int companyType;
  final String companyLabel;
  final bool hasTraxroot;  // NEW FIELD
  final int userRole;
  final String? companyLogo;

  // Parse from login response json['Data']
}

class TraxrootPoint {
  final int trackerId;
  final double lat;
  final double lng;
  final double speed;
  final int sat;

  bool get isMoving => speed > 0;

  // Parse from json in points array
}
```

### 4.2. Traxroot Service Class

```dart
class TraxrootService {
  final String baseUrl;
  final String apiKey;
  String? _traxrootToken;

  TraxrootService({required this.baseUrl, required this.apiKey});

  Map<String, String> get _headers => {'X-API-Key': apiKey};

  /// Step 1: Get Traxroot access token from our backend
  Future<String> getToken() async {
    final response = await http.post(
      Uri.parse('$baseUrl/myapi/traxroot/token'),
      headers: _headers,
    );

    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      _traxrootToken = json['accessToken'];
      return _traxrootToken!;
    } else if (response.statusCode == 404) {
      throw Exception('Traxroot not configured for this company');
    } else {
      throw Exception('Failed to get Traxroot token');
    }
  }

  /// Step 2: Get live vehicle positions
  Future<List<TraxrootPoint>> getObjectsStatus() async {
    _traxrootToken ??= await getToken();

    final response = await http.get(
      Uri.parse('$baseUrl/myapi/traxroot/objects-status?token=$_traxrootToken'),
      headers: _headers,
    );

    // Token expired — refresh and retry once
    if (response.statusCode == 401) {
      _traxrootToken = await getToken();
      return getObjectsStatus();  // retry with new token
    }

    if (response.statusCode == 200) {
      final json = jsonDecode(response.body);
      final points = json['points'] as List;
      return points.map((p) => TraxrootPoint.fromJson(p)).toList();
    } else {
      throw Exception('Failed to fetch vehicle status');
    }
  }

  /// Step 3: Get geozones
  Future<List<dynamic>> getGeozones() async {
    _traxrootToken ??= await getToken();

    final response = await http.get(
      Uri.parse('$baseUrl/myapi/traxroot/geozones?token=$_traxrootToken'),
      headers: _headers,
    );

    // Token expired — refresh and retry once
    if (response.statusCode == 401) {
      _traxrootToken = await getToken();
      return getGeozones();  // retry with new token
    }

    if (response.statusCode == 200) {
      return jsonDecode(response.body) as List;
    } else {
      throw Exception('Failed to fetch geozones');
    }
  }

  /// Call this on logout or when token is rejected
  void clearToken() {
    _traxrootToken = null;
  }
}
```

### 4.3. Typical Usage Flow

```dart
// After login, check if Traxroot is available
if (loginData.hasTraxroot) {
  final traxroot = TraxrootService(
    baseUrl: 'http://your-server.com',
    apiKey: loginData.apiKey,
  );

  // Fetch live vehicle positions
  final vehicles = await traxroot.getObjectsStatus();

  // Show on map
  for (final v in vehicles) {
    addMarker(lat: v.lat, lng: v.lng, label: 'Vehicle ${v.trackerId}');
  }

  // Optional: fetch geozones for map overlays
  final zones = await traxroot.getGeozones();
}
```

---

## 5. Error Handling Summary

All API responses follow this pattern for errors:

```json
{
  "Success": false,
  "Message": "Human-readable error description"
}
```

Validation errors (422) include an additional `Errors` field:

```json
{
  "Success": false,
  "Message": "Validation failed",
  "Errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 5 characters."]
  }
}
```

### HTTP Status Code Reference

| Code | Meaning | Action |
|------|---------|--------|
| 200 | Success | Parse response data |
| 400 | Bad request / missing params | Check request parameters |
| 401 | Unauthorized / expired token | Re-login (if API key) or refresh Traxroot token |
| 404 | Not found / not configured | Show appropriate "not available" UI |
| 422 | Validation failed | Show field-specific errors from `Errors` object |
| 500 | Server error | Show generic error, retry later |
| 502 | Traxroot API unreachable | Show "GPS service unavailable", retry later |

---

## 6. Key Reminders

1. **Never store Traxroot credentials in the Flutter app.** The backend handles all Traxroot authentication. The app only stores the E-FMS `ApiKey` from login.

2. **Send `X-API-Key` header on ALL authenticated requests**, not just Traxroot ones. This applies to every endpoint under `/myapi/` that requires auth (get-job, get-user, cancel-job, etc.).

3. **The Traxroot token (`accessToken`) expires.** When any Traxroot endpoint returns 401, discard the cached token and call `/traxroot/token` again to get a fresh one, then retry the original request.

4. **Only show Traxroot features when `HasTraxroot` is `true`** in the login response. Companies without Traxroot configured will get 404 errors on the proxy endpoints.

5. **For periodic live tracking**, poll `/traxroot/objects-status` on an interval (e.g., every 10-30 seconds). Do not re-call `/traxroot/token` each time — reuse the cached token until it expires.
