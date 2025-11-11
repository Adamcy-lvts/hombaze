# HomeBaze Payment System & Monetization Plan

## Current Payment System Analysis

### Existing Infrastructure ✅
- **RentPayment Model**: Already handles rent collection with payment tracking
- **Payment Methods**: Cash, Transfer, POS, Card (ready for Paystack expansion)
- **Payment Statuses**: Pending, Partial, Paid, Overdue, Cancelled, Refunded
- **Financial Tracking**: Late fees, discounts, deposits, balance calculations
- **Receipt System**: Automated receipt generation with PDF downloads

### Current Payment Gaps 🚫
- No payment gateway integration (manual payment recording)
- No automated recurring billing for subscriptions
- No commission tracking for agents/agencies
- No platform service fees collection
- No escrow/security deposit management

## Monetization Opportunities 💰

### 1. Property Listing Fees
**Revenue Model**: One-time listing fees or premium listing packages

**Implementation**:
```php
// Property listing packages
Basic Package (₦5,000): 30-day listing, 5 photos
Premium Package (₦15,000): 60-day listing, 15 photos, featured placement
Professional Package (₦25,000): 90-day listing, unlimited photos, priority support, analytics
```

### 2. Subscription Plans for Agencies & Agents
**Revenue Model**: Monthly/Annual SaaS subscriptions

**Agency Plans**:
- **Starter** (₦50,000/month): 50 properties, 5 agents, basic features
- **Growth** (₦150,000/month): 200 properties, 20 agents, advanced analytics
- **Enterprise** (₦300,000/month): Unlimited properties/agents, white-label, API access

**Independent Agent Plans**:
- **Basic** (₦15,000/month): 20 properties, basic CRM
- **Pro** (₦35,000/month): 100 properties, advanced tools, lead management

### 3. Transaction-Based Commission
**Revenue Model**: Percentage of successful property transactions

**Implementation**:
- **Property Sales**: 1-2% platform fee on successful sales
- **Rental Facilitation**: ₦10,000-50,000 per successful lease agreement
- **Lead Generation**: ₦5,000 per qualified lead delivered to agents

### 4. Premium Features & Add-ons
**Revenue Model**: Feature-based pricing

**Premium Features**:
- **Virtual Tours**: ₦20,000 per property for 360° tour hosting
- **Professional Photography**: ₦15,000 per property photoshoot service
- **Priority Support**: ₦25,000/month for dedicated account management
- **Advanced Analytics**: ₦10,000/month for detailed insights and reports

### 5. Advertising & Sponsored Content
**Revenue Model**: Display and sponsored content advertising

**Ad Products**:
- **Banner Advertisements**: ₦100,000/month for homepage placement
- **Sponsored Properties**: ₦50,000/month for featured property promotion
- **Agent Spotlight**: ₦30,000/month for agent profile promotion

## Paystack Integration Architecture

### 1. Core Payment Infrastructure

#### Database Schema Extensions
```sql
-- Subscriptions table
CREATE TABLE subscriptions (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    plan_id BIGINT,
    paystack_subscription_id VARCHAR(255),
    status ENUM('active', 'cancelled', 'expired', 'pending'),
    current_period_start DATE,
    current_period_end DATE,
    amount DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'NGN',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payment transactions table
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY,
    paystack_reference VARCHAR(255) UNIQUE,
    user_id BIGINT,
    subscription_id BIGINT NULLABLE,
    property_id BIGINT NULLABLE,
    amount DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'NGN',
    status ENUM('pending', 'success', 'failed', 'cancelled'),
    payment_method VARCHAR(50),
    paystack_response JSON,
    purpose ENUM('subscription', 'listing', 'commission', 'deposit'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Subscription plans table
CREATE TABLE subscription_plans (
    id BIGINT PRIMARY KEY,
    name VARCHAR(100),
    slug VARCHAR(100),
    user_type ENUM('agency', 'agent', 'landlord'),
    price DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'NGN',
    billing_cycle ENUM('monthly', 'quarterly', 'yearly'),
    features JSON,
    limits JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Agent/Agency commissions table
CREATE TABLE commissions (
    id BIGINT PRIMARY KEY,
    transaction_id BIGINT,
    agent_id BIGINT NULLABLE,
    agency_id BIGINT NULLABLE,
    property_id BIGINT,
    commission_rate DECIMAL(5,2),
    commission_amount DECIMAL(10,2),
    status ENUM('pending', 'paid', 'disputed'),
    paid_at TIMESTAMP NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Model Implementation
```php
// app/Models/Subscription.php
class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'paystack_subscription_id',
        'status', 'current_period_start', 'current_period_end',
        'amount', 'currency'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }
    public function transactions() { return $this->hasMany(PaymentTransaction::class); }
}

// app/Models/PaymentTransaction.php
class PaymentTransaction extends Model
{
    protected $fillable = [
        'paystack_reference', 'user_id', 'subscription_id',
        'property_id', 'amount', 'currency', 'status',
        'payment_method', 'paystack_response', 'purpose'
    ];

    protected $casts = ['paystack_response' => 'array'];
}
```

### 2. Paystack Service Integration

#### Payment Service
```php
// app/Services/PaystackService.php
class PaystackService
{
    private $secretKey;
    private $baseUrl = 'https://api.paystack.co';

    public function initializePayment($email, $amount, $reference, $callback_url)
    {
        // Initialize payment with Paystack
        // Return payment URL for user redirection
    }

    public function verifyPayment($reference)
    {
        // Verify payment status with Paystack
        // Update local transaction record
    }

    public function createSubscription($customer_code, $plan_code)
    {
        // Create recurring subscription
        // Handle subscription lifecycle
    }

    public function handleWebhook($payload, $signature)
    {
        // Process Paystack webhooks
        // Update local records based on events
    }
}
```

### 3. Subscription Management

#### Subscription Plans Configuration
```php
// Agency Plans
'agency_starter' => [
    'name' => 'Agency Starter',
    'price' => 50000,
    'limits' => [
        'properties' => 50,
        'agents' => 5,
        'analytics' => 'basic'
    ]
],
'agency_growth' => [
    'name' => 'Agency Growth',
    'price' => 150000,
    'limits' => [
        'properties' => 200,
        'agents' => 20,
        'analytics' => 'advanced'
    ]
]
```

## Implementation Timeline

### Phase 1: Foundation (Weeks 1-2) 🏗️
1. **Database Schema**: Create new payment-related tables
2. **Paystack Package**: Install and configure Paystack Laravel package
3. **Basic Models**: Implement Subscription, PaymentTransaction, SubscriptionPlan models
4. **Environment Setup**: Configure Paystack keys and webhook endpoints

### Phase 2: Core Payment Features (Weeks 3-4) 💳
1. **Payment Service**: Implement PaystackService with core methods
2. **Webhook Handling**: Create webhook controller for Paystack events
3. **Transaction Management**: Build payment verification and logging
4. **Basic UI**: Create payment forms and success/failure pages

### Phase 3: Subscription System (Weeks 5-6) 📅
1. **Subscription Plans**: Define and seed subscription plans
2. **Plan Selection**: Build subscription plan selection interface
3. **Recurring Billing**: Implement automatic subscription renewal
4. **Subscription Management**: User dashboard for subscription control

### Phase 4: Monetization Features (Weeks 7-8) 💰
1. **Listing Fees**: Implement property listing payment requirements
2. **Commission Tracking**: Build agent/agency commission system
3. **Premium Features**: Add paid feature gates and upgrade flows
4. **Analytics Dashboard**: Financial reporting for all user types

### Phase 5: Enhanced Features (Weeks 9-10) ⚡
1. **Escrow System**: Implement security deposit management
2. **Multi-Payment**: Support multiple payment methods
3. **Refund System**: Build refund and dispute handling
4. **Advanced Analytics**: Revenue tracking and business intelligence

## Technical Requirements

### Laravel Packages Needed
```bash
composer require unicodeveloper/laravel-paystack
composer require laravel/cashier  # For subscription management
composer require spatie/laravel-webhook-client  # For webhook handling
```

### Environment Configuration
```env
PAYSTACK_PUBLIC_KEY=pk_test_xxx
PAYSTACK_SECRET_KEY=sk_test_xxx
PAYSTACK_PAYMENT_URL=https://api.paystack.co
PAYSTACK_MERCHANT_EMAIL=admin@homebaze.ng
```

## Revenue Projections (Year 1)

### Conservative Estimates
- **50 Agencies** × ₦50,000/month = ₦2.5M/month
- **200 Independent Agents** × ₦15,000/month = ₦3M/month
- **500 Property Listings** × ₦15,000 avg = ₦7.5M/month
- **Transaction Commissions** = ₦5M/month

**Total Monthly Revenue**: ₦18M (~$11,000 USD)
**Annual Revenue Projection**: ₦216M (~$130,000 USD)

### Growth Projections (Year 3)
- **200 Agencies** = ₦20M/month
- **1,000 Agents** = ₦15M/month
- **2,000 Listings** = ₦30M/month
- **Commissions** = ₦25M/month

**Year 3 Annual Revenue**: ₦1.08B (~$650,000 USD)

## Risk Mitigation

### Technical Risks
1. **Payment Security**: PCI compliance and fraud prevention
2. **Webhook Reliability**: Implement retry mechanisms and monitoring
3. **Subscription Edge Cases**: Handle payment failures and grace periods
4. **Multi-Currency**: Prepare for regional expansion

### Business Risks
1. **Market Adoption**: Gradual rollout with pilot agencies
2. **Pricing Competition**: Flexible pricing and value proposition
3. **Cash Flow**: Payment terms and commission structures
4. **Regulatory Compliance**: Nigerian payment regulations

## Success Metrics

### Key Performance Indicators
1. **Monthly Recurring Revenue (MRR)**: Target ₦18M by month 12
2. **Customer Acquisition Cost (CAC)**: Keep below ₦25,000 per agency
3. **Customer Lifetime Value (CLV)**: Target ₦2M+ for agencies
4. **Churn Rate**: Maintain below 5% monthly for subscriptions
5. **Payment Success Rate**: Achieve 95%+ successful transactions

This comprehensive plan positions HomeBaze to capture significant value in Nigeria's growing real estate market while providing essential tools for all stakeholders in the property ecosystem.