<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $templates = DocumentTemplate::where('company_id', $companyId)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('DocumentTemplates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('DocumentTemplates/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'fields' => 'nullable|array',
        ]);

        DocumentTemplate::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'fields' => $validated['fields'] ?? [],
        ]);

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        $this->authorizeTemplate($documentTemplate);

        return Inertia::render('DocumentTemplates/Show', [
            'template' => $documentTemplate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        $this->authorizeTemplate($documentTemplate);

        return Inertia::render('DocumentTemplates/Edit', [
            'template' => $documentTemplate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        $this->authorizeTemplate($documentTemplate);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'fields' => 'nullable|array',
        ]);

        $documentTemplate->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'fields' => $validated['fields'] ?? [],
        ]);

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        $this->authorizeTemplate($documentTemplate);

        $documentTemplate->delete();

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla eliminada correctamente.');
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Request $request, $id)
    {
        $originalTemplate = DocumentTemplate::findOrFail($id);
        $this->authorizeTemplate($originalTemplate);

        $newTemplate = $originalTemplate->replicate();
        $newTemplate->name = $originalTemplate->name . ' (Copia)';
        $newTemplate->save();

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla duplicada correctamente.');
    }

    /**
     * Verifica que la plantilla pertenezca a la empresa del usuario autenticado.
     */
    private function authorizeTemplate(DocumentTemplate $documentTemplate)
    {
        if ($documentTemplate->company_id !== auth()->user()->company_id) {
            abort(403, 'No autorizado.');
        }
    }
}