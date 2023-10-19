<div class="container">

    <div class="d-flex justify-content-evenly align-items-center">
        <h1 class="">Crear una Empresa</h1>
        <button wire:click="alerta()"  class="btn btn-info text-white rounded-circle"><i class="fa-solid fa-plus"></i></button>
    </div>
    <hr>
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="mb-3 row d-flex align-items-center">
            <label for="name" class="col-sm-2 col-form-label">Nombre de la Compañia</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" wire:model="name" name="name" id="name" placeholder="Nombre de su empresa...">
              @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="taxNumber" class="col-sm-2 col-form-label">CIF</label>
            <div class="col-sm-10">
              <input type="text" wire:model="taxNumber" class="form-control" name="taxNumber" id="taxNumber" placeholder="CIF (B0000000)">
              @error('taxNumber') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="adress" class="col-sm-2 col-form-label">Direccion de la Compañia</label>
            <div class="col-sm-10">
              <input type="text" wire:model="adress" class="form-control" name="adress" id="adress" placeholder="C/ Direccion de la empresa">
              @error('adress') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="ciudad" class="col-sm-2 col-form-label">Ciudad</label>
            <div class="col-sm-10">
              <input type="text" wire:model="ciudad" class="form-control" name="ciudad" id="ciudad" placeholder="Ciudad">
              @error('ciudad') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="province" class="col-sm-2 col-form-label">Provincia</label>
            <div class="col-sm-10">
              <input type="text" wire:model="province" class="form-control" name="province" id="province" placeholder="Provincia">
              @error('province') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="postCode" class="col-sm-2 col-form-label">Codigo Postal</label>
            <div class="col-sm-10">
              <input type="text" wire:model="postCode" class="form-control" name="postCode" id="postCode" placeholder="Codigo Postal (00000)">
              @error('postCode') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>
        
        
    </form>
</div>