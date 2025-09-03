<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\SubmittedDocument;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubmittedDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $submittedDocuments = SubmittedDocument::where('company_id', $companyId)
            ->with(['template', 'submittedBy'])
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $documentTemplates = DocumentTemplate::where('company_id', $companyId)->get(); // Fetch all document templates

        return Inertia::render('SubmittedDocuments/Index', [
            'submittedDocuments' => $submittedDocuments,
            'documentTemplates' => $documentTemplates, // Pass document templates to the view
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Muestra el formulario para crear un documento a partir de una plantilla.
     */
    public function create($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $this->authorizeDocumentTemplate($template);

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
        $this->authorizeDocumentTemplate($template);

        // Validación dinámica basada en los campos de la plantilla
        $rules = ['data' => 'required|array'];
        $fieldTypesToRules = [
            'text' => 'required|string|max:255',
            'textarea' => 'required|string|max:5000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'email' => 'required|email',
        ];

        if (is_array($template->fields)) {
            foreach ($template->fields as $field) {
                $fieldName = $field['name'];
                $fieldType = $field['type'] ?? 'text'; // Por defecto 'text'
                if (isset($fieldTypesToRules[$fieldType])) {
                    $rules['data.' . $fieldName] = $fieldTypesToRules[$fieldType];
                } else {
                    $rules['data.' . $fieldName] = 'required|string'; // Regla por defecto
                }
            }
        }

        $validated = $request->validate($rules);

        $submitted = SubmittedDocument::create([
            'submitted_by_user_id' => auth()->id(),
            'company_id'           => auth()->user()->company_id,
            'document_template_id' => $template->id,
            'data'                 => $validated['data'],
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
        $this->authorizeSubmittedDocument($submitted);

        return Inertia::render('SubmittedDocuments/Show', [
            'submitted' => $submitted,
        ]);
    }

    /**
     * Verifica que la plantilla pertenezca a la empresa del usuario autenticado.
     */
    private function authorizeDocumentTemplate(DocumentTemplate $documentTemplate)
    {
        if ($documentTemplate->company_id !== auth()->user()->company_id) {
            abort(403, 'No autorizado.');
        }
    }

    /**
     * Verifica que el documento pertenezca a la empresa del usuario autenticado.
     */
    private function authorizeSubmittedDocument(SubmittedDocument $submittedDocument)
    {
        if ($submittedDocument->company_id !== auth()->user()->company_id) {
            abort(403, 'No autorizado.');
        }
    }
}
