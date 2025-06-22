<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GestionarUsuariosController;
use App\Http\Controllers\GestionarRolesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Legal\LegalController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Rutas de Invitados (No Autenticados) ---
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// --- Rutas Protegidas (Requieren Autenticación) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');

    // --- DASHBOARDS PARA CADA ROL ---
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('role:admin');
    Route::get('/legal/dashboard', [LegalController::class, 'dashboard'])->name('legal.dashboard')->middleware('role:admin,legal');
    Route::get('/responsable/dashboard', fn() => view('pages.responsable.dashboard'))->name('responsable.dashboard')->middleware('role:admin,responsable');
    
    // --- GRUPO DE RUTAS DE GESTIÓN DE ADULTOS MAYORES ---
    Route::prefix('gestionar-adultos-mayores')->name('gestionar-adultomayor.')->middleware('role:admin,legal')->group(function () {
        Route::get('/', [AdminController::class, 'gestionarAdultoMayorIndex'])->name('index');
        Route::get('/crear', [AdminController::class, 'showRegisterAdultoMayor'])->name('create');
        Route::get('/buscar', [AdminController::class, 'buscarAdultoMayor'])->name('buscar');
        Route::post('/', [AdminController::class, 'storeAdultoMayor'])->name('store');
        Route::get('/{ci}/editar', [AdminController::class, 'editarAdultoMayor'])->name('editar');
        Route::put('/{ci}', [AdminController::class, 'actualizarAdultoMayor'])->name('actualizar');
        Route::delete('/{ci}', [AdminController::class, 'eliminarAdultoMayor'])->name('eliminar');
    });

    // --- GRUPO DE RUTAS SOLO PARA ADMINISTRADOR ---
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('gestionar-usuarios', GestionarUsuariosController::class)->except(['show']);
        Route::patch('/gestionar-usuarios/{id}/toggle-activity', [GestionarUsuariosController::class, 'toggleActivity'])->name('gestionar-usuarios.toggleActivity');
        
        Route::resource('gestionar-roles', GestionarRolesController::class)->parameters([
            'gestionar-roles' => 'rol'
        ])->except(['show']);

        Route::get('/registrar-usuario-legal', [AdminController::class, 'showRegisterLegal'])->name('registrar-usuario-legal');
        Route::post('/store-legal', [AdminController::class, 'storeUsuarioLegal'])->name('store-legal');
        Route::get('/registrar-responsable-salud', [AdminController::class, 'showRegisterResponsableSalud'])->name('registrar-responsable-salud');
        Route::post('/store-responsable-salud', [AdminController::class, 'storeResponsableSalud'])->name('store-responsable-salud');
      
        // ##########################################################################################################################
      // (MODULO PROTECCION) REGISTRAR 
      Route::prefix('caso')->name('caso.')->group(function () {
            Route::get('/', [RegistrarCasoController::class, 'index'])->name('index');

            // Ruta para INICIAR el registro de un caso para un Adulto Mayor ya existente
            // Esta es la ruta para el botón "Registrar Nuevo Caso" en indexPro.blade.php
            Route::get('{id_adulto}/registrar/{active_tab?}', [RegistrarCasoController::class, 'registerNewCaseForm'])->name('register'); // <-- CAMBIO AQUÍ

            // Ruta para EDITAR un caso existente de un Adulto Mayor
            // Esta es la ruta para el botón "Editar Caso"
            Route::get('{id_adulto}/editar/{active_tab?}', [RegistrarCasoController::class, 'edit'])->name('edit'); // <-- TU MÉTODO 'EDIT'

            // Ruta para VER el detalle de un caso (solo lectura)
            Route::get('{id_adulto}/detalle', [RegistrarCasoController::class, 'showDetalle'])->name('detalle');

            // Rutas POST para guardar/actualizar los datos de cada pestaña
            // Estas rutas seguirán apuntando al método 'edit' después de guardar para mantener el estado de edición.
            Route::post('{id_adulto}/actividad', [RegistrarCasoController::class, 'storeActividad'])->name('storeActividad');
            Route::post('{id_adulto}/encargado', [RegistrarCasoController::class, 'storeEncargado'])->name('storeEncargado');
            Route::post('{id_adulto}/denunciado', [RegistrarCasoController::class, 'storeDenunciado'])->name('storeDenunciado');
            Route::post('{id_adulto}/grupo', [RegistrarCasoController::class, 'storeGrupoFamiliar'])->name('storeGrupoFamiliar');
            Route::post('{id_adulto}/croquis', [RegistrarCasoController::class, 'storeCroquis'])->name('storeCroquis');
            Route::post('{id_adulto}/seguimiento', [RegistrarCasoController::class, 'storeSeguimiento'])->name('storeSeguimiento');
            Route::post('{id_adulto}/intervencion', [RegistrarCasoController::class, 'storeIntervencion'])->name('storeIntervencion');
            Route::post('{id_adulto}/anexo3', [RegistrarCasoController::class, 'storeAnexoN3'])->name('storeAnexoN3');
            Route::post('{id_adulto}/anexo5', [RegistrarCasoController::class, 'storeAnexoN5'])->name('storeAnexoN5');
            // Nueva ruta para eliminar un caso (añadimos la ruta de eliminación aquí)
            Route::delete('{id_adulto}', [RegistrarCasoController::class, 'destroy'])->name('destroy');
            });

      // NUEVAS RUTAS PARA REPORTES DE PROTECCIÓN
      Route::prefix('reportes_proteccion')->name('reportes_proteccion.')->group(function () {
            Route::get('/', [ReporteProteccionController::class, 'index'])->name('index');
            Route::get('{id_adulto}/ver', [ReporteProteccionController::class, 'showReporte'])->name('showReporte');
            Route::get('/{id_adulto}/exportar-word', [ReporteProteccionController::class, 'exportarFichaProteccionWordIndividual'])->name('exportWordIndividual');
            });
      
      // ##########################################################################################################################
      //   (MODULO ORIENTACION) REGISTRAR FICHA 12/06/2025################################################################################################
      Route::prefix('orientacion')->name('orientacion.')->group(function () {
            // Ruta principal para listar todos los adultos mayores (indexOri.blade.php)
            // Esto es el equivalente a Route::get('/', [RegistrarCasoController::class, 'index'])->name('index');
            Route::get('/', [RegistrarFichaController::class, 'index'])->name('index');

            // Rutas para el REGISTRO DE FICHAS DE ORIENTACIÓN
            // Muestra el formulario para registrar una nueva ficha de orientación para un Adulto Mayor
            // Equivalente a Route::get('{id_adulto}/registrar/{active_tab?}', [RegistrarCasoController::class, 'registerNewCaseForm'])->name('register');
            Route::get('{id_adulto}/registrar', [RegistrarFichaController::class, 'registerOrientacion'])->name('register');

            // Almacena la nueva orientación (se mantiene como la tenías)
            Route::post('/store', [RegistrarFichaController::class, 'storeOrientacion'])->name('store'); // Ya no necesita id_adulto en la URL para el POST

            // Ruta para EDITAR una ficha de orientación existente
            // Muestra el formulario para editar una ficha existente del Adulto Mayor
            // NOTA: Esta ruta pasa el id_adulto, y el controlador buscará la ficha de orientación.
            Route::get('{id_adulto}/editar', [RegistrarFichaController::class, 'edit'])->name('edit');
            // Actualiza la ficha de orientación existente (usa el cod_or de la ficha)
            Route::put('update/{cod_or}', [RegistrarFichaController::class, 'updateOrientacion'])->name('update');
            // VER DETALLES FICHA DE ORIENTACION
            Route::get('{cod_or}/ver', [RegistrarFichaController::class, 'showOrientacionDetail'])->name('show');                                                 
            });
      // Rutas para el NUEVO MODULO DE REPORTES DE ORIENTACION
      Route::prefix('reportes-orientacion')->name('reportes_orientacion.')->group(function () {
      // Ruta principal para listar todas las orientaciones (CRUD)
            Route::get('/', [ReporteOrientacionController::class, 'index'])->name('index');

            // Ruta para ver el reporte detallado de una orientación específica
            // AHORA ESTA RUTA CARGA DIRECTAMENTE LA VISTA verReporte.blade.php desde ReporteOrientacionController
            Route::get('{cod_or}/ver', [ReporteOrientacionController::class, 'showReporte'])->name('show_reporte');

            // Ruta para eliminar una ficha de orientación desde el listado de reportes
            Route::delete('eliminar/{cod_or}', [ReporteOrientacionController::class, 'destroy'])->name('destroy');
            Route::get('/{cod_or}/exportar-word', [ReporteOrientacionController::class, 'exportarFichaOrientacionWordIndividual'])->name('exportWordIndividual');
            });
            
      //   (MODULO MEDICO) REGISTRAR FICHA 17/06/2025################################################################################################
      Route::prefix('medico.historia_clinica')->name('medico.historia_clinica.')->group(function () {
            // Ruta principal: Listado de Adultos Mayores con gestión de Historia Clínica
            Route::get('/', [HistoriaClinicaController::class, 'index'])->name('index');
            Route::get('{id_adulto}/registrar/{active_tab?}', [HistoriaClinicaController::class, 'register'])->name('register');
            Route::post('{id_adulto}/store-historia', [HistoriaClinicaController::class, 'storeHistoriaClinica'])->name('storeHistoria');
            Route::put('{id_historia}/update-historia', [HistoriaClinicaController::class, 'updateHistoriaClinica'])->name('updateHistoria');
            Route::match(['POST', 'PUT'], '{id_historia}/store-examenes', [HistoriaClinicaController::class, 'storeExamenesComplementarios'])->name('storeExamenes');
            Route::get('{id_historia}/editar/{active_tab?}', [HistoriaClinicaController::class, 'edit'])->name('edit');
            Route::get('{id_historia}/ver', [HistoriaClinicaController::class, 'showDetalle'])->name('show_detalle');
            Route::delete('eliminar/{id_historia}', [HistoriaClinicaController::class, 'destroy'])->name('destroy');
            });
      // ENFERMERIA
      Route::prefix('enfermeria')->name('enfermeria.')->group(function () {
            Route::get('/', [EnfermeriaController::class, 'index'])->name('index');
            Route::get('{id_adulto}/create', [EnfermeriaController::class, 'create'])->name('create');
            Route::post('{id_adulto}/store', [EnfermeriaController::class, 'store'])->name('store');
            Route::get('{cod_enf}/edit', [EnfermeriaController::class, 'edit'])->name('edit');
            Route::put('{cod_enf}', [EnfermeriaController::class, 'update'])->name('update');
            Route::get('{cod_enf}/show', [EnfermeriaController::class, 'show'])->name('show');
            Route::delete('{cod_enf}', [EnfermeriaController::class, 'destroy'])->name('destroy');
      });
      // (MODULO MÉDICO) REPORTES DE HISTORIA CLÍNICA Y ENFERMERÍA #########################################
      Route::prefix('reportes_enfermeria')->name('reportes_enfermeria.')->group(function () {
            Route::get('/', [ReporteMedicoController::class, 'index'])->name('index');
            Route::get('/imprimir', [ReporteMedicoController::class, 'imprimirAtencionesEnfermeria'])->name('imprimir');
            Route::get('/exportar-excel', [ReporteMedicoController::class, 'exportarAtencionesEnfermeriaExcel'])->name('exportar_excel');
            Route::get('atencion_enfermeria/{cod_enf}/show', [ReporteMedicoController::class, 'showAtencionEnfermeria'])->name('show_atencion_enfermeria');
            Route::delete('atencion_enfermeria/{cod_enf}', [ReporteMedicoController::class, 'destroyAtencionEnfermeria'])->name('destroy_atencion_enfermeria');
            });
      // NUEVAS RUTAS para el MODULO MEDICO - REPORTES DE HISTORIAS CLÍNICAS (Exclusivamente)
            Route::prefix('reportes_historia_clinica')->name('reportes_historia_clinica.')->group(function () {
            Route::get('/', [ReporteMedicoController::class, 'indexHistoriaClinica'])->name('index');
            Route::get('/exportar-excel/{id_historia}', [ReporteMedicoController::class, 'exportarHistoriaClinicaExcel'])->name('exportar_excel');
            });
            // NUEVAS RUTAS para el MODULO DE FISIOTERAPIA Y KINESIOLOGÍA
            Route::prefix('fisiokine')->name('fisiokine.')->group(function () {
                  // Rutas para Fisioterapia (Listado de Adultos Mayores para registro y CRUD individual)
                  Route::get('/fisioterapia', [FisioKineController::class, 'indexFisio'])->name('indexFisio');
                  Route::get('/fisioterapia/{id_adulto}/registrar', [FisioKineController::class, 'createFisio'])->name('createFisio');
                  Route::post('/fisioterapia/{id_adulto}/store', [FisioKineController::class, 'storeFisio'])->name('storeFisio');
                  Route::get('/fisioterapia/{cod_fisio}/editar', [FisioKineController::class, 'editFisio'])->name('editFisio'); // GET para mostrar formulario
                  Route::put('/fisioterapia/{cod_fisio}/update', [FisioKineController::class, 'updateFisio'])->name('updateFisio'); // PUT para actualizar
                  Route::get('/fisioterapia/{cod_fisio}/ver', [FisioKineController::class, 'showFisio'])->name('showFisio'); // GET para ver detalles
                  Route::delete('/fisioterapia/{cod_fisio}/eliminar', [FisioKineController::class, 'destroyFisio'])->name('destroyFisio'); // DELETE para eliminar
                  // Rutas para Kinesiología (Listado de Adultos Mayores para registro y CRUD individual)
                  Route::get('/kinesiologia', [FisioKineController::class, 'indexKine'])->name('indexKine');
                  Route::get('/kinesiologia/{id_adulto}/registrar', [FisioKineController::class, 'createKine'])->name('createKine');
                  Route::post('/kinesiologia/{id_adulto}/store', [FisioKineController::class, 'storeKine'])->name('storeKine');
                  Route::get('/kinesiologia/{cod_kine}/editar', [FisioKineController::class, 'editKine'])->name('editKine'); // GET para mostrar formulario
                  Route::put('/kinesiologia/{cod_kine}/update', [FisioKineController::class, 'updateKine'])->name('updateKine'); // PUT para actualizar
                  Route::get('/kinesiologia/{cod_kine}/ver', [FisioKineController::class, 'showKine'])->name('showKine'); // GET para ver detalles
                  Route::delete('/kinesiologia/{cod_kine}/eliminar', [FisioKineController::class, 'destroyKine'])->name('destroyKine'); // DELETE para eliminar

                  
            });
            // Rutas para el Reporte de Fichas de Fisioterapia
            Route::prefix('reportefisio')->name('reportefisio.')->group(function () {
                  Route::get('/', [ReporteFisioKineController::class, 'indexReporteFisio'])->name('index'); // Reporte de fichas de Fisioterapia
                  // Las rutas CRUD para una ficha específica de Fisioterapia desde el reporte
                  Route::get('/{cod_fisio}/editar', [ReporteFisioKineController::class, 'editFisio'])->name('edit');
                  Route::put('/{cod_fisio}/update', [ReporteFisioKineController::class, 'updateFisio'])->name('update');
                  Route::get('/{cod_fisio}/ver', [ReporteFisioKineController::class, 'showFisio'])->name('show');
                  Route::delete('/{cod_fisio}/eliminar', [ReporteFisioKineController::class, 'destroyFisio'])->name('destroy');
                  Route::get('/{cod_fisio}/exportar-word', [ReporteFisioKineController::class, 'exportarFichaFisioWordIndividual'])->name('exportWordIndividual');
            });
            // Rutas para el Reporte de Fichas de Kinesiología
            Route::prefix('reportekine')->name('reportekine.')->group(function () {
                  Route::get('/', [ReporteFisioKineController::class, 'indexReporteKine'])->name('index'); // Reporte de fichas de Kinesiología
                  Route::get('/{cod_kine}/editar', [ReporteFisioKineController::class, 'editKine'])->name('edit');
                  Route::put('/{cod_kine}/update', [ReporteFisioKineController::class, 'updateKine'])->name('update');
                  Route::get('/{cod_kine}/ver', [ReporteFisioKineController::class, 'showKine'])->name('show');
                  Route::delete('/{cod_kine}/eliminar', [ReporteFisioKineController::class, 'destroyKine'])->name('destroy');
                  Route::get('exportar-excel', [ReporteFisioKineController::class, 'exportarFichaKineExcel'])->name('exportExcel');
            });
    });



    // --- GRUPO DE RUTAS PARA ROL LEGAL (y admin) ---
    Route::prefix('legal')->name('legal.')->middleware('role:admin,legal')->group(function () {
      /*
        Route::get('/proteccion', [LegalController::class, 'proteccionIndex'])->name('proteccion.index');
        Route::get('/proteccion/create', [LegalController::class, 'proteccionCreate'])->name('proteccion.create');
        Route::post('/proteccion', [LegalController::class, 'proteccionStore'])->name('proteccion.store');
        Route::get('/proteccion/reportes', [LegalController::class, 'proteccionReportes'])->name('proteccion.reportes');*/
         // (MODULO PROTECCION) REGISTRAR CASO 12/06/2025################################################################################################
      Route::prefix('caso')->name('caso.')->group(function () {
            Route::get('/', [RegistrarCasoController::class, 'index'])->name('index');

            // Ruta para INICIAR el registro de un caso para un Adulto Mayor ya existente
            // Esta es la ruta para el botón "Registrar Nuevo Caso" en indexPro.blade.php
            Route::get('{id_adulto}/registrar/{active_tab?}', [RegistrarCasoController::class, 'registerNewCaseForm'])->name('register'); // <-- CAMBIO AQUÍ

            // Ruta para EDITAR un caso existente de un Adulto Mayor
            // Esta es la ruta para el botón "Editar Caso"
            Route::get('{id_adulto}/editar/{active_tab?}', [RegistrarCasoController::class, 'edit'])->name('edit'); // <-- TU MÉTODO 'EDIT'

            // Ruta para VER el detalle de un caso (solo lectura)
            Route::get('{id_adulto}/detalle', [RegistrarCasoController::class, 'showDetalle'])->name('detalle');

            // Rutas POST para guardar/actualizar los datos de cada pestaña
            // Estas rutas seguirán apuntando al método 'edit' después de guardar para mantener el estado de edición.
            Route::post('{id_adulto}/actividad', [RegistrarCasoController::class, 'storeActividad'])->name('storeActividad');
            Route::post('{id_adulto}/encargado', [RegistrarCasoController::class, 'storeEncargado'])->name('storeEncargado');
            Route::post('{id_adulto}/denunciado', [RegistrarCasoController::class, 'storeDenunciado'])->name('storeDenunciado');
            Route::post('{id_adulto}/grupo', [RegistrarCasoController::class, 'storeGrupoFamiliar'])->name('storeGrupoFamiliar');
            Route::post('{id_adulto}/croquis', [RegistrarCasoController::class, 'storeCroquis'])->name('storeCroquis');
            Route::post('{id_adulto}/seguimiento', [RegistrarCasoController::class, 'storeSeguimiento'])->name('storeSeguimiento');
            Route::post('{id_adulto}/intervencion', [RegistrarCasoController::class, 'storeIntervencion'])->name('storeIntervencion');
            Route::post('{id_adulto}/anexo3', [RegistrarCasoController::class, 'storeAnexoN3'])->name('storeAnexoN3');
            Route::post('{id_adulto}/anexo5', [RegistrarCasoController::class, 'storeAnexoN5'])->name('storeAnexoN5');
            // Nueva ruta para eliminar un caso (añadimos la ruta de eliminación aquí)
            Route::delete('{id_adulto}', [RegistrarCasoController::class, 'destroy'])->name('destroy');
            });

      // NUEVAS RUTAS PARA REPORTES DE PROTECCIÓN
      Route::prefix('reportes_proteccion')->name('reportes_proteccion.')->group(function () {
            Route::get('/', [ReporteProteccionController::class, 'index'])->name('index');
            Route::get('{id_adulto}/ver', [ReporteProteccionController::class, 'showReporte'])->name('showReporte');
            Route::get('/{id_adulto}/exportar-word', [ReporteProteccionController::class, 'exportarFichaProteccionWordIndividual'])->name('exportWordIndividual');
            });
      
      // ##########################################################################################################################
      //   (MODULO ORIENTACION) REGISTRAR FICHA 12/06/2025################################################################################################
      Route::prefix('orientacion')->name('orientacion.')->group(function () {
            // Ruta principal para listar todos los adultos mayores (indexOri.blade.php)
            // Esto es el equivalente a Route::get('/', [RegistrarCasoController::class, 'index'])->name('index');
            Route::get('/', [RegistrarFichaController::class, 'index'])->name('index');

            // Rutas para el REGISTRO DE FICHAS DE ORIENTACIÓN
            // Muestra el formulario para registrar una nueva ficha de orientación para un Adulto Mayor
            // Equivalente a Route::get('{id_adulto}/registrar/{active_tab?}', [RegistrarCasoController::class, 'registerNewCaseForm'])->name('register');
            Route::get('{id_adulto}/registrar', [RegistrarFichaController::class, 'registerOrientacion'])->name('register');

            // Almacena la nueva orientación (se mantiene como la tenías)
            Route::post('/store', [RegistrarFichaController::class, 'storeOrientacion'])->name('store'); // Ya no necesita id_adulto en la URL para el POST

            // Ruta para EDITAR una ficha de orientación existente
            // Muestra el formulario para editar una ficha existente del Adulto Mayor
            // NOTA: Esta ruta pasa el id_adulto, y el controlador buscará la ficha de orientación.
            Route::get('{id_adulto}/editar', [RegistrarFichaController::class, 'edit'])->name('edit');
            // Actualiza la ficha de orientación existente (usa el cod_or de la ficha)
            Route::put('update/{cod_or}', [RegistrarFichaController::class, 'updateOrientacion'])->name('update');
            // VER DETALLES FICHA DE ORIENTACION
            Route::get('{cod_or}/ver', [RegistrarFichaController::class, 'showOrientacionDetail'])->name('show');                                                 
            });
      // Rutas para el NUEVO MODULO DE REPORTES DE ORIENTACION
      Route::prefix('reportes-orientacion')->name('reportes_orientacion.')->group(function () {
      // Ruta principal para listar todas las orientaciones (CRUD)
            Route::get('/', [ReporteOrientacionController::class, 'index'])->name('index');

            // Ruta para ver el reporte detallado de una orientación específica
            // AHORA ESTA RUTA CARGA DIRECTAMENTE LA VISTA verReporte.blade.php desde ReporteOrientacionController
            Route::get('{cod_or}/ver', [ReporteOrientacionController::class, 'showReporte'])->name('show_reporte');

            // Ruta para eliminar una ficha de orientación desde el listado de reportes
            Route::delete('eliminar/{cod_or}', [ReporteOrientacionController::class, 'destroy'])->name('destroy');
            Route::get('/{cod_or}/exportar-word', [ReporteOrientacionController::class, 'exportarFichaOrientacionWordIndividual'])->name('exportWordIndividual');
            });
            
      

    });


    // --- GRUPO DE RUTAS PARA RESPONSABLE (y admin) ---
    Route::prefix('responsable')->name('responsable.')->middleware('role:admin,responsable')->group(function () {
        
        // --- Rutas para Enfermería ---
        Route::prefix('enfermeria')->name('enfermeria.')->middleware('especialidad:Enfermeria')->group(function () {
            // Route::get('/servicios', function() { return "Página de Servicios de Enfermería"; })->name('servicios');
            // Route::get('/historias', function() { return "Página de Historias Clínicas"; })->name('historias');
            // Route::get('/reportes', function() { return "Página de Reportes de Enfermería"; })->name('reportes');

            //   (MODULO MEDICO) REGISTRAR FICHA 17/06/2025################################################################################################
        Route::prefix('medico.historia_clinica')->name('medico.historia_clinica.')->group(function () {
            // Ruta principal: Listado de Adultos Mayores con gestión de Historia Clínica
            Route::get('/', [HistoriaClinicaController::class, 'index'])->name('index');
            Route::get('{id_adulto}/registrar/{active_tab?}', [HistoriaClinicaController::class, 'register'])->name('register');
            Route::post('{id_adulto}/store-historia', [HistoriaClinicaController::class, 'storeHistoriaClinica'])->name('storeHistoria');
            Route::put('{id_historia}/update-historia', [HistoriaClinicaController::class, 'updateHistoriaClinica'])->name('updateHistoria');
            Route::match(['POST', 'PUT'], '{id_historia}/store-examenes', [HistoriaClinicaController::class, 'storeExamenesComplementarios'])->name('storeExamenes');
            Route::get('{id_historia}/editar/{active_tab?}', [HistoriaClinicaController::class, 'edit'])->name('edit');
            Route::get('{id_historia}/ver', [HistoriaClinicaController::class, 'showDetalle'])->name('show_detalle');
            Route::delete('eliminar/{id_historia}', [HistoriaClinicaController::class, 'destroy'])->name('destroy');
            });
      // ENFERMERIA
      Route::prefix('enfermeria')->name('enfermeria.')->group(function () {
            Route::get('/', [EnfermeriaController::class, 'index'])->name('index');
            Route::get('{id_adulto}/create', [EnfermeriaController::class, 'create'])->name('create');
            Route::post('{id_adulto}/store', [EnfermeriaController::class, 'store'])->name('store');
            Route::get('{cod_enf}/edit', [EnfermeriaController::class, 'edit'])->name('edit');
            Route::put('{cod_enf}', [EnfermeriaController::class, 'update'])->name('update');
            Route::get('{cod_enf}/show', [EnfermeriaController::class, 'show'])->name('show');
            Route::delete('{cod_enf}', [EnfermeriaController::class, 'destroy'])->name('destroy');
      });
      // (MODULO MÉDICO) REPORTES DE HISTORIA CLÍNICA Y ENFERMERÍA #########################################
      Route::prefix('reportes_enfermeria')->name('reportes_enfermeria.')->group(function () {
            Route::get('/', [ReporteMedicoController::class, 'index'])->name('index');
            Route::get('/imprimir', [ReporteMedicoController::class, 'imprimirAtencionesEnfermeria'])->name('imprimir');
            Route::get('/exportar-excel', [ReporteMedicoController::class, 'exportarAtencionesEnfermeriaExcel'])->name('exportar_excel');
            Route::get('atencion_enfermeria/{cod_enf}/show', [ReporteMedicoController::class, 'showAtencionEnfermeria'])->name('show_atencion_enfermeria');
            Route::delete('atencion_enfermeria/{cod_enf}', [ReporteMedicoController::class, 'destroyAtencionEnfermeria'])->name('destroy_atencion_enfermeria');
            });
      // NUEVAS RUTAS para el MODULO MEDICO - REPORTES DE HISTORIAS CLÍNICAS (Exclusivamente)
            Route::prefix('reportes_historia_clinica')->name('reportes_historia_clinica.')->group(function () {
            Route::get('/', [ReporteMedicoController::class, 'indexHistoriaClinica'])->name('index');
            Route::get('/exportar-excel/{id_historia}', [ReporteMedicoController::class, 'exportarHistoriaClinicaExcel'])->name('exportar_excel');
            });
        });


        // --- CORRECCIÓN: Rutas para Fisioterapia accesibles por la especialidad combinada ---
        Route::prefix('fisioterapia')->name('fisioterapia.')->middleware('especialidad:Fisioterapia-Kinesiologia')->group(function () {
            // Route::get('/atencion', function() { return "Página de Atención de Fisioterapia"; })->name('atencion');
            // Route::get('/reportes', function() { return "Página de Reportes de Fisioterapia"; })->name('reportes');
              Route::prefix('fisiokine')->name('fisiokine.')->group(function () {
                  // Rutas para Fisioterapia (Listado de Adultos Mayores para registro y CRUD individual)
                  Route::get('/fisioterapia', [FisioKineController::class, 'indexFisio'])->name('indexFisio');
                  Route::get('/fisioterapia/{id_adulto}/registrar', [FisioKineController::class, 'createFisio'])->name('createFisio');
                  Route::post('/fisioterapia/{id_adulto}/store', [FisioKineController::class, 'storeFisio'])->name('storeFisio');
                  Route::get('/fisioterapia/{cod_fisio}/editar', [FisioKineController::class, 'editFisio'])->name('editFisio'); // GET para mostrar formulario
                  Route::put('/fisioterapia/{cod_fisio}/update', [FisioKineController::class, 'updateFisio'])->name('updateFisio'); // PUT para actualizar
                  Route::get('/fisioterapia/{cod_fisio}/ver', [FisioKineController::class, 'showFisio'])->name('showFisio'); // GET para ver detalles
                  Route::delete('/fisioterapia/{cod_fisio}/eliminar', [FisioKineController::class, 'destroyFisio'])->name('destroyFisio'); // DELETE para eliminar
                  
        });
         // Rutas para el Reporte de Fichas de Fisioterapia
            Route::prefix('reportefisio')->name('reportefisio.')->group(function () {
                  Route::get('/', [ReporteFisioKineController::class, 'indexReporteFisio'])->name('index'); // Reporte de fichas de Fisioterapia
                  // Las rutas CRUD para una ficha específica de Fisioterapia desde el reporte
                  Route::get('/{cod_fisio}/editar', [ReporteFisioKineController::class, 'editFisio'])->name('edit');
                  Route::put('/{cod_fisio}/update', [ReporteFisioKineController::class, 'updateFisio'])->name('update');
                  Route::get('/{cod_fisio}/ver', [ReporteFisioKineController::class, 'showFisio'])->name('show');
                  Route::delete('/{cod_fisio}/eliminar', [ReporteFisioKineController::class, 'destroyFisio'])->name('destroy');
                  Route::get('/{cod_fisio}/exportar-word', [ReporteFisioKineController::class, 'exportarFichaFisioWordIndividual'])->name('exportWordIndividual');
            });

        // --- CORRECCIÓN: Rutas para Kinesiología accesibles por la especialidad combinada ---
        Route::prefix('kinesiologia')->name('kinesiologia.')->middleware('especialidad:Fisioterapia-Kinesiologia')->group(function () {
            // Route::get('/atencion', function() { return "Página de Atención de Kinesiología"; })->name('atencion');
            // Route::get('/reportes', function() { return "Página de Reportes de Kinesiología"; })->name('reportes');
             // Rutas para Kinesiología (Listado de Adultos Mayores para registro y CRUD individual)
            Route::prefix('fisiokine')->name('fisiokine.')->group(function () {
                  Route::get('/kinesiologia', [FisioKineController::class, 'indexKine'])->name('indexKine');
                  Route::get('/kinesiologia/{id_adulto}/registrar', [FisioKineController::class, 'createKine'])->name('createKine');
                  Route::post('/kinesiologia/{id_adulto}/store', [FisioKineController::class, 'storeKine'])->name('storeKine');
                  Route::get('/kinesiologia/{cod_kine}/editar', [FisioKineController::class, 'editKine'])->name('editKine'); // GET para mostrar formulario
                  Route::put('/kinesiologia/{cod_kine}/update', [FisioKineController::class, 'updateKine'])->name('updateKine'); // PUT para actualizar
                  Route::get('/kinesiologia/{cod_kine}/ver', [FisioKineController::class, 'showKine'])->name('showKine'); // GET para ver detalles
                  Route::delete('/kinesiologia/{cod_kine}/eliminar', [FisioKineController::class, 'destroyKine'])->name('destroyKine'); // DELETE para eliminar
          });
             // Rutas para el Reporte de Fichas de Kinesiología
            Route::prefix('reportekine')->name('reportekine.')->group(function () {
                  Route::get('/', [ReporteFisioKineController::class, 'indexReporteKine'])->name('index'); // Reporte de fichas de Kinesiología
                  Route::get('/{cod_kine}/editar', [ReporteFisioKineController::class, 'editKine'])->name('edit');
                  Route::put('/{cod_kine}/update', [ReporteFisioKineController::class, 'updateKine'])->name('update');
                  Route::get('/{cod_kine}/ver', [ReporteFisioKineController::class, 'showKine'])->name('show');
                  Route::delete('/{cod_kine}/eliminar', [ReporteFisioKineController::class, 'destroyKine'])->name('destroy');
                  Route::get('exportar-excel', [ReporteFisioKineController::class, 'exportarFichaKineExcel'])->name('exportExcel');
            });
        });
    });
});