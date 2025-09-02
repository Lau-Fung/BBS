<x-app-layout>
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Import Assignments</h1>

    @if(session('status'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <form action="{{ route('imports.assignments.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block font-medium mb-1">Excel file (.xlsx, .xls, .csv)</label>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="border p-2 rounded w-full" required>
            @error('file')
                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="bg-indigo-600 px-4 py-2 rounded">Preview</button>
    </form>
</div>
</x-app-layout>


