<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\ProductExitController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ResignationFormController;
use App\Http\Controllers\Export\DocumentExportController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\SubmittedDocumentController;



use App\Exports\ProductsExport;




//Formatos
use App\Http\Controllers\FresignationController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');



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


Route::resource("payment-methods", PaymentMethodController::class)->except(['show']);

Route::resource("payment-methods", PaymentMethodController::class)
    ->only(['create', 'store'])
    ->middleware("permission:payment-methods.create");

Route::resource("payment-methods", PaymentMethodController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:payment-methods.edit");

Route::resource("payment-methods", PaymentMethodController::class)
    ->only(['destroy'])
    ->middleware("permission:payment-methods.delete");

Route::resource("payment-methods", PaymentMethodController::class)
    ->only(['index'])
    ->middleware("permission:payment-methods.create|payment-methods.edit|payment-methods.delete|payment-methods.view");


//Productos -

Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search')->middleware('auth');


Route::resource('products', ProductController::class)->except(['show']);


Route::put('products/{product}/update-image', [ProductController::class, 'updateImage'])->name('products.updateImage');

Route::resource("products", ProductController::class);

Route::resource("products", ProductController::class)
    ->only(['create', 'store'])
    ->middleware("permission:products.create");

Route::resource("products", ProductController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:products.edit");

Route::resource("products", ProductController::class)
    ->only(['destroy'])
    ->middleware("permission:products.delete");

Route::resource("products", ProductController::class)
    ->only(['index', 'show'])
    ->middleware("permission:products.create|products.edit|products.delete|users.view");




//Entradas -
Route::resource("entries", EntryController::class);

Route::resource("entries", EntryController::class)
    ->only(['create', 'store'])
    ->middleware("permission:entries.create");

Route::resource("entries", EntryController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:entries.edit");

Route::resource("entries", EntryController::class)
    ->only(['destroy'])
    ->middleware("permission:entries.delete");

Route::resource("entries", EntryController::class)
    ->only(['index', 'show'])
    ->middleware("permission:entries.create|entries.edit|entries.delete|entries.view");
    

//Salidas

Route::resource("product-exits", ProductExitController::class);

Route::resource("product-exits", ProductExitController::class)
    ->only(['create', 'store'])
    ->middleware("permission:product-exits.create");

Route::resource("product-exits", ProductExitController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:product-exits.edit");

Route::resource("product-exits", ProductExitController::class)
    ->only(['destroy'])
    ->middleware("permission:product-exits.delete");

Route::resource("product-exits", ProductExitController::class)
    ->only(['index', 'show'])
    ->middleware("permission:product-exits.create|product-exits.edit|product-exits.delete|product-exits.view");


//CAJA
Route::resource("cash-registers", CashRegisterController::class);

Route::resource("cash-registers", CashRegisterController::class)
    ->only(['create', 'store'])
    ->middleware("permission:cash-registers.create");

Route::resource("cash-registers", CashRegisterController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:cash-registers.edit");

Route::resource("cash-registers", CashRegisterController::class)
    ->only(['destroy'])
    ->middleware("permission:cash-registers.delete");

Route::middleware('permission:cash-registers.close')->group(function () {
    Route::get('cash-registers/{cash_register}/close', [CashRegisterController::class, 'close'])->name('cash-registers.close');
    Route::put('cash-registers/{cash_register}/close', [CashRegisterController::class, 'closeStore'])->name('cash-registers.close.store');
});

Route::resource("cash-registers", CashRegisterController::class)
    ->only(['index', 'show'])
    ->middleware("permission:cash-registers.create|cash-registers.edit|cash-registers.delete|cash-registers.view");


    
 //VENTAS
Route::resource("sales", SaleController::class);

Route::resource("sales", SaleController::class)
    ->only(['create', 'store'])
    ->middleware("permission:sales.create");

Route::resource("sales", SaleController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:sales.edit");

Route::resource("sales", SaleController::class)
    ->only(['destroy'])
    ->middleware("permission:sales.delete");

Route::resource("sales", SaleController::class)
    ->only(['index', 'show'])
    ->middleware("permission:sales.create|sales.edit|sales.delete|sales.view");

//Clientes -
Route::post('/clients/api-store', [ClientController::class, 'apiStore'])->name('clients.api.store')->middleware(['auth', 'permission:clients.create']);
Route::resource("clients", ClientController::class);

Route::resource("clients", ClientController::class)
    ->only(['create', 'store'])
    ->middleware("permission:clients.create");

Route::resource("clients", ClientController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:clients.edit");

Route::resource("clients", ClientController::class)
    ->only(['destroy'])
    ->middleware("permission:clients.delete");

Route::resource("clients", ClientController::class)
    ->only(['index', 'show'])
    ->middleware("permission:clients.create|clients.edit|users.delete|clients.view");



    //Rutas Formatos NUEVAMENE

Route::resource("resignation-forms", ResignationFormController::class);

Route::resource("resignation-forms", ResignationFormController::class)
    ->only(['create', 'store'])
    ->middleware("permission:resignation-forms.create");

Route::resource("resignation-forms", ResignationFormController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:resignation-forms.edit");

Route::resource("resignation-forms", ResignationFormController::class)
    ->only(['destroy'])
    ->middleware("permission:resignation-forms.delete");

Route::resource("resignation-forms", ResignationFormController::class)
    ->only(['index', 'show'])
    ->middleware("permission:resignation-forms.create|resignation-forms.edit|delete|resignation-forms.view");


// Rutas publicas

Route::get('/public/resignation/{token}', [ResignationFormController::class, 'publicEdit'])->name('resignation-forms.public.edit');
Route::put('/public/resignation/{token}', [ResignationFormController::class, 'publicUpdate'])->name('resignation-forms.public.update');


//Rutas Exportar PDF

Route::middleware(['auth'])->group(function () {
    Route::get('/resignation-forms/{id}/export-pdf', [DocumentExportController::class, 'exportResignationPDF'])
        ->name('resignation-forms.export.pdf');
});


// Rutas impresion
Route::get('/public/resignation/{token}', [ResignationFormController::class, 'show']);
Route::get('/public/resignation/{token}', [ResignationFormController::class, 'publicPdf']);
Route::get('/public/resignation/pdf/{token}', [ResignationFormController::class, 'publicPdf']);



// Modulos de Documentos dinamicos Rutas

Route::resource("document-templates", DocumentTemplateController::class);

Route::resource("document-templates", DocumentTemplateController::class)
    ->only(['create', 'store'])
    ->middleware("permission:document-templates.create");

Route::resource("document-templates", DocumentTemplateController::class)
    ->only(['edit', 'update'])
    ->middleware("permission:document-templates.edit");

Route::resource("document-templates", DocumentTemplateController::class)
    ->only(['destroy'])
    ->middleware("permission:document-templates.delete");

Route::post('document-templates/{id}/duplicate', [DocumentTemplateController::class, 'duplicate'])
    ->name('document-templates.duplicate')
    ->middleware('permission:document-templates.create');

Route::resource("document-templates", DocumentTemplateController::class)
    ->only(['index', 'show'])
    ->middleware("permission:document-templates.create|document-templates.edit|document-templates.delete|document-templates.view");

Route::resource('document-templates', DocumentTemplateController::class)->parameters([
    'document-templates' => 'id'
]);

// --- Rutas para Documentos Enviados (Submitted Documents) ---
// Ruta 'create' personalizada para aceptar el ID de la plantilla
Route::get('submitted-documents/create/{id}', [SubmittedDocumentController::class, 'create'])
    ->name('submitted-documents.create')
    ->middleware(['auth', 'permission:submitted-documents.create']);

// Ruta 'store' personalizada para que también acepte el ID de la plantilla
Route::post('submitted-documents/{id}', [SubmittedDocumentController::class, 'store'])
    ->name('submitted-documents.store')
    ->middleware(['auth', 'permission:submitted-documents.create']);

// Resto de las rutas del recurso, con sus permisos específicos
Route::resource('submitted-documents', SubmittedDocumentController::class)
    ->except(['create', 'store'])
    ->parameters(['submitted-documents' => 'id'])
    ->middleware(['auth', 'permission:submitted-documents.view|submitted-documents.edit|submitted-documents.delete']);

// Ruta para exportar un documento enviado a PDF
Route::get('submitted-documents/{id}/export-pdf', [SubmittedDocumentController::class, 'exportPdf'])
    ->name('submitted-documents.export.pdf')
    ->middleware(['auth', 'permission:submitted-documents.view']);

// --- Rutas Públicas para Documentos Enviados ---
Route::get('/public/submitted-documents/{token}', [SubmittedDocumentController::class, 'publicShow'])->name('public.submitted-documents.show');
Route::get('/public/submitted-documents/pdf/{token}', [SubmittedDocumentController::class, 'publicPdf'])->name('public.submitted-documents.pdf');



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';