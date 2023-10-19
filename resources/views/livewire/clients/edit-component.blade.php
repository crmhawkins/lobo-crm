<div class="container">

    <div class="d-flex justify-content-evenly align-items-center">
        <h1 class="">Editar Cliente</h1>
    </div>
    <hr>

    {{-- {{$tipoCliente}} --}}
    @if ($tipoCliente == 1)
        <form class="container" wire:submit.prevent="submitCliente">
            <form wire:submit.prevent="submit">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                <div class="mb-3 row d-flex align-items-center">
                    <label for="nameCliente" class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" wire:model="nameCliente" name="nameCliente" id="nameCliente" placeholder="Nombre">
                      @error('nameCliente') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="firstSurname" class="col-sm-2 col-form-label">Primer Apellido</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" wire:model="firstSurname" name="firstSurname" id="firstSurname" placeholder="Primer apellido">
                      @error('firstSurname') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="lastSurname" class="col-sm-2 col-form-label">Segundo Apellido</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" wire:model="lastSurname" name="lastSurname" id="lastSurname" placeholder="Segundo apellido">
                      @error('lastSurname') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="dni" class="col-sm-2 col-form-label">DNI</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="dni" class="form-control" name="dni" id="taxNumber" placeholder="DNI (0000000B)">
                      @error('dni') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="emailCliente" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                    <input type="email" wire:model="emailCliente" class="form-control" name="emailCliente" id="emailCliente" placeholder="Direccion de correo electronico">
                    @error('emailCliente') <span class="text-danger">{{ $message }}</span> @enderror    
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="adressCliente" class="col-sm-2 col-form-label">Direccion</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="adressCliente" class="form-control" name="adressCliente" id="adressCliente" placeholder="C/ Direccion de la empresa">
                      @error('adressCliente') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="ciudadCliente" class="col-sm-2 col-form-label">Ciudad</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="ciudadCliente" class="form-control" name="ciudadCliente" id="ciudadCliente" placeholder="Ciudad">
                      @error('ciudadCliente') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="provinceCliente" class="col-sm-2 col-form-label">Provincia</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="provinceCliente" class="form-control" name="provinceCliente" id="provinceCliente" placeholder="Provincia">
                      @error('provinceCliente') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="postCodeCliente" class="col-sm-2 col-form-label">Codigo Postal</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="postCodeCliente" class="form-control" name="postCodeCliente" id="postCodeCliente" placeholder="Codigo Postal (00000)">
                      @error('postCodeCliente') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-info">Guardar</button>
                </div>
                
                
            </form>
        </form>
    @elseif($tipoCliente == 2)
        <form wire:submit.prevent="submit" class="container">
            <div class="mb-3 row d-flex align-items-center">
                <label for="nameEmpresa" class="col-sm-2 col-form-label">Nombre de la empresa</label>
                <div class="col-sm-10">
                <input type="text" wire:model="nameEmpresa" class="form-control" name="nameEmpresa" id="nameEmpresa" placeholder="Nombre de la empresa">
                @error('nameEmpresa') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <label for="taxNumber" class="col-sm-2 col-form-label">CIF</label>
                <div class="col-sm-10">
                <input type="text"wire:model="taxNumber" class="form-control" name="taxNumber" id="taxNumber" placeholder="CIF">
                @error('taxNumber') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                <input type="email" wire:model="email" class="form-control" name="email" id="email" placeholder="Direccion de correo electronico">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror

                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <label for="address" class="col-sm-2 col-form-label">Direccion de la Compañia</label>
                <div class="col-sm-10">
                <input type="text"wire:model="address" class="form-control" name="address" id="address" placeholder="Direccion de la Compañia">
                @error('address') <span class="text-danger">{{ $message }}</span> @enderror

                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <label for="ciudad" class="col-sm-2 col-form-label">Ciudad</label>
                <div class="col-sm-10">
                <input type="text"wire:model="ciudad" class="form-control" name="ciudad" id="ciudad" placeholder="Ciudad">
                @error('ciudad') <span class="text-danger">{{ $message }}</span> @enderror

                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <label for="province" class="col-sm-2 col-form-label">Provincia</label>
                <div class="col-sm-10">
                <input type="text"wire:model="province" class="form-control" name="province" id="province" placeholder="Provincia">
                @error('province') <span class="text-danger">{{ $message }}</span> @enderror

                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <label for="postCode" class="col-sm-2 col-form-label">Codigo Postal</label>
                <div class="col-sm-10">
                <input type="text"wire:model="postCode" class="form-control" name="postCode" id="postCode" placeholder="Codigo Postal">
                @error('postCode') <span class="text-danger">{{ $message }}</span> @enderror

                </div>
            </div>
            <div class="mb-3 row d-flex align-items-center">
                <button type="submit" class="btn btn-outline-info">Guardar</button>
            </div>
        </form>
    @else
    <div class="text-center mt-3 w-50 m-auto">
            <hr class="border-secondary">
            <h5 class="text-secondary fs-6 fst-italic fw-light">Seleccione el tipo de cliente a crear</h5>
        </div>
    @endif
    
</div>
