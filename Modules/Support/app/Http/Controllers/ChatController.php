<?php

namespace Modules\Support\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Support\Events\MessageSent;
use Modules\Support\Models\Conversation;
use Modules\Support\Models\Message;
use Modules\Support\Transformers\ConversationResource;
use Modules\Support\Transformers\MessageResource;

class ChatController extends Controller
{
    /**
     * Public: start a new conversation.
     */
    public function startConversation(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customerName' => ['nullable', 'string', 'max:255'],
                'customerEmail' => ['nullable', 'email', 'max:255'],
            ]);

            $conversation = Conversation::create([
                'customer_name' => $validated['customerName'] ?? null,
                'customer_email' => $validated['customerEmail'] ?? null,
            ]);

            $res = [
                'success' => true,
                'data' => new ConversationResource($conversation),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Public: load a conversation's message history.
     */
    public function messages(string $uuid): JsonResponse
    {
        try {
            $conversation = Conversation::where('uuid', $uuid)->firstOrFail();

            $res = [
                'success' => true,
                'data' => MessageResource::collection($conversation->messages()->orderBy('created_at')->get()),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Public: customer sends a message.
     */
    public function sendMessage(Request $request, string $uuid): JsonResponse
    {
        try {
            $validated = $request->validate([
                'body' => ['required', 'string', 'max:4000'],
            ]);

            $conversation = Conversation::where('uuid', $uuid)->firstOrFail();

            $message = $conversation->messages()->create([
                'sender_type' => 'customer',
                'body' => $validated['body'],
            ]);
            $conversation->update(['last_message_at' => now()]);

            broadcast(new MessageSent($message));

            $res = [
                'success' => true,
                'data' => new MessageResource($message),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: list conversations, most recently active first.
     */
    public function adminIndex(): JsonResponse
    {
        try {
            $conversations = Conversation::query()
                ->withCount(['messages as unread_count' => function ($query) {
                    $query->where('sender_type', 'customer')->whereNull('read_at');
                }])
                ->orderByRaw('last_message_at IS NULL, last_message_at DESC')
                ->get();

            $res = [
                'success' => true,
                'data' => ConversationResource::collection($conversations),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: load a conversation's messages and mark customer messages read.
     */
    public function adminMessages(Conversation $conversation): JsonResponse
    {
        try {
            $conversation->messages()
                ->where('sender_type', 'customer')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $res = [
                'success' => true,
                'data' => MessageResource::collection($conversation->messages()->orderBy('created_at')->get()),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }

    /**
     * Admin: reply in a conversation.
     */
    public function adminReply(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            $validated = $request->validate([
                'body' => ['required', 'string', 'max:4000'],
            ]);

            $message = $conversation->messages()->create([
                'sender_type' => 'admin',
                'admin_id' => $request->user()->id,
                'body' => $validated['body'],
            ]);
            $conversation->update(['last_message_at' => now()]);

            broadcast(new MessageSent($message));

            $res = [
                'success' => true,
                'data' => new MessageResource($message),
            ];
        } catch (Exception $e) {
            $res = [
                'success' => false,
                'message' => $e->getMessage(),
                'getFile' => $e->getFile(),
                'getLine' => $e->getLine(),
            ];
        } catch (\Throwable $t) {
            $res = [
                'success' => false,
                'message' => $t->getMessage(),
                'getFile' => $t->getFile(),
                'getLine' => $t->getLine(),
            ];
        }

        return response()->json($res);
    }
}
