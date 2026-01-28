<?php

namespace App\Exports;

use App\Models\Campaign;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class CampaignsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithDrawings
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Campaign::query()
            ->with(['user', 'status', 'department', 'agreements'])
            ->whereDate('start_at', '>=', $this->filters['startDate'])
            ->whereDate('start_at', '<=', $this->filters['endDate']);

        // --- FILTROS OPCIONALES ---
        
        if (!empty($this->filters['departmentId'])) {
            $query->where('department_id', $this->filters['departmentId']);
        }

        // Filtro por acuerdo
        if (!empty($this->filters['agreementId'])) {
            $query->whereHas('agreements', function($q) {
                $q->where('agreements.id', $this->filters['agreementId']);
            });
        }

        if (!empty($this->filters['statusId'])) {
            $query->where('status_id', $this->filters['statusId']);
        }

        return $query->orderBy('start_at', 'desc');
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE CAMPAÑAS'],
            [
                'Título de la Campaña',
                'Fecha Inicio',
                'Fecha Fin',
                'Departamento / Categoria',
                'Estatus',
                'Usuario Creador',
                'Acuerdos Asociados',
            ]
        ];
    }

    public function map($campaign): array
    {
        $agreementsList = $campaign->agreements->isNotEmpty() 
            ? $campaign->agreements->pluck('name')->implode(', ') 
            : 'Sin Acuerdos';

        return [
            $campaign->title,
            Carbon::parse($campaign->start_at)->format('d/m/Y H:i'),
            Carbon::parse($campaign->end_at)->format('d/m/Y H:i'),
            $campaign->department?->name ?? 'N/A', 
            $campaign->status?->status ?? 'Sin Estatus',
            $campaign->user?->name ?? 'Sistema',
            $agreementsList, // Aquí mostramos la lista de acuerdos
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Corporativo');
        $drawing->setPath(public_path('Logo.webp'));

        if (file_exists(public_path('Logo.webp'))) {
            $drawing->setHeight(50);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(15);
            $drawing->setOffsetY(10);
        } else {
            return [];
        }

        return $drawing;
    }

    public function styles(Worksheet $sheet)
    {
        // el rango hasta la columna G (7 columnas en total)
        
        // Título Principal
        $sheet->mergeCells('A1:G1'); 
        $sheet->getRowDimension(1)->setRowHeight(60);
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Cabecera de la Tabla
        $sheet->getStyle('A2:G2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF00A650']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Bordes
        $highestRow = $sheet->getHighestRow();
        $tableRange = 'A2:G' . $highestRow;

        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
            // Alineación superior para celdas con múltiples líneas (como los acuerdos)
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Campañas';
    }
}