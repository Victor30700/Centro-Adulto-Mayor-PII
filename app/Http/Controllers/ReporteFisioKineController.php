<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fisioterapia;
use App\Models\AdultoMayor;
use App\Models\HistoriaClinica;
use App\Models\Kinesiologia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
// Para la exportación a Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse; // Importar StreamedResponse
// Importar las clases de estilo necesarias de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color; // Importar la clase Color
// Para la exportación a Word
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\VerticalJc;
use PhpOffice\PhpWord\SimpleType\TblWidth; // Aquí está la clase que contiene la constante PCT

class ReporteFisioKineController extends Controller
{
    /**
     * Muestra el listado de Fichas de Fisioterapia registradas (el reporte).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexReporteFisio(Request $request)
    {
        $search = $request->input('search');
        $fichasFisioterapia = collect(); // Inicializar con una colección vacía
        $totalFichasFisioterapia = Fisioterapia::count();

        try {
            $query = Fisioterapia::with('adulto.persona', 'usuario.persona');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('motivo_consulta', 'like', '%' . $search . '%')
                      ->orWhere('equipos', 'like', '%' . $search . '%')
                      ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                             ->orWhere('ci', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('usuario.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%');
                      });
                });
            }

            $fichasFisioterapia = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@indexReporteFisio: ' . $e->getMessage(), ['exception' => $e]);
            return view('Medico.indexRepFisio', compact('fichasFisioterapia', 'search', 'totalFichasFisioterapia'))
                         ->with('error', 'Ocurrió un error al cargar las fichas de fisioterapia. Por favor, intente de nuevo más tarde.');
        }

        return view('Medico.indexRepFisio', compact('fichasFisioterapia', 'search', 'totalFichasFisioterapia'));
    }

    /**
     * Muestra los detalles de una ficha de Fisioterapia.
     *
     * @param int $cod_fisio
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showFisio($cod_fisio)
    {
        try {
            $fisioterapia = Fisioterapia::with('adulto.persona', 'historiaClinica', 'usuario.persona')->findOrFail($cod_fisio);
            return view('Medico.verDetallesFichaFisio', compact('fisioterapia'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@showFisio: ' . $e->getMessage(), ['cod_fisio' => $cod_fisio, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar los detalles de la ficha de Fisioterapia.');
        }
    }

    /**
     * Elimina una ficha de Fisioterapia.
     *
     * @param int $cod_fisio
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyFisio($cod_fisio)
    {
        try {
            $fisioterapia = Fisioterapia::findOrFail($cod_fisio);
            $fisioterapia->delete();
            return redirect()->route('responsable.fisioterapia.reportefisio.index')->with('success', 'Ficha de Fisioterapia eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Fisioterapia: ' . $e->getMessage(), ['cod_fisio' => $cod_fisio, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Fisioterapia: ' . $e->getMessage());
        }
    }
   public function exportarFichaFisioWordIndividual(int $cod_fisio)
    {
        try {
            // Cargar la ficha de fisioterapia con sus relaciones necesarias
            $fichaFisio = Fisioterapia::with('adulto.persona', 'historiaClinica', 'usuario.persona')->findOrFail($cod_fisio);
            
            // Acceso simplificado a los datos relacionados
            $adulto = $fichaFisio->adulto;
            $persona = optional($adulto)->persona;
            $historiaClinica = $fichaFisio->historiaClinica; 
            $usuarioRegistro = optional($fichaFisio->usuario)->persona;

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            // --- Estilos de fuente ---
            $phpWord->addFontStyle('headerStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('mainTitleStyle', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
            $phpWord->addFontStyle('sectionTitleStyle', ['name' => 'Arial', 'size' => 11, 'bold' => true, 'underline' => 'single']);
            $phpWord->addFontStyle('labelStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);
            $phpWord->addFontStyle('valueStyle', ['name' => 'Arial', 'size' => 10]);
            $phpWord->addFontStyle('checkboxStyle', ['name' => 'Arial', 'size' => 10]); 
            $phpWord->addFontStyle('signatureStyle', ['name' => 'Arial', 'size' => 10, 'bold' => true]);

            // --- Estilos de párrafo ---
            $phpWord->addParagraphStyle('P_Center', ['align' => 'center', 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_Left', ['align' => 'left', 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_Right', ['align' => 'right', 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $phpWord->addParagraphStyle('P_Indent', ['indentation' => ['left' => 360], 'spaceAfter' => 0, 'spaceBefore' => 0]); 
            $phpWord->addParagraphStyle('P_LineSpace10', ['spaceAfter' => 100, 'spaceBefore' => 0]); 
            $phpWord->addParagraphStyle('P_LineSpace20', ['spaceAfter' => 240, 'spaceBefore' => 0]); 


            // --- Encabezado del Documento ---
            $section->addText('DEL ADULTO MAYOR EN EL CTAM ' . Carbon::now()->year, 'headerStyle', 'P_Center');
            $section->addTextBreak(1); 
            $section->addText('FICHA DE INGRESO DEL ADULTO MAYOR EN EL CTAM', 'mainTitleStyle', 'P_Center');
            $section->addTextBreak(2); 


            // --- DATOS PERSONALES ---
            $section->addText('DATOS PERSONALES', 'sectionTitleStyle', 'P_Left');

            // Crear tabla para estructurar los datos personales sin bordes visibles
            // Ajustamos el ancho para que la tabla sea casi de página completa con TWIP.
            // Un ancho de página estándar es ~11906 TWIPs. Usaremos 9000 TWIPs para el contenido,
            // y distribuiremos el resto en las columnas.
            $tableStylePersonal = [
                'borderColor' => 'FFFFFF', 
                'borderSize' => 0,
                'cellMargin' => 0,
                'alignment' => 'left',
                'width' => 9000, // Ancho total de la tabla en TWIPs. Ajusta si sigue saliendo.
                'unit' => TblWidth::TWIP, 
            ];
            $tablePersonal = $section->addTable($tableStylePersonal);

            // Fila 1: Nombre Completo
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('NOMBRE COMPLETO DEL ADULTO MAYOR', 'labelStyle', 'P_Left'); // Ancho para la etiqueta
            $tablePersonal->addCell(6000)->addText((optional($persona)->nombres ?? '') . ' ' . (optional($persona)->primer_apellido ?? '') . ' ' . (optional($persona)->segundo_apellido ?? ''), 'valueStyle', 'P_Left'); // Ancho para el valor
            $tablePersonal->addCell(500)->addText(',', 'valueStyle', 'P_Left'); // Pequeño ancho para la coma

            // Fila 2: Fecha de Nacimiento, Edad
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('FECHA DE NACIMIENTO', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(2500)->addText((optional($persona)->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') : ''), 'valueStyle', 'P_Left');
            $tablePersonal->addCell(1000)->addText('EDAD', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(2500)->addText((optional($persona)->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->age : ''), 'valueStyle', 'P_Left');
            $tablePersonal->addCell(500)->addText(',', 'valueStyle', 'P_Left');

            // Fila 3: Número de Documento de Identidad, Teléfono
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('NÚMERO DE DOCUMENTO DE IDENTIDAD', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(2500)->addText(optional($persona)->ci ?? '', 'valueStyle', 'P_Left');
            $tablePersonal->addCell(1000)->addText('NÚMERO DE TELÉFONO', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(2500)->addText(optional($persona)->celular ?? 'N/A', 'valueStyle', 'P_Left');
            $tablePersonal->addCell(500)->addText(',', 'valueStyle', 'P_Left');

            // Fila 4: Dirección de Domicilio
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('DIRECCIÓN DE DOMICILIO', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(6000)->addText((optional($persona)->zona_comunidad ?? 'N/A') . ', ' . (optional($persona)->direccion ?? 'N/A'), 'valueStyle', 'P_Left');
            $tablePersonal->addCell(500)->addText(',', 'valueStyle', 'P_Left');

            // Fila 5: Con Quien Vive
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('CON QUIEN VIVE', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(6500)->addText(optional($adulto)->con_quien_vive ?? 'N/A', 'valueStyle', 'P_Left');

            // Fila 6: Estado Civil
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('ESTADO CIVIL', 'labelStyle', 'P_Left');
            $estadoCivil = optional($adulto)->estado_civil;
            $tablePersonal->addCell(1800)->addText('(' . ($estadoCivil == 'Casado/a' ? 'X' : ' ') . ') Casado/a', 'checkboxStyle', 'P_Left');
            $tablePersonal->addCell(1800)->addText('(' . ($estadoCivil == 'Soltero/a' ? 'X' : ' ') . ') Soltero/a', 'checkboxStyle', 'P_Left');
            $tablePersonal->addCell(1800)->addText('(' . ($estadoCivil == 'Viudo/a' ? 'X' : ' ') . ') Viudo/a', 'checkboxStyle', 'P_Left');
            $tablePersonal->addCell(1800)->addText('(' . ($estadoCivil == 'Divorciado/a' ? 'X' : ' ') . ') Divorciado/a', 'checkboxStyle', 'P_Left');

            // Fila 7: Grado de Instrucción
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('GRADO DE INSTRUCCIÓN', 'labelStyle', 'P_Left');
            $gradoInstruccion = optional($adulto)->grado_instruccion;
            $tablePersonal->addCell(2000)->addText('(' . ($gradoInstruccion == 'Sin instrucción' ? 'X' : ' ') . ') Sin instrucción', 'checkboxStyle', 'P_Left');
            $tablePersonal->addCell(1500)->addText('(' . ($gradoInstruccion == 'Primaria' ? 'X' : ' ') . ') Primaria', 'checkboxStyle', 'P_Left');
            $tablePersonal->addCell(2000)->addText('(' . ($gradoInstruccion == 'Secundaria' ? 'X' : ' ') . ') Secundaria', 'checkboxStyle', 'P_Left');
            $tablePersonal->addCell(2000)->addText('(' . ($gradoInstruccion == 'Superior' ? 'X' : ' ') . ') Superior', 'checkboxStyle', 'P_Left');

            // Fila 8: Ocupación Actual
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('OCUPACIÓN ACTUAL', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(6500)->addText(optional($adulto)->ocupacion_actual ?? 'N/A', 'valueStyle', 'P_Left');

            // Fila 9: Números de Emergencia
            $tablePersonal->addRow();
            $tablePersonal->addCell(3000)->addText('NÚMEROS DE EMERGENCIA', 'labelStyle', 'P_Left');
            $tablePersonal->addCell(6500)->addText(optional($adulto)->numero_emergencia ?? 'N/A', 'valueStyle', 'P_Left');
            $section->addTextBreak(2);


            // --- SITUACIÓN DE SALUD ---
            $section->addText('SITUACIÓN DE SALUD', 'sectionTitleStyle', 'P_Left');
            $section->addTextBreak(1); 

            $section->addText('ENFERMEDADES ACTUALES', 'labelStyle', 'P_Left');
            $section->addText('(' . ' ' . ') Hipertensión arterial', 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') Diabetes.', 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') Artrosis.', 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') Osteoporosis.', 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') Parkinson.', 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') No refiere.', 'checkboxStyle', 'P_Indent');
            $section->addText('Otras: ' . ($fichaFisio->diagnostico ?? $fichaFisio->observaciones ?? 'N/A'), 'valueStyle', 'P_Indent');
            $section->addTextBreak(1); 

            $section->addText('ALERGIAS', 'labelStyle', 'P_Left');
            $section->addText('(' . ' ' . ') Medicamentos. Indicar: ' . ($fichaFisio->observaciones ?? 'N/A'), 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') Alimentos. Indicar: ' . ($fichaFisio->observaciones ?? 'N/A'), 'checkboxStyle', 'P_Indent');
            $section->addText('(' . ' ' . ') No refiere', 'checkboxStyle', 'P_Indent');
            $section->addTextBreak(2);

            // --- PLAN DE PARTICIPACIÓN INDIVIDUAL. O GRUPAL ---
            $section->addText('PLAN DE PARTICIPACIÓN INDIVIDUAL. O GRUPAL', 'sectionTitleStyle', 'P_Left');
            $section->addTextBreak(1);

            $section->addText('ATENCIÓN FISIOTERAPIA KINESIOLOGÍA', 'labelStyle', 'P_Left');
            $section->addText('FECHA DE PROGRAMACIÓN: ' . (optional($fichaFisio->fecha_programacion) ? Carbon::parse($fichaFisio->fecha_programacion)->format('d/m/Y') : 'N/A'), 'valueStyle', 'P_Left');
            $section->addText('MOTIVO DE CONSULTA: ' . ($fichaFisio->motivo_consulta ?? 'N/A'), 'valueStyle', 'P_Left');
            $section->addText('SOLICITUD ATENCIÓN: ' . ($fichaFisio->solicitud_atencion ?? 'N/A'), 'valueStyle', 'P_Left'); 
            $section->addTextBreak(1);

            $section->addText('EQUIPOS', 'labelStyle', 'P_Left');
            $equiposFisio = strval($fichaFisio->equipos);

            $section->addText('(' . (mb_strpos(mb_strtoupper($equiposFisio), 'ELECTRO ESTIMULADOR') !== false ? 'X' : ' ') . ') ELECTRO ESTIMULADOR', 'checkboxStyle', 'P_Indent');
            $section->addText('(' . (mb_strpos(mb_strtoupper($equiposFisio), 'ULTRASONIDO') !== false ? 'X' : ' ') . ') ULTRASONIDO', 'checkboxStyle', 'P_Indent');
            $section->addText('OTROS: ' . (!empty($equiposFisio) ? $equiposFisio : 'N/A'), 'valueStyle', 'P_Indent');
            $section->addTextBreak(2);


            // --- Firmas al final ---
            $section->addTextBreak(3);
            $section->addText('____________________________', 'NormalText', 'P_Center');
            $section->addText('FIRMA FISIOTERAPEUTA', 'signatureStyle', 'P_Center');
            $section->addTextBreak(1);
            $section->addText('____________________________', 'NormalText', 'P_Center');
            $section->addText('FIRMA ENCARGAD@ OF. ADULTO MAYOR', 'signatureStyle', 'P_Center');

            // Preparar el archivo para la descarga
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

            $nombreAdulto = (optional($persona)->nombres ?? '') . '_' . (optional($persona)->primer_apellido ?? '');
            $fileName = 'ficha_fisioterapia_' . $fichaFisio->cod_fisio . '_' . $nombreAdulto . '_' . Carbon::now()->format('Ymd') . '.docx';
            $fileName = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName); 
            $fileName = substr($fileName, 0, 200); 

            $response = new StreamedResponse(function() use ($objWriter) {
                $objWriter->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error al generar Word de la Ficha de Fisioterapia individual (cod_fisio: ' . $cod_fisio . '): ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al generar el Word de la Ficha de Fisioterapia: ' . $e->getMessage());
        }
    }

    // ###########################################################################################################################
    // REPORTES KINESIOLOGIA
        // --- MÉTODOS PARA KINESIOLOGÍA ---

    /**
     * Muestra el listado de Fichas de Kinesiología registradas (el reporte).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexReporteKine(Request $request)
    {
        $search = $request->input('search');
        $fichasKinesiologia = collect(); // Inicializar con una colección vacía
        $totalFichasKinesiologia = Kinesiologia::count();

        try {
            $query = Kinesiologia::with('adulto.persona', 'usuario.persona');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->orWhereHas('adulto.persona', function ($qr) use ($search) {
                            $qr->where('nombres', 'like', '%' . $search . '%')
                               ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                               ->orWhere('ci', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('usuario.persona', function ($qr) use ($search) {
                            $qr->where('nombres', 'like', '%' . $search . '%')
                               ->orWhere('primer_apellido', 'like', '%' . $search . '%');
                        });
                });
            }

            $fichasKinesiologia = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@indexReporteKine: ' . $e->getMessage(), ['exception' => $e]);
            return view('Medico.indexRepKine', compact('fichasKinesiologia', 'search', 'totalFichasKinesiologia'))
                           ->with('error', 'Ocurrió un error al cargar las fichas de kinesiología. Por favor, intente de nuevo más tarde.');
        }

        return view('Medico.indexRepKine', compact('fichasKinesiologia', 'search', 'totalFichasKinesiologia'));
    }

    /**
     * Muestra el formulario para editar una ficha de Kinesiología.
     *
     * @param int $cod_kine
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editKine($cod_kine)
    {
        try {
            // Asegúrate de cargar historiaClinica aquí también
            $kinesiologia = Kinesiologia::with('adulto.persona', 'historiaClinica')->findOrFail($cod_kine);
            $adulto = $kinesiologia->adulto;
            $historiaClinica = $kinesiologia->historiaClinica;

            return view('Medico.registrarFichaKine', compact('kinesiologia', 'adulto', 'historiaClinica'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@editKine: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar la ficha de Kinesiología para edición.');
        }
    }

    /**
     * Actualiza una ficha de Kinesiología existente.
     *
     * @param Request $request
     * @param int $cod_kine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateKine(Request $request, $cod_kine)
    {
        // Validación solo para los campos específicos de Kinesiología (booleanos)
        $request->validate([
            'entrenamiento_funcional' => 'boolean',
            'gimnasio_maquina' => 'boolean',
            'aquafit' => 'boolean',
            'hidroterapia' => 'boolean',
            'manana' => 'boolean',
            'tarde' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $kinesiologia = Kinesiologia::findOrFail($cod_kine);

            $kinesiologia->update([
                'entrenamiento_funcional' => $request->has('entrenamiento_funcional'),
                'gimnasio_maquina' => $request->has('gimnasio_maquina'),
                'aquafit' => $request->has('aquafit'),
                'hidroterapia' => $request->has('hidroterapia'),
                'manana' => $request->has('manana'),
                'tarde' => $request->has('tarde'),
                // Los campos de texto/fecha de Fisioterapia NO se actualizan desde este formulario.
                // Se mantienen sus valores existentes en la base de datos si existen.
            ]);

            DB::commit();

            return redirect()->route('reportekine.index')->with('success', 'Ficha de Kinesiología actualizada exitosamente.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación al actualizar Ficha de Kinesiología: ', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al actualizar Ficha de Kinesiología: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al actualizar la Ficha de Kinesiología: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de una ficha de Kinesiología.
     *
     * @param int $cod_kine
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showKine($cod_kine)
    {
        try {
            // Asegurarse de cargar historiaClinica aquí también
            $kinesiologia = Kinesiologia::with('adulto.persona', 'historiaClinica', 'usuario.persona')->findOrFail($cod_kine);
            return view('Medico.verDetallesKine', compact('kinesiologia'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteFisioKineController@showKine: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar los detalles de la ficha de Kinesiología.');
        }
    }

    /**
     * Elimina una ficha de Kinesiología.
     *
     * @param int $cod_kine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyKine($cod_kine)
    {
        try {
            $kinesiologia = Kinesiologia::findOrFail($cod_kine);
            $kinesiologia->delete();
            return redirect()->route('reportekine.index')->with('success', 'Ficha de Kinesiología eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Kinesiología: ' . $e->getMessage(), ['cod_kine' => $cod_kine, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Kinesiología: ' . $e->getMessage());
        }
    }

    /**
     * Exporta los registros de Kinesiología a un archivo Excel.
     *
     * @param Request $request La solicitud HTTP que puede contener parámetros de filtro.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarFichaKineExcel(Request $request)
    {
        try {
            // Obtener los parámetros de búsqueda del request
            $search = $request->input('search');

            // Construir la consulta para obtener todas las fichas de kinesiología
            $query = Kinesiologia::with('adulto.persona', 'historiaClinica', 'usuario.persona');

            // Aplicar filtros de búsqueda si existen
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('adulto.persona', function ($qr) use ($search) {
                        $qr->where('nombres', 'like', '%' . $search . '%')
                           ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                           ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                           ->orWhere('ci', 'like', '%' . $search . '%');
                    });
                });
            }

            $fichasKinesiologia = $query->orderBy('created_at', 'asc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Fichas Kinesiología');

            // --- Encabezado General (Filas 1-4) ---
            // GOBIERNO AUTÓNOMO MUNICIPAL DE TARIJA
            $sheet->mergeCells('A1:M1'); // Fusionar de A a M
            $sheet->setCellValue('A1', 'GOBIERNO AUTÓNOMO MUNICIPAL DE TARIJA');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // OFICINAS DE ASISTENCIA GENERACIONAL Y PERSONAS ADULTAS MAYORES
            $sheet->mergeCells('A2:M2'); // Fusionar de A a M
            $sheet->setCellValue('A2', 'OFICINAS DE ASISTENCIA GENERACIONAL Y PERSONAS ADULTAS MAYORES');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Fila 3 vacía para separación
            $sheet->getRowDimension(3)->setRowHeight(15); // Altura para espacio

            // PLANILLA DE ATENCION EN KINESIOLOGIA CORRESPONDIENTE AL MES: ..........................
            Carbon::setLocale('es'); // Asegura que el nombre del mes sea en español
            $currentMonthName = Carbon::now()->locale('es')->monthName;
            $sheet->mergeCells('A4:M4'); // Fusionar de A a M
            $sheet->setCellValue('A4', 'PLANILLA DE ATENCION EN KINESIOLOGIA CORRESPONDIENTE AL MES: ' . mb_strtoupper($currentMonthName));
            $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12); // Tamaño ajustado para cabecera de tabla
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Fila 5 vacía para separación antes de la tabla
            $sheet->getRowDimension(5)->setRowHeight(15);

            // --- Cabeceras de la Tabla (Filas 6-7) ---
            // Fila 6: Cabeceras principales
            $sheet->setCellValue('A6', 'N°');
            $sheet->setCellValue('B6', 'NOMBRE Y APELLIDO');
            $sheet->setCellValue('C6', 'SEXO'); // Esta será la cabecera fusionada para F y M
            $sheet->setCellValue('E6', 'SERVICIOS REALIZADO'); // Esta será la cabecera fusionada
            $sheet->setCellValue('I6', 'TURNO'); // Esta será la cabecera fusionada
            $sheet->setCellValue('K6', 'LUGAR DE NACIMIENTO');
            $sheet->setCellValue('L6', 'BARRIO');
            $sheet->setCellValue('M6', 'FIRMA');

            // Fila 7: Sub-cabeceras (datos específicos dentro de las categorías fusionadas)
            $sheet->setCellValue('C7', 'F');
            $sheet->setCellValue('D7', 'M');
            $sheet->setCellValue('E7', 'ENTRENAMIENTO FUNCIONAL');
            $sheet->setCellValue('F7', 'GIMNASIO MAQUINAS');
            $sheet->setCellValue('G7', 'AQUAFIT');
            $sheet->setCellValue('H7', 'HIDROTERAPIA');
            $sheet->setCellValue('I7', 'MAÑANA');
            $sheet->setCellValue('J7', 'TARDE');

            // Fusionar celdas para las cabeceras de la tabla según el diseño
            $sheet->mergeCells('A6:A7'); // N°
            $sheet->mergeCells('B6:B7'); // NOMBRE Y APELLIDO
            $sheet->mergeCells('C6:D6'); // SEXO
            $sheet->mergeCells('E6:H6'); // SERVICIOS REALIZADO (abarcando E,F,G,H de la fila 6)
            $sheet->mergeCells('I6:J6'); // TURNO (abarcando I,J de la fila 6)
            $sheet->mergeCells('K6:K7'); // LUGAR DE NACIMIENTO
            $sheet->mergeCells('L6:L7'); // BARRIO
            $sheet->mergeCells('M6:M7'); // FIRMA

            // Estilos para las cabeceras de la tabla
            $headerStyle = [
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true, // Asegura que el texto largo se ajuste
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0'], // Fondo gris claro
                ],
            ];
            $sheet->getStyle('A6:M7')->applyFromArray($headerStyle);
            $sheet->getRowDimension(6)->setRowHeight(30); // Altura para cabeceras principales
            $sheet->getRowDimension(7)->setRowHeight(30); // Altura para sub-cabeceras

            // --- Configurar la orientación del texto vertical para las sub-cabeceras ---
            $verticalTextColumns = ['E', 'F', 'G', 'H', 'I', 'J']; // Las columnas que tienen texto vertical en la fila 7
            foreach ($verticalTextColumns as $column) {
                $sheet->getStyle($column . '7')->getAlignment()->setTextRotation(90);
            }
            // Para las cabeceras principales que son verticales en la imagen
            $sheet->getStyle('K6')->getAlignment()->setTextRotation(90); // Lugar de Nacimiento
            $sheet->getStyle('L6')->getAlignment()->setTextRotation(90); // Barrio
            $sheet->getStyle('M6')->getAlignment()->setTextRotation(90); // Firma

            // --- Datos de los Registros (a partir de la fila 8) ---
            $currentRow = 8;
            foreach ($fichasKinesiologia as $index => $fichaKine) {
                $sheet->setCellValue('A' . $currentRow, $index + 1); // N°
                $sheet->setCellValue('B' . $currentRow, optional($fichaKine->adulto->persona)->nombres . ' ' . optional($fichaKine->adulto->persona)->primer_apellido . ' ' . optional($fichaKine->adulto->persona)->segundo_apellido);
                $sheet->setCellValue('C' . $currentRow, (optional($fichaKine->adulto->persona)->sexo == 'F' ? 'X' : '')); // Sexo F
                $sheet->setCellValue('D' . $currentRow, (optional($fichaKine->adulto->persona)->sexo == 'M' ? 'X' : '')); // Sexo M
                $sheet->setCellValue('E' . $currentRow, ($fichaKine->entrenamiento_funcional ? 'X' : ''));
                $sheet->setCellValue('F' . $currentRow, ($fichaKine->gimnasio_maquina ? 'X' : ''));
                $sheet->setCellValue('G' . $currentRow, ($fichaKine->aquafit ? 'X' : ''));
                $sheet->setCellValue('H' . $currentRow, ($fichaKine->hidroterapia ? 'X' : ''));
                $sheet->setCellValue('I' . $currentRow, ($fichaKine->manana ? 'X' : ''));
                $sheet->setCellValue('J' . $currentRow, ($fichaKine->tarde ? 'X' : ''));
                $sheet->setCellValue('K' . $currentRow, optional($fichaKine->historiaClinica)->lugar_nacimiento ?? 'N/A');
                $sheet->setCellValue('L' . $currentRow, (optional($fichaKine->adulto->persona)->zona_comunidad ?? 'N/A') . ' / ' . (optional($fichaKine->adulto->persona)->domicilio ?? 'N/A'));
                $sheet->setCellValue('M' . $currentRow, ''); // Columna de Firma (vacía para que firmen a mano)

                $currentRow++;
            }

            // Aplicar bordes a las celdas de datos si hay registros
            if (count($fichasKinesiologia) > 0) {
                $dataStyle = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                    ],
                ];
                $sheet->getStyle('A8:M' . ($currentRow - 1))->applyFromArray($dataStyle);
                // Ajustar alineación izquierda para la columna de nombre completo
                $sheet->getStyle('B8:B' . ($currentRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            } else {
                // Si no hay datos, mostrar un mensaje en la tabla
                $sheet->mergeCells('A8:M8');
                $sheet->setCellValue('A8', 'No se encontraron fichas de kinesiología registradas.');
                $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // CORRECCIÓN: Usar un valor ARGB directo para el color gris.
                $sheet->getStyle('A8')->getFont()->setItalic(true)->setColor(new Color('FF808080')); // Un gris medio
                $currentRow = 9; // Ajustar la fila actual para las firmas si no hay datos
            }
            
            // --- Autoajustar el ancho de las columnas ---
            // Las columnas con texto vertical necesitan un ancho fijo o muy pequeño para verse bien
            // Ajusta estos valores según sea necesario para que el texto vertical se lea bien
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setWidth(35); // Ampliar para Nombre y Apellido
            $sheet->getColumnDimension('C')->setWidth(4); // F
            $sheet->getColumnDimension('D')->setWidth(4); // M
            $sheet->getColumnDimension('E')->setWidth(4); // Entrenamiento Funcional (vertical)
            $sheet->getColumnDimension('F')->setWidth(4); // Gimnasio Maquinas (vertical)
            $sheet->getColumnDimension('G')->setWidth(4); // Aquafit (vertical)
            $sheet->getColumnDimension('H')->setWidth(4); // Hidroterapia (vertical)
            $sheet->getColumnDimension('I')->setWidth(4); // Mañana (vertical)
            $sheet->getColumnDimension('J')->setWidth(4); // Tarde (vertical)
            $sheet->getColumnDimension('K')->setWidth(15); // Lugar de Nacimiento (vertical)
            $sheet->getColumnDimension('L')->setWidth(15); // Barrio (vertical)
            $sheet->getColumnDimension('M')->setWidth(15); // Firma (vertical)

            // --- Firmas al final ---
            $currentRow += 3; // Espacio entre la tabla y las firmas
            $sheet->setCellValue('C' . $currentRow, '____________________________');
            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('C' . ($currentRow + 1) . ':F' . ($currentRow + 1));
            $sheet->setCellValue('C' . ($currentRow + 1), 'FIRMA ADULTO MAYOR');
            $sheet->getStyle('C' . ($currentRow + 1))->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->setCellValue('G' . $currentRow, '____________________________');
            $sheet->getStyle('G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('G' . ($currentRow + 1) . ':J' . ($currentRow + 1));
            $sheet->setCellValue('G' . ($currentRow + 1), 'FIRMA KINESIOLOGO/A'); // Cambiado a Kinesiólogo/a
            $sheet->getStyle('G' . ($currentRow + 1))->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $sheet->setCellValue('K' . $currentRow, '____________________________');
            $sheet->getStyle('K' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->mergeCells('K' . ($currentRow + 1) . ':M' . ($currentRow + 1));
            $sheet->setCellValue('K' . ($currentRow + 1), 'FIRMA ENCARGAD@ OF. ADULTO MAYOR');
            $sheet->getStyle('K' . ($currentRow + 1))->applyFromArray([
                'font' => ['bold' => true, 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $fileName = 'reporte_kinesiologia_' . Carbon::now()->format('Ymd_His') . '.xlsx';

            $writer = new Xlsx($spreadsheet);

            $response = new StreamedResponse(function() use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            Log::error('Error al generar Excel del Reporte de Kinesiología: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Error al generar el Excel del Reporte de Kinesiología: ' . $e->getMessage());
        }
    }
}