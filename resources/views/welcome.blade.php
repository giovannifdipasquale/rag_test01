<x-layout title="chat and qdrant">

    <div class="container">
        <div class="row d-flex">
            <div class="col-6">

                @livewire('chat')
            </div>
            <div class="col-6">
                @livewire('qdrant-module')
            </div>
        </div>
    </div>
</x-layout>