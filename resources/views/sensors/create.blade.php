<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">New Sensor</h2></x-slot>
    <form method="POST" action="{{ route('sensors.store') }}">
        @include('sensors._form', ['sensor'=> new \App\Models\Sensor(), 'sensorModels'=>$sensorModels])
    </form>
</x-app-layout>
