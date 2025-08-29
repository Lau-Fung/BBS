<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Edit Vehicle</h2></x-slot>
    <form method="POST" action="{{ route('vehicles.update',$vehicle) }}">
        @method('PUT')
        @include('vehicles._form')
    </form>
</x-app-layout>
