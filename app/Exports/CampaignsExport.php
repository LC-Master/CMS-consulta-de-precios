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
        
        // Si seleccionó departamento
        if (!empty($this->filters['departmentId'])) {
            $query->where('department_id', $this->filters['departmentId']);
        }

        // Si seleccionó acuerdo
        if (!empty($this->filters['agreementId'])) {
            $query->where('agreement_id', $this->filters['agreementId']);
        }

        // Si seleccionó estatus
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
                'Departamento', 
                // 'Acuerdo',      
                'Estatus',
                'Usuario Creador',
            ]
        ];
    }

    public function map($campaign): array
    {
        return [
            $campaign->title,
            Carbon::parse($campaign->start_at)->format('d/m/Y H:i'),
            Carbon::parse($campaign->end_at)->format('d/m/Y H:i'),
            $campaign->department?->name ?? 'N/A', 
            //$campaign->agreement?->name ?? 'N/A',
            $campaign->status?->status ?? 'Sin Estatus',
            $campaign->user?->name ?? 'Sistema',
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Corporativo');
        $logoPath = public_path('Logo.webp'); 

        if (file_exists($logoPath)) {
            $drawing->setPath($logoPath);
            $drawing->setHeight(50);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(15);
            $drawing->setOffsetY(10);
        }

        return $drawing;
    }

    public function styles(Worksheet $sheet)
    {
        // Título Principal (Combinado hasta la columna G)
        $sheet->mergeCells('A1:G1'); 
        $sheet->getRowDimension(1)->setRowHeight(60);
        
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Cabecera de la Tabla (Fila 2, hasta columna G)
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
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Campañas';
    }
}