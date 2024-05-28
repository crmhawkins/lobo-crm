<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\MercaderiaController;
use App\Http\Controllers\ProductosPedidoController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\OrdenMercaderiaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMercaderiaController;
use App\Http\Controllers\MaterialesProductoController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\Test;
use App\Http\Middleware\IsAdmin;
use FontLib\Table\Type\name;

use App\Http\Controllers\ConfiguracionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::name('inicio')->get('/', function () {
    return view('auth.login');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');



Route::group(['middleware' => 'is.admin', 'prefix' => 'admin'], function () {

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index');
    // Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes-create', [ClienteController::class, 'create'])->name('clientes.create');
    Route::get('/clientes-edit/{id}', [ClienteController::class, 'edit'])->name('clientes.edit');

    Route::get('/proveedores', [ProveedoresController::class, 'index'])->name('proveedores.index');
    Route::get('/proveedores-create', [ProveedoresController::class, 'create'])->name('proveedores.create');
    Route::get('/proveedores-edit/{id}', [ProveedoresController::class, 'edit'])->name('proveedores.edit');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos-create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::get('/pedidos-edit/{id}', [PedidoController::class, 'edit'])->name('pedidos.edit');

    Route::get('/productos', [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/productos-create', [ProductosController::class, 'create'])->name('productos.create');
    Route::get('/productos-edit/{id}', [ProductosController::class, 'edit'])->name('productos.edit');

    Route::get('/materiales-producto', [MaterialesProductoController::class, 'index'])->name('materiales-producto.index');
    Route::get('/materiales-producto-create/{id}', [MaterialesProductoController::class, 'create'])->name('materiales-producto.create');
    Route::get('/materiales-producto-edit/{id}', [MaterialesProductoController::class, 'edit'])->name('materiales-producto.edit');

    Route::get('/productos-pedido', [ProductosPedidoController::class, 'index'])->name('productos-pedido.index');
    Route::get('/productos-pedido-create', [ProductosPedidoController::class, 'create'])->name('productos-pedido.create');
    Route::get('/productos-pedido-edit/{id}', [ProductosPedidoController::class, 'edit'])->name('productos-pedido.edit');

    Route::get('/contabilidad', [ContabilidadController::class, 'index'])->name('productos-pedido.index');
    Route::get('/contabilidad-create', [ContabilidadController::class, 'create'])->name('productos-pedido.create');
    Route::get('/contabilidad-edit/{id}', [ContabilidadController::class, 'edit'])->name('productos-pedido.edit');

    Route::get('/produccion', [ProduccionController::class, 'index'])->name('produccion.index');
    Route::get('/produccion-create', [ProduccionController::class, 'create'])->name('produccion.create');
    Route::get('/produccion-edit/{id}', [ProduccionController::class, 'edit'])->name('produccion.edit');


    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas-create/{id}', [FacturaController::class, 'create'])->name('facturas.create');
    Route::get('/facturas-create', [FacturaController::class, 'create1'])->name('facturas.create');
    Route::get('/facturas-create-rectificativa', [FacturaController::class, 'create2'])->name('facturas.createrectificativa');

    Route::get('/facturas-edit/{id}', [FacturaController::class, 'edit'])->name('facturas.edit');
    Route::get('/facturas-pdf/{id}', [FacturaController::class, 'pdf'])->name('facturas.pdf');
    Route::get('/facturas-pdf-preview/{id}', [FacturaController::class, 'pdfPreview'])->name('facturas.pdfpreview');

    Route::get('/almacen', [AlmacenController::class, 'index'])->name('almacen.index');
    Route::get('/almacen-create/{id}', [AlmacenController::class, 'create'])->name('almacen.create');
    Route::get('/almacen-edit/{id}', [AlmacenController::class, 'edit'])->name('almacen.edit');
    Route::get('/almacen-pdf/{id}', [AlmacenController::class, 'pdf'])->name('almacen.pdf');

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock-create/{id}', [StockController::class, 'create'])->name('stock.create');
    Route::get('/stock-edit/{id}', [StockController::class, 'edit'])->name('stock.edit');
    Route::get('/stock-traspaso/{id}', [StockController::class, 'traspaso'])->name('traspaso.edit');
    Route::get('/stock/crear-qr', [StockController::class, 'crearQR'])->name('stock.crear-qr');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios-create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::get('/usuarios-edit/{id}', [UsuarioController::class, 'edit'])->name('usuarios.edit');

    Route::get('/mercaderia', [MercaderiaController::class, 'index'])->name('mercaderia.index');
    Route::get('/mercaderia-create', [MercaderiaController::class, 'create'])->name('mercaderia.create');
    Route::get('/mercaderia-edit/{id}', [MercaderiaController::class, 'edit'])->name('mercaderia.edit');

    Route::get('/orden-mercaderia', [OrdenMercaderiaController::class, 'index'])->name('orden-mercaderia.index');
    Route::get('/orden-mercaderia-create', [OrdenMercaderiaController::class, 'create'])->name('orden-mercaderia.create');
    Route::get('/orden-mercaderia-edit/{id}', [OrdenMercaderiaController::class, 'edit'])->name('orden-mercaderia.edit');

    Route::get('/stock-mercaderia', [StockMercaderiaController::class, 'index'])->name('stock-mercaderia.index');
    Route::get('/stock-mercaderia-create/{id}', [StockMercaderiaController::class, 'create'])->name('stock-mercaderia.create');
    Route::get('/stock-mercaderia-edit/{id}', [StockMercaderiaController::class, 'edit'])->name('stock-mercaderia.edit');
    Route::get('/stock-mercaderia/crear-qr', [StockMercaderiaController::class, 'crearQR'])->name('stock-mercaderia.crear-qr');
    Route::get('/stock-mercaderia/mostrar-qr', [StockMercaderiaController::class, 'mostrarQR'])->name('stock-mercaderia.mostrar-qr');

    Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
    Route::get('/caja-create-ingreso', [CajaController::class, 'createIngreso'])->name('caja.create-ingreso');
    Route::get('/caja-create-gasto', [CajaController::class, 'createGasto'])->name('caja.create-gasto');
    Route::get('/caja-edit/{id}', [CajaController::class, 'edit'])->name('caja.edit');

    //Route::get('/test/fechavencimiento', [Test::class, 'index'])->name('test.index');
    Route::get('/test/ivaAProductos', [Test::class, 'ivaAProductos'])->name('test.ivaAProductos'); //ok
    Route::get('/test/calcularIvayTotalFacturas', [Test::class, 'calcularIvayTotalFacturas'])->name('test.calcularIvayTotalFacturas'); //ok
    Route::get('/test/fixPedidos', [Test::class, 'fixPedidos'])->name('test.fixPedidos'); //ok


    //ruta configuracion
    Route::get('/configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
});
