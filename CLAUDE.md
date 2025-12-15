# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Waraqa** is a Laravel 9-based web application implementing a multi-role platform with support for Users, Providers, and Admins. The application manages orders, products, wallets, and analytics with localization support and Firebase integration.

### Tech Stack
- **Backend**: Laravel 9.19
- **Database**: MySQL
- **Authentication**: Laravel Passport & Sanctum
- **Localization**: Laravel Localization (mcamara/laravel-localization)
- **Permissions**: Spatie Laravel Permission
- **Excel Handling**: Maatwebsite Excel
- **Notifications**: Firebase Cloud Messaging (Kreait)
- **Payment**: Knet (ASCIIsd/Knet)
- **Frontend Build**: Vite

## Architecture Overview

### Multi-Guard Authentication System
The application uses **two authentication guards**:
- `web`: Default guard for web-based users (registered users, providers)
- `admin`: Separate guard for administrative users

Routes are organized by guard:
- `/user/*` - User dashboard and operations (auth:web)
- `/provider/*` - Provider dashboard (auth:provider)
- `/admin/*` - Admin panel (auth:admin)

Each guard has its own routing file and middleware configuration.

### Core Directory Structure

```
app/
├── Http/
│   ├── Controllers/     # Organized by role (Admin/, User/, Provider/, Auth/)
│   ├── Middleware/      # Custom authentication and localization middleware
│   ├── Requests/        # Form request validation classes
│   └── Kernel.php       # Middleware configuration and route groups
├── Models/              # Eloquent models (User, Provider, Order, Product, etc.)
├── Services/            # Business logic (DriverLocationService, EnhancedFCMService)
├── Helpers/             # Global helpers (AppSetting.php, General.php)
├── Exports/             # Maatwebsite Excel export classes
├── Imports/             # Maatwebsite Excel import classes
├── Traits/              # Shared model/class behaviors
├── Console/             # Artisan commands
├── Exceptions/          # Custom exception handling
└── Providers/           # Service providers

routes/
├── web.php              # Web routes with locale prefix and guard-based grouping
├── api.php              # API routes (for potential mobile apps)
├── admin.php            # Admin panel routes
├── channels.php         # Broadcast channels
└── console.php          # Artisan command routes

database/
├── migrations/          # Schema changes
├── factories/           # Model factories for testing
└── seeders/             # Database seeding classes

config/
├── laravellocalization.php  # Localization configuration (critical)
├── permission.php           # Spatie permission config
├── auth.php                 # Authentication guards and providers
├── services.php             # Firebase and other service configs
└── [other standard configs]
```

### Key Models
- **User**: Platform users with wallet and order functionality
- **Provider**: Service providers with product catalog
- **Order**: Orders from users to providers
- **Product**: Products offered by providers
- **Wallet**: User financial transactions (WalletTransaction model)
- **UserDept**: User department/organizational structure
- **NoteVoucher**: Financial note voucher system
- **Warehouse**: Inventory management

### Key Features

1. **Multi-language Support**: Localized via LaravelLocalization middleware with locale in URL prefix
2. **Role-Based Access Control**: Uses Spatie Laravel Permission for granular permissions
3. **Wallet System**: Tracks user financial transactions (WalletTransaction model)
4. **Excel Import/Export**: Products, orders, and other entities support Excel operations
5. **Firebase Integration**: Push notifications and real-time features via EnhancedFCMService
6. **Knet Payment**: Payment processing through Knet gateway
7. **Location Tracking**: Driver location service for delivery tracking

## Common Development Commands

### Setup & Configuration
```bash
# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Link storage (for file uploads)
php artisan storage:link
```

### Development Server
```bash
# Start local development server
php artisan serve

# Run Vite dev server (for frontend assets)
npm run dev

# Build frontend assets for production
npm run build
```

### Database & Migrations
```bash
# Run pending migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Create new migration
php artisan make:migration create_table_name

# Reset database (careful - destroys data)
php artisan migrate:reset
php artisan migrate:refresh --seed
```

### Testing
```bash
# Run all tests
php artisan test

# Run unit tests only
php artisan test --testsuite=Unit

# Run feature tests only
php artisan test --testsuite=Feature

# Run with coverage report
php artisan test --coverage

# Run single test file
php artisan test tests/Feature/ExampleTest.php
```

### Code Quality & Linting
```bash
# Format code with Pint
./vendor/bin/pint

# Format specific file
./vendor/bin/pint app/Models/User.php
```

### Model & Code Generation
```bash
# Create model with migration and factory
php artisan make:model ModelName -mf

# Create controller
php artisan make:controller ControllerName

# Create service class
php artisan make:class Services/ServiceName

# Create request validation class
php artisan make:request NameRequest
```

### Maintenance Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize application
php artisan config:cache
php artisan route:cache
```

## Routing & Middleware Notes

### Locale Prefix Routing
All routes in `web.php` are wrapped in a locale prefix group:
```php
Route::group(['prefix' => LaravelLocalization::setLocale(), ...], function () {
    // Routes accessible as /en/user/dashboard, /ar/user/dashboard, etc.
});
```

### Middleware Groups
- `web`: Session, CSRF, cookie handling, localization
- `api`: API throttling, localization headers
- `admin`: Admin-specific middleware

### Custom Middleware
- `SetLocale`: Sets locale from query/session
- `SetLocaleFromHeader`: Sets locale from Accept-Language header
- Localization-specific middleware from mcamara/laravel-localization

## Database Considerations

### Localized Content
The application uses Spatie Laravel Translatable for multilingual model attributes. Check `config/laravellocalization.php` for supported locales.

### Relationships
Key relationships to understand:
- User has many Orders, WalletTransactions
- Provider has many Products
- Order has many OrderProducts (pivot)
- Product belongs to Provider and Category

## Authentication Flow

1. Users log in via `/login` endpoint (UserAuthController)
2. Guards determine access to `/user/*` (web guard) or `/admin/*` (admin guard)
3. Permissions enforced via Spatie Laravel Permission
4. Sessions managed with file/database drivers

## Important Configuration Files

- `.env`: Environment variables (DB, MAIL, FIREBASE credentials, etc.)
- `config/auth.php`: Guard and provider definitions
- `config/laravellocalization.php`: Supported languages and locales
- `phpunit.xml`: Test configuration

## Helper Functions

Located in `app/Helpers/`:
- **General.php**: Basic utility functions
- **AppSetting.php**: Application-wide settings (autoloaded in composer.json)

These are globally available via PSR-4 autoloading.

## Export & Import System

The application supports Excel operations via Maatwebsite Excel:
- Export classes in `app/Exports/`
- Import classes in `app/Imports/`
- Used for bulk product/order operations

## External Service Integration

### Firebase (Kreait)
- FCM push notifications via EnhancedFCMService
- Configuration in `.env` with `FIREBASE_*` keys

### Knet Payment Gateway
- Integrated via asciisd/knet package
- Receipt handling in routes

### Google API Client
- Used for potential Google services integration
- Configuration in `services.php`

## Common Patterns

### Service Classes
Business logic should be abstracted to service classes in `app/Services/`. Examples:
- DriverLocationService: Location tracking
- EnhancedFCMService: Firebase notifications

### Form Validation
Use form request classes in `app/Http/Requests/`. Examples:
- LoginRequest
- CustomerRequest

### Model Scopes
Define query scopes directly on models for reusable filtering logic.

### Traits
Shared functionality between models/classes go in `app/Traits/`.

## Testing Guidelines

- Unit tests in `tests/Unit/`
- Feature tests in `tests/Feature/`
- Use database transactions in tests to avoid data pollution
- Test configuration in `phpunit.xml` includes environment variables for testing