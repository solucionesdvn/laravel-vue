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
        // The ForCompany trait automatically filters these queries.
        $submittedDocuments = SubmittedDocument::with(['template', 'submittedBy'])
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->through(fn ($doc) => [
                'id' => $doc->id,
                'created_at' => $doc->created_at,
                'document_template' => $doc->template,
                'submitted_by_user' => $doc->submittedBy,
                'token' => $doc->token,
            ])
            ->withQueryString();

        $documentTemplates = DocumentTemplate::get();

        return Inertia::render('SubmittedDocuments/Index', [
            'submittedDocuments' => $submittedDocuments,
            'documentTemplates' => $documentTemplates,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Muestra el formulario para crear un documento a partir de una plantilla.
     */
    public function create(DocumentTemplate $documentTemplate)
    {
        // Route-model binding on DocumentTemplate handles authorization via global scope.
        return Inertia::render('SubmittedDocuments/Create', [
            'template' => $documentTemplate,
        ]);
    }

    /**
     * Guarda el documento rellenado.
     */
    public function store(Request $request, DocumentTemplate $documentTemplate)
    {
        // Route-model binding on DocumentTemplate handles authorization.
        $rules = $this->getValidationRules($documentTemplate);
        $validated = $request->validate($rules);

        // The boot method on SubmittedDocument handles company_id and token.
        $submitted = SubmittedDocument::create([
            'submitted_by_user_id' => auth()->id(),
            'document_template_id' => $documentTemplate->id,
            'data'                 => $validated['data'],
        ]);

        return redirect()
            ->route('submitted-documents.show', $submitted->id)
            ->with('success', 'Documento creado exitosamente.');
    }

    /**
     * Muestra un documento ya generado.
     */
    public function show(SubmittedDocument $submittedDocument)
    {
        // Route-model binding on SubmittedDocument handles authorization.
        $submittedDocument->load('template');

        return Inertia::render('SubmittedDocuments/Show', [
            'submittedDocument' => [
                'id' => $submittedDocument->id,
                'data' => $submittedDocument->data,
                'document_template' => $submittedDocument->template,
            ],
        ]);
    }

    /**
     * Muestra el formulario para editar un documento existente.
     */
    public function edit(SubmittedDocument $submittedDocument)
    {
        // Route-model binding on SubmittedDocument handles authorization.
        $submittedDocument->load('template');

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
    public function update(Request $request, SubmittedDocument $submittedDocument)
    {
        // Route-model binding on SubmittedDocument handles authorization.
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
    public function regenerateToken(SubmittedDocument $submittedDocument)
    {
        // Route-model binding on SubmittedDocument handles authorization.
        $submittedDocument->update([
            'token' => Str::uuid(),
        ]);

        return back()->with('success', 'Se ha generado un nuevo enlace para compartir.');
    }

    /**
     * Exporta un documento a PDF.
     */
    public function exportPdf(SubmittedDocument $submittedDocument)
    {
        // Route-model binding on SubmittedDocument handles authorization.
        $submittedDocument->load('template');

        $template = $submittedDocument->template;
        $content = $template->content;
        $data = $submittedDocument->data;

        // Reemplazar placeholders con los datos
        foreach ($data as $key => $value) {
            $content = str_replace("{{ " . $key . " }}", htmlspecialchars((string)$value), $content);
        }

        // Limpiar placeholders que no fueron llenados
        $content = preg_replace('/{{(.*?)}}/', '', $content);

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>' . $template->name . '</title><style>body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }</style></head><body>' . $content . '</body></html>';

        $pdf = Pdf::loadHTML($html);
        return $pdf->stream('documento-' . $submittedDocument->id . '.pdf');
    }

    /**
     * Muestra una versión pública del documento enviado.
     */
    public function publicShow($token)
    {
        $submittedDocument = SubmittedDocument::where('token', $token)->firstOrFail();
        $submittedDocument->load('template');

        if (is_null($submittedDocument->template)) {
            abort(404, 'Document template not found for this submitted document.');
        }

        return Inertia::render('SubmittedDocuments/PublicShow', [
            'submittedDocument' => [
                'id' => $submittedDocument->id,
                'token' => $submittedDocument->token,
                'data' => $submittedDocument->data,
                'document_template' => $submittedDocument->template,
            ],
        ]);
    }

    /**
     * Exporta la versión pública del documento enviado como PDF.
     */
    public function publicPdf($token)
    {
        $submittedDocument = SubmittedDocument::where('token', $token)->firstOrFail();
        $submittedDocument->load('template');
        
        $template = $submittedDocument->template;
        $content = $template->content;
        $data = $submittedDocument->data;

        foreach ($data as $key => $value) {
            $content = str_replace("{{ " . $key . " }}", htmlspecialchars((string)$value), $content);
        }
        $content = preg_replace('/{{(.*?)}}/', '', $content);

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>' . $template->name . '</title><style>body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }</style></head><body>' . $content . '</body></html>';

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
        $validated = validator(['data' => $request->all()], $rules)->validate();

        $submittedDocument->update([
            'data' => $validated['data'],
            'token' => null,
        ]);

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
}
