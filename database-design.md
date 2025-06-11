# HomeBaze Database Design

## Overview
Comprehensive database schema for Nigerian real estate platform with multi-tenancy support for agencies, agents, and property management.

## Implementation Phases (Laravel Migration + Model Convention)

### 🚀 **Phase 1: Foundation Tables** (Migrations + Models)
**Order: Create tables with no foreign key dependencies first**

1. **States** - ✅ **Needs Model** (State.php)
   - Data seeding required (36 Nigerian states)
   - Relationships: hasMany cities

2. **Property Types** - ✅ **Needs Model** (PropertyType.php)
   - Data seeding required (Apartment, House, Land, Commercial, etc.)
   - Relationships: hasMany properties, hasMany subtypes

3. **Property Features** - ✅ **Needs Model** (PropertyFeature.php)
   - Data seeding required (Swimming Pool, Generator, Security, etc.)
   - Relationships: belongsToMany properties

### 🏗️ **Phase 2: Location Hierarchy** (Migrations + Models)
**Order: Build location hierarchy from top to bottom**

4. **Cities** - ✅ **Needs Model** (City.php)
   - Data seeding required (Major Nigerian cities)
   - Relationships: belongsTo state, hasMany areas

5. **Areas** - ✅ **Needs Model** (Area.php)
   - Data seeding required (Popular areas in major cities)
   - Relationships: belongsTo city, hasMany properties

### 👥 **Phase 3: User System Enhancement** (Migration Only + Model Extension)
**Order: Extend existing user system and add profiles**

6. **Modify Users Table** - ❌ **No New Model** (Extend existing User.php)
   - Add columns to existing users table
   - Update existing User model with new attributes

7. **User Profiles** - ✅ **Needs Model** (UserProfile.php)
   - Relationships: belongsTo user

8. **Property Subtypes** - ✅ **Needs Model** (PropertySubtype.php)
   - Data seeding required (Studio, 1BR, 2BR, Duplex, etc.)
   - Relationships: belongsTo propertyType, hasMany properties

### 🏢 **Phase 4: Business Entities** (Migrations + Models)
**Order: Create business-related tables**

9. **Agencies** - ✅ **Needs Model** (Agency.php)
   - Relationships: belongsTo user (owner), hasMany agents, hasMany properties

10. **Agents** - ✅ **Needs Model** (Agent.php)
    - Relationships: belongsTo user, belongsTo agency, hasMany properties

### 🏠 **Phase 5: Core Property System** (Migrations + Models)
**Order: Main property management tables**

11. **Properties** - ✅ **Needs Model** (Property.php)
    - Complex relationships with all previous models
    - Main business logic model

12. **Property Feature Property** - ❌ **No Model** (Pivot Table)
    - Laravel handles this automatically via belongsToMany

### 📸 **Phase 6: Property Media & Content** (Migrations + Models)
**Order: Property-related content tables**

13. **Property Images** - ✅ **Needs Model** (PropertyImage.php)
    - Relationships: belongsTo property, belongsTo user (uploader)

### 📞 **Phase 7: Engagement & Communication** (Migrations + Models)
**Order: User interaction tables**

14. **Property Inquiries** - ✅ **Needs Model** (PropertyInquiry.php)
    - Relationships: belongsTo property, belongsTo user (inquirer)

15. **Property Viewings** - ✅ **Needs Model** (PropertyViewing.php)
    - Relationships: belongsTo property, belongsTo user (inquirer), belongsTo user (agent)

16. **Reviews** - ✅ **Needs Model** (Review.php)
    - Polymorphic relationships: reviewable (properties, agencies, agents)

17. **Saved Properties** - ✅ **Needs Model** (SavedProperty.php)
    - Relationships: belongsTo user, belongsTo property

18. **Saved Searches** - ✅ **Needs Model** (SavedSearch.php)
    - Relationships: belongsTo user

### 🔔 **Phase 8: System Features** (Mixed)
**Order: Supporting system functionality**

19. **Notifications** - ❌ **No Model** (Use Laravel's built-in notifications)
    - Laravel has built-in notification system
    - Custom notification classes instead of model

20. **Property Views** - ✅ **Needs Model** (PropertyView.php)
    - Analytics model for tracking
    - Relationships: belongsTo property, belongsTo user (nullable)

---

## Database Tables & Relationships

### 1. User Management & Authentication

#### `users` (Extended)
- id (Primary Key)
- first_name
- last_name  
- email (Unique)
- phone (Unique, **NULLABLE**)
- email_verified_at (**NULLABLE**)
- phone_verified_at (**NULLABLE**)
- password
- avatar (**NULLABLE**)
- user_type (enum: admin, agency_owner, agent, property_owner, tenant, seeker)
- status (enum: active, inactive, suspended) - Default: 'active'
- last_login_at (**NULLABLE**)
- preferences (JSON, **NULLABLE**)
- remember_token (**NULLABLE**)
- timestamps

#### `user_profiles`
- id (Primary Key)
- user_id (Foreign Key - users.id)
- date_of_birth (**NULLABLE**)
- gender (enum: male, female, other, **NULLABLE**)
- occupation (**NULLABLE**)
- monthly_income (Decimal, **NULLABLE**)
- marital_status (enum: single, married, divorced, widowed, **NULLABLE**)
- emergency_contact_name (**NULLABLE**)
- emergency_contact_phone (**NULLABLE**)
- address (**NULLABLE**)
- city_id (Foreign Key, **NULLABLE**)
- state_id (Foreign Key, **NULLABLE**)
- bio (Text, **NULLABLE**)
- social_links (JSON, **NULLABLE**)
- verification_documents (JSON, **NULLABLE**)
- is_verified (Boolean) - Default: false
- timestamps

### 2. Location Hierarchy (Nigerian Context)

#### `states`
- id (Primary Key)
- name
- code (e.g., 'LA' for Lagos)
- region (enum: north_central, north_east, north_west, south_east, south_south, south_west)
- status (enum: active, inactive) - Default: 'active'
- timestamps

#### `cities`
- id (Primary Key)
- state_id (Foreign Key - states.id)
- name
- is_capital (Boolean) - Default: false
- status (enum: active, inactive) - Default: 'active'
- timestamps

#### `areas`
- id (Primary Key)
- city_id (Foreign Key - cities.id)
- name
- type (enum: mainland, island, suburb, estate, **NULLABLE**)
- is_popular (Boolean) - Default: false
- average_rent_range (JSON, **NULLABLE**)
- description (Text, **NULLABLE**)
- status (enum: active, inactive) - Default: 'active'
- timestamps

### 3. Agency & Agent Management

#### `agencies`
- id (Primary Key)
- owner_id (Foreign Key - users.id)
- name
- registration_number (**NULLABLE**)
- email
- phone
- address
- city_id (Foreign Key - cities.id)
- state_id (Foreign Key - states.id)
- logo (**NULLABLE**)
- website (**NULLABLE**)
- description (Text, **NULLABLE**)
- commission_rate (Decimal, **NULLABLE**) - Default agency commission
- is_verified (Boolean) - Default: false
- verification_documents (JSON, **NULLABLE**)
- status (enum: active, inactive, suspended) - Default: 'active'
- subscription_plan (enum: basic, premium, enterprise) - Default: 'basic'
- subscription_expires_at (**NULLABLE**)
- total_properties (Integer) - Default: 0
- total_agents (Integer) - Default: 0
- rating_average (Decimal, **NULLABLE**)
- total_ratings (Integer) - Default: 0
- timestamps

#### `agents`
- id (Primary Key)
- user_id (Foreign Key - users.id)
- agency_id (Foreign Key - agencies.id, **NULLABLE**) - Null for independent agents
- employee_id (**NULLABLE**)
- agent_type (enum: agency_agent, independent_agent) - Default: 'independent_agent'
- specializations (JSON, **NULLABLE**) - property types they specialize in
- commission_rate (Decimal, **NULLABLE**) - Individual agent commission
- is_active (Boolean) - Default: true
- hire_date (**NULLABLE**) - For agency agents
- total_properties (Integer) - Default: 0
- total_sales (Integer) - Default: 0
- total_rentals (Integer) - Default: 0
- rating_average (Decimal, **NULLABLE**)
- total_ratings (Integer) - Default: 0
- timestamps

### 4. Property Management

#### `property_types`
- id (Primary Key)
- name (e.g., Apartment, House, Land, Commercial)
- slug
- description (Text, **NULLABLE**)
- icon (**NULLABLE**)
- is_active (Boolean) - Default: true
- sort_order (Integer) - Default: 0
- timestamps

#### `property_subtypes`
- id (Primary Key)
- property_type_id (Foreign Key - property_types.id)
- name (e.g., Studio, 1BR, 2BR, Duplex, etc.)
- description (Text, **NULLABLE**)
- is_active (Boolean) - Default: true
- sort_order (Integer) - Default: 0
- timestamps

#### `properties`
- id (Primary Key)
- title
- slug
- description (Text)
- property_type_id (Foreign Key - property_types.id)
- property_subtype_id (Foreign Key - property_subtypes.id, **NULLABLE**)
- purpose (enum: rent, sale, lease)
- status (enum: available, rented, sold, maintenance, draft) - Default: 'draft'
-
- **Location**
- address
- area_id (Foreign Key - areas.id)
- city_id (Foreign Key - cities.id)
- state_id (Foreign Key - states.id)
- latitude (Decimal, **NULLABLE**)
- longitude (Decimal, **NULLABLE**)
-
- **Ownership**
- property_owner_id (Foreign Key - users.id) - The actual property owner
- agency_id (Foreign Key - agencies.id, **NULLABLE**) - Managing agency
- agent_id (Foreign Key - users.id, **NULLABLE**) - Assigned agent
- is_featured (Boolean) - Default: false
- is_verified (Boolean) - Default: false
-
- **Property Details**
- bedrooms (Integer, **NULLABLE**)
- bathrooms (Integer, **NULLABLE**)
- toilets (Integer, **NULLABLE**)
- floor_area (Decimal, **NULLABLE**) - in sqm
- land_area (Decimal, **NULLABLE**) - in sqm for houses/land
- floors (Integer, **NULLABLE**)
- year_built (Year, **NULLABLE**)
- furnishing_status (enum: furnished, semi_furnished, unfurnished, **NULLABLE**)
- parking_spaces (Integer, **NULLABLE**)
-
- **Pricing**
- price (Decimal)
- price_negotiable (Boolean) - Default: false
- service_charge (Decimal, **NULLABLE**) - annual
- caution_fee (Decimal, **NULLABLE**)
- legal_fee (Decimal, **NULLABLE**)
- agency_fee (Decimal, **NULLABLE**)
- rent_period (enum: yearly, monthly, **NULLABLE**) - for rentals
-
- **Media & SEO**
- featured_image (**NULLABLE**)
- virtual_tour_url (**NULLABLE**)
- video_url (**NULLABLE**)
- meta_title (**NULLABLE**)
- meta_description (Text, **NULLABLE**)
-
- **Stats & Management**
- views_count (Integer) - Default: 0
- inquiries_count (Integer) - Default: 0
- bookings_count (Integer) - Default: 0
- published_at (**NULLABLE**)
- expires_at (**NULLABLE**)
- last_updated_by (Foreign Key - users.id, **NULLABLE**)
- timestamps

#### `property_features`
- id (Primary Key)
- name (e.g., Swimming Pool, Generator, Security, etc.)
- icon (**NULLABLE**)
- category (enum: security, utilities, leisure, structural)
- is_active (Boolean) - Default: true
- sort_order (Integer) - Default: 0
- timestamps

#### `property_feature_property` (Pivot Table)
- property_id (Foreign Key - properties.id)
- property_feature_id (Foreign Key - property_features.id)
- value (**NULLABLE**) - for features with values like "2 Generators"
- timestamps

### 5. Media Management

#### `property_images`
- id (Primary Key)
- property_id (Foreign Key - properties.id)
- image_path
- alt_text (**NULLABLE**)
- is_featured (Boolean) - Default: false
- sort_order (Integer) - Default: 0
- uploaded_by (Foreign Key - users.id)
- timestamps

### 6. Property Inquiries & Bookings

#### `property_inquiries`
- id (Primary Key)
- property_id (Foreign Key - properties.id)
- inquirer_id (Foreign Key - users.id, **NULLABLE**) - Null for anonymous inquiries
- inquirer_name
- inquirer_email
- inquirer_phone
- message (Text)
- preferred_viewing_date (**NULLABLE**)
- status (enum: new, contacted, scheduled, viewed, closed) - Default: 'new'
- responded_at (**NULLABLE**)
- responded_by (Foreign Key - users.id, **NULLABLE**)
- response_message (Text, **NULLABLE**)
- timestamps

#### `property_viewings`
- id (Primary Key)
- property_id (Foreign Key - properties.id)
- inquirer_id (Foreign Key - users.id)
- agent_id (Foreign Key - users.id, **NULLABLE**)
- scheduled_date
- scheduled_time
- status (enum: scheduled, confirmed, completed, cancelled, no_show) - Default: 'scheduled'
- notes (Text, **NULLABLE**)
- rating (Integer, **NULLABLE**) - 1-5
- feedback (Text, **NULLABLE**)
- timestamps

### 7. Reviews & Ratings

#### `reviews`
- id (Primary Key)
- reviewable_type (morphs - properties, agencies, agents)
- reviewable_id
- reviewer_id (Foreign Key - users.id)
- rating (Integer) - 1-5
- title (**NULLABLE**)
- review_text (Text, **NULLABLE**)
- is_verified (Boolean) - Default: false
- is_approved (Boolean) - Default: false
- helpful_votes (Integer) - Default: 0
- status (enum: pending, approved, rejected) - Default: 'pending'
- timestamps

### 8. Saved Properties & Searches

#### `saved_properties`
- id (Primary Key)
- user_id (Foreign Key - users.id)
- property_id (Foreign Key - properties.id)
- notes (Text, **NULLABLE**)
- timestamps

#### `saved_searches`
- id (Primary Key)
- user_id (Foreign Key - users.id)
- name
- search_criteria (JSON)
- alert_frequency (enum: instant, daily, weekly) - Default: 'daily'
- is_active (Boolean) - Default: true
- last_alerted_at (**NULLABLE**)
- timestamps

### 9. Notifications & Communications

#### `notifications`
- id (Primary Key)
- user_id (Foreign Key - users.id)
- type (enum: property_alert, inquiry_received, viewing_scheduled, etc.)
- title
- message (Text)
- data (JSON, **NULLABLE**)
- read_at (**NULLABLE**)
- action_url (**NULLABLE**)
- timestamps

### 10. Analytics & Tracking

#### `property_views`
- id (Primary Key)
- property_id (Foreign Key - properties.id)
- user_id (Foreign Key - users.id, **NULLABLE**) - Null for anonymous views
- ip_address
- user_agent (Text, **NULLABLE**)
- referrer (**NULLABLE**)
- session_id (**NULLABLE**)
- timestamps

## Indexes for Performance

### Essential Indexes:
1. `properties`: (status, purpose, area_id, price)
2. `properties`: (agency_id, status)
3. `properties`: (agent_id, status)
4. `property_inquiries`: (property_id, status)
5. `users`: (user_type, status)
6. `areas`: (city_id, is_popular)
7. `cities`: (state_id, status)

## Relationships Summary

### User Relationships:
- User hasOne UserProfile
- User hasMany Properties (as landlord)
- User hasMany Inquiries
- User hasMany SavedProperties
- User hasMany Reviews (as reviewer)

### Location Relationships:
- State hasMany Cities
- City hasMany Areas
- Area hasMany Properties

### Agency Relationships:
- Agency belongsTo User (owner)
- Agency hasMany Agents
- Agency hasMany Properties
- Agency morphMany Reviews

### Property Relationships:
- Property belongsTo PropertyType
- Property belongsTo Area
- Property belongsTo User (property_owner)
- Property belongsTo Agency (**NULLABLE**)
- Property belongsTo Agent (**NULLABLE**)
- Property hasMany PropertyImages
- Property hasMany Inquiries
- Property hasMany Viewings
- Property morphMany Reviews
- Property belongsToMany PropertyFeatures

This design supports:
- Multi-tenancy (Agencies with their own agents)
- Nigerian location hierarchy
- Comprehensive property management
- Advanced search and filtering
- User engagement tracking
- Reviews and ratings system
- Notification system
- Analytics and reporting

---

## 📋 Laravel Migration Implementation Commands

### Phase 1: Foundation Tables
```bash
# 1. States
php artisan make:migration create_states_table

# 2. Property Types  
php artisan make:migration create_property_types_table

# 3. Property Features
php artisan make:migration create_property_features_table
```

### Phase 2: Location Hierarchy
```bash
# 4. Cities (depends on states)
php artisan make:migration create_cities_table

# 5. Areas (depends on cities) 
php artisan make:migration create_areas_table
```

### Phase 3: User System Enhancement
```bash
# 6. Modify existing users table
php artisan make:migration modify_users_table_for_homebaze

# 7. User Profiles (depends on users, cities, states)
php artisan make:migration create_user_profiles_table

# 8. Property Subtypes (depends on property_types)
php artisan make:migration create_property_subtypes_table
```

### Phase 4: Business Entities
```bash
# 9. Agencies (depends on users, cities, states)
php artisan make:migration create_agencies_table

# 10. Agents (depends on users, agencies)
php artisan make:migration create_agents_table
```

### Phase 5: Core Property System
```bash
# 11. Properties (depends on users, agencies, property_types, property_subtypes, areas, cities, states)
php artisan make:migration create_properties_table

# 12. Property Feature Property Pivot (depends on properties, property_features)
php artisan make:migration create_property_feature_property_table
```

### Phase 6: Property Media & Content
```bash
# 13. Property Images (depends on properties, users)
php artisan make:migration create_property_images_table
```

### Phase 7: Engagement & Communication
```bash
# 14. Property Inquiries (depends on properties, users)
php artisan make:migration create_property_inquiries_table

# 15. Property Viewings (depends on properties, users)
php artisan make:migration create_property_viewings_table

# 16. Reviews (depends on users - polymorphic)
php artisan make:migration create_reviews_table

# 17. Saved Properties (depends on users, properties)
php artisan make:migration create_saved_properties_table

# 18. Saved Searches (depends on users)
php artisan make:migration create_saved_searches_table
```

### Phase 8: System Features
```bash
# 19. Notifications (depends on users)
php artisan make:migration create_notifications_table

# 20. Property Views (depends on properties, users)
php artisan make:migration create_property_views_table
```

## 🎯 Migration Execution Strategy

### Option 1: Phase-by-Phase (Recommended for Development)
```bash
# Run migrations by phase to test each dependency level
php artisan migrate --path=/database/migrations/phase1
php artisan migrate --path=/database/migrations/phase2
# ... continue for each phase
```

### Option 2: All at Once (Production Ready)
```bash
# After all migrations are created and tested
php artisan migrate
```

## 🔄 Rollback Strategy
```bash
# Rollback in reverse order if needed
php artisan migrate:rollback --step=20  # Rollback all 20 migrations
```

## 📝 Migration File Naming Convention
Laravel will automatically timestamp the migrations, but they should be created in the order listed above to ensure proper dependency resolution:

- `2024_XX_XX_XXXXXX_create_states_table.php`
- `2024_XX_XX_XXXXXX_create_property_types_table.php`
- `2024_XX_XX_XXXXXX_create_property_features_table.php`
- ...and so on

This ensures that when `php artisan migrate` runs, tables are created in the correct dependency order.
