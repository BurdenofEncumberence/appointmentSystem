<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminCourtController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminFinanceController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminCustomizationController;
use App\Http\Controllers\StaffTodayController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $events = \App\Models\Event::query()
        ->where('start_date', '<=', now()->addDays(30)->toDateString())
        ->where('end_date', '>=', now()->toDateString())
        ->orderBy('start_date')
        ->get();

    $siteSettings = \App\Models\SiteSettings::first();

    return view('welcome', compact('events', 'siteSettings'));
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    return match(true) {
        $user?->isAdmin() => redirect()->route('admin.dashboard'),
        $user?->hasRole('staff') => redirect()->route('staff.today'),
        default => redirect()->route('customer.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/finance', [AdminFinanceController::class, 'index'])->name('finance');
    Route::resource('courts', AdminCourtController::class)->except(['show']);
    Route::resource('events', AdminEventController::class);
    Route::get('/customization', [AdminCustomizationController::class, 'index'])->name('customization.index');
    Route::patch('/customization', [AdminCustomizationController::class, 'update'])->name('customization.update');
});

Route::middleware(['auth', 'verified', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/today', [StaffTodayController::class, 'index'])->name('today');
    Route::patch('/bookings/{booking}/status', [StaffTodayController::class, 'updateStatus'])->name('bookings.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/booking', [BookingController::class, 'create'])->name('booking');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

require __DIR__.'/auth.php';