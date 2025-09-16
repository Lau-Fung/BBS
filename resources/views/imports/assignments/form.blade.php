<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.assignments.import_assignments') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @php($clients = \App\Models\Client::orderBy('name')->pluck('name','id'))
                <form method="post" action="{{ route('imports.assignments.preview') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        {{-- <label class="block mb-2">{{ __('messages.assignments.default_client_optional') }}</label>
                        <select name="client_id" class="border rounded mb-4">
                            <option value="">{{ __('messages.assignments.none') }}</option>
                            @foreach($clients as $id => $name)
                                <option value="{{ $id }}" @selected(old('client_id')==$id)>{{ $name }}</option>
                            @endforeach
                        </select> --}}
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.assignments.excel_file') }}</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full border rounded p-2">
                        @error('file')
                          <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <x-primary-button>{{ __('messages.assignments.preview') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

