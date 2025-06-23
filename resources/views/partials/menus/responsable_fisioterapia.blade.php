{{--
Ruta: resources/views/partials/menus/responsable_fisioterapia.blade.php
Menú para usuarios con rol 'responsable' y especialidad 'Fisioterapia-Kinesiología'.
--}}
{{-- Dashboard --}}
<li class="slide">
    <a class="side-menu__item" href="{{ route('responsable.dashboard') }}">
        <i class="side-menu__icon fe fe-home"></i>
        <span class="side-menu__label">Dashboard</span>
    </a>
</li>

<li class="sub-category"><h3>Módulo Médico</h3></li>
<li class="slide">
    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);"><i class="side-menu__icon fe fe-activity"></i><span class="side-menu__label">Fisioterapia-Kinesiologia</span><i class="angle fe fe-chevron-right"></i></a>
    <ul class="slide-menu">
        <li><a href="{{ route('responsable.fisioterapia.fisiokine.indexFisio') }}" class="slide-item">Registrar Ficha</a></li>
        <li><a href="{{ route('responsable.fisioterapia.reportefisio.index') }}" class="slide-item">Reportes Fisio-Kine</a></li>
    </ul>
</li>
