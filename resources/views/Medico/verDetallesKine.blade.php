{{-- resources/views/Medico/verDetallesKine.blade.php --}}
@extends('layouts.main')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Ficha de Kinesiología - {{ optional($kinesiologia->adulto->persona)->nombres }} {{ optional($kinesiologia->adulto->persona)->primer_apellido }}</title>
    {{-- Enlazar tus CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> {{-- Asumiendo que dashboard.css es tu CSS global --}}
    <link rel="stylesheet" href="{{ asset('css/Medico/verDetallesKine.css') }}"> {{-- CSS específico para esta vista --}}
    
    {{-- Enlazar Feather Icons y jQuery (necesario para Bootstrap JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Asegúrate de que Bootstrap JS esté enlazado si no lo hace dashboard.css para los acordeones --}}
    {{-- Si estás usando Bootstrap 5, este bundle incluye Popper.js --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>


@section('content')
<body>

                    {{-- Título de la página --}}
                    <div class="enfermeria-header"> {{-- Clase genérica 'enfermeria-header' usada para consistencia --}}
                        <h1 class="page-title">Detalle de Atención de Kinesiología</h1>
                    </div>

                    {{-- Tarjeta principal de detalles --}}
                    <div class="card enfermeria-card"> {{-- Clase genérica 'enfermeria-card' usada para consistencia --}}
                        <div class="card-header bg-empresa-primary">
                            <h3 class="card-title text-white mb-0">
                                Ficha de Kinesiología de: {{ optional($kinesiologia->adulto->persona)->nombres }} {{ optional($kinesiologia->adulto->persona)->primer_apellido }} {{ optional($kinesiologia->adulto->persona)->segundo_apellido }}
                            </h3>
                            <div class="card-options">
                                <span class="badge bg-empresa-secondary">
                                    <i data-feather="file-text"></i> Nº Ficha: {{ $kinesiologia->cod_kine ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="enfermeria-detail-container">
                                <div class="accordion" id="kinesiologiaDetailsAccordion"> {{-- ID de acordeón actualizado --}}

                                    {{-- Sección 1: Datos Personales del Adulto Mayor --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingDatosAdulto">
                                            <h2 class="mb-0">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDatosAdulto" aria-expanded="true" aria-controls="collapseDatosAdulto">
                                                    <div class="section-icon"><i data-feather="user"></i></div>
                                                    <h3 class="section-title">Datos Personales del Adulto Mayor</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseDatosAdulto" class="accordion-collapse collapse show" aria-labelledby="headingDatosAdulto" data-bs-parent="#kinesiologiaDetailsAccordion"> {{-- ID de acordeón actualizado --}}
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Nombre Completo:</span>
                                                        <span class="detail-value">{{ optional($kinesiologia->adulto->persona)->nombres }} {{ optional($kinesiologia->adulto->persona)->primer_apellido }} {{ optional($kinesiologia->adulto->persona)->segundo_apellido }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Sexo:</span>
                                                        <span class="detail-value">{{ optional($kinesiologia->adulto->persona)->sexo == 'F' ? 'Femenino' : (optional($kinesiologia->adulto->persona)->sexo == 'M' ? 'Masculino' : 'N/A') }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Lugar de Nacimiento:</span>
                                                        <span class="detail-value">{{ optional($kinesiologia->historiaClinica)->lugar_nacimiento ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Barrio:</span>
                                                        <span class="detail-value">{{ optional($kinesiologia->adulto->persona)->zona_comunidad ?? 'N/A' }} / {{ optional($kinesiologia->adulto->persona)->domicilio ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 2: Detalles de la Atención de Kinesiología --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingAtencionKine"> {{-- ID actualizado --}}
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAtencionKine" aria-expanded="false" aria-controls="collapseAtencionKine"> {{-- ID actualizado --}}
                                                    <div class="section-icon"><i data-feather="activity"></i></div> {{-- Icono para Kinesiología --}}
                                                    <h3 class="section-title">Detalles de la Atención de Kinesiología</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseAtencionKine" class="accordion-collapse collapse" aria-labelledby="headingAtencionKine" data-bs-parent="#kinesiologiaDetailsAccordion"> {{-- ID de acordeón actualizado --}}
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Servicios Realizados:</span>
                                                        <span class="detail-value">
                                                            @if($kinesiologia->entrenamiento_funcional) <span class="badge bg-primary me-1">Entrenamiento Funcional</span> @endif
                                                            @if($kinesiologia->gimnasio_maquina) <span class="badge bg-primary me-1">Gimnasio Máquinas</span> @endif
                                                            @if($kinesiologia->aquafit) <span class="badge bg-primary me-1">Aquafit</span> @endif
                                                            @if($kinesiologia->hidroterapia) <span class="badge bg-primary me-1">Hidroterapia</span> @endif
                                                            @if(!$kinesiologia->entrenamiento_funcional && !$kinesiologia->gimnasio_maquina && !$kinesiologia->aquafit && !$kinesiologia->hidroterapia)
                                                                <span class="text-muted">Ningún servicio marcado</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Turnos:</span>
                                                        <span class="detail-value">
                                                            @if($kinesiologia->manana) <span class="badge bg-info me-1">Mañana</span> @endif
                                                            @if($kinesiologia->tarde) <span class="badge bg-info me-1">Tarde</span> @endif
                                                            @if(!$kinesiologia->manana && !$kinesiologia->tarde)
                                                                <span class="text-muted">Ningún turno marcado</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 3: Información de Registro --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingRegistroInfo">
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRegistroInfo" aria-expanded="false" aria-controls="collapseRegistroInfo">
                                                    <div class="section-icon"><i data-feather="info"></i></div>
                                                    <h3 class="section-title">Información de Registro</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseRegistroInfo" class="accordion-collapse collapse" aria-labelledby="headingRegistroInfo" data-bs-parent="#kinesiologiaDetailsAccordion"> {{-- ID de acordeón actualizado --}}
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Registrado por:</span>
                                                        <span class="detail-value">
                                                            {{ optional($kinesiologia->usuario->persona)->nombres }}
                                                            {{ optional($kinesiologia->usuario->persona)->primer_apellido }}
                                                            {{ optional($kinesiologia->usuario->persona)->segundo_apellido }}
                                                            ({{ optional($kinesiologia->usuario)->email }})
                                                            @if(!optional($kinesiologia->usuario->persona)->nombres)
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Fecha de Registro:</span>
                                                        <span class="detail-value">{{ optional($kinesiologia)->created_at ? \Carbon\Carbon::parse($kinesiologia->created_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Última Actualización:</span>
                                                        <span class="detail-value">{{ optional($kinesiologia)->updated_at ? \Carbon\Carbon::parse($kinesiologia->updated_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- Fin acordeon principal --}}
                            </div> {{-- Fin enfermeria-detail-container --}}

                            <div class="enfermeria-actions">
                                <a href="{{ route('responsable.kinesiologia.fisiokine.indexKine') }}" class="btn btn-empresa-secondary"> {{-- Volver al Reporte de Kinesiología --}}
                                    <i data-feather="arrow-left"></i> Volver al Listado
                                </a>
                        </div> {{-- Fin card-body --}}
                    </div> {{-- Fin card enfermeria-card --}}

     
@endsection

{{-- Scripts específicos para esta vista --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        // Usamos jQuery para la compatibilidad con Bootstrap JS
        $('#kinesiologiaDetailsAccordion').on('show.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });

        $('#kinesiologiaDetailsAccordion').on('hide.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });
    });
</script>
@endpush

</body>
</html>
