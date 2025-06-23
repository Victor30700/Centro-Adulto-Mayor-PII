{{-- resources/views/Medico/registrarFichaKine.blade.php --}}
@extends('layouts.main')


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(isset($kinesiologia) && $kinesiologia->exists)
            Editar Ficha de Kinesiología
        @else
            Registrar Ficha de Kinesiología
        @endif
    </title>
    {{-- Incluye tus CSS aquí --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    {{-- CSS específico para este formulario --}}
    <link rel="stylesheet" href="{{ asset('css/Medico/registrarFichaKine.css') }}"> 
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>


@section('content')
                    <div class="navigation-buttons full-width">
                        {{-- El botón "Volver" debe apuntar al listado de adultos mayores para Kinesiología --}}
                        <a href="{{ route('responsable.kinesiologia.fisiokine.indexKine') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left"></i> Volver al listado
                        </a>
                    </div>
                    {{-- Título principal del formulario --}}
                    <h6>
                        @if(isset($kinesiologia) && $kinesiologia->exists)
                            EDITAR FICHA DE KINESIOLOGÍA PARA: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
                        @else
                            REGISTRO DE FICHA DE KINESIOLOGÍA PARA: <strong>{{ optional($adulto->persona)->nombres }} {{ optional($adulto->persona)->primer_apellido }} {{ optional($adulto->persona)->segundo_apellido }}</strong>
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
                        <form action="@if(isset($kinesiologia) && $kinesiologia->exists)
                                            {{ route('responsable.kinesiologia.fisiokine.updateKine', ['cod_kine' => $kinesiologia->cod_kine]) }}
                                          @else
                                            {{ route('responsable.kinesiologia.fisiokine.storeKine', ['id_adulto' => $adulto->id_adulto]) }}
                                          @endif" method="POST">
                            @csrf
                            @if(isset($kinesiologia) && $kinesiologia->exists)
                                @method('PUT')
                            @endif

                            {{-- Campo oculto para pasar el id_adulto --}}
                            <input type="hidden" name="id_adulto" value="{{ $adulto->id_adulto }}">
                            {{-- Campo oculto para id_historia (si existe una historia clínica asociada) --}}
                            @if (isset($historiaClinica) && $historiaClinica)
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lugar_nacimiento_am">LUGAR DE NACIMIENTO:</label>
                                        <div class="read-only-field">
                                            {{ optional($historiaClinica)->lugar_nacimiento ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="barrio_am">BARRIO:</label>
                                        <div class="read-only-field">
                                            {{ optional($adulto->persona)->zona_comunidad ?? 'N/A' }} / {{ optional($adulto->persona)->domicilio ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">SERVICIOS REALIZADOS:</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="checkbox-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="entrenamiento_funcional" id="entrenamiento_funcional" value="1"
                                                    {{ old('entrenamiento_funcional', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->entrenamiento_funcional : false) ? 'checked' : '' }}>
                                                <label1 class="form-check-label" for="entrenamiento_funcional">Entrenamiento Funcional</label1>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="gimnasio_maquina" id="gimnasio_maquina" value="1"
                                                    {{ old('gimnasio_maquina', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->gimnasio_maquina : false) ? 'checked' : '' }}>
                                                <label1 class="form-check-label" for="gimnasio_maquina">Gimnasio Máquinas</label1>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="aquafit" id="aquafit" value="1"
                                                    {{ old('aquafit', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->aquafit : false) ? 'checked' : '' }}>
                                                <label1 class="form-check-label" for="aquafit">Aquafit</label1>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="hidroterapia" id="hidroterapia" value="1"
                                                    {{ old('hidroterapia', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->hidroterapia : false) ? 'checked' : '' }}>
                                                <label1 class="form-check-label" for="hidroterapia">Hidroterapia</label1>
                                            </div>
                                            @error('entrenamiento_funcional')<span class="error-message">{{ $message }}</span>@enderror
                                            @error('gimnasio_maquina')<span class="error-message">{{ $message }}</span>@enderror
                                            @error('aquafit')<span class="error-message">{{ $message }}</span>@enderror
                                            @error('hidroterapia')<span class="error-message">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mt-4">TURNOS:</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="checkbox-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="manana" id="manana" value="1"
                                                    {{ old('manana', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->manana : false) ? 'checked' : '' }}>
                                                <label1 class="form-check-label" for="manana">Mañana</label1>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tarde" id="tarde" value="1"
                                                    {{ old('tarde', (isset($kinesiologia) && $kinesiologia->exists) ? $kinesiologia->tarde : false) ? 'checked' : '' }}>
                                                <label1 class="form-check-label" for="tarde">Tarde</label1>
                                            </div>
                                            @error('manana')<span class="error-message">{{ $message }}</span>@enderror
                                            @error('tarde')<span class="error-message">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="navigation-buttons full-width">
                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save"></i> @if(isset($kinesiologia) && $kinesiologia->exists) Guardar Cambios @else Guardar Ficha @endif
                                </button>
                            </div>
                        </form>
                    </div> {{-- Fin form-section --}}


@endsection

{{-- Scripts específicos para este formulario --}}
@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush

