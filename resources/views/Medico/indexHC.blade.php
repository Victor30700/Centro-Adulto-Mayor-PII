@extends('layouts.main')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico - Historias Clínicas</title>
    <!-- Añadimos jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico para este módulo -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHC.css') }}"> {{-- Este CSS lo crearemos a continuación --}}
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
                        <h1 class="page-title">Módulo Médico / Listado de Adultos Mayores (Historia Clínica)</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Historia Clínica</li>
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
                            {{-- Tarjeta: Total Adultos Mayores --}}
                            <div class="card overflow-hidden sales-card bg-primary-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $adultos->total() ?? 0 }}</h4>
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
                            {{-- Tarjeta: Total Historias Clínicas Registradas --}}
                            <div class="card overflow-hidden sales-card bg-success-gradient"> {{-- Usamos un gradiente verde para diferenciarse --}}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Historias Registradas</h6>
                                            {{-- Se asume que \App\Models\HistoriaClinica::count() existe y funciona --}}
                                            <h4 class="mb-0 num-text text-white">{{ \App\Models\HistoriaClinica::count() }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-success ms-auto box-shadow-success"> {{-- Icono y gradiente verde --}}
                                                <i class="fe fe-heart text-white mb-5"></i>
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
                                    <h3 class="card-title">Listado de Adultos Mayores para Gestión de Historia Clínica</h3>
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
                                                    <th>Fecha Registro Adulto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($adultos as $adulto)
                                                <tr>
                                                    <td>
                                                        <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }}</strong>
                                                        @if(optional($adulto->persona)->segundo_apellido)
                                                            {{ optional($adulto->persona)->segundo_apellido }}
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($adulto->persona)->ci }}</td>
                                                    <td>
                                                        @if($adulto->created_at)
                                                            {{ $adulto->created_at->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            {{-- Botón para registrar una NUEVA HISTORIA CLÍNICA para este adulto mayor --}}
                                                            <a href="{{ route('admin.medico.historia_clinica.register', ['id_adulto' => $adulto->id_adulto]) }}"
                                                               class="btn btn-sm btn-primary"
                                                               data-bs-toggle="tooltip"
                                                               title="Registrar Historia Clínica">
                                                                <i class="fe fe-file-plus"></i>
                                                            </a>

                                                            {{-- Botón para EDITAR la ÚLTIMA HISTORIA CLÍNICA de este adulto mayor --}}
                                                            @if($adulto->latestHistoriaClinica)
                                                                <a href="{{ route('admin.medico.historia_clinica.edit', ['id_historia' => $adulto->latestHistoriaClinica->id_historia]) }}"
                                                                   class="btn btn-sm btn-warning"
                                                                   data-bs-toggle="tooltip"
                                                                   title="Editar Última Historia">
                                                                    <i class="fe fe-edit"></i>
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-light text-muted" disabled
                                                                        data-bs-toggle="tooltip"
                                                                        title="No hay historia clínica para editar.">
                                                                    <i class="fe fe-edit"></i>
                                                                </button>
                                                            @endif

                                                            {{-- Botón para VER DETALLES de la ÚLTIMA HISTORIA CLÍNICA del Adulto Mayor --}}
                                                            @if($adulto->latestHistoriaClinica)
                                                                <a href="{{ route('admin.medico.historia_clinica.show_detalle', ['id_historia' => $adulto->latestHistoriaClinica->id_historia]) }}"
                                                                   class="btn btn-sm btn-info"
                                                                   data-bs-toggle="tooltip"
                                                                   title="Ver Detalles de Historia Clínica">
                                                                    <i class="fe fe-eye"></i>
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-light text-muted" disabled
                                                                        data-bs-toggle="tooltip"
                                                                        title="No hay detalles de historia clínica para ver.">
                                                                    <i class="fe fe-eye-off"></i>
                                                                </button>
                                                            @endif

                                                            {{-- Botón para ELIMINAR la ÚLTIMA HISTORIA CLÍNICA del Adulto Mayor --}}
                                                            @if($adulto->latestHistoriaClinica)
                                                                <form action="{{ route('admin.medico.historia_clinica.destroy', ['id_historia' => $adulto->latestHistoriaClinica->id_historia]) }}"
                                                                      method="POST"
                                                                      style="display:inline-block;"
                                                                      onsubmit="return confirm('¿Está seguro de que desea eliminar la última historia clínica registrada para este adulto mayor? Esta acción no se puede deshacer.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="btn btn-sm btn-danger"
                                                                            data-bs-toggle="tooltip"
                                                                            title="Eliminar Última Historia">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-sm btn-light text-muted" disabled
                                                                        data-bs-toggle="tooltip"
                                                                        title="No hay historia clínica para eliminar.">
                                                                    <i class="fe fe-trash-2"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        <i class="fe fe-inbox"></i>
                                                        <br>
                                                        No hay adultos mayores registrados.
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
            paging: false,      // ocultamos paginación (funcionalmente)
            info: false,        // ocultamos texto tipo "Showing X of Y"
            searching: true,    // ⚠️ debe seguir en true para que funcione tu input personalizado
            ordering: true,
            pageLength: 25,
            order: [[2, 'desc']],
            columnDefs: [
                { targets: [3], orderable: false, searchable: false }
            ],
            dom: 'rtip', // Esto muestra solo la tabla y la información de paginación básica
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
</body>
</html>
