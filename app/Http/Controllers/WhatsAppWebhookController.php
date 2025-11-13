<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Services\Communication\WhatsAppService;
use App\Services\WhatsAppMessageHandler;

class WhatsAppWebhookController extends Controller
{
    protected WhatsAppService $whatsappService;
    protected WhatsAppMessageHandler $messageHandler;

    public function __construct(
        WhatsAppService $whatsappService,
        WhatsAppMessageHandler $messageHandler
    ) {
        $this->whatsappService = $whatsappService;
        $this->messageHandler = $messageHandler;
    }

    /**
     * Handle WhatsApp webhook verification
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // Verify the webhook
        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            Log::info('WhatsApp webhook verified successfully');
            return response($challenge, 200);
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
            'expected_token' => config('services.whatsapp.verify_token')
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming WhatsApp messages
     */
    public function handleWebhook(Request $request): Response
    {
        try {
            $data = $request->all();

            Log::info('WhatsApp webhook received', ['data' => $data]);

            // Verify webhook signature
            if (!$this->verifySignature($request)) {
                Log::warning('WhatsApp webhook signature verification failed');
                return response('Unauthorized', 401);
            }

            // Process webhook data
            if (isset($data['entry'])) {
                foreach ($data['entry'] as $entry) {
                    if (isset($entry['changes'])) {
                        foreach ($entry['changes'] as $change) {
                            $this->processChange($change);
                        }
                    }
                }
            }

            return response('OK', 200);
        } catch (Exception $e) {
            Log::error('WhatsApp webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Internal Server Error', 500);
        }
    }

    /**
     * Verify webhook signature
     */
    protected function verifySignature(Request $request): bool
    {
        $signature = $request->header('X-Hub-Signature-256');
        $payload = $request->getContent();
        $appSecret = config('services.whatsapp.app_secret');

        if (!$signature || !$appSecret) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Process webhook change data
     */
    protected function processChange(array $change): void
    {
        $field = $change['field'] ?? null;
        $value = $change['value'] ?? [];

        switch ($field) {
            case 'messages':
                $this->processMessages($value);
                break;
            case 'message_echoes':
                $this->processMessageEchoes($value);
                break;
            case 'message_deliveries':
                $this->processMessageDeliveries($value);
                break;
            case 'message_reads':
                $this->processMessageReads($value);
                break;
            default:
                Log::info('Unhandled WhatsApp webhook field', ['field' => $field]);
        }
    }

    /**
     * Process incoming messages
     */
    protected function processMessages(array $value): void
    {
        $messages = $value['messages'] ?? [];
        $contacts = $value['contacts'] ?? [];
        $metadata = $value['metadata'] ?? [];

        foreach ($messages as $message) {
            $this->messageHandler->handleIncomingMessage($message, $contacts, $metadata);
        }
    }

    /**
     * Process message echoes (messages sent by the business)
     */
    protected function processMessageEchoes(array $value): void
    {
        $messages = $value['messages'] ?? [];

        foreach ($messages as $message) {
            Log::info('WhatsApp message echo received', ['message' => $message]);
            // Handle message echo if needed
        }
    }

    /**
     * Process message delivery notifications
     */
    protected function processMessageDeliveries(array $value): void
    {
        $messages = $value['messages'] ?? [];

        foreach ($messages as $message) {
            Log::info('WhatsApp message delivered', ['message_id' => $message['id'] ?? null]);
            // Update message status in database if needed
        }
    }

    /**
     * Process message read notifications
     */
    protected function processMessageReads(array $value): void
    {
        $messages = $value['messages'] ?? [];

        foreach ($messages as $message) {
            Log::info('WhatsApp message read', ['message_id' => $message['id'] ?? null]);
            // Update message status in database if needed
        }
    }
}