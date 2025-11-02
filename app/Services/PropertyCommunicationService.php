<?php

namespace App\Services;

use App\Models\Property;
use App\Services\Communication\WhatsAppService;
use App\Services\Communication\SmsService;

class PropertyCommunicationService
{
    /**
     * Get the best available phone number for the property
     */
    public static function getContactPhone(Property $property): ?string
    {
        // First try to get agent phone if property has an agent
        if ($property->agent && $property->agent->phone) {
            return $property->agent->phone;
        }

        // If no agent, try to get agency contact phone
        if ($property->agency && $property->agency->phone) {
            return $property->agency->phone;
        }

        // If no agency phone, try property owner phone
        if ($property->owner && $property->owner->phone) {
            return $property->owner->phone;
        }

        return null;
    }

    /**
     * Get the best available email for the property
     */
    public static function getContactEmail(Property $property): ?string
    {
        // First try to get agent email if property has an agent
        if ($property->agent && $property->agent->email) {
            return $property->agent->email;
        }

        // If no agent, try to get agency contact email
        if ($property->agency && $property->agency->email) {
            return $property->agency->email;
        }

        // If no agency email, try property owner email
        if ($property->owner && $property->owner->email) {
            return $property->owner->email;
        }

        return null;
    }

    /**
     * Get WhatsApp URL with pre-filled message using existing WhatsApp service
     */
    public static function getWhatsAppUrl(Property $property): ?string
    {
        $phone = self::getContactPhone($property);
        if (!$phone) {
            return null;
        }

        $message = self::getWhatsAppMessage($property);
        $formattedPhone = self::formatPhoneNumberForWhatsApp($phone);

        return "https://wa.me/{$formattedPhone}?text={$message}";
    }

    /**
     * Format phone number for WhatsApp (consistent with existing service)
     */
    private static function formatPhoneNumberForWhatsApp(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // If phone starts with +234, remove + for wa.me URL
        if (str_starts_with($phone, '+234')) {
            return substr($phone, 1);
        }

        // If phone starts with 234, keep as is
        if (str_starts_with($phone, '234')) {
            return $phone;
        }

        // If phone starts with 0, replace with 234
        if (str_starts_with($phone, '0')) {
            return '234' . substr($phone, 1);
        }

        // Otherwise assume it's a Nigerian number without country code
        return '234' . $phone;
    }

    /**
     * Get SMS URL with pre-filled message
     */
    public static function getSMSUrl(Property $property): ?string
    {
        $phone = self::getContactPhone($property);
        if (!$phone) {
            return null;
        }

        $message = self::getSMSMessage($property);

        return "sms:{$phone}?body={$message}";
    }

    /**
     * Get email URL with pre-filled subject and body
     */
    public static function getEmailUrl(Property $property): ?string
    {
        $email = self::getContactEmail($property);
        if (!$email) {
            return null;
        }

        $subject = self::getEmailSubject($property);
        $body = self::getEmailBody($property);

        return "mailto:{$email}?subject={$subject}&body={$body}";
    }

    /**
     * Generate WhatsApp message content
     */
    private static function getWhatsAppMessage(Property $property): string
    {
        $propertyUrl = route('property.show', $property->slug);
        $message = "🏠 *Property Inquiry - HomeBaze*\n\n";
        $message .= "Hi! I'm interested in your property: *{$property->title}*\n\n";
        $message .= "📍 *Location:* {$property->area->name}, {$property->city->name}\n";
        $message .= "💰 *Price:* ₦" . number_format($property->price) . "\n";
        $message .= "🏠 *Type:* {$property->propertySubtype->name}\n\n";
        $message .= "🔗 *Property Details:* {$propertyUrl}\n\n";
        $message .= "Please let me know if it's still available and if I can schedule a viewing. Thank you! 😊\n\n";
        $message .= "🔐 *Via HomeBaze - Nigeria's Premier Real Estate Platform*";

        return urlencode($message);
    }

    /**
     * Generate SMS message content
     */
    private static function getSMSMessage(Property $property): string
    {
        $message = "Hi! I'm interested in your property: {$property->title} in {$property->area->name}. Price: ₦" . number_format($property->price) . ". Is it still available?";
        return urlencode($message);
    }

    /**
     * Generate email subject
     */
    private static function getEmailSubject(Property $property): string
    {
        return urlencode("Inquiry about: {$property->title}");
    }

    /**
     * Generate email body
     */
    private static function getEmailBody(Property $property): string
    {
        $propertyUrl = route('property.show', $property->slug);
        $message = "Hi,\n\nI am interested in your property listing:\n\n";
        $message .= "Property: {$property->title}\n";
        $message .= "Location: {$property->area->name}, {$property->city->name}\n";
        $message .= "Price: ₦" . number_format($property->price) . "\n";
        $message .= "Property Type: {$property->propertySubtype->name}\n\n";
        $message .= "Property Link: {$propertyUrl}\n\n";
        $message .= "Could you please provide more details and let me know if I can schedule a viewing?\n\n";
        $message .= "Thank you for your time.\n\nBest regards";

        return urlencode($message);
    }

    /**
     * Get contact person name for the property
     */
    public static function getContactName(Property $property): string
    {
        // First try to get agent name if property has an agent
        if ($property->agent && $property->agent->name) {
            return $property->agent->name;
        }

        // If no agent, try to get agency name
        if ($property->agency && $property->agency->name) {
            return $property->agency->name;
        }

        // If no agency name, try property owner name
        if ($property->owner && $property->owner->name) {
            return $property->owner->name;
        }

        return 'Property Contact';
    }

    /**
     * Check if WhatsApp service is available
     */
    public static function isWhatsAppAvailable(): bool
    {
        $whatsappService = new WhatsAppService();
        return $whatsappService->isAvailable();
    }

    /**
     * Check if SMS service is available
     */
    public static function isSMSAvailable(): bool
    {
        $smsService = new SmsService();
        return $smsService->isAvailable();
    }

    /**
     * Get available communication methods for a property
     */
    public static function getAvailableMethods(Property $property): array
    {
        $methods = [];

        if (self::getWhatsAppUrl($property)) {
            $methods[] = [
                'type' => 'whatsapp',
                'label' => 'WhatsApp',
                'url' => self::getWhatsAppUrl($property),
                'icon' => 'whatsapp',
                'priority' => 1
            ];
        }

        if (self::getSMSUrl($property)) {
            $methods[] = [
                'type' => 'sms',
                'label' => 'SMS',
                'url' => self::getSMSUrl($property),
                'icon' => 'message',
                'priority' => 2
            ];
        }

        if (self::getEmailUrl($property)) {
            $methods[] = [
                'type' => 'email',
                'label' => 'Email',
                'url' => self::getEmailUrl($property),
                'icon' => 'mail',
                'priority' => 3
            ];
        }

        return $methods;
    }
}