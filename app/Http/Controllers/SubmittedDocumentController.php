<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\SubmittedDocument;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SubmittedDocumentController extends Controller
{
    /**
     * Muestra el formulario para crear un documento a partir de una plantilla.
     */
    public function create($id)
    {
        $template = DocumentTemplate::findOrFail($id);

        return Inertia::render('SubmittedDocuments/Create', [
            'template' => $template,
        ]);
    }

    /**
     * Guarda el documento rellenado.
     */
    public function store(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        // 🔹 Aceptamos cualquier campo dinámico de la plantilla y los hacemos requeridos
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*' => 'required|string', // Hace que cada campo dentro de 'data' sea requerido
        ]);

        $submitted = SubmittedDocument::create([
            'submitted_by_user_id' => auth()->id(),
            'company_id'           => auth()->user()->company_id, // multiempresa
            'document_template_id' => $template->id,
            'data'                 => $validated['data'], // Guardamos solo el array 'data' en JSON
            'token'                => Str::uuid(), // token único
        ]);

        return redirect()
            ->route('submitted-documents.show', $submitted->id)
            ->with('success', 'Documento creado exitosamente.');
    }

    /**
     * Muestra un documento ya generado.
     */
    public function show($id)
    {
        $submitted = SubmittedDocument::with('template')->findOrFail($id);

        return Inertia::render('SubmittedDocuments/Show', [
            'submitted' => $submitted,
        ]);
    }
}