@extends('layouts.main')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Ficha de Fisioterapia - {{ optional($fisioterapia->adulto->persona)->nombres }} {{ optional($fisioterapia->adulto->persona)->primer_apellido }}</title>
    {{-- Enlazar tus CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}"> {{-- Asumiendo que dashboard.css es tu CSS global --}}
    <link rel="stylesheet" href="{{ asset('css/Medico/verDetallesFisio.css') }}"> {{-- CSS específico para esta vista --}}
    
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
                        <h1 class="page-title">Detalle de Atención de Fisioterapia</h1>
                    </div>

                    {{-- Tarjeta principal de detalles --}}
                    <div class="card enfermeria-card"> {{-- Clase genérica 'enfermeria-card' usada para consistencia --}}
                        <div class="card-header bg-empresa-primary">
                            <h3 class="card-title text-white mb-0">
                                Ficha de Fisioterapia de: {{ optional($fisioterapia->adulto->persona)->nombres }} {{ optional($fisioterapia->adulto->persona)->primer_apellido }} {{ optional($fisioterapia->adulto->persona)->segundo_apellido }}
                            </h3>
                            <div class="card-options">
                                <span class="badge bg-empresa-secondary">
                                    <i data-feather="file-text"></i> Nº Ficha: {{ $fisioterapia->cod_fisio ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="enfermeria-detail-container">
                                <div class="accordion" id="fisioterapiaDetailsAccordion">

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
                                        <div id="collapseDatosAdulto" class="accordion-collapse collapse show" aria-labelledby="headingDatosAdulto" data-bs-parent="#fisioterapiaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Nombre Completo:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona)->nombres }} {{ optional($fisioterapia->adulto->persona)->primer_apellido }} {{ optional($fisioterapia->adulto->persona)->segundo_apellido }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Sexo:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona)->sexo == 'F' ? 'Femenino' : (optional($fisioterapia->adulto->persona)->sexo == 'M' ? 'Masculino' : 'N/A') }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Edad:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona->fecha_nacimiento) ? \Carbon\Carbon::parse($fisioterapia->adulto->persona->fecha_nacimiento)->age . ' años' : 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">CI:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona)->ci ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Teléfono:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona)->telefono ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Domicilio:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona)->domicilio ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Zona/Comunidad:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->adulto->persona)->zona_comunidad ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Vive con:</span>
                                                        <span class="detail-value">{{ $fisioterapia->adulto->vive_con ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Estado Civil:</span>
                                                        <span class="detail-value">{{ ucfirst(optional($fisioterapia->adulto->persona)->estado_civil ?? 'N/A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 2: Detalles de la Atención de Fisioterapia --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingAtencionFisio">
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAtencionFisio" aria-expanded="false" aria-controls="collapseAtencionFisio">
                                                    <div class="section-icon"><i data-feather="heart"></i></div> {{-- Icono cambiado a "heart" o "activity" --}}
                                                    <h3 class="section-title">Detalles de la Atención de Fisioterapia</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseAtencionFisio" class="accordion-collapse collapse" aria-labelledby="headingAtencionFisio" data-bs-parent="#fisioterapiaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Fecha de Programación:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->fecha_programacion)->format('d/m/Y') ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Motivo de Consulta:</span>
                                                        <span class="detail-value">{{ $fisioterapia->motivo_consulta ?? 'No especificado' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Solicitud de Atención:</span>
                                                        <span class="detail-value">{{ $fisioterapia->solicitud_atencion ?? 'No especificada' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Equipos Utilizados:</span>
                                                        <span class="detail-value">{{ $fisioterapia->equipos ?? 'No especificado' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 3: Historial de Salud (Enfermedades y Alergias) --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingHistorialSalud">
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistorialSalud" aria-expanded="false" aria-controls="collapseHistorialSalud">
                                                    <div class="section-icon"><i data-feather="activity"></i></div> {{-- Icono cambiado a "activity" o "stethoscope" --}}
                                                    <h3 class="section-title">Historial de Salud del Adulto Mayor</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseHistorialSalud" class="accordion-collapse collapse" aria-labelledby="headingHistorialSalud" data-bs-parent="#fisioterapiaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Enfermedades Actuales:</span>
                                                        <span class="detail-value">{{ $fisioterapia->enfermedades_actuales ?? 'No refiere' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Alergias:</span>
                                                        <span class="detail-value">{{ $fisioterapia->alergias ?? 'No refiere' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Número de Emergencia:</span>
                                                        <span class="detail-value">{{ $fisioterapia->num_emergencia ?? 'No especificado' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 4: Información de Registro --}}
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
                                        <div id="collapseRegistroInfo" class="accordion-collapse collapse" aria-labelledby="headingRegistroInfo" data-bs-parent="#fisioterapiaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Registrado por:</span>
                                                        <span class="detail-value">
                                                            {{ optional($fisioterapia->usuario->persona)->nombres }}
                                                            {{ optional($fisioterapia->usuario->persona)->primer_apellido }}
                                                            {{ optional($fisioterapia->usuario->persona)->segundo_apellido }}
                                                            ({{ optional($fisioterapia->usuario)->email }})
                                                            @if(!optional($fisioterapia->usuario->persona)->nombres)
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">CI del Registrador:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->usuario)->ci ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Rol del Registrador:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia->usuario)->getRoleNameAttribute() ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Fecha de Registro:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia)->created_at ? \Carbon\Carbon::parse($fisioterapia->created_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Última Actualización:</span>
                                                        <span class="detail-value">{{ optional($fisioterapia)->updated_at ? \Carbon\Carbon::parse($fisioterapia->updated_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- Fin acordeon principal --}}
                            </div> {{-- Fin enfermeria-detail-container --}}

                            <div class="enfermeria-actions">
                                <a href="{{ route('responsable.fisioterapia.fisiokine.indexFisio') }}" class="btn btn-empresa-secondary">
                                    <i data-feather="arrow-left"></i> Volver al listado
                                </a>
                                <div class="export-buttons">
                                    {{-- Botón de Imprimir, se necesitaría una ruta y lógica para generar el documento de impresión --}}
                                    <a href="#" class="btn btn-empresa-primary" onclick="window.print(); return false;">
                                        <i data-feather="printer"></i> Imprimir
                                    </a>
                                    {{-- Placeholder para exportar a PDF, se necesitaría un controlador y librería para esto --}}
                                    <a href="#" class="btn btn-empresa-accent" onclick="alert('Funcionalidad de exportar a PDF aún no implementada.')">
                                        <i data-feather="download"></i> Exportar PDF
                                    </a>
                                </div>
                            </div>
                        </div> {{-- Fin card-body --}}
                    </div> {{-- Fin card enfermeria-card --}}

@endsection

@push('scripts')
{{-- Scripts específicos para esta vista --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        // Usamos jQuery dado que el ejemplo de enfermeria lo usa
        $('#fisioterapiaDetailsAccordion').on('show.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });

        $('#fisioterapiaDetailsAccordion').on('hide.bs.collapse', function (e) {
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