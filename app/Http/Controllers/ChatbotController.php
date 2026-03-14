<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function reply(Request $request)
    {
        $userMessage = trim($request->input('message', ''));
        $history     = $request->input('history', []);

        if (!$userMessage) {
            return response()->json(['reply' => 'Please type a message.']);
        }

        $pi = Setting::servicePrice('identity-verification');
        $pc = Setting::servicePrice('crb-blacklist-check');
        $ps = Setting::servicePrice('credit-score-check');
        $pl = Setting::servicePrice('loan-eligibility');
        $pr = Setting::servicePrice('full-credit-report');

        $contactEmail   = Setting::get('contact_email', 'hello@readiwork.co.ke');
        $supportPhone   = Setting::get('support_phone', '+254722175570');
        $supportWhatsApp = Setting::get('support_whatsapp', '254722175570');

        $systemPrompt = "You are the Readiwork support assistant. Readiwork is a Kenyan credit-data platform that lets individuals and lenders verify identities, check CRB blacklist status, get credit scores, and download full credit reports — powered by Metropol CRB.\n\nKey facts:\n- Services: Identity Verification (KSh {$pi}), CRB Blacklist Check (KSh {$pc}), Credit Score (KSh {$ps}), Loan Eligibility Bundle (KSh {$pl}), Full Credit Report (from KSh {$pr})\n- Payment: M-Pesa STK Push only\n- Results are instant (5–15 seconds)\n- No monthly fees — pay per check\n- Credits never expire\n- Contact: {$contactEmail} | {$supportPhone}\n- Website: readi.work\n\nBe friendly, concise, and helpful. Answer questions about the services, pricing, how credit scores work, M-Pesa payments, and general credit bureau questions. If asked about something completely unrelated to Readiwork or credit, politely redirect back to how you can help with credit checks.\n\nKeep replies short (2–4 sentences). Use line breaks for lists. Do not use markdown bold or asterisks in replies.";

        // Sanitise history
        $safeHistory = [];
        foreach (array_slice($history, -10) as $h) {
            if (in_array($h['role'] ?? '', ['user', 'assistant']) && !empty($h['content'])) {
                $safeHistory[] = ['role' => $h['role'], 'content' => substr($h['content'], 0, 500)];
            }
        }

        $messages = array_merge($safeHistory, [['role' => 'user', 'content' => $userMessage]]);
        $apiKey   = config('services.anthropic.key', env('ANTHROPIC_API_KEY', ''));

        if ($apiKey) {
            $payload = json_encode([
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 300,
                'system'     => $systemPrompt,
                'messages'   => $messages,
            ]);

            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'x-api-key: ' . $apiKey,
                    'anthropic-version: 2023-06-01',
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data  = json_decode($response, true);
                $reply = $data['content'][0]['text'] ?? null;
                if ($reply) {
                    return response()->json(['reply' => $reply]);
                }
            }
        }

        // Fallback replies
        return response()->json(['reply' => $this->fallbackReply($userMessage, $pi, $pc, $ps, $pl, $pr, $contactEmail, $supportPhone, $supportWhatsApp)]);
    }

    private function fallbackReply(string $msg, int $pi, int $pc, int $ps, int $pl, int $pr, string $email, string $phone, string $wa): string
    {
        $q = strtolower(trim($msg));

        $greetWords = ['hi', 'hello', 'hey', 'hii', 'helo', 'howdy', 'yo', 'morning', 'afternoon', 'evening'];
        foreach ($greetWords as $g) {
            if ($q === $g || str_starts_with($q, $g . ' ')) {
                return "Hi there! I'm the Readiwork assistant. I can help with credit checks, pricing, M-Pesa payments, and more. What would you like to know?";
            }
        }

        if (preg_match('/what.*(service|offer|do|check)|service.*offer/i', $msg)) {
            return "Readiwork offers 5 services:\n• Identity Verification – KSh {$pi}\n• CRB Blacklist Check – KSh {$pc}\n• Credit Score – KSh {$ps}\n• Loan Eligibility Bundle – KSh {$pl}\n• Full Credit Report – from KSh {$pr}\n\nAll paid via M-Pesa, results in seconds!";
        }
        if (preg_match('/price|cost|how much|pricing/i', $msg)) {
            return "Our pricing:\n• Identity Verification – KSh {$pi}\n• CRB Blacklist Check – KSh {$pc}\n• Credit Score – KSh {$ps}\n• Loan Eligibility – KSh {$pl}\n• Full Credit Report – from KSh {$pr}\n\nAll paid via M-Pesa STK Push, no hidden fees.";
        }
        if (preg_match('/mpesa|m-pesa|payment|pay/i', $msg)) {
            return "All payments are made via M-Pesa STK Push. Enter your phone number, we send a push notification to your phone, and you enter your M-Pesa PIN to confirm. Payment is processed in seconds and your report is delivered instantly.";
        }
        if (preg_match('/crb|blacklist|default|loan default/i', $msg)) {
            return "Our CRB Blacklist Check (KSh {$pc}) checks if a National ID is listed for unpaid or defaulted loans at any licensed Credit Reference Bureau in Kenya. Results are instant after payment.";
        }
        if (preg_match('/credit score|score/i', $msg)) {
            return "Our Credit Score Check (KSh {$ps}) gives you an AI-powered credit risk score on a 200–900 scale. A higher score means lower risk. It's instant after M-Pesa payment.";
        }
        if (preg_match('/contact|email|phone|whatsapp|support|help/i', $msg)) {
            return "You can reach us at:\n• Email: {$email}\n• Phone: {$phone}\n• WhatsApp: wa.me/{$wa}\n\nWe're happy to help!";
        }
        if (preg_match('/identity|id|verify|verification/i', $msg)) {
            return "Our Identity Verification (KSh {$pi}) confirms a National ID against the Kenya government registry — returning the verified full name, DOB, gender, and location. Results are instant.";
        }

        return "I can help with information about Readiwork's credit check services, pricing, and M-Pesa payments. Could you ask me something specific, like pricing or how a particular service works?";
    }
}
