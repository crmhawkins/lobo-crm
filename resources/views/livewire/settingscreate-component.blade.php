<div class="container">
    <div class="d-flex justify-content-evenly align-items-center">
        <h1 class="">Configuracion de la Empresa</h1>
        <a href="{{route('settings.store')}}" class="btn btn-info text-white rounded-circle"><i class="fa-solid fa-plus"></i></a>
    </div>
    <hr>
    <form action="">
        <div class="mb-3 row d-flex align-items-center">
            <label for="name" class="col-sm-2 col-form-label">Nombre de la Compañia</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="name" id="name" value="ejemplo">
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="taxNumber" class="col-sm-2 col-form-label">CIF</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="taxNumber" id="taxNumber" value="ejemplo">
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="adress" class="col-sm-2 col-form-label">Direccion de la Compañia</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="adress" id="adress" value="ejemplo">
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="ciudad" class="col-sm-2 col-form-label">Ciudad</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="ciudad" id="ciudad" value="ejemplo">
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="province" class="col-sm-2 col-form-label">Provincia</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="province" id="province" value="ejemplo">
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="postCode" class="col-sm-2 col-form-label">Codigo Postal</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="postCode" id="postCode" value="ejemplo">
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>
        
        
    </form>
</div>
