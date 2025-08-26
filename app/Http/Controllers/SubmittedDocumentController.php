<?php

namespace App\Http\Controllers;

use App\Models\SubmittedDocument;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SubmittedDocumentController extends Controller
{
    /**
     * Mostrar listado de documentos enviados de la empresa.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $submittedDocuments = SubmittedDocument::with('documentTemplate')
            ->where('company_id', $companyId)
            ->latest()
            ->get();

        return Inertia::render('SubmittedDocuments/Index', [
            'submittedDocuments' => $submittedDocuments,
        ]);
    }

    /**
     * Mostrar formulario para llenar una plantilla.
     */
    public function create(DocumentTemplate $documentTemplate)
    {
        return Inertia::render('SubmittedDocuments/Create', [
            'template' => $documentTemplate,
        ]);
    }

    /**
     * Guardar el documento enviado.
     */
    public function store(Request $request, DocumentTemplate $documentTemplate)
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'data' => 'required|array',
        ]);

        $submittedDocument = SubmittedDocument::create([
            'document_template_id' => $documentTemplate->id,
            'company_id' => $companyId,
            'submitted_by_user_id' => Auth::id(),
            'data' => $request->input('data'),
            'token' => Str::uuid(),
        ]);

        return redirect()->route('submitted-documents.index')
            ->with('success', 'Documento enviado correctamente.');
    }
}
