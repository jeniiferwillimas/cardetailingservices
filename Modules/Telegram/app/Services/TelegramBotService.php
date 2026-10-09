<?php

namespace Modules\Telegram\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Payment\Services\NowPaymentsClient;
use Modules\Service\Models\Service;

class TelegramBotService
{
    private string $token;

    private string $apiBase;

    private string $walletAddress;

    public function __construct()
    {
        $this->token = config('telegram.bot_token', '');
        $this->apiBase = "https://api.telegram.org/bot{$this->token}";
        $this->walletAddress = config('cardramp.merchant_wallet', '');
    }

    public function handleUpdate(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);
        }
    }

    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = trim($message['text'] ?? '');
        $firstName = $message['from']['first_name'] ?? 'there';

        if ($text === '/start') {
            $this->sendWelcome($chatId, $firstName);

            return;
        }

        $lower = strtolower($text);

        if (str_contains($lower, 'pay') || str_contains($lower, 'crypto') || str_contains($lower, 'usdt') || $text === '/pay') {
            $this->sendPaymentMenu($chatId);

            return;
        }

        if (str_contains($lower, 'card') || str_contains($lower, 'visa') || str_contains($lower, 'mastercard') || str_contains($lower, 'apple pay') || $text === '/card') {
            $this->sendCardPayment($chatId);

            return;
        }

        if (str_contains($lower, 'service') || str_contains($lower, 'price') || str_contains($lower, 'cost') || str_contains($lower, 'how much') || str_contains($lower, 'pricing') || $text === '/services') {
            $this->sendServices($chatId);

            return;
        }

        if (str_contains($lower, 'book') || str_contains($lower, 'appointment') || str_contains($lower, 'schedule') || $text === '/book') {
            $this->sendBookingInfo($chatId);

            return;
        }

        if (str_contains($lower, 'hour') || str_contains($lower, 'open') || str_contains($lower, 'available') || str_contains($lower, 'time')) {
            $this->sendHours($chatId);

            return;
        }

        if (str_contains($lower, 'location') || str_contains($lower, 'area') || str_contains($lower, 'where') || str_contains($lower, 'come to') || str_contains($lower, 'mobile')) {
            $this->sendLocation($chatId);

            return;
        }

        if (str_contains($lower, 'contact') || str_contains($lower, 'email') || str_contains($lower, 'phone') || str_contains($lower, 'reach')) {
            $this->sendContact($chatId);

            return;
        }

        if (str_contains($lower, 'wallet') || str_contains($lower, 'address') || str_contains($lower, 'ethereum') || str_contains($lower, 'eth')) {
            $this->sendWalletAddress($chatId);

            return;
        }

        if ($text === '/help' || str_contains($lower, 'help') || str_contains($lower, 'menu')) {
            $this->sendHelp($chatId);

            return;
        }

        if (str_contains($lower, 'thank') || str_contains($lower, 'thanks') || str_contains($lower, 'appreciate')) {
            $this->sendText($chatId, "You're welcome! 😊 Is there anything else I can help you with? Just type /help to see all options.");

            return;
        }

        if (str_contains($lower, 'hi') || str_contains($lower, 'hello') || str_contains($lower, 'hey') || str_contains($lower, 'yo') || str_contains($lower, 'sup') || str_contains($lower, 'good morning') || str_contains($lower, 'good afternoon')) {
            $this->sendWelcome($chatId, $firstName);

            return;
        }

        if (str_contains($lower, 'how long') || str_contains($lower, 'duration') || str_contains($lower, 'take')) {
            $this->sendText($chatId, "⏱ Service times vary:\n\n• Interior Detail: 1.5 – 2 hours\n• Exterior Detail: 1 – 1.5 hours\n• Full Detail: 2.5 – 4 hours\n• Ceramic Coating: 4 – 8 hours\n\nExact duration depends on your vehicle's size and condition. Want to see our full service list? Type /services");

            return;
        }

        if (str_contains($lower, 'cancel') || str_contains($lower, 'refund')) {
            $this->sendText($chatId, "📋 Cancellation Policy:\n\n• Cancel 24+ hours before → Full refund\n• Cancel within 24 hours → 50% fee\n• No-show → No refund\n\nTo cancel a booking, email us at info@elitecardetailing.co with your order reference number.");

            return;
        }

        if (is_numeric($text) && (float) $text >= 3) {
            $this->handleAmountReceived($chatId, (float) $text);

            return;
        }

        $this->sendFallback($chatId);
    }

    private function handleCallback(array $callback): void
    {
        $chatId = $callback['message']['chat']['id'];
        $data = $callback['data'] ?? '';

        $this->answerCallback($callback['id']);

        match (true) {
            $data === 'pay_crypto' => $this->sendCryptoPayment($chatId),
            $data === 'pay_card' => $this->sendCardPayment($chatId),
            $data === 'pay_menu' => $this->sendPaymentMenu($chatId),
            $data === 'services' => $this->sendServices($chatId),
            $data === 'book' => $this->sendBookingInfo($chatId),
            $data === 'help' => $this->sendHelp($chatId),
            $data === 'wallet' => $this->sendWalletAddress($chatId),
            $data === 'contact' => $this->sendContact($chatId),
            str_starts_with($data, 'amount_') => $this->handleQuickAmount($chatId, $data),
            default => $this->sendFallback($chatId),
        };
    }

    private function sendWelcome(int $chatId, string $name): void
    {
        $text = "Hey {$name}! 👋 Welcome to Service Detail USA.\n\n";
        $text .= "I'm here to help you with payments, bookings, and anything about our car detailing services.\n\n";
        $text .= 'What can I help you with today?';

        $keyboard = [
            [
                ['text' => '💳 Make a Payment', 'callback_data' => 'pay_menu'],
                ['text' => '📋 Our Services', 'callback_data' => 'services'],
            ],
            [
                ['text' => '📅 Book a Service', 'callback_data' => 'book'],
                ['text' => '📞 Contact Us', 'callback_data' => 'contact'],
            ],
            [
                ['text' => '❓ Help', 'callback_data' => 'help'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendPaymentMenu(int $chatId): void
    {
        $text = "💰 Choose your payment method:\n\n";
        $text .= "🪙 **Crypto (USDT TRC-20)** — Pay with stablecoins. Fast, low fees.\n\n";
        $text .= "💳 **Card / Wallet** — Pay with Visa, Mastercard, Apple Pay, or Google Pay via Ethereum.\n\n";
        $text .= 'Which would you prefer?';

        $keyboard = [
            [
                ['text' => '🪙 Pay with Crypto (USDT)', 'callback_data' => 'pay_crypto'],
            ],
            [
                ['text' => '💳 Pay with Card / Wallet', 'callback_data' => 'pay_card'],
            ],
            [
                ['text' => '⬅️ Back to Menu', 'callback_data' => 'help'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendCryptoPayment(int $chatId): void
    {
        $text = "🪙 **Crypto Payment (USDT TRC-20)**\n\n";
        $text .= "Here's how it works:\n\n";
        $text .= "1️⃣ Tell me the amount in USD you'd like to pay\n";
        $text .= "2️⃣ I'll generate a payment link for you\n";
        $text .= "3️⃣ Complete the payment on the secure checkout page\n";
        $text .= "4️⃣ You'll get a confirmation once we receive it\n\n";
        $text .= 'Pick a quick amount or type your own:';

        $keyboard = [
            [
                ['text' => '$50', 'callback_data' => 'amount_50'],
                ['text' => '$100', 'callback_data' => 'amount_100'],
                ['text' => '$150', 'callback_data' => 'amount_150'],
            ],
            [
                ['text' => '$200', 'callback_data' => 'amount_200'],
                ['text' => '$300', 'callback_data' => 'amount_300'],
                ['text' => '$500', 'callback_data' => 'amount_500'],
            ],
            [
                ['text' => '⬅️ Back', 'callback_data' => 'pay_menu'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendCardPayment(int $chatId): void
    {
        $walletDisplay = $this->walletAddress
            ? substr($this->walletAddress, 0, 6).'...'.substr($this->walletAddress, -4)
            : 'Not configured';

        $text = "💳 **Card / Wallet Payment (Ethereum)**\n\n";
        $text .= "You can pay using Visa, Mastercard, Apple Pay, or Google Pay.\n\n";
        $text .= "Here's how:\n\n";
        $text .= "1️⃣ Go to one of these trusted platforms:\n";
        $text .= "   • [Alchemy Pay](https://ramp.alchemypay.org)\n";
        $text .= "   • [MoonPay](https://buy.moonpay.com)\n";
        $text .= "   • [Transak](https://transak.com)\n\n";
        $text .= "2️⃣ Select **Buy ETH** and enter your amount in USD\n\n";
        $text .= "3️⃣ Paste this wallet address when asked:\n";

        $this->sendText($chatId, $text);

        if ($this->walletAddress) {
            $this->sendText($chatId, "`{$this->walletAddress}`\n\n👆 Tap to copy the address above");
        }

        $followUp = "4️⃣ Choose your payment method (card, Apple Pay, etc.)\n";
        $followUp .= "5️⃣ Complete the purchase — ETH will be sent to our wallet\n";
        $followUp .= "6️⃣ Send me a screenshot of the confirmation and I'll verify it ✅\n\n";
        $followUp .= 'Need help? Just ask!';

        $keyboard = [
            [
                ['text' => '📋 Copy Wallet Address', 'callback_data' => 'wallet'],
            ],
            [
                ['text' => '🪙 Pay with Crypto Instead', 'callback_data' => 'pay_crypto'],
            ],
            [
                ['text' => '⬅️ Back', 'callback_data' => 'pay_menu'],
            ],
        ];

        $this->sendMessage($chatId, $followUp, $keyboard);
    }

    private function handleQuickAmount(int $chatId, string $data): void
    {
        $amount = (float) str_replace('amount_', '', $data);
        $this->handleAmountReceived($chatId, $amount);
    }

    private function handleAmountReceived(int $chatId, float $amount): void
    {
        if ($amount < 3) {
            $this->sendText($chatId, '⚠️ Minimum payment amount is $3. Please enter a higher amount.');

            return;
        }

        if ($amount > 10000) {
            $this->sendText($chatId, '⚠️ Maximum payment amount is $10,000. For larger amounts, please contact us at info@elitecardetailing.co');

            return;
        }

        $this->sendText($chatId, '⏳ Creating your payment link for $'.number_format($amount, 2).'...');

        try {
            $nowPayments = app(NowPaymentsClient::class);

            if (! $nowPayments->isConfigured()) {
                $this->sendText($chatId, '❌ Payment system is being set up. Please try again later or contact us at info@elitecardetailing.co');

                return;
            }

            $orderId = 'TG-'.strtoupper(Str::random(8));
            $response = $nowPayments->createInvoice($orderId, $amount, "Telegram payment {$orderId}");
            $invoiceUrl = $response['invoice_url'] ?? null;

            if ($invoiceUrl) {
                $text = "✅ Your payment link is ready!\n\n";
                $text .= '💰 Amount: $'.number_format($amount, 2)."\n";
                $text .= "🔗 Order: {$orderId}\n\n";
                $text .= '👇 Tap the button below to pay:';

                $keyboard = [
                    [
                        ['text' => '💳 Pay Now — $'.number_format($amount, 2), 'url' => $invoiceUrl],
                    ],
                    [
                        ['text' => '💰 Different Amount', 'callback_data' => 'pay_crypto'],
                    ],
                    [
                        ['text' => '⬅️ Back to Menu', 'callback_data' => 'help'],
                    ],
                ];

                $this->sendMessage($chatId, $text, $keyboard);
            } else {
                $this->sendText($chatId, '❌ Something went wrong generating your link. Please try again or contact us at info@elitecardetailing.co');
            }
        } catch (\Exception $e) {
            Log::error('Telegram payment error', ['error' => $e->getMessage(), 'chatId' => $chatId]);
            $this->sendText($chatId, '❌ Payment system is temporarily unavailable. Please try again in a moment or contact us at info@elitecardetailing.co');
        }
    }

    private function sendServices(int $chatId): void
    {
        $services = Service::where('is_active', true)->orderBy('price')->get();

        if ($services->isEmpty()) {
            $this->sendText($chatId, "Our services are being updated. Visit our website to see the latest:\nhttps://elitecardetailing-nu.vercel.app/cardetailingservices");

            return;
        }

        $text = "🚗 **Our Services**\n\n";

        foreach ($services as $service) {
            $price = number_format((float) $service->price, 2);
            $text .= "▫️ **{$service->name}** — \${$price}\n";
            if ($service->description) {
                $desc = Str::limit($service->description, 80);
                $text .= "   {$desc}\n";
            }
            $text .= "\n";
        }

        $text .= 'Ready to book or pay? Choose below:';

        $keyboard = [
            [
                ['text' => '💳 Make a Payment', 'callback_data' => 'pay_menu'],
                ['text' => '📅 Book Now', 'callback_data' => 'book'],
            ],
            [
                ['text' => '⬅️ Back to Menu', 'callback_data' => 'help'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendBookingInfo(int $chatId): void
    {
        $siteUrl = config('app.frontend_url', 'https://elitecardetailing-nu.vercel.app');

        $text = "📅 **Book a Service**\n\n";
        $text .= "You can book directly on our website:\n\n";
        $text .= "1️⃣ Pick your service(s)\n";
        $text .= "2️⃣ Choose your date and location\n";
        $text .= "3️⃣ We come to you — mobile detailing!\n\n";
        $text .= '👇 Tap below to book:';

        $keyboard = [
            [
                ['text' => '🌐 Book on Website', 'url' => "{$siteUrl}/booking"],
            ],
            [
                ['text' => '📋 View Services', 'callback_data' => 'services'],
                ['text' => '💳 Pay Now', 'callback_data' => 'pay_menu'],
            ],
            [
                ['text' => '⬅️ Back to Menu', 'callback_data' => 'help'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendHours(int $chatId): void
    {
        $text = "🕐 **Business Hours**\n\n";
        $text .= "Monday – Friday: 7:00 AM – 7:00 PM\n";
        $text .= "Saturday: 8:00 AM – 6:00 PM\n";
        $text .= "Sunday: 9:00 AM – 5:00 PM\n\n";
        $text .= "We're a mobile service — we come to your location! 🚗\n\n";
        $text .= 'Need to book? Type /book';

        $this->sendText($chatId, $text);
    }

    private function sendLocation(int $chatId): void
    {
        $text = "📍 **Service Area**\n\n";
        $text .= "We're a mobile car detailing service available across the U.S.\n\n";
        $text .= "We come to YOU — your home, office, or wherever your car is parked.\n\n";
        $text .= "Just tell us your state when booking and we'll take care of the rest! 🚗✨\n\n";
        $text .= 'Ready to book? Type /book';

        $this->sendText($chatId, $text);
    }

    private function sendContact(int $chatId): void
    {
        $text = "📞 **Contact Us**\n\n";
        $text .= "📧 Email: info@elitecardetailing.co\n";
        $text .= "🌐 Website: elitecardetailing-nu.vercel.app\n\n";
        $text .= "Or just message me here — I'm happy to help! 😊";

        $keyboard = [
            [
                ['text' => '💳 Make a Payment', 'callback_data' => 'pay_menu'],
                ['text' => '📅 Book a Service', 'callback_data' => 'book'],
            ],
            [
                ['text' => '⬅️ Back to Menu', 'callback_data' => 'help'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendWalletAddress(int $chatId): void
    {
        if (! $this->walletAddress) {
            $this->sendText($chatId, '❌ Wallet address is not configured yet. Please contact us at info@elitecardetailing.co');

            return;
        }

        $text = "📋 **Our Ethereum Wallet Address (ERC-20)**\n\n";
        $text .= "`{$this->walletAddress}`\n\n";
        $text .= "👆 Tap the address above to copy it.\n\n";
        $text .= "Use this address when buying ETH through Alchemy Pay, MoonPay, or Transak.\n\n";
        $text .= '⚠️ **Important**: Only send ETH (ERC-20 network) to this address. Sending other tokens or using wrong networks may result in lost funds.';

        $keyboard = [
            [
                ['text' => '💳 Pay with Card', 'callback_data' => 'pay_card'],
                ['text' => '🪙 Pay with Crypto', 'callback_data' => 'pay_crypto'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendHelp(int $chatId): void
    {
        $text = "❓ **How can I help you?**\n\n";
        $text .= "Here's what I can do:\n\n";
        $text .= "/pay — Make a payment (crypto or card)\n";
        $text .= "/services — View our services & prices\n";
        $text .= "/book — Book a detailing service\n";
        $text .= "/card — Pay with card/wallet (Visa, Apple Pay)\n";
        $text .= "/help — Show this menu\n\n";
        $text .= 'Or just type your question naturally — I understand things like "how much", "book", "pay", "cancel", etc.';

        $keyboard = [
            [
                ['text' => '💳 Make a Payment', 'callback_data' => 'pay_menu'],
                ['text' => '📋 Our Services', 'callback_data' => 'services'],
            ],
            [
                ['text' => '📅 Book a Service', 'callback_data' => 'book'],
                ['text' => '📞 Contact Us', 'callback_data' => 'contact'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendFallback(int $chatId): void
    {
        $text = "I'm not sure I understood that 🤔\n\n";
        $text .= "Here's what I can help with:\n\n";
        $text .= "• Type **pay** to make a payment\n";
        $text .= "• Type **services** to see our prices\n";
        $text .= "• Type **book** to schedule a service\n";
        $text .= "• Type **card** to pay with card/wallet\n";
        $text .= "• Type **help** for all options\n\n";
        $text .= 'Or email us at info@elitecardetailing.co for anything else!';

        $keyboard = [
            [
                ['text' => '💳 Payment', 'callback_data' => 'pay_menu'],
                ['text' => '📋 Services', 'callback_data' => 'services'],
            ],
            [
                ['text' => '📅 Book', 'callback_data' => 'book'],
                ['text' => '❓ Help', 'callback_data' => 'help'],
            ],
        ];

        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendMessage(int $chatId, string $text, array $inlineKeyboard = []): void
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if (! empty($inlineKeyboard)) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard,
            ]);
        }

        Http::post("{$this->apiBase}/sendMessage", $payload);
    }

    private function sendText(int $chatId, string $text): void
    {
        Http::post("{$this->apiBase}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }

    private function answerCallback(string $callbackId): void
    {
        Http::post("{$this->apiBase}/answerCallbackQuery", [
            'callback_query_id' => $callbackId,
        ]);
    }

    public function setWebhook(string $url): array
    {
        $response = Http::post("{$this->apiBase}/setWebhook", [
            'url' => $url,
            'allowed_updates' => ['message', 'callback_query'],
        ]);

        return $response->json();
    }

    public function removeWebhook(): array
    {
        $response = Http::post("{$this->apiBase}/deleteWebhook");

        return $response->json();
    }
}
