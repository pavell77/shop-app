<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Додати новий товар') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('products.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    
                    <div>
                        <x-input-label for="name" :value="__('Назва товару')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category_id" :value="__('Категорія')" />
                        <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-700">
                            <option value="">-- Оберіть категорію --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Опис')" />
                        <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="image" :value="__('Зображення товару')" />
                        <input type="file" name="image" id="image" class="mt-1 block w-full text-gray-700" accept="image/*">
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="price" :value="__('Ціна ($)')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="stock" :value="__('Кількість на складі')" />
                            <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Зберегти товар') }}</x-primary-button>
                        <a href="{{ route('products.index') }}" class="text-sm text-gray-600 underline">Скасувати</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>