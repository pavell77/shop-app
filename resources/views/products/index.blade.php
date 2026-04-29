<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Каталог товарів') }}
        </h2>
        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            + Додати товар
        </a>
    </x-slot>

    @if (session('success'))
    <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-lg">
            {{ session('success') }}
    </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500">
                            Категорія: {{ $product->category->name ?? 'Без категорії' }}
                        </p>
                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($product->description, 100) }}</p>
                        <div class="flex items-center space-x-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded shadow">
                            @else
                                <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-400 text-xs">
                                    No Photo
                                </div>
                            @endif
                            
                            <div>
                                <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                                </div>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-xl font-semibold text-indigo-600">${{ $product->price }}</span>
                            <span class="text-xs text-gray-400">Stock: {{ $product->stock }}</span>
                        </div>
                        <div class="mt-4">
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition duration-150">
                                    Додати в кошик
                                </button>
                            </form>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="text-xs bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                Редагувати
                            </a>

                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Ви впевнені?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Видалити
                                </button>
                            </form>
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