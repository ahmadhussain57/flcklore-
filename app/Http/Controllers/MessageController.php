<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    /**
     * إرسال رسالة جديدة إلى محادثة
     */
       /**
     * إرسال رسالة جديدة إلى محادثة
     */
    public function store(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        // ✅ التحقق من أن المستخدم مشارك في المحادثة
        if (!$conversation->hasParticipant($user)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'غير مصرح'], 403);
            }
            abort(403, 'غير مصرح لك بإرسال رسالة في هذه المحادثة.');
        }

        $validated = $request->validate([
            'body' => 'required|string|min:1|max:5000',
        ], [
            'body.required' => 'لا يمكن إرسال رسالة فارغة.',
            'body.max'      => 'الرسالة طويلة جداً (الحد 5000 حرف).',
        ]);

        // ✅ إنشاء الرسالة وتحديث last_message_at في transaction
        $message = DB::transaction(function () use ($conversation, $user, $validated) {
            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body'    => trim($validated['body']),
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            // تحديث last_read_at للمرسل (لأنه قرأ رسالته)
            $conversation->participants()
                ->where('user_id', $user->id)
                ->update(['last_read_at' => now()]);

            return $message;
        });

        // ✅ إرسال إشعار للمستقبل
        $recipient = $conversation->getOtherParticipant($user);

        if ($recipient) {
            $recipient->notify(new NewMessageReceived($conversation, $message, $user));
        }

        // ✅ إذا كان الطلب AJAX، أعِد JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'               => $message->id,
                    'body'             => $message->body,
                    'sender_id'        => $message->user_id,
                    'sender_name'      => $user->name,
                    'is_mine'          => true,
                    'created_at'       => $message->created_at->toIso8601String(),
                    'created_at_human' => $message->created_at->diffForHumans(),
                ],
            ], 201);
        }

        // للطلبات العادية
        return back()->with('success', 'تم إرسال الرسالة.');
    }

    /**
     * جلب الرسائل الجديدة (JSON API للـ Polling)
     */
    public function fetch(Request $request, Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        // ✅ التحقق من الصلاحية
        if (!$conversation->hasParticipant($user)) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        // آخر رسالة عند العميل (لتجنب التكرار)
        $lastMessageId = $request->input('last_message_id');

        $query = $conversation->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc');

        // إذا كان هناك last_message_id، جلب الرسائل الأحدث فقط
        if ($lastMessageId) {
            // نحتاج وقت آخر رسالة تم استلامها
            $lastMessage = Message::find($lastMessageId);
            if ($lastMessage) {
                $query->where('created_at', '>', $lastMessage->created_at);
            }
        }

        $messages = $query->get()->map(function ($message) use ($user) {
            return [
                'id'              => $message->id,
                'body'            => $message->body,
                'sender_id'       => $message->user_id,
                'sender_name'     => $message->sender->name,
                'is_mine'         => $message->user_id === $user->id,
                'created_at'      => $message->created_at->toIso8601String(),
                'created_at_human' => $message->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'messages'   => $messages,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * تعليم كل رسائل المحادثة كمقروءة
     */
    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $user = Auth::user();

        // ✅ التحقق من الصلاحية
        if (!$conversation->hasParticipant($user)) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        // تحديث last_read_at
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}