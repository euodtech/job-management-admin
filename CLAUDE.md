# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**E-FMS (Electronic Fleet Management System)** - A fleet/field service management platform for managing jobs, drivers, companies, and vehicles. Integrates with the **Traxroot** GPS tracking API for vehicle/object tracking and geozones.

The repository contains **two separate applications** sharing the same MySQL database (`Efms`):

- **`api/`** - Laravel Lumen 5.8 REST API (consumed by a Flutter mobile app)
- **`internal/`** - CodeIgniter 3 web application (admin dashboard)

## Development Environment

- **Runtime:** XAMPP on Windows (Apache + MySQL + PHP 7.1+)
- **Database:** MySQL, database name `Efms`, charset `utf8mb4`
- **Timezone:** Asia/Manila (set in `MY_Controller`)

## Common Commands

```bash
# Install API dependencies
cd api && composer install

# Run Lumen development server
cd api && php -S localhost:8000 -t public

# Run API tests
cd api && vendor/bin/phpunit

# Lumen artisan (via flipbox/lumen-generator)
cd api && php artisan <command>
```

The internal CodeIgniter app runs directly via Apache (XAMPP) - no separate dev server needed. Access at `http://localhost:8080/be-fms/internal/`.

## Architecture

### API (Lumen) - `api/`

**Routing:** All API routes are in `api/routes/web.php`, prefixed with `myapi/`. Public routes (login, forgot-password) have no middleware. Authenticated routes use the `auth` middleware.

**Authentication:** API-key based. On login, a base64-encoded random key is generated and stored in `UserLogin.ApiKey`. Authenticated requests must include `x-key` as a query parameter, validated by `Authenticate` middleware (`api/app/Http/Middleware/Authenticate.php`).

**Controllers:**
- `AuthController` - Login, logout, forgot/reset password, company check
- `JobController` - Job listing, assignment, completion (with base64 image upload), cancellation, rescheduling
- `UserController` - User profile retrieval
- `DapaController` - Alternative job CRUD endpoints

**Models** (all use `$guarded = []`, `$timestamps = false`):

| Model | Table | Primary Key |
|-------|-------|-------------|
| `UserLogin` | `UserLogin` | `UserLoginID` |
| `DriverModel` | `ListUser` | `UserID` |
| `JobModel` | `ListJob` | `JobID` |
| `JobDetailModel` | `ListJobDetail` | `ListDetailID` |
| `ListCompanyModel` | `ListCompany` | `ListCompanyID` |
| `RescheduleJobModel` | `RescheduledJob` | `RescheduledID` |
| `HistoryCancelJobModel` | `HistoryCancelJob` | `HistoryCancelJobID` |

**Key relationship:** `JobModel` has many `JobDetailModel` via `ListJobID` -> `JobID`.

**Bootstrap:** `api/bootstrap/app.php` registers middleware, service providers (Mail, LumenGenerator, Intervention Image), and loads routes.

### Internal (CodeIgniter 3) - `internal/`

**Base controller:** `MY_Controller` (`internal/application/core/MY_Controller.php`) provides page rendering methods:
- `render_page_login()` - Auth layout (header, content, footer)
- `render_page()` - Main layout (header, topbar, sidebar, content, ourjs, footer)

**Session auth:** Login sets `session->userdata('status')` to `'kusam'`. Controllers check this value in `__construct()` to enforce authentication.

**Global model:** `M_Global` (`internal/application/models/M_Global.php`) is a generic database query helper used across all controllers. Use `globalquery($sql, $binds)` with parameterized queries.

**Controllers:**
- `Auth` - Login/logout with auto-migration of plaintext passwords to bcrypt
- `Home` - Dashboard with driver/job statistics
- `Job` - Job CRUD, reschedule management, job summary
- `User`, `Company`, `Customer` - Entity management
- `Map`, `Vehicle` - GPS tracking integration
- `ReportDriver`, `ReportJob`, `ReportCustomer` - Reporting

**Traxroot API integration:** Controllers under `internal/application/controllers/API/V1/` proxy requests to the Traxroot fleet tracking service. Resources: ApiToken, Drivers, Geozones, Objects, ObjectsStatus, ObjectsMerge, Profile, Users.

**Routes:** Defined in `internal/application/config/routes.php`. Default controller is `auth`.

## Domain Concepts

**Job statuses:** `null` = unassigned, `1` = active/assigned, `2` = finished, `3` = ongoing

**Job types:** `1` = Line Interrupt, `2` = Reconnection, `3` = Short Circuit, `4` = Disconnection

**Company subscriptions:** `1` = Basic, other = Pro

**User roles:** Role `1` = superadmin (sees all companies' data). Other roles are scoped to their own `CompanyID`.

## API Response Format

The Lumen API uses a consistent JSON response pattern:
```json
{
  "Success": true|false,
  "Message": "...",
  "Data": { ... }
}
```

## File Storage

Job completion photos (base64-encoded from mobile app) are saved to `api/storage/app/finished_jobs/` with naming pattern `job_{id}_{timestamp}_{index}.{ext}`.
