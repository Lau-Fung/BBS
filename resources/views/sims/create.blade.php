<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">New SIM</h2></x-slot>
    <form method="POST" action="{{ route('sims.store') }}">
        @include('sims._form', ['sim'=> new \App\Models\Sim(), 'carriers'=>$carriers])
    </form>
</x-app-layout>
