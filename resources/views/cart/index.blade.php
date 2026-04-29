<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Твій кошик') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(empty($cart))
                    <p class="text-gray-500">Кошик порожній.</p>
                @else
                    <table class="table-auto w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Товар</th>
                                <th class="py-2">Ціна</th>
                                <th class="py-2">Кількість</th>
                                <th class="py-2">Разом</th>
                                <th class="py-2">Дія</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $id => $details)
                            <tr class="border-b">
                                <td class="py-4">{{ $details['name'] }}</td>
                                <td class="py-4">{{ number_format($details['price'], 2) }} грн</td>
                                <td class="py-4">{{ $details['quantity'] }}</td>
                                <td class="py-4">{{ number_format($details['price'] * $details['quantity'], 2) }} грн</td>
                                <td class="py-4">
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Видалити</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                                Оформити замовлення
                            </button>
                        </form>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>