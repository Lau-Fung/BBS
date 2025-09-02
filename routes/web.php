<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use PragmaRX\Google2FAQRCode\Google2FA;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ImportAssignmentsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['ar','en']), 404);
    session(['locale' => $locale]);
    // Optional: if you use cache for config/trans, you don't need to clear here.
    return back();
})->name('locale.switch');

Route::get('/user/two-factor-qr-code', function () {
    $user = auth()->user();

    if (! $user->two_factor_secret) {
        abort(404);
    }

    $google2fa = app(Google2FA::class);

    $svg = $google2fa->getQRCodeInline(
        config('app.name'),
        $user->email,
        decrypt($user->two_factor_secret)
    );

    return response($svg, 200)->header('Content-Type', 'image/svg+xml');
})->middleware(['auth'])->name('two-factor.qr-code');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::view('/profile/security', 'profile.security')->name('profile.security');

     // Minimal records routes for Milestone-1
    Route::prefix('records')->name('records.')->group(function () {
        Route::get('/', [RecordController::class, 'index'])->name('index');       // records.index
        Route::get('/create', [RecordController::class, 'create'])->name('create');
        Route::post('/', [RecordController::class, 'store'])->name('store');
        Route::get('/{record}/edit', [RecordController::class, 'edit'])->name('edit');
        Route::put('/{record}', [RecordController::class, 'update'])->name('update');
        Route::delete('/{record}', [RecordController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth','role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->only(['index','edit','update']);
    
    // Domain records (role/permission-based)
    // Route::prefix('records')->name('records.')->group(function () {
    //     Route::get('/', [RecordController::class,'index'])
    //         ->middleware('permission:records.view')->name('index');

    //     Route::get('/create', [RecordController::class,'create'])
    //         ->middleware('permission:records.create')->name('create');

    //     Route::post('/', [RecordController::class,'store'])
    //         ->middleware('permission:records.create')->name('store');

    //     Route::get('/{record}/edit', [RecordController::class,'edit'])
    //         ->middleware('permission:records.update')->name('edit');

    //     Route::put('/{record}', [RecordController::class,'update'])
    //         ->middleware('permission:records.update')->name('update');

    //     Route::delete('/{record}', [RecordController::class,'destroy'])
    //         ->middleware('permission:records.delete')->name('destroy');
    // });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('assignments', AssignmentController::class);
    Route::post('assignments/{assignment}/restore', [AssignmentController::class,'restore'])
        ->name('assignments.restore'); // if you enable soft deletes

    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/restore', [VehicleController::class,'restore'])->name('vehicles.restore');

    Route::resource('sims', SimController::class);
    Route::post('sims/{sim}/restore', [SimController::class,'restore'])->name('sims.restore');

    Route::resource('devices', DeviceController::class);
    Route::post('devices/{device}/restore', [DeviceController::class,'restore'])->name('devices.restore');

    Route::resource('sensors', SensorController::class);
    Route::post('sensors/{sensor}/restore', [SensorController::class,'restore'])->name('sensors.restore');

    Route::resource('carriers', CarrierController::class);
    Route::post('carriers/{carrier}/restore', [CarrierController::class,'restore'])->name('carriers.restore');

    Route::get ('/imports/assignments',              [ImportAssignmentsController::class,'form'])->name('imports.assignments.form');
    Route::post('/imports/assignments/preview',      [ImportAssignmentsController::class,'preview'])->name('imports.assignments.preview');
    Route::post('/imports/assignments/confirm',      [ImportAssignmentsController::class,'confirm'])->name('imports.assignments.confirm');
    Route::post('/imports/assignments/cancel',       [ImportAssignmentsController::class,'cancel'])->name('imports.assignments.cancel');
});

require __DIR__.'/auth.php';
