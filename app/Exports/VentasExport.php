<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Venta::all();
    }

    public function headings(): array
    {
        return [
            'Mes',
            'Delegación',
            'Total',
            // Añade más encabezados según sea necesario
        ];
    }
}
