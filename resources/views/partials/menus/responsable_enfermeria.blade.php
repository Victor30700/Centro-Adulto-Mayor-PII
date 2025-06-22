{{--
Ruta: resources/views/partials/menus/responsable_enfermeria.blade.php
Menú específico para usuarios con rol 'responsable' y especialidad 'Enfermeria'.
Las rutas han sido corregidas para coincidir con el archivo web.php.
--}}

{{--
    NOTA: Las rutas que no existen en tu archivo web.php se han comentado
    para evitar errores. Deberás crear esas rutas y controladores para habilitarlas.
--}}

{{-- Módulo Médico: Enfermería --}}
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-activity"></i>
        <span class="side-menu__label">Servicios</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        {{-- Ruta Corregida --}}
        <li><a href="{{ route('responsable.enfermeria.servicios') }}" class="slide-item">Ver Servicios</a></li>
        {{-- <li><a href="{{-- route('responsable.enfermeria.servicios.create') --}}" class="slide-item">Registrar Servicio</a></li> --}}
    </ul>
</li>

<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-file-text"></i>
        <span class="side-menu__label">Historias Clínicas</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        {{-- Ruta Corregida --}}
        <li><a href="{{ route('responsable.enfermeria.historias') }}" class="slide-item">Ver Historias</a></li>
        {{-- <li><a href="{{-- route('responsable.enfermeria.historias.create') --}}" class="slide-item">Nueva Historia</a></li> --}}
    </ul>
</li>

<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-bar-chart-2"></i>
        <span class="side-menu__label">Reportes Enfermería</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        {{-- Ruta Corregida --}}
        <li><a href="{{ route('responsable.enfermeria.reportes') }}" class="slide-item">Reportes Generales</a></li>
        {{-- <li><a href="{{-- route('responsable.enfermeria.reportes.estadisticas') --}}" class="slide-item">Estadísticas</a></li> --}}
    </ul>
</li>
