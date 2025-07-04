# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

HomeBaze is a comprehensive real estate management platform built with Laravel 12 and Filament v3, designed specifically for the Nigerian property market. It features a multi-panel architecture serving different user types: Admin, Agency, Agent, Property Owner (Landlord), and Tenant panels.

## Development Commands

### Essential Commands
- `composer dev` - Start development environment (runs server, queue, logs, and Vite concurrently)
- `php artisan serve` - Start Laravel development server
- `php artisan queue:listen --tries=1` - Start queue worker for background jobs
- `php artisan pail --timeout=0` - Start real-time log monitoring
- `npm run dev` - Start Vite development server for assets
- `npm run build` - Build production assets

### Testing
- `composer test` - Run full test suite (clears config and runs tests)
- `php artisan test` - Run tests directly
- `vendor/bin/pest` - Run Pest tests directly

### Database Management
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Run database seeders
- `php artisan migrate:fresh --seed` - Fresh migration with seeding

### Code Quality
- `vendor/bin/pint` - Format code with Laravel Pint

## Architecture Overview

### Multi-Panel Structure
The application uses Filament's multi-panel architecture with 5 distinct panels:

1. **Admin Panel** (`/admin`) - Global platform administration
   - Provider: `app/Providers/Filament/AdminPanelProvider.php`
   - Resources in: `app/Filament/Resources/`

2. **Agency Panel** (`/agency`) - Real estate agencies with tenant isolation
   - Provider: `app/Providers/Filament/AgencyPanelProvider.php`
   - Resources in: `app/Filament/Agency/Resources/`

3. **Agent Panel** (`/agent`) - Independent agents
   - Provider: `app/Providers/Filament/AgentPanelProvider.php`
   - Resources in: `app/Filament/Agent/Resources/`

4. **Landlord Panel** (`/landlord`) - Property owners
   - Provider: `app/Providers/Filament/LandlordPanelProvider.php`
   - Resources in: `app/Filament/Landlord/Resources/`

5. **Tenant Panel** (`/tenant`) - Property seekers
   - Provider: `app/Providers/Filament/TenantPanelProvider.php`
   - Resources in: `app/Filament/Tenant/Resources/`

### Key Models and Relationships
- **Property** - Central model with relationships to Agency, Agent, PropertyOwner, PropertyType, PropertySubtype, Area, and PropertyFeature
- **Agency** - Multi-tenant model for real estate companies
- **User** - Extended with user types (admin, agency_owner, agent, landlord, tenant)
- **PropertyInquiry**, **PropertyViewing**, **Review** - Engagement models
- **State**, **City**, **Area** - Geographic hierarchy for Nigerian locations

### Media Management
Uses Spatie Media Library with collections:
- `gallery` - Property images
- `featured` - Featured property image
- `floor_plans` - Floor plan documents
- `documents` - Property documents
- `videos` - Property videos

### Authentication & Authorization
- Laravel Breeze for authentication
- Spatie Laravel Permission for role-based access
- Custom middleware for agency scoping: `ApplyAgencyScopes`, `EnsureUserBelongsToAgency`

## Database Structure

### Geographic Data (Nigerian Market)
- 37 States (all Nigerian states + FCT)
- 57 Cities (major urban centers)
- 63 Areas (popular neighborhoods)
- Complete hierarchical relationships: State → City → Area

### Property System
- Property types: Apartment, House, Land, Commercial, Industrial, Mixed-use
- 54 Property subtypes (Studio, 1BR, 2BR, Duplex, etc.)
- 25+ Property features with categorization
- Comprehensive pricing structure (rent, sale, service charges, deposits)

## Testing Configuration

- Uses Pest PHP for testing framework
- Test configuration in `phpunit.xml`
- SQLite in-memory database for testing
- Separate test suites: Unit and Feature
- Test files in `tests/Feature/` and `tests/Unit/`

## Key Development Considerations

### Multi-Tenancy
Agency panel uses agency-based tenancy. When working with agency-scoped resources, ensure proper tenant context is maintained through middleware and scopes.

### Filament Resources
Each panel has its own set of resources. When creating or modifying resources, ensure they're placed in the correct panel directory and registered with the appropriate panel provider.

### Nigerian Localization
The platform is specifically designed for the Nigerian market. All geographic data, property types, and business logic reflect Nigerian real estate practices.

### Media Handling
All property media should be handled through the Spatie Media Library. Use the predefined collections and conversions for consistency.

### Performance
The application includes proper indexing and eager loading patterns. When adding new queries, consider performance impact and use appropriate relationships and indexing.