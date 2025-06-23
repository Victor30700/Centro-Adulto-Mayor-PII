@extends('layouts.main')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Casos de Protección</title>
    <!-- Añadimos jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/indexRep.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>

                      <div class="page-header">
                            <h1 class="page-title">Modulo de Protección / Reportes de Casos</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Reportes de Casos</li>
                                </ol>
                            </div>
                      </div>

                      {{-- Las alertas de sesión ahora se manejan con SweetAlert2 en el script --}}

                      <!-- Tarjetas de estadísticas (ajustado para paginación) -->
                      <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                                <div class="card overflow-hidden sales-card bg-primary-gradient">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <h6 class="card-text mb-0 text-white">Total Casos (Página Actual)</h6>
                                                <h4 class="mb-0 num-text text-white">{{ $casos->count() ?? 0 }}</h4>
                                            </div>
                                            <div class="col col-auto">
                                                <div class="counter-icon bg-gradient-primary ms-auto box-shadow-primary">
                                                    <i class="fe fe-file-text text-white mb-5"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                      </div>

                      <!-- Tabla de casos -->
                      <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Listado de Casos de Protección</h3>
                                    </div>
                                    <!-- Filtros -->
                                    <div class="card-body">
                                        <form action="{{ route('legal.reportes_proteccion.index') }}" method="GET" class="form-filter">
                                            <div class="input-group flex-col">
                                                <label for="nro_caso_filter" class="text-xs text-gray-600 mb-1">Filtrar por Nro. Caso:</label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="nro_caso_filter"
                                                       name="nro_caso_filter"
                                                       placeholder="Ej. 2024-001"
                                                       value="{{ $nro_caso_filter ?? '' }}">
                                            </div>

                                            <div class="input-group flex-col">
                                                <label for="search" class="text-xs text-gray-600 mb-1">Buscador General (Adulto Mayor):</label>
                                                <div class="flex">
                                                    <button type="submit" class="input-group-text bg-primary text-white border-0 rounded-l-md" id="buscarAdultoButton">
                                                        <i class="fe fe-search"></i>
                                                    </button>
                                                    <input type="text"
                                                           class="form-control"
                                                           id="busquedaAdultoInput"
                                                           name="search"
                                                           placeholder="Buscar por CI, nombres o apellidos del AM..."
                                                           autocomplete="off"
                                                           value="{{ $search ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="input-group flex-col">
                                                <label for="denunciado_search" class="text-xs text-gray-600 mb-1">Buscador (Ofensor):</label>
                                                <div class="flex">
                                                    <button type="submit" class="input-group-text bg-primary text-white border-0 rounded-l-md" id="buscarDenunciadoButton">
                                                        <i class="fe fe-search"></i>
                                                    </button>
                                                    <input type="text"
                                                           class="form-control"
                                                           id="busquedaDenunciadoInput"
                                                           name="denunciado_search"
                                                           placeholder="Buscar por nombres o apellidos del Denunciado..."
                                                           autocomplete="off"
                                                           value="{{ $denunciado_search ?? '' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="input-group" style="align-self: flex-end;">
                                                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                                                <button type="button"
                                                        class="btn btn-outline-secondary ms-2 reset"
                                                        onclick="window.location.href='{{ route('legal.reportes_proteccion.index')}}'">
                                                    <i class="fe fe-x"></i> Restablecer
                                                </button>
                                            </div>
                                        </form>

                                        <div class="table-responsive">
                                            <table id="casosTable" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nro. Caso</th>
                                                        <th>Fecha Registro</th>
                                                        <th>Nombres Adulto</th>
                                                        <th>Apellidos Adulto</th>
                                                        <th>CI Adulto</th>
                                                        <th>Nombre Completo Ofensor</th>
                                                        <th>Discapacidad</th>
                                                        <th>Vive con</th>
                                                        <th>Migrante</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($casos as $caso)
                                                        <tr>
                                                            <td>{{ $caso->nro_caso ?? 'N/A' }}</td>
                                                            <td>{{ optional($caso->fecha)->format('d/m/Y') ?? 'N/A' }}</td>
                                                            <td>{{ optional($caso->persona)->nombres ?? 'N/A' }}</td>
                                                            <td>
                                                                {{ optional($caso->persona)->primer_apellido ?? 'N/A' }}
                                                                @if(optional($caso->persona)->segundo_apellido)
                                                                    {{ optional($caso->persona)->segundo_apellido }}
                                                                @endif
                                                            </td>
                                                            <td>{{ optional($caso->persona)->ci ?? 'N/A' }}</td>
                                                            <td>
                                                                @if(optional($caso->denunciado)->personaNatural)
                                                                    {{ optional($caso->denunciado->personaNatural)->nombres ?? '' }}
                                                                    {{ optional($caso->denunciado->personaNatural)->primer_apellido ?? '' }}
                                                                    {{ optional($caso->denunciado->personaNatural)->segundo_apellido ?? '' }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>{{ $caso->discapacidad ?? 'N/A' }}</td>
                                                            <td>{{ $caso->vive_con ?? 'N/A' }}</td>
                                                            <td>{{ ($caso->migrante === true ? 'Sí' : ($caso->migrante === false ? 'No' : 'N/A')) }}</td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('legal.reportes_proteccion.showReporte', ['id_adulto' => $caso->id_adulto]) }}"
                                                                       class="btn btn-sm btn-info"
                                                                       data-bs-toggle="tooltip"
                                                                       title="Ver Reporte">
                                                                        <i class="fe fe-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $caso->id_adulto]) }}"
                                                                       class="btn btn-sm btn-warning"
                                                                       data-bs-toggle="tooltip"
                                                                       title="Editar Caso">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('legal.reportes_proteccion.exportWordIndividual', ['id_adulto' => $caso->id_adulto]) }}"
                                                                       class="btn btn-sm btn-primary"
                                                                       data-bs-toggle="tooltip"
                                                                       title="Exportar a Word">
                                                                        <i class="fe fe-file-text"></i>
                                                                    </a>
                                                                    {{-- Se reemplaza onsubmit por una clase para manejar con JS --}}
                                                                    <form action="{{ route('legal.caso.destroy', ['id_adulto' => $caso->id_adulto]) }}"
                                                                          method="POST"
                                                                          class="d-inline form-delete-case">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-danger"
                                                                                data-bs-toggle="tooltip"
                                                                                title="Eliminar Caso">
                                                                            <i class="fe fe-trash-2"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="10" class="text-center text-muted">
                                                                <i class="fe fe-inbox"></i>
                                                                <br>
                                                                No se encontraron casos de protección
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Paginación -->
                                        @if(method_exists($casos, 'links') && $casos->hasPages())
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $casos->appends(request()->query())->links() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                      </div>
    

@endsection

@push('scripts')
{{-- Se mueven los scripts al final del body, como en el original --}}
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
{{-- Cargamos SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicialización de DataTables
    if (typeof $().DataTable === 'function') {
        var table = $('#casosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            paging: false,
            info: false,
            searching: false,
            ordering: true,
            order: [[1, 'desc']],
            columnDefs: [
                { targets: [9], orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'excel', text: '<i class="fe fe-download"></i> Excel', className: 'btn btn-success btn-sm' },
                { extend: 'pdf', text: '<i class="fe fe-file-text"></i> PDF', className: 'btn btn-danger btn-sm' },
                { extend: 'print', text: '<i class="fe fe-printer"></i> Imprimir', className: 'btn btn-info btn-sm' }
            ]
        });
    }

    // Evento para el botón "Restablecer"
    $('.reset').on('click', function() {
        window.location.href = '{{ route('legal.reportes_proteccion.index') }}';
    });

    // Inicializar tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // --- MANEJO DE ALERTAS CON SWEETALERT2 ---

    // Alertas de sesión
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });
    @endif

    // Confirmación para eliminar caso
    $('.form-delete-case').on('submit', function(event) {
        event.preventDefault();
        const form = this;
        Swal.fire({
            title: '¿Está seguro de que desea eliminar este caso?',
            text: "Esto eliminará todos los datos relacionados y la acción no se puede deshacer.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush

</body>
</html>