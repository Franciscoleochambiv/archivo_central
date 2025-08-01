<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class WordExportController extends Controller
{
    public function generateDocument(Request $request)
    {
        // Ruta de la plantilla de Word
        $templatePath = storage_path('app/templates/ANEXO.docx'); // Cambia esta ruta a tu plantilla
        $outputPath = storage_path('app/exports/anexo_generado.docx');

        // Cargar la plantilla
        $templateProcessor = new TemplateProcessor($templatePath);

        // Reemplazar variables dinámicamente
        $templateProcessor->setValue('NOMBRE_ANIO2', 'Año del Bicentenario, de la consolidación de nuestra Independencia, ');
        $templateProcessor->setValue('NOMBRE_ANIO', 'y de la conmemoración de las heroicas batallas de Junín y Ayacucho');
        $templateProcessor->setValue('DEPENDENCIA_TITULO_PADRE', 'MUNICIPALIDAD DISTRITAL DE COLQUEMARCA');
        $templateProcessor->setValue('DEPENDENCIA_TITULO', 'OFICINA DE RECURSOS HUMANOS');

        $templateProcessor->setValue('EMPLEADO_DESTINO', $request->input('empleado_destino'));

        $templateProcessor->setValue('CARGO_EMP_DESTINO', $request->input('cargo_emp_destino'));
        
        $templateProcessor->setValue('DEPENDENCIA_DESTINO', $request->input('dependencia_destino'));

                    
        $templateProcessor->setValue('EMPLEADO_EMITE', $request->input('empleado_emite'));
        $templateProcessor->setValue('CARGO_EMP_EMITE', $request->input('cargo_emp_emite'));


        $templateProcessor->setValue('DEPENDENCIA_EMITE', $request->input('dependencia_emite'));
        $templateProcessor->setValue('ASUNTO', $request->input('asunto'));

        $templateProcessor->setValue('REFERENCIA', $request->input('referencia'));

        $templateProcessor->setValue('FECHA_DOC', now()->format('d/m/Y'));
        $templateProcessor->setValue('FECHA_INI', $request->input('fecha_ingreso'));
        $templateProcessor->setValue('FECHA_FINAL', $request->input('fecha_final'));
        $templateProcessor->setValue('DIAS', $request->input('dias_trabajados'));

        $templateProcessor->setValue('ANIOS', $request->input('anios'));
        $templateProcessor->setValue('MESES', $request->input('meses'));
        $templateProcessor->setValue('DIAS1', $request->input('dias'));


        $templateProcessor->setValue('REMU_BASICO', $request->input('remu_basico'));
        $templateProcessor->setValue('TOTAL', $request->input('remu_vacacional'));

        


      

        // Guardar el documento generado
        $templateProcessor->saveAs($outputPath);

        // Retornar el archivo al frontend
        return response()->download($outputPath)->deleteFileAfterSend(true);
    }
}
