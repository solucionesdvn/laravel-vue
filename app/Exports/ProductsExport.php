<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Product::where('company_id', Auth::user()->company_id)
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
            })
            ->select('id', 'sku', 'name', 'category_id', 'stock', 'price', 'supplier_id', 'image', 'created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'SKU',
            'Nombre',
            'Categoría',
            'Stock',
            'Precio',
            'Proveedor',
            'Imagen',
            'Fecha de creación'
        ];
    }
}