<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CalendarVisualExport implements WithDrawings, WithTitle
{
    protected $imagePath;

    public function __construct(string $imagePath)
    {
        $this->imagePath = $imagePath;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Calendario');
        $drawing->setDescription('Vista del Calendario');
        $drawing->setPath($this->imagePath);
        $drawing->setHeight(900); 
        $drawing->setCoordinates('A1');

        return [$drawing];
    }

    public function title(): string
    {
        return 'Vista Calendario';
    }
}