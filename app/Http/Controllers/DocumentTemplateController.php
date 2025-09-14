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
        // The ForCompany trait automatically filters DocumentTemplate queries.
        $templates = DocumentTemplate::when($request->search, function ($query, $search) {
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'fields' => 'nullable|array',
        ]);

        // The ForCompany trait automatically adds the company_id.
        DocumentTemplate::create($validated);

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('DocumentTemplates/Show', [
            'template' => $documentTemplate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('DocumentTemplates/Edit', [
            'template' => $documentTemplate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        // The ForCompany trait's global scope handles authorization.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'fields' => 'nullable|array',
        ]);

        $documentTemplate->update($validated);

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        // The ForCompany trait's global scope handles authorization.
        $documentTemplate->delete();

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla eliminada correctamente.');
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Request $request, DocumentTemplate $documentTemplate)
    {
        // The ForCompany trait's global scope handles authorization for the original template.
        $newTemplate = $documentTemplate->replicate();
        $newTemplate->name = $documentTemplate->name . ' (Copia)';
        $newTemplate->save(); // The creating event from ForCompany trait will set the correct company_id.

        return redirect()
            ->route('document-templates.index')
            ->with('success', 'Plantilla duplicada correctamente.');
    }
}
