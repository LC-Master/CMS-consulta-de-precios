<?php

namespace App\Exports;

use App\Models\Campaign;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class SingleCampaignExport implements WithTitle, WithDrawings, WithEvents
{
    protected $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function title(): string
    {
        return substr($this->campaign->title, 0, 30);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Corporativo');
        $drawing->setPath(public_path('Logo.webp'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('B2');

        if (file_exists(public_path('Logo.webp'))) {
            return [$drawing];
        }
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $campaign = $this->campaign;

                // --- ESTILOS GLOBALES ---
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF00A650']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ];
                
                $subHeaderStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]]
                ];

                // CAMBIO: Color del borde a NEGRO (FF000000)
                $borderStyle = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
                    ],
                ];

                // Ajustar ancho de columnas
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(40); // Un poco más ancho para nombres largos
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(15);

                // --- TÍTULO PRINCIPAL ---
                $sheet->mergeCells('C2:E3');
                $sheet->setCellValue('C2', 'DETALLE DE CAMPAÑA');
                $sheet->getStyle('C2')->getFont()->setSize(20)->setBold(true);
                $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // --- SECCIÓN 1: DATOS GENERALES ---
                $row = 6;
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row", 'INFORMACIÓN GENERAL');
                $sheet->getStyle("B$row")->applyFromArray($headerStyle);
                
                $row++;
                $sheet->setCellValue("B$row", 'Título:');
                $sheet->setCellValue("C$row", $campaign->title);
                $sheet->getStyle("B$row")->getFont()->setBold(true);
                
                $sheet->setCellValue("D$row", 'Departamento:');
                $sheet->setCellValue("E$row", $campaign->department?->name ?? 'N/A');
                $sheet->getStyle("D$row")->getFont()->setBold(true);

                $row++;
                $sheet->setCellValue("B$row", 'Fecha Inicio:');
                $sheet->setCellValue("C$row", Carbon::parse($campaign->start_at)->format('d/m/Y H:i'));
                $sheet->getStyle("B$row")->getFont()->setBold(true);

                $sheet->setCellValue("D$row", 'Fecha Fin:');
                $sheet->setCellValue("E$row", Carbon::parse($campaign->end_at)->format('d/m/Y H:i'));
                $sheet->getStyle("D$row")->getFont()->setBold(true);

                $row++;
                $sheet->setCellValue("B$row", 'Estatus:');
                $sheet->setCellValue("C$row", $campaign->status?->status);
                $sheet->getStyle("B$row")->getFont()->setBold(true);

                $sheet->setCellValue("D$row", 'Creado por:');
                $sheet->setCellValue("E$row", $campaign->user?->name);
                $sheet->getStyle("D$row")->getFont()->setBold(true);

                // Aplicar bordes negros a Sección 1
                $sheet->getStyle("B7:E$row")->applyFromArray($borderStyle);

                // --- SECCIÓN 2: CENTROS Y ACUERDOS ---
                $row += 3;
                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row", 'CENTROS ASOCIADOS');
                $sheet->getStyle("B$row")->applyFromArray($headerStyle);
                
                $sheet->mergeCells("D$row:E$row");
                $sheet->setCellValue("D$row", 'ACUERDOS COMERCIALES');
                $sheet->getStyle("D$row")->applyFromArray($headerStyle);

                $startListRow = $row + 1;
                $currentCenterRow = $startListRow;
                $currentAgreeRow = $startListRow;
                
                if ($campaign->centers->isEmpty()) {
                    $sheet->setCellValue("B$currentCenterRow", "Sin centros asignados");
                    $sheet->mergeCells("B$currentCenterRow:C$currentCenterRow");
                    $currentCenterRow++;
                } else {
                    foreach ($campaign->centers as $center) {
                        $sheet->setCellValue("B$currentCenterRow", $center->code);
                        $sheet->setCellValue("C$currentCenterRow", $center->name);
                        $currentCenterRow++;
                    }
                }

                if ($campaign->agreements->isEmpty()) {
                    $sheet->setCellValue("D$currentAgreeRow", "Sin acuerdos asignados");
                    $sheet->mergeCells("D$currentAgreeRow:E$currentAgreeRow");
                    $currentAgreeRow++;
                } else {
                    foreach ($campaign->agreements as $agreement) {
                        $sheet->mergeCells("D$currentAgreeRow:E$currentAgreeRow");
                        $sheet->setCellValue("D$currentAgreeRow", $agreement->name);
                        $currentAgreeRow++;
                    }
                }

                $maxRow = max($currentCenterRow, $currentAgreeRow);
                
                if ($maxRow > $startListRow) {
                    // Aplicar bordes negros a las listas
                    $sheet->getStyle("B$startListRow:C" . ($maxRow - 1))->applyFromArray($borderStyle);
                    $sheet->getStyle("D$startListRow:E" . ($maxRow - 1))->applyFromArray($borderStyle);
                }

                // --- SECCIÓN 3: PROGRAMACIÓN MULTIMEDIA ---
                $row = $maxRow + 2;
                
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row", 'PROGRAMACIÓN MULTIMEDIA');
                $sheet->getStyle("B$row")->applyFromArray($headerStyle);

                $row++;
                $sheet->setCellValue("B$row", 'BLOQUE');
                $sheet->setCellValue("C$row", 'NOMBRE DEL ARCHIVO');
                $sheet->setCellValue("D$row", 'TIPO');
                $sheet->setCellValue("E$row", 'ORDEN');
                $sheet->getStyle("B$row:E$row")->applyFromArray($subHeaderStyle);
                $sheet->getStyle("B$row:E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $tableStartRow = $row + 1;
                
                // Ordenar ítems
                $items = $campaign->timeLineItems->sortBy('position');
                
                // CAMBIO: Separamos los ítems por bloque para procesar el "Merge"
                $amItems = $items->where('slot', 'am');
                $pmItems = $items->where('slot', 'pm');

                if ($items->isEmpty()) {
                    $row++;
                    $sheet->mergeCells("B$row:E$row");
                    $sheet->setCellValue("B$row", "No hay contenido multimedia programado.");
                    $sheet->getStyle("B$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B$row:E$row")->applyFromArray($borderStyle);
                } else {
                    // 1. Procesar Bloque AM
                    if ($amItems->isNotEmpty()) {
                        $startAmRow = $row + 1;
                        foreach ($amItems as $item) {
                            $row++;
                            $this->writeMediaRow($sheet, $row, $item);
                        }
                        $endAmRow = $row;
                        
                        // Combinar celda del bloque AM verticalmente
                        $sheet->mergeCells("B$startAmRow:B$endAmRow");
                        $sheet->setCellValue("B$startAmRow", '🌞 MAÑANA (AM)');
                        $sheet->getStyle("B$startAmRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // 2. Procesar Bloque PM
                    if ($pmItems->isNotEmpty()) {
                        $startPmRow = $row + 1;
                        foreach ($pmItems as $item) {
                            $row++;
                            $this->writeMediaRow($sheet, $row, $item);
                        }
                        $endPmRow = $row;

                        // Combinar celda del bloque PM verticalmente
                        $sheet->mergeCells("B$startPmRow:B$endPmRow");
                        $sheet->setCellValue("B$startPmRow", '🌙 TARDE (PM)');
                        $sheet->getStyle("B$startPmRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    // Aplicar bordes negros a toda la tabla multimedia al final
                    $sheet->getStyle("B$tableStartRow:E$row")->applyFromArray($borderStyle);
                }
            },
        ];
    }

    /**
     * Función auxiliar para escribir los datos de una fila multimedia
     */
    private function writeMediaRow($sheet, $row, $item)
    {
        $mediaName = $item->media ? $item->media->name : 'Archivo eliminado';
        $mime = $item->media ? $item->media->mime_type : '-';

        // Nota: No escribimos en la columna B aquí, eso se hace en el merge.
        $sheet->setCellValue("C$row", $mediaName);
        $sheet->setCellValue("D$row", $mime);
        $sheet->setCellValue("E$row", $item->position);
        
        // Alineación
        $sheet->getStyle("B$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Para el texto del bloque
        $sheet->getStyle("D$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}