<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Edit Sensor</h2></x-slot>
    <form method="POST" action="{{ route('sensors.update',$sensor) }}">
        @method('PUT')
        @include('sensors._form')
    </form>
</x-app-layout>
