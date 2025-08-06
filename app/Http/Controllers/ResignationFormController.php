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
        $query = ResignationForm::where('company_id', auth()->user()->company_id);

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

        ResignationForm::create([
            'company_id' => auth()->user()->company_id,
            'full_name' => $data['full_name'],
            'resignation_date' => $data['resignation_date'],
            'reason' => $data['reason'],
            'signature' => $data['signature'] ?? null,
            'token' => Str::uuid(),
        ]);

        return redirect()->route('resignation-forms.index');
    }

    public function show(ResignationForm $resignationForm)
    {
        $this->authorizeAccess($resignationForm);

        return Inertia::render('ResignationForms/Show', [
            'form' => $resignationForm,
        ]);
    }

    public function edit(ResignationForm $resignationForm)
    {
        $this->authorizeAccess($resignationForm);

        return Inertia::render('ResignationForms/Edit', [
            'form' => $resignationForm,
        ]);
    }

    public function update(Request $request, ResignationForm $resignationForm)
    {
        $this->authorizeAccess($resignationForm);

        return Inertia::render('ResignationForms/Edit', [
            'formData' => $resignationForm->only(['id', 'full_name', 'resignation_date', 'reason', 'signature']),
        ]);
    }

    public function destroy(ResignationForm $resignationForm)
    {
        $this->authorizeAccess($resignationForm);

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

        return $pdf->stream('carta-renuncia.pdf'); // También puedes usar ->download('carta.pdf')
    }

    private function authorizeAccess(ResignationForm $form)
    {
        if ($form->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}