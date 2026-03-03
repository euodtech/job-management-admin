# E-FMS API Reference (for Flutter App)

## Base URL

```
https://jms.euodoo.com.ph/api/myapi/
```

All endpoints below are relative to this base. Example: `POST /myapi/login` = `POST https://jms.euodoo.com.ph/api/myapi/login`

---

## Authentication

- **Method**: API key, obtained on login
- **Header**: `X-API-Key: <your_api_key>` (required on all protected endpoints)
- On successful login the server returns an `ApiKey` string. Store it securely and send it as the `X-API-Key` header on every subsequent request.
- On logout, the key is invalidated server-side.

---

## Standard Response Format

Every endpoint returns JSON in this shape:

```json
{
  "Success": true | false,
  "Message": "Human-readable message",
  "Data": { ... }
}
```

On validation errors (422), an `Errors` object is also included:

```json
{
  "Success": false,
  "Message": "Validation failed",
  "Errors": {
    "email": ["The email field is required."]
  }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad request / invalid state |
| 401 | Unauthorized (missing/invalid API key or wrong password) |
| 403 | Forbidden (access denied, wrong company) |
| 404 | Not found |
| 422 | Validation failed |
| 429 | Rate limited (retry after `Retry-After` header seconds) |
| 500 | Server error |
| 502 | External service error (Traxroot) |

---

## Domain Concepts

### Job Statuses

| Status | Meaning |
|--------|---------|
| `null` | Unassigned (available for drivers to pick up) |
| `1` | Active / assigned to a driver |
| `2` | Finished (completed with photos) |
| `3` | Rescheduled (pending admin approval) |

### Job Types

| TypeJob | Name |
|---------|------|
| `1` | Line Interrupt |
| `2` | Reconnection |
| `3` | Short Circuit |
| `4` | Disconnection |

### Company Subscriptions

| CompanySubscribe | Label |
|------------------|-------|
| `1` | Basic |
| other | Pro |

### Reschedule Statuses

| StatusApproved | Label |
|----------------|-------|
| `1` | Pending |
| `2` | Approved |
| `3` | Rejected |

---

## PUBLIC ENDPOINTS (no auth required)

### POST /myapi/login

Authenticate a driver and get an API key.

**Rate limit**: 5 attempts per minute.

**Request body**:
```json
{
  "email": "driver@company.com",      // required, valid email
  "password": "password123",           // required, min 5 chars
  "firebasetoken": "fcm_device_token"  // optional, for push notifications
}
```

**Success response (200)**:
```json
{
  "Success": true,
  "Data": {
    "UserID": 123,
    "ApiKey": "base64_encoded_key",
    "Company": "Company Name",
    "CompanyID": 456,
    "CompanyType": 1,
    "CompanyLabel": "Basic",
    "HasTraxroot": true,
    "UserRole": 1,
    "CompanyLogo": "https://jms.euodoo.com.ph/api/myapi/company-logo/logo.png"
  }
}
```

**Error responses**:
- `401` — `"Email Not Found, Please Create Account"` or `"Incorrect Password"`
- `422` — Validation errors

---

### POST /myapi/forgot-password

Request a password reset email.

**Rate limit**: 3 attempts per 5 minutes.

**Request body**:
```json
{
  "email": "driver@company.com"  // required, valid email
}
```

**Response (200)**: Always returns success (prevents email enumeration):
```json
{
  "Success": true,
  "Message": "If your email is registered, you will receive a password reset link."
}
```

---

### GET /myapi/company-logo/{filename}

Retrieve a company logo image. Returns raw image bytes with appropriate Content-Type.

---

## PROTECTED ENDPOINTS (X-API-Key header required)

### GET /myapi/get-user/{userID}

Get a driver's profile.

**Response (200)**:
```json
{
  "Success": true,
  "Data": {
    "Fullname": "John Doe",
    "Email": "john@example.com",
    "PhoneNumber": "+1234567890"
  }
}
```

**Errors**: `404` if user not found.

---

### GET /myapi/get-job

List all **unassigned** jobs for the authenticated user's company (available for pickup).

**Response (200)**:
```json
{
  "Success": true,
  "Data": [
    {
      "JobID": 789,
      "JobName": "Line Maintenance",
      "CustomerID": 101,
      "TypeJob": "1",
      "TypeJobName": "Line Interrupt",
      "JobDate": "2026-03-01",
      "CreatedBy": "Admin Name",
      "Status": null,
      "UserID": null,
      "CompanyID": 456,
      "Notes": null
    }
  ]
}
```

Only returns jobs where `JobDate <= today` and `UserID IS NULL`. Ordered by `JobDate DESC`.

---

### POST /myapi/driver-get-job

Driver accepts/claims an available job.

**Request body**:
```json
{
  "user_id": 123,  // required
  "job_id": 789    // required
}
```

**Success (200)**:
```json
{ "Success": true, "Message": "Success Driver Get The Job" }
```

**Errors**:
- `400` — Job already assigned, driver already has an active job, or job belongs to different company
- `404` — Job not found

**Rules**: A driver can only have **one active job** (Status=1) at a time. The job must be unassigned (UserID=null, Status=null).

---

### GET /myapi/get-job-ongoing/{user_id}

Get the driver's current active and rescheduled jobs.

**Response (200)**:
```json
{
  "Success": true,
  "Data": [
    {
      "JobID": 789,
      "JobName": "Line Repair",
      "TypeJobName": "Reconnection",
      "Status": 1,
      "JobDate": "2026-03-02",
      "CreatedBy": "Admin Name",
      "RescheduleStatus": null,
      "RescheduledDateJob": null,
      "ReasonReject": null,
      "CanFinish": true
    }
  ]
}
```

Returns jobs with `Status IN (1, 3)`. The `CanFinish` field indicates whether the driver can mark this job as finished:
- `true` if Status=1 (active, no reschedule)
- `true` if reschedule was rejected (StatusApproved=3)
- `true` if reschedule was approved AND the new job date <= today
- `false` if reschedule is pending (StatusApproved=1)
- `false` if reschedule approved but new date is in the future

---

### GET /myapi/get-job-by-user/{userID}

List all **finished** jobs for a specific driver (history).

**Response (200)**:
```json
{
  "Success": true,
  "Data": [
    {
      "JobID": 789,
      "JobName": "Line Maintenance",
      "Status": 2,
      "details": [
        {
          "ListDetailID": 1,
          "ListJobID": 789,
          "Photo": "storage/app/finished_jobs/job_789_abc123_0.png",
          "created_at": "2026-03-01 14:30:00"
        }
      ]
    }
  ]
}
```

**Errors**: `403` if the target user is not in the same company as the authenticated user.

---

### POST /myapi/finished-job

Mark a job as complete with photos.

**Request body (JSON)**:
```json
{
  "job_id": 789,                                       // required, numeric
  "notes": "Job completed successfully",               // optional
  "images": [                                          // required, non-empty array
    "data:image/png;base64,iVBORw0KGgo...",
    "data:image/jpeg;base64,/9j/4AAQSkZJ..."
  ]
}
```

**Image requirements**:
- Must be base64-encoded with data URI prefix: `data:image/{type};base64,{data}`
- Accepted MIME types: `image/jpeg`, `image/png`, `image/jpg`
- At least one image is required

**Success (200)**:
```json
{ "Success": true, "Message": "This Job is finished" }
```

**Errors**:
- `400` — `"Job is not assigned"` (Status=null), `"Job is already finished"` (Status=2), `"Reschedule is pending admin approval"`, `"Cannot finish until DD MMM YYYY"` (approved reschedule with future date)
- `400` — `"Invalid base64 image at position N"` or `"File at position N is not a valid image"`
- `404` — Job not found or access denied

---

### POST /myapi/reschedule-job/{jobID}

Request to reschedule a job to a later date.

**Request body**:
```json
{
  "notes": "Cannot complete due to weather",  // required
  "new_date": "2026-03-05"                    // required, must be > current JobDate
}
```

**Success (200)**:
```json
{
  "Success": true,
  "Message": "Success Request Reschedule Job",
  "Data": { "RescheduledID": 456 }
}
```

**Errors**:
- `400` — `"Job is not in a valid state for rescheduling"` (Status must be 1 or 3)
- `400` — `"Request Date must be greater than the current job date."`
- `400` — `"A reschedule request is already pending approval."`
- `404` — Job not found

---

### GET /myapi/reschedule-status/{jobID}

Check the latest reschedule request status.

**Response (200)**:
```json
{
  "Success": true,
  "Data": {
    "RescheduledID": 456,
    "StatusApproved": 1,
    "StatusLabel": "pending",
    "RescheduledDateJob": "2026-03-05",
    "ReasonReject": null,
    "CanFinish": false
  }
}
```

Returns `"Data": null` if no reschedule exists.

---

### POST /myapi/cancel-job/{jobID}

Cancel (unassign) a job, returning it to the available pool.

**Request body**:
```json
{
  "reason": "Driver unavailable"  // optional
}
```

**Success (200)**:
```json
{ "Success": true, "Message": "Success Cancel Job" }
```

---

### POST /myapi/change-password

Change the authenticated user's password.

**Request body**:
```json
{
  "current_password": "oldpass",           // required
  "new_password": "newpass123",            // required, min 8 chars
  "new_password_confirmation": "newpass123" // required, must match
}
```

**Errors**: `401` — `"Current password is incorrect"`, `422` — validation errors.

---

### POST /myapi/logout

Clear the API key and end the session.

**Request body**: Empty.

**Response (200)**:
```json
{ "Success": true, "Message": "Berhasil keluar" }
```

After logout, the API key is invalidated. All future requests with that key will return 401.

---

### GET /myapi/check-type-company/{companyID}

Check a company's subscription type.

**Response (200)**:
```json
{ "Success": true, "CompanySubscribe": 1 }
```

---

## TRAXROOT GPS PROXY ENDPOINTS

These proxy endpoints communicate with the Traxroot fleet tracking API. Credentials are stored server-side.

### POST /myapi/traxroot/token

Get a Traxroot API token for the authenticated user's company.

**Request body**: Empty (server uses stored credentials).

**Response (200)**: Raw Traxroot token response:
```json
{
  "access_token": "bearer_token_string",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

**Errors**: `404` — Company has no Traxroot credentials. `502` — Traxroot service unavailable.

---

### GET /myapi/traxroot/objects-status?token={traxroot_token}

Get vehicle/object locations and status.

**Query params**: `token` (required) — the Traxroot token from the token endpoint above.

**Response (200)**: Raw Traxroot JSON with vehicle locations.

---

### GET /myapi/traxroot/geozones?token={traxroot_token}

Get geofence/geozone definitions.

**Query params**: `token` (required) — the Traxroot token.

**Response (200)**: Raw Traxroot JSON with geozone data.

---

## TYPICAL FLUTTER APP FLOW

```
1. LOGIN
   POST /myapi/login { email, password, firebasetoken }
   -> Save ApiKey to secure storage
   -> Save UserID, CompanyID, CompanyType, HasTraxroot

2. VIEW AVAILABLE JOBS
   GET /myapi/get-job  (X-API-Key header)
   -> Display unassigned jobs for driver's company

3. ACCEPT A JOB
   POST /myapi/driver-get-job { user_id, job_id }
   -> Job becomes active (Status=1)

4. VIEW ACTIVE JOB
   GET /myapi/get-job-ongoing/{user_id}
   -> Show current job with CanFinish flag

5a. FINISH JOB (if CanFinish=true)
    POST /myapi/finished-job { job_id, notes, images[] }
    -> Send base64 photos, job becomes finished (Status=2)

5b. RESCHEDULE JOB (if needed)
    POST /myapi/reschedule-job/{jobID} { notes, new_date }
    -> Job moves to Status=3, wait for admin approval
    GET /myapi/reschedule-status/{jobID}
    -> Poll for approval (check CanFinish)

5c. CANCEL JOB (if needed)
    POST /myapi/cancel-job/{jobID} { reason }
    -> Job returns to unassigned pool

6. VIEW JOB HISTORY
   GET /myapi/get-job-by-user/{userID}
   -> Finished jobs with photo details

7. GPS TRACKING (if HasTraxroot=true)
   POST /myapi/traxroot/token -> get token
   GET /myapi/traxroot/objects-status?token=... -> vehicle locations
   GET /myapi/traxroot/geozones?token=... -> geofences

8. LOGOUT
   POST /myapi/logout
   -> Clear stored ApiKey
```
