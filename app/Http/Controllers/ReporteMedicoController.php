<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enfermeria;
use App\Models\HistoriaClinica;
use App\Models\ExamenComplementario;
use App\Models\MedicamentoRecetado;
use App\Models\AdultoMayor;
use App\Models\User; // Asegúrate de que esta línea esté presente si tu modelo User está en App\Models
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReporteMedicoController extends Controller
{
    /**
     * Muestra el listado de reportes de Atención de Enfermería con filtros y buscador.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $search = $request->input('search');

        $totalAdultos = AdultoMayor::count();
        $reportes = collect(); 
        $totalFichasEnfermeria = 0; 

        try {
            $query = Enfermeria::with('adulto.persona', 'usuario.persona');

            // Aplicar filtros de fecha
            if ($fecha_inicio) {
                $query->whereDate('created_at', '>=', $fecha_inicio);
            }
            if ($fecha_fin) {
                $query->whereDate('created_at', '<=', $fecha_fin);
            }

            // Aplicar búsqueda general
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('presion_arterial', 'like', '%' . $search . '%')
                      ->orWhere('temperatura', 'like', '%' . $search . '%')
                      ->orWhere('derivacion', 'like', '%' . $search . '%')
                      ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                             ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                             ->orWhere('ci', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('usuario.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%');
                      });
                });
            }

            $reportes = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
            $totalFichasEnfermeria = Enfermeria::count(); 
            
        } catch (\Exception $e) {
            Log::error('Error en ReporteMedicoController@index (Enfermería): ' . $e->getMessage(), ['exception' => $e]);
            return view('Medico.indexRep', compact('reportes', 'fecha_inicio', 'fecha_fin', 'search', 'totalAdultos', 'totalFichasEnfermeria', 'request'))
                        ->with('error', 'Ocurrió un error al cargar los reportes de enfermería. Por favor, intente de nuevo más tarde.');
        }

        return view('Medico.indexRep', compact('reportes', 'fecha_inicio', 'fecha_fin', 'search', 'totalAdultos', 'totalFichasEnfermeria', 'request'));
    }

    /**
     * Muestra el detalle de un reporte de Atención de Enfermería.
     *
     * @param int $cod_enf
     * @return \Illuminate\View\View
     */
    public function showAtencionEnfermeria($cod_enf)
    {
        try {
            $fichaEnfermeria = Enfermeria::with('adulto.persona', 'usuario.persona')->findOrFail($cod_enf);
            return view('Medico.verDetallesEnfermeria', compact('fichaEnfermeria'));
        } catch (\Exception $e) {
            Log::error('Error en ReporteMedicoController@showAtencionEnfermeria: ' . $e->getMessage(), ['cod_enf' => $cod_enf, 'exception' => $e]);
            return back()->with('error', 'No se pudo cargar el detalle de la Ficha de Atención de Enfermería.');
        }
    }

    /**
     * Elimina una ficha de Atención de Enfermería.
     * @param int $cod_enf
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAtencionEnfermeria($cod_enf)
    {
        try {
            $enfermeria = Enfermeria::findOrFail($cod_enf);
            $enfermeria->delete();
            return redirect()->route('responsable.enfermeria.reportes_enfermeria.index')->with('success', 'Ficha de Atención de Enfermería eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar Ficha de Atención de Enfermería: ' . $e->getMessage(), ['cod_enf' => $cod_enf, 'exception' => $e]);
            return back()->with('error', 'Error al eliminar la Ficha de Atención de Enfermería: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la vista previa de impresión para las atenciones de enfermería.
     * Incluye filtros de fecha y búsqueda.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function imprimirAtencionesEnfermeria(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $search = $request->input('search');
        
        // Determinar el mes para el título del reporte
        $currentMonth = Carbon::now()->month;
        if ($fecha_fin) {
            $currentMonth = Carbon::parse($fecha_fin)->month;
        }

        $query = Enfermeria::with('adulto.persona', 'usuario.persona');

        // Aplicar los mismos filtros que en la vista principal
        if ($fecha_inicio) {
            $query->whereDate('created_at', '>=', $fecha_inicio);
        }
        if ($fecha_fin) {
            $query->whereDate('created_at', '<=', $fecha_fin);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('presion_arterial', 'like', '%' . $search . '%')
                  ->orWhere('temperatura', 'like', '%' . $search . '%')
                  ->orWhere('derivacion', 'like', '%' . $search . '%')
                  ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                      $qr->where('nombres', 'like', '%' . $search . '%')
                         ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                         ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                         ->orWhere('ci', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('usuario.persona', function ($qr) use ($search) {
                      $qr->where('nombres', 'like', '%' . $search . '%')
                         ->orWhere('primer_apellido', 'like', '%' . $search . '%');
                  });
            });
        }

        // Recuperar TODOS los registros que coinciden con los filtros (sin paginación)
        $atenciones = $query->orderBy('created_at', 'asc')->get();

        return view('Medico.verReportes', compact('atenciones', 'request', 'currentMonth'));
    }


    /**
     * Exporta los registros de Atención de Enfermería a un archivo Excel usando PhpOffice/PhpSpreadsheet directamente.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarAtencionesEnfermeriaExcel(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $search = $request->input('search');

        $query = Enfermeria::with('adulto.persona', 'usuario.persona');

        // Aplicar los mismos filtros que en la vista principal
        if ($fecha_inicio) {
            $query->whereDate('created_at', '>=', $fecha_inicio);
        }
        if ($fecha_fin) {
            $query->whereDate('created_at', '<=', $fecha_fin);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('presion_arterial', 'like', '%' . $search . '%')
                    ->orWhere('temperatura', 'like', '%' . $search . '%')
                    ->orWhere('derivacion', 'like', '%' . $search . '%')
                    ->orWhere('lavado_oidos', 'like', '%' . $search . '%')
                    ->orWhere('orientacion_tratamiento', 'like', '%' . $search . '%')
                    ->orWhere('adm_medicamentos', 'like', '%' . $search . '%')
                    ->orWhere('curacion', 'like', '%' . $search . '%') // Añadido para búsqueda
                    ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                        $qr->where('nombres', 'like', '%' . $search . '%')
                            ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                            ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                            ->orWhere('ci', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('usuario.persona', function ($qr) use ($search) {
                        $qr->where('nombres', 'like', '%' . $search . '%')
                            ->orWhere('primer_apellido', 'like', '%' . $search . '%');
                    });
            });
        }

        $atenciones = $query->orderBy('created_at', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Atenciones Enfermería');

        // --- Título del documento (ajustado para cubrir hasta la columna 'S') ---
        Carbon::setLocale('es'); // Asegura que el nombre del mes sea en español
        $monthName = Carbon::now()->locale('es')->monthName;
        if ($fecha_fin) {
            $monthName = Carbon::parse($fecha_fin)->locale('es')->monthName;
        }
        
        $sheet->mergeCells('A1:S1');
        $sheet->setCellValue('A1', 'GOBIERNO AUTÓNOMO MUNICIPAL DE TARIJA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:S2');
        $sheet->setCellValue('A2', 'OFICINA DEL ADULTO MAYOR');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:S3');
        $sheet->setCellValue('A3', 'PLANILLA DE ATENCIÓN DE ENFERMERÍA CORRESPONDIENTE AL MES DE ' . mb_strtoupper($monthName));
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getRowDimension(4)->setRowHeight(15); 

        // --- Encabezados de la tabla (filas 5, 6, 7) ---
        // Columnas fijas
        $sheet->mergeCells('A5:A7');
        $sheet->setCellValue('A5', 'Nº');

        $sheet->mergeCells('B5:B7');
        $sheet->setCellValue('B5', 'NOMBRES Y APELLIDOS');

        $sheet->mergeCells('C5:D6');
        $sheet->setCellValue('C5', 'SEXO');
        $sheet->setCellValue('C7', 'F');
        $sheet->setCellValue('D7', 'M');

        $sheet->mergeCells('E5:E7');
        $sheet->setCellValue('E5', 'EDAD');

        // Sección "ATENCIÓN DE ENFERMERÍA"
        $sheet->mergeCells('F5:Q5');
        $sheet->setCellValue('F5', 'ATENCIÓN DE ENFERMERÍA');

        // Sub-sección "CONTROL SIGNOS VITALES"
        $sheet->mergeCells('F6:J6');
        $sheet->setCellValue('F6', 'CONTROL SIGNOS VITALES');
        $sheet->setCellValue('F7', 'PRESIÓN ARTERIAL'); // No vertical
        $sheet->setCellValue('G7', 'FRECUENCIA CARDÍACA');
        $sheet->setCellValue('H7', 'FRECUENCIA RESPIRATORIA');
        $sheet->setCellValue('I7', 'PULSO');
        $sheet->setCellValue('J7', 'TEMPERATURA');

        // Columnas verticales individuales dentro de ATENCION DE ENFERMERIA
        $sheet->mergeCells('K6:K7');
        $sheet->setCellValue('K6', 'CONTROL DE OXIMETRIA');

        $sheet->mergeCells('L6:L7');
        $sheet->setCellValue('L6', 'INYECTABLES');

        $sheet->mergeCells('M6:M7');
        $sheet->setCellValue('M6', 'PESO Y TALLA');

        $sheet->mergeCells('N6:N7');
        $sheet->setCellValue('N6', 'ORIENTACIÓN ALIMENTACIÓN');

        $sheet->mergeCells('O6:O7');
        $sheet->setCellValue('O6', 'LAVADO DE OÍDO');

        $sheet->mergeCells('P6:P7');
        $sheet->setCellValue('P6', 'CURACIÓN');

        $sheet->mergeCells('Q6:Q7');
        $sheet->setCellValue('Q6', 'MEDICAMENTOS'); // Nombre del encabezado según imagen, el dato es adm_medicamentos

        // Columna "DERIVACIÓN"
        $sheet->mergeCells('R5:R7');
        $sheet->setCellValue('R5', 'DERIVACIÓN');

        // Columna "FIRMA"
        $sheet->mergeCells('S5:S7');
        $sheet->setCellValue('S5', 'FIRMA');

        // --- Aplicar estilos a los encabezados de la tabla (filas 5, 6, 7) ---
        $sheet->getStyle('A5:S7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10], 
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true, 
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FFE0E0E0'], 
            ],
        ]);
        
        // Colores de relleno específicos para secciones
        $sheet->getStyle('C5:D6')->getFill()->getStartColor()->setARGB('FFD0D0D0'); 
        $sheet->getStyle('F6:J6')->getFill()->getStartColor()->setARGB('FFD0D0D0'); 

        // Alineación específica para las columnas F y M (Sexo)
        $sheet->getStyle('C7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Configurar la orientación del texto vertical ---
        $verticalColumns = ['G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];
        foreach ($verticalColumns as $column) {
            $sheet->getStyle($column . '7')->getAlignment()->setTextRotation(90);
            $sheet->getStyle($column . '6')->getAlignment()->setTextRotation(90); // Para los encabezados de dos filas como K6
        }
        $sheet->getStyle('R5')->getAlignment()->setTextRotation(90); // Derivacion
        $sheet->getStyle('S5')->getAlignment()->setTextRotation(90); // Firma

        // --- Ajustar anchos de columna para acomodar texto vertical ---
        foreach (range('A', 'S') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getColumnDimension('B')->setWidth(35); // Nombres y Apellidos
        $sheet->getColumnDimension('F')->setWidth(15); // Presión Arterial
        $sheet->getColumnDimension('G')->setWidth(4);  // Frecuencia Cardíaca (ajustado para vertical)
        $sheet->getColumnDimension('H')->setWidth(4);  // Frecuencia Respiratoria (ajustado para vertical)
        $sheet->getColumnDimension('I')->setWidth(4);  // Pulso (ajustado para vertical)
        $sheet->getColumnDimension('J')->setWidth(4);  // Temperatura (ajustado para vertical)
        $sheet->getColumnDimension('K')->setWidth(4);  // Control de Oximetría (ajustado para vertical)
        $sheet->getColumnDimension('L')->setWidth(4);  // Inyectables (ajustado para vertical)
        $sheet->getColumnDimension('M')->setWidth(4);  // Peso y Talla (ajustado para vertical)
        $sheet->getColumnDimension('N')->setWidth(4);  // Orientación Alimentación (ajustado para vertical)
        $sheet->getColumnDimension('O')->setWidth(4);  // Lavado de Oído (ajustado para vertical)
        $sheet->getColumnDimension('P')->setWidth(4);  // Curación (ajustado para vertical)
        $sheet->getColumnDimension('Q')->setWidth(4);  // Medicamentos (ajustado para vertical)
        $sheet->getColumnDimension('R')->setWidth(4);  // Derivación (ajustado para vertical)
        $sheet->getColumnDimension('S')->setWidth(4);  // Firma (ajustado para vertical)


        // Llenar datos
        $currentRow = 8; 
        foreach ($atenciones as $index => $atencion) {
            $adulto = $atencion->adulto;
            $persona = $adulto->persona;

            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $persona->nombres . ' ' . $persona->primer_apellido . ' ' . $persona->segundo_apellido);
            $sheet->setCellValue('C' . $currentRow, ($persona->sexo == 'F' ? 'X' : ''));
            $sheet->setCellValue('D' . $currentRow, ($persona->sexo == 'M' ? 'X' : ''));
            $sheet->setCellValue('E' . $currentRow, ($persona->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->age : ''));
            
            // Datos de Atención de Enfermería (mapeados a las columnas según la imagen)
            $sheet->setCellValue('F' . $currentRow, $atencion->presion_arterial);
            $sheet->setCellValue('G' . $currentRow, $atencion->frecuencia_cardiaca);
            $sheet->setCellValue('H' . $currentRow, $atencion->frecuencia_respiratoria);
            $sheet->setCellValue('I' . $currentRow, $atencion->pulso);
            $sheet->setCellValue('J' . $currentRow, $atencion->temperatura);
            $sheet->setCellValue('K' . $currentRow, $atencion->control_oximetria);
            $sheet->setCellValue('L' . $currentRow, $atencion->inyectables);
            $sheet->setCellValue('M' . $currentRow, $atencion->peso_talla);
            $sheet->setCellValue('N' . $currentRow, $atencion->orientacion_alimentacion);
            $sheet->setCellValue('O' . $currentRow, $atencion->lavado_oidos);
            $sheet->setCellValue('P' . $currentRow, $atencion->curacion);
            $sheet->setCellValue('Q' . $currentRow, $atencion->adm_medicamentos);
            $sheet->setCellValue('R' . $currentRow, $atencion->derivacion);
            $sheet->setCellValue('S' . $currentRow, ''); // Columna de firma vacía

            $currentRow++;
        }

        $dataRowsCount = count($atenciones);
        if ($dataRowsCount > 0) {
            $sheet->getStyle('A8:S' . ($currentRow - 1))->applyFromArray([ // Ajustado el rango a 'S'
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);
            $sheet->getStyle('B8:B' . ($currentRow - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Alineación izquierda para nombres
        }

        // --- Firmas al final (ajustadas para reflejar la nueva estructura y columnas) ---
        $currentRow += 3;
        $sheet->setCellValue('D' . $currentRow, '____________________________');
        $sheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('D' . ($currentRow + 1) . ':G' . ($currentRow + 1));
        $sheet->setCellValue('D' . ($currentRow + 1), 'FIRMA ADULTO MAYOR');
        $sheet->getStyle('D' . ($currentRow + 1))->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setCellValue('I' . $currentRow, '____________________________');
        $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('I' . ($currentRow + 1) . ':L' . ($currentRow + 1));
        $sheet->setCellValue('I' . ($currentRow + 1), 'FIRMA ENFERMER@');
        $sheet->getStyle('I' . ($currentRow + 1))->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setCellValue('N' . $currentRow, '____________________________');
        $sheet->getStyle('N' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('N' . ($currentRow + 1) . ':Q' . ($currentRow + 1)); // Ajustado a Q para que sea coherente con la imagen
        $sheet->setCellValue('N' . ($currentRow + 1), 'FIRMA ENCARGAD@ OF. ADULTO MAYOR');
        $sheet->getStyle('N' . ($currentRow + 1))->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Reporte_Atenciones_Enfermeria_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Muestra el listado de Historias Clínicas con filtros y buscador.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexHistoriaClinica(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        $search = $request->input('search');

        $totalAdultos = AdultoMayor::count();
        $historiasClinicas = collect();
        $totalHistoriasClinicas = 0;

        try {
            $query = HistoriaClinica::with('adulto.persona', 'usuario.persona');

            // Aplicar filtros de fecha
            if ($fecha_inicio) {
                $query->whereDate('created_at', '>=', $fecha_inicio);
            }
            if ($fecha_fin) {
                $query->whereDate('created_at', '<=', $fecha_fin);
            }

            // Aplicar búsqueda general
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('motivo_consulta', 'like', '%' . $search . '%')
                      ->orWhere('diagnostico_medico', 'like', '%' . $search . '%')
                      ->orWhereHas('adulto.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%')
                             ->orWhere('segundo_apellido', 'like', '%' . $search . '%')
                             ->orWhere('ci', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('usuario.persona', function ($qr) use ($search) {
                          $qr->where('nombres', 'like', '%' . $search . '%')
                             ->orWhere('primer_apellido', 'like', '%' . $search . '%');
                      });
                });
            }

            $historiasClinicas = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
            $totalHistoriasClinicas = HistoriaClinica::count();
            
        } catch (\Exception $e) {
            Log::error('Error en ReporteMedicoController@indexHistoriaClinica: ' . $e->getMessage(), ['exception' => $e]);
            return view('Medico.indexRepHis', compact('historiasClinicas', 'fecha_inicio', 'fecha_fin', 'search', 'totalAdultos', 'totalHistoriasClinicas', 'request'))
                        ->with('error', 'Ocurrió un error al cargar los reportes de historia clínica. Por favor, intente de nuevo más tarde.');
        }

        return view('Medico.indexRepHis', compact('historiasClinicas', 'fecha_inicio', 'fecha_fin', 'search', 'totalAdultos', 'totalHistoriasClinicas', 'request'));
    }

    /**
     * Exporta una Historia Clínica específica a un archivo Excel con el formato de la imagen.
     *
     * @param int $id_historia
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarHistoriaClinicaExcel($id_historia)
    {
        try {
            // Cargar la historia clínica con sus relaciones usando los nombres de método correctos
            $historia = HistoriaClinica::with('adulto.persona', 'usuario.persona', 'examenesComplementarios', 'medicamentosRecetados')->findOrFail($id_historia);
            
            $adulto = $historia->adulto;
            $persona = $adulto ? $adulto->persona : null;
            $usuario = $historia->usuario;
            $personaUsuario = $usuario ? $usuario->persona : null;
            
            // Acceder a la colección de exámenes y tomar el primero, si existe
            $examenComplementario = $historia->examenesComplementarios->first(); // CAMBIO CLAVE AQUÍ
            $medicamentosRecetados = $historia->medicamentosRecetados;

            if (!$adulto || !$persona) {
                throw new \Exception("Datos del adulto mayor o persona no encontrados para la historia clínica #{$id_historia}.");
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Historia Clinica');

            // --- Estilos generales para bordes ---
            $thinBorder = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $doubleBorderBottom = [
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $thickBorderOutline = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];

            // --- Encabezados Superiores ---
            $sheet->mergeCells('A1:J1');
            $sheet->setCellValue('A1', 'GOBIERNO AUTONOMO MUNICIPAL DE LA CIUDAD DE TARIJA');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A2:J2');
            $sheet->setCellValue('A2', 'Y LA PROVINCIA CERCADO');
            $sheet->getStyle('A2')->getFont()->setSize(10);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A3:J3');
            $sheet->setCellValue('A3', 'SECRETARIA DE LA MUJER, FAMILIA Y POBLACIONES VULNERABLES');
            $sheet->getStyle('A3')->getFont()->setSize(10);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A4:J4');
            $sheet->setCellValue('A4', '"OFICINA DEL ADULTO MAYOR"');
            $sheet->getStyle('A4')->getFont()->setSize(10);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // HISTORIA CLINICA (Título principal)
            $sheet->mergeCells('C6:H6');
            $sheet->setCellValue('C6', 'HISTORIA CLINICA');
            $sheet->getStyle('C6')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('C6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C6:H6')->applyFromArray($doubleBorderBottom);

            // MUNICIPIO:
            $sheet->mergeCells('A8:B8'); $sheet->setCellValue('A8', 'MUNICIPIO:');
            $sheet->mergeCells('C8:J8'); $sheet->setCellValue('C8', $historia->municipio_nombre ?? '');
            $sheet->getStyle('A8:J8')->applyFromArray($thinBorder);

            // ESTABLECIMIENTO:
            $sheet->mergeCells('A9:B9'); $sheet->setCellValue('A9', 'ESTABLECIMIENTO:');
            $sheet->mergeCells('C9:J9'); $sheet->setCellValue('C9', $historia->establecimiento ?? '');
            $sheet->getStyle('A9:J9')->applyFromArray($thinBorder);

            // --- DATOS PERSONALES ---
            $sheet->mergeCells('A11:J11');
            $sheet->setCellValue('A11', 'DATOS PERSONALES:');
            $sheet->getStyle('A11')->getFont()->setBold(true);
            $sheet->getStyle('A11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A11:J11')->applyFromArray($thickBorderOutline);

            // Fila 12: APELLIDO PATERNO | APELLIDO MATERNO | NOMBRES | SEXO | EDAD | CONSULTA N°
            $sheet->mergeCells('A12:B12'); $sheet->setCellValue('A12', 'APELLIDO PATERNO');
            $sheet->mergeCells('C12:D12'); $sheet->setCellValue('C12', 'APELLIDO MATERNO');
            $sheet->mergeCells('E12:F12'); $sheet->setCellValue('E12', 'NOMBRES');
            $sheet->setCellValue('G12', 'SEXO');
            $sheet->setCellValue('H12', 'EDAD');
            $sheet->mergeCells('I12:J12'); $sheet->setCellValue('I12', 'CONSULTA N°');

            // Fila 13 (Datos):
            $sheet->mergeCells('A13:B13'); $sheet->setCellValue('A13', $persona->primer_apellido ?? '');
            $sheet->mergeCells('C13:D13'); $sheet->setCellValue('C13', $persona->segundo_apellido ?? '');
            $sheet->mergeCells('E13:F13'); $sheet->setCellValue('E13', $persona->nombres ?? '');
            $sheet->setCellValue('G13', $persona->sexo ?? '');
            $sheet->setCellValue('H13', $persona->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->age : '');
            
            $consultaN = ($historia->tipo_consulta == 'N' ? 'X' : '');
            $consultaR = ($historia->tipo_consulta == 'R' ? 'X' : '');
            $sheet->mergeCells('I13:J13');
            $sheet->setCellValue('I13', 'N: ' . $consultaN . ' R: ' . $consultaR); 

            $sheet->getStyle('A12:J13')->applyFromArray($thinBorder);

            // Fila 14: ESTADO CIVIL | OCUPACION | GRADO DE INSTRUCCION | FECHA DE NACIMIENTO
            $sheet->mergeCells('A14:B14'); $sheet->setCellValue('A14', 'ESTADO CIVIL');
            $sheet->mergeCells('C14:D14'); $sheet->setCellValue('C14', 'OCUPACION');
            $sheet->mergeCells('E14:F14'); $sheet->setCellValue('E14', 'GRADO DE INSTRUCCION');
            $sheet->mergeCells('G14:H14'); $sheet->setCellValue('G14', 'FECHA DE NACIMIENTO');
            $sheet->mergeCells('I14:J14'); $sheet->setCellValue('I14', 'TELEFONO');

            // Fila 15 (Datos):
            $sheet->mergeCells('A15:B15'); $sheet->setCellValue('A15', $persona->estado_civil ?? '');
            $sheet->mergeCells('C15:D15'); $sheet->setCellValue('C15', $historia->ocupacion ?? $persona->ocupacion ?? '');
            $sheet->mergeCells('E15:F15'); $sheet->setCellValue('E15', $historia->grado_instruccion ?? $persona->grado_instruccion ?? '');
            $sheet->mergeCells('G15:H15'); $sheet->setCellValue('G15', $persona->fecha_nacimiento ? Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') : '');
            $sheet->mergeCells('I15:J15'); $sheet->setCellValue('I15', $persona->celular ?? $persona->telefono ?? '');

            $sheet->getStyle('A14:J15')->applyFromArray($thinBorder);

            // Fila 16: LUGAR DE NACIMIENTO | BARRIO O COMUNIDAD | DOMICILIO ACTUAL
            $sheet->mergeCells('A16:B16'); $sheet->setCellValue('A16', 'LUGAR DE NACIMIENTO');
            $sheet->mergeCells('C16:D16'); $sheet->setCellValue('C16', 'BARRIO O COMUNIDAD');
            $sheet->mergeCells('E16:F16'); $sheet->setCellValue('E16', 'DOMICILIO ACTUAL');
            $sheet->mergeCells('G16:H16'); $sheet->setCellValue('G16', 'PROVINCIA');
            $sheet->mergeCells('I16:J16'); $sheet->setCellValue('I16', 'DEPARTAMENTO');
            
            // Fila 17 (Datos):
            $sheet->mergeCells('A17:B17'); $sheet->setCellValue('A17', $historia->lugar_nacimiento_provincia ?? $persona->lugar_nacimiento ?? '');
            $sheet->mergeCells('C17:D17'); $sheet->setCellValue('C17', $persona->zona_comunidad ?? '');
            $sheet->mergeCells('E17:F17'); $sheet->setCellValue('E17', $historia->domicilio_actual ?? $persona->domicilio ?? '');
            $sheet->mergeCells('G17:H17'); $sheet->setCellValue('G17', $historia->lugar_nacimiento_provincia ?? '');
            $sheet->mergeCells('I17:J17'); $sheet->setCellValue('I17', $historia->lugar_nacimiento_departamento ?? '');

            $sheet->getStyle('A16:J17')->applyFromArray($thinBorder);

            // Ajuste de filas para compensar la eliminación de anamnesis:
            $currentRowAfterPersonalData = 19; // Después de la Fila 17 de datos personales, dejamos un espacio.

            // --- ANTECEDENTES PERSONALES ---
            $sheet->mergeCells('A' . $currentRowAfterPersonalData . ':J' . $currentRowAfterPersonalData); $sheet->setCellValue('A' . $currentRowAfterPersonalData, 'ANTECEDENTES PERSONALES:');
            $sheet->getStyle('A' . $currentRowAfterPersonalData)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRowAfterPersonalData . ':J' . $currentRowAfterPersonalData)->applyFromArray($doubleBorderBottom);
            $currentRowAfterPersonalData++;
            $sheet->mergeCells('A' . $currentRowAfterPersonalData . ':J' . ($currentRowAfterPersonalData + 1)); $sheet->setCellValue('A' . $currentRowAfterPersonalData, $historia->antecedentes_personales ?? '');
            $sheet->getStyle('A' . $currentRowAfterPersonalData . ':J' . ($currentRowAfterPersonalData + 1))->applyFromArray($thinBorder);
            $currentRowAfterPersonalData += 2; // Avanza 2 filas por el contenido

            // --- ANTECEDENTES FAMILIARES ---
            $sheet->mergeCells('A' . $currentRowAfterPersonalData . ':J' . $currentRowAfterPersonalData); $sheet->setCellValue('A' . $currentRowAfterPersonalData, 'ANTECEDENTES FAMILIARES:');
            $sheet->getStyle('A' . $currentRowAfterPersonalData)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRowAfterPersonalData . ':J' . $currentRowAfterPersonalData)->applyFromArray($doubleBorderBottom);
            $currentRowAfterPersonalData++;
            $sheet->mergeCells('A' . $currentRowAfterPersonalData . ':J' . ($currentRowAfterPersonalData + 1)); $sheet->setCellValue('A' . $currentRowAfterPersonalData, $historia->antecedentes_familiares ?? '');
            $sheet->getStyle('A' . $currentRowAfterPersonalData . ':J' . ($currentRowAfterPersonalData + 1))->applyFromArray($thinBorder);
            $currentRowAfterPersonalData += 2;

            // --- ESTADO ACTUAL ---
            $sheet->mergeCells('A' . $currentRowAfterPersonalData . ':J' . $currentRowAfterPersonalData); $sheet->setCellValue('A' . $currentRowAfterPersonalData, 'ESTADO ACTUAL:');
            $sheet->getStyle('A' . $currentRowAfterPersonalData)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRowAfterPersonalData)->applyFromArray($doubleBorderBottom);
            $currentRowAfterPersonalData++;
            $sheet->mergeCells('A' . $currentRowAfterPersonalData . ':J' . ($currentRowAfterPersonalData + 1)); $sheet->setCellValue('A' . $currentRowAfterPersonalData, $historia->estado_actual ?? '');
            $sheet->getStyle('A' . $currentRowAfterPersonalData . ':J' . ($currentRowAfterPersonalData + 1))->applyFromArray($thinBorder);
            $currentRowAfterPersonalData += 2;


            // --- EXAMENES COMPLEMENTARIOS ---
            $currentRowExamenes = $currentRowAfterPersonalData + 2; // Dejar 2 filas de espacio
            $sheet->mergeCells('A' . $currentRowExamenes . ':J' . $currentRowExamenes);
            $sheet->setCellValue('A' . $currentRowExamenes, 'EXAMENES COMPLEMENTARIOS:');
            $sheet->getStyle('A' . $currentRowExamenes)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRowExamenes)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . $currentRowExamenes . ':J' . $currentRowExamenes)->applyFromArray($thickBorderOutline);
            $currentRowExamenes++;

            // Fila: PRESION ARTERIAL | TEMPERATURA | PESO CORPORAL
            $sheet->mergeCells('A' . $currentRowExamenes . ':C' . $currentRowExamenes); $sheet->setCellValue('A' . $currentRowExamenes, 'PRESION ARTERIAL');
            $sheet->mergeCells('D' . $currentRowExamenes . ':F' . $currentRowExamenes); $sheet->setCellValue('D' . $currentRowExamenes, 'TEMPERATURA');
            $sheet->mergeCells('G' . $currentRowExamenes . ':J' . $currentRowExamenes); $sheet->setCellValue('G' . $currentRowExamenes, 'PESO CORPORAL');
            $currentRowExamenes++;

            // Fila (Datos de ExamenComplementario):
            // Se usa $examenComplementario?->propiedad ?? '' para acceder de forma segura al objeto
            $sheet->mergeCells('A' . $currentRowExamenes . ':C' . $currentRowExamenes); $sheet->setCellValue('A' . $currentRowExamenes, $examenComplementario->presion_arterial ?? '');
            $sheet->mergeCells('D' . $currentRowExamenes . ':F' . $currentRowExamenes); $sheet->setCellValue('D' . $currentRowExamenes, $examenComplementario->temperatura ?? '');
            $sheet->mergeCells('G' . $currentRowExamenes . ':J' . $currentRowExamenes); $sheet->setCellValue('G' . $currentRowExamenes, $examenComplementario->peso_corporal ?? '');
            $sheet->getStyle('A' . ($currentRowExamenes - 1) . ':J' . $currentRowExamenes)->applyFromArray($thinBorder);
            $currentRowExamenes++;

            // --- RESULTADO DE LA PRUEBA... y DIAGNOSTICO ---
            $currentRowExamenes++; // Espacio en blanco
            $sheet->mergeCells('A' . $currentRowExamenes . ':J' . $currentRowExamenes);
            $sheet->setCellValue('A' . $currentRowExamenes, 'RESULTADO DE LA PRUEBA (mg/dl): ' . ($examenComplementario->resultado_prueba ?? '') . ' DIAGNOSTICO: ' . ($examenComplementario->diagnostico ?? ''));
            $sheet->getStyle('A' . $currentRowExamenes)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRowExamenes)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . $currentRowExamenes . ':J' . $currentRowExamenes)->applyFromArray($doubleBorderBottom);
            $currentRowExamenes++;
            $sheet->mergeCells('A' . $currentRowExamenes . ':J' . ($currentRowExamenes + 1));
            $sheet->getStyle('A' . $currentRowExamenes . ':J' . ($currentRowExamenes + 1))->applyFromArray($thinBorder);
            $currentRowExamenes += 2;

            // --- MEDICAMENTOS ---
            $currentMedRow = $currentRowExamenes + 2; // Fila de inicio para la sección de medicamentos

            $sheet->mergeCells('A' . $currentMedRow . ':J' . $currentMedRow);
            $sheet->setCellValue('A' . $currentMedRow, 'MEDICAMENTOS');
            $sheet->getStyle('A' . $currentMedRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentMedRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . $currentMedRow . ':J' . $currentMedRow)->applyFromArray($thickBorderOutline);
            $currentMedRow++;

            // Fila de encabezados de la tabla de medicamentos
            $sheet->mergeCells('A' . $currentMedRow . ':F' . $currentMedRow); $sheet->setCellValue('A' . $currentMedRow, '(Nombre Genérico, Forma Farmacéutica y Concentración)');
            $sheet->setCellValue('G' . $currentMedRow, 'CANTIDAD RECETADA');
            $sheet->setCellValue('H' . $currentMedRow, 'CANTIDAD DISPENSADA');
            $sheet->setCellValue('I' . $currentMedRow, 'VALOR UNITARIO');
            $sheet->setCellValue('J' . $currentMedRow, 'TOTAL');

            $sheet->getStyle('A' . $currentMedRow . ':J' . $currentMedRow)->applyFromArray($thinBorder);
            $sheet->getStyle('A' . $currentMedRow . ':J' . $currentMedRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentMedRow . ':J' . $currentMedRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currentMedRow++;

            // Iterar sobre los medicamentos recetados y llenar la tabla
            if ($medicamentosRecetados->isEmpty()) {
                $sheet->mergeCells('A' . $currentMedRow . ':F' . $currentMedRow); $sheet->setCellValue('A' . $currentMedRow, '');
                $sheet->setCellValue('G' . $currentMedRow, '');
                $sheet->setCellValue('H' . $currentMedRow, '');
                $sheet->setCellValue('I' . $currentMedRow, '');
                $sheet->setCellValue('J' . $currentMedRow, '');
                $sheet->getStyle('A' . $currentMedRow . ':J' . $currentMedRow)->applyFromArray($thinBorder);
                $currentMedRow++;
            } else {
                foreach ($medicamentosRecetados as $medicamento) {
                    $sheet->mergeCells('A' . $currentMedRow . ':F' . $currentMedRow); $sheet->setCellValue('A' . $currentMedRow, $medicamento->nombre_medicamento ?? '');
                    $sheet->setCellValue('G' . $currentMedRow, $medicamento->cantidad_recetada ?? '');
                    $sheet->setCellValue('H' . $currentMedRow, $medicamento->cantidad_dispensada ?? '');
                    $sheet->setCellValue('I' . $currentMedRow, $medicamento->valor_unitario ?? '');
                    $sheet->setCellValue('J' . $currentMedRow, $medicamento->total ?? '');
                    $sheet->getStyle('A' . $currentMedRow . ':J' . $currentMedRow)->applyFromArray($thinBorder);
                    $sheet->getStyle('G' . $currentMedRow . ':J' . $currentMedRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $currentMedRow++;
                }
            }

            // --- Pie de Página / Firmas ---
            $currentRow = $currentMedRow + 3;
            
            $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow); $sheet->setCellValue('A' . $currentRow, 'RESPONSABLE');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow)->applyFromArray($doubleBorderBottom);
            $sheet->mergeCells('D' . $currentRow . ':F' . $currentRow); $sheet->setCellValue('D' . $currentRow, ($personaUsuario->nombres ?? '') . ' ' . ($personaUsuario->primer_apellido ?? ''));
            $sheet->getStyle('D' . $currentRow . ':F' . $currentRow)->applyFromArray($doubleBorderBottom);
            
            $sheet->mergeCells('H' . $currentRow . ':J' . $currentRow); $sheet->setCellValue('H' . $currentRow, 'FIRMA DEL PACIENTE');
            $sheet->getStyle('H' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('H' . $currentRow)->applyFromArray($doubleBorderBottom);

            $currentRow++;
            $sheet->mergeCells('D' . $currentRow . ':F' . $currentRow); $sheet->setCellValue('D' . $currentRow, 'C.I. ' . ($personaUsuario->ci ?? ''));
            $sheet->mergeCells('H' . $currentRow . ':J' . $currentRow); $sheet->setCellValue('H' . $currentRow, 'C.I. ' . ($persona->ci ?? ''));

            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            $sheet->getColumnDimension('B')->setWidth(20); 
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);


            $writer = new Xlsx($spreadsheet);
            $fileName = 'Historia_Clinica_' . ($persona->primer_apellido ?? 'Desconocido') . '_' . ($persona->nombres ?? 'Desconocido') . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            Log::error('Error al exportar Historia Clínica a Excel: ' . $e->getMessage(), [
                'id_historia' => $id_historia,
                'exception' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'No se pudo exportar la Historia Clínica a Excel. Por favor, revise los datos o intente de nuevo más tarde. Error: ' . $e->getMessage());
        }
    }
}
