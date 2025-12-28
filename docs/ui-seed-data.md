# UI Seed Data (Complete Records)

This file lists the minimum required fields per model plus ready-to-enter sample data (Nigeria-focused) that creates complete records through the UI. Use the creation order below so relationships resolve cleanly.

## Creation Order
1) Users (agency owners, agents, property owners)
2) Agencies (linked to agency-owner user + location)
3) Agents (linked to user + optional agency)
4) Property Owners (linked to user + optional agency/agent + location)
5) Properties (linked to owner + agent + optional agency + location)

## Required Fields by Model (From Migrations)

### User (users)
- Required: `name`, `password`
- Conditionally required (unique if present): `email`
- Defaults/optional: `phone`, `user_type`, `is_verified`, `is_active`, `avatar`, `preferences`, `last_login_at`

### Agency (agencies)
- Required: `name`, `slug`, `email`, `phone`, `address` (JSON), `owner_id`, `state_id`, `city_id`
- Optional: `license_number`, `license_expiry_date`, `website`, `latitude`, `longitude`, `logo`, `social_media`, `specializations`, `years_in_business`, `rating`, `total_reviews`, `total_properties`, `total_agents`, `is_verified`, `is_featured`, `is_active`, `verified_at`, `area_id`

### Agent (agents)
- Required: `user_id`
- Optional: `license_number`, `license_expiry_date`, `bio`, `specializations`, `years_experience`, `commission_rate`, `languages`, `service_areas`, `rating`, `total_reviews`, `total_properties`, `active_listings`, `properties_sold`, `properties_rented`, `is_available`, `is_verified`, `is_featured`, `accepts_new_clients`, `verified_at`, `last_active_at`, `agency_id`

### Property Owner (property_owners)
- Required: `type` (defaults to `individual`)
- Optional: `first_name`, `last_name`, `company_name`, `email`, `phone`, `address`, `city`, `state`, `country`, `tax_id`, `user_id`, `agency_id`, `agent_id`, `notes`, `is_active`, `state_id`, `city_id`, `area_id`, `date_of_birth`, `preferred_communication`, `profile_photo`, `id_document`, `proof_of_address`, `is_verified`, `verified_at`

### Property (properties)
- Required: `title`, `description`, `listing_type`, `price`, `address`, `property_type_id`, `state_id`, `city_id`, `owner_id`
- Optional: `slug` (auto-generated), `status`, `price_period`, `service_charge`, `legal_fee`, `agency_fee`, `caution_deposit`, `bedrooms`, `bathrooms`, `toilets`, `size_sqm`, `parking_spaces`, `year_built`, `furnishing_status`, `landmark`, `latitude`, `longitude`, `property_subtype_id`, `agent_id`, `agency_id`, `meta_title`, `meta_description`, `video_url`, `virtual_tour_url`, `view_count`, `inquiry_count`, `favorite_count`, `last_viewed_at`, `is_featured`, `is_verified`, `is_published`, `featured_until`, `verified_at`, `published_at`, `price_negotiable`, `contact_phone`, `contact_email`, `viewing_instructions`, `is_active`

## Pricing Rules to Use
- Maiduguri, Kano, Kaduna: Rent 250,000–1,500,000; Sale 5,000,000–90,000,000
- Abuja: Rent 500,000–8,000,000; Sale 30,000,000–300,000,000

## Sample Data (Ready for UI)

### Users
| name | email | phone | user_type | password | is_verified | is_active |
| --- | --- | --- | --- | --- | --- | --- |
| Amina Shehu | amina.shehu@sahelhomes.ng | +2348063102202 | agency_owner | password123 | true | true |
| Usman Ibrahim | usman.ibrahim@sahelhomes.ng | +2348063102203 | agent | password123 | true | true |
| Zainab Sani | zainab.sani@sahelhomes.ng | +2348063102204 | agent | password123 | true | true |
| Yusuf Bintu | yusuf.bintu@indagents.ng | +2348092201401 | agent | password123 | true | true |
| Hadiza Lawan | hadiza.lawan@owners.ng | +2348064105501 | property_owner | password123 | true | true |
| Maryam Gana | maryam.gana@owners.ng | +2348064105502 | property_owner | password123 | true | true |
| Moses Dikko | moses.dikko@capitalcrest.ng | +2348074103302 | agency_owner | password123 | true | true |
| Chinedu Okeke | chinedu.okeke@capitalcrest.ng | +2348074103303 | agent | password123 | true | true |
| Blessing Yusuf | blessing.yusuf@capitalcrest.ng | +2348074103304 | agent | password123 | true | true |
| Hauwa Abubakar | hauwa.abubakar@indagents.ng | +2348092201502 | agent | password123 | true | true |
| Segun Adebayo | segun.adebayo@owners.ng | +2348075107701 | property_owner | password123 | true | true |
| Ruth Daniel | ruth.daniel@owners.ng | +2348075107702 | property_owner | password123 | true | true |

### Agencies
| name | slug | email | phone | address.street | address.city | address.state | address.country | owner_email | state | city | area |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| Sahel Homes & Realty | sahel-homes-realty | info@sahelhomes.ng | +2348063102201 | 12 GRA Close | Maiduguri | Borno | Nigeria | amina.shehu@sahelhomes.ng | Borno | Maiduguri | GRA (Government Reserved Area) |
| Capital Crest Realty | capital-crest-realty | info@capitalcrest.ng | +2348074103301 | 45 Maitama Crescent | Abuja | FCT - Abuja | Nigeria | moses.dikko@capitalcrest.ng | FCT - Abuja | Abuja Municipal | Maitama |

### Agents
| user_email | agency_name | license_number | specializations | years_experience | commission_rate | languages | is_verified |
| --- | --- | --- | --- | --- | --- | --- | --- |
| usman.ibrahim@sahelhomes.ng | Sahel Homes & Realty | AGT-SAHEL-001 | Residential,Land | 6 | 5.0 | English,Hausa | true |
| zainab.sani@sahelhomes.ng | Sahel Homes & Realty | AGT-SAHEL-002 | Residential,Shortlet | 4 | 4.0 | English,Hausa | true |
| yusuf.bintu@indagents.ng | (Independent) | IND-YUS-2025 | Residential,Commercial | 7 | 6.0 | English,Hausa | true |
| chinedu.okeke@capitalcrest.ng | Capital Crest Realty | AGT-CAP-001 | Luxury,Residential | 8 | 5.5 | English,Igbo | true |
| blessing.yusuf@capitalcrest.ng | Capital Crest Realty | AGT-CAP-002 | Residential,Lease | 5 | 4.5 | English,Hausa | true |
| hauwa.abubakar@indagents.ng | (Independent) | IND-HAU-2025 | Residential,Commercial | 6 | 6.0 | English,Hausa | true |

### Property Owners
| type | first_name | last_name | email | phone | address | city | state | country | user_email | agency_name | agent_email | preferred_communication | is_verified |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| individual | Hadiza | Lawan | hadiza.lawan@owners.ng | +2348064105501 | Plot 18, GRA | Maiduguri | Borno | Nigeria | hadiza.lawan@owners.ng | Sahel Homes & Realty | usman.ibrahim@sahelhomes.ng | email | true |
| individual | Maryam | Gana | maryam.gana@owners.ng | +2348064105502 | Plot 44, Bulumkutu | Maiduguri | Borno | Nigeria | maryam.gana@owners.ng | Sahel Homes & Realty | zainab.sani@sahelhomes.ng | email | true |
| individual | Segun | Adebayo | segun.adebayo@owners.ng | +2348075107701 | 22 Jabi Lake Road | Abuja | FCT - Abuja | Nigeria | segun.adebayo@owners.ng | Capital Crest Realty | chinedu.okeke@capitalcrest.ng | phone | true |
| individual | Ruth | Daniel | ruth.daniel@owners.ng | +2348075107702 | 14 Utako Estate | Abuja | FCT - Abuja | Nigeria | ruth.daniel@owners.ng | Capital Crest Realty | blessing.yusuf@capitalcrest.ng | email | true |

### Properties (Vertical Format)

#### Property 1
- title: Modern 3 Bedroom Duplex in GRA
- description: Well-finished 3 bedroom duplex in GRA with parking, modern kitchen, and quick access to major roads.
- listing_type: sale
- status: available
- price: 45000000
- price_period: total
- bedrooms: 3
- bathrooms: 3
- toilets: 4
- size_sqm: 420
- furnishing_status: furnished
- address: Plot 12, GRA, Maiduguri
- landmark: near Maiduguri Central Market
- property_type: House
- property_subtype: Duplex
- state: Borno
- city: Maiduguri
- area: GRA (Government Reserved Area)
- owner_email: hadiza.lawan@owners.ng
- agent_email: usman.ibrahim@sahelhomes.ng
- agency_name: Sahel Homes & Realty
- is_featured: true
- is_verified: true
- is_published: true
- contact_phone: +2348063102203
- contact_email: usman.ibrahim@sahelhomes.ng

#### Property 2
- title: Spacious 2 Bedroom Flat in Bulumkutu
- description: Spacious 2 bedroom flat with en-suite rooms and reliable access road in Bulumkutu.
- listing_type: rent
- status: available
- price: 650000
- price_period: per_year
- bedrooms: 2
- bathrooms: 2
- toilets: 3
- size_sqm: 220
- furnishing_status: semi_furnished
- address: Plot 7, Bulumkutu, Maiduguri
- landmark: close to main express road
- property_type: Apartment
- property_subtype: Flat
- state: Borno
- city: Maiduguri
- area: Bulumkutu
- owner_email: maryam.gana@owners.ng
- agent_email: zainab.sani@sahelhomes.ng
- agency_name: Sahel Homes & Realty
- is_featured: false
- is_verified: true
- is_published: true
- contact_phone: +2348063102204
- contact_email: zainab.sani@sahelhomes.ng

#### Property 3
- title: Serviced 1 Bedroom Studio in Wuse 2
- description: Serviced studio with fitted kitchen, steady power, and close proximity to offices in Wuse 2.
- listing_type: rent
- status: available
- price: 2200000
- price_period: per_year
- bedrooms: 1
- bathrooms: 1
- toilets: 1
- size_sqm: 120
- furnishing_status: furnished
- address: Plot 4, Wuse 2, Abuja
- landmark: near city ring road
- property_type: Apartment
- property_subtype: Studio
- state: FCT - Abuja
- city: Abuja Municipal
- area: Wuse 2
- owner_email: segun.adebayo@owners.ng
- agent_email: chinedu.okeke@capitalcrest.ng
- agency_name: Capital Crest Realty
- is_featured: true
- is_verified: true
- is_published: true
- contact_phone: +2348074103303
- contact_email: chinedu.okeke@capitalcrest.ng

#### Property 4
- title: Luxury 4 Bedroom Terrace in Maitama
- description: Luxury 4 bedroom terrace with maid room, private parking, and high-end finishes in Maitama.
- listing_type: sale
- status: available
- price: 180000000
- price_period: total
- bedrooms: 4
- bathrooms: 4
- toilets: 5
- size_sqm: 520
- furnishing_status: furnished
- address: Plot 90, Maitama, Abuja
- landmark: opposite neighborhood park
- property_type: House
- property_subtype: Terrace
- state: FCT - Abuja
- city: Abuja Municipal
- area: Maitama
- owner_email: ruth.daniel@owners.ng
- agent_email: blessing.yusuf@capitalcrest.ng
- agency_name: Capital Crest Realty
- is_featured: true
- is_verified: true
- is_published: true
- contact_phone: +2348074103304
- contact_email: blessing.yusuf@capitalcrest.ng

## Notes for UI Entry
- Ensure the referenced State, City, and Area exist before creating Agencies and Properties.
- `property_type` and `property_subtype` must match existing catalog values.
- You can reuse agents/owners across multiple properties; vary prices within the city ranges for realism.
