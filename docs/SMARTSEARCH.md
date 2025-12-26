# SmartSearch Documentation

## Table of Contents
1. [Overview](#overview)
2. [For End Users](#for-end-users)
   - [What is SmartSearch?](#what-is-smartsearch)
   - [Why SmartSearch?](#why-smartsearch)
   - [Subscription Tiers](#subscription-tiers)
   - [How It Works](#how-it-works)
3. [Technical Documentation](#technical-documentation)
   - [Architecture Overview](#architecture-overview)
   - [Core Components](#core-components)
   - [First Dibs Cascade System](#first-dibs-cascade-system)
   - [Database Schema](#database-schema)
   - [Scheduled Jobs](#scheduled-jobs)
4. [Production Checklist](#production-checklist)

---

## Overview

SmartSearch is HomeBaze's premium property matching service that automatically hunts for properties matching user criteria 24/7. Unlike traditional saved searches that simply notify users of new listings, SmartSearch implements a sophisticated tiered notification system with exclusive "First Dibs" access for VIP subscribers.

**Core Value Proposition:**
- Pay once, matches auto-unlocked (no double payment to view properties)
- AI-powered 24/7 property hunting
- VIP users get exclusive early access to new listings
- Time-limited subscriptions with fair pricing

---

## For End Users

### What is SmartSearch?

SmartSearch is your personal property hunting assistant. Instead of spending hours scrolling through listings, tell us exactly what you're looking for and we'll do the searching for you - around the clock, every single day.

When we find a property that matches your criteria, you'll be notified instantly via email, WhatsApp, or SMS (depending on your plan). No more missed opportunities because you weren't online at the right time.

### Why SmartSearch?

#### The Problem with Traditional Property Hunting

In Nigeria's competitive rental market:
- Good properties get snapped up within hours of listing
- Agents often show properties to their "preferred" contacts first
- You're competing against hundreds of other searchers
- Manually checking listings is time-consuming and frustrating

#### The SmartSearch Solution

| Traditional Hunting | SmartSearch |
|---------------------|-------------|
| You search manually | We search for you 24/7 |
| Miss listings while busy | Never miss a match |
| Same access as everyone | VIP gets exclusive early access |
| Pay agents for introductions | Pay once, contact agents directly |
| Hours of scrolling | Instant notifications |

### Subscription Tiers

#### Starter - ₦10,000
*Perfect for first-time searchers testing the waters*

| Feature | Details |
|---------|---------|
| Searches | 1 active search |
| Duration | 60 days |
| Notifications | Email only |
| Priority | Standard (48hrs after VIP) |

**Best for:** Someone with a specific search need, first-time users wanting to try the service.

---

#### Standard - ₦20,000 ⭐ Most Popular
*The sweet spot for serious property hunters*

| Feature | Details |
|---------|---------|
| Searches | 3 active searches |
| Duration | 90 days |
| Notifications | Email + WhatsApp |
| Priority | Standard (24hrs after VIP) |

**Best for:** Active searchers looking in multiple areas or for different property types. WhatsApp notifications ensure you never miss a match.

---

#### Priority - ₦35,000
*Get ahead of the crowd*

| Feature | Details |
|---------|---------|
| Searches | 5 active searches |
| Duration | 90 days |
| Notifications | Email + WhatsApp + SMS |
| Priority | High (immediately after VIP cascade) |

**Best for:** Serious relocators, corporate housing needs, or those searching for specific/rare property types.

---

#### VIP - ₦50,000
*First Dibs - Be first in line, every time*

| Feature | Details |
|---------|---------|
| Searches | Unlimited |
| Duration | 120 days |
| Notifications | Email + WhatsApp + SMS |
| Priority | **Exclusive 3-hour window** |

**The VIP Advantage - First Dibs Explained:**

When a new property matches your search:
1. **You're notified first** - before any other tier
2. **3-hour exclusive window** - only you can see the match initially
3. **Claim the property** - view it and contact the agent to secure your interest
4. **Beat the competition** - by the time others see it, you've already made contact

**Best for:** Those who need specific properties urgently, premium locations with high competition, corporate clients.

---

### How It Works

```
Step 1: Choose Your Plan
↓
Step 2: Create Your Search
   • Location (State, City, Area)
   • Property Type (Apartment, House, etc.)
   • Budget Range
   • Specific Features
↓
Step 3: We Hunt 24/7
   • Our system scans every new listing
   • AI matches properties to your criteria
   • Quality scored for relevance
↓
Step 4: Get Notified
   • Instant alerts when matches found
   • VIP users notified first
   • Direct links to property details
↓
Step 5: Contact & Visit
   • View property details
   • Contact agent directly
   • No extra fees!
```

### Guarantees

- **No Hidden Fees:** Pay once for your tier, all matches are auto-unlocked
- **No Match Extension:** If your search expires with zero matches, we extend it by 30 days free
- **50% Renewal Discount:** Renew before expiration and save 50%

---

## Technical Documentation

### Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     SmartSearch System                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐      │
│  │   Property   │───▶│  Matcher     │───▶│   Cascade    │      │
│  │   Observer   │    │  Service     │    │   Service    │      │
│  └──────────────┘    └──────────────┘    └──────────────┘      │
│         │                   │                   │               │
│         ▼                   ▼                   ▼               │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐      │
│  │ New Property │    │SmartSearch   │    │SmartSearch   │      │
│  │   Event      │    │   Match      │    │ Notification │      │
│  └──────────────┘    └──────────────┘    └──────────────┘      │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                    Claim Detection                       │   │
│  │  ┌────────────────┐         ┌────────────────┐          │   │
│  │  │ PropertyView   │         │PropertyInquiry │          │   │
│  │  │   Observer     │         │   Observer     │          │   │
│  │  └────────────────┘         └────────────────┘          │   │
│  │           │                         │                    │   │
│  │           └─────────┬───────────────┘                    │   │
│  │                     ▼                                    │   │
│  │              ┌─────────────┐                             │   │
│  │              │   Claim     │                             │   │
│  │              │  Service    │                             │   │
│  │              └─────────────┘                             │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Core Components

#### Models

| Model | Purpose | Location |
|-------|---------|----------|
| `SmartSearch` | User's search criteria with tier info | `app/Models/SmartSearch.php` |
| `SmartSearchSubscription` | Payment and subscription tracking | `app/Models/SmartSearchSubscription.php` |
| `SmartSearchMatch` | Individual property matches with cascade state | `app/Models/SmartSearchMatch.php` |
| `PropertyView` | Tracks user property page views | `app/Models/PropertyView.php` |

#### Services

| Service | Purpose | Location |
|---------|---------|----------|
| `SmartSearchMatcher` | Matches properties against search criteria | `app/Services/SmartSearchMatcher.php` |
| `SmartSearchCascadeService` | Manages First Dibs notification cascade | `app/Services/SmartSearchCascadeService.php` |
| `SmartSearchClaimService` | Detects view + contact claims | `app/Services/SmartSearchClaimService.php` |

#### Observers

| Observer | Purpose | Location |
|----------|---------|----------|
| `SmartSearchObserver` | Handles search lifecycle events | `app/Observers/SmartSearchObserver.php` |
| `PropertyObserver` | Triggers matching when properties published | `app/Observers/PropertyObserver.php` |
| `PropertyViewObserver` | Records views for claim detection | `app/Observers/PropertyViewObserver.php` |
| `PropertyInquiryObserver` | Records contacts for claim detection | `app/Observers/PropertyInquiryObserver.php` |

#### Jobs

| Job | Purpose | Location |
|-----|---------|----------|
| `ProcessSmartSearchMatches` | Main matching job | `app/Jobs/ProcessSmartSearchMatches.php` |
| `ProcessVipExclusiveExpiry` | Handles expired VIP windows | `app/Jobs/ProcessVipExclusiveExpiry.php` |
| `ProcessClaimPauseExpiry` | Resumes cascade after claim pause | `app/Jobs/ProcessClaimPauseExpiry.php` |
| `ProcessTierBatchNotification` | Sends non-VIP batch notifications | `app/Jobs/ProcessTierBatchNotification.php` |

### First Dibs Cascade System

The cascade system ensures VIP users get exclusive first access to new property matches.

#### Cascade Flow

```
Property Published
        │
        ▼
┌───────────────────┐
│  Find All Matches │
│  (Score & Rank)   │
└─────────┬─────────┘
          │
          ▼
┌───────────────────┐
│   VIP CASCADE     │
│                   │
│  VIP User A       │◀─── 3hr exclusive window
│       │           │
│  ┌────┴────┐      │
│  │         │      │
│  ▼         ▼      │
│ Claim    Expire   │
│  │         │      │
│  ▼         ▼      │
│ Pause    Next VIP │
│ 24hrs    User B   │
│  │         │      │
│  ▼         ▼      │
│ Check    Repeat   │
│ Status            │
└─────────┬─────────┘
          │
          ▼ (VIP cascade complete)
┌───────────────────┐
│  PRIORITY TIER    │◀─── Batch notification (immediate)
└─────────┬─────────┘
          │
          ▼ (+24 hours)
┌───────────────────┐
│  STANDARD TIER    │◀─── Batch notification
└─────────┬─────────┘
          │
          ▼ (+48 hours from VIP cascade end)
┌───────────────────┐
│  STARTER TIER     │◀─── Batch notification
└───────────────────┘
```

#### Claim Detection Logic

A valid VIP claim requires BOTH actions within the exclusive window:

```php
// Claim = View + Contact (both required)
$isClaimed = $match->property_viewed && $match->agent_contacted;

// Actions tracked via observers:
// - PropertyViewObserver: Records property page views
// - PropertyInquiryObserver: Records agent contact (inquiry, phone reveal, WhatsApp)
```

#### Cascade States (SmartSearchMatch)

| Status | Description |
|--------|-------------|
| `pending` | Match found, waiting in queue |
| `queued` | Scheduled for notification |
| `notified` | Notification sent, in exclusive window (VIP) |
| `claimed` | User viewed + contacted (VIP only) |
| `expired` | Exclusive window passed without claim |
| `skipped` | Cascade ended (property unavailable) |
| `completed` | All tier notifications sent |

### Database Schema

#### smart_searches
```sql
- id, user_id, name, description
- search_type, selected_property_type
- property_categories (json), location_preferences (json)
- property_subtypes (json), budget_min, budget_max
- additional_filters (json), notification_settings (json)
- is_active, is_default
- tier (enum: starter, standard, priority, vip)
- expires_at, purchased_at, purchase_reference
- matches_sent, last_match_at, is_expired, is_paused
```

#### smart_search_subscriptions
```sql
- id, user_id, tier
- searches_limit, searches_used, duration_days
- amount_paid, payment_reference, payment_method
- payment_status (pending, paid, failed)
- paid_at, starts_at, expires_at
- is_renewal, renewal_discount, renewed_from_id
- notification_channels (json), payment_metadata (json)
```

#### smart_search_matches
```sql
- id, smart_search_id, property_id, user_id
- match_score, tier, status
- queued_at, notified_at, exclusive_until
- claimed_at, claim_expires_at
- property_viewed, property_viewed_at
- agent_contacted, agent_contacted_at
- notification_channels_used (json), match_reasons (json)
- cascade_position
```

#### property_views
```sql
- id, property_id, user_id, session_id
- ip_address, user_agent, referrer
- source, smart_search_match_id
```

### Scheduled Jobs

Configured in `routes/console.php`:

| Schedule | Command/Job | Purpose |
|----------|-------------|---------|
| Every 15 min | `smartsearch:process-matches --new-properties` | Match new properties |
| Every 4 hours | `smartsearch:process-matches` | Full property rescan |
| Every 5 min | `ProcessVipExclusiveExpiry` | Check expired VIP windows |
| Every 15 min | `ProcessClaimPauseExpiry` | Resume paused cascades |
| Every 30 min | `ProcessTierBatchNotification` | Send non-VIP batches |
| Daily 8am | `smartsearch:expire-searches` | Mark expired searches |

---

## Production Checklist

### Pre-Deployment

#### Database
- [ ] Run all migrations
  ```bash
  docker compose exec app php artisan migrate
  ```
- [ ] Verify tables exist:
  - [ ] `smart_searches`
  - [ ] `smart_search_subscriptions`
  - [ ] `smart_search_matches`
  - [ ] `property_views`

#### Configuration
- [ ] Paystack API keys configured in `.env`
  ```
  PAYSTACK_PUBLIC_KEY=pk_live_xxxxx
  PAYSTACK_SECRET_KEY=sk_live_xxxxx
  ```
- [ ] Mail configuration set up
- [ ] WhatsApp integration configured (if using)
- [ ] SMS gateway configured (if using)

#### Queue Worker
- [ ] Queue worker running
  ```bash
  php artisan queue:work --tries=3
  ```
- [ ] Supervisor or process manager configured for queue persistence

#### Scheduler
- [ ] Laravel scheduler cron job configured
  ```cron
  * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] Verify scheduled jobs registered:
  ```bash
  php artisan schedule:list
  ```

### Post-Deployment Verification

#### Payment Flow
- [ ] Test Starter tier purchase (Paystack sandbox first)
- [ ] Test Standard tier purchase
- [ ] Test Priority tier purchase
- [ ] Test VIP tier purchase
- [ ] Verify subscription created after payment
- [ ] Verify `expires_at` calculated correctly

#### Search Creation
- [ ] Create search as Starter user (verify 1 search limit)
- [ ] Create searches as Standard user (verify 3 search limit)
- [ ] Create searches as VIP user (verify unlimited)
- [ ] Verify search criteria saved correctly

#### Matching Engine
- [ ] Manually trigger matching:
  ```bash
  docker compose exec app php artisan smartsearch:process-matches
  ```
- [ ] Verify matches created for test searches
- [ ] Check match scores calculated correctly
- [ ] Verify match reasons populated

#### Notification Cascade
- [ ] Create VIP and Starter searches with same criteria
- [ ] Publish matching property
- [ ] Verify VIP notified first (check `notified_at`)
- [ ] Verify VIP has `exclusive_until` set (3 hours)
- [ ] Wait for exclusive window expiry
- [ ] Verify cascade progresses to next tier

#### Claim Detection
- [ ] As VIP user, view matched property
- [ ] Verify `property_viewed` set to true
- [ ] Submit inquiry for property
- [ ] Verify `agent_contacted` set to true
- [ ] Verify match status changed to `claimed`
- [ ] Verify `claim_expires_at` set (24 hours)

#### Expiration
- [ ] Create test search with short expiration
- [ ] Run expiry command:
  ```bash
  docker compose exec app php artisan smartsearch:expire-searches
  ```
- [ ] Verify search marked as expired
- [ ] Verify expiration notification sent

#### No-Match Extension
- [ ] Create search that will have no matches
- [ ] Let it expire
- [ ] Request extension via UI
- [ ] Verify 30-day extension applied
- [ ] Verify extension only granted once

### Monitoring

#### Logs to Watch
```bash
# SmartSearch matching logs
tail -f storage/logs/smart-search-matches.log

# General Laravel logs
tail -f storage/logs/laravel.log
```

#### Key Metrics
- [ ] Number of active subscriptions per tier
- [ ] Matches sent per day
- [ ] VIP claim rate (claimed / notified)
- [ ] Average match score
- [ ] Subscription conversion rate
- [ ] No-match extension requests

#### Alert Conditions
- [ ] Queue jobs failing repeatedly
- [ ] Matching job taking > 5 minutes
- [ ] Payment callback failures
- [ ] Notification delivery failures

### Rollback Plan

If critical issues discovered:

1. **Disable SmartSearch purchases:**
   ```php
   // In SmartSearchPaymentController::purchase()
   return back()->with('error', 'SmartSearch is temporarily unavailable.');
   ```

2. **Pause scheduled jobs:**
   ```bash
   php artisan schedule:clear-cache
   ```

3. **Investigate logs:**
   ```bash
   grep -i "smartsearch\|smart_search" storage/logs/laravel.log
   ```

4. **Database rollback (if needed):**
   ```bash
   docker compose exec app php artisan migrate:rollback --step=4
   ```

---

## Support & Troubleshooting

### Common Issues

**User not receiving notifications:**
1. Check `notification_channels` in subscription
2. Verify email/phone configured on user profile
3. Check mail/SMS gateway logs
4. Verify search is active and not expired

**Matches not being found:**
1. Check search criteria matches property attributes
2. Run manual matching:
   ```bash
   docker compose exec app php artisan smartsearch:process-matches
   ```
3. Check property is published and active

**VIP not getting exclusive access:**
1. Verify subscription tier is `vip`
2. Check `exclusive_until` timestamp on match
3. Verify cascade service running properly

**Payment completed but subscription not active:**
1. Check Paystack webhook/callback received
2. Verify `payment_status` in `smart_search_subscriptions`
3. Check payment verification logs

---

*Last Updated: December 2024*
*Version: 1.0*
