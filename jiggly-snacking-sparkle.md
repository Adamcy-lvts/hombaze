# Plan: Public Property Listing for All Users

## Context
HomeBaze is new and needs listings to grow. Currently only agents, agencies, and property owners (who register specifically as such) can list properties. Opening property listing to ALL logged-in users via a simple public form will bootstrap the supply side. All user-submitted listings go through admin moderation before going live.

## Design Principle: Mobile-First
Most Nigerian users are on phones. The entire flow reuses the existing mobile-first design from the PropertyOwner `CreateProperty` page — same step transitions, touch-friendly inputs (`py-3.5`), large tap targets, full-width buttons, camera-first image upload, and swipe-like Alpine.js transitions. The view will be a direct adaptation of `resources/views/filament/property-owner/pages/create-property.blade.php`, replacing the `<x-filament-panels::page>` wrapper with the `guest-app` layout.

---

## Implementation Steps

### 1. Add `getOrCreatePropertyOwnerProfile()` to User model
**File:** `app/Models/User.php`

Add method that finds existing `PropertyOwner` by `user_id` or creates a minimal one (`first_name`, `last_name`, `email`, `phone`, `is_verified=false`). Mirrors pattern in `UnifiedRegistrationController::createPropertyOwnerProfile()`.

### 2. Create Livewire component — `app/Livewire/Listing/CreateListing.php`

Multi-step Livewire component (NOT Filament) reusing the exact same pattern as `app/Filament/PropertyOwner/Pages/CreateProperty.php`:

- **Category selection screen** (Residential / Commercial / Land) — same 3 big tap-target cards
- **Step 1 — Essentials:** property_type, subtype, title, price, listing_type (no status field — always `available`)
- **Step 2 — Photos:** featured image with camera-first upload + gallery. Reuse the same `validateAndProcessImage` JS, same processing overlay, same "Tap to change" UX
- **Step 3 — Details:** bedrooms (+/- counter), plot size (for land), description
- **Step 4 — Location:** state > city > area cascade with searchable area dropdown, address, then **Submit** button (label: "Submit for Review" instead of "Publish Property")
- **Step 5 — Success:** amber clock icon + "Listing Submitted! Under review" message + "View My Listings" + "List Another" buttons

On submit:
- `auth()->user()->getOrCreatePropertyOwnerProfile()` to lazily get/create owner
- `Property::create(...)` with `moderation_status=pending`, `listing_fee_status=waived`, `is_published=true`
- Attach images via Spatie Media Library (same `addMedia()` pattern)
- No credit check — waived for public listings

### 3. Create Blade view — `resources/views/livewire/listing/create-listing.blade.php`

Direct adaptation of `resources/views/filament/property-owner/pages/create-property.blade.php`:

**What stays the same (mobile-first patterns):**
- `min-h-screen flex flex-col pb-20` container
- Alpine.js `x-data` with step management, `next()`, `prev()`, `goTo()`
- Slide transitions (`translate-x-full` → `translate-x-0` → `-translate-x-full`)
- `absolute inset-0 flex flex-col space-y-6 overflow-y-auto pb-32` step containers
- Progress dots at top (`h-1.5 w-6 rounded-full`)
- Full-width rounded-xl inputs with `py-3.5 px-4` touch targets
- Camera-first featured image upload with processing overlay
- Gallery uploader with 2-column grid preview + caption inputs + remove buttons
- Bedroom +/- counter with large round tap targets
- Searchable area dropdown with `@focus="open = true"`
- Inline `validateAndProcessImage` script (same JS)
- `active:scale-95` button feedback
- `wire:loading` states on submit button

**What changes:**
- Wrapper: `<div>` with `guest-app` layout instead of `<x-filament-panels::page>`
- Remove `$this->notify()` Filament calls → use `session()->flash()` or dispatch browser events
- Step 5: amber `heroicon-o-clock` + "Under Review" instead of green checkmark + "Published!"
- Submit button: "Submit for Review" instead of "Publish Property"
- No "Back" link to category at top (use a simple back arrow)
- Remove status field (always `available`)
- Add a thin top bar with HomeBaze logo + close (X) button linking back to homepage

### 4. Create "My Listings" component — `app/Livewire/Listing/MyListings.php`
**View:** `resources/views/livewire/listing/my-listings.blade.php`

Mobile-first card grid:
- Single column on mobile, 2 columns on `sm:`, 3 on `lg:`
- Each card: property image, title, price, location, moderation status badge
  - Pending → amber "Under Review"
  - Approved → green "Live"
  - Rejected → red "Rejected" with rejection reason shown below
- "List a Property" floating action button at bottom-right on mobile
- Empty state: illustration + "List your first property" CTA

### 5. Add routes — `routes/web.php`

```php
// Public listing routes - any authenticated user
Route::middleware(['auth'])->prefix('listing')->name('listing.')->group(function () {
    Route::get('/create', \App\Livewire\Listing\CreateListing::class)->name('create');
    Route::get('/my-listings', \App\Livewire\Listing\MyListings::class)->name('my-listings');
});
```

Uses `auth` middleware only (NOT `customer` middleware) so agents and property owners can also use this simpler flow.

### 6. Add "List Property" CTA to navigation
**File:** `resources/views/layouts/guest-app.blade.php`

**Desktop nav (line ~130, before auth dropdown):**
- Emerald pill button with `heroicon-o-plus-circle` + "List Property"
- Guests: links to `route('login')` (Laravel `intended()` redirects back after login)
- Logged-in: links to `route('listing.create')`

**Mobile responsive menu (line ~196):**
- Add "List Property" as a prominent responsive nav link
- Add "My Listings" link for authenticated users

### 7. Update landing page navigation
**File:** `resources/views/livewire/welcome/navigation.blade.php`

Add "List Property" link alongside existing dashboard/login links.

### 8. Handle post-login redirect
**File:** `app/Http/Controllers/Auth/UnifiedLoginController.php`

Check `session('url.intended')` before user_type-based redirect. Guests who click "List Property" → login → land on the listing form.

---

## Files Summary

**New files (4):**
- `app/Livewire/Listing/CreateListing.php`
- `resources/views/livewire/listing/create-listing.blade.php`
- `app/Livewire/Listing/MyListings.php`
- `resources/views/livewire/listing/my-listings.blade.php`

**Modified files (5):**
- `app/Models/User.php` — add `getOrCreatePropertyOwnerProfile()`
- `routes/web.php` — add listing routes
- `resources/views/layouts/guest-app.blade.php` — add "List Property" CTA (desktop + mobile)
- `resources/views/livewire/welcome/navigation.blade.php` — add "List Property" link
- `app/Http/Controllers/Auth/UnifiedLoginController.php` — intended redirect

**Key reference files (reuse patterns from):**
- `app/Filament/PropertyOwner/Pages/CreateProperty.php` — component logic
- `resources/views/filament/property-owner/pages/create-property.blade.php` — mobile-first view template + JS

## Why This Works Without Breaking Anything
- PropertyObserver skips auto-moderation when `moderation_status` is explicitly set → our `pending` is preserved
- `listing_fee_status=waived` satisfies `scopePublished()` without touching credits
- `is_verified=false` on lazily-created PropertyOwner ensures `requiresModeration()` returns true
- Existing admin `PropertyModerationResource` handles approval — no changes needed
- Customer `user_type` stays unchanged — no panel access granted

## Verification
1. **Mobile:** Open on phone, tap "List Property" → complete all steps with camera photos → confirm "Under Review"
2. **Guest flow:** Tap "List Property" as guest → login → land on listing form (not dashboard)
3. **My Listings:** Submit listing → check My Listings page shows it with "Under Review" badge
4. **Admin moderation:** Open admin → Property Moderation → see the listing → approve → confirm it appears on public search
5. **Desktop:** Verify the form scales up nicely on larger screens (max-width container, centered)
6. **Existing flows unaffected:** Agent/Agency/PropertyOwner panel creation still works independently
