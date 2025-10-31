# WhatsApp Integration Quick Start

## 🚀 Quick Setup (5 Minutes)

### 1. Get Your Credentials
Visit the [Facebook Developers Console](https://developers.facebook.com/) and follow the [complete setup guide](WHATSAPP_SETUP_GUIDE.md).

### 2. Add Environment Variables
Copy these to your `.env` file:

```env
# WhatsApp Business API Configuration
WHATSAPP_ENABLED=true
WHATSAPP_ACCESS_TOKEN=your_temporary_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_VERIFY_TOKEN=homebaze_webhook_2024
WHATSAPP_APP_ID=your_app_id_here
WHATSAPP_APP_SECRET=your_app_secret_here
```

### 3. Clear Config Cache
```bash
docker compose exec app php artisan config:cache
```

### 4. Test Integration
```bash
# Test basic WhatsApp functionality
docker compose exec app php artisan whatsapp:test +2348012345678

# Check service status
docker compose exec app php artisan whatsapp:test --help
```

### 5. Set Up Webhook (Production)
- **Webhook URL:** `https://yourdomain.com/api/whatsapp/webhook`
- **Verify Token:** `homebaze_webhook_2024` (or your custom token)

## 🔧 Features Ready to Use

### ✅ Property Inquiries
- Automatic WhatsApp links on property pages
- Pre-filled messages with property details
- Lead tracking and logging

### ✅ Viewing Scheduling
- WhatsApp-based appointment booking
- Confirmation messages
- Automated reminders

### ✅ Two-Way Communication
- Incoming message handling
- Automated responses
- Lead qualification

## 📱 Test Messages

### Property Inquiry
```
🏠 Property Inquiry - HomeBaze

Hi! I'm interested in your property: Luxury 3-Bedroom Apartment in Lekki

📍 Location: Lekki Phase 1, Lagos
💰 Price: ₦2,500,000
🏠 Type: Apartment

🔗 Property Details: https://homebaze.com/property/123

Please let me know if it's still available and if I can schedule a viewing. Thank you! 😊

🔐 Via HomeBaze - Nigeria's Premier Real Estate Platform
```

### Viewing Confirmation
```
✅ Viewing Appointment Confirmed - HomeBaze

📅 Date & Time: Tomorrow at 2:00 PM
🏠 Property: Luxury 3-Bedroom Apartment in Lekki
📍 Location: 15 Admiralty Way, Lekki Phase 1, Lagos
👤 Agent: John Doe
📞 Agent Contact: +234801234567

📋 What to bring:
• Valid ID
• Proof of income (if interested)
• Any questions you have

⚠️ Please arrive 5 minutes early

Need to reschedule? Reply to this message or call our agent directly.

🔐 HomeBaze - Your Trusted Property Partner
```

## 🛠️ Commands Available

```bash
# Test WhatsApp integration
docker compose exec app php artisan whatsapp:test

# Test with specific phone number
docker compose exec app php artisan whatsapp:test +2348012345678

# Test with custom message
docker compose exec app php artisan whatsapp:test +2348012345678 --message="Hello from HomeBaze!"
```

## 🔍 Troubleshooting

### Common Issues:

1. **"WhatsApp service is not enabled"**
   - Set `WHATSAPP_ENABLED=true` in `.env`
   - Run `php artisan config:cache`

2. **"Credentials not configured"**
   - Check all required env variables are set
   - Verify access token is not expired

3. **"API request failed"**
   - Check phone number format (+234...)
   - Verify WhatsApp Business API credentials
   - Ensure phone number is registered for testing

4. **Webhook not receiving messages**
   - Verify webhook URL is publicly accessible
   - Check verify token matches env variable
   - Ensure HTTPS is enabled in production

## 📞 Support

- [Facebook WhatsApp Business API Documentation](https://developers.facebook.com/docs/whatsapp)
- [HomeBaze WhatsApp Setup Guide](WHATSAPP_SETUP_GUIDE.md)
- Check logs: `docker compose exec app php artisan pail`

## ⚡ Next Steps

1. **Apply for Production Access** - Business verification required
2. **Create Message Templates** - For automated responses
3. **Set Up Analytics** - Track message performance
4. **Implement Advanced Features** - Rich media, quick replies, etc.