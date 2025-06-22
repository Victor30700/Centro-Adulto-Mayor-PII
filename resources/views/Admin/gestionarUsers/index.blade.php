{{-- resources/views/Admin/gestionarUsuarios.blade.php --}}
@extends('layouts.main')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/gestionarUsuarios.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    {{-- Cargamos DataTables --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
</head>

<div class="page">
    <div class="page-main">
        <div class="main-content app-content mt-0">
            <div class="side-app">
                <div class="main-container container-fluid">

                    <div class="page-header">
                        <h1 class="page-title">Gestionar Usuarios</h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestionar Usuarios</li>
                            </ol>
                        </div>
                    </div>

                    {{-- Mensajes de alerta de sesión --}}
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <strong>¡Éxito!</strong>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-error" role="alert">
                            <strong>¡Error!</strong>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title text-white">Listado de Usuarios del Sistema</h3>
                                    <div class="card-options">
                                        <a href="{{ route('admin.registrar-responsable-salud') }}" class="btn btn-white btn-sm">
                                            <i data-feather="plus-circle"></i> Registrar Nuevo Responsable
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="usersTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>CI (ID Usuario)</th>
                                                    <th>CI/Usuario</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Rol</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Registro</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($users as $usuario)
                                                    <tr>
                                                        <td><strong>{{ $usuario->ci }}</strong></td>
                                                        <td>{{ $usuario->username ?? $usuario->ci ?? 'N/A' }}</td>
                                                        <td>
                                                            {{ $usuario->persona->nombres ?? 'N/A' }} 
                                                            {{ $usuario->persona->primer_apellido ?? '' }} 
                                                            {{ $usuario->persona->segundo_apellido ?? '' }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $usuario->rol->nombre_rol ?? 'Sin rol asignado' }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($usuario->active)
                                                                <span class="badge bg-success">Activo</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactivo</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($usuario->created_at)
                                                                {{ $usuario->created_at->format('d/m/Y') }}
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                {{-- Botón para editar usuario --}}
                                                                <a href="{{ route('admin.gestionar-usuarios.edit', $usuario->id_usuario) }}" 
                                                                   class="btn btn-sm btn-info" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="Editar">
                                                                    <i class="fe fe-edit"></i>
                                                                </a>

                                                                {{-- Formulario para activar/desactivar usuario --}}
                                                                <form action="{{ route('admin.gestionar-usuarios.toggleActivity', $usuario->id_usuario) }}" 
                                                                      method="POST" 
                                                                      class="d-inline"
                                                                      onsubmit="return showCustomConfirm(event, '¿Está seguro de cambiar el estado de este usuario?', this)">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                            class="btn btn-sm {{ $usuario->active ? 'btn-warning' : 'btn-success' }}"
                                                                            data-bs-toggle="tooltip" 
                                                                            title="{{ $usuario->active ? 'Desactivar Usuario' : 'Activar Usuario' }}">
                                                                        <i class="fe {{ $usuario->active ? 'fe-user-x' : 'fe-user-check' }}"></i>
                                                                    </button>
                                                                </form>

                                                                {{-- Formulario para eliminar usuario --}}
                                                                <form action="{{ route('admin.gestionar-usuarios.destroy', $usuario->id_usuario) }}" 
                                                                      method="POST" 
                                                                      class="d-inline"
                                                                      onsubmit="return showCustomConfirm(event, '¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.', this)">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="btn btn-sm btn-danger" 
                                                                            data-bs-toggle="tooltip" 
                                                                            title="Eliminar Usuario">
                                                                        <i class="fe fe-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            <i class="fe fe-inbox"></i>
                                                            <br>
                                                            No hay usuarios registrados
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    {{-- Paginación si está disponible --}}
                                    @if(method_exists($users, 'links') && $users->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $users->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modales (iguales a los del dashboard) --}}
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailsModalLabel">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal de Confirmación Personalizado --}}
<div class="custom-modal-overlay" id="customConfirmModalOverlay">
    <div class="custom-modal">
        <div class="custom-modal-header">
            <h5 class="modal-title">Confirmación</h5>
            <button type="button" class="btn-close" onclick="hideCustomConfirm()"></button>
        </div>
        <div class="custom-modal-body">
            <p id="customConfirmMessage"></p>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideCustomConfirm()">Cancelar</button>
            <button type="button" class="btn btn-danger" id="customConfirmBtn">Confirmar</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script>
    // Inicializar Feather Icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Inicialización de DataTables
        if (typeof $().DataTable === 'function') {
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                },
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 25,
                order: [[0, 'asc']], // Ordenar por CI ascendente
                columnDefs: [
                    {
                        targets: [6], // Columna de acciones
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fe fe-download"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fe fe-file-text"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fe fe-printer"></i> Imprimir',
                        className: 'btn btn-info btn-sm'
                    }
                ]
            });
        }
    });

    // --- Funciones para el Modal de Confirmación Personalizado ---
    let currentConfirmForm = null;

    function showCustomConfirm(event, message, form) {
        event.preventDefault();
        currentConfirmForm = form;

        const overlay = document.getElementById('customConfirmModalOverlay');
        const msgElement = document.getElementById('customConfirmMessage');
        const confirmBtn = document.getElementById('customConfirmBtn');

        msgElement.textContent = message;
        
        confirmBtn.onclick = function() {
            if (currentConfirmForm) {
                currentConfirmForm.submit();
                hideCustomConfirm();
            }
        };

        overlay.classList.add('show');
        return false;
    }

    function hideCustomConfirm() {
        const overlay = document.getElementById('customConfirmModalOverlay');
        overlay.classList.remove('show');
        currentConfirmForm = null;
    }

    // --- Función para ver detalles del usuario ---
    function viewUser(userId) {
        const modalElement = document.getElementById('userDetailsModal');
        const modal = new bootstrap.Modal(modalElement);
        const content = document.getElementById('userDetailsContent');
        
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        modal.show();
        
        // Aquí iría la lógica AJAX para obtener los detalles
        // Por ahora, mostramos un placeholder
        content.innerHTML = `
            <div class="alert alert-info">
                <i data-feather="info"></i>
                Detalles del usuario con ID: ${userId}
                <br><small>Esta funcionalidad puede ser implementada con AJAX para mostrar información detallada.</small>
            </div>
        `;
        
        if (typeof feather !== 'undefined') {
            feather.replace({ parent: content });
        }
    }
</script>
@endpush