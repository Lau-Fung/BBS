<x-app-layout>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="mb-8 flex items-center justify-between">
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.sensors.new') }}</h1>
			<a href="{{ route('sensors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">{{ __('messages.common.back') }}</a>
		</div>

		<div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
			<div class="p-6">
				<form method="POST" action="{{ route('sensors.store') }}">
					@include('sensors._form', ['sensor'=> new \App\Models\Sensor(), 'sensorModels'=>$sensorModels])
				</form>
			</div>
		</div>
	</div>
</x-app-layout>
