{{-- Proteccion/partials/seguimientoCaso.blade.php --}}
<div class="section-content">
    @if($adulto->seguimientos->isNotEmpty())
        <ul class="item-list">
            @foreach($adulto->seguimientos->sortByDesc('fecha') as $seguimiento)
                <li>
                    <div class="sub-section-title">Seguimiento Nro: {{ $seguimiento->nro }}</div>
                    <div class="detail-row">
                        <span class="detail-label">Fecha:</span> <span class="detail-value">{{ optional($seguimiento->fecha)->format('d/m/Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Acción Realizada:</span> <span class="detail-value">{{ $seguimiento->accion_realizada ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Resultado Obtenido:</span> <span class="detail-value">{{ $seguimiento->resultado_obtenido ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Registrado por:</span> <span class="detail-value">{{ optional(optional($seguimiento->usuario)->persona)->nombres }} {{ optional(optional($seguimiento->usuario)->persona)->primer_apellido }} {{ optional(optional($seguimiento->usuario)->persona)->segundo_apellido }}</span>
                    </div>

                    {{-- Detalles de la intervención (si existe) - Ahora con un toggle --}}
                    @if(optional($seguimiento->intervencion)->exists)
                        <button type="button" class="btn btn-view-details mt-3 toggle-intervencion-btn" data-bs-toggle="collapse" data-bs-target="#collapseIntervencion-{{ $seguimiento->nro }}">
                            <i data-feather="plus-circle" class="toggle-icon"></i> Ver Detalles de Intervención
                        </button>
                        <div class="intervencion-details-collapsible collapse mt-3" id="collapseIntervencion-{{ $seguimiento->nro }}">
                            <div class="sub-section-title">Detalle de Intervención</div>
                            <div class="detail-row">
                                <span class="detail-label">Fecha Intervención:</span>
                                <span class="detail-value">
                                    @php
                                        $fechaIntervencion = optional($seguimiento->intervencion)->fecha_intervencion;
                                        $formattedDate = 'N/A'; // Valor por defecto si no hay fecha o es inválida

                                        if ($fechaIntervencion) {
                                            try {
                                                // Intenta convertir a Carbon si no lo es ya
                                                if (!($fechaIntervencion instanceof \Carbon\Carbon)) {
                                                    $fechaIntervencion = \Carbon\Carbon::parse($fechaIntervencion);
                                                }
                                                $formattedDate = $fechaIntervencion->format('d/m/Y');
                                            } catch (\Exception $e) {
                                                // En caso de que la cadena de fecha sea inválida
                                                // error_log("Error parsing fecha_intervencion: " . $e->getMessage()); // Solo para depuración
                                                $formattedDate = 'Fecha inválida';
                                            }
                                        }
                                    @endphp
                                    {{ $formattedDate }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Resuelto (Descripción):</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->resuelto_descripcion ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">No Resultado (Motivo):</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->no_resultado ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Derivado a Institución:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->derivacion_institucion ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Seguimiento Legal:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->der_seguimiento_legal ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Seguimiento Psicológico:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->der_seguimiento_psi ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Resuelto Externo:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->der_resuelto_externo ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">No Resuelto Externo:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->der_noresuelto_externo ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Abandono Víctima:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->abandono_victima ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Conciliación JIO:</span> <span class="detail-value">{{ optional($seguimiento->intervencion)->resuelto_conciliacion_jio ?? 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="no-data-message" style="margin-top: 15px;">No hay datos de intervención para este seguimiento.</div>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <div class="no-data-message">No se han registrado seguimientos del caso.</div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-intervencion-btn').forEach(button => {
            // Adjuntar listener directamente al botón de Bootstrap
            button.addEventListener('click', function() {
                const icon = this.querySelector('.toggle-icon');
                // Bootstrap 5 ya maneja el toggle, solo necesitamos el icono
                // Comprobamos si el target está actualmente visible
                const targetId = this.getAttribute('data-bs-target');
                const targetElement = document.querySelector(targetId);

                // Esperar un breve momento para que Bootstrap haga su trabajo de toggle
                setTimeout(() => {
                    if ($(targetElement).hasClass('show')) {
                        icon.setAttribute('data-feather', 'minus-circle');
                        this.innerHTML = '<i data-feather="minus-circle" class="toggle-icon"></i> Ocultar Detalles de Intervención';
                    } else {
                        icon.setAttribute('data-feather', 'plus-circle');
                        this.innerHTML = '<i data-feather="plus-circle" class="toggle-icon"></i> Ver Detalles de Intervención';
                    }
                    if (typeof feather !== 'undefined') {
                        feather.replace({ target: icon }); // Solo reemplazar el icono específico
                    }
                }, 100); // Pequeño retraso para que Bootstrap actualice la clase 'show'
            });

            // Llamar a feather.replace() para los botones que ya están en el DOM al cargar el partial
            if (typeof feather !== 'undefined') {
                feather.replace({ target: button.querySelector('.toggle-icon') });
            }
        });
    });
</script>