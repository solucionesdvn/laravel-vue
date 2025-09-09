<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\SubmittedDocument;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SubmittedDocumentController extends Controller
{
    /**
     * Muestra la página de éxito después de un envío público.
     */
    public function publicSuccess()
    {
        return Inertia::render('SubmittedDocuments/PublicSuccess');
    }

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
            ->through(fn ($doc) => [
                'id' => $doc->id,
                'created_at' => $doc->created_at,
                'document_template' => $doc->template, // Cargar explícitamente la relación
                'submitted_by_user' => $doc->submittedBy, // Cargar explícitamente la relación
                'token' => $doc->token,
            ])
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

        $rules = $this->getValidationRules($template);
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
            'submittedDocument' => [
                'id' => $submitted->id,
                'data' => $submitted->data,
                'document_template' => $submitted->template,
            ],
        ]);
    }

    /**
     * Muestra el formulario para editar un documento existente.
     */
    public function edit($id)
    {
        $submittedDocument = SubmittedDocument::with('template')->findOrFail($id);
        $this->authorizeSubmittedDocument($submittedDocument);

        return Inertia::render('SubmittedDocuments/Edit', [
            'submittedDocument' => [
                'id' => $submittedDocument->id,
                'data' => $submittedDocument->data,
                'document_template' => $submittedDocument->template,
            ],
        ]);
    }

    /**
     * Actualiza un documento existente.
     */
    public function update(Request $request, $id)
    {
        $submittedDocument = SubmittedDocument::findOrFail($id);
        $this->authorizeSubmittedDocument($submittedDocument);

        $template = $submittedDocument->template;
        $rules = $this->getValidationRules($template);
        $validated = $request->validate($rules);

        $submittedDocument->update([
            'data' => $validated['data'],
        ]);

        return back()->with('success', 'Documento actualizado exitosamente.');
    }

    /**
     * Regenera el token para un documento enviado.
     */
    public function regenerateToken($id)
    {
        $submittedDocument = SubmittedDocument::findOrFail($id);
        $this->authorizeSubmittedDocument($submittedDocument);

        $submittedDocument->update([
            'token' => Str::uuid(),
        ]);

        return back()->with('success', 'Se ha generado un nuevo enlace para compartir.');
    }

    /**
     * Obtiene las reglas de validación para una plantilla de documento.
     */
    public function exportPdf($id)
    {
        $submittedDocument = SubmittedDocument::with('template')->findOrFail($id);
        $this->authorizeSubmittedDocument($submittedDocument);

        $template = $submittedDocument->template;
        $content = $template->content;
        $data = $submittedDocument->data;

        // --- TEMPORARY LOGGING START ---
        \Log::info('PDF Export Debug: Document ID ' . $id);
        \Log::info('PDF Export Debug: Template Content: ' . $content);
        \Log::info('PDF Export Debug: Submitted Data: ' . json_encode($data));
        // --- TEMPORARY LOGGING END ---

        // Reemplazar placeholders con los datos
        foreach ($data as $key => $value) {
            $content = str_replace("{{ " . $key . " }}", htmlspecialchars((string)$value), $content);
        }

        // Limpiar placeholders que no fueron llenados
        $content = preg_replace('/{{(.*?)}}/', '', $content);

        // Estructura HTML básica para el PDF
        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{$template->name}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
    </style>
</head>
<body>
    {$content}
</body>
</html>
HTML;

        $pdf = Pdf::loadHTML($html);
        return $pdf->stream('documento-' . $submittedDocument->id . '.pdf');
    }

    /**
     * Muestra una versión pública del documento enviado.
     */
    public function publicShow($token)
    {
        $submittedDocument = SubmittedDocument::where('token', $token)->firstOrFail();

        // Ensure the template relationship is loaded
        $submittedDocument->load('template');

        // If template is null, it means the related template was deleted.
        // We should handle this gracefully, e.g., abort or redirect.
        if (is_null($submittedDocument->template)) {
            abort(404, 'Document template not found for this submitted document.');
        }

        return Inertia::render('SubmittedDocuments/PublicShow', [
            'submittedDocument' => [
                'id' => $submittedDocument->id,
                'token' => $submittedDocument->token,
                'data' => $submittedDocument->data,
                'document_template' => $submittedDocument->template, // Clave renombrada para consistencia
            ],
        ]);
    }

    /**
     * Exporta la versión pública del documento enviado como PDF.
     */
    public function publicPdf($token)
    {
        $submittedDocument = SubmittedDocument::where('token', $token)->firstOrFail();

        $template = $submittedDocument->template;
        $content = $template->content;
        $data = $submittedDocument->data;

        // --- TEMPORARY LOGGING START ---
        \Log::info('PDF Export Debug (Public): Token ' . $token);
        \Log::info('PDF Export Debug (Public): Template Content: ' . $content);
        \Log::info('PDF Export Debug (Public): Submitted Data: ' . json_encode($data));
        // --- TEMPORARY LOGGING END ---

        // Reemplazar placeholders con los datos
        foreach ($data as $key => $value) {
            $content = str_replace("{{ " . $key . " }}", htmlspecialchars((string)$value), $content);
        }

        // Limpiar placeholders que no fueron llenados
        $content = preg_replace('/{{(.*?)}}/', '', $content);

        // Estructura HTML básica para el PDF
        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{$template->name}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
    </style>
</head>
<body>
    {$content}
</body>
</html>
HTML;

        $pdf = Pdf::loadHTML($html);
        return $pdf->stream('documento-' . $submittedDocument->id . '.pdf');
    }

    /**
     * Actualiza un documento desde el formulario público.
     */
    public function publicUpdate(Request $request, $token)
    {
        $submittedDocument = SubmittedDocument::where('token', $token)->firstOrFail();
        $template = $submittedDocument->template;

        if (!$template) {
            abort(404, 'La plantilla para este documento no fue encontrada.');
        }

        $rules = $this->getValidationRules($template);

        // Se envuelven los datos del request en la clave 'data' para que coincida con las reglas de validación.
        $validated = validator(['data' => $request->all()], $rules)->validate();

        $submittedDocument->update([
            'data' => $validated['data'],
            'token' => null, // Invalidar el token para que no se pueda volver a usar
        ]);

        // Redirigir a la página de éxito
        return redirect()->route('public.submitted-documents.success');
    }

    /**
     * Obtiene las reglas de validación para una plantilla de documento.
     */
    private function getValidationRules(DocumentTemplate $template): array
    {
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
                $fieldType = $field['type'] ?? 'text';
                if (isset($fieldTypesToRules[$fieldType])) {
                    $rules['data.' . $fieldName] = $fieldTypesToRules[$fieldType];
                } else {
                    $rules['data.' . $fieldName] = 'required|string';
                }
            }
        }

        return $rules;
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
