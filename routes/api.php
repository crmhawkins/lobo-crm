<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;

use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\MercaderiaController;
use App\Http\Controllers\ProductosPedidoController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMercaderiaController;
use App\Http\Controllers\MaterialesProductoController;
use App\Http\Controllers\CajaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

 Route::get('/getClientes', [ClienteController::class, 'indexApi']);

 Route::get('/getProveedores', [ProveedoresController::class, 'indexApi']);

 Route::get('/getPedidos', [PedidoController::class, 'indexApi']);

 Route::get('/getProductos', [ProductosController::class, 'indexApi']);

 Route::get('/getProduccion', [ProduccionController::class, 'indexApi']);

 Route::get('/getFacturas', [FacturaController::class, 'indexApi']);

 Route::get('/getUsuarios', [UsuarioController::class, 'indexApi']);

 Route::get('/getMercaderia', [MercaderiaController::class, 'indexApi']);

 Route::get('/getCaja', [CajaController::class, 'indexApi']);
