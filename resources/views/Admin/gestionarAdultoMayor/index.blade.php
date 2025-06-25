{{-- views/Admin/gestionarAdultoMayor/index.blade.php --}}

@extends('layouts.main') {{-- Se usa el layout principal --}}

@section('content')

    <div class="page-header">
        <h1 class="page-title">Gestionar Adultos Mayores</h1>
        <div>
            <ol class="breadcrumb">
                {{-- Se genera la ruta del dashboard dinámicamente según el rol del usuario logueado. --}}
                @php
                    $dashboardRoute = optional(Auth::user()->rol)->nombre_rol . '.dashboard';
                @endphp
                <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestionar Adultos Mayores</li>
            </ol>
        </div>
    </div>

    {{-- Las alertas de sesión ahora se manejarán con SweetAlert2 en el script --}}

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-white mb-0">
                        <i class="fe fe-users me-2"></i>Listado de Adultos Mayores
                    </h3>
                   {{-- ===== AÑADIR ESTE BOTÓN ===== --}}
                    <a href="{{ route('admin.gestionar-usuarios.trash') }}" class="btn btn-secondary btn-sm ms-2" data-bs-toggle="tooltip" title="Ver registros eliminados">
                        <i class="fe fe-trash-2"></i> Papelera
                    </a>
                    {{-- ============================== --}}
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

{{-- Formulario oculto que usará SweetAlert2 para realizar la eliminación --}}
<form id="formEliminar" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    /* Estilos para que los botones de SweetAlert2 se vean consistentes */
    .swal2-styled.swal2-confirm, .swal2-styled.swal2-cancel {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endpush

@push('scripts')
{{-- CDN de SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // === MANEJO DE ALERTAS DE SESIÓN CON SWEETALERT2 ===
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif


    // === LÓGICA DE BÚSQUEDA Y PAGINACIÓN (SIN CAMBIOS) ===
    let timeoutId;
    const busquedaInput = document.getElementById('busquedaInput');
    const limpiarBtn = document.getElementById('limpiarBusqueda');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const tablaContainer = document.getElementById('tablaContainer');

    function realizarBusqueda(termino, page = 1) {
        loadingIndicator.style.display = 'block';
        tablaContainer.style.opacity = '0.5';
        
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
                // Alerta de error con SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la Búsqueda',
                    text: data.message || 'Ocurrió un error al procesar la búsqueda.',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Alerta de error de conexión con SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo conectar con el servidor. Por favor, revise su conexión a internet e intente de nuevo.',
                confirmButtonColor: '#d33'
            });
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


    // === INICIALIZACIÓN DE EVENTOS (MODIFICADO PARA SWEETALERT2) ===
    function inicializarEventosTabla() {
        // --- Confirmación de eliminación con SweetAlert2 ---
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.preventDefault(); // Previene la acción por defecto del botón
                
                const ci = this.dataset.ci;
                const nombre = this.dataset.nombre;
                
                // Se construye la URL del formulario de eliminación con la ruta correcta
                let deleteUrl = "{{ route('gestionar-adultomayor.eliminar', ['ci' => ':ci']) }}";
                deleteUrl = deleteUrl.replace(':ci', ci);
                
                // Diálogo de confirmación con SweetAlert2
                Swal.fire({
                    title: '¿Está seguro de eliminar este registro?',
                    html: `Se eliminará toda la información del adulto mayor:<br><strong>${nombre}</strong><br><small>CI: ${ci}</small><br><br><b class='text-danger'>Advertencia: ¡Esta acción no se puede deshacer!</b>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6e7881',
                    confirmButtonText: '<i class="fe fe-trash-2"></i> Sí, Eliminar',
                    cancelButtonText: '<i class="fe fe-x"></i> Cancelar',
                    reverseButtons: true // Pone el botón de confirmar a la derecha
                }).then((result) => {
                    // Si el usuario confirma, se envía el formulario oculto
                    if (result.isConfirmed) {
                        const form = document.getElementById('formEliminar');
                        form.action = deleteUrl;
                        form.submit();
                    }
                });
            });
        });

        // --- Inicialización de tooltips de Bootstrap (sin cambios) ---
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // === MANEJO DE PAGINACIÓN (SIN CAMBIOS) ===
    tablaContainer.addEventListener('click', function(event) {
        if (event.target.matches('.pagination a')) {
            event.preventDefault();
            const url = new URL(event.target.href);
            const page = url.searchParams.get('page');
            realizarBusqueda(busquedaInput.value.trim(), page);
        }
    });

    // Carga inicial de los eventos en la tabla
    inicializarEventosTabla();
});
</script>
@endpush
