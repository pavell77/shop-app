<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Оплата замовлення') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 border border-gray-200">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Замовлення №{{ $order->id }} сформовано!</h3>
                    <p class="text-gray-600 mb-6">Дякуємо, **{{ Auth::user()->name }}**. Ваше замовлення прийняте в обробку та чекає на оплату.</p>
                    
                    <div class="bg-gray-50 rounded-lg p-6 max-w-sm mx-auto mb-8">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">Сума до сплати:</span>
                            <span class="font-bold text-lg text-indigo-600">{{ number_format($order->total_price, 2) }} UAH</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Статус:</span>
                            <span class="text-orange-500 uppercase font-semibold">{{ $order->status }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Кнопка WayForPay -->
                        <div class="payment-form-wrapper">
                            {!! $paymentForm !!}
                        </div>

                        <p class="text-xs text-gray-400 mt-4">
                            Натискаючи кнопку, ви перейдете на захищену сторінку платіжної системи WayForPay.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 transition">
                    &larr; Повернутися до магазину
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Стилізація кнопки, яку генерує SDK, щоб вона вписувалася в дизайн Tailwind */
        .payment-form-wrapper input[type="submit"], 
        .payment-form-wrapper button {
            background-color: #4f46e5 !important; /* Indigo-600 */
            color: white !important;
            padding: 0.75rem 2rem !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-size: 0.875rem !important;
            transition: all 0.2s !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        .payment-form-wrapper input[type="submit"]:hover {
            background-color: #4338ca !important; /* Indigo-700 */
            transform: translateY(-1px) !important;
        }
    </style>
</x-app-layout>