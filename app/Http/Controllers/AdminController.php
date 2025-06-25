<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona; // Asegúrate que el namespace sea correcto
use App\Models\Rol;     // Asegúrate que el namespace sea correcto
use App\Models\AdultoMayor;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // Usar Validator para más control
use Carbon\Carbon; // Para calcular la edad
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard con estadísticas y la tabla de usuarios.
     */
    public function dashboard()
    {
        // Obtener todos los usuarios con sus relaciones
        $users = User::with(['persona', 'rol'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Estadísticas
        $totalUsers    = $users->count();
        $activeUsers   = $users->where('active', true)->count();
        $inactiveUsers = $users->where('active', false)->count();
        $lockedUsers   = User::where('active', false)
                                ->whereNotNull('temporary_lockout_until')
                                ->count();

        return view('Admin.dashboard', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'lockedUsers'
        ));
    }

    /**
     * Muestra la lista de usuarios por separado (si la necesitas).
     */
    public function listUsers()
    {
        $users = User::with(['persona', 'rol'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('Admin.users', compact('users'));
    }

    /**
     * Activa o desactiva un usuario.
     */
    public function toggleActive(User $user)
    {
        $user->active = !$user->active;

        if (! $user->active) {
            $user->temporary_lockout_until = null;
            $user->login_attempts = 0;
            $user->last_failed_login_at = null;
        }

        $user->save();

        $status = $user->active ? 'activado' : 'desactivado';
        Log::info("Admin cambió estado de usuario {$user->ci} a {$status}");

        return back()->with('success', "Usuario {$status} exitosamente.");
    }

    /** Mostrar formularios de registro *
    public function showRegisterAsistenteSocial()
    {
        return view('Admin.registerUsers.registerAsistsocial.registerAsistsocial');
    }*/

    public function showRegisterLegal()
    {
        return view('Admin.registerUsers.registerLegal.registerLeg');
    }

    public function showRegisterAdultoMayor()
    {
        return view('Admin.registerUsers.registerPaciente.registerPac');
    }

    public function showRegisterResponsableSalud() // Este método muestra el formulario
    {
        // Podrías pasar roles si el campo de rol fuera un select dinámico
        // $roles = Rol::all();
        // return view('Admin.registerUsers.registerResponsable.registerRes', compact('roles'));
        return view('Admin.registerUsers.registerResponsable.registerRes');
    }

 public function storeAdultoMayor(Request $request)
    {
        // Agregar logging para depuración
        Log::info('Iniciando registro de adulto mayor', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            // Pestaña 1: Datos Personales (tabla 'persona')
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => 'required|string|max:20|unique:persona,ci',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',

            // Pestaña 2: Datos específicos de adulto mayor (tabla 'adulto_mayor')
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'nullable|in:0,1',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso',
            'fecha' => 'required|date',
        ], [
            // Mensajes personalizados
            'nombres.required' => 'El campo nombres es obligatorio.',
            'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.unique' => 'Este CI ya ha sido registrado.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'sexo.in' => 'El sexo debe ser Femenino, Masculino u Otro.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El estado civil debe ser uno de los valores válidos.',
            'domicilio.required' => 'El domicilio es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'nro_caso.unique' => 'Este número de caso ya ha sido registrado.',
            'fecha.required' => 'La fecha de registro es obligatoria.',
            'fecha.date' => 'La fecha de registro debe ser una fecha válida.',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación falló para registro de adulto mayor', ['errors' => $validator->errors()]);
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        DB::beginTransaction();

        try {
            // Calcular edad
            $edad = Carbon::parse($request->fecha_nacimiento)->age;
            
            Log::info('Calculando edad', ['fecha_nacimiento' => $request->fecha_nacimiento, 'edad' => $edad]);

            // 1. Crear Persona
            // Si estás usando modelos Eloquent y tienen $fillable configurado, puedes usar create.
            // Si no, el Query Builder como lo tenías está bien, pero Persona::create es más idiomático de Eloquent.
            $persona = Persona::create([
                'ci' => $request->ci,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'nombres' => $request->nombres,
                'sexo' => $request->sexo,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'edad' => $edad,
                'estado_civil' => $request->estado_civil,
                'domicilio' => $request->domicilio,
                'telefono' => $request->telefono,
                'zona_comunidad' => $request->zona_comunidad,
                // 'created_at' y 'updated_at' usualmente son manejados por Eloquent automáticamente
            ]);

            Log::info('Persona creada exitosamente', ['persona_id' => $persona->id, 'ci' => $persona->ci]);

            // 2. Preparar datos para adulto_mayor
            $adultoMayorData = [
                'ci' => $request->ci, // Clave foránea hacia persona
                'discapacidad' => $request->discapacidad,
                'vive_con' => $request->vive_con,
                'migrante' => $request->migrante == '1' ? true : false, // Conversión explícita
                'fecha' => $request->fecha,
                // 'created_at' y 'updated_at' usualmente son manejados por Eloquent automáticamente
            ];

            // Solo agregar nro_caso si se proporcionó y no está vacío
            if ($request->filled('nro_caso')) {
                $adultoMayorData['nro_caso'] = $request->nro_caso;
            }

            // Si usas Eloquent para AdultoMayor y tiene una relación definida con Persona,
            // podrías hacer algo como $persona->adultoMayor()->create($adultoMayorData);
            // o si 'ci' es la clave primaria/única en adulto_mayor y también la FK:
            $adultoMayor = AdultoMayor::create($adultoMayorData);


            Log::info('Adulto mayor creado exitosamente', ['adulto_mayor_id' => $adultoMayor->id_adulto ?? $adultoMayor->id]); // Ajusta según tu PK

            DB::commit();

            Log::info('Transacción completada exitosamente');

            return redirect()->route('gestionar-adultomayor.index') // <--- ESTA ES LA LÍNEA CORRECTA
                 ->with('success', 'Adulto Mayor registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error registrando adulto mayor: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                // 'trace' => $e->getTraceAsString(), // Puede ser muy verboso para logs regulares
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                            ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar al adulto mayor. Por favor, intente de nuevo. Detalles: ' . $e->getMessage()])
                            ->withInput();
        }
    }

    /**
    * Mostrar listado de adultos mayores
    */
    public function gestionarAdultoMayorIndex()
    {
        try {
            // Obtener todos los adultos mayores con sus datos de persona
            $adultosMayores = DB::table('adulto_mayor as am')
                ->join('persona as p', 'am.ci', '=', 'p.ci')
                ->select([
                    'p.ci',
                    'p.nombres',
                    'p.primer_apellido',
                    'p.segundo_apellido',
                    'p.sexo',
                    'p.fecha_nacimiento',
                    'p.edad',
                    'p.estado_civil',
                    'p.domicilio',
                    'p.telefono',
                    'p.zona_comunidad',
                    'am.id_adulto', // Clave primaria de adulto_mayor
                    'am.discapacidad',
                    'am.vive_con',
                    'am.migrante',
                    'am.nro_caso',
                    'am.fecha as fecha_registro'
                ])
                ->orderBy('p.primer_apellido', 'asc')
                ->orderBy('p.nombres', 'asc')
                ->paginate(10); // Paginación

            return view('Admin.gestionarAdultoMayor.index', compact('adultosMayores'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar listado de adultos mayores: ' . $e->getMessage());
            return redirect()->route('admin.dashboard') // O alguna otra ruta de fallback
                            ->with('error', 'Error al cargar el listado de adultos mayores.');
        }
    }

    /**
    * Buscar adultos mayores (AJAX)
    */
    public function buscarAdultoMayor(Request $request)
    {
        try {
            $busqueda = $request->get('busqueda', '');
            
            $query = DB::table('adulto_mayor as am')
                ->join('persona as p', 'am.ci', '=', 'p.ci')
                ->select([
                    'p.ci',
                    'p.nombres',
                    'p.primer_apellido',
                    'p.segundo_apellido',
                    'p.sexo',
                    'p.fecha_nacimiento',
                    'p.edad',
                    'p.estado_civil',
                    'p.domicilio',
                    'p.telefono',
                    'p.zona_comunidad',
                    'am.id_adulto',
                    'am.discapacidad',
                    'am.vive_con',
                    'am.migrante',
                    'am.nro_caso',
                    'am.fecha as fecha_registro'
                ]);

            if (!empty($busqueda)) {
                $query->where(function($q) use ($busqueda) {
                    $q->where('p.ci', 'ILIKE', '%' . $busqueda . '%') // ILIKE para PostgreSQL (case-insensitive)
                    ->orWhere('p.nombres', 'ILIKE', '%' . $busqueda . '%')
                    ->orWhere('p.primer_apellido', 'ILIKE', '%' . $busqueda . '%')
                    ->orWhere('p.segundo_apellido', 'ILIKE', '%' . $busqueda . '%')
                    // Para PostgreSQL, la concatenación es con ||
                    ->orWhereRaw("p.nombres || ' ' || p.primer_apellido || ' ' || COALESCE(p.segundo_apellido, '') ILIKE ?", ['%' . $busqueda . '%']);
                });
            }

            $adultosMayores = $query->orderBy('p.primer_apellido', 'asc')
                                    ->orderBy('p.nombres', 'asc')
                                    ->paginate(10); // Paginación también aquí

            // Devolver HTML de la tabla y de la paginación
            return response()->json([
                'success' => true,
                'html' => view('Admin.gestionarAdultoMayor.partials.tabla-adultos', compact('adultosMayores'))->render(),
                'pagination' => $adultosMayores->links()->toHtml(), // CORRECCIÓN AQUÍ
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de adultos mayores: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda. Detalles: ' . $e->getMessage()
            ], 500); // Es buena práctica devolver un código de error HTTP apropiado
        }
    }

    /**
    * Mostrar formulario de edición
    */
    public function editarAdultoMayor($ci) // Se recibe el CI de la persona
    {
        try {
            $adultoMayor = DB::table('persona as p') // Empezar por persona para obtener todos sus datos
                ->join('adulto_mayor as am', 'p.ci', '=', 'am.ci')
                ->select([
                    'p.*', // Todos los campos de persona
                    'am.id_adulto', // Clave primaria de adulto_mayor
                    'am.discapacidad',
                    'am.vive_con',
                    'am.migrante',
                    'am.nro_caso',
                    'am.fecha as fecha_registro_am' // Renombrar para evitar colisión con persona.fecha si existiera
                ])
                ->where('p.ci', $ci)
                ->first();

            if (!$adultoMayor) {
                return redirect()->route('admin.gestionar-adultomayor.index')
                                ->with('error', 'Adulto mayor no encontrado.');
            }
            
            // Convertir migrante a string '0' o '1' para el select del formulario si es necesario
            if (isset($adultoMayor->migrante)) {
                $adultoMayor->migrante = $adultoMayor->migrante ? '1' : '0';
            }


            return view('Admin.gestionarAdultoMayor.editar.editAdultoMayor', compact('adultoMayor'));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar datos para edición: ' . $e->getMessage());
            return redirect()->route('admin.gestionar-adultomayor.index')
                            ->with('error', 'Error al cargar los datos del adulto mayor.');
        }
    }

    /**
    * Actualizar adulto mayor
    */
    public function actualizarAdultoMayor(Request $request, $ci_original)
    {
        $adultoMayorDb = DB::table('adulto_mayor')->where('ci', $ci_original)->first();

        if (!$adultoMayorDb) {
            return redirect()->back()
                             ->withErrors(['error_actualizacion' => 'Registro de Adulto Mayor no encontrado para el CI proporcionado.'])
                             ->withInput();
        }
        $idAdultoMayor = $adultoMayorDb->id_adulto;

        $validator = Validator::make($request->all(), [
            // Datos de persona
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => 'required|string|max:20|unique:persona,ci,' . $ci_original . ',ci',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            // ===================== CORRECCIÓN CLAVE =====================
            // Se ajusta la regla de validación para que coincida EXACTAMENTE
            // con los valores permitidos en la base de datos.
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            
            // Datos de adulto mayor
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'nullable|in:0,1',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso,' . $idAdultoMayor . ',id_adulto',
            'fecha' => 'required|date',
        ], [
            // Mensajes de error
            'nombres.required' => 'El campo nombres es obligatorio.',
            'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.unique' => 'Este CI ya ha sido registrado por otra persona.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'sexo.required' => 'El campo sexo es obligatorio.',
            'estado_civil.required' => 'El estado civil es obligatorio.',
            'estado_civil.in' => 'El valor seleccionado para el estado civil no es válido.', // Mensaje de error más específico
            'domicilio.required' => 'El domicilio es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'nro_caso.unique' => 'Este número de caso ya ha sido registrado para otro adulto mayor.',
            'fecha.required' => 'La fecha de registro del adulto mayor es obligatoria.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        DB::beginTransaction();

        try {
            $edad = Carbon::parse($request->fecha_nacimiento)->age;

            // 1. Actualizar Persona
            DB::table('persona')
              ->where('ci', $ci_original)
              ->update([
                  'ci' => $request->ci,
                  'primer_apellido' => $request->primer_apellido,
                  'segundo_apellido' => $request->segundo_apellido,
                  'nombres' => $request->nombres,
                  'sexo' => $request->sexo,
                  'fecha_nacimiento' => $request->fecha_nacimiento,
                  'edad' => $edad,
                  'estado_civil' => $request->estado_civil,
                  'domicilio' => $request->domicilio,
                  'telefono' => $request->telefono,
                  'zona_comunidad' => $request->zona_comunidad,
                  'updated_at' => now()
              ]);

            // 2. Preparar y actualizar datos de adulto_mayor
            $adultoMayorData = [
                'ci' => $request->ci,
                'discapacidad' => $request->discapacidad,
                'vive_con' => $request->vive_con,
                'migrante' => $request->migrante == '1' ? true : false,
                'fecha' => $request->fecha,
                'nro_caso' => $request->filled('nro_caso') ? $request->nro_caso : null,
                'updated_at' => now()
            ];

            DB::table('adulto_mayor')
              ->where('id_adulto', $idAdultoMayor)
              ->update($adultoMayorData);

            DB::commit();

            return redirect()->route('gestionar-adultomayor.index')
                             ->with('success', 'Adulto mayor actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando adulto mayor: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                             ->withErrors(['error_actualizacion' => 'Ocurrió un error inesperado al actualizar el registro. Por favor, intente de nuevo.'])
                             ->withInput();
        }
    }
    /**
    * Eliminar adulto mayor
    */
    public function eliminarAdultoMayor($ci)
    {
        DB::beginTransaction();

        try {
            // Verificar si existe la persona y el adulto mayor asociado
            $persona = DB::table('persona')->where('ci', $ci)->first();
            
            if (!$persona) {
                 DB::rollBack(); // No es necesario si no se ha hecho ninguna operación aún, pero por consistencia.
                return redirect()->route('admin.gestionar-adultomayor.index')
                                ->with('error', 'Persona no encontrada.');
            }

            $existeAdultoMayor = DB::table('adulto_mayor')->where('ci', $ci)->exists();
            if (!$existeAdultoMayor) {
                DB::rollBack();
                 // Si la persona existe pero no el adulto mayor, podría ser un estado inconsistente.
                 // O quizás solo se quiere eliminar la persona si no hay adulto mayor.
                 // Por ahora, asumimos que si se intenta eliminar "adulto mayor", ambos deben existir.
                Log::warning('Intento de eliminar adulto mayor no encontrado, pero persona sí existe.', ['ci' => $ci]);
                // Decidir si eliminar solo persona o devolver error. Aquí devuelvo error.
                return redirect()->route('admin.gestionar-adultomayor.index')
                                ->with('error', 'Registro de Adulto Mayor asociado a la persona no encontrado.');
            }


            // Eliminar adulto mayor primero (por la relación de clave foránea si persona.ci es referenciada)
            // O si la FK es de adulto_mayor.ci a persona.ci, el orden es correcto.
            DB::table('adulto_mayor')->where('ci', $ci)->delete();
            
            // Eliminar persona
            DB::table('persona')->where('ci', $ci)->delete();

            DB::commit();

            return redirect()->route('admin.gestionar-adultomayor.index')
                            ->with('success', 'Adulto mayor y datos personales asociados eliminados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando adulto mayor: ' . $e->getMessage());
            
            return redirect()->route('admin.gestionar-adultomayor.index')
                            ->with('error', 'Error al eliminar el adulto mayor. Detalles: ' . $e->getMessage());
        }
    }
    
// MÉTODO ACTUALIZADO PARA EL NUEVO FORMULARIO DE RESPONSABLE DE SALUD
public function storeResponsableSalud(Request $request)
{
    $validator = Validator::make($request->all(), [
        // Pestaña 1: Datos Personales (tabla 'persona')
        'nombres' => 'required|string|max:255',
        'primer_apellido' => 'required|string|max:255',
        'segundo_apellido' => 'nullable|string|max:255',
        'ci' => 'required|string|max:20|unique:persona,ci',
        'fecha_nacimiento' => 'required|date|before_or_equal:today',
        'sexo' => 'required|string|in:F,M,O',
        'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
        'domicilio' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'zona_comunidad' => 'nullable|string|max:100',
        // CORRECCIÓN: Se actualiza la regla 'in' para que coincida con los valores de la base de datos.
        'area_especialidad' => 'required|string|in:Enfermeria,Fisioterapia-Kinesiologia,otro',

        // Pestaña 2: Datos de Usuario (tabla 'users')
        'id_rol' => 'required|integer|exists:rol,id_rol',
        'password' => 'required|string|min:8|confirmed',
        'terms_acceptance' => 'accepted' // Importante para validar el checkbox
    ], [
        // Mensajes personalizados
        'nombres.required' => 'El campo nombres es obligatorio.',
        'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
        'ci.required' => 'El campo CI es obligatorio.',
        'ci.unique' => 'Este CI ya ha sido registrado.',
        'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
        'sexo.required' => 'El campo sexo es obligatorio.',
        'estado_civil.required' => 'El estado civil es obligatorio.',
        'domicilio.required' => 'El domicilio es obligatorio.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'area_especialidad.required' => 'El área de especialidad es obligatoria para el responsable de salud.',
        'area_especialidad.in' => 'El área de especialidad seleccionada no es válida.',
        'id_rol.required' => 'El rol es obligatorio.',
        'id_rol.exists' => 'El rol seleccionado no es válido.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'La confirmación de contraseña no coincide.',
        'terms_acceptance.accepted' => 'Debe aceptar los términos y condiciones.'
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    DB::beginTransaction();

    try {
        // Calcular edad
        $edad = Carbon::parse($request->fecha_nacimiento)->age;

        // 1. Crear Persona
        $persona = Persona::create([
            'ci' => $request->ci,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'nombres' => $request->nombres,
            'sexo' => $request->sexo,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'edad' => $edad,
            'estado_civil' => $request->estado_civil,
            'domicilio' => $request->domicilio,
            'telefono' => $request->telefono,
            'zona_comunidad' => $request->zona_comunidad,
            'area_especialidad' => $request->area_especialidad,
            'area_especialidad_legal' => null, // Aseguramos que el otro campo de especialidad sea nulo
        ]);

        // 2. Crear Usuario
        User::create([
            'ci' => $request->ci,
            'id_rol' => $request->id_rol,
            'name' => $request->nombres . ' ' . $request->primer_apellido,
            'password' => Hash::make($request->password),
            'active' => true,
            'login_attempts' => 0,
        ]);

        DB::commit();

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Responsable de Salud registrado exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error registrando responsable de salud: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
        return redirect()->back()
                         ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar al responsable. Por favor, inténtelo más tarde.'])
                         ->withInput();
    }
}


}
