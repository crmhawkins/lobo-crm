<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetReferenceAutopincrementsController;
use App\Http\Controllers\BudgetStatuController;
use App\Http\Controllers\ClientsEmailController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProjectPriorityController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ArticulosController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\IvaController;
use App\Http\Controllers\ProductosCategoriesController;
use App\Http\Controllers\DepartamentosUserController;
use App\Http\Controllers\TipoGastoController;
use App\Http\Controllers\CategoriaEventoController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\AlbaranController;

use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ServicioCategoriaController;
use App\Http\Controllers\ServicioPackController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ResumenDiaController;
use App\Http\Controllers\ResumenSemanaController;
use App\Http\Controllers\ResumenMensualController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\MapKitController;
use App\Http\Livewire\Facturas\EditComponent;
use App\Http\Livewire\Facturas\IndexComponent as FacturasIndexComponent;
use App\Http\Livewire\Productos\IndexComponent;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\StockController;
use App\Http\Middleware\IsAdmin;
use FontLib\Table\Type\name;

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

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos-create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::get('/pedidos-edit/{id}', [PedidoController::class, 'edit'])->name('pedidos.edit');

    Route::get('/productos', [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/productos-create', [ProductosController::class, 'create'])->name('productos.create');
    Route::get('/productos-edit/{id}', [ProductosController::class, 'edit'])->name('productos.edit');

    Route::get('/productos-lote', [ProductosLoteController::class, 'index'])->name('productosLote.index');
    Route::get('/productos-lote-create', [ProductosLoteController::class, 'create'])->name('productosLote.create');
    Route::get('/productos-lote-edit/{id}', [ProductosLoteController::class, 'edit'])->name('productosLote.edit');

    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas-create', [FacturaController::class, 'create'])->name('facturas.create');
    Route::get('/facturas-edit/{id}', [FacturaController::class, 'edit'])->name('facturas.edit');
    Route::get('/facturas-pdf/{id}', [FacturaController::class, 'pdf'])->name('facturas.pdf');

    Route::get('/albaranes', [AlbaranController::class, 'index'])->name('albaran.index');
    Route::get('/albaranes-create/{id}', [AlbaranController::class, 'create'])->name('albaran.create');
    Route::get('/albaranes-edit/{id}', [AlbaranController::class, 'edit'])->name('albaran.edit');
    Route::get('/albaranes-pdf/{id}', [AlbaranController::class, 'pdf'])->name('albaran.pdf');

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock-create/{id}', [StockController::class, 'create'])->name('stock.create');
    Route::get('/stock-edit/{id}', [StockController::class, 'edit'])->name('stock.edit');
});
