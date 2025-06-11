# HomeBaze Agent Flexibility Model

## 🎯 **Agent Business Model Overview**

### **Why Agent Flexibility Matters:**
In Nigerian real estate, agents operate in various business structures:
- **Agency Employees** - Work under established agencies
- **Independent Agents** - Freelance real estate professionals  
- **Hybrid Agents** - Start independent, later join agencies
- **Franchise Agents** - Independent but use agency branding

## 🔄 **Agent Types & Panel Access**

### **1. Agency Agents**
**Database:** `agents.agency_id = [agency_id]`
**Panel Access:** Agency Panel (`/agency`) 
**Tenant Context:** Agency-scoped
**Features:**
- Share leads with agency team
- Agency branding and resources
- Commission sharing with agency
- Agency-supervised activities

### **2. Independent Agents**  
**Database:** `agents.agency_id = NULL`
**Panel Access:** Agent Panel (`/agent`)
**Tenant Context:** Self-scoped
**Features:**
- Own brand and marketing
- Full commission retention
- Direct client relationships
- Independent business operations

## 🏗️ **Database Design Support**

### **Updated Agents Table:**
```sql
CREATE TABLE agents (
    id PRIMARY KEY,
    user_id FOREIGN KEY (users.id),
    agency_id FOREIGN KEY (agencies.id) NULLABLE, -- Key change!
    agent_type ENUM('agency_agent', 'independent_agent') DEFAULT 'independent_agent',
    specializations JSON NULLABLE,
    commission_rate DECIMAL NULLABLE,
    is_active BOOLEAN DEFAULT true,
    hire_date DATETIME NULLABLE, -- For agency agents only
    -- performance metrics
    total_properties INTEGER DEFAULT 0,
    total_sales INTEGER DEFAULT 0,
    total_rentals INTEGER DEFAULT 0,
    rating_average DECIMAL NULLABLE,
    total_ratings INTEGER DEFAULT 0,
    timestamps
);
```

### **Property Assignment Logic:**
```sql
-- Properties can be assigned to:
properties.agent_id = [user_id] -- Direct agent assignment
properties.agency_id = [agency_id] -- Agency assignment (any agent can handle)

-- Query Logic:
-- Agency agents see: agency properties + their own assignments
-- Independent agents see: only their own assignments
```

## 🎛️ **Panel Routing & Access**

### **User Login Flow:**
```php
// After authentication, redirect based on user type:
if ($user->isAdmin()) {
    return redirect('/admin');
} elseif ($user->agent && $user->agent->agency_id) {
    return redirect('/agency'); // Agency agent
} elseif ($user->agent && !$user->agent->agency_id) {
    return redirect('/agent'); // Independent agent  
} elseif ($user->isPropertyOwner()) {
    return redirect('/landlord');
} else {
    return redirect('/tenant'); // Property seekers
}
```

### **Panel Access Control:**
```php
// Agency Panel Middleware
Route::middleware(['auth', 'verified', 'agency.member'])->group(function () {
    // Only users with agent.agency_id can access
});

// Independent Agent Panel Middleware  
Route::middleware(['auth', 'verified', 'independent.agent'])->group(function () {
    // Only users with agent.agency_id = NULL can access
});
```

## 🔄 **Agent Status Transitions**

### **Independent → Agency Agent:**
```php
// When an independent agent joins an agency
$agent = Auth::user()->agent;
$agent->update([
    'agency_id' => $selectedAgency->id,
    'agent_type' => 'agency_agent',
    'hire_date' => now(),
]);

// Properties remain with agent but gain agency context
Property::where('agent_id', $agent->user_id)
    ->update(['agency_id' => $selectedAgency->id]);
```

### **Agency Agent → Independent:**
```php
// When an agency agent becomes independent
$agent = Auth::user()->agent;
$agent->update([
    'agency_id' => null,
    'agent_type' => 'independent_agent', 
    'hire_date' => null,
]);

// Properties lose agency context but remain with agent
Property::where('agent_id', $agent->user_id)
    ->update(['agency_id' => null]);
```

## 📊 **Resource Scoping Examples**

### **Agency Panel - Property Resource:**
```php
class PropertyResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $currentTenant = Filament::getTenant(); // Current agency
        
        return parent::getEloquentQuery()
            ->where('agency_id', $currentTenant->id)
            ->orWhere(function($query) use ($currentTenant) {
                // Include properties assigned to agency agents
                $query->whereHas('agent.user', function($userQuery) use ($currentTenant) {
                    $userQuery->whereHas('agent', function($agentQuery) use ($currentTenant) {
                        $agentQuery->where('agency_id', $currentTenant->id);
                    });
                });
            });
    }
}
```

### **Independent Agent Panel - Property Resource:**
```php
class PropertyResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('agent_id', auth()->id())
            ->whereNull('agency_id'); // Only independent properties
    }
}
```

## 🎨 **UI/UX Considerations**

### **Panel Branding:**
- **Agency Panel:** Agency logo, colors, branding
- **Agent Panel:** Personal branding, customizable theme
- **Status Indicator:** Clear indication of agent type

### **Dashboard Widgets:**
- **Agency Dashboard:** Team performance, shared leads, agency metrics
- **Agent Dashboard:** Personal performance, individual leads, earnings

### **Navigation Differences:**
- **Agency Panel:** Team management, shared resources, agency settings
- **Agent Panel:** Personal profile, individual tools, independent features

## 🔒 **Security & Data Isolation**

### **Data Access Rules:**
1. **Agency Agents** can see:
   - All agency properties
   - Agency team members
   - Shared agency resources
   - Personal assignments

2. **Independent Agents** can see:
   - Only their own properties  
   - Their own clients
   - Personal resources only
   - No agency data

### **Permission Levels:**
```php
// Filament Shield permissions
'view_agency_properties' => agency agents only
'view_team_members' => agency agents only  
'manage_agency_settings' => agency owners only
'view_own_properties' => all agents
'manage_own_profile' => all agents
```

## ✅ **Benefits of This Model**

### **For Agents:**
- **Career Flexibility** - Start independent, join agency later
- **Business Growth** - Scale from solo to team operations
- **Data Portability** - Properties move with agent status changes

### **For Agencies:**
- **Talent Acquisition** - Recruit successful independent agents
- **Scalable Operations** - Grow team organically
- **Performance Tracking** - Clear metrics for agent contributions

### **For Platform:**
- **Market Coverage** - Support all business models
- **User Retention** - Agents don't need to leave platform
- **Revenue Growth** - Multiple monetization paths

This flexible agent model perfectly matches Nigerian real estate market dynamics while providing clear technical implementation paths.
