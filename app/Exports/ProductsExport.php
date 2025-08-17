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
        $companyId = Auth::user()->company_id;

        $query = Product::query()
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->where('products.company_id', $companyId)
            ->select(
                'products.id',
                'products.sku',
                'products.name',
                'categories.name as category_name',
                'products.stock',
                'products.price',
                'suppliers.name as supplier_name',
                'products.image',
                'products.created_at'
            );

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('products.name', 'like', "%{$this->search}%")
                  ->orWhere('products.sku', 'like', "%{$this->search}%");
            });
        }

        return $query->get();
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