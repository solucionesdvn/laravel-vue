<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Fresignation;
use Illuminate\Http\Request;
use Inertia\Inertia;


class FresignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fresignations = Fresignation::with('company')->get();
        return Inertia::render('Fresignations/Index', [
         'fresignations' => $fresignations->map(fn ($item) => [
            'id' => $item->id,
            'resignation_date' => $item->resignation_date,
            'city' => $item->city,
            'company_name' => $item->company_name,
            'position' => $item->position,
            'start_date' => $item->start_date,
            'end_date' => $item->end_date,
            'reason' => $item->reason,
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
            $companies = Company::all(['id', 'name']); // si necesitas mostrar compañías

            return Inertia::render('Fresignations/Create', [
                'companies' => $companies,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'resignation_date' => 'required|date',
            'city' => 'required|string',
            'company_name' => 'required|string',
            'position' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $data['company_id'] = auth()->user()->company_id;

        Fresignation::create($data);

        return to_route('fresignations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resignation $resignation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resignation $resignation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resignation $resignation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resignation $resignation)
    {
        //
    }
}
