<div class="row">
    <div class="col-md-10 px-4">
        <div class="card">
            <div class="card-header">{{ __('Budget Status') }}  - {{$titulo}}</div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if (isset($status))
                <table class="table table-bordered table-responsive">
                    <thead style="border-bottom: 2px solid var(--color-verde-claro);">
                        <tr style="background-color:#EBEDEF">
                            <th 
                                class="pe-pointer" 
                                wire:click="order('id')" 
                                style="color: var(--color-verde-oscuro); width:100px" 
                                scope="col" >
                                ID
                                @if ($sort == 'id')
                                    @if ($direction == 'asc')
                                        <i class="fas fa-sort-amount-up-alt float-end mt-1"></i>
                                    @else
                                        <i class="fas fa-sort-amount-down-alt float-end mt-1"></i>
                                    @endif
                                @else
                                    <i class="fas fa-sort float-end mt-1"></i>
                                @endif
                            </th>
                            <th 
                                class="pe-pointer" 
                                wire:click="order('name')" 
                                style="color: var(--color-verde-oscuro)" 
                                scope="col">
                                Status 
        
                                @if ($sort == 'name')
                                    @if ($direction == 'asc')
                                        <i class="fas fa-sort-amount-up-alt float-end mt-1"></i>
                                    @else
                                        <i class="fas fa-sort-amount-down-alt float-end mt-1"></i>
                                    @endif
                                @else
                                    <i class="fas fa-sort float-end mt-1"></i>
                                @endif
                            </th>

                            <th 
                                class="pe-pointer" 
                                style="color: var(--color-verde-oscuro)" 
                                scope="col">
                                Actions 
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($status as $statu)
            
                    <tr>
                        <td>{{ $statu->id }}</td>
                        <td>{{ $statu->name }}</td>
                        
                        <td><button 
                            wire:click="title('Titulo render')">Cambiar titulo</button>
                        </div></td>
            
                    </tr>
                    @endforeach
                    </tbody>
                </table> 
            @endif
            @if ($status->hasPages())
                <div class="px-5 py-3">
                    {{$status->links()}}
                </div>
            @endif
        </div>
    </div>
</div>
