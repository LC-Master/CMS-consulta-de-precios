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
        $drawing->setCoordinates('B2'); // Posición del logo

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
                // Estilo para encabezados verdes
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // Texto blanco
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF00A650']], // Verde corporativo
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ];
                
                // Estilo para sub-encabezados (gris claro)
                $subHeaderStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]]
                ];

                // Estilo de bordes simples
                $borderStyle = [
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']],
                    ],
                ];

                // Ajustar ancho de columnas para que se vea bien
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(35);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);

                // --- TÍTULO PRINCIPAL (Fila 2-4) ---
                // Combinamos celdas para el título, dejando B2 libre para el logo
                $sheet->mergeCells('C2:E3');
                $sheet->setCellValue('C2', 'DETALLE DE CAMPAÑA');
                $sheet->getStyle('C2')->getFont()->setSize(20)->setBold(true);
                $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // --- SECCIÓN 1: DATOS GENERALES (Fila 6) ---
                $row = 6;
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row", 'INFORMACIÓN GENERAL');
                $sheet->getStyle("B$row")->applyFromArray($headerStyle);
                
                $row++;
                // Titulo y Departamento
                $sheet->setCellValue("B$row", 'Título:');
                $sheet->setCellValue("C$row", $campaign->title);
                $sheet->getStyle("B$row")->getFont()->setBold(true);
                
                $sheet->setCellValue("D$row", 'Departamento:');
                $sheet->setCellValue("E$row", $campaign->department?->name ?? 'N/A');
                $sheet->getStyle("D$row")->getFont()->setBold(true);

                $row++;
                // Fechas
                $sheet->setCellValue("B$row", 'Fecha Inicio:');
                $sheet->setCellValue("C$row", Carbon::parse($campaign->start_at)->format('d/m/Y H:i'));
                $sheet->getStyle("B$row")->getFont()->setBold(true);

                $sheet->setCellValue("D$row", 'Fecha Fin:');
                $sheet->setCellValue("E$row", Carbon::parse($campaign->end_at)->format('d/m/Y H:i'));
                $sheet->getStyle("D$row")->getFont()->setBold(true);

                $row++;
                // Estatus y Creador
                $sheet->setCellValue("B$row", 'Estatus:');
                $sheet->setCellValue("C$row", $campaign->status?->status);
                $sheet->getStyle("B$row")->getFont()->setBold(true);

                $sheet->setCellValue("D$row", 'Creado por:');
                $sheet->setCellValue("E$row", $campaign->user?->name);
                $sheet->getStyle("D$row")->getFont()->setBold(true);

                // Aplicar bordes a toda la sección 1
                $sheet->getStyle("B7:E$row")->applyFromArray($borderStyle);

                // --- SECCIÓN 2: CENTROS Y ACUERDOS (Separación de filas) ---
                $row += 3; // Saltamos 3 filas para separar
                
                // Encabezado Centros (Columna B-C)
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row", 'CENTROS ASOCIADOS');
                $sheet->getStyle("B$row")->applyFromArray($headerStyle);
                
                // Encabezado Acuerdos (Columna D-E)
                $sheet->mergeCells("D$row:E$row");
                $sheet->setCellValue("D$row", 'ACUERDOS COMERCIALES');
                $sheet->getStyle("D$row")->applyFromArray($headerStyle);

                $startListRow = $row + 1;
                $currentCenterRow = $startListRow;
                $currentAgreeRow = $startListRow;
                
                // Listar Centros hacia abajo
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

                // Listar Acuerdos hacia abajo
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

                // Calcular cuál lista fue más larga para cerrar los bordes correctamente
                $maxRow = max($currentCenterRow, $currentAgreeRow);
                
                // Aplicar bordes si hay datos
                if ($maxRow > $startListRow) {
                    $sheet->getStyle("B$startListRow:C" . ($maxRow - 1))->applyFromArray($borderStyle);
                    $sheet->getStyle("D$startListRow:E" . ($maxRow - 1))->applyFromArray($borderStyle);
                }

                // --- SECCIÓN 3: PROGRAMACIÓN MULTIMEDIA ---
                $row = $maxRow + 2; // Saltamos espacio
                
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row", 'PROGRAMACIÓN MULTIMEDIA');
                $sheet->getStyle("B$row")->applyFromArray($headerStyle);

                $row++;
                // Sub-encabezados de la tabla
                $sheet->setCellValue("B$row", 'BLOQUE');
                $sheet->setCellValue("C$row", 'NOMBRE DEL ARCHIVO');
                $sheet->setCellValue("D$row", 'TIPO');
                $sheet->setCellValue("E$row", 'ORDEN');
                $sheet->getStyle("B$row:E$row")->applyFromArray($subHeaderStyle);
                $sheet->getStyle("B$row:E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $row++;
                
                // Ordenar ítems: Primero por Slot (AM/PM) y luego por Posición
                $items = $campaign->timeLineItems->sortBy([
                    ['slot', 'asc'],
                    ['position', 'asc'],
                ]);

                if ($items->isEmpty()) {
                    $sheet->mergeCells("B$row:E$row");
                    $sheet->setCellValue("B$row", "No hay contenido multimedia programado.");
                    $sheet->getStyle("B$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B$row:E$row")->applyFromArray($borderStyle);
                } else {
                    foreach ($items as $item) {
                        $slotName = $item->slot === 'am' ? '🌞 MAÑANA (AM)' : '🌙 TARDE (PM)';
                        $mediaName = $item->media ? $item->media->name : 'Archivo eliminado';
                        $mime = $item->media ? $item->media->mime_type : '-';

                        $sheet->setCellValue("B$row", $slotName);
                        $sheet->setCellValue("C$row", $mediaName); // Nombre del archivo
                        $sheet->setCellValue("D$row", $mime);
                        $sheet->setCellValue("E$row", $item->position);
                        
                        // Centrar columnas B, D y E
                        $sheet->getStyle("B$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("D$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        
                        // Bordes de la fila
                        $sheet->getStyle("B$row:E$row")->applyFromArray($borderStyle);
                        $row++;
                    }
                }
            },
        ];
    }
}