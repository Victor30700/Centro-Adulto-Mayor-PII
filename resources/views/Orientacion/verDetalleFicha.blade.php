@extends('layouts.main')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orientación</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Orientacion/verDetalleFicha.css') }}">
    {{-- Asegúrate de que Feather Icons esté enlazado antes de tu script --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- Agregamos un script para jQuery si Bootstrap lo necesita, como en tu ejemplo --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- Si usas Bootstrap 5, asegúrate de que su JS también esté enlazado si no lo hace dashboard.css --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
</head>

<body>
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="orientacion-header">
                        <h1 class="page-title">Detalle de Orientación</h1>
                    </div>

                    <div class="card orientacion-card">
                        <div class="card-header bg-empresa-primary text-white">
                            <h3 class="card-title text-white mb-0">
                                Ficha de Orientación de: {{ optional($orientacion->adulto->persona)->nombres }} {{ optional($orientacion->adulto->persona)->primer_apellido }} {{ optional($orientacion->adulto->persona)->segundo_apellido }}
                            </h3>
                            <div class="card-options">
                                <span class="badge bg-empresa-secondary">
                                    <i class="fe fe-file-text"></i> Nº Ficha: {{ $orientacion->cod_or ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="orientacion-detail-container">
                                <div class="accordion" id="orientacionDetailsAccordion">

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
                                        <div id="collapseDatosAdulto" class="accordion-collapse collapse show" aria-labelledby="headingDatosAdulto" data-bs-parent="#orientacionDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Número de Caso:</span>
                                                        <span class="detail-value">{{ optional($orientacion->adulto)->nro_caso ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Nombre Completo:</span>
                                                        <span class="detail-value">{{ optional($orientacion->adulto->persona)->nombres }} {{ optional($orientacion->adulto->persona)->primer_apellido }} {{ optional($orientacion->adulto->persona)->segundo_apellido }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Edad:</span>
                                                        <span class="detail-value">{{ optional($orientacion->adulto->persona)->edad ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Domicilio/Comunidad:</span>
                                                        <span class="detail-value">Domicilio: {{ optional($orientacion->adulto->persona)->domicilio ?? 'N/A' }}</span>
                                                        <span class="detail-value">Zona Comunidad: {{ optional($orientacion->adulto->persona)->zona_comunidad ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Teléfono:</span>
                                                        <span class="detail-value">{{ optional($orientacion->adulto->persona)->telefono ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sección 2: Detalles de la Ficha de Orientación --}}
                                    <div class="accordion-item detail-section">
                                        <div class="accordion-header" id="headingDetallesOrientacion">
                                            <h2 class="mb-0">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetallesOrientacion" aria-expanded="false" aria-controls="collapseDetallesOrientacion">
                                                    <div class="section-icon"><i data-feather="clipboard"></i></div>
                                                    <h3 class="section-title">Detalles de la Ficha de Orientación</h3>
                                                    <i data-feather="chevron-down" class="accordion-icon"></i>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseDetallesOrientacion" class="accordion-collapse collapse" aria-labelledby="headingDetallesOrientacion" data-bs-parent="#orientacionDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="detail-group">
                                                    <div class="detail-row">
                                                        <span class="detail-label">Fecha de Ingreso:</span>
                                                        <span class="detail-value">{{ optional($orientacion)->fecha_ingreso ? \Carbon\Carbon::parse($orientacion->fecha_ingreso)->format('d/m/Y') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Tipo de Orientación:</span>
                                                        <span class="detail-value">
                                                            @php
                                                                $tipoOrientacion = optional($orientacion)->tipo_orientacion;
                                                                if ($tipoOrientacion == 'psicologica') echo 'PSICOLÓGICA';
                                                                else if ($tipoOrientacion == 'social') echo 'SOCIAL';
                                                                else if ($tipoOrientacion == 'legal') echo 'LEGAL';
                                                                else echo 'N/A';
                                                            @endphp
                                                        </span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Motivos de Orientación:</span>
                                                        <span class="detail-value">{{ optional($orientacion)->motivo_orientacion ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Resultados Obtenidos:</span>
                                                        <span class="detail-value">{{ optional($orientacion)->resultado_obtenido ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        <span class="detail-label">Registrado por (ID Usuario):</span>
                                                        <span class="detail-value">{{ optional($orientacion)->id_usuario ?? 'N/A' }}</span>
                                                        {{-- Si tienes la relación con el modelo User y quieres mostrar el nombre: --}}
                                                        {{-- <span class="detail-value">{{ optional($orientacion->usuario)->name ?? 'N/A' }}</span> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- Fin acordeon principal --}}
                            </div> {{-- Fin caso-detail-container --}}

                            <div class="orientacion-actions">
                                <a href="{{ route('admin.orientacion.index') }}" class="btn btn-empresa-secondary">
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
                    </div> {{-- Fin card proteccion-card --}}

                </div> {{-- Fin main-container --}}
            </div> {{-- Fin side-app --}}
        </div> {{-- Fin main-content --}}
    </div> {{-- Fin page-main --}}
</div> {{-- Fin page --}}

@endsection

{{-- Modal para confirmación de eliminación (si es necesario) --}}

{{-- Modal para detalles (se mantiene si se usa para otros fines, pero para esta vista no es estrictamente necesario) --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-empresa-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Detalles del Registro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Contenido se carga dinámicamente --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-empresa-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Feather Icons al cargar la página
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Script para manejar los íconos de los acordeones al abrir/cerrar
        $('#orientacionDetailsAccordion').on('show.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-up');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });

        $('#orientacionDetailsAccordion').on('hide.bs.collapse', function (e) {
            const button = $(e.target).prev('.accordion-header').find('.accordion-button');
            const icon = button.find('.accordion-icon');
            icon.attr('data-feather', 'chevron-down');
            feather.replace({ target: icon[0] }); // Solo reemplazar el icono específico
        });

        // Script para cargar dinámicamente el contenido del modal y reemplazar iconos
        // (Este script es para un modal genérico, no para esta vista de detalles directamente)
        var detailModal = document.getElementById('detailModal');
        if (detailModal) {
            detailModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Botón que activó el modal
                var title = button.getAttribute('data-title');
                var content = JSON.parse(button.getAttribute('data-content'));

                var modalTitle = detailModal.querySelector('.modal-title');
                var modalBody = detailModal.querySelector('.modal-body');

                modalTitle.textContent = title;

                // Construir el contenido del modal
                let htmlContent = '<div class="detail-group">';
                for (const [label, value] of Object.entries(content)) {
                    htmlContent += `
                        <div class="detail-row">
                            <span class="detail-label">${label}:</span>
                            <span class="detail-value">${value}</span>
                        </div>
                    `;
                }
                htmlContent += '</div>';
                modalBody.innerHTML = htmlContent;

                // Importante: Reemplazar iconos dentro del modal después de que el contenido se ha insertado
                if (typeof feather !== 'undefined') {
                    feather.replace({ parent: modalBody }); // Reemplazar solo los iconos dentro del modalBody
                }
            });
        }
    });
</script>
@endpush
</body>
</html>
