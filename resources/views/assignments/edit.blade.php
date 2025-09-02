<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Assignment</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Update device assignment details</p>
                </div>
                <a href="{{ route('assignments.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Assignments
                </a>
            </div>
        </div>

        {{-- Upload --}}
        <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="space-y-3 mt-2">
        @csrf
        <input type="hidden" name="attachable_type" value="assignment">  {{-- uses morphMap key --}}
        <input type="hidden" name="attachable_id" value="{{ $assignment->id }}">
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="category" class="border rounded p-2">
                <option value="">—</option>
                <option class="pr-4" value="contract">Contract</option>
                <option value="photo">Photo</option>
                <option value="invoice">Invoice</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="mb-2">
            <label class="block text-sm font-medium mb-1">Files</label>
            <input type="file" name="files[]" multiple class="border rounded p-2 w-full">
            @error('files.*') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        <x-primary-button>Upload</x-primary-button>
        </form>

        {{-- List --}}
        <div class="mt-6 mb-2">
            <h3 class="font-semibold mb-2">Attachments</h3>
            <ul class="divide-y border rounded">
                @forelse($assignment->attachments as $att)
                <li class="flex items-center justify-between p-3">
                    <div class="truncate">
                    <a class="text-indigo-600 hover:underline" href="{{ route('attachments.download', $att) }}">
                        {{ $att->original_name }}
                    </a>
                    <span class="text-gray-500 text-xs ml-2">({{ number_format($att->size/1024,1) }} KB) • {{ $att->category ?? '—' }}</span>
                    </div>
                    <form method="POST" action="{{ route('attachments.destroy', $att) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline" onclick="return confirm('Delete this file?')">Delete</button>
                    </form>
                </li>
                @empty
                <li class="p-3 text-gray-500">No attachments yet.</li>
                @endforelse
            </ul>
        </div>


        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
            <form method="POST" action="{{ route('assignments.update', $assignment) }}" class="p-6">
                @method('PUT')
                @include('assignments._form', ['assignment' => $assignment])
            </form>
        </div>
    </div>
</x-app-layout>
