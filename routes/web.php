<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ImportAssignmentsController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard.index');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar','en']), 404);
    session(['locale' => $locale]);
    return back();
})->name('locale.switch');

Route::get('/user/two-factor-qr-code', function () {
    $user = auth()->user();
    if (! $user?->two_factor_secret) abort(404);

    $google2fa = app(Google2FA::class);
    $svg = $google2fa->getQRCodeInline(
        config('app.name'),
        $user->email,
        decrypt($user->two_factor_secret)
    );

    return response($svg, 200)->header('Content-Type', 'image/svg+xml');
})->middleware(['auth'])->name('two-factor.qr-code');

/* -------------------- AUTH’D AREA -------------------- */
Route::middleware('auth')->group(function () {
    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::view('/profile/security', 'profile.security')->name('profile.security');

    // (optional) example “records” – keep if you use it
    Route::prefix('records')->name('records.')->group(function () {
        Route::get('/', [RecordController::class, 'index'])->name('index');
        Route::get('/create', [RecordController::class, 'create'])->name('create');
        Route::post('/', [RecordController::class, 'store'])->name('store');
        Route::get('/{record}/edit', [RecordController::class, 'edit'])->name('edit');
        Route::put('/{record}', [RecordController::class, 'update'])->name('update');
        Route::delete('/{record}', [RecordController::class, 'destroy'])->name('destroy');
    });
});

/* -------------------- ADMIN (permission-based) -------------------- */
// User management – Admin only (via permission)
Route::middleware(['auth','verified'])->prefix('admin')->name('admin.')->group(function () {
    // list users (allow if you gave users.view to Manager, otherwise only Admin has it)
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class,'index'])
        ->middleware('permission:users.view')
        ->name('users.index');

    // manage users (Admin only)
    Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class,'create'])
        ->middleware('permission:users.manage')->name('users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UserController::class,'store'])
        ->middleware('permission:users.manage')->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class,'edit'])
        ->middleware('permission:users.manage')->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class,'update'])
        ->middleware('permission:users.manage')->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class,'destroy'])
        ->middleware('permission:users.manage')->name('users.destroy');
});

/* -------------------- APP DOMAIN ROUTES -------------------- */
Route::middleware(['auth', 'verified'])->group(function () {
    // Main resources (visible to Manager / Data Entry too)
    Route::resource('assignments', AssignmentController::class);
    Route::post('assignments/{assignment}/restore', [AssignmentController::class,'restore'])
        ->name('assignments.restore');

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/restore', [VehicleController::class,'restore'])->name('vehicles.restore');

    Route::resource('sims', SimController::class);
    Route::post('sims/{sim}/restore', [SimController::class,'restore'])->name('sims.restore');

    Route::resource('devices', DeviceController::class);
    Route::post('devices/{device}/restore', [DeviceController::class,'restore'])->name('devices.restore');

    Route::resource('sensors', SensorController::class);
    Route::post('sensors/{sensor}/restore', [SensorController::class,'restore'])->name('sensors.restore');

    // Reference data (Carriers) — protect with dedicated permission
    Route::resource('carriers', CarrierController::class)
        ->middleware('permission:admin.reference.manage');
    Route::post('carriers/{carrier}/restore', [CarrierController::class,'restore'])
        ->middleware('permission:admin.reference.manage')
        ->name('carriers.restore');

    // Import / Export (permission-based)
    Route::get ('/imports/assignments',         [ImportAssignmentsController::class,'form'])
        ->middleware('permission:assignments.create')
        ->name('imports.assignments.form');

    Route::post('/imports/assignments/preview', [ImportAssignmentsController::class,'preview'])
        ->middleware('permission:assignments.create')
        ->name('imports.assignments.preview');

    Route::post('/imports/assignments/confirm', [ImportAssignmentsController::class,'confirm'])
        ->middleware('permission:assignments.create')
        ->name('imports.assignments.confirm');

    Route::get('/exports/assignments',          [ImportAssignmentsController::class, 'export'])
        ->middleware('permission:assignments.export')
        ->name('exports.assignments');

    // Attachments
    Route::post('/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Clients (permission-scoped)
    Route::get('/clients',                 [ClientController::class, 'index'])
        ->middleware('permission:clients.view')->name('clients.index');
    Route::get('/clients/{client}',        [ClientController::class, 'show'])
        ->middleware('permission:clients.view')->name('clients.show');
    Route::get('/clients/{client}/export', [ClientController::class, 'export'])
        ->middleware('permission:clients.export')->name('clients.export'); // ?format=xlsx|csv|pdf
    Route::get('/clients/export/xlsx', [ClientController::class,'exportXlsx'])
        ->middleware('permission:clients.export')->name('clients.export.xlsx');
    Route::get('/clients/export/pdf',  [ClientController::class,'exportPdf'])
        ->middleware('permission:clients.export')->name('clients.export.pdf');
    Route::get('/clients/{client}/assignments/create', [ClientController::class, 'createAssignment'])->name('clients.assignments.create');
    
    // Client Sheet Rows (for direct data entry)
    Route::get('/clients/{client}/sheet-rows/create', [\App\Http\Controllers\ClientSheetRowController::class, 'create'])->name('clients.sheet-rows.create');
    Route::post('/clients/{client}/sheet-rows', [\App\Http\Controllers\ClientSheetRowController::class, 'store'])->name('clients.sheet-rows.store');
    Route::get('/clients/{client}/sheet-rows/{clientSheetRow}/edit', [\App\Http\Controllers\ClientSheetRowController::class, 'edit'])->name('clients.sheet-rows.edit');
    Route::put('/clients/{client}/sheet-rows/{clientSheetRow}', [\App\Http\Controllers\ClientSheetRowController::class, 'update'])->name('clients.sheet-rows.update');
    Route::delete('/clients/{client}/sheet-rows/{clientSheetRow}', [\App\Http\Controllers\ClientSheetRowController::class, 'destroy'])->name('clients.sheet-rows.destroy');
    
    // Test route to debug 405 error
    Route::post('/test-update-all', function() { return response()->json(['success' => true, 'message' => 'Test route works']); })->name('test.update-all');
    
    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activity}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/activity-logs-stats', [ActivityLogController::class, 'stats'])->name('activity-logs.stats');
    Route::get('/activity-logs-quick-stats', [ActivityLogController::class, 'quickStats'])->name('activity-logs.quick-stats');
    Route::get('/activity-logs/export/csv', [ActivityLogController::class, 'exportCsv'])->name('activity-logs.export.csv');
    Route::get('/activity-logs/export/pdf', [ActivityLogController::class, 'exportPdf'])->name('activity-logs.export.pdf');
});

// Edit All functionality - Outside middleware group to avoid conflicts
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/clients/{client}/edit-all-sheet-rows', [\App\Http\Controllers\ClientSheetRowController::class, 'editAll'])->name('clients.sheet-rows.edit-all');
    Route::post('/clients/{client}/update-all-sheet-rows', [\App\Http\Controllers\ClientSheetRowController::class, 'updateAll'])->name('clients.sheet-rows.update-all');
});

require __DIR__.'/auth.php';
