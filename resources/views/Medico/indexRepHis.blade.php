@extends('layouts.main')

@section('content')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico / Reportes de Historia Clínica</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHistoriaClinica.css') }}"> 
    
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
                        <h1 class="page-title">Módulo Médico / Reportes de Historia Clínica</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reportes de Historia Clínica</li>
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
                            <div class="card overflow-hidden sales-card bg-warning-gradient"> {{-- Color warning para historia clínica --}}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Historias Clínicas Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalHistoriasClinicas ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-warning ms-auto box-shadow-warning">
                                                <i class="fe fe-file-text text-white mb-5"></i> {{-- Icono de historia clínica --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones para Navegación entre Reportes -->
                    <div class="mb-3 d-flex justify-content-start gap-2">
                        <a href="{{ route('admin.reportes_enfermeria.index') }}" class="btn btn-secondary">
                            <i class="fe fe-heart"></i> Reportes de Atención de Enfermería
                        </a>
                        <a href="{{ route('admin.reportes_historia_clinica.index') }}" class="btn btn-primary">
                            <i class="fe fe-file-text"></i> Reportes de Historia Clínica
                        </a>
                    </div>

                    <!-- Tabla de reportes de Historia Clínica -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Historias Clínicas</h3>
                                </div>
                                <div class="card-body">
                                    {{-- Formulario de Filtros y Búsqueda (gestionado por Laravel) --}}
                                    <form id="filterFormHis" action="{{ route('admin.reportes_historia_clinica.index') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-4">
                                                <label for="fecha_inicio_his" class="form-label">Fecha Inicio:</label>
                                                <input type="date" class="form-control" id="fecha_inicio_his" name="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="fecha_fin_his" class="form-label">Fecha Fin:</label>
                                                <input type="date" class="form-control" id="fecha_fin_his" name="fecha_fin" value="{{ $fecha_fin ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="search_his" class="form-label">Búsqueda General:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search_his" name="search" placeholder="Buscar por CI, nombres, motivo, diagnóstico..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                                    <a href="{{ route('admin.reportes_historia_clinica.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                                </div>
                                                <small class="text-muted">Búsqueda por CI, nombres, motivo de consulta o diagnóstico.</small>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="historiasTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID Historia</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Adulto Mayor</th>
                                                    <th>CI Adulto</th>
                                                    <th>Motivo Consulta (Extracto)</th>
                                                    <th>Diagnóstico (Extracto)</th>
                                                    <th>Registrado Por</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($historiasClinicas as $historia)
                                                    <tr>
                                                        <td>{{ $historia->id_historia }}</td>
                                                        <td>{{ optional($historia->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                                        <td>
                                                            <strong>{{ optional($historia->adulto->persona)->nombres }} {{ optional($historia->adulto->persona)->primer_apellido }}</strong>
                                                            @if(optional($historia->adulto->persona)->segundo_apellido)
                                                                {{ optional($historia->adulto->persona)->segundo_apellido }}
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($historia->adulto->persona)->ci ?? 'N/A' }}</td>
                                                        <td>{{ $historia->motivo_consulta ? Str::limit($historia->motivo_consulta, 50, '...') : 'N/A' }}</td>
                                                        <td>{{ $historia->diagnostico_medico ? Str::limit($historia->diagnostico_medico, 50, '...') : 'N/A' }}</td>
                                                        <td>{{ optional($historia->usuario->persona)->nombres }} {{ optional($historia->usuario->persona)->primer_apellido }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Enlace para ver detalles (si lo tienes, ajusta la ruta) --}}
                                                                {{-- <a href="{{ route('admin.medico.historia_clinica.show_detalle', ['id_historia' => $historia->id_historia]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ver Detalles">
                                                                    <i class="fe fe-eye"></i>
                                                                </a> --}}
                                                                {{-- Botón para exportar esta Historia Clínica individual a Excel --}}
                                                                <a href="{{ route('admin.reportes_historia_clinica.exportar_excel', ['id_historia' => $historia->id_historia]) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Exportar a Excel">
                                                                    <i class="fe fe-file-text"></i>
                                                                </a>
                                                                {{-- Botón para eliminar (si lo tienes, ajusta la ruta y el formulario) --}}
                                                                {{-- <form action="{{ route('admin.medico.historia_clinica.destroy', ['id_historia' => $historia->id_historia]) }}" method="POST" onsubmit="showCustomConfirm(event, '¿Está seguro de que desea eliminar esta Historia Clínica?', this);">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form> --}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No se encontraron historias clínicas con los filtros aplicados.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if(method_exists($historiasClinicas, 'links') && $historiasClinicas->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $historiasClinicas->links() }}
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

{{-- IMPORTANTE: NO DataTables JS aquí, ya que no se usa en esta vista --}}

{{-- Scripts adicionales --}}

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
// Función para mostrar el modal de confirmación personalizado (si lo necesitas para eliminar en esta vista)
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
