@extends('layouts.main')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Orientación</title>
    <!-- Añadimos jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Orientacion/indexRep.css') }}"> {{-- Asegúrate de que este CSS se cargue --}}
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
                        <h1 class="page-title">Módulo de Orientación / Reportes de Orientaciones</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reportes de Orientaciones</li>
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
                            {{-- Tarjeta de "Total Orientaciones" --}}
                            <div class="card overflow-hidden sales-card bg-primary-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Orientaciones</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $orientaciones->total() ?? 0 }}</h4>
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

                        {{-- NUEVA TARJETA: Total Fichas Registradas (General) --}}
                        {{-- <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-info-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Fichas Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $orientaciones->total() ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info">
                                                <i class="fe fe-bar-chart-2 text-white mb-5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        {{-- FIN NUEVA TARJETA --}}

                    </div>

                    <!-- Tabla de orientaciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Fichas de Orientación</h3>
                                </div>
                                <!-- Filtros -->
                                <div class="card-body">
                                    <form action="{{ route('admin.reportes_orientacion.index') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label for="cod_or_filter" class="form-label">Nro. Ficha:</label>
                                                <input type="text" class="form-control" id="cod_or_filter" name="cod_or_filter" placeholder="Ej. 123" value="{{ $cod_or_filter ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="tipo_orientacion_filter" class="form-label">Tipo Orientación:</label>
                                                <select class="form-select" id="tipo_orientacion_filter" name="tipo_orientacion_filter">
                                                    <option value="">Todos</option>
                                                    @foreach($tiposOrientacion as $key => $value)
                                                        <option value="{{ $key }}" {{ ($tipo_orientacion_filter == $key) ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="search" class="form-label">Búsqueda General (Nombre/CI Adulto, Motivos):</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por nombre, CI, o motivos..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fe fe-search"></i> Buscar
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('admin.reportes_orientacion.index') }}'">
                                                        <i class="fe fe-x"></i> Restablecer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="adultosTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nº Ficha</th>
                                                    <th>Fecha Ingreso</th>
                                                    <th>Tipo Orientación</th>
                                                    <th>Motivos (Extracto)</th>
                                                    <th>Adulto Mayor</th>
                                                    <th>CI Adulto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($orientaciones as $orientacion)
                                                    <tr>
                                                        <td>{{ $orientacion->cod_or ?? 'N/A' }}</td>
                                                        <td>{{ optional($orientacion->fecha_ingreso)->format('d/m/Y') ?? 'N/A' }}</td>
                                                        <td>
                                                            @php
                                                                $tipo = $orientacion->tipo_orientacion;
                                                                if ($tipo == 'psicologica') echo 'PSICOLÓGICA';
                                                                else if ($tipo == 'social') echo 'SOCIAL';
                                                                else if ($tipo == 'legal') echo 'LEGAL';
                                                                else echo 'N/A';
                                                            @endphp
                                                        </td>
                                                        <td>{{ \Illuminate\Support\Str::limit($orientacion->motivo_orientacion, 50, '...') ?? 'N/A' }}</td>
                                                        <td>
                                                            {{ optional($orientacion->adulto->persona)->nombres ?? 'N/A' }}
                                                            {{ optional($orientacion->adulto->persona)->primer_apellido ?? 'N/A' }}
                                                            {{ optional($orientacion->adulto->persona)->segundo_apellido ?? '' }}
                                                        </td>
                                                        <td>{{ optional($orientacion->adulto->persona)->ci ?? 'N/A' }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Botón "Ver Reporte" --}}
                                                                <a href="{{ route('admin.reportes_orientacion.show_reporte', ['cod_or' => $orientacion->cod_or]) }}"
                                                                   class="btn btn-sm btn-info"
                                                                   data-bs-toggle="tooltip"
                                                                   title="Ver Reporte">
                                                                    <i class="fe fe-eye"></i>
                                                                </a>
                                                                {{-- NUEVO: Botón "Exportar a Word" --}}
                                                                <a href="{{ route('admin.reportes_orientacion.exportWordIndividual', ['cod_or' => $orientacion->cod_or]) }}"
                                                                   class="btn btn-sm btn-primary" {{-- Puedes cambiar el color: btn-success, btn-warning, etc. --}}
                                                                   data-bs-toggle="tooltip"
                                                                   title="Exportar a Word">
                                                                    <i class="fe fe-file-text"></i>
                                                                </a>
                                                                {{-- Botón "Eliminar Ficha" --}}
                                                                <form action="{{ route('admin.reportes_orientacion.destroy', ['cod_or' => $orientacion->cod_or]) }}"
                                                                      method="POST"
                                                                      style="display:inline-block;"
                                                                      onsubmit="return confirm('¿Está seguro de que desea eliminar esta ficha de orientación? Esta acción no se puede deshacer.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="btn btn-sm btn-danger"
                                                                            data-bs-toggle="tooltip"
                                                                            title="Eliminar Ficha">
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
                                                            No se encontraron fichas de orientación.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if(method_exists($orientaciones, 'links') && $orientaciones->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $orientaciones->appends(request()->query())->links() }}
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

@push('scripts')
<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicialización de DataTables
    // Nota: Deshabilitamos algunas funcionalidades de DataTables porque Laravel maneja la paginación y la búsqueda.
    var table = $('#adultosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        responsive: true,
        paging: false,      // Deshabilitar paginación de DataTables, ya que la manejamos con Laravel
        info: false,        // Deshabilitar info de DataTables
        searching: false,   // Deshabilitar la búsqueda por defecto de DataTables, usamos la del formulario
        ordering: true,     // Habilitar el ordenamiento de columnas
        columnDefs: [
            { targets: [6], orderable: false, searchable: false } // Deshabilitar ordenamiento y búsqueda para la columna de acciones
        ],
    });

    // Manejo de tooltips de Bootstrap
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
@endpush