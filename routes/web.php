<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controladores
use App\Http\Controllers\{
    CompanyController,
    CategoryController,
    EmployeeController,
    FresignationController,
    RoleController,
    UserController
};

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Gestión de Compañías
    |--------------------------------------------------------------------------
    */
    Route::resource('companies', CompanyController::class);

    /*
    |--------------------------------------------------------------------------
    | Inventario y Ventas
    |--------------------------------------------------------------------------
    */
    //Route::resource('categories', CategoryController::class);

    /*
    |--------------------------------------------------------------------------
    | Gestión de Empleados
    |--------------------------------------------------------------------------
    */
    Route::resource('employees', EmployeeController::class);

    /*
    |--------------------------------------------------------------------------
    | Formatos
    |--------------------------------------------------------------------------
    */
    Route::resource('fresignations', FresignationController::class);
});

/*
|--------------------------------------------------------------------------
| Seguridad - Permisos por recurso (Users, Roles, Employees)
|--------------------------------------------------------------------------
*/

/** Users */
Route::prefix('users')->name('users.')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:users.view|users.create|users.edit|users.delete');
    Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('permission:users.create');
    Route::post('/', [UserController::class, 'store'])->name('store')->middleware('permission:users.create');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('permission:users.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update')->middleware('permission:users.edit');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:users.delete');
});

/** Roles */
Route::prefix('roles')->name('roles.')->middleware('auth')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:roles.view|roles.create|roles.edit|roles.delete');
    Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:roles.create');
    Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:roles.create');
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:roles.edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:roles.edit');
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:roles.delete');
});

/** Employees (Roles de seguridad) */
Route::prefix('employee')->name('employee.')->middleware('auth')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:employee.view|employee.create|employee.edit|employee.delete');
    Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:employee.create');
    Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:employee.create');
    Route::get('/{employee}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:employee.edit');
    Route::put('/{employee}', [RoleController::class, 'update'])->name('update')->middleware('permission:employee.edit');
    Route::delete('/{employee}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:employee.delete');
});

Route::prefix('categories')->name('categories.')->middleware('auth')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index')->middleware('permission:categories.view|categories.create|categories.edit|categories.delete');
    Route::get('/create', [CategoryController::class, 'create'])->name('create')->middleware('permission:categories.create');
    Route::post('/', [CategoryController::class, 'store'])->name('store')->middleware('permission:categories.create');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit')->middleware('permission:categories.edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update')->middleware('permission:categories.edit');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('permission:categories.delete');
});


/*
|--------------------------------------------------------------------------
| Configuración adicional
|--------------------------------------------------------------------------
*/
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
