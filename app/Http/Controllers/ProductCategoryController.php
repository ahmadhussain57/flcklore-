<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount('products')
            ->orderBy('name')
            ->paginate(20);

        return view('products.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('products.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']);
        $counter = 1;
        while (ProductCategory::where('slug', $slug)->exists()) {
            $slug = Str::slug($validated['name']) . '-' . $counter++;
        }

        ProductCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('products.categories.index')
            ->with('success', 'تم إنشاء التصنيف بنجاح.');
    }

    public function edit(ProductCategory $category)
    {
        return view('products.categories.edit', compact('category'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('products.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroy(ProductCategory $category)
    {
        $category->delete();

        return redirect()->route('products.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح.');
    }
}