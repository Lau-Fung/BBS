<x-app-layout>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="mb-8 flex items-center justify-between">
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white">New Carrier</h1>
			<a href="{{ route('carriers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">Back</a>
		</div>

		<div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
			<div class="p-6">
				<form method="POST" action="{{ route('carriers.store') }}">
					@include('carriers._form', ['carrier'=> new \App\Models\Carrier()])
				</form>
			</div>
		</div>
	</div>
</x-app-layout>
