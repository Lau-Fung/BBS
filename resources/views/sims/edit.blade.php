<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Edit SIM</h2></x-slot>
    <form method="POST" action="{{ route('sims.update',$sim) }}">
        @method('PUT')
        @include('sims._form')
    </form>
</x-app-layout>
