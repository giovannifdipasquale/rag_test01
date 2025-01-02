<div class="container mt-5">
    <div class="border p-3">
        <h3 class="text-center">ChatGPT</h3>
        <div class="row justify-content-center align-items-center flex-column">
            <div class="col-md-6">
                <form wire:submit.prevent="generateOutput">
                    <div class="">
                        <label for="prompt" class="form-label">Enter Prompt</label>
                        <input type="text" id="prompt" wire:model="prompt" class="form-control"
                            placeholder="Type your prompt here">
                    </div>
                    <button type="submit" class="btn btn-dark my-3 w-100">Generate</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="border p-3" style="min-height: 100px;">
                    <h5 class="text-muted">Generated Output:</h5>
                    <p wire:loading wire:target="generateOutput" class="text-muted">Loading...</p>
                    <div wire:loading.remove wire:target="generateOutput">
                        @foreach ($messages as $message)
                        <div class="bg-light p-2 mb-2">
                            {{ $message['role'] . ":" . " " . $message['content'] }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>