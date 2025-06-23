@extends('layouts.main')


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($modoEdicion)
            Editar Atención de Enfermería
        @else
            Registrar Atención de Enfermería
        @endif
    </title>
    {{-- Incluye tus CSS aquí --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    {{-- CSS específico para este formulario --}}
    <link rel="stylesheet" href="{{ asset('css/Medico/registrarAtencionEnfermeria.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>


@section('content')
<body>

<div class="container1">
    <div class="navigation-buttons">
        <a href="{{ route('responsable.enfermeria.enfermeria.index') }}" class="btn btn-secondary">← Volver al listado</a>
    </div>

    <h6>
        @if($modoEdicion)
            Editar Atención de Enfermería para: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
        @else
            REGISTRO DE ATENCIÓN DE ENFERMERÍA
        @endif
    </h6>

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
        <form action="{{ $modoEdicion ? route('responsable.enfermeria.enfermeria.update', ['cod_enf' => optional($fichaEnfermeria)->cod_enf]) : route('responsable.enfermeria.enfermeria.store', ['id_adulto' => $adulto->id_adulto]) }}" method="POST">
            @csrf
            @if($modoEdicion)
                @method('PUT')
            @endif

            {{-- Campo oculto para pasar el id_adulto --}}
            <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">

            <h5 class="mt-4">DATOS DE IDENTIFICACIÓN DEL ADULTO MAYOR:</h5>
            <div class="row">
                <div class="col-md-6"> {{-- Se cambió a col-md-6 para dejar espacio para Sexo y Edad --}}
                    <div class="form-group">
                        <label for="nombre_completo_am">NOMBRE COMPLETO:</label>
                        <div class="read-only-field">
                            {{ optional($adulto->persona)->nombres }}
                            {{ optional($adulto->persona)->primer_apellido }}
                            {{ optional($adulto->persona)->segundo_apellido }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3"> {{-- Nueva columna para Sexo --}}
                    <div class="form-group">
                        <label for="sexo_am">SEXO:</label>
                        <div class="read-only-field">{{ optional($adulto->persona)->sexo ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-3"> {{-- Nueva columna para Edad --}}
                    <div class="form-group">
                        <label for="edad_am">EDAD:</label>
                        <div class="read-only-field">{{ optional($adulto->persona)->edad ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4">CONTROL DE SIGNOS VITALES:</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="presion_arterial">Presión Arterial:</label>
                        <input type="text" id="presion_arterial" name="presion_arterial" class="form-control"
                               value="{{ old('presion_arterial', optional($fichaEnfermeria)->presion_arterial) }}">
                        @error('presion_arterial')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="frecuencia_cardiaca">Frecuencia Cardíaca:</label>
                        <input type="text" id="frecuencia_cardiaca" name="frecuencia_cardiaca" class="form-control"
                               value="{{ old('frecuencia_cardiaca', optional($fichaEnfermeria)->frecuencia_cardiaca) }}">
                        @error('frecuencia_cardiaca')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="frecuencia_respiratoria">Frecuencia Respiratoria:</label>
                        <input type="text" id="frecuencia_respiratoria" name="frecuencia_respiratoria" class="form-control"
                               value="{{ old('frecuencia_respiratoria', optional($fichaEnfermeria)->frecuencia_respiratoria) }}">
                        @error('frecuencia_respiratoria')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="pulso">Pulso:</label>
                        <input type="text" id="pulso" name="pulso" class="form-control"
                               value="{{ old('pulso', optional($fichaEnfermeria)->pulso) }}">
                        @error('pulso')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="temperatura">Temperatura:</label>
                        <input type="text" id="temperatura" name="temperatura" class="form-control"
                               value="{{ old('temperatura', optional($fichaEnfermeria)->temperatura) }}">
                        @error('temperatura')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="control_oximetria">Control Oximetría:</label>
                        <input type="text" id="control_oximetria" name="control_oximetria" class="form-control"
                               value="{{ old('control_oximetria', optional($fichaEnfermeria)->control_oximetria) }}">
                        @error('control_oximetria')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <h5 class="mt-4">ATENCIONES DE ENFERMERÍA:</h5>
            <div class="form-group">
                <label for="inyectables">INYECTABLES:</label>
                <textarea id="inyectables" name="inyectables" rows="3" class="form-control">{{ old('inyectables', optional($fichaEnfermeria)->inyectables) }}</textarea>
                @error('inyectables')<span style="color: red;">{{ $message }}</span>@enderror
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="peso_talla">PESO Y TALLA:</label>
                        <input type="text" id="peso_talla" name="peso_talla" class="form-control"
                               value="{{ old('peso_talla', optional($fichaEnfermeria)->peso_talla) }}">
                        @error('peso_talla')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="orientacion_alimentacion">ORIENTACIÓN ALIMENTACIÓN:</label>
                        <textarea id="orientacion_alimentacion" name="orientacion_alimentacion" rows="3" class="form-control">{{ old('orientacion_alimentacion', optional($fichaEnfermeria)->orientacion_alimentacion) }}</textarea>
                        @error('orientacion_alimentacion')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lavado_oidos">LAVADO DE OÍDOS:</label>
                        <textarea id="lavado_oidos" name="lavado_oidos" rows="3" class="form-control">{{ old('lavado_oidos', optional($fichaEnfermeria)->lavado_oidos) }}</textarea>
                        @error('lavado_oidos')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="orientacion_tratamiento">ORIENTACIÓN TRATAMIENTO:</label>
                        <textarea id="orientacion_tratamiento" name="orientacion_tratamiento" rows="3" class="form-control">{{ old('orientacion_tratamiento', optional($fichaEnfermeria)->orientacion_tratamiento) }}</textarea>
                        @error('orientacion_tratamiento')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="curacion">CURACIÓN:</label>
                        <textarea id="curacion" name="curacion" rows="3" class="form-control">{{ old('curacion', optional($fichaEnfermeria)->curacion) }}</textarea>
                        @error('curacion')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="adm_medicamentos">responsable.enfermeriaISTRACIÓN MEDICAMENTOS:</label>
                        <textarea id="adm_medicamentos" name="adm_medicamentos" rows="3" class="form-control">{{ old('adm_medicamentos', optional($fichaEnfermeria)->adm_medicamentos) }}</textarea>
                        @error('adm_medicamentos')<span style="color: red;">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="derivacion">DERIVACIÓN:</label>
                <textarea id="derivacion" name="derivacion" rows="3" class="form-control">{{ old('derivacion', optional($fichaEnfermeria)->derivacion) }}</textarea>
                @error('derivacion')<span style="color: red;">{{ $message }}</span>@enderror
            </div>

            <div class="navigation-buttons full-width">
                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-save"></i> @if($modoEdicion) Guardar Cambios @else Guardar Atención @endif
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
{{-- Incluye tus scripts aquí --}}
{{-- Script para los iconos Feather Icons --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush

</body>
</html>
@endsection