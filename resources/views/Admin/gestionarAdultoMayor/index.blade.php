{{-- views/Admin/gestionarAdultoMayor/index.blade.php --}}
@extends('layouts.main') {{-- Se usa el layout principal --}}

@section('content')
    <div class="page-header">
        <h1 class="page-title">Gestionar Adultos Mayores</h1>
        <div>
            <ol class="breadcrumb">
                {{-- ===================== CORRECCIÓN 1 ===================== --}}
                {{-- Se genera la ruta del dashboard dinámicamente según el rol del usuario logueado. --}}
                @php
                    $dashboardRoute = optional(Auth::user()->rol)->nombre_rol . '.dashboard';
                @endphp
                <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestionar Adultos Mayores</li>
            </ol>
        </div>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fe fe-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fe fe-alert-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-white mb-0">
                        <i class="fe fe-users me-2"></i>Listado de Adultos Mayores
                    </h3>
                    <div class="card-options">
                        @if(isset($adultosMayores) && $adultosMayores->total() > 0)
                            <span class="badge bg-light text-success fs-12">
                                Total: {{ $adultosMayores->total() }} registros
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    {{-- Buscador --}}
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white">
                                    <i class="fe fe-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="busquedaInput" 
                                       placeholder="Buscar por CI, nombres o apellidos..."
                                       autocomplete="off">
                                <button class="btn btn-outline-success" type="button" id="limpiarBusqueda">
                                    <i class="fe fe-x"></i> Limpiar
                                </button>
                            </div>
                            <small class="text-muted">Búsqueda en tiempo real por CI, nombres y apellidos.</small>
                        </div>
                    </div>

                    {{-- Indicador de carga --}}
                    <div id="loadingIndicator" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        <p class="mt-2 text-muted">Buscando adultos mayores...</p>
                    </div>

                    {{-- Contenedor de la tabla --}}
                    <div id="tablaContainer">
                        @include('Admin.gestionarAdultoMayor.partials.tabla-adultos')
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Modal de confirmación para eliminar --}}
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="modalEliminarLabel">
                    <i class="fe fe-alert-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="fe fe-alert-triangle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">¿Está seguro de eliminar este registro?</h5>
                    <p class="text-muted">
                        Se eliminará toda la información del adulto mayor: <br>
                        <strong id="nombreEliminar"></strong> <br>
                        <small>CI: <span id="ciEliminar"></span></small>
                    </p>
                    <div class="alert alert-warning mt-3">
                        <small><strong>Advertencia:</strong> Esta acción no se puede deshacer.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fe fe-x"></i> Cancelar
                </button>
                <form id="formEliminar" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fe fe-trash-2"></i> Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeoutId;
    const busquedaInput = document.getElementById('busquedaInput');
    const limpiarBtn = document.getElementById('limpiarBusqueda');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const tablaContainer = document.getElementById('tablaContainer');
    
    function realizarBusqueda(termino, page = 1) {
        loadingIndicator.style.display = 'block';
        tablaContainer.style.opacity = '0.5';
        
        // ===================== CORRECCIÓN 3 =====================
        const url = `{{ route('gestionar-adultomayor.buscar') }}?busqueda=${encodeURIComponent(termino)}&page=${page}`;

        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tablaContainer.innerHTML = data.html;
                inicializarEventosTabla(); 
            } else {
                console.error('Error en búsqueda:', data.message);
                // mostrarAlerta('Error en la búsqueda', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // mostrarAlerta('Error de conexión durante la búsqueda', 'danger');
        })
        .finally(() => {
            loadingIndicator.style.display = 'none';
            tablaContainer.style.opacity = '1';
        });
    }
    
    busquedaInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        const termino = this.value.trim();
        timeoutId = setTimeout(() => { realizarBusqueda(termino, 1); }, 300);
    });
    
    limpiarBtn.addEventListener('click', function() {
        busquedaInput.value = '';
        realizarBusqueda('');
    });

    function inicializarEventosTabla() {
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const ci = this.dataset.ci;
                const nombre = this.dataset.nombre;
                
                document.getElementById('ciEliminar').textContent = ci;
                document.getElementById('nombreEliminar').textContent = nombre;
                
                // ===================== CORRECCIÓN 4 =====================
                // Se construye la URL del formulario de eliminación con la ruta correcta
                let deleteUrl = "{{ route('gestionar-adultomayor.eliminar', ['ci' => ':ci']) }}";
                deleteUrl = deleteUrl.replace(':ci', ci);
                document.getElementById('formEliminar').action = deleteUrl;
            });
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    tablaContainer.addEventListener('click', function(event) {
        if (event.target.matches('.pagination a')) {
            event.preventDefault();
            const url = new URL(event.target.href);
            const page = url.searchParams.get('page');
            realizarBusqueda(busquedaInput.value.trim(), page);
        }
    });
    
    inicializarEventosTabla();
});
</script>
@endpush