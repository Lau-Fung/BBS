<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">New Device</h2></x-slot>
    <form method="POST" action="{{ route('devices.store') }}">
        @include('devices._form', ['device'=> new \App\Models\Device(), 'deviceModels'=>$deviceModels])
    </form>
</x-app-layout>
