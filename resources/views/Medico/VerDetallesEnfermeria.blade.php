@extends('layouts.main')

@section('content')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Atención de Enfermería</title>
    {{-- Enlazar tus CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Medico/verDetallesEnfermeria.css') }}"> {{-- CSS específico con los colores unificados --}}
    {{-- Enlazar Feather Icons y jQuery (necesario para Bootstrap JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Asegúrate de que Bootstrap JS esté enlazado si no lo hace dashboard.css para los acordeones --}}
    {{-- Si estás usando Bootstrap 5, este bundle incluye Popper.js --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
</head>

<body>
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    {{-- Título de la página --}}
                    <div class="enfermeria-header">
                        <h1 class="page-title">Detalle de Atención de Enfermería</h1>
                    </div>

                    {{-- Tarjeta principal de detalles --}}
                    <div class="card enfermeria-card">
                        <div class="card-header bg-empresa-primary"> {{-- Usamos bg-empresa-primary aquí --}}
                            <h3 class="card-title text-white mb-0"> {{-- Título de la tarjeta, forzar texto blanco --}}
                                Ficha de Enfermería de: {{ optional($fichaEnfermeria->adulto->persona)->nombres }} {{ optional($fichaEnfermeria->adulto->persona)->primer_apellido }} {{ optional($fichaEnfermeria->adulto->persona)->segundo_apellido }}
                            </h3>
                            <div class="card-options">
                                <span class="badge bg-empresa-secondary"> {{-- Usamos bg-empresa-secondary sin text-white --}}
                                    <i class="fe fe-file-text"></i> Nº Ficha: {{ $fichaEnfermeria->cod_enf ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="enfermeria-detail-container">
                                <div class="accordion" id="enfermeriaDetailsAccordion">

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
                                        <div id="collapseDatosAdulto" class="accordion-collapse collapse show" aria-labelledby="headingDatosAdulto" data-bs-parent="#enfermeriaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Nombre Completo:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->nombres }} {{ optional($fichaEnfermeria->adulto->persona)->primer_apellido }} {{ optional($fichaEnfermeria->adulto->persona)->segundo_apellido }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Sexo:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->sexo ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Edad:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->edad ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">CI:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->ci ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Teléfono:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->telefono ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Domicilio:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->domicilio ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Zona/Comunidad:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria->adulto->persona)->zona_comunidad ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 2: Control de Signos Vitales --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingSignosVitales">
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSignosVitales" aria-expanded="false" aria-controls="collapseSignosVitales">
                                                    <div class="section-icon"><i data-feather="activity"></i></div>
                                                    <h3 class="section-title">Control de Signos Vitales</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseSignosVitales" class="accordion-collapse collapse" aria-labelledby="headingSignosVitales" data-bs-parent="#enfermeriaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Presión Arterial:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->presion_arterial ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Frecuencia Cardíaca:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->frecuencia_cardiaca ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Frecuencia Respiratoria:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->frecuencia_respiratoria ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Pulso:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->pulso ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Temperatura:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->temperatura ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Control Oximetría:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->control_oximetria ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 3: Atenciones de Enfermería --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingAtencionesEnfermeria">
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAtencionesEnfermeria" aria-expanded="false" aria-controls="collapseAtencionesEnfermeria">
                                                    <div class="section-icon"><i data-feather="heart"></i></div>
                                                    <h3 class="section-title">Atenciones de Enfermería</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseAtencionesEnfermeria" class="accordion-collapse collapse" aria-labelledby="headingAtencionesEnfermeria" data-bs-parent="#enfermeriaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Inyectables:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->inyectables ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Peso y Talla:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->peso_talla ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Orientación Alimentación:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->orientacion_alimentacion ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Lavado de Oídos:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->lavado_oidos ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Orientación Tratamiento:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->orientacion_tratamiento ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Curación:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->curacion ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Administración Medicamentos:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->adm_medicamentos ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Derivación:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->derivacion ?? 'N/A' }}</span>
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
                                        <div id="collapseRegistroInfo" class="accordion-collapse collapse" aria-labelledby="headingRegistroInfo" data-bs-parent="#enfermeriaDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Registrado por:</span>
                                                        <span class="detail-value">
                                                            {{ optional($fichaEnfermeria->usuario->persona)->nombres }}
                                                            {{ optional($fichaEnfermeria->usuario->persona)->primer_apellido }}
                                                            {{ optional($fichaEnfermeria->usuario->persona)->segundo_apellido }}
                                                            ({{ optional($fichaEnfermeria->usuario)->email }})
                                                            @if(!optional($fichaEnfermeria->usuario->persona)->nombres)
                                                                N/A
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Fecha de Registro:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->created_at ? \Carbon\Carbon::parse($fichaEnfermeria->created_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Última Actualización:</span>
                                                        <span class="detail-value">{{ optional($fichaEnfermeria)->updated_at ? \Carbon\Carbon::parse($fichaEnfermeria->updated_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- Fin acordeon principal --}}
                            </div> {{-- Fin enfermeria-detail-container --}}

                            <div class="enfermeria-actions">
                                <a href="{{ route('admin.enfermeria.index') }}" class="btn btn-empresa-secondary">
                                    <i class="fe fe-arrow-left"></i> Volver al listado
                                </a>
                                <div class="export-buttons">
                                    <a href="#" class="btn btn-empresa-primary">
                                        <i class="fe fe-printer"></i> Imprimir
                                    </a>
                                    <a href="#" class="btn btn-empresa-accent">
                                        <i class="fe fe-download"></i> Exportar PDF
                                    </a>
                                </div>
                            </div>
                        </div> {{-- Fin card-body --}}
                    </div> {{-- Fin card enfermeria-card --}}

                </div> {{-- Fin main-container --}}
            </div> {{-- Fin side-app --}}
        </div> {{-- Fin main-content --}}
    </div> {{-- Fin page-main --}}
</div> {{-- Fin page --}}

@endsection

{{-- Scripts específicos para la página --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        $('#enfermeriaDetailsAccordion').on('show.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });

        $('#enfermeriaDetailsAccordion').on('hide.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });
    });
</script>

</body>
</html>
