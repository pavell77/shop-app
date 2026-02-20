<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Беремо всі активні товари з пагінацією по 12 на сторінку
        $products = Product::where('is_active', true)->paginate(12);

        // Передаємо їх у шаблон
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Отримуємо всі категорії з бази
        $categories = Category::all();
        
        // Передаємо їх у в'юшку
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Валідація — це безпека!
        $validated = $request->validate([
            'name' => 'required|min:3|max:255|unique:products,name,' . ($product->id ?? ''),
            'description' => 'required|min:10',
            'price' => 'required|numeric|gt:0', // gt:0 означає "greater than 0"
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Додаємо автоматичну генерацію slug
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = true;

        // Створюємо запис у базі
        Product::create($validated);

        // Повертаємося до списку з повідомленням про успіх
        return redirect()->route('products.index')->with('success', 'Товар успішно додано!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all(); // Додаємо отримання категорій
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    // 2. Оновити дані в базі
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|max:255|unique:products,name,' . ($product->id ?? ''),
            'description' => 'required|min:10',
            'price' => 'required|numeric|gt:0', // gt:0 означає "greater than 0"
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Товар оновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Товар видалено!');
    }
}
