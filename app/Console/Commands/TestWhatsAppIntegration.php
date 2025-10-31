<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Communication\WhatsAppService;
use Exception;

class TestWhatsAppIntegration extends Command
{
    protected $signature = 'whatsapp:test {phone?} {--message=Test message from HomeBaze}';
    protected $description = 'Test WhatsApp integration by sending a test message';

    public function handle()
    {
        $whatsappService = new WhatsAppService();

        // Check service status
        $this->info('🔍 Checking WhatsApp service status...');
        $status = $whatsappService->getStatus();

        $this->table(
            ['Setting', 'Status'],
            [
                ['Enabled', $status['enabled'] ? '✅ Yes' : '❌ No'],
                ['Configured', $status['configured'] ? '✅ Yes' : '❌ No'],
                ['API URL', $status['api_url']],
                ['Phone Number ID', $status['phone_number_id']],
                ['Access Token', $status['access_token']],
            ]
        );

        if (!$status['enabled']) {
            $this->error('❌ WhatsApp service is not enabled. Set WHATSAPP_ENABLED=true in your .env file.');
            return 1;
        }

        if (!$status['configured']) {
            $this->error('❌ WhatsApp service is not properly configured. Check your credentials in .env file.');
            $this->info('📋 Required environment variables:');
            $this->info('   - WHATSAPP_ACCESS_TOKEN');
            $this->info('   - WHATSAPP_PHONE_NUMBER_ID');
            $this->info('   - WHATSAPP_VERIFY_TOKEN');
            return 1;
        }

        // Get phone number for testing
        $phone = $this->argument('phone');
        if (!$phone) {
            $phone = $this->ask('Enter phone number to send test message (e.g., +2348012345678)');
        }

        if (!$phone) {
            $this->error('❌ Phone number is required.');
            return 1;
        }

        // Send test message
        $message = $this->option('message');
        $this->info("📱 Sending test message to {$phone}...");

        try {
            $result = $whatsappService->sendTextMessage($phone, $message);

            if ($result['success']) {
                $this->info('✅ Test message sent successfully!');
                $this->info("📩 Message ID: {$result['message_id']}");
            } else {
                $this->error('❌ Failed to send test message.');
                $this->error("Error: {$result['error']}");
                return 1;
            }
        } catch (Exception $e) {
            $this->error('❌ Exception occurred while sending message.');
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }

        // Test property inquiry message
        if ($this->confirm('Would you like to test a property inquiry message?', false)) {
            $this->info('📱 Sending property inquiry test message...');

            try {
                $result = $whatsappService->sendPropertyInquiry(
                    $phone,
                    'Luxury 3-Bedroom Apartment in Lekki',
                    'https://homebaze.com/property/luxury-3br-lekki',
                    [
                        'Location' => 'Lekki Phase 1, Lagos',
                        'Price' => '₦2,500,000/year',
                        'Bedrooms' => '3',
                        'Bathrooms' => '3',
                        'Type' => 'Apartment'
                    ]
                );

                if ($result['success']) {
                    $this->info('✅ Property inquiry message sent successfully!');
                } else {
                    $this->error('❌ Failed to send property inquiry message.');
                    $this->error("Error: {$result['error']}");
                }
            } catch (Exception $e) {
                $this->error('❌ Exception occurred while sending property inquiry message.');
                $this->error("Error: {$e->getMessage()}");
            }
        }

        // Test viewing confirmation message
        if ($this->confirm('Would you like to test a viewing confirmation message?', false)) {
            $this->info('📱 Sending viewing confirmation test message...');

            try {
                $result = $whatsappService->sendViewingConfirmation(
                    $phone,
                    [
                        'date' => 'Tomorrow',
                        'time' => '2:00 PM',
                        'property_title' => 'Luxury 3-Bedroom Apartment in Lekki',
                        'address' => '15 Admiralty Way, Lekki Phase 1, Lagos',
                        'agent_name' => 'John Doe',
                        'agent_phone' => '+234801234567'
                    ]
                );

                if ($result['success']) {
                    $this->info('✅ Viewing confirmation message sent successfully!');
                } else {
                    $this->error('❌ Failed to send viewing confirmation message.');
                    $this->error("Error: {$result['error']}");
                }
            } catch (Exception $e) {
                $this->error('❌ Exception occurred while sending viewing confirmation message.');
                $this->error("Error: {$e->getMessage()}");
            }
        }

        $this->info('🎉 WhatsApp integration test completed!');
        return 0;
    }
}