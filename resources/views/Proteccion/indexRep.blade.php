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
</head>
<body>

<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">Modulo de Protección / Reportes de Casos</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reportes de Casos</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Mensajes de éxito o error -->
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                            {{ session('error') }}
                        </div>
                    @endif

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
                                    <form action="{{ route('admin.reportes_proteccion.index') }}" method="GET" class="form-filter">
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
                                                     onclick="window.location.href='{{ route('admin.reportes_proteccion.index') }}'">
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
                                                        {{-- Mostrar nombre completo del denunciado si existe --}}
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
                                                            {{-- Botón "Ver Reporte" --}}
                                                            <a href="{{ route('admin.reportes_proteccion.showReporte', ['id_adulto' => $caso->id_adulto]) }}"
                                                               class="btn btn-sm btn-info"
                                                               data-bs-toggle="tooltip"
                                                               title="Ver Reporte">
                                                                <i class="fe fe-eye"></i>
                                                            </a>
                                                            {{-- Botón "Editar Caso" --}}
                                                            <a href="{{ route('admin.caso.edit', ['id_adulto' => $caso->id_adulto]) }}"
                                                               class="btn btn-sm btn-warning"
                                                               data-bs-toggle="tooltip"
                                                               title="Editar Caso">
                                                                <i class="fe fe-edit"></i>
                                                            </a>
                                                            {{-- NUEVO BOTÓN: Exportar a Word --}}
                                                            <a href="{{ route('admin.reportes_proteccion.exportWordIndividual', ['id_adulto' => $caso->id_adulto]) }}"
                                                               class="btn btn-sm btn-primary"
                                                               data-bs-toggle="tooltip"
                                                               title="Exportar a Word">
                                                                <i class="fe fe-file-text"></i>
                                                            </a>
                                                            {{-- Botón "Eliminar Caso" --}}
                                                            <form action="{{ route('admin.caso.destroy', ['id_adulto' => $caso->id_adulto]) }}"
                                                                  method="POST"
                                                                  style="display:inline-block;"
                                                                  onsubmit="return confirm('¿Está seguro de que desea eliminar este caso? Esto eliminará todos los datos relacionados.');">
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
            </div>
        </div>
    </div>
</div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
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
            paging: false, // Desactivado para usar la paginación de Laravel
            info: false,   // Desactivado para usar la paginación de Laravel
            searching: false, // Desactivar la barra de búsqueda por defecto de DataTables
            ordering: true,
            // pageLength: 25, // Ya no es relevante si paging está en false
            order: [[1, 'desc']], // Ordenar por Fecha Registro descendente por defecto
            columnDefs: [
                { targets: [9], orderable: false, searchable: false } // Columna de Acciones
            ],
            dom: 'Bfrtip', // Mostrar botones de exportación
            buttons: [
                { extend: 'excel', text: '<i class="fe fe-download"></i> Excel', className: 'btn btn-success btn-sm' },
                { extend: 'pdf', text: '<i class="fe fe-file-text"></i> PDF', className: 'btn btn-danger btn-sm' },
                { extend: 'print', text: '<i class="fe fe-printer"></i> Imprimir', className: 'btn btn-info btn-sm' }
            ]
        });

        // NOTA: La búsqueda y el filtro ahora se manejan enviando el formulario GET
        // al servidor. Se eliminan los listeners de DataTables para busquedaInput
        // y buscarButton para evitar conflictos con la paginación de Laravel.

        // Evento para el botón "Restablecer" (ya usa window.location.href)
        // No necesita modificar directamente los campos, solo redirigir.
        $('.reset').on('click', function() {
            window.location.href = '{{ route('admin.reportes_proteccion.index') }}';
        });
    }

    // Inicializar tooltips de Bootstrap (si usas Bootstrap para esto)
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>
