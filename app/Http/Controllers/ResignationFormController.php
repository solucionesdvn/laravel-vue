<?php

namespace App\Http\Controllers;

use App\Models\ResignationForm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ResignationFormController extends Controller
{
    public function index(Request $request)
    {
        // The ForCompany trait automatically filters ResignationForm queries.
        $query = ResignationForm::query();

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%' . $request->input('search') . '%');
        }

        $forms = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('ResignationForms/Index', [
            'forms' => $forms,
            'filters' => [
                'search' => $request->input('search', ''),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('ResignationForms/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'resignation_date' => 'required|date',
            'reason' => 'required|string',
            'signature' => 'nullable|string',
        ]);

        // The ForCompany trait automatically adds the company_id.
        ResignationForm::create($data);

        return redirect()->route('resignation-forms.index');
    }

    public function show(ResignationForm $resignationForm)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('ResignationForms/Show', [
            'form' => $resignationForm,
        ]);
    }

    public function edit(ResignationForm $resignationForm)
    {
        // The ForCompany trait's global scope handles authorization.
        return Inertia::render('ResignationForms/Edit', [
            'form' => $resignationForm,
        ]);
    }

    public function update(Request $request, ResignationForm $resignationForm)
    {
        // The ForCompany trait's global scope handles authorization.
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'resignation_date' => 'required|date',
            'reason' => 'required|string',
            'signature' => 'nullable|string',
        ]);

        $resignationForm->update($data);

        return redirect()->route('resignation-forms.index'); // Assuming this should redirect to index after update
    }

    public function destroy(ResignationForm $resignationForm)
    {
        // The ForCompany trait's global scope handles authorization.
        $resignationForm->delete();

        return redirect()->route('resignation-forms.index');
    }

    // Cliente edita desde enlace público
    public function publicEdit(string $token)
    {
        $form = ResignationForm::where('token', $token)->firstOrFail();

        return Inertia::render('ResignationForms/PublicEdit', [
            'form' => $form,
        ]);
    }

    public function publicUpdate(Request $request, string $token)
    {
        $form = ResignationForm::where('token', $token)->firstOrFail();

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'resignation_date' => 'required|date',
            'reason' => 'required|string',
            'signature' => 'nullable|string',
        ]);

        $form->update($data);

        return Inertia::render('ResignationForms/PublicThanks');
    }

    // 🆕 Método para exportar o imprimir desde enlace público
    public function publicPdf(string $token)
    {
        $form = ResignationForm::where('token', $token)->firstOrFail();

        $pdf = Pdf::loadView('exports.resignations.pdf', [
            'form' => $form,
        ]);

        return $pdf->stream('carta-renuncia.pdf');
    }
}
