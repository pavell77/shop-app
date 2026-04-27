<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class ProductController extends Controller
{
    use AuthorizesRequests; // ДОДАЙ ЦЕЙ ТРЕЙТ
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('is_active', true)->paginate(12);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Перевіряємо, чи має користувач право створювати товари
        $this->authorize('create', Product::class);

        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Перевіряємо право створення перед валідацією
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|min:3|max:255|unique:products,name',
            'description' => 'required|min:10',
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Товар успішно додано!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Перегляд товару доступний всім (якщо не закрити окремо)
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|min:3|max:255|unique:products,name,' . $product->id,
            'description' => 'required|min:10',
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Товар оновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Товар видалено!');
    }
}