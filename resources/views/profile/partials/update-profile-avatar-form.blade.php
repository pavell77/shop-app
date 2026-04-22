<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Avatar') }}
        </h2>
    </header>

    {{-- Блок відображення поточного аватара --}}
    <div class="mt-4 flex items-center gap-4">
        @if (auth()->user()->avatar)
            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                 alt="Avatar" 
                 class="w-20 h-20 rounded-full object-cover border border-gray-300">
        @else
            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                {{ __('No') }}
            </div>
        @endif
    </div>

    <form method="post" action="{{ route('profile.update-avatar') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" :value="__('Upload new avatar')" />
            <input type="file" id="avatar" name="avatar" class="mt-1 block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100" accept="image/*" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'avatar-updated')
                <p class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>