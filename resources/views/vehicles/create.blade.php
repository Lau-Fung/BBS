<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">New Vehicle</h2></x-slot>
    <form method="POST" action="{{ route('vehicles.store') }}">
        @include('vehicles._form', ['vehicle' => new \App\Models\Vehicle()])
    </form>
</x-app-layout>
