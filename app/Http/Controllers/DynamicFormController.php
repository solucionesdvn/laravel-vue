<?php

namespace App\Http\Controllers;

use App\Models\FormDefinition;
use App\Models\FormData;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class DynamicFormController extends Controller
{
    // --- FormDefinition CRUD ---

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = FormDefinition::where('company_id', $companyId);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $definitions = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('DynamicForms/Definitions/Index', [
            'definitions' => $definitions,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('DynamicForms/Definitions/Create');
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:form_definitions,slug,NULL,id,company_id,' . $companyId,
            'description' => 'nullable|string',
            'fields' => 'required|array', // Validate as array, detailed validation can be done in a FormRequest
        ]);

        FormDefinition::create([
            'company_id' => $companyId,
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'fields' => $validated['fields'],
        ]);

        return redirect()->route('form-definitions.index')->with('success', 'Definición de formulario creada correctamente.');
    }

    public function show(FormDefinition $formDefinition)
    {
        $this->authorizeAccess($formDefinition);
        return Inertia::render('DynamicForms/Definitions/Show', [
            'formDefinition' => $formDefinition,
        ]);
    }

    public function edit(FormDefinition $formDefinition)
    {
        $this->authorizeAccess($formDefinition);
        return Inertia::render('DynamicForms/Definitions/Edit', [
            'formDefinition' => $formDefinition,
        ]);
    }

    public function update(Request $request, FormDefinition $formDefinition)
    {
        $this->authorizeAccess($formDefinition);
        $companyId = auth()->user()->company_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:form_definitions,slug,' . $formDefinition->id . ',id,company_id,' . $companyId,
            'description' => 'nullable|string',
            'fields' => 'required|array',
        ]);

        $formDefinition->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'fields' => $validated['fields'],
        ]);

        return redirect()->route('form-definitions.index')->with('success', 'Definición de formulario actualizada correctamente.');
    }

    public function destroy(FormDefinition $formDefinition)
    {
        $this->authorizeAccess($formDefinition);
        $formDefinition->delete(); // This will also delete related FormData due to cascadeOnDelete

        return redirect()->route('form-definitions.index')->with('success', 'Definición de formulario eliminada correctamente.');
    }

    // --- FormData CRUD ---

    public function formDataIndex(Request $request) // Renamed to avoid conflict with index()
    {
        $companyId = auth()->user()->company_id;
        $query = FormData::where('company_id', $companyId)->with('formDefinition');

        if ($request->filled('search')) {
            $query->where('token', 'like', '%' . $request->input('search') . '%'); // Example search
        }

        $formData = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('DynamicForms/Data/Index', [
            'formData' => $formData,
            'filters' => $request->only('search'),
        ]);
    }

    public function formDataCreate(FormDefinition $formDefinition) // Renamed
    {
        $this->authorizeAccess($formDefinition);
        return Inertia::render('DynamicForms/Data/Create', [
            'formDefinition' => $formDefinition,
        ]);
    }

    public function formDataStore(Request $request, FormDefinition $formDefinition) // Renamed
    {
        $this->authorizeAccess($formDefinition);
        $companyId = auth()->user()->company_id;

        // Basic validation for the 'data' field as a whole
        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        // TODO: Implement detailed validation based on $formDefinition->fields
        // This would involve iterating through $formDefinition->fields and applying rules

        FormData::create([
            'company_id' => $companyId,
            'form_definition_id' => $formDefinition->id,
            'data' => $validated['data'],
            'token' => Str::uuid(),
        ]);

        return redirect()->route('form-data.index')->with('success', 'Datos de formulario guardados correctamente.');
    }

    public function formDataShow(FormData $formData) // Renamed
    {
        $this->authorizeAccess($formData);
        $formData->load('formDefinition');
        return Inertia::render('DynamicForms/Data/Show', [
            'formData' => $formData,
        ]);
    }

    public function formDataEdit(FormData $formData) // Renamed
    {
        $this->authorizeAccess($formData);
        $formData->load('formDefinition');
        return Inertia::render('DynamicForms/Data/Edit', [
            'formData' => $formData,
        ]);
    }

    public function formDataUpdate(Request $request, FormData $formData) // Renamed
    {
        $this->authorizeAccess($formData);

        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        // TODO: Implement detailed validation based on $formData->formDefinition->fields

        $formData->update([
            'data' => $validated['data'],
        ]);

        return redirect()->route('form-data.index')->with('success', 'Datos de formulario actualizados correctamente.');
    }

    public function formDataDestroy(FormData $formData) // Renamed
    {
        $this->authorizeAccess($formData);
        $formData->delete();

        return redirect()->route('form-data.index')->with('success', 'Datos de formulario eliminados correctamente.');
    }

    // --- Public Access Methods ---

    public function publicEdit(string $token)
    {
        $formData = FormData::where('token', $token)->firstOrFail();
        $formData->load('formDefinition');

        // TODO: Implement logic to filter fields based on public_editable property
        // and pass only editable fields to the view.

        return Inertia::render('DynamicForms/Public/Edit', [
            'formData' => $formData,
            'formDefinition' => $formData->formDefinition,
        ]);
    }

    public function publicUpdate(Request $request, string $token)
    {
        $formData = FormData::where('token', $token)->firstOrFail();
        $formData->load('formDefinition');

        // TODO: Implement detailed validation based on $formData->formDefinition->fields
        // and ensure only public_editable fields are updated.

        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        $formData->update([
            'data' => $validated['data'],
        ]);

        return Inertia::render('DynamicForms/Public/Thanks');
    }

    public function publicPdf(string $token)
    {
        $formData = FormData::where('token', $token)->firstOrFail();
        $formData->load('formDefinition');

        $pdf = Pdf::loadView('exports.dynamic_form_pdf', [ // TODO: Create this generic Blade view
            'formData' => $formData,
            'formDefinition' => $formData->formDefinition,
        ]);

        return $pdf->stream($formData->formDefinition->slug . '.pdf');
    }

    // --- Authorization Helper ---

    private function authorizeAccess($model)
    {
        if ($model->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
