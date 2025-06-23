@extends('layouts.main')

@push('styles')

    <link rel="stylesheet" href="{{ asset('css/gestionarRoles.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.css"/>
    {{-- Cargamos SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

@endpush

@section('content')
<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <!-- Cabecera de la Página -->
                    <div class="page-header">
                        <h1 class="page-title">Gestionar Roles</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestionar Roles</li>
                            </ol>
                        </div>
                    </div>

                    {{-- Las Alertas de Sesión se manejarán con SweetAlert2 --}}

                    <!-- Contenido Principal: Tabla de Roles -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h3 class="card-title text-white mb-0">Listado de Roles del Sistema</h3>

                                    @can('roles.create')
                                        <a href="{{ route('admin.gestionar-roles.create') }}" class="btn btn-light btn-sm">
                                            <i class="fe fe-plus-circle me-1"></i>Agregar Rol
                                        </a>
                                    @endcan

                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="rolesTable" class="table table-bordered table-striped w-100">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre del Rol</th>
                                                    <th>Descripción</th>
                                                    <th class="text-center">Permisos</th>
                                                    <th class="text-center">Usuarios</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($roles as $rol)
                                                    <tr>
                                                        <td><strong>{{ $rol->id_rol }}</strong></td>
                                                        <td>{{ $rol->nombre_rol }}</td>
                                                        <td>
                                                            <span title="{{ $rol->descripcion }}">{{ Str::limit($rol->descripcion, 50) }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">{{ $rol->permissions_count ?? $rol->permissions->count() }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-secondary">{{ $rol->users_count ?? $rol->users->count() }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($rol->active)
                                                                <span class="badge bg-success">Activo</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactivo</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">
                                                                @can('roles.edit')
                                                                    <a href="{{ route('admin.gestionar-roles.edit', $rol->id_rol) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Editar Rol">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('roles.destroy')
                                                                    @if (strtolower($rol->nombre_rol) !== 'admin')
                                                                        {{-- Se añade una clase para identificar el formulario con JS --}}
                                                                        <form action="{{ route('admin.gestionar-roles.destroy', $rol->id_rol) }}" method="POST" class="d-inline form-delete-role">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Eliminar Rol">
                                                                                <i class="fe fe-trash-2"></i>
                                                                            </button>
                                                                        </form>
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="El rol de Administrador no puede ser eliminado" disabled>
                                                                            <i class="fe fe-trash-2"></i>
                                                                        </button>
                                                                    @endif
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            <i class="fe fe-inbox fs-3"></i><br>
                                                            No hay roles registrados en el sistema.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- El modal de confirmación personalizado ya no es necesario --}}
@endsection



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.5/datatables.min.js"></script>
{{-- Cargamos SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        if (typeof $().DataTable === 'function') {
            $('#rolesTable').DataTable({
                language: {
                    url: '{{ asset('assets/translates/Spanish.json') }}'
                },
                responsive: true,
                order: [[0, 'asc']],
                dom: 'lfrtip',
                columnDefs: [
                    { targets: [3, 4, 5, 6], orderable: false, searchable: false }
                ]
            });
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // --- Manejo de Alertas de Sesión con SweetAlert2 ---
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif
        
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // --- Confirmación para eliminar Rol con SweetAlert2 ---
        document.querySelectorAll('.form-delete-role').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Está seguro de eliminar este rol?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

    });
</script>
@endpush
