<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Edit Record') }}</h2></x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 sm:rounded-lg p-6">
            <form method="POST" action="{{ route('records.update',$record) }}">
                @csrf @method('PUT')
                <div>
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                  :value="old('title',$record->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div class="mt-6 flex gap-3">
                    <x-primary-button>{{ __('Update') }}</x-primary-button>
                    <a href="{{ route('records.index') }}" class="underline">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
