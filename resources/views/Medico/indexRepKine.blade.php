indexRepKine:
@extends('layouts.main')

{{-- Contenido de la cabecera (HEAD) --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Médico / Reporte de Fichas de Kinesiología</title>
    
    {{-- jQuery (si es necesario para otras librerías como DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    {{-- Hojas de estilo --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexFisioKine.css') }}"> 
    
    {{-- Feather Icons para los íconos --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    
    {{-- SweetAlert2 CSS para alertas y confirmaciones --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

{{-- Contenido principal de la vista (BODY) --}}
@section('content')

                    <div class="page-header">
                        <h1 class="page-title">Módulo Médico / Reporte de Fichas de Kinesiología</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reporte Kinesiología</li>
                            </ol>
                        </div>
                    </div>

                    {{-- Las alertas de sesión se manejarán con JS a través de SweetAlert2. --}}
                    {{-- Por lo tanto, los bloques @if(session('success')) y @if(session('error')) se eliminan del HTML. --}}

                    <!-- Tarjetas de estadísticas -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-success-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Fichas Kinesiología Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalFichasKinesiologia ?? 0 }}</h4>
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

                    <!-- Botones para Navegación entre Módulos y Exportar Excel General -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            {{-- Botón para ir al Reporte de Fichas de Fisioterapia --}}
                            {{-- Ruta: responsable.fisioterapia.reportefisio.index --}}
                            <a href="{{ route('responsable.fisioterapia.reportefisio.index') }}" class="btn btn-info">
                                <i class="fe fe-list"></i> Ver Reporte de Fichas Fisioterapia
                            </a>
                        </div>
                        {{-- Botón para Exportar a Excel (General) --}}
                        {{-- La lógica de exportación se manejará con JavaScript al hacer clic --}}
                        <button type="button" class="btn btn-success btn-sm" id="exportarExcelGeneralBtn">
                            <i class="fe fe-download"></i> Exportar a Excel
                        </button>
                    </div>

                    <!-- Listado de Fichas de Kinesiología Registradas -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Fichas de Kinesiología Registradas</h3>
                                </div>
                                <div class="card-body">
                                    {{-- Formulario de Filtros y Búsqueda para fichas --}}
                                    {{-- La acción del formulario apunta a la ruta de índice de Kinesiología --}}
                                    {{-- Ruta: responsable.kinesiologia.reportekine.index --}}
                                    <form id="filterFormFichasKine" action="{{ route('responsable.kinesiologia.reportekine.index') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <label for="search_fichas_kine" class="form-label">Buscar Ficha (Adulto Mayor):</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search_fichas_kine" name="search" placeholder="Buscar por CI o nombres del adulto mayor..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                                    {{-- Botón Restablecer --}}
                                                    <a href="{{ route('responsable.kinesiologia.reportekine.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                                </div>
                                                <small class="text-muted">Busca por CI o nombres del adulto mayor.</small>
                                            </div>
                                        </div>
                                    </form>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Cod. Kine</th>
                                                    <th>Adulto Mayor</th>
                                                    <th>CI</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Servicios Realizados</th>
                                                    <th>Turnos</th>
                                                    <th>Registrado Por</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($fichasKinesiologia as $fichaKine)
                                                    <tr>
                                                        <td>{{ $fichaKine->cod_kine }}</td>
                                                        <td>
                                                            <strong>{{ optional(optional($fichaKine->adulto)->persona)->nombres }}</strong>
                                                            {{ optional(optional($fichaKine->adulto)->persona)->primer_apellido }}
                                                            {{ optional(optional($fichaKine->adulto)->persona)->segundo_apellido }}
                                                        </td>
                                                        <td>{{ optional(optional($fichaKine->adulto)->persona)->ci }}</td>
                                                        <td>{{ optional($fichaKine->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                                        <td>
                                                            @php
                                                                $services = [];
                                                                if($fichaKine->entrenamiento_funcional) $services[] = 'EF';
                                                                if($fichaKine->gimnasio_maquina) $services[] = 'GM';
                                                                if($fichaKine->aquafit) $services[] = 'AQ';
                                                                if($fichaKine->hidroterapia) $services[] = 'HT';
                                                                echo empty($services) ? 'N/A' : implode(', ', $services);
                                                            @endphp
                                                        </td>
                                                        <td>
                                                            @php
                                                                $turns = [];
                                                                if($fichaKine->manana) $turns[] = 'M';
                                                                if($fichaKine->tarde) $turns[] = 'T';
                                                                echo empty($turns) ? 'N/A' : implode(', ', $turns);
                                                            @endphp
                                                        </td>
                                                        <td>{{ optional(optional($fichaKine->usuario)->persona)->nombres }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Botón para Ver Detalles --}}
                                                                {{-- Ruta: responsable.kinesiologia.reportekine.show --}}
                                                                <a href="{{ route('responsable.kinesiologia.reportekine.show', ['cod_kine' => $fichaKine->cod_kine]) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Ver Detalles">
                                                                    <i class="fe fe-eye"></i>
                                                                </a>
                                                                {{-- Botón para Editar Ficha (si se desea permitir editar desde el reporte, añadir la ruta aquí) --}}
                                                                {{-- Ejemplo: <a href="{{ route('responsable.kinesiologia.reportekine.edit', ['cod_kine' => $fichaKine->cod_kine]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Editar Ficha"><i class="fe fe-edit"></i></a> --}}

                                                                {{-- Botón para Eliminar Ficha (con SweetAlert2) --}}
                                                                {{-- Ruta: responsable.kinesiologia.reportekine.destroy --}}
                                                                <form action="{{ route('responsable.kinesiologia.reportekine.destroy', ['cod_kine' => $fichaKine->cod_kine]) }}" method="POST" style="display:inline-block;" class="form-delete-kine-report">
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
                                                        <td colspan="8" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No se encontraron fichas de kinesiología registradas.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <!-- Paginación para las fichas de kinesiología -->
                                        @if(method_exists($fichasKinesiologia, 'links') && $fichasKinesiologia->hasPages())
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $fichasKinesiologia->links() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

@endsection

{{-- Scripts específicos de la vista --}}
@push('scripts')
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Inicializar Tooltips de Bootstrap
            // Asegúrate de que Bootstrap JS esté cargado en tu layout principal para que esto funcione
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }

            // Manejo de SweetAlert2 para mensajes de sesión (éxito/error)
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true,
                    confirmButtonText: 'Cerrar'
                });
            @endif

            // Lógica para el botón de exportar a Excel (TODOS los registros)
            document.getElementById('exportarExcelGeneralBtn').addEventListener('click', function() {
                const form = document.getElementById('filterFormFichasKine'); // Obtener el formulario de filtro
                const formData = new FormData(form); // Crear un objeto FormData con los datos del formulario
                const queryString = new URLSearchParams(formData).toString(); // Convertir a string de query
                
                // Redirige a la ruta de exportación de Excel con los parámetros de filtro
                // Ruta: responsable.kinesiologia.reportekine.exportExcel
                window.location.href = {{ route('responsable.kinesiologia.reportekine.exportExcel') }}?${queryString};
            });

            // Manejo de SweetAlert2 para confirmaciones de eliminación
            document.querySelectorAll('.form-delete-kine-report').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Detener el envío del formulario
                    const form = this; // Referencia al formulario actual

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "¿Desea eliminar esta ficha de Kinesiología? ¡Esta acción no se puede deshacer!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Si el usuario confirma, enviar el formulario
                        }
                    });
                });
            });

        });
    </script>
@endpush