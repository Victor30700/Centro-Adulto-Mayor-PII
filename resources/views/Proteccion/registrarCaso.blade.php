@php
    $modoEdicion = $modoEdicion ?? false; // Asegura que $modoEdicion esté definido
    $idAdulto = $adulto->id_adulto ?? null; // Obtener id_adulto si está disponible
    $activeTab = $activeTab ?? (old('active_tab', session('active_tab', 'actividad'))); // Obtiene la pestaña activa desde el controlador, sesión o valor antiguo
@endphp

@extends('layouts.main')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modoEdicion ? 'Editar' : 'Registrar' }} Caso</title>
    {{-- Incluye tus CSS aquí --}}
    <link rel="stylesheet" href="{{ asset('css/tabs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/RegistrarCaso.css') }}">
    {{-- CSS específicos de cada tab --}}
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/actividad.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/encargado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/denunciado.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/grupo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/croquis.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/seguimiento.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/intervencion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/anexoN3.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Proteccion/tabs/anexoN5.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>


                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.index') }}">Volver al listado</a>
                </div>
    <h6>
        @if($modoEdicion)
            Editar Caso de: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
        @else
            Registrar Nuevo Caso para: {{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}
        @endif
    </h6>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="general-error-container">
            <h6>Se encontraron los siguientes errores:</h6>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <ul id="formTabs">
        {{-- Los enlaces de las pestañas ahora siempre apuntan a la ruta 'legal.caso.edit' --}}
        <li><a class="tab-link {{ $activeTab == 'actividad' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'actividad']) }}">1. Actividad</a></li>
        <li><a class="tab-link {{ $activeTab == 'encargado' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'encargado']) }}">2. Encargado</a></li>
        <li><a class="tab-link {{ $activeTab == 'denunciado' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'denunciado']) }}">3. Denunciado</a></li>
        <li><a class="tab-link {{ $activeTab == 'grupo' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'grupo']) }}">4. Grupo Familiar</a></li>
        <li><a class="tab-link {{ $activeTab == 'croquis' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'croquis']) }}">5. Croquis</a></li>
        <li><a class="tab-link {{ $activeTab == 'seguimiento' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'seguimiento']) }}">6. Seguimiento</a></li>
        <li><a class="tab-link {{ $activeTab == 'intervencion' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'intervencion']) }}">7. Intervención</a></li>
        <li><a class="tab-link {{ $activeTab == 'anexo3' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'anexo3']) }}">8. Anexo N3</a></li>
        <li><a class="tab-link {{ $activeTab == 'anexo5' ? 'active' : '' }}" href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'anexo5']) }}">9. Anexo N5</a></li>
    </ul>

    {{-- Contenido de tabs --}}
    <div class="tab-content">
        {{-- Pestaña 1: Actividad Laboral --}}
        <div class="tab-pane {{ $activeTab == 'actividad' ? 'active' : '' }}" id="actividad">
            <form action="{{ route('legal.caso.storeActividad', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="actividad"> {{-- Para regresar a esta pestaña si hay errores --}}
                @include('Proteccion.tabs.actividad', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="next-act">
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 2: Encargado --}}
        <div class="tab-pane {{ $activeTab == 'encargado' ? 'active' : '' }}" id="encargado">
            <form action="{{ route('legal.caso.storeEncargado', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="encargado">
                @include('Proteccion.tabs.encargado', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    {{-- El botón "Anterior" también debe usar la ruta 'edit' --}}
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'actividad']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 3: Denunciado --}}
        <div class="tab-pane {{ $activeTab == 'denunciado' ? 'active' : '' }}" id="denunciado">
            <form action="{{ route('legal.caso.storeDenunciado', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="denunciado">
                @include('Proteccion.tabs.denunciado', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'encargado']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 4: Grupo Familiar --}}
        <div class="tab-pane {{ $activeTab == 'grupo' ? 'active' : '' }}" id="grupo">
            <form action="{{ route('legal.caso.storeGrupoFamiliar', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="grupo">
                @include('Proteccion.tabs.grupo', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'denunciado']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 5: Croquis --}}
        <div class="tab-pane {{ $activeTab == 'croquis' ? 'active' : '' }}" id="croquis">
            <form action="{{ route('legal.caso.storeCroquis', $adulto->id_adulto) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="active_tab" value="croquis">
                @include('Proteccion.tabs.croquis', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'grupo']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 6: Seguimiento --}}
        <div class="tab-pane {{ $activeTab == 'seguimiento' ? 'active' : '' }}" id="seguimiento">
            <form action="{{ route('legal.caso.storeSeguimiento', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="seguimiento">
                @include('Proteccion.tabs.seguimiento', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'croquis']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 7: Intervención --}}
        <div class="tab-pane {{ $activeTab == 'intervencion' ? 'active' : '' }}" id="intervencion">
            <form action="{{ route('legal.caso.storeIntervencion', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="intervencion">
                @include('Proteccion.tabs.intervencion', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'seguimiento']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 8: Anexo N3 --}}
        <div class="tab-pane {{ $activeTab == 'anexo3' ? 'active' : '' }}" id="anexo3">
            <form action="{{ route('legal.caso.storeAnexoN3', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="anexo3">
                @include('Proteccion.tabs.anexo3', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'intervencion']) }}">← Anterior</a>
                    <button type="submit">Siguiente →</button>
                </div>
            </form>
        </div>

        {{-- Pestaña 9: Anexo N5 --}}
        <div class="tab-pane {{ $activeTab == 'anexo5' ? 'active' : '' }}" id="anexo5">
            <form action="{{ route('legal.caso.storeAnexoN5', $adulto->id_adulto) }}" method="POST">
                @csrf
                <input type="hidden" name="active_tab" value="anexo5">
                @include('Proteccion.tabs.anexo5', ['adulto' => $adulto, 'modoEdicion' => $modoEdicion])
                <div class="navigation-buttons">
                    <a href="{{ route('legal.caso.edit', ['id_adulto' => $adulto->id_adulto, 'active_tab' => 'anexo3']) }}">← Anterior</a>
                    <button type="submit" id="btnGuardarFinal">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>


{{-- Script para la lógica de visualización de pestañas (sin AJAX) --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        // Función para mostrar una pestaña específica (solo CSS/JS para visibilidad, no carga de contenido)
        function showTab(targetId) {
            tabLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                // Usa indexOf para buscar el id de la pestaña en la URL del link
                if (linkHref && linkHref.indexOf(`active_tab=${targetId}`) > -1) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            tabPanes.forEach(pane => {
                if (pane.id === targetId) {
                    pane.classList.add('active');
                } else {
                    pane.classList.remove('active');
                }
            });
        }

        // Determinar la pestaña activa al cargar la página
        // `activeTab` ya viene del PHP con el valor correcto.
        const initialTabId = "{{ $activeTab }}";
        showTab(initialTabId);

        // Los enlaces de las pestañas ahora simplemente cambian la URL.
        // No necesitamos `e.preventDefault()` aquí porque queremos que los enlaces disparen una nueva carga de página
        // a la URL que ya contiene el `active_tab` correcto.
    });
    // ############################################################################################################
    document.addEventListener('DOMContentLoaded', function () {
        // Función para validar formato de CI (solo números y guiones, máx. 15 caracteres)
        function validarCI(ci) {
            return /^[0-9-]{1,15}$/.test(ci);
        }

        // Función para validar formato de teléfono (solo números y guiones, máx. 15 caracteres)
        function validarTelefono(telefono) {
            return /^[0-9-]{0,15}$/.test(telefono);
        }

        // Asignar validaciones a cada formulario según la pestaña
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function (event) {
                let errors = [];
                const activeTab = form.querySelector('input[name="active_tab"]').value;

                // Tab 1: Actividad Laboral
                if (activeTab === 'actividad') {
                    const skipActividad = document.getElementById('skipActividadLaboral')?.value === '1';
                    if (!skipActividad) {
                        const actividadFields = [
                            { id: 'nombre_actividad', label: 'Nombre de la Actividad Laboral' },
                            { id: 'direccion_trabajo', label: 'Dirección Habitual del Trabajo' },
                            { id: 'horario', label: 'Horario' },
                            { id: 'horas_x_dia', label: 'Horas de Trabajo por Día' },
                            { id: 'rem_men_aprox', label: 'Remuneración Mensual Aproximada' },
                            { id: 'telefono_laboral', label: 'Teléfono Laboral' }
                        ];
                        actividadFields.forEach(field => {
                            const input = document.getElementById(field.id);
                            if (input && input.value.trim() === '') {
                                errors.push(`El campo "${field.label}" es obligatorio si no omite la pestaña.`);
                            }
                            if (field.id === 'telefono_laboral' && input?.value.trim() && !validarTelefono(input.value.trim())) {
                                errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 15 caracteres).`);
                            }
                        });
                    }
                }

                // Tab 2: Encargado
                if (activeTab === 'encargado') {
                    const tipoEncargado = form.querySelector('input[name="tipo_encargado"]:checked')?.value;
                    if (!tipoEncargado) {
                        errors.push('Debe seleccionar un tipo de encargado (Persona Natural o Jurídica).');
                    } else if (tipoEncargado === 'natural') {
                        const naturalFields = [
                            { name: 'encargado_natural[nombres]', label: 'Nombres' },
                            { name: 'encargado_natural[primer_apellido]', label: 'Primer Apellido' },
                            { name: 'encargado_natural[ci]', label: 'CI' },
                            { name: 'encargado_natural[edad]', label: 'Edad' },
                            { name: 'encargado_natural[telefono]', label: 'Teléfono' },
                            { name: 'encargado_natural[direccion_domicilio]', label: 'Dirección Domicilio' },
                            { name: 'encargado_natural[relacion_parentesco]', label: 'Relación/Parentesco' }
                        ];
                        naturalFields.forEach(field => {
                            const input = form.querySelector(`input[name="${field.name}"]`);
                            if (input && input.value.trim() === '') {
                                errors.push(`El campo "${field.label}" es obligatorio para Persona Natural.`);
                            }
                            if (field.name === 'encargado_natural[ci]' && input?.value.trim() && !validarCI(input.value.trim())) {
                                errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 15 caracteres).`);
                            }
                            if (field.name === 'encargado_natural[telefono]' && input?.value.trim() && !validarTelefono(input.value.trim())) {
                                errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 15 caracteres).`);
                            }
                            if (field.name === 'encargado_natural[edad]' && input?.value.trim()) {
                                const edad = parseInt(input.value.trim());
                                if (isNaN(edad) || edad < 1 || edad > 120) {
                                    errors.push(`El campo "${field.label}" debe ser un número entre 1 y 120.`);
                                }
                            }
                        });
                    } else if (tipoEncargado === 'juridica') {
                        const juridicaFields = [
                            { name: 'nombre_institucion', label: 'Nombre de Institución' },
                            { name: 'direccion', label: 'Dirección' },
                            { name: 'telefono_juridica', label: 'Teléfono' },
                            { name: 'nombre_funcionario', label: 'Nombre del Funcionario Responsable' }
                        ];
                        juridicaFields.forEach(field => {
                            const input = form.querySelector(`input[name="${field.name}"]`);
                            if (input && input.value.trim() === '') {
                                errors.push(`El campo "${field.label}" es obligatorio para Persona Jurídica.`);
                            }
                            if (field.name === 'telefono_juridica' && input?.value.trim() && !validarTelefono(input.value.trim())) {
                                errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 15 caracteres).`);
                            }
                        });
                    }
                }

                // Tab 3: Denunciado
                if (activeTab === 'denunciado') {
                    const denunciadoFields = [
                        { name: 'denunciado_natural[nombres]', label: 'Nombres' },
                        { name: 'denunciado_natural[primer_apellido]', label: 'Primer Apellido' },
                        { name: 'sexo', label: 'Sexo' },
                        { name: 'denunciado_natural[edad]', label: 'Edad' },
                        { name: 'descripcion_hechos', label: 'Descripción de los Hechos' }
                    ];
                    denunciadoFields.forEach(field => {
                        const input = form.querySelector(`[name="${field.name}"]`);
                        if (input && input.value.trim() === '') {
                            errors.push(`El campo "${field.label}" es obligatorio en Denunciado.`);
                        }
                        
                        if (field.name === 'denunciado_natural[edad]' && input?.value.trim()) {
                            const edad = parseInt(input.value.trim());
                            if (isNaN(edad) || edad < 1 || edad > 120) {
                                errors.push(`El campo "${field.label}" debe ser un número entre 1 y 120.`);
                            }
                        }
                    });
                }

                // Tab 4: Grupo Familiar
                if (activeTab === 'grupo') {
                    const familiares = form.querySelectorAll('#familiares-container .familiar-group');
                    if (familiares.length === 0) {
                        errors.push('Debe registrar al menos un familiar en Grupo Familiar.');
                    } else {
                        familiares.forEach((group, index) => {
                            const familiarFields = [
                                { name: `familiares[${index}][apellido_paterno]`, label: 'Apellido Paterno' },
                                { name: `familiares[${index}][nombres]`, label: 'Nombres' },
                                { name: `familiares[${index}][parentesco]`, label: 'Parentesco' },
                                { name: `familiares[${index}][edad]`, label: 'Edad' }
                            ];
                            familiarFields.forEach(field => {
                                const input = group.querySelector(`input[name="${field.name}"]`);
                                if (input && input.value.trim() === '') {
                                    errors.push(`El campo "${field.label}" del Familiar #${index + 1} es obligatorio.`);
                                }
                                if (field.name.includes('[edad]') && input?.value.trim()) {
                                    const edad = parseInt(input.value.trim());
                                    if (isNaN(edad) || edad < 0 || edad > 120) {
                                        errors.push(`El campo "${field.label}" del Familiar #${index + 1} debe ser un número entre 0 y 120.`);
                                    }
                                }
                            });
                            const telefono = group.querySelector(`input[name="familiares[${index}][telefono]"]`);
                            if (telefono && telefono.value.trim() && !validarTelefono(telefono.value.trim())) {
                                errors.push(`El campo "Teléfono" del Familiar #${index + 1} debe contener solo números y guiones (máx. 15 caracteres).`);
                            }
                        });
                    }
                }

                // Tab 5: Croquis
                if (activeTab === 'croquis') {
                    const croquisFields = [
                        { id: 'nombre_denunciante', label: 'Nombres del Denunciante' },
                        { id: 'apellidos_denunciante', label: 'Apellidos del Denunciante' },
                        { id: 'ci_denunciante', label: 'CI del Denunciante' }
                    ];
                    croquisFields.forEach(field => {
                        const input = document.getElementById(field.id);
                        if (input && input.value.trim() === '') {
                            errors.push(`El campo "${field.label}" es obligatorio en Croquis.`);
                        }
                        if (field.id === 'ci_denunciante' && input?.value.trim() && !validarCI(input.value.trim())) {
                            errors.push(`El campo "${field.label}" debe contener solo números y guiones (máx. 15 caracteres).`);
                        }
                    });
                    const imageFile = document.getElementById('image_file');
                    const removeImage = document.getElementById('remove_image');
                    if (imageFile && imageFile.files.length > 0) {
                        const file = imageFile.files[0];
                        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!validTypes.includes(file.type)) {
                            errors.push('La imagen del croquis debe ser JPG, PNG o GIF.');
                        }
                        if (file.size > 2 * 1024 * 1024) { // 2MB, alineado con backend
                            errors.push('La imagen del croquis no debe superar los 2MB.');
                        }
                    } else if (!removeImage || !removeImage.checked) {
                        const currentImage = document.getElementById('image_preview')?.src;
                        if (!currentImage || currentImage.includes('Croquis.png')) {
                            errors.push('Debe subir una imagen para el Croquis o tener una imagen existente.');
                        }
                    }
                }

                // Tab 6: Seguimiento
                if (activeTab === 'seguimiento') {
                    const seguimientos = form.querySelectorAll('#seguimientos-container .seguimiento-group');
                    if (seguimientos.length === 0) {
                        errors.push('Debe registrar al menos un seguimiento en Seguimiento del Caso.');
                    } else {
                        seguimientos.forEach((group, index) => {
                            const seguimientoFields = [
                                { name: `seguimientos[${index}][nro]`, label: 'Nro de Seguimiento' },
                                { name: `seguimientos[${index}][fecha]`, label: 'Fecha' },
                                { name: `seguimientos[${index}][accion_realizada]`, label: 'Acción Realizada' },
                                { name: `seguimientos[${index}][resultado_obtenido]`, label: 'Resultado Obtenido' }
                            ];
                            seguimientoFields.forEach(field => {
                                const input = group.querySelector(`[name="${field.name}"]`);
                                if (input && input.value.trim() === '') {
                                    errors.push(`El campo "${field.label}" del Seguimiento #${index + 1} es obligatorio.`);
                                }
                            });
                        });
                    }
                }

                // Tab 7: Intervención
                if (activeTab === 'intervencion') {
                    const intervencionFields = [
                        'intervencion[resuelto_descripcion]',
                        'intervencion[no_resultado]',
                        'intervencion[derivacion_institucion]',
                        'intervencion[der_seguimiento_legal]',
                        'intervencion[der_seguimiento_psi]',
                        'intervencion[der_resuelto_externo]',
                        'intervencion[der_noresuelto_externo]',
                        'intervencion[abandono_victima]',
                        'intervencion[resuelto_conciliacion_jio]'
                    ];
                    let hasIntervencionValue = false;
                    intervencionFields.forEach(name => {
                        const input = form.querySelector(`[name="${name}"]`);
                        if (input && input.value.trim() !== '') {
                            hasIntervencionValue = true;
                        }
                    });
                    if (!hasIntervencionValue) {
                        errors.push('Debe completar al menos un campo en Intervención.');
                    }
                    const fechaIntervencion = form.querySelector('input[name="intervencion[fecha_intervencion]"]');
                    if (fechaIntervencion && fechaIntervencion.value.trim() === '') {
                        errors.push('El campo "Fecha de Intervención" es obligatorio.');
                    }
                }

                // Tab 8: Anexo N3
                if (activeTab === 'anexo3') {
                    const anexosN3 = form.querySelectorAll('#anexos3-container .anexo3-group');
                    if (anexosN3.length === 0) {
                        errors.push('Debe registrar al menos una persona en Anexo N3.');
                    } else {
                        const cis = [];
                        anexosN3.forEach((group, index) => {
                            const anexoFields = [
                                { name: `anexos_n3[${index}][primer_apellido]`, label: 'Primer Apellido' },
                                { name: `anexos_n3[${index}][nombres]`, label: 'Nombres' },
                                { name: `anexos_n3[${index}][sexo]`, label: 'Sexo' },
                                { name: `anexos_n3[${index}][edad]`, label: 'Edad' },
                                { name: `anexos_n3[${index}][ci]`, label: 'CI' }
                            ];
                            anexoFields.forEach(field => {
                                const input = group.querySelector(`[name="${field.name}"]`);
                                if (input && input.value.trim() === '') {
                                    errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 es obligatorio.`);
                                }
                                if (field.name.includes('[ci]') && input?.value.trim()) {
                                    if (!validarCI(input.value.trim())) {
                                        errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 debe contener solo números y guiones (máx. 15 caracteres).`);
                                    }
                                    const ci = input.value.trim();
                                    if (cis.includes(ci)) {
                                        errors.push(`El CI ${ci} está duplicado dentro de los Anexos N3.`);
                                    } else {
                                        cis.push(ci);
                                    }
                                }
                                if (field.name.includes('[telefono]') && input?.value.trim() && !validarTelefono(input.value.trim())) {
                                    errors.push(`El campo "Teléfono" de la Persona Natural #${index + 1} en Anexo N3 debe contener solo números y guiones (máx. 15 caracteres).`);
                                }
                                if (field.name.includes('[edad]') && input?.value.trim()) {
                                    const edad = parseInt(input.value.trim());
                                    if (isNaN(edad) || edad < 1 || edad > 120) {
                                        errors.push(`El campo "${field.label}" de la Persona Natural #${index + 1} en Anexo N3 debe ser un número entre 1 y 120.`);
                                    }
                                }
                            });
                        });
                    }
                }

                // Tab 9: Anexo N5
                if (activeTab === 'anexo5') {
                    const anexosN5 = form.querySelectorAll('#anexo5-container .anexo5-group');
                    if (anexosN5.length === 0) {
                        errors.push('Debe registrar al menos un anexo en Anexo N5.');
                    } else {
                        anexosN5.forEach((group, index) => {
                            const anexoFields = [
                                { name: `anexos_n5[${index}][numero]`, label: 'Número' },
                                { name: `anexos_n5[${index}][fecha]`, label: 'Fecha' },
                                { name: `anexos_n5[${index}][accion_realizada]`, label: 'Acción Realizada' },
                                { name: `anexos_n5[${index}][resultado_obtenido]`, label: 'Resultado Obtenido' }
                            ];
                            anexoFields.forEach(field => {
                                const input = group.querySelector(`[name="${field.name}"]`);
                                if (input && input.value.trim() === '') {
                                    errors.push(`El campo "${field.label}" del Anexo N5 #${index + 1} es obligatorio.`);
                                }
                            });
                        });
                    }
                }

                // Mostrar errores con SweetAlert2
                if (errors.length > 0) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores en el formulario',
                        html: '<ul style="text-align: left;">' + errors.map(error => `<li>${error}</li>`).join('') + '</ul>',
                        confirmButtonText: 'Corregir'
                    });
                }
            });
        });
    });
</script>

</body>
</html>
@endsection
