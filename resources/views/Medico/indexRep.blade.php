@extends('layouts.main')

{{-- Define el título de la página --}}
@section('title', 'Módulo Médico / Reportes de Atención de Enfermería')

{{-- Estilos específicos de la vista --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/indexHistoriaClinica.css') }}"> 
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')

                    <div class="page-header">
                        <h1 class="page-title">Módulo Médico / Reportes de Atención de Enfermería</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reportes de Enfermería</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Tarjetas de estadísticas -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                            <div class="card overflow-hidden sales-card bg-primary-gradient">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Adultos Mayores Registrados</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalAdultos ?? 0 }}</h4>
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
                            <div class="card overflow-hidden sales-card bg-info-gradient"> {{-- Color info para enfermería --}}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="card-text mb-0 text-white">Total Fichas de Enfermería Registradas</h6>
                                            <h4 class="mb-0 num-text text-white">{{ $totalFichasEnfermeria ?? 0 }}</h4>
                                        </div>
                                        <div class="col col-auto">
                                            <div class="counter-icon bg-gradient-info ms-auto box-shadow-info">
                                                <i class="fe fe-heart text-white mb-5"></i> {{-- Icono de corazón para enfermería --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones para Navegación entre Reportes -->
                    <div class="mb-3 d-flex justify-content-start gap-2">
                        {{-- Ruta CORRECTA según web.php: responsable.enfermeria.reportes_enfermeria.index --}}
                        <a href="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" class="btn btn-primary">
                            <i class="fe fe-heart"></i> Reportes de Atención de Enfermería
                        </a>
                        {{-- Ruta CORRECTA según web.php: responsable.enfermeria.reportes_historia_clinica.index --}}
                        <a href="{{ route('responsable.enfermeria.reportes_historia_clinica.index') }}" class="btn btn-secondary">
                            <i class="fe fe-file-text"></i> Reportes de Historia Clínica
                        </a>
                    </div>

                    <!-- Tabla de reportes de Atención de Enfermería -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Fichas de Atención de Enfermería</h3>
                                </div>
                                <div class="card-body">
                                    {{-- Formulario de Filtros y Búsqueda (gestionado por Laravel) --}}
                                    {{-- Ruta CORRECTA según web.php: responsable.enfermeria.reportes_enfermeria.index --}}
                                    <form id="filterForm" action="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" method="GET" class="form-filter mb-4">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-4">
                                                <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fecha_inicio ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fecha_fin ?? '' }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="search" class="form-label">Búsqueda General:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="search" name="search" placeholder="Buscar por CI, nombres, PA, Temp, Derivación..." value="{{ $search ?? '' }}">
                                                    <button type="submit" class="btn btn-primary"><i class="fe fe-search"></i> Buscar</button>
                                                    {{-- Ruta CORRECTA según web.php: responsable.enfermeria.reportes_enfermeria.index --}}
                                                    <a href="{{ route('responsable.enfermeria.reportes_enfermeria.index') }}" class="btn btn-outline-secondary"><i class="fe fe-x"></i> Restablecer</a>
                                                </div>
                                                <small class="text-muted">Búsqueda por CI, nombres, presión arterial, temperatura, o derivación.</small>
                                            </div>
                                        </div>
                                    </form>

                                    {{-- Botones para Imprimir y Exportar Excel --}}
                                    <div class="mb-3 d-flex justify-content-end gap-2">
                                        {{-- Ruta CORRECTA según web.php: responsable.enfermeria.reportes_enfermeria.exportar_excel --}}
                                        <button type="button" class="btn btn-success btn-sm" id="exportarExcelBtn">
                                            <i class="fe fe-download"></i> Exportar a Excel
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="reportesTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID Atención</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Adulto Mayor</th>
                                                    <th>CI Adulto</th>
                                                    <th>Presión Arterial</th>
                                                    <th>Temperatura</th>
                                                    <th>Derivación (Extracto)</th>
                                                    <th>Registrado Por</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reportes as $reporte)
                                                    <tr>
                                                        <td>{{ $reporte->cod_enf }}</td>
                                                        <td>{{ optional($reporte->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                                        <td>
                                                            <strong>{{ optional($reporte->adulto->persona)->nombres }} {{ optional($reporte->adulto->persona)->primer_apellido }}</strong>
                                                            @if(optional($reporte->adulto->persona)->segundo_apellido)
                                                                {{ optional($reporte->adulto->persona)->segundo_apellido }}
                                                            @endif
                                                        </td>
                                                        <td>{{ optional($reporte->adulto->persona)->ci ?? 'N/A' }}</td>
                                                        <td>{{ $reporte->presion_arterial ?? 'N/A' }}</td>
                                                        <td>{{ $reporte->temperatura ?? 'N/A' }}</td>
                                                        <td>{{ $reporte->derivacion ? Str::limit($reporte->derivacion, 50, '...') : 'N/A' }}</td>
                                                        <td>{{ optional($reporte->usuario->persona)->nombres }} {{ optional($reporte->usuario->persona)->primer_apellido }}</td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Ruta CORRECTA según web.php: responsable.enfermeria.reportes_enfermeria.destroy_atencion_enfermeria --}}
                                                                <form action="{{ route('responsable.enfermeria.reportes_enfermeria.destroy_atencion_enfermeria', ['cod_enf' => $reporte->cod_enf]) }}" method="POST" style="display:inline-block;" class="form-delete-report">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No se encontraron fichas de atención de enfermería con los filtros aplicados.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if(method_exists($reportes, 'links') && $reportes->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $reportes->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

@endsection

{{-- Scripts específicos de la vista --}}
@push('scripts')
    {{-- Carga de jQuery (si es necesario para algún otro script en dashboard.css o generales, o DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Feather Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Inicializar Tooltips de Bootstrap (asumiendo que Bootstrap JS está en layouts.main)
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
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

            // Lógica para el botón de exportar a Excel
            document.getElementById('exportarExcelBtn').addEventListener('click', function() {
                const form = document.getElementById('filterForm');
                const formData = new FormData(form);
                const queryString = new URLSearchParams(formData).toString();
                // Ruta CORRECTA según web.php: responsable.enfermeria.reportes_enfermeria.exportar_excel
                window.location.href = `{{ route('responsable.enfermeria.reportes_enfermeria.exportar_excel') }}?${queryString}`;
            });

            // Manejo de SweetAlert2 para confirmaciones de eliminación
            document.querySelectorAll('.form-delete-report').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Detener el envío del formulario
                    const form = this; // Referencia al formulario actual

                    Swal.fire({
                        title: '¿Está seguro?',
                        text: "¿Desea eliminar esta atención de enfermería? ¡Esta acción no se puede deshacer!",
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