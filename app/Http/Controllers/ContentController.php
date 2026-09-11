<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\ContentSubmittedForReview;
use App\Notifications\ContentPendingEditSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Cloudinary\Cloudinary;

class ContentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $contents = Content::with(['categories', 'tags'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Content::where('user_id', $user->id)->count(),
            'published' => Content::where('user_id', $user->id)->where('status', Content::STATUS_PUBLISHED)->count(),
            'pending' => Content::where('user_id', $user->id)->whereIn('status', [Content::STATUS_PENDING_REVIEW, Content::STATUS_PENDING_EDIT])->count(),
            'draft' => Content::where('user_id', $user->id)->where('status', Content::STATUS_DRAFT)->count(),
            'rejected' => Content::where('user_id', $user->id)->where('status', Content::STATUS_REJECTED)->count(),
        ];

        return view('content.index', compact('contents', 'stats'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('content.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in([Content::TYPE_ARTICLE, Content::TYPE_IMAGE, Content::TYPE_VIDEO, Content::TYPE_AUDIO])],
            'body' => 'nullable|string',
            'keywords' => 'nullable|string|max:255',
            'historical_importance' => 'nullable|string|max:500',
            'geographic_location' => 'nullable|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm,ogg,mov,mp3,wav,aac,m4a|max:51200',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Content::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $content = Content::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'type' => $validated['type'],
            'body' => $validated['body'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'historical_importance' => $validated['historical_importance'] ?? null,
            'geographic_location' => $validated['geographic_location'] ?? null,
            'status' => Content::STATUS_DRAFT,
        ]);

        if (!empty($validated['categories'])) {
            $content->categories()->sync($validated['categories']);
        }
        if (!empty($validated['tags'])) {
            $content->tags()->sync($validated['tags']);
        }

        if ($request->hasFile('media')) {
            $this->uploadMedia($request->file('media'), $content, 'contents');
        }

        return redirect()
            ->route('content.index')
            ->with('success', 'تم إنشاء المحتوى بنجاح! يمكنك الآن تقديمه للمراجعة.');
    }

    public function show(Content $content)
    {
        if ($content->user_id !== Auth::id() && !auth()->user()->hasPermissionTo('edit_any_content', 'web')) {
            abort(403, 'غير مصرح لك بمشاهدة هذا المحتوى.');
        }

        $content->load(['categories', 'tags', 'media']);
        return view('content.show', compact('content'));
    }

    public function edit(Content $content)
    {
        if ($content->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المحتوى.');
        }

        if (!in_array($content->status, [
            Content::STATUS_DRAFT,
            Content::STATUS_REJECTED,
            Content::STATUS_PENDING_EDIT,
        ])) {
            return redirect()->route('content.index')->with('error', 'لا يمكن تعديل هذا المحتوى في حالته الحالية.');
        }

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('content.edit', compact('content', 'categories', 'tags'));
    }

    public function update(Request $request, Content $content)
    {
        if ($content->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المحتوى.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in([Content::TYPE_ARTICLE, Content::TYPE_IMAGE, Content::TYPE_VIDEO, Content::TYPE_AUDIO])],
            'body' => 'nullable|string',
            'keywords' => 'nullable|string',
            'historical_importance' => 'nullable|string',
            'geographic_location' => 'nullable|string|max:255',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // إذا كان منشوراً → ننشئ نسخة جديدة
        if ($content->status === Content::STATUS_PUBLISHED) {
            $newContent = Content::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
                'type' => $validated['type'],
                'body' => $validated['body'] ?? null,
                'keywords' => $validated['keywords'] ?? null,
                'historical_importance' => $validated['historical_importance'] ?? null,
                'geographic_location' => $validated['geographic_location'] ?? null,
                'status' => Content::STATUS_PENDING_EDIT,
                'original_id' => $content->id,
                'queued_at' => now(),
            ]);

            if (!empty($validated['categories'])) {
                $newContent->categories()->sync($validated['categories']);
            }
            if (!empty($validated['tags'])) {
                $newContent->tags()->sync($validated['tags']);
            }

            // ✅ إرسال إشعار للمدققين بوجود تعديل جديد
            $this->notifyReviewers(new ContentPendingEditSubmitted($newContent));

            return redirect()->route('content.index')->with('success', 'تم إرسال طلب تعديل المنشور للمراجعة.');
        }

        // تحديث عادي (مسودة أو مرفوض)
        $content->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'body' => $validated['body'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'historical_importance' => $validated['historical_importance'] ?? null,
            'geographic_location' => $validated['geographic_location'] ?? null,
            'status' => $content->status === Content::STATUS_REJECTED ? Content::STATUS_DRAFT : $content->status,
        ]);

        $content->categories()->sync($validated['categories'] ?? []);
        $content->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('content.index')->with('success', 'تم تحديث المحتوى بنجاح.');
    }

    public function submit(Request $request, Content $content)
    {
        if ($content->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء.');
        }

        if (!in_array($content->status, [Content::STATUS_DRAFT, Content::STATUS_REJECTED])) {
            return redirect()->route('content.index')->with('error', 'هذا المحتوى غير قابل للتقديم للمراجعة.');
        }

        $content->submitToQueue();

        // ✅ إرسال إشعار للمدققين
        $this->notifyReviewers(new ContentSubmittedForReview($content));

        return redirect()->route('content.index')->with('success', 'تم تقديم المحتوى للمراجعة بنجاح.');
    }

    public function destroy(Content $content)
    {
        if ($content->user_id !== Auth::id() && !auth()->user()->hasPermissionTo('delete_any_content', 'web')) {
            abort(403, 'غير مصرح لك بحذف هذا المحتوى.');
        }

        $content->delete();

        return redirect()->route('content.index')->with('success', 'تم حذف المحتوى بنجاح.');
    }

    /**
     * ✅ إرسال إشعار لجميع مدققي ومديري المحتوى
     */
    private function notifyReviewers($notification): void
    {
        // جلب المستخدمين الذين لديهم دور مدقق أو مدير محتوى
        $reviewers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['content_Reviewer', 'content_admin']);
        })->get();

        // إرسال الإشعار لكل مدقق
        foreach ($reviewers as $reviewer) {
            $reviewer->notify($notification);
        }
    }

    private function uploadMedia(array $files, Content $content, string $folder): void
    {
        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key'    => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
                'url' => [
                    'secure' => true,
                ],
            ]);

            foreach ($files as $index => $file) {
                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => "folklore/{$folder}",
                    'resource_type' => 'auto',
                ]);

                $mimeType = $file->getMimeType();
                $mediaType = str_starts_with($mimeType, 'image')
                    ? 'image'
                    : (str_starts_with($mimeType, 'video') ? 'video' : 'audio');

                $content->media()->create([
                    'media_type' => $mediaType,
                    'path' => $result['secure_url'],
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'size' => $file->getSize(),
                    'sort_order' => $index,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
        }
    }
}