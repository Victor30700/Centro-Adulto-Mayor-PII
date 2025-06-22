@extends('layouts.main')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico / Fisioterapia</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexFisioKine.css') }}"> 
    
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>

<body>
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">Módulo Médico / Fisioterapia</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Fisioterapia</li>
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
                    </div>

                    <!-- Botones para Navegación entre Módulos de Fisioterapia/Kinesiología y Ver Fichas -->
                    <div class="mb-3 d-flex justify-content-start gap-2">
                        <a href="{{ route('admin.fisiokine.indexFisio') }}" class="btn btn-primary">
                            <i class="fe fe-user-plus"></i> Registrar Nueva Ficha Fisioterapia
                        </a>
                        <a href="{{ route('admin.fisiokine.indexKine') }}" class="btn btn-secondary">
                            <i class="fe fe-heart"></i> Kinesiología
                        </a>
                    </div>

                    <!-- Tabla de Adultos Mayores para Registro de Fisioterapia -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Adultos Mayores para Fichas de Fisioterapia</h3>
                                </div>
                                <div class="card-body">
                                    {{-- Formulario de Filtros y Búsqueda --}}
                                    <form id="filterFormFisio" action="{{ route('admin.fisiokine.indexFisio') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <label for="search_fisio" class="form-label">Buscar Adulto Mayor:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search_fisio" name="search" placeholder="Buscar por CI, nombres, apellidos..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                                    <a href="{{ route('admin.fisiokine.indexFisio') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                                </div>
                                                <small class="text-muted">Busca por CI, nombres o apellidos del adulto mayor.</small>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="adultosTableFisio" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID Adulto</th>
                                                    <th>CI</th>
                                                    <th>Nombres y Apellidos</th>
                                                    <th>Fecha Nacimiento</th>
                                                    <th>Teléfono</th>
                                                    <th>Acciones Fisioterapia</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($adultos as $adulto)
                                                    <tr>
                                                        <td>{{ $adulto->id_adulto }}</td>
                                                        <td>{{ optional($adulto->persona)->ci ?? 'N/A' }}</td>
                                                        <td>
                                                            <strong>{{ optional($adulto->persona)->nombres }}</strong>
                                                            {{ optional($adulto->persona)->primer_apellido }}
                                                            {{ optional($adulto->persona)->segundo_apellido }}
                                                        </td>
                                                        <td>{{ optional(optional($adulto->persona)->fecha_nacimiento)->format('d/m/Y') ?? 'N/A' }}</td>
                                                        <td>{{ optional($adulto->persona)->telefono ?? 'N/A' }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Botón para registrar una NUEVA FICHA DE FISIOTERAPIA para este adulto mayor --}}
                                                                <a href="{{ route('admin.fisiokine.createFisio', ['id_adulto' => $adulto->id_adulto]) }}"
                                                                    class="btn btn-sm btn-success"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Registrar Nueva Ficha de Fisioterapia">
                                                                    <i class="fe fe-file-plus"></i>
                                                                </a>

                                                                {{-- Botón para EDITAR la ÚLTIMA FICHA DE FISIOTERAPIA de este adulto mayor --}}
                                                                @if($adulto->latestFisioterapia)
                                                                    <a href="{{ route('admin.fisiokine.editFisio', ['cod_fisio' => $adulto->latestFisioterapia->cod_fisio]) }}"
                                                                        class="btn btn-sm btn-primary"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Editar Última Ficha de Fisioterapia">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                                            data-bs-toggle="tooltip"
                                                                            title="No hay ficha de fisioterapia para editar.">
                                                                        <i class="fe fe-edit"></i>
                                                                    </button>
                                                                @endif

                                                                {{-- Botón para VER DETALLES de la ÚLTIMA FICHA DE FISIOTERAPIA del Adulto Mayor --}}
                                                                @if($adulto->latestFisioterapia)
                                                                    <a href="{{ route('admin.fisiokine.showFisio', ['cod_fisio' => $adulto->latestFisioterapia->cod_fisio]) }}"
                                                                        class="btn btn-sm btn-info"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Ver Detalles de Ficha de Fisioterapia">
                                                                        <i class="fe fe-eye"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                                            data-bs-toggle="tooltip"
                                                                            title="No hay detalles de ficha de fisioterapia para ver.">
                                                                        <i class="fe fe-eye-off"></i>
                                                                    </button>
                                                                @endif

                                                                {{-- Botón para ELIMINAR la ÚLTIMA FICHA DE FISIOTERAPIA del Adulto Mayor --}}
                                                                @if($adulto->latestFisioterapia)
                                                                    <form action="{{ route('admin.fisiokine.destroyFisio', ['cod_fisio' => $adulto->latestFisioterapia->cod_fisio]) }}"
                                                                        method="POST"
                                                                        style="display:inline-block;"
                                                                        onsubmit="showCustomConfirm(event, '¿Está seguro de que desea eliminar la última ficha de fisioterapia registrada para este adulto mayor? Esta acción no se puede deshacer.', this);">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-danger"
                                                                                data-bs-toggle="tooltip"
                                                                                title="Eliminar Última Ficha de Fisioterapia">
                                                                            <i class="fe fe-trash-2"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <button class="btn btn-sm btn-light text-muted" disabled
                                                                            data-bs-toggle="tooltip"
                                                                            title="No hay ficha de fisioterapia para eliminar.">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No se encontraron adultos mayores.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if(method_exists($adultos, 'links') && $adultos->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $adultos->links() }}
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

{{-- Incluye los scripts necesarios --}}

{{-- MODAL DE CONFIRMACIÓN PERSONALIZADO --}}
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
