@include('header')

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($fisioterapia->exists)
            Editar Ficha de Fisioterapia
        @else
            Registrar Ficha de Fisioterapia
        @endif
    </title>
    {{-- Incluye tus CSS aquí --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    {{-- CSS específico para este formulario (ubicación corregida) --}}
    <link rel="stylesheet" href="{{ asset('css/Medico/registrarFichaFisio.css') }}"> 
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body>

<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">
                            <div class="navigation-buttons full-width">
                                <a href="{{ route('admin.fisiokine.indexFisio') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left"></i> Volver al listado
                                </a>
                            </div>
                    {{-- Título principal del formulario, con la misma estructura y clase "h6" --}}
                    <h6>
                        @if($fisioterapia->exists)
                            EDITAR FICHA DE FISIOTERAPIA PARA: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
                        @else
                            REGISTRO DE FICHA DE FISIOTERAPIA
                        @endif
                    </h6>

                    {{-- Mensajes de éxito y error --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="general-error-container1">
                            <h6>Se encontraron los siguientes errores:</h6>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-section">
                        <form action="@if($fisioterapia->exists)
                                        {{ route('admin.fisiokine.updateFisio', ['cod_fisio' => $fisioterapia->cod_fisio]) }}
                                      @else
                                        {{ route('admin.fisiokine.storeFisio', ['id_adulto' => $adulto->id_adulto]) }}
                                      @endif" method="POST">
                            @csrf
                            @if($fisioterapia->exists)
                                @method('PUT')
                            @endif

                            {{-- Campo oculto para pasar el id_adulto --}}
                            <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">
                            {{-- Campo oculto para id_historia (si existe una historia clínica asociada) --}}
                            @if ($historiaClinica)
                                <input type="hidden" name="id_historia" value="{{ $historiaClinica->id_historia }}">
                            @endif

                            <h5 class="mt-4">DATOS DE IDENTIFICACIÓN DEL ADULTO MAYOR:</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre_completo_am">NOMBRE COMPLETO:</label>
                                        <div class="read-only-field">
                                            {{ optional($adulto->persona)->nombres }}
                                            {{ optional($adulto->persona)->primer_apellido }}
                                            {{ optional($adulto->persona)->segundo_apellido }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sexo_am">SEXO:</label>
                                        <div class="read-only-field">{{ optional($adulto->persona)->sexo ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="edad_am">EDAD:</label>
                                        <div class="read-only-field">{{ optional($adulto->persona->fecha_nacimiento) ? \Carbon\Carbon::parse($adulto->persona->fecha_nacimiento)->age . ' años' : 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ci_am">NÚMERO DE DOCUMENTO DE IDENTIDAD:</label>
                                        <div class="read-only-field">{{ optional($adulto->persona)->ci ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono_am">NÚMERO DE TELÉFONO:</label>
                                        <div class="read-only-field">{{ optional($adulto->persona)->telefono ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="direccion_am">DIRECCIÓN DE DOMICILIO:</label>
                                        <div class="read-only-field">{{ optional($adulto->persona)->direccion ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vive_con">CON QUIEN VIVE:</label>
                                        <div class="read-only-field">{{ optional($adulto)->vive_con ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado_civil_am">ESTADO CIVIL:</label>
                                        <div class="read-only-field">{{ ucfirst(optional($adulto->persona)->estado_civil ?? 'N/A') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="grado_instruccion_am">GRADO DE INSTRUCCIÓN:</label>
                                        <div class="read-only-field">{{ optional($historiaClinica)->grado_instruccion ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ocupacion_actual_am">OCUPACIÓN ACTUAL:</label>
                                        <div class="read-only-field">{{ optional($historiaClinica)->ocupacion ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="num_emergencia" class="form-label">NÚMEROS DE EMERGENCIA:</label>
                                        <input type="text" id="num_emergencia" name="num_emergencia" class="form-control {{ $errors->has('num_emergencia') ? 'is-invalid' : '' }}"
                                               value="{{ old('num_emergencia', $fisioterapia->num_emergencia) }}">
                                        @error('num_emergencia')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">SITUACIÓN DE SALUD:</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="enfermedades_actuales" class="form-label">ENFERMEDADES ACTUALES:</label>
                                        <textarea id="enfermedades_actuales" name="enfermedades_actuales" rows="3" class="form-control {{ $errors->has('enfermedades_actuales') ? 'is-invalid' : '' }}">{{ old('enfermedades_actuales', $fisioterapia->enfermedades_actuales) }}</textarea>
                                        <small class="form-text text-muted">Ej: Hipertensión arterial, Diabetes, Artrosis, Osteoporosis, Parkinson. Otras: (Especificar)</small>
                                        @error('enfermedades_actuales')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="alergias" class="form-label">ALERGIAS:</label>
                                        <textarea id="alergias" name="alergias" rows="3" class="form-control {{ $errors->has('alergias') ? 'is-invalid' : '' }}">{{ old('alergias', $fisioterapia->alergias) }}</textarea>
                                        <small class="form-text text-muted">Ej: Medicamentos. Indicar: / Alimentos. Indicar: / No refiere.</small>
                                        @error('alergias')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">PLAN DE PARTICIPACIÓN INDIVIDUAL O GRUPAL:</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_programacion" class="form-label">FECHA DE PROGRAMACIÓN:</label>
                                        <input type="date" id="fecha_programacion" name="fecha_programacion" class="form-control {{ $errors->has('fecha_programacion') ? 'is-invalid' : '' }}"
                                               value="{{ old('fecha_programacion', optional($fisioterapia->fecha_programacion)->format('Y-m-d')) }}">
                                        @error('fecha_programacion')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="motivo_consulta" class="form-label">MOTIVO DE CONSULTA:</label>
                                        <textarea id="motivo_consulta" name="motivo_consulta" rows="3" class="form-control {{ $errors->has('motivo_consulta') ? 'is-invalid' : '' }}">{{ old('motivo_consulta', $fisioterapia->motivo_consulta) }}</textarea>
                                        @error('motivo_consulta')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="solicitud_atencion" class="form-label">SOLICITUD ATENCIÓN:</label>
                                        <textarea id="solicitud_atencion" name="solicitud_atencion" rows="3" class="form-control {{ $errors->has('solicitud_atencion') ? 'is-invalid' : '' }}">{{ old('solicitud_atencion', $fisioterapia->solicitud_atencion) }}</textarea>
                                        @error('solicitud_atencion')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">EQUIPOS:</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="equipos" class="form-label">EQUIPOS UTILIZADOS:</label>
                                        <input type="text" id="equipos" name="equipos" class="form-control {{ $errors->has('equipos') ? 'is-invalid' : '' }}"
                                               value="{{ old('equipos', $fisioterapia->equipos) }}">
                                        <small class="form-text text-muted">Ej: ELECTRO ESTIMULADOR, ULTRASONIDO, OTROS (Especificar)</small>
                                        @error('equipos')<span class="error-message">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="navigation-buttons full-width">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save"></i> @if($fisioterapia->exists) Guardar Cambios @else Guardar Ficha @endif
                                </button>
                            </div>
                        </form>
                    </div> {{-- Fin form-section --}}

                </div> {{-- Fin main-container --}}
            </div> {{-- Fin side-app --}}
        </div> {{-- Fin main-content --}}
    </div> {{-- Fin page-main --}}
</div> {{-- Fin page --}}

@include('footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
</body>
</html>
