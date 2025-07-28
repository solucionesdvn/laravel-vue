<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;


//Formatos
use App\Http\Controllers\FresignationController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



//Empleados, pruebas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    
    //Compañia
    Route::resource('companies', CompanyController::class);
    //Route::resource('categories', CategoryController::class);
    //Route::resource('suppliers', SupplierController::class);
    
});
// Segurridad empleados, pruebas

//Users

Route::resource("users", UserController::class)
    ->only(['create', 'store'])
    ->middleware("permission:users.create");

Route::resource("users", UserController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:users.edit");

Route::resource("users", UserController::class)
    ->only(['destroy'])
    ->middleware("permission:users.delete");

Route::resource("users", UserController::class)
    ->only(['index', 'show'])
    ->middleware("permission:users.create|users.edit|users.delete|users.view");

//Roles
Route::resource("roles", RoleController::class);

Route::resource("roles", RoleController::class)
    ->only(['create', 'store'])
    ->middleware("permission:roles.create");

Route::resource("roles", RoleController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:roles.edit");

Route::resource("roles", RoleController::class)
    ->only(['destroy'])
    ->middleware("permission:roles.delete");

Route::resource("roles", RoleController::class)
    ->only(['index', 'show'])
    ->middleware("permission:roles.create|roles.edit|roles.delete|roles.view");
    
//Proveedores Suppliers
Route::resource("suppliers", SupplierController::class);

Route::resource("suppliers", SupplierController::class)
    ->only(['create', 'store'])
    ->middleware("permission:suppliers.create");

Route::resource("suppliers", SupplierController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:suppliers.edit");

Route::resource("suppliers", SupplierController::class)
    ->only(['destroy'])
    ->middleware("permission:suppliers.delete");

Route::resource("suppliers", SupplierController::class)
    ->only(['index', 'show'])
    ->middleware("permission:suppliers.create|suppliers.edit|suppliers.delete|suppliers.view");


//Categorias -
Route::resource("categories", CategoryController::class);

Route::resource("categories", CategoryController::class)
    ->only(['create', 'store'])
    ->middleware("permission:categories.create");

Route::resource("categories", CategoryController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:categories.edit");

Route::resource("categories", CategoryController::class)
    ->only(['destroy'])
    ->middleware("permission:categories.delete");

Route::resource("categories", CategoryController::class)
    ->only(['index', 'show'])
    ->middleware("permission:categories.create|categories.edit|categories.delete|categories.view");













//Rutas Formatos

Route::resource('fresignations', FresignationController::class)->middleware(['auth', 'verified']);
Route::put('/fresignations/{fresignation}', [FresignationController::class, 'update'])->name('fresignations.update');


Route::resource("fresignations", RoleController::class);

Route::resource("fresignations", RoleController::class)
    ->only(['create', 'store'])
    ->middleware("permission:fresignations.create");

Route::resource("fresignations", RoleController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:fresignations.edit");

Route::resource("fresignations", RoleController::class)
    ->only(['destroy'])
    ->middleware("permission:fresignations.delete");

Route::resource("fresignations", RoleController::class)
    ->only(['index', 'show'])
    ->middleware("permission:fresignations.create|fresignations.edit|fresignations.delete|fresignations.view");


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';