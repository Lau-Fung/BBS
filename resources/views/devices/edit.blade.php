<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Edit Device</h2></x-slot>
    <form method="POST" action="{{ route('devices.update',$device) }}">
        @method('PUT')
        @include('devices._form')
    </form>
</x-app-layout>
