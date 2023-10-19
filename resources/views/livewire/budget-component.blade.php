    <div class="row">
        <div class="col-md-5 px-4">
            <div class="card">
                <div class="card-header">{{ __('Budget') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{$response}}

                    <button 
                    wire:click="title('Titulo render')">Cambiar nuevo</button>
                </div>
            </div>
        </div>
    </div>
