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

    public function storeUsuarioLegal(Request $request)
    {
        Log::info('Iniciando registro de usuario legal', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => 'required|string|max:20|unique:persona,ci|regex:/^\d+$/',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            'area_especialidad_legal' => 'required|string|in:Asistente Social,Psicologia,Derecho',
            'id_rol' => 'required|integer|exists:rol,id_rol',
            'password' => 'required|string|min:8|confirmed',
            'terms_acceptance' => 'required|accepted',
        ], [
            'area_especialidad_legal.required' => 'El área de especialidad es obligatoria.',
            // ... (Tus otros mensajes de error personalizados)
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $edad = Carbon::parse($request->fecha_nacimiento)->age;

            // Se guarda el valor en la columna correcta, y la otra se deja nula.
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
                'area_especialidad_legal' => $request->area_especialidad_legal,
            ]);

            User::create([
                'ci' => $request->ci,
                'id_rol' => $request->id_rol,
                'name' => $request->nombres . ' ' . $request->primer_apellido,
                'password' => Hash::make($request->password),
                'active' => true,
                'login_attempts' => 0,
            ]);

            DB::commit();

            return redirect()->route('admin.dashboard')->with('success', 'Usuario Legal registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrando usuario legal: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withErrors(['error_registro' => 'Error interno del servidor: ' . $e->getMessage()])
                ->withInput();
        }
    }
    
/**
 * --- FUNCIÓN MODIFICADA ---
 * Almacena un nuevo Adulto Mayor o detecta si ya existe uno borrado para ofrecer restauración.
 */
    public function storeAdultoMayor(Request $request)
    {
        Log::info('Iniciando registro de adulto mayor', ['data' => $request->all()]);

        // Mantenemos tu validador, pero quitamos 'unique' de la regla 'ci'
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => 'required|string|max:20', // Regla 'unique' eliminada de aquí
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'nullable|in:0,1',
            'nro_caso' => 'nullable|string|max:50|unique:adulto_mayor,nro_caso',
            'fecha' => 'required|date',
        ], [
            // Se conservan todos tus mensajes personalizados
            'nombres.required' => 'El campo nombres es obligatorio.',
            'primer_apellido.required' => 'El campo primer apellido es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.unique' => 'Este CI ya ha sido registrado.', // Este mensaje ahora se mostrará manualmente
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // --- NUEVA LÓGICA DE DETECCIÓN INTELIGENTE ---
        $personaExistente = Persona::withTrashed()->where('ci', $request->ci)->first();

        if ($personaExistente) {
            if ($personaExistente->trashed()) {
                // El CI existe en la papelera, damos la opción de restaurar.
                $errorMessage = "El CI '{$request->ci}' ya existe pero fue eliminado. Puede restaurarlo y actualizar sus datos con la información de este formulario.";
                return redirect()->back()
                                 ->with('restore_error', $errorMessage)
                                 ->with('trashed_persona', $personaExistente->load('adultoMayor'))
                                 ->withInput();
            } else {
                // El CI existe y está activo, devolvemos el error 'unique' manualmente.
                return redirect()->back()->withErrors(['ci' => 'Este CI ya está registrado y activo en el sistema.'])->withInput();
            }
        }

        // --- LÓGICA DE CREACIÓN ORIGINAL (si el CI es nuevo) ---
        DB::beginTransaction();
        try {
            $edad = Carbon::parse($request->fecha_nacimiento)->age;
            
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
            ]);

            $adultoMayorData = $request->only(['discapacidad', 'vive_con', 'nro_caso', 'fecha']);
            $adultoMayorData['ci'] = $persona->ci;
            $adultoMayorData['migrante'] = $request->migrante == '1' ? true : false;
            AdultoMayor::create($adultoMayorData);

            DB::commit();

            return redirect()->route('gestionar-adultomayor.index')
                             ->with('success', 'Adulto Mayor registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error registrando adulto mayor: ' . $e->getMessage());
            return redirect()->back()
                             ->withErrors(['error_registro' => 'Ocurrió un error interno al registrar.'])
                             ->withInput();
        }
    }

    /**
     * --- FUNCIÓN NUEVA ---
     * Pega esta función justo debajo de la función storeAdultoMayor.
     */
    public function restoreAndupdateAdultoMayor(Request $request, $ci)
    {
        Log::info('Iniciando restauración de adulto mayor', ['ci' => $ci]);

        // Se usa la misma validación y mensajes que en la función store
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
            'ci' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'sexo' => 'required|string|in:F,M,O',
            'estado_civil' => 'required|string|in:casado,divorciado,soltero,otro',
            'domicilio' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'zona_comunidad' => 'nullable|string|max:100',
            'discapacidad' => 'nullable|string',
            'vive_con' => 'nullable|string|max:200',
            'migrante' => 'nullable|in:0,1',
            // La regla 'unique' para nro_caso ignora el registro que estamos restaurando.
            'nro_caso' => ['nullable', 'string', 'max:50', Rule::unique('adulto_mayor', 'nro_caso')->ignore($ci, 'ci')],
            'fecha' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $persona = Persona::withTrashed()->where('ci', $ci)->firstOrFail();
            $adultoMayor = $persona->adultoMayor()->withTrashed()->firstOrFail();

            $persona->restore();
            $adultoMayor->restore();
            
            $edad = Carbon::parse($request->fecha_nacimiento)->age;
            
            $personaData = $request->only(['ci', 'nombres', 'primer_apellido', 'segundo_apellido', 'sexo', 'fecha_nacimiento', 'estado_civil', 'domicilio', 'telefono', 'zona_comunidad']);
            $personaData['edad'] = $edad;
            $persona->update($personaData);
            
            $adultoMayorData = $request->only(['discapacidad', 'vive_con', 'nro_caso', 'fecha']);
            $adultoMayorData['migrante'] = $request->migrante == '1' ? true : false;
            $adultoMayor->update($adultoMayorData);
            
            DB::commit();
            
            return redirect()->route('gestionar-adultomayor.index')
                             ->with('success', "El registro de CI {$ci} ha sido restaurado y actualizado.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error restaurando adulto mayor con CI {$ci}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al intentar restaurar.')->withInput();
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
            // CORRECCIÓN 1: Se busca usando el Modelo para que SoftDeletes funcione.
            // firstOrFail() se encarga de verificar si existe. Si no, falla de forma segura.
            $adultoMayor = AdultoMayor::where('ci', $ci)->firstOrFail();
            $persona = $adultoMayor->persona;

            // Si la persona no existiera por alguna inconsistencia de datos, falla.
            if (!$persona) {
                throw new \Exception("Datos inconsistentes: No se encontró la persona asociada al CI {$ci}.");
            }

            // CORRECCIÓN 2: Se usa el método delete() de los modelos para el borrado lógico.
            $adultoMayor->delete();
            $persona->delete();

            DB::commit();

            Log::info("Adulto mayor con CI {$ci} ha sido eliminado (lógicamente).");

            // CORRECCIÓN 3: Se usa el nombre de ruta EXACTO Y CONFIRMADO del archivo web.php
            return redirect()->route('gestionar-adultomayor.index')
                             ->with('success', 'Adulto mayor y datos personales asociados eliminados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error eliminando adulto mayor: ' . $e->getMessage());
            
            // También se corrige el nombre de la ruta aquí.
            return redirect()->route('gestionar-adultomayor.index')
                             ->with('error', 'Error al eliminar el adulto mayor. ' . $e->getMessage());
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
