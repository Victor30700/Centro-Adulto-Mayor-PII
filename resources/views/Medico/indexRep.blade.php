@extends('layouts.main')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico / Reportes de Atención de Enfermería</title>
    
    {{-- Carga de jQuery (si es necesario para algún otro script en dashboard.css o generales) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Tus CSS personalizados --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHistoriaClinica.css') }}"> 
    
    {{-- Feather Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    {{-- IMPORTANTE: NO DataTables CSS/JS aquí --}}
</head>

<body>
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">Módulo Médico / Reportes de Atención de Enfermería</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reportes de Enfermería</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Mensajes de éxito o error -->
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Tarjetas de estadísticas -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-primary-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores Registrados</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalAdultos ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-primary ms-auto box-shadow-primary">
                                                <i class="fe fe-users text-white mb-5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-info-gradient"> {{-- Color info para enfermería --}}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Fichas de Enfermería Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalFichasEnfermeria ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info">
                                                <i class="fe fe-heart text-white mb-5"></i> {{-- Icono de corazón para enfermería --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones para Navegación entre Reportes -->
                    <div class="mb-3 d-flex justify-content-start gap-2">
                        <a href="{{ route('admin.reportes_enfermeria.index') }}" class="btn btn-primary">
                            <i class="fe fe-heart"></i> Reportes de Atención de Enfermería
                        </a>
                        <a href="{{ route('admin.reportes_historia_clinica.index') }}" class="btn btn-secondary">
                            <i class="fe fe-file-text"></i> Reportes de Historia Clínica
                        </a>
                    </div>


                    <!-- Tabla de reportes de Atención de Enfermería -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Fichas de Atención de Enfermería</h3>
                                </div>
                                <div class="card-body">
                                    {{-- Formulario de Filtros y Búsqueda (gestionado por Laravel) --}}
                                    <form id="filterForm" action="{{ route('admin.reportes_enfermeria.index') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-4">
                                                <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fecha_fin ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="search" class="form-label">Búsqueda General:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por CI, nombres, PA, Temp, Derivación..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                                    <a href="{{ route('admin.reportes_enfermeria.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                                </div>
                                                <small class="text-muted">Búsqueda por CI, nombres, presión arterial, temperatura, o derivación.</small>
                                            </div>
                                        </div>
                                    </form>

                                    {{-- Botones para Imprimir y Exportar Excel --}}
                                    <div class="mb-3 d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-success btn-sm" id="exportarExcelBtn">
                                            <i class="fe fe-download"></i> Exportar a Excel
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="reportesTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID Atención</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Adulto Mayor</th>
                                                    <th>CI Adulto</th>
                                                    <th>Presión Arterial</th>
                                                    <th>Temperatura</th>
                                                    <th>Derivación (Extracto)</th>
                                                    <th>Registrado Por</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reportes as $reporte)
                                                    <tr>
                                                        <td>{{ $reporte->cod_enf }}</td>
                                                        <td>{{ optional($reporte->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                                        <td>
                                                            <strong>{{ optional($reporte->adulto->persona)->nombres }} {{ optional($reporte->adulto->persona)->primer_apellido }}</strong>
                                                            @if(optional($reporte->adulto->persona)->segundo_apellido)
                                                                {{ optional($reporte->adulto->persona)->segundo_apellido }}
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($reporte->adulto->persona)->ci ?? 'N/A' }}</td>
                                                        <td>{{ $reporte->presion_arterial ?? 'N/A' }}</td>
                                                        <td>{{ $reporte->temperatura ?? 'N/A' }}</td>
                                                        <td>{{ $reporte->derivacion ? Str::limit($reporte->derivacion, 50, '...') : 'N/A' }}</td>
                                                        <td>{{ optional($reporte->usuario->persona)->nombres }} {{ optional($reporte->usuario->persona)->primer_apellido }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.reportes_enfermeria.show_atencion_enfermeria', ['cod_enf' => $reporte->cod_enf]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver Detalles">
                                                                    <i class="fe fe-eye"></i>
                                                                </a>
                                                                {{-- Usar un modal de confirmación personalizado para eliminar --}}
                                                                <form action="{{ route('admin.reportes_enfermeria.destroy_atencion_enfermeria', ['cod_enf' => $reporte->cod_enf]) }}" method="POST" onsubmit="showCustomConfirm(event, '¿Está seguro de que desea eliminar esta atención de enfermería?', this);">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No se encontraron fichas de atención de enfermería con los filtros aplicados.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if(method_exists($reportes, 'links') && $reportes->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $reportes->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- Carga de scripts al final del body --}}

{{-- MODAL DE CONFIRMACIÓN PERSONALIZADO (Añadir al final del body si no está ya en el footer o un layout principal) --}}
<div class="modal fade" id="customConfirmModalOverlay" tabindex="-1" aria-labelledby="customConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="customConfirmModalLabel">Confirmación de Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="customConfirmMessage">¿Está seguro de que desea realizar esta acción?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="customConfirmBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para mostrar el modal de confirmación personalizado
let currentConfirmForm = null; 

function showCustomConfirm(event, message, form) {
    event.preventDefault(); 
    currentConfirmForm = form; 

    const overlay = document.getElementById('customConfirmModalOverlay');
    const msgElement = document.getElementById('customConfirmMessage');
    const confirmBtn = document.getElementById('customConfirmBtn');

    msgElement.textContent = message; 
    
    confirmBtn.onclick = null; 
    confirmBtn.onclick = function() {
        if (currentConfirmForm) {
            currentConfirmForm.submit(); 
            hideCustomConfirm(); 
        }
    };

    var customConfirmModal = new bootstrap.Modal(overlay);
    customConfirmModal.show();
    return false; 
}

function hideCustomConfirm() {
    const overlay = document.getElementById('customConfirmModalOverlay');
    var customConfirmModal = bootstrap.Modal.getInstance(overlay);
    if (customConfirmModal) {
        customConfirmModal.hide(); 
    }
    currentConfirmForm = null; 
}


document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Lógica para el botón de imprimir registros
    document.getElementById('imprimirRegistrosBtn').addEventListener('click', function() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();
        // Redirige a la nueva ruta de impresión con los mismos parámetros de filtro
        window.open(`{{ route('admin.reportes_enfermeria.imprimir') }}?${queryString}`, '_blank');
    });

    // Lógica para el botón de exportar a Excel
    document.getElementById('exportarExcelBtn').addEventListener('click', function() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();
        // Redirige a la nueva ruta de exportación de Excel con los mismos parámetros de filtro
        window.location.href = `{{ route('admin.reportes_enfermeria.exportar_excel') }}?${queryString}`;
    });

    // Inicializar tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    }
});
</script>
</body>
</html>
