<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\ResignationForm;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentExportController extends Controller
{
    public function exportResignationPDF($id)
    {
        $form = ResignationForm::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $pdf = Pdf::loadView('exports.resignations.pdf', [
            'form' => $form,
        ]);

        return $pdf->download('carta-renuncia-' . $form->full_name . '.pdf');
    }
}
