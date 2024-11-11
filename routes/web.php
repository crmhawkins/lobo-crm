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
use App\Http\Controllers\VerEmailsController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ControlPresupuestarioController;

use App\Http\Controllers\GrupoContabilidadController;
use App\Http\Controllers\SubGrupoContabilidadController;
use App\Http\Controllers\CuentasContableController;
use App\Http\Controllers\SubCuentasContableController;
use App\Http\Controllers\SubCuentasHijoController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\acuerdosComerciales;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ProductosMarketingController;
use App\Http\Controllers\StockSubalmacenController;
use App\Http\Controllers\AlertasController;
use App\Http\Controllers\ComercialViewController;


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
    Route::post('/alertas/marcar-leida/{id}', [AlertasController::class, 'marcarLeida'])->name('alertas.marcarLeida');
    Route::post('/alertas/marcar-todas-leidas', [AlertasController::class, 'marcarTodasLeidas'])->name('alertas.marcarTodasLeidas');

    Route::get('/alertas/popup', [AlertasController::class, 'popup'])->name('alertas.popup');

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
    Route::get('/pedidos-carta/{id}', [PedidoController::class, 'carta'])->name('pedidos.cartatransporte');


    Route::get('/productos', [ProductosController::class, 'index'])->name('productos.index');
    Route::get('/productos-create', [ProductosController::class, 'create'])->name('productos.create');
    Route::get('/productos-edit/{id}', [ProductosController::class, 'edit'])->name('productos.edit');

    Route::get('/productos-marketing', [ProductosMarketingController::class, 'index'])->name('productosmarketing.index');
    Route::get('/productosmarketing-create', [ProductosMarketingController::class, 'create'])->name('productosmarketing.create');
    Route::get('/productos-marketing-edit/{id}', [ProductosMarketingController::class, 'edit'])->name('productosmarketing.edit');


    Route::get('/materiales-producto', [MaterialesProductoController::class, 'index'])->name('materiales-producto.index');
    Route::get('/materiales-producto-create/{id}', [MaterialesProductoController::class, 'create'])->name('materiales-producto.create');
    Route::get('/materiales-producto-edit/{id}', [MaterialesProductoController::class, 'edit'])->name('materiales-producto.edit');

    Route::get('/productos-pedido', [ProductosPedidoController::class, 'index'])->name('productos-pedido.index');
    Route::get('/productos-pedido-create', [ProductosPedidoController::class, 'create'])->name('productos-pedido.create');
    Route::get('/productos-pedido-edit/{id}', [ProductosPedidoController::class, 'edit'])->name('productos-pedido.edit');

    Route::get('/contabilidad', [ContabilidadController::class, 'index'])->name('contabilidad.index');
    Route::get('/contabilidad-create', [ContabilidadController::class, 'create'])->name('contabilidad.create');
    Route::get('/contabilidad-edit/{id}', [ContabilidadController::class, 'edit'])->name('contabilidad.edit');

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
    Route::get('/stock/historial', [StockController::class, 'historial'])->name('stock.historial');

    Route::get('/stock-subalmacen', [StockSubalmacenController::class, 'index'])->name('stock-subalmacen.index');
    Route::get('/stock-subalmacen/registro', [StockSubalmacenController::class, 'registro'])->name('stock-subalmacen.registro');


    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios-create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::get('/usuarios-edit/{id}', [UsuarioController::class, 'edit'])->name('usuarios.edit');

    Route::get('/mercaderia', [MercaderiaController::class, 'index'])->name('mercaderia.index');
    Route::get('/mercaderia-create', [MercaderiaController::class, 'create'])->name('mercaderia.create');
    Route::get('/mercaderia-edit/{id}', [MercaderiaController::class, 'edit'])->name('mercaderia.edit');
    Route::get('/mercaderia/historial', [MercaderiaController::class, 'historial'])->name('mercaderia.historial');


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


    //ruta ver emails
    Route::get('/ver-emails', [VerEmailsController::class, 'index'])->name('ver-emails.index');


    Route::get('/control-presupuestario', [ControlPresupuestarioController::class, 'index'])->name('control-presupuestario.index');
    // Route::get('/control-presupuestario/ventas', [ControlPresupuestarioController::class, 'ventas'])->name('control-presupuestario.ventas');
    Route::get('/control-presupuestario/ventas', [ControlPresupuestarioController::class, 'ventas'])->name('control-presupuestario.ventas');
    Route::get('/control-presupuestario/compras', [ControlPresupuestarioController::class, 'compras'])->name('control-presupuestario.compras');
    Route::post('/guardar-costes', [ControlPresupuestarioController::class, 'guardarCostes'])->name('control-presupuestario.guardarCostes');
    Route::post('/costes/{id}', [ControlPresupuestarioController::class, 'eliminarCoste'])->name('costes.eliminar');
    Route::get('/control-presupuestario/logistica', [ControlPresupuestarioController::class, 'logistica'])->name('control-presupuestario.logistica');
    Route::get('/control-presupuestario/comerciales', [ControlPresupuestarioController::class, 'comerciales'])->name('control-presupuestario.comerciales');
    Route::get('/control-presupuestario/marketing', [ControlPresupuestarioController::class, 'marketing'])->name('control-presupuestario.marketing');
    Route::get('/control-presupuestario/patrocinios', [ControlPresupuestarioController::class, 'patrocinios'])->name('control-presupuestario.patrocinios');
    Route::get('/control-presupuestario/presupuestos-delegacion', [ControlPresupuestarioController::class, 'presupuestosDelegacion'])->name('control-presupuestario.presupuestos-delegacion');
    Route::get('/control-presupuestario/ventas-delegaciones', [ControlPresupuestarioController::class, 'ventasDelegaciones'])->name('control-presupuestario.ventas-delegaciones');
    Route::get('/control-presupuestario/ventas-por-productos', [ControlPresupuestarioController::class, 'ventasPorProductos'])->name('control-presupuestario.ventas-por-productos');
    Route::get('/control-presupuestario/analisis-global', [ControlPresupuestarioController::class, 'analisisGlobal'])->name('control-presupuestario.analisis-global');
    Route::get('/control-presupuestario/analisis-ventas', [ControlPresupuestarioController::class, 'analisisVentas'])->name('control-presupuestario.analisis-ventas');
    Route::get('/control-presupuestario/proyeccion', [ControlPresupuestarioController::class, 'proyeccion'])->name('control-presupuestario.proyeccion');


    // Cuentas Contables
    Route::get('/cuentas-contables', [App\Http\Controllers\CuentasContableController::class, 'index'])->name('admin.cuentasContables.index');
    Route::get('/cuentas-contables/create', [App\Http\Controllers\CuentasContableController::class, 'create'])->name('admin.cuentasContables.create');
    Route::post('/cuentas-contables/store', [App\Http\Controllers\CuentasContableController::class, 'store'])->name('admin.cuentasContables.store');
    Route::get('/cuentas-contables/{id}/edit', [App\Http\Controllers\CuentasContableController::class, 'edit'])->name('admin.cuentasContables.edit');
    Route::put('/cuentas-contables/{id}/updated', [App\Http\Controllers\CuentasContableController::class, 'updated'])->name('admin.cuentasContables.updated');
    Route::delete('/cuentas-contables/destroy/{id}', [App\Http\Controllers\CuentasContableController::class, 'destroy'])->name('admin.cuentasContables.destroy');

    Route::get('/cuentas-contables/get-cuentas', [App\Http\Controllers\CuentasContableController::class, 'getCuentasByDataTables'])->name('admin.cuentasContables.getClients');

    // Sub-Cuentas Contables
    Route::get('/sub-cuentas-contables', [App\Http\Controllers\SubCuentasContableController::class, 'index'])->name('admin.subCuentasContables.index');
    Route::get('/sub-cuentas-contables/create', [SubCuentasContableController::class, 'create'])->name('admin.subCuentasContables.create');
    Route::post('/sub-cuentas-contables/store', [SubCuentasContableController::class, 'store'])->name('admin.subCuentasContables.store');
    Route::get('/sub-cuentas-contables/{id}/edit', [SubCuentasContableController::class, 'edit'])->name('admin.subCuentasContables.edit');
    Route::put('/sub-cuentas-contables/{id}', [App\Http\Controllers\SubCuentasContableController::class, 'update'])->name('admin.subCuentasContables.update');
    Route::delete('/sub-cuentas-contables/{id}/destroy', [SubCuentasContableController::class, 'destroy'])->name('admin.subCuentasContables.destroy');


    // Sub-Cuentas Hijas Contables
    Route::get('/sub-cuentas-hijas-contables', [App\Http\Controllers\SubCuentasHijoController::class, 'index'])->name('admin.subCuentasHijaContables.index');
    Route::get('/sub-cuentas-hijas-contables/create', [App\Http\Controllers\SubCuentasHijoController::class, 'create'])->name('admin.subCuentasHijaContables.create');
    Route::post('/sub-cuentas-hijas-contables/store', [App\Http\Controllers\SubCuentasHijoController::class, 'store'])->name('admin.subCuentasHijaContables.store');
    Route::get('/sub-cuentas-hijas-contables/{id}/edit', [App\Http\Controllers\SubCuentasHijoController::class, 'edit'])->name('admin.subCuentasHijaContables.edit');
    Route::put('/sub-cuentas-hijas-contables/{id}', [App\Http\Controllers\SubCuentasHijoController::class, 'update'])->name('admin.subCuentasHijaContables.update');
    Route::delete('/sub-cuentas-hijas-contables/{id}', [App\Http\Controllers\SubCuentasHijoController::class, 'destroy'])->name('admin.subCuentasHijaContables.destroy');


    // Grupos Contables
    Route::get('/grupo-contable', [App\Http\Controllers\GrupoContabilidadController::class, 'index'])->name('admin.grupoContabilidad.index');
    Route::get('/grupo-contable/create', [App\Http\Controllers\GrupoContabilidadController::class, 'create'])->name('admin.grupoContabilidad.create');
    Route::post('/grupo-contable/store', [App\Http\Controllers\GrupoContabilidadController::class, 'store'])->name('admin.grupoContabilidad.store');
    Route::get('/grupo-contable/{id}/edit', [App\Http\Controllers\GrupoContabilidadController::class, 'edit'])->name('admin.grupoContabilidad.edit');
    Route::put('/grupo-contable/{id}/update', [App\Http\Controllers\GrupoContabilidadController::class, 'update'])->name('admin.grupoContabilidad.update');
    Route::delete('/grupo-contable/{id}/destroy', [App\Http\Controllers\GrupoContabilidadController::class, 'destroy'])->name('admin.grupoContabilidad.destroy');


    // Sub Grupos Contables
    Route::get('/sub-grupo-contable', [App\Http\Controllers\SubGrupoContabilidadController::class, 'index'])->name('admin.subGrupoContabilidad.index');
    Route::get('/sub-grupo-contable/create', [App\Http\Controllers\SubGrupoContabilidadController::class, 'create'])->name('admin.subGrupoContabilidad.create');
    Route::post('/sub-grupo-contable/store', [App\Http\Controllers\SubGrupoContabilidadController::class, 'store'])->name('admin.subGrupoContabilidad.store');
    Route::get('/sub-grupo-contable/{id}/edit', [App\Http\Controllers\SubGrupoContabilidadController::class, 'edit'])->name('admin.subGrupoContabilidad.edit');
    Route::put('/sub-grupo-contable/{id}/update', [App\Http\Controllers\SubGrupoContabilidadController::class, 'update'])->name('admin.subGrupoContabilidad.update');
    Route::delete('/sub-grupo-contable/{id}/destroy', [App\Http\Controllers\SubGrupoContabilidadController::class, 'destroy'])->name('admin.subGrupoContabilidad.destroy');

    //Contabilidad
    Route::get('/contabilidad', [ContabilidadController::class, 'index'])->name('contabilidad.index');

    //Calendario
    Route::get('/calendario-marketing', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::post('/calendario/event', [CalendarioController::class, 'store'])->name('event.store');
    Route::put('/calendario/event/{id}', [CalendarioController::class, 'update'])->name('event.update');
    Route::delete('/calendario/event/{id}', [CalendarioController::class, 'destroy'])->name('event.destroy');

    //incidencias
    Route::get('/incidencias', [App\Http\Controllers\IncidenciasController::class, 'index'])->name('admin.incidencias.index');
    Route::get('/incidencias/todas', [App\Http\Controllers\IncidenciasController::class, 'todas'])->name('incidencias.todas');
    //acuerdos comerciales
    Route::get('/create/acuerdos-comerciales/{id}', [acuerdosComerciales::class, 'create'])->name('acuerdos-comerciales.create');
    Route::get('/edit/acuerdos-comerciales/{id}', [acuerdosComerciales::class, 'edit'])->name('acuerdos-comerciales.edit');

    Route::post('/acuerdos-comerciales', [acuerdosComercialesController::class, 'store'])->name('acuerdos-comerciales.store');


    //Comercial
    //Route::get('/comercial', [ComercialViewController::class, 'index'])->name('comercial.index');
    Route::get('/clientes-comercial-create', [ComercialViewController::class, 'clientecomercial'])->name('comercial.addcliente');
    Route::get('/comercial', [ComercialViewController::class, 'clientecomercialview'])->name('comercial.clientes');
    Route::get('/comercial/edit/{id}', [ComercialViewController::class, 'editcliente'])->name('comercial.editcliente');

    Route::get('/comercial/create-pedido', [ComercialViewController::class, 'createpedido'])->name('comercial.createpedido');
    Route::get('/comercial/edit-pedido/{id}', [ComercialViewController::class, 'editpedido'])->name('comercial.editpedido');
    Route::get('/comercial/pedidos', [ComercialViewController::class, 'pedidos'])->name('comercial.pedidos');
});

Route::get('/whatsapp', [App\Http\Controllers\WhatsappController::class, 'hookWhatsapp'])->name('whatsapp.hookWhatsapp');
Route::post('/whatsapp', [App\Http\Controllers\WhatsappController::class, 'processHookWhatsapp'])->name('whatsapp.processHookWhatsapp');
// Route::get('/chatgpt','SiteController@chatGptPruebas')->name('admin.estadisticas.hookWhatsapp');
// Route::get('/cron','SiteController@obtenerAudioMedia2')->name('admin.estadisticas.obtenerAudioMedia2');
//Route::get('/cron', [App\Http\Controllers\WhatsappController::class, 'cron'])->name('whatsapp.cron');
Route::get('/mensajes-whatsapp', [App\Http\Controllers\WhatsappController::class, 'whatsapp'])->name('whatsapp.mensajes');

Route::get('/buscar-gastos', [GastosController::class, 'buscarGastos'])->name('buscarGastos');
