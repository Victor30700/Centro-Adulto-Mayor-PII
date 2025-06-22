@include('header')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Enfermería - Fichas de Enfermería</title>
    <!-- Añadimos jQuery (si es necesario para algún otro script en dashboard.css o generales) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Añadimos el CSS del dashboard y el nuevo CSS específico para este módulo -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexEnfermeria.css') }}"> {{-- Este CSS es ahora el mismo que indexHistoriaClinica.css --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- NO DataTables CSS aquí, la paginación y búsqueda se manejan con Laravel --}}
</head>

<body>
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">Módulo Enfermería / Listado de Adultos Mayores (Fichas de Enfermería)</h1>
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
                                            <h4 class="mb-0 num-text text-white">{{ $totalAdultosMayores ?? 0 }}</h4>
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
                            {{-- Tarjeta: Total Fichas de Enfermería Registradas --}}
                            <div class="card overflow-hidden sales-card bg-info-gradient"> {{-- Usamos un gradiente azul claro para diferenciarse --}}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Fichas Enfermería Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalFichasEnfermeria ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info"> {{-- Icono y gradiente azul claro --}}
                                                <i class="fe fe-heart text-white mb-5"></i> {{-- Puedes cambiar el icono a algo más relacionado con enfermería si tienes uno, ej. fe fe-activity o fe fe-plus-square --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Puedes añadir más tarjetas si necesitas otras estadísticas --}}
                    </div>

                    <!-- Tabla de adultos mayores -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Adultos Mayores para Gestión de Fichas de Enfermería</h3>
                                </div>
                                <!-- Buscador -->
                                <div class="card-body">
                                    <form action="{{ route('admin.enfermeria.index') }}" method="GET" class="buscador row mb-4">
                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <button type="submit" class="input-group-text bg-primary text-white border-0" id="buscarButton">
                                                    <i class="fe fe-search"></i>
                                                </button>
                                                <input type="text"
                                                       name="search"
                                                       class="form-control"
                                                       id="busquedaInput"
                                                       placeholder="Buscar por CI, nombres o apellidos..."
                                                       autocomplete="off"
                                                       value="{{ $search ?? '' }}">
                                                @if ($search)
                                                    <a href="{{ route('admin.enfermeria.index') }}" class="btn btn-outline-secondary" id="limpiarBusqueda">
                                                        <i class="fe fe-x"></i> Limpiar
                                                    </a>
                                                @endif
                                            </div>
                                            <small class="text-muted">Búsqueda por CI, nombres y apellidos.</small>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="adultosTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre completo</th>
                                                    <th>CI</th>
                                                    <th>Fecha Registro Adulto</th>
                                                    <th>Última Ficha Enfermería</th>
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
                                                        @if($adulto->latestEnfermeria)
                                                            {{ $adulto->latestEnfermeria->created_at->format('d/m/Y') }}
                                                            <br>
                                                            <span class="text-muted">(Cód: {{ $adulto->latestEnfermeria->cod_enf }})</span>
                                                        @else
                                                            <span class="text-muted">Sin ficha</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            {{-- Botón para registrar una NUEVA FICHA DE ENFERMERÍA para este adulto mayor --}}
                                                            <a href="{{ route('admin.enfermeria.create', ['id_adulto' => $adulto->id_adulto]) }}"
                                                               class="btn btn-sm btn-success"
                                                               data-bs-toggle="tooltip"
                                                               title="Registrar Nueva Ficha de Enfermería">
                                                                <i class="fe fe-file-plus"></i>
                                                            </a>

                                                            {{-- Botón para EDITAR la ÚLTIMA FICHA DE ENFERMERÍA de este adulto mayor --}}
                                                            @if($adulto->latestEnfermeria)
                                                                <a href="{{ route('admin.enfermeria.edit', ['cod_enf' => $adulto->latestEnfermeria->cod_enf]) }}"
                                                                   class="btn btn-sm btn-primary"
                                                                   data-bs-toggle="tooltip"
                                                                   title="Editar Última Ficha">
                                                                    <i class="fe fe-edit"></i>
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-light text-muted" disabled
                                                                        data-bs-toggle="tooltip"
                                                                        title="No hay ficha de enfermería para editar.">
                                                                    <i class="fe fe-edit"></i>
                                                                </button>
                                                            @endif

                                                            {{-- Botón para VER DETALLES de la ÚLTIMA FICHA DE ENFERMERÍA del Adulto Mayor --}}
                                                            @if($adulto->latestEnfermeria)
                                                                <a href="{{ route('admin.enfermeria.show', ['cod_enf' => $adulto->latestEnfermeria->cod_enf]) }}"
                                                                   class="btn btn-sm btn-info"
                                                                   data-bs-toggle="tooltip"
                                                                   title="Ver Detalles de Ficha de Enfermería">
                                                                    <i class="fe fe-eye"></i>
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-light text-muted" disabled
                                                                        data-bs-toggle="tooltip"
                                                                        title="No hay detalles de ficha de enfermería para ver.">
                                                                    <i class="fe fe-eye-off"></i>
                                                                </button>
                                                            @endif

                                                            {{-- Botón para ELIMINAR la ÚLTIMA FICHA DE ENFERMERÍA del Adulto Mayor --}}
                                                            @if($adulto->latestEnfermeria)
                                                                <form action="{{ route('admin.enfermeria.destroy', ['cod_enf' => $adulto->latestEnfermeria->cod_enf]) }}"
                                                                      method="POST"
                                                                      style="display:inline-block;"
                                                                      onsubmit="return confirm('¿Está seguro de que desea eliminar la última ficha de enfermería registrada para este adulto mayor? Esta acción no se puede deshacer.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="btn btn-sm btn-danger"
                                                                            data-bs-toggle="tooltip"
                                                                            title="Eliminar Última Ficha">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-sm btn-light text-muted" disabled
                                                                        data-bs-toggle="tooltip"
                                                                        title="No hay ficha de enfermería para eliminar.">
                                                                    <i class="fe fe-trash-2"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        <i class="fe fe-inbox"></i>
                                                        <br>
                                                        No hay adultos mayores que coincidan con la búsqueda.
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

@include('footer')
{{-- NO DataTables JS aquí, ya que usamos la paginación y búsqueda de Laravel --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
</body>
</html>
