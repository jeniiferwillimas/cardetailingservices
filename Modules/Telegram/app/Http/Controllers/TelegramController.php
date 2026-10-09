<?php

namespace Modules\Telegram\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Telegram\Services\TelegramBotService;

class TelegramController extends Controller
{
    public function __construct(private TelegramBotService $bot) {}

    public function webhook(Request $request): JsonResponse
    {
        $this->bot->handleUpdate($request->all());

        return response()->json(['ok' => true]);
    }

    public function setWebhook(): JsonResponse
    {
        $url = rtrim(config('app.url'), '/').'/api/telegram/webhook';
        $result = $this->bot->setWebhook($url);

        return response()->json($result);
    }

    public function removeWebhook(): JsonResponse
    {
        $result = $this->bot->removeWebhook();

        return response()->json($result);
    }
}
