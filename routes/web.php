<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminCourtController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminFinanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:view_admin_dashboard')
        ->name('dashboard');
    Route::get('/finance', [AdminFinanceController::class, 'index'])
        ->middleware('permission:view_finances')
        ->name('finance');
    Route::resource('courts', AdminCourtController::class)
        ->middleware('permission:manage_courts')
        ->except(['show']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/booking', fn () => view('booking'))->name('booking');