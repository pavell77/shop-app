<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Каталог товарів') }}
            </h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Додати товар
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="font-medium text-sm text-green-600 bg-green-100 p-4 rounded-lg text-center shadow-sm border border-green-200">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6" x-data="{}">
                @foreach($products as $product)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500 mb-2">
                                Категорія: {{ $product->category->name ?? 'Без категорії' }}
                            </p>
                            
                            <div class="flex items-center space-x-4 mb-4">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded shadow">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-400 text-xs text-center p-1">
                                        No Photo
                                    </div>
                                @endif
                                <p class="text-gray-600 text-sm leading-tight">{{ Str::limit($product->description, 50) }}</p>
                            </div>

                            <div class="mt-auto flex justify-between items-center">
                                <span class="text-xl font-semibold text-indigo-600">${{ $product->price }}</span>
                                <span class="text-xs text-gray-400">Stock: {{ $product->stock }}</span>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <!-- AJAX Кнопка: прибрано тег form для уникнення конфліктів -->
                            <button 
                                type="button"
                                @click.prevent="
                                console.log('click');
                                    fetch('{{ route('cart.add', $product) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(response => {
                                        if (!response.ok) throw new Error('Помилка мережі');
                                        return response.json();
                                    })
                                    .then(data => {
                                        // Відправляємо подію для оновлення лічильника в navigation.blade.php
                                        $dispatch('cart-updated', { count: data.totalCount });
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                    })
                                "
                                class="w-full flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:scale-95 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 shadow-sm"
                            >
                                Додати в кошик
                            </button>

                            <div class="flex gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="flex-1 text-center text-xs bg-yellow-500 text-white px-3 py-1.5 rounded hover:bg-yellow-600 transition shadow-sm">
                                    Редагувати
                                </a>

                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Ви впевнені?')" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-xs bg-red-500 text-white px-3 py-1.5 rounded hover:bg-red-600 transition shadow-sm">
                                        Видалити
                                    </button>
                                </form>
                            </div>
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