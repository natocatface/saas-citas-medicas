<?php

use App\Http\Controllers\AseguradoraController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BandejaSunatController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Cie10Controller;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ConfiguracionClinicaController;
use App\Http\Controllers\FacturacionConfiguracionController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\PlantillaEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ListaEsperaController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SaaS\BitacoraController;
use App\Http\Controllers\SaaS\ClinicaController;
use App\Http\Controllers\SaaS\ConfiguracionController;
use App\Http\Controllers\SaaS\DashboardController as SaaSDashboardController;
use App\Http\Controllers\SaaS\PlanController;
use App\Http\Controllers\SaaS\UsuarioController as SaaSUsuarioController;
use Illuminate\Support\Facades\Route;

// Autenticacion
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Landing page pública
Route::get('/', fn () => view('landing'))->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('perfil.update');

    // Calendario y Reportes
    Route::get('/calendario', [CalendarController::class, 'index'])->name('calendario.index');
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');

    // CRUD operativos
    Route::resource('especialidades', EspecialidadController::class)->except(['show']);
    Route::resource('medicos', MedicoController::class)->except(['show']);
    Route::resource('pacientes', PacienteController::class)->except(['show']);
    Route::resource('aseguradores', AseguradoraController::class)->except(['show']);
    Route::resource('citas', CitaController::class);
    Route::patch('citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');

    // Recetas
    Route::resource('recetas', RecetaController::class);

    // Facturacion
    Route::resource('facturacion', FacturaController::class);
    Route::patch('facturacion/{facturacion}/pagar', [FacturaController::class, 'pagar'])->name('facturacion.pagar');
    // Facturación electrónica (SUNAT/DIAN/...)
    Route::post('facturacion/{facturacion}/emitir-cpe', [FacturaController::class, 'emitirCpe'])->name('facturacion.emitir-cpe');
    Route::post('facturacion/{facturacion}/consultar-cpe', [FacturaController::class, 'consultarCpe'])->name('facturacion.consultar-cpe');
    Route::post('facturacion/{facturacion}/nota-credito', [FacturaController::class, 'emitirNotaCredito'])->name('facturacion.nota-credito');
    Route::post('facturacion/{facturacion}/anular-cpe', [FacturaController::class, 'anularCpe'])->name('facturacion.anular-cpe');
    Route::post('facturacion/{facturacion}/consultar-baja', [FacturaController::class, 'consultarBajaCpe'])->name('facturacion.consultar-baja');
    Route::get('facturacion/{facturacion}/cpe-xml', [FacturaController::class, 'descargarXml'])->name('facturacion.cpe-xml');
    Route::get('facturacion/{facturacion}/cpe-print', [FacturaController::class, 'imprimirCpe'])->name('facturacion.cpe-print');
    // Bandeja SUNAT (monitoreo y reproceso de comprobantes)
    Route::get('bandeja-sunat', [BandejaSunatController::class, 'index'])->name('bandeja-sunat.index');
    Route::post('bandeja-sunat/reprocesar', [BandejaSunatController::class, 'reintentarLote'])->name('bandeja-sunat.reprocesar');
    Route::post('bandeja-sunat/consultar-bajas', [BandejaSunatController::class, 'consultarBajasPendientes'])->name('bandeja-sunat.consultar-bajas');
    Route::post('bandeja-sunat/{comprobante}/reintentar', [BandejaSunatController::class, 'reintentar'])->name('bandeja-sunat.reintentar');

    // Lista de espera
    Route::get('/lista-espera', [ListaEsperaController::class, 'index'])->name('lista-espera.index');
    Route::get('/lista-espera/create', [ListaEsperaController::class, 'create'])->name('lista-espera.create');
    Route::post('/lista-espera', [ListaEsperaController::class, 'store'])->name('lista-espera.store');
    Route::get('/lista-espera/{registro}/edit', [ListaEsperaController::class, 'edit'])->name('lista-espera.edit');
    Route::put('/lista-espera/{registro}', [ListaEsperaController::class, 'update'])->name('lista-espera.update');
    Route::delete('/lista-espera/{registro}', [ListaEsperaController::class, 'destroy'])->name('lista-espera.destroy');
    Route::post('/lista-espera/{registro}/convertir', [ListaEsperaController::class, 'convertir'])->name('lista-espera.convertir');

    // Plantillas CIE-10 (admin/médico)
    Route::resource('cie10', Cie10Controller::class)->except(['show'])->middleware('role:admin,medico');

    // Mensajería interna (todos)
    Route::resource('mensajes', MensajeController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Administracion (solo admin)
    Route::resource('usuarios', UsuarioController::class)->except(['show'])->middleware('role:admin');

    Route::resource('plantillas-email', PlantillaEmailController::class)
        ->except(['show'])->parameters(['plantillas-email' => 'plantillas_email'])->middleware('role:admin');

    Route::get('/configuracion', [ConfiguracionClinicaController::class, 'edit'])->name('configuracion.index')->middleware('role:admin');
    Route::put('/configuracion', [ConfiguracionClinicaController::class, 'update'])->name('configuracion.update')->middleware('role:admin');

    // Configuración de Facturación Electrónica (SUNAT)
    Route::get('/facturacion-config', [FacturacionConfiguracionController::class, 'index'])->name('facturacion-config.index')->middleware('role:admin');
    Route::put('/facturacion-config', [FacturacionConfiguracionController::class, 'update'])->name('facturacion-config.update')->middleware('role:admin');
    Route::post('/facturacion-config/probar', [FacturacionConfiguracionController::class, 'probarConexion'])->name('facturacion-config.probar')->middleware('role:admin');

    // Mantenimiento (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/mantenimiento', [MantenimientoController::class, 'index'])->name('mantenimiento.index');
        Route::post('/mantenimiento/cache', [MantenimientoController::class, 'limpiarCache'])->name('mantenimiento.cache');
        Route::post('/mantenimiento/bitacora', [MantenimientoController::class, 'limpiarBitacora'])->name('mantenimiento.bitacora');
        Route::get('/mantenimiento/respaldo', [MantenimientoController::class, 'respaldo'])->name('mantenimiento.respaldo');
    });

    // ===================== PANEL SUPER ADMIN (SaaS) =====================
    Route::prefix('saas')->name('saas.')->middleware('role:superadmin')->group(function () {
        Route::get('/', [SaaSDashboardController::class, 'index'])->name('dashboard');
        Route::resource('clinicas', ClinicaController::class)->except(['show']);
        Route::resource('planes', PlanController::class)->except(['show'])->parameters(['planes' => 'plan']);
        Route::resource('usuarios', SaaSUsuarioController::class)->except(['show']);
        Route::get('/configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    });

    // Modulos placeholder
    Route::get('/m/{modulo}', [ModuleController::class, 'show'])
        ->where('modulo', '[a-z0-9\-]+')
        ->name('modulo');
});
