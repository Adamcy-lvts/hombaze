# WhatsApp Business API Setup Guide

## Prerequisites
- Facebook Business Manager account
- Phone number dedicated to business (not used on regular WhatsApp)
- Domain ownership verification
- Business verification documents

## Step 1: Create Facebook App

1. Go to [Facebook Developers](https://developers.facebook.com)
2. Click "Create App" → "Business" → "WhatsApp"
3. Fill in app details:
   - App Name: "HomeBaze WhatsApp"
   - App Contact Email: your-email@domain.com

## Step 2: Configure WhatsApp Product

1. In your app dashboard, add "WhatsApp" product
2. Go to WhatsApp → Configuration
3. Add your business phone number
4. Verify phone number via SMS/call

## Step 3: Get API Credentials

### Required Credentials:
- **Access Token** (Temporary, then permanent)
- **Phone Number ID**
- **App ID**
- **App Secret**
- **Verify Token** (you create this)

### Get Phone Number ID:
```bash
curl -X GET "https://graph.facebook.com/v18.0/YOUR_APP_ID/phone_numbers" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## Step 4: Environment Configuration

Add to your `.env` file:

```env
# WhatsApp Business API Configuration
WHATSAPP_ENABLED=true
WHATSAPP_ACCESS_TOKEN=your_permanent_access_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_VERIFY_TOKEN=your_custom_verify_token
WHATSAPP_APP_ID=your_app_id
WHATSAPP_APP_SECRET=your_app_secret
```

## Step 5: Webhook Configuration

1. In Facebook App Dashboard → WhatsApp → Configuration
2. Set Webhook URL: `https://yourdomain.com/api/whatsapp/webhook`
3. Set Verify Token: (same as WHATSAPP_VERIFY_TOKEN in .env)
4. Subscribe to webhook fields:
   - `messages`
   - `message_deliveries`
   - `message_reads`
   - `message_echoes`

## Step 6: Message Templates

Create pre-approved templates for:
1. Property inquiry response
2. Viewing appointment confirmation
3. Property recommendations

### Template Example:
```
Name: property_inquiry_response
Category: UTILITY
Language: English (US)

Template:
Hello {{1}}, thank you for your interest in {{2}}. Our agent will contact you within {{3}} hours. Property details: {{4}}
```

## Step 7: Testing

1. Send test message using Graph API Explorer
2. Verify webhook receives messages
3. Test template messages

## Step 8: Production Access

1. Business verification (required for production)
2. Submit app for review
3. Get production access token
4. Update environment variables

## Important Notes

- **Sandbox**: 5 phone numbers for testing
- **Production**: Unlimited after business verification
- **Rates**: Conversation-based pricing (~₦15-30 per 24h session)
- **Templates**: Required for business-initiated conversations

## Support Resources

- [WhatsApp Business API Documentation](https://developers.facebook.com/docs/whatsapp)
- [Graph API Explorer](https://developers.facebook.com/tools/explorer)
- [Business Verification Guide](https://www.facebook.com/business/help/2058515294227817)