<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ConversationController extends Controller
{
    /**
     * عرض كل محادثات المستخدم (مرتبة حسب آخر رسالة)
     */
    public function index(): View
    {
        $user = Auth::user();

        // جلب المحادثات التي يشارك فيها المستخدم
        $conversations = Conversation::with([
                'users',
                'participants',
                'messages' => function ($query) {
                    $query->latest('created_at')->limit(1);
                },
            ])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        // إضافة بعض البيانات لكل محادثة (الطرف الآخر + unread count)
        $conversations->each(function ($conversation) use ($user) {
            $conversation->other_user = $conversation->getOtherParticipant($user);
            $conversation->unread_count = $conversation->unreadCountFor($user);
            $conversation->last_message = $conversation->lastMessage();
        });

        return view('messages.index', compact('conversations', 'user'));
    }

    /**
     * عرض نموذج بدء محادثة جديدة
     */
    public function create(): View
    {
        $user = Auth::user();

        // جلب المستخدمين الآخرين (بدون المستخدم الحالي)
        // للبحث، سنستخدم Alpine.js على الواجهة
        $users = User::where('id', '!=', $user->id)
            ->orderBy('name')
            ->limit(50)
            ->get();

        return view('messages.create', compact('users'));
    }

    /**
     * إنشاء محادثة جديدة أو إرجاع محادثة موجودة
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
        ], [
            'recipient_id.required' => 'يجب اختيار مستخدم.',
            'recipient_id.exists'   => 'المستخدم غير موجود.',
        ]);

        $currentUser = Auth::user();
        $recipientId = $validated['recipient_id'];

        // منع محادثة مع النفس
        if ($recipientId === $currentUser->id) {
            return back()->with('error', 'لا يمكنك بدء محادثة مع نفسك.');
        }

        // ✅ البحث عن محادثة موجودة بين المستخدمين
        $existingConversation = Conversation::whereHas('participants', function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id);
            })
            ->whereHas('participants', function ($q) use ($recipientId) {
                $q->where('user_id', $recipientId);
            })
            ->has('participants', '=', 2) // محادثة ثنائية فقط
            ->first();

        if ($existingConversation) {
            // محادثة موجودة → اذهب إليها
            return redirect()->route('messages.show', $existingConversation);
        }

        // ✅ إنشاء محادثة جديدة
        $conversation = DB::transaction(function () use ($currentUser, $recipientId) {
            $conversation = Conversation::create([
                'created_by' => $currentUser->id,
                'last_message_at' => null,
            ]);

            // إضافة المشاركين
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $currentUser->id,
                'last_read_at' => now(), // أنشأها، فهو قرأها
            ]);

            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $recipientId,
                'last_read_at' => null,
            ]);

            return $conversation;
        });

        return redirect()
            ->route('messages.show', $conversation)
            ->with('success', 'تم بدء المحادثة.');
    }

    /**
     * عرض محادثة محددة
     */
    public function show(Conversation $conversation): View
    {
        $user = Auth::user();

        // ✅ التحقق من أن المستخدم مشارك في المحادثة
        if (!$conversation->hasParticipant($user)) {
            abort(403, 'غير مصرح لك بعرض هذه المحادثة.');
        }

        // تحميل العلاقات
        $conversation->load(['users', 'participants']);

        // جلب الرسائل (مع المرسل)
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        // الطرف الآخر
        $otherUser = $conversation->getOtherParticipant($user);

        // ✅ تعليم كل الرسائل كمقروءة (تحديث last_read_at)
        $conversation->participants()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        // قائمة المحادثات الجانبية
        $conversations = Conversation::with(['users', 'participants'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $conversations->each(function ($c) use ($user) {
            $c->other_user = $c->getOtherParticipant($user);
            $c->unread_count = $c->unreadCountFor($user);
            $c->last_message = $c->lastMessage();
        });

        return view('messages.show', compact(
            'conversation',
            'messages',
            'otherUser',
            'conversations',
            'user'
        ));
    }
}