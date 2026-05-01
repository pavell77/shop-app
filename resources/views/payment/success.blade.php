<x-guest-layout>
    <div class="py-12 text-center">
        <h2 class="text-2xl font-bold">Оплата успішна!</h2>
        <p>Вас буде перенаправлено назад через мить...</p>
        <script>
            setTimeout(() => {
                window.location.href = "{{ route('dashboard') }}";
            }, 2000);
        </script>
    </div>
</x-guest-layout>