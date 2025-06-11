# HomeBaze Filament Multi-Tenancy Strategy (Updated with Official Documentation)

## 🎯 **Multi-Tenancy Requirements Analysis**

### **Business Context:**
HomeBaze has multiple distinct user types with different access levels:
1. **Super Admin** - Platform administrators
2. **Agency Owners** - Manage their own agencies and properties  
3. **Agency Agents** - Work under agencies, manage assigned properties
4. **Independent Agents** - Work independently, manage their own properties
5. **Property Owners (Landlords)** - Independent property owners
6. **Tenants/Seekers** - Browse properties, make inquiries

### **Tenancy Model (Based on Official Filament Guidelines):**
- **Agencies** act as tenants with automatic data isolation
- **Users belong to multiple agencies** (many-to-many relationship via pivot table)
- **Agency Agents** access agency panel with agency as current tenant
- **Independent Agents** use simple one-to-many relationship (no tenancy needed)
- **Super Admins** have global access across all tenants

---

## 🏗️ **Filament Panel Architecture (Official Implementation)**

### **Recommended Approach: Multiple Panels + Selective Tenancy**

#### **Panel Structure:**
1. **Admin Panel** (`/admin`) - Super Admin only (no tenancy)
2. **Agency Panel** (`/agency`) - Agency owners and agents (tenant-aware using Agency model)
3. **Agent Panel** (`/agent`) - Independent agents (simple one-to-many scoping)
4. **Property Owner Panel** (`/landlord`) - Independent property owners (simple scoping)
5. **Tenant Panel** (`/tenant`) - Property seekers (user-scoped, no tenancy)

#### **Tenancy Implementation:**
- **Only Agency Panel uses Filament tenancy** (users belong to multiple agencies)
- **Other panels use simple scoping** via global scopes and observers
- **Independent agents don't need tenancy** - simple user-based scoping

---

## 📊 **Panel Configuration Details (Following Official Patterns)**

### **1. Admin Panel (`/admin`) - No Tenancy**
**Purpose:** Platform-wide management
**Users:** Super Admins only
**Tenancy:** None (global access)

```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('/admin')
        ->login()
        ->colors(['primary' => Color::Blue])
        ->discoverResources(app_path('Filament/Admin/Resources'))
        ->middleware(['auth', 'admin.only'])
        ->authGuard('web');
        // No tenant() configuration - global access
}
```

### **2. Agency Panel (`/agency`) - Full Filament Tenancy**
**Purpose:** Agency-specific management
**Users:** Agency owners and their agents
**Tenancy:** Agency model with many-to-many user relationship

```php
// app/Providers/Filament/AgencyPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('agency')
        ->path('/agency')
        ->login()
        ->tenant(Agency::class)
        ->tenantRegistration(RegisterAgency::class)
        ->tenantProfile(EditAgencyProfile::class)
        ->tenantMiddleware(['auth', 'verified'], isPersistent: true)
        ->colors(['primary' => Color::Green])
        ->discoverResources(app_path('Filament/Agency/Resources'));
}
```

### **3. Agent Panel (`/agent`) - Simple One-to-Many**
**Purpose:** Independent agent management
**Users:** Independent agents only
**Tenancy:** Simple user-based scoping (no Filament tenancy)

```php
// app/Providers/Filament/AgentPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('agent')
        ->path('/agent')
        ->login()
        ->colors(['primary' => Color::Orange])
        ->discoverResources(app_path('Filament/Agent/Resources'))
        ->middleware(['auth', 'independent.agent'])
        ->authGuard('web');
        // No tenant() - uses global scopes instead
}
```

### **4. Property Owner Panel (`/landlord`)**
**Purpose:** Independent property owner management
**Users:** Property owners (non-agency)
**Features:**
- Own properties management
- Inquiry handling
- Tenant management
- Basic analytics

**Resources:**
```php
// Owner-scoped resources
PropertyOwnerPanelProvider::class
├── PropertyResource (owner properties only)
├── InquiryResource (property inquiries)
├── ViewingResource (property viewings)
├── TenantResource (current tenants)
└── EarningsResource
```

### **5. Tenant Panel (`/tenant`)**
**Purpose:** Property seeker dashboard
**Users:** Tenants and property seekers
**Features:**
- Saved properties
- Search history
- Inquiry tracking
- Viewing appointments

**Resources:**
```php
// User-scoped resources
TenantPanelProvider::class
├── SavedPropertyResource
├── SearchHistoryResource
├── InquiryResource (user inquiries)
├── ViewingResource (user viewings)
└── ProfileResource
```

---

## 🔧 **Implementation Strategy**

### **Phase 1: Core Panel Setup**
1. **Install Filament Tenancy**
   ```bash
   composer require filament/tenancy
   php artisan filament:install --panels
   ```

2. **Create Panel Providers**
   ```bash
   php artisan make:filament-panel admin
   php artisan make:filament-panel agency
   php artisan make:filament-panel agent
   php artisan make:filament-panel landlord
   php artisan make:filament-panel tenant
   ```

3. **Configure Tenancy Model**
   ```php
   // Agency model becomes the tenant
   class Agency extends Model implements TenantContract
   {
       use HasTenancy;
       
       public function getTenantName(): string
       {
           return $this->name;
       }
   }
   ```

### **Phase 2: Panel Configuration**

#### **Admin Panel Config**
```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('/admin')
        ->login()
        ->colors(['primary' => Color::Blue])
        ->discoverResources(app_path('Filament/Admin/Resources'))
        ->middleware(['auth', 'admin.only'])
        ->authGuard('web');
}
```

#### **Agency Panel Config (Tenant-Aware)**
```php
// app/Providers/Filament/AgencyPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('agency')
        ->path('/agency')
        ->login()
        ->tenant(Agency::class)
        ->tenantMiddleware(['auth', 'agency.member'])
        ->colors(['primary' => Color::Green])
        ->discoverResources(app_path('Filament/Agency/Resources'));
}
```

#### **Agent Panel Config (Independent Agents)**
```php
// app/Providers/Filament/AgentPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('agent')
        ->path('/agent')
        ->login()
        ->colors(['primary' => Color::Orange])
        ->discoverResources(app_path('Filament/Agent/Resources'))
        ->middleware(['auth', 'independent.agent'])
        ->authGuard('web');
}
```

#### **Property Owner Panel Config**
```php
// app/Providers/Filament/LandlordPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('landlord')
        ->path('/landlord')
        ->login()
        ->colors(['primary' => Color::Emerald])
        ->discoverResources(app_path('Filament/Landlord/Resources'))
        ->middleware(['auth', 'property.owner'])
        ->authGuard('web');
}
```

### **Phase 3: Resource Scoping**

#### **Independent Agent Resource Scoping**
```php
// app/Filament/Agent/Resources/PropertyResource.php
class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $agent = $user->agent; // Get agent record
        
        return parent::getEloquentQuery()
            ->where('agent_id', $user->id)
            ->where(function($query) use ($agent) {
                // Independent agents only see their own properties
                $query->whereNull('agency_id')
                      ->orWhere('agency_id', $agent->agency_id ?? null);
            });
    }
}
```

#### **Tenant-Aware Property Resource**
```php
// app/Filament/Agency/Resources/PropertyResource.php
class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('agency_id', Filament::getTenant()->id);
    }
}
```

#### **Owner-Scoped Property Resource**
```php
// app/Filament/Landlord/Resources/PropertyResource.php
class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('property_owner_id', auth()->id());
    }
}
```

---

## 🔒 **Security & Access Control**

### **Middleware Stack**
```php
// Agency Panel Middleware
'auth', 'verified', 'agency.member', 'tenant.context'

// Property Owner Panel Middleware  
'auth', 'verified', 'property.owner'

// Admin Panel Middleware
'auth', 'verified', 'admin.only'
```

### **Role-Based Access (Using Filament Shield)**
```php
// Agency context roles
- agency_owner (full agency access)
- agency_agent (limited agency access)
- agency_admin (agency management)

// Property owner roles
- property_owner (own properties)
- property_manager (managed properties)

// System roles
- super_admin (global access)
- platform_admin (platform management)
```

---

## 🎨 **UI/UX Considerations**

### **Panel Branding**
- **Admin Panel:** Blue theme, corporate feel
- **Agency Panel:** Green theme, business-focused
- **Property Owner Panel:** Orange theme, individual-focused  
- **Tenant Panel:** Purple theme, consumer-friendly

### **Navigation Structure**
Each panel will have contextual navigation appropriate to the user type.

### **Dashboard Widgets**
- **Agency Dashboard:** Properties, agents, inquiries, revenue
- **Property Owner Dashboard:** Properties, inquiries, earnings
- **Admin Dashboard:** System overview, all metrics
- **Tenant Dashboard:** Saved properties, recent searches

---

## 📈 **Database Considerations**

### **Tenant Isolation**
```php
// Agency-scoped queries
Property::where('agency_id', $currentTenant->id)

// Owner-scoped queries  
Property::where('property_owner_id', auth()->id())

// Global admin queries (no scoping)
Property::all()
```

### **Performance Optimization**
- Database indexes on tenant keys (`agency_id`, `property_owner_id`)
- Query scoping at model level
- Eager loading for tenant relationships

---

## 🚀 **Deployment Strategy**

### **Environment Configuration**
```env
# Panel URLs
APP_URL=https://homebaze.com
ADMIN_URL=https://homebaze.com/admin
AGENCY_URL=https://homebaze.com/agency
LANDLORD_URL=https://homebaze.com/landlord
TENANT_URL=https://homebaze.com/tenant
```

### **Route Structure**
```php
// routes/web.php
Route::domain('admin.homebaze.com')->group(function () {
    // Admin panel routes
});

Route::domain('agency.homebaze.com')->group(function () {
    // Agency panel routes  
});

// Or path-based:
Route::prefix('admin')->group(function () {
    // Admin routes
});
```

---

## ✅ **Recommended Implementation Order**

1. **Phase 1:** Admin Panel + basic resources
2. **Phase 2:** Agency Panel + tenancy setup
3. **Phase 3:** Property Owner Panel
4. **Phase 4:** Tenant Panel (minimal features)
5. **Phase 5:** Advanced features and optimization

This multi-tenancy approach provides:
- ✅ Clear data isolation
- ✅ Role-based access control
- ✅ Scalable architecture
- ✅ User-specific experiences
- ✅ Security compliance
- ✅ Performance optimization
