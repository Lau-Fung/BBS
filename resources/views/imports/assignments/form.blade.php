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
                    <div class="mb-3">
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
                    <button type="submit" class="inline-flex items-center px-6 py-3 font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                            style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                            onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                            onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('messages.assignments.preview') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

