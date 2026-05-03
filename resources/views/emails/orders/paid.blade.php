<x-mail::message>
# Дякуємо за оплату, {{ $order->user->name }}!

Ваше замовлення **№{{ $order->id }}** на суму **{{ $order->total_price }} грн** успішно оплачено.

Ми вже готуємо товари до відправки.

<x-mail::button :url="route('dashboard')">
Переглянути замовлення
</x-mail::button>

Дякуємо, що обрали наш магазин!<br>
{{ config('app.name') }}
</x-mail::message>