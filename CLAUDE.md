# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PavasCation is a church administration system (Sistema de Administración de Iglesia IBBP) built with Laravel 12. It manages financial tracking (envelopes/offerings), attendance records, member management, and financial commitments.

## Development Commands

### Setup (First Time)
```bash
composer run setup
```
This installs dependencies, copies `.env.example`, generates app key, runs migrations, and builds assets.

### Development Server
```bash
composer run dev
```
Runs 4 concurrent processes: Laravel server, queue worker, log viewer (Pail), and Vite dev server with hot reload.

Alternatively, run individually:
```bash
php artisan serve                    # Development server (port 8000)
php artisan queue:listen --tries=1   # Queue worker
php artisan pail --timeout=0         # Real-time logs
npm run dev                          # Vite with hot reload
```

### Testing
```bash
composer run test
```

### Database
```bash
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Fresh database with seed data
php artisan db:seed             # Seed data only
```

### Production Build
```bash
npm run build                    # Build and minify assets
```

### Code Quality
```bash
./vendor/bin/pint                # Laravel Pint linter
```

## Architecture Overview

### MVC + Service Layer Pattern

**Controllers** (`app/Http/Controllers/`): Handle HTTP requests and orchestrate business logic
- `DashboardController` - Main dashboard with charts and analytics
- `RecuentoController` - Envelope/offering management
- `RecuentoClasesController` - Classroom offerings tracking
- `AsistenciaController` - Attendance records
- `PersonaController` - Member CRUD operations
- `PromesaController` - Financial commitment tracking
- `CultoController` - Service/worship event management
- `IngresosAsistenciaController` - Financial and attendance reports

**Models** (`app/Models/`): Eloquent ORM with rich relationships
- `User` - System users with 6 roles (admin, tesorero, asistente, general, miembro, invitado)
- `Culto` - Services/worship events (parent entity for offerings, attendance, expenses)
- `Sobre` - Envelopes/offerings with auto-increment numbering per service
- `SobreDetalle` - Breakdown by category (diezmo, misiones, seminario, campamento, préstamo, construcción, micro)
- `Persona` - Church members
- `Promesa` - Financial commitments/pledges
- `Asistencia` - Detailed attendance demographics (chapel and age-grouped classes)
- `ClaseAsistencia` - Class definitions for children's attendance
- `Egreso` - Expenses/withdrawals
- `TotalesCulto` - Calculated totals per service
- `AuditLog` - Audit trail for all model changes

**Services** (`app/Services/`): Business logic encapsulation
- `CalculoTotalesCultoService` - Calculates service totals by summing envelope categories and applying expenses

**Middleware** (`app/Http/Middleware/`):
- `RoleMiddleware` - Role-based access control enforcement
- `AuditLogger` + `AuditLogMiddleware` - Automatic audit trail logging

**Traits** (`app/Traits/`):
- `Auditable` - Auto-logs create/update/delete operations on models (attach to models needing audit trails)

### Role-Based Access Control

Protected routes use `role:` middleware. Key roles:
- `admin` - Full system access (Dashboard, Recuento, Asistencia, Admin panel, User management)
- `tesorero` - Financial and reporting access (Recuento, Reports)
- `asistente` - Attendance tracking (Asistencia, Reports)
- `general`/`invitado` - View-only access to reports dashboard
- `miembro` - Member profile access only

Middleware implementation in `routes/web.php`:
```php
Route::middleware(['auth', 'role:admin,tesorero'])->group(function () {
    // Routes requiring admin or tesorero role
});
```

### Key Data Relationships

```
Culto (Service)
├── hasMany Sobres (Envelopes)
│   └── hasMany SobreDetalles (Category breakdown)
├── hasMany Asistencia (Attendance records)
├── hasMany OfrendasSueltas (Loose offerings)
├── hasMany Egresos (Expenses)
└── hasOne TotalesCulto (Calculated totals)

Persona (Member)
├── hasMany Sobres
├── hasMany Promesas (Commitments)
└── hasMany Compromisos (Commitment tracking)
```

### Financial Categories

All envelope offerings are tracked across these categories:
- `diezmo` - Tithes
- `misiones` - Missions
- `seminario` - Seminary
- `campamento` - Camp
- `prestamo` - Loan
- `construccion` - Construction
- `micro` - Micro offerings

When adding new categories, update:
1. Database migration for `sobre_detalles` table
2. `CalculoTotalesCultoService::recalcular()` method
3. Dashboard charts in `DashboardController`
4. PDF templates in `resources/views/pdfs/`

### Attendance Demographics Structure

Attendance records track:
- **Chapel**: Adults (men/women), seniors (men/women), youth (men/women), teachers
- **Classes**: Age groups (0-1, 2-6, 7-8, 9-11) with students and teachers per group
- **Special metrics**: Salvations, baptisms, visits

Classes are defined in `clase_asistencia` table and linked via `ClaseAsistencia` model.

## Configuration & Environment

### Critical Settings

**Timezone**: `America/Costa_Rica` (set in `config/app.php`)
**Locale**: Spanish (`es`)
**Database**: MySQL 8.0+ (connection details in `.env`)
**Session**: Database driver, 480-minute lifetime, closes on browser close

### Environment Variables

Key `.env` settings (see `.env.example` for template):
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3332
DB_DATABASE=ibbpavas
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## Frontend Stack

- **Blade Templates**: `resources/views/` (84+ files organized by feature)
- **TailwindCSS 4**: Custom Gemini color palette, glass effects, animations (`tailwind.config.js`)
- **Alpine.js 3.4.2**: Interactivity (form handling, toggles, modals)
- **Chart.js 4.5.1**: Dashboard analytics visualization
- **Vite 7**: Asset bundling with hot reload

Build configuration in `vite.config.js` bundles:
- `resources/css/app.css`
- `resources/js/app.js`

## Testing

Run PHPUnit tests with `composer run test` or `php artisan test`.
Test configuration in `phpunit.xml`.

## Audit Logging

Models using the `Auditable` trait automatically log all create/update/delete operations to `audit_logs` table with:
- User who performed action
- Timestamp
- Model type and ID
- Old/new values
- IP address and user agent

To add auditing to a model:
```php
use App\Traits\Auditable;

class MyModel extends Model
{
    use Auditable;
}
```

## PDF Generation

Uses `barryvdh/laravel-dompdf` for server-side PDF rendering.
Templates located in `resources/views/pdfs/`.

Generate PDFs in controllers:
```php
$pdf = PDF::loadView('pdfs.template', $data);
return $pdf->download('filename.pdf');
```

## Smart Features

**Auto-incrementing envelope numbers**: `Sobre` model automatically generates `numero_sobre` incrementing per culto.

**Dynamic total calculation**: `CalculoTotalesCultoService::recalcular($cultoId)` recalculates all financial totals for a service (called after envelope/expense changes).

**Polymorphic audit logging**: `AuditLog` model tracks changes across any model type via `model_type` and `model_id` columns.

## Common Development Patterns

### Adding a new offering category
1. Add migration to add column to `sobre_detalles` table
2. Update `CalculoTotalesCultoService::recalcular()` to include new category in calculations
3. Update dashboard charts in `DashboardController::index()`
4. Update Recuento views to include new category input field
5. Update PDF templates to display new category

### Adding a new user role
1. Update `config/auth.php` if needed
2. Add role check to `RoleMiddleware`
3. Protect routes with `role:newrole` middleware
4. Update seeder to create test user with new role

### Working with migrations
When modifying existing tables, always create a new migration rather than editing old ones in production environments.
