{{--
Ruta: resources/views/partials/menus/responsable_fisioterapia.blade.php
Menú para usuarios con rol 'responsable' y especialidad 'Fisioterapia-Kinesiología'.
--}}

{{-- Menú Desplegable: Fisioterapia --}}
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-activity"></i>
        <span class="side-menu__label">Fisioterapia</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        <li>
            <a href="{{ route('responsable.fisioterapia.atencion') }}" class="slide-item">Atención Fisioterapia</a>
        </li>
        <li>
            <a href="{{ route('responsable.fisioterapia.reportes') }}" class="slide-item">Reportes Fisioterapia</a>
        </li>
    </ul>
</li>

{{-- Menú Desplegable: Kinesiología --}}
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
        <i class="side-menu__icon fe fe-wind"></i>
        <span class="side-menu__label">Kinesiología</span>
        <i class="angle fe fe-chevron-right"></i>
    </a>
    <ul class="slide-menu">
        <li>
            <a href="{{ route('responsable.kinesiologia.atencion') }}" class="slide-item">Atención Kinesiología</a>
        </li>
        <li>
            <a href="{{ route('responsable.kinesiologia.reportes') }}" class="slide-item">Reportes Kinesiología</a>
        </li>
    </ul>
</li>
