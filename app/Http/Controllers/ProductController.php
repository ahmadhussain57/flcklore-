<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMedia;
use App\Models\User;
use App\Notifications\ProductSubmittedForReview;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Cloudinary\Cloudinary;

class ProductController extends Controller
{
    /**
     * إنشاء كائن Cloudinary جاهز
     */
    private function cloudinary(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key'    => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    /**
     * عرض قائمة منتجات المستخدم مع الإحصائيات
     */
    public function index()
    {
        $user = Auth::user();

        $products = Product::with(['categories', 'media'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Product::where('user_id', $user->id)->count(),
            'published' => Product::where('user_id', $user->id)
                ->where('status', Product::STATUS_PUBLISHED)
                ->count(),
            'pending' => Product::where('user_id', $user->id)
                ->whereIn('status', [Product::STATUS_PENDING_REVIEW, Product::STATUS_PENDING_EDIT])
                ->count(),
            'draft' => Product::where('user_id', $user->id)
                ->where('status', Product::STATUS_DRAFT)
                ->count(),
            'rejected' => Product::where('user_id', $user->id)
                ->where('status', Product::STATUS_REJECTED)
                ->count(),
        ];

        return view('products.index', compact('products', 'stats'));
    }

    /**
     * عرض نموذج إنشاء منتج جديد
     */
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    /**
     * حفظ منتج جديد (حالة draft)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in([Product::TYPE_PHYSICAL, Product::TYPE_DIGITAL])],
            'price' => 'required|numeric|min:1|max:9999999.99',
            'sale_price' => 'nullable|numeric|min:1|lt:price',
            'stock_quantity' => 'nullable|integer|min:1',  // ✅
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:product_categories,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm,ogg,mov|max:51200',
        ], [
            'sale_price.lt' => 'سعر التخفيض يجب أن يكون أقل من السعر الأصلي.',
            'price.min'     => 'يجب أن يكون السعر 1 على الأقل.',
            'sale_price.min'=> 'يجب أن يكون سعر التخفيض 1 على الأقل.',
            'stock_quantity.min' => 'يجب أن تكون الكمية المتوفرة 1 على الأقل.',
        ]);

        // ✅ التحقق من المنتجات المادية
        if ($validated['type'] === Product::TYPE_PHYSICAL) {
            $request->validate([
                'stock_quantity' => 'required|integer|min:1',  // ✅ min:1
            ], [
                'stock_quantity.required' => 'يجب تحديد الكمية المتوفرة للمنتجات المادية.',
                'stock_quantity.min'      => 'يجب أن تكون الكمية المتوفرة 1 على الأقل للمنتجات المادية.',
            ]);
        } else {
            $validated['stock_quantity'] = null;
        }

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $product = Product::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'short_description' => $validated['short_description'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'status' => Product::STATUS_DRAFT,
        ]);

        if (!empty($validated['categories'])) {
            $product->categories()->sync($validated['categories']);
        }

        if ($request->hasFile('media')) {
            $this->uploadMedia($request->file('media'), $product);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'تم إنشاء المنتج بنجاح! يمكنك الآن تقديمه للمراجعة.');
    }

    /**
     * عرض منتج واحد
     */
    public function show(Product $product)
    {
        if ($product->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('edit_any_product', 'web')) {
            abort(403, 'غير مصرح لك بمشاهدة هذا المنتج.');
        }

        $product->load(['categories', 'media']);
        return view('products.show', compact('product'));
    }

    /**
     * عرض نموذج تعديل المنتج
     */
    public function edit(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المنتج.');
        }

        if (!in_array($product->status, [
            Product::STATUS_DRAFT,
            Product::STATUS_REJECTED,
            Product::STATUS_PENDING_EDIT,
        ])) {
            return redirect()
                ->route('products.index')
                ->with('error', 'لا يمكن تعديل هذا المنتج في حالته الحالية.');
        }

        $categories = ProductCategory::orderBy('name')->get();
        $product->load('media');

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * تحديث المنتج (يدعم النسخ للمنشورات + رفع/حذف الوسائط)
     */
    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المنتج.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'keywords' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in([Product::TYPE_PHYSICAL, Product::TYPE_DIGITAL])],
            'price' => 'required|numeric|min:1|max:9999999.99',  // ✅ min:1
            'sale_price' => 'nullable|numeric|min:1|lt:price',   // ✅ min:1
            'stock_quantity' => 'nullable|integer|min:1',         // ✅ min:1
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'categories' => 'nullable|array',
            'categories.*' => 'exists:product_categories,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm,ogg,mov|max:51200',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:product_media,id',
        ], [
            'sale_price.lt' => 'سعر التخفيض يجب أن يكون أقل من السعر الأصلي.',
            'price.min'     => 'يجب أن يكون السعر 1 على الأقل.',
            'sale_price.min'=> 'يجب أن يكون سعر التخفيض 1 على الأقل.',
            'stock_quantity.min' => 'يجب أن تكون الكمية المتوفرة 1 على الأقل.',
        ]);

        // ✅ التحقق من المنتجات المادية
        if ($validated['type'] === Product::TYPE_PHYSICAL) {
            $request->validate([
                'stock_quantity' => 'required|integer|min:1',  // ✅ min:1
            ], [
                'stock_quantity.required' => 'يجب تحديد الكمية المتوفرة للمنتجات المادية.',
                'stock_quantity.min'      => 'يجب أن تكون الكمية المتوفرة 1 على الأقل للمنتجات المادية.',
            ]);
        } else {
            $validated['stock_quantity'] = null;
        }

        // === حالة خاصة: إذا كان المنتج منشوراً، ننشئ نسخة جديدة ===
        if ($product->status === Product::STATUS_PUBLISHED) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $newProduct = Product::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => $slug,
                'short_description' => $validated['short_description'] ?? null,
                'keywords' => $validated['keywords'] ?? null,
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'price' => $validated['price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'status' => Product::STATUS_PENDING_EDIT,
                'original_id' => $product->id,
                'queued_at' => now(),
            ]);

            if (!empty($validated['categories'])) {
                $newProduct->categories()->sync($validated['categories']);
            }

            foreach ($product->media as $oldMedia) {
                $newProduct->media()->create([
                    'media_type' => $oldMedia->media_type,
                    'path' => $oldMedia->path,
                    'public_id' => $oldMedia->public_id,
                    'original_name' => $oldMedia->original_name,
                    'mime_type' => $oldMedia->mime_type,
                    'size' => $oldMedia->size,
                    'sort_order' => $oldMedia->sort_order,
                ]);
            }

            if ($request->hasFile('media')) {
                $this->uploadMedia($request->file('media'), $newProduct);
            }

            $this->notifyReviewers(new ProductSubmittedForReview($newProduct));

            return redirect()
                ->route('products.index')
                ->with('success', 'تم إرسال طلب تعديل المنتج للمراجعة. سيتم تحديثه بعد الموافقة.');
        }

        // === التحديث العادي ===
        $product->update([
            'title' => $validated['title'],
            'short_description' => $validated['short_description'] ?? null,
            'keywords' => $validated['keywords'] ?? null,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'] ?? null,
            'sku' => $validated['sku'] ?? null,
            'status' => $product->status === Product::STATUS_REJECTED
                ? Product::STATUS_DRAFT
                : $product->status,
        ]);

        $product->categories()->sync($validated['categories'] ?? []);

        if (!empty($validated['delete_media'])) {
            $mediaToDelete = ProductMedia::whereIn('id', $validated['delete_media'])
                ->where('product_id', $product->id)
                ->get();

            foreach ($mediaToDelete as $media) {
                $this->deleteMediaFromCloudinary($media);
                $media->delete();
            }
        }

        if ($request->hasFile('media')) {
            $this->uploadMedia($request->file('media'), $product);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'تم تحديث المنتج بنجاح.');
    }

    /**
     * تقديم المنتج للمراجعة
     */
    public function submit(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء.');
        }

        if (!in_array($product->status, [Product::STATUS_DRAFT, Product::STATUS_REJECTED])) {
            return redirect()
                ->route('products.index')
                ->with('error', 'هذا المنتج غير قابل للتقديم للمراجعة.');
        }

        $product->submitToQueue();

        $this->notifyReviewers(new ProductSubmittedForReview($product));

        return redirect()
            ->route('products.index')
            ->with('success', 'تم تقديم المنتج للمراجعة بنجاح.');
    }

    /**
     * حذف المنتج (مع حذف الوسائط من Cloudinary)
     */
    public function destroy(Product $product)
    {
        if ($product->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('delete_any_product', 'web')) {
            abort(403, 'غير مصرح لك بحذف هذا المنتج.');
        }

        foreach ($product->media as $media) {
            $this->deleteMediaFromCloudinary($media);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح.');
    }

    /**
     * إرسال إشعار لجميع مديري التسويق
     */
    private function notifyReviewers($notification): void
    {
        $reviewers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['marketing_admin']);
        })->get();

        foreach ($reviewers as $reviewer) {
            $reviewer->notify($notification);
        }
    }

    /**
     * رفع الوسائط إلى Cloudinary وربطها بالمنتج
     */
    private function uploadMedia(array $files, Product $product): void
    {
        try {
            $cloudinary = $this->cloudinary();

            $lastOrder = $product->media()->max('sort_order') ?? -1;

            foreach ($files as $index => $file) {
                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'folklore/products',
                    'resource_type' => 'auto',
                ]);

                $mimeType = $file->getMimeType();
                $mediaType = str_starts_with($mimeType, 'image') ? 'image' : 'video';

                $product->media()->create([
                    'media_type' => $mediaType,
                    'path' => $result['secure_url'],
                    'public_id' => $result['public_id'] ?? null,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'size' => $file->getSize(),
                    'sort_order' => $lastOrder + $index + 1,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
        }
    }

    /**
     * حذف وسيط من Cloudinary
     */
    private function deleteMediaFromCloudinary(ProductMedia $media): void
    {
        if (!$media->public_id) {
            return;
        }

        try {
            $cloudinary = $this->cloudinary();
            $resourceType = $media->media_type === 'video' ? 'video' : 'image';
            $cloudinary->uploadApi()->destroy($media->public_id, [
                'resource_type' => $resourceType,
            ]);
        } catch (\Exception $e) {
            \Log::error('Cloudinary delete failed: ' . $e->getMessage());
        }
    }
}