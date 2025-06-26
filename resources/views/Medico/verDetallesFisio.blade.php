@extends('layouts.main')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Fichas de Fisioterapia</title>
    {{-- Enlazar tus CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/verDetallesFisio.css') }}"> {{-- CSS específico --}}
    {{-- Enlazar Feather Icons y jQuery (necesario para Bootstrap JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Asegúrate de que Bootstrap JS esté enlazado --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

@section('content')

<body>
    {{-- Título de la página --}}
    <div class="fisioterapia-header">
        <h1 class="page-title">Historial de Fichas de Fisioterapia</h1>
    </div>

    {{-- Tarjeta principal de detalles --}}
    <div class="card fisioterapia-card">
        <div class="card-header bg-empresa-primary">
            <h3 class="card-title text-white mb-0">
                Adulto Mayor: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
            </h3>
            <div class="card-options">
                <span class="badge bg-empresa-secondary">
                    <i class="fe fe-user"></i> CI: {{ optional($adulto->persona)->ci ?? 'N/A' }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @forelse($fichasFisioterapia as $index => $fichaFisioterapia)
                <div class="accordion mb-3" id="fisioterapiaFichaAccordion-{{ $fichaFisioterapia->cod_fisio }}">
                    <div class="accordion-item detail-section">
                        <div class="accordion-header" id="headingFicha-{{ $fichaFisioterapia->cod_fisio }}">
                            <h2 class="mb-0">
                                <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFicha-{{ $fichaFisioterapia->cod_fisio }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapseFicha-{{ $fichaFisioterapia->cod_fisio }}">
                                    <div class="section-icon"><i data-feather="file-text"></i></div>
                                    <h3 class="section-title">Ficha N°: {{ $fichaFisioterapia->cod_fisio ?? 'N/A' }} (Fecha: {{ optional($fichaFisioterapia->created_at)->format('d/m/Y H:i') ?? 'N/A' }})</h3>
                                    <i data-feather="{{ $index == 0 ? 'chevron-up' : 'chevron-down' }}" class="accordion-icon"></i>
                                </button>
                            </h2>
                        </div>
                        <div id="collapseFicha-{{ $fichaFisioterapia->cod_fisio }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="headingFicha-{{ $fichaFisioterapia->cod_fisio }}" data-bs-parent="#fisioterapiaFichaAccordion-{{ $fichaFisioterapia->cod_fisio }}">
                            <div class="accordion-body">
                                <div class="sub-detail-group mt-3">
                                    <h4>Detalles de la Ficha:</h4>
                                    <div class="detail-group">
                                        <div class="detail-row">
                                            <span class="detail-label">Registrado por:</span>
                                            <span class="detail-value">
                                                {{ optional($fichaFisioterapia->usuario->persona)->nombres }}
                                                {{ optional($fichaFisioterapia->usuario->persona)->primer_apellido }}
                                                {{ optional($fichaFisioterapia->usuario->persona)->segundo_apellido }}
                                                ({{ optional($fichaFisioterapia->usuario)->email }})
                                                @if(!optional($fichaFisioterapia->usuario->persona)->nombres)
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Fecha de Registro:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->created_at ? \Carbon\Carbon::parse($fichaFisioterapia->created_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Última Actualización:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->updated_at ? \Carbon\Carbon::parse($fichaFisioterapia->updated_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Historia Clínica Asociada:</span>
                                            <span class="detail-value">
                                                @if(optional($fichaFisioterapia)->historiaClinica)
                                                    N° {{ $fichaFisioterapia->historiaClinica->id_historia }} (Fecha: {{ optional($fichaFisioterapia->historiaClinica->created_at)->format('d/m/Y') }})
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Número de Emergencia:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->num_emergencia ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Enfermedades Actuales:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->enfermedades_actuales ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Alergias:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->alergias ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Fecha de Programación:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->fecha_programacion ? \Carbon\Carbon::parse($fichaFisioterapia->fecha_programacion)->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Motivo de Consulta:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->motivo_consulta ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Solicitud de Atención:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->solicitud_atencion ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Equipos Utilizados:</span>
                                            <span class="detail-value">{{ optional($fichaFisioterapia)->equipos ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="ficha-actions mt-4 text-end">
                                    <a href="{{ route('responsable.fisioterapia.fisiokine.editFisio', ['cod_fisio' => $fichaFisioterapia->cod_fisio]) }}" class="btn btn-warning btn-sm" title="Editar esta ficha">
                                        <i class="fe fe-edit"></i> Editar Ficha
                                    </a>
                                    <form action="{{ route('responsable.fisioterapia.fisiokine.destroyFisio', ['cod_fisio' => $fichaFisioterapia->cod_fisio]) }}" method="POST" class="d-inline form-delete-fisio">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar esta ficha">
                                            <i class="fe fe-trash-2"></i> Eliminar Ficha
                                        </button>
                                    </form>
                                </div>

                            </div> {{-- Fin accordion-body --}}
                        </div> {{-- Fin accordion-collapse --}}
                    </div> {{-- Fin accordion-item --}}
                </div> {{-- Fin acordeon por cada ficha --}}
            @empty
                <div class="text-center text-muted">
                    <i class="fe fe-inbox"></i>
                    <br>
                    No hay fichas de fisioterapia registradas para este adulto mayor.
                </div>
            @endforelse

            <div class="fisioterapia-actions mt-4 d-flex justify-content-between">
                <a href="{{ route('responsable.fisioterapia.fisiokine.indexFisio') }}" class="btn btn-empresa-secondary" style="background-color: gray; color:white;">
                    <i class="fe fe-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div> {{-- Fin card-body --}}
    </div> {{-- Fin card fisioterapia-card --}}

@endsection

{{-- Scripts específicos para la página --}}
@push('scripts')
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        $(document).on('show.bs.collapse', '.accordion-collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ target: icon[0] });
        });

        $(document).on('hide.bs.collapse', '.accordion-collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ target: icon[0] });
        });

        // Confirmación para eliminar ficha de fisioterapia
        document.querySelectorAll('.form-delete-fisio').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevenir el envío inmediato del formulario
                
                Swal.fire({
                    title: '¿Está seguro?',
                    text: "Se eliminará esta ficha de fisioterapia. ¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Enviar el formulario si el usuario confirma
                    }
                });
            });
        });
    });
</script>
</body>
</html>