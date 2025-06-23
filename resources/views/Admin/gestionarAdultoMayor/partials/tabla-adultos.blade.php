{{-- resources/views/Admin/gestionarAdultoMayor/partials/tabla-adultos.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped text-nowrap">
        <thead class="bg-success-light">
            <tr>
                <th>CI</th>
                <th>Nombre Completo</th>
                <th>Edad</th>
                <th>Sexo</th>
                <th>Teléfono</th>
                <th>Domicilio</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($adultosMayores as $adulto)
                <tr>
                    <td>{{ $adulto->ci }}</td>
                    <td>{{ $adulto->nombres }} {{ $adulto->primer_apellido }} {{ $adulto->segundo_apellido }}</td>
                    <td>{{ $adulto->edad }} años</td>
                    <td>
                        @if($adulto->sexo == 'M')
                            <span class="badge bg-primary-light">Masculino</span>
                        @elseif($adulto->sexo == 'F')
                            <span class="badge bg-pink-light">Femenino</span>
                        @else
                            <span class="badge bg-warning-light">Otro</span>
                        @endif
                    </td>
                    <td>{{ $adulto->telefono }}</td>
                    <td>{{ $adulto->domicilio }}</td>
                    <td class="text-center">
                        {{-- ===================== CORRECCIÓN ===================== --}}
                        {{-- Se eliminó el prefijo 'admin.' del nombre de la ruta para que coincida con web.php --}}
                        <a href="{{ route('gestionar-adultomayor.editar', $adulto->ci) }}" class="btn btn-sm btn-primary btn-action" data-bs-toggle="tooltip" title="Editar">
                            <i class="fe fe-edit"></i>
                        </a>

                        {{-- Botón para Eliminar (que activa el modal) --}}
                        <button type="button" class="btn btn-sm btn-danger btn-action btn-eliminar" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEliminar"
                                data-ci="{{ $adulto->ci }}"
                                data-nombre="{{ $adulto->nombres }} {{ $adulto->primer_apellido }}"
                                data-bs-toggle="tooltip" title="Eliminar">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        <i class="fe fe-info me-2"></i>No se encontraron registros de adultos mayores.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación --}}
<div class="d-flex justify-content-center mt-3">
    {{ $adultosMayores->links() }}
</div>