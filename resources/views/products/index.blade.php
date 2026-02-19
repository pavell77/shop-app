<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Каталог товарів') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($product->description, 100) }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-xl font-semibold text-indigo-600">${{ $product->price }}</span>
                            <span class="text-xs text-gray-400">Stock: {{ $product->stock }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>