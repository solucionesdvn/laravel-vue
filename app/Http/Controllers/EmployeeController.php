<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Employees/Index', [
            'employees' => Employee::all(), // SIN PAGINACIÓN
            //'employees' => Employee::select('id', 'name', 'email', 'position')->get(),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Employees/Create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'position' => 'required|string|max:255',
        'salary' => 'required|numeric|min:0',
        ]);
        Employee::create($request->all());
        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return Inertia::render('Employees/Edit',[
            'employee' => $employee
        ]);        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'position' => 'required|string|max:255',
        'salary' => 'required|numeric|min:0',
        ]);

        $employee->update($request->all());
        return redirect()->route('employees.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return Inertia::render('Employees/Index',[
            'employees' => Employee::all()
        ]);
    }
}
