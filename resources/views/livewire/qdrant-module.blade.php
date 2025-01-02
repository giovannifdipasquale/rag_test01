<div class="container mt-5">
    <div class="border p-3">
        <h3 class="text-center">Qdrant Module</h3>
        <div class="row justify-content-center align-items-center flex-column">
            <div class="col-md-6 ">
                <form>
                    <div class="mb-3">
                        <label for="prompt" class="form-label">Enter Prompt</label>
                        <input type="text" id="prompt" wire:model="text" class="form-control"
                            placeholder="Type your prompt here">
                    </div>
                    <div class="buttons d-flex gap-2 my-3 w-100">
                        <button type="button" wire:click="add" class="btn btn-dark w-100">Add</button>
                        <button type="button" wire:click="search" class="btn btn-dark w-100">Search</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="border p-3" style="min-height: 100px;">
                    <h5 class="text-muted">Generated Output:</h5>
                    <p wire:loading wire:target="show" class="text-muted">Loading...</p>
                    <div wire:loading.remove wire:target="show">
                        @if (isset($results))
                        @foreach($results as $result)
                        <p>{{ $result['payload']['question'] }}</p>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>