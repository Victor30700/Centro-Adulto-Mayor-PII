{{-- resources/views/Medico/indexRepFisio.blade.php --}}
@extends('layouts.main')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico / Reporte de Fichas de Fisioterapia</title>
    
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
                        <h1 class="page-title">Módulo Médico / Reporte de Fichas de Fisioterapia</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reporte Fisioterapia</li>
                            </ol>
                        </div>
                    </div>

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

                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-success-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Fichas Fisioterapia Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalFichasFisioterapia ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-success ms-auto box-shadow-success">
                                                <i class="fe fe-file-text text-white mb-5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex justify-content-start gap-2"> {{-- Revertido a solo navegación --}}
                        <a href="{{ route('admin.reportekine.index') }}" class="btn btn-info">
                            <i class="fe fe-list"></i> Ver Reporte de Fichas Kinesiología
                        </a>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Fichas de Fisioterapia Registradas</h3>
                                </div>
                                <div class="card-body">
                                    {{-- Formulario de Filtros y Búsqueda para fichas --}}
                                    <form id="filterFormFichasFisio" action="{{ route('admin.reportefisio.index') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <label for="search_fichas_fisio" class="form-label">Buscar Ficha (Adulto Mayor, Motivo, Equipos):</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search_fichas_fisio" name="search" placeholder="Buscar por CI, nombres, motivo de consulta..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                                    <a href="{{ route('admin.reportefisio.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                                </div>
                                                <small class="text-muted">Busca por CI, nombres del adulto mayor, motivo de consulta o equipos.</small>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Cod. Fisio</th>
                                                    <th>Adulto Mayor</th>
                                                    <th>CI</th>
                                                    <th>Fecha Programación</th>
                                                    <th>Motivo Consulta</th>
                                                    <th>Registrado Por</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($fichasFisioterapia as $fichaFisio)
                                                    <tr>
                                                        <td>{{ $fichaFisio->cod_fisio }}</td>
                                                        <td>
                                                            <strong>{{ optional(optional($fichaFisio->adulto)->persona)->nombres }}</strong>
                                                            {{ optional(optional($fichaFisio->adulto)->persona)->primer_apellido }}
                                                            {{ optional(optional($fichaFisio->adulto)->persona)->segundo_apellido }}
                                                        </td>
                                                        <td>{{ optional(optional($fichaFisio->adulto)->persona)->ci }}</td>
                                                        <td>{{ optional($fichaFisio->fecha_programacion)->format('d/m/Y') ?? 'N/A' }}</td>
                                                        <td>{{ Str::limit($fichaFisio->motivo_consulta, 50) ?? 'N/A' }}</td>
                                                        <td>{{ optional(optional($fichaFisio->usuario)->persona)->nombres }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Botón para Ver Detalles --}}
                                                                <a href="{{ route('admin.reportefisio.show', ['cod_fisio' => $fichaFisio->cod_fisio]) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Ver Detalles">
                                                                    <i class="fe fe-eye"></i>
                                                                </a>
                                                                {{-- NUEVO: Botón para Exportar a Word (ficha individual) --}}
                                                                <a href="{{ route('admin.reportefisio.exportWordIndividual', ['cod_fisio' => $fichaFisio->cod_fisio]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Exportar Word">
                                                                    <i class="fe fe-file-text"></i>
                                                                </a>
                                                                {{-- Botón para Eliminar Ficha --}}
                                                                <form action="{{ route('admin.reportefisio.destroy', ['cod_fisio' => $fichaFisio->cod_fisio]) }}" method="POST" onsubmit="showCustomConfirm(event, '¿Está seguro de que desea eliminar esta ficha de Fisioterapia?', this);">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar Ficha">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No se encontraron fichas de fisioterapia registradas.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        @if(method_exists($fichasFisioterapia, 'links') && $fichasFisioterapia->hasPages())
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $fichasFisioterapia->links() }}
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
</div>

@endsection

{{-- Incluye el footer --}}

{{-- MODAL DE CONFIRMACIÓN PERSONALIZADO (se mantiene aquí porque esta vista lo usa) --}}
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

        // NO HAY LÓGICA DE JS PARA EL BOTÓN DE WORD INDIVIDUAL, YA QUE ES UN ENLACE DIRECTO (<a> tag)

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
