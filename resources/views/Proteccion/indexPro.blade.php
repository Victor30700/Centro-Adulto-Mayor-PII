@extends('layouts.main')

@section('content')
<head>
    <!-- Añadimos jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/indexPro.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
</head>

<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">Modulo de Proteccion / Adultos Mayores Registrados</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Adultos Mayores</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Tarjetas de estadísticas (opcional) -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-primary-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Registrados</h6>
                                            <h4 class="mb-0 num-text text-white">{{ count($adultos) ?? 0 }}</h4>
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
                    <!-- Tabla de adultos mayores -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Adultos Mayores</h3>
                                </div>
                                <!-- Buscador -->
                                <div class="buscador row mb-4">
                                    <div class="col-md-12">
                                        <div class="input-group">
    <button type="button" class="input-group-text bg-primary text-white border-0" id="buscarButton">
        <i class="fe fe-search"></i>
    </button>
    <input type="text" 
           class="form-control" 
           id="busquedaInput" 
           placeholder="Buscar por CI, nombres o apellidos..."
           autocomplete="off">
    <button type="button" class="btn btn-outline-secondary" id="limpiarBusqueda">
        <i class="fe fe-x"></i> Limpiar
    </button>
</div>
                                        <small class="text-muted">Búsqueda en tiempo real por CI, nombres y apellidos.</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="adultosTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre completo</th>
                                                    <th>CI</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($adultos as $adulto)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $adulto->persona->nombres }} {{ $adulto->persona->primer_apellido }}</strong>
                                                        @if($adulto->persona->segundo_apellido)
                                                            {{ $adulto->persona->segundo_apellido }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $adulto->persona->ci }}</td>
                                                    <td>
                                                        @if($adulto->created_at)
                                                            {{ $adulto->created_at->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.caso.register', ['id_adulto' => $adulto->id_adulto]) }}" 
                                                               class="btn btn-sm btn-primary"
                                                               data-bs-toggle="tooltip"
                                                               title="Registrar nuevo caso">
                                                                <i class="fe fe-plus"></i>
                                                            </a>
                                                            <a href="{{ route('admin.caso.edit', ['id_adulto' => $adulto->id_adulto]) }}" 
                                                               class="btn btn-sm btn-warning"
                                                               data-bs-toggle="tooltip"
                                                               title="Editar información">
                                                                <i class="fe fe-edit"></i>
                                                            </a>
                                                            <a href="{{ route('admin.caso.detalle', ['id_adulto' => $adulto->id_adulto]) }}"
                                                               class="btn btn-sm btn-info"
                                                               data-bs-toggle="tooltip"
                                                               title="Ver detalles completos">
                                                                <i class="fe fe-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        <i class="fe fe-inbox"></i>
                                                        <br>
                                                        No hay adultos mayores registrados
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Paginación si está disponible -->
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    if (typeof $().DataTable === 'function') {
        var table = $('#adultosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        responsive: true,
        paging: false,    // ocultamos paginación (funcionalmente)
        info: false,      // ocultamos texto tipo "Showing X of Y"
        searching: true,  // ⚠️ debe seguir en true para que funcione tu input personalizado
        ordering: true,
        pageLength: 25,
        order: [[2, 'desc']],
        columnDefs: [
            { targets: [3], orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', text: '<i class="fe fe-download"></i> Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', text: '<i class="fe fe-file-text"></i> PDF', className: 'btn btn-danger btn-sm' },
            { extend: 'print', text: '<i class="fe fe-printer"></i> Imprimir', className: 'btn btn-info btn-sm' }
        ]
    });

        $('#busquedaInput').on('input', function () {
            table.search(this.value).draw();
        });

        $('#buscarButton').on('click', function () {
            table.search($('#busquedaInput').val()).draw();
        });

        $('#limpiarBusqueda').on('click', function () {
            $('#busquedaInput').val('');
            table.search('').draw();
        });
    }

    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
