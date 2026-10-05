<?php

use App\Http\Controllers\AdminCourtController;
use App\Http\Controllers\AdminCustomizationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminFinanceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffTodayController;
use App\Http\Controllers\StaffWalkInController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $courts = \App\Models\Court::where('court_status', 'available')->get();
    $events = \App\Models\Event::query()
        ->where('start_date', '<=', now()->addDays(30)->toDateString())
        ->where('end_date', '>=', now()->toDateString())
        ->orderBy('start_date')
        ->get();
    $siteSettings = \App\Models\SiteSettings::first();

    return view('welcome', compact('courts', 'events', 'siteSettings'));
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Central Authenticated Dashboard Router
|--------------------------------------------------------------------------
| Routes authenticated users strictly according to their assigned role.
*/
Route::get('/dashboard', function () {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    return match (true) {
        $user?->isAdmin() || $user?->hasRole('manager') => redirect()->route('admin.dashboard'),
        $user?->hasRole('staff') => redirect()->route('staff.today'),
        default => redirect()->route('booking'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('booking');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin & Manager Workspace
|--------------------------------------------------------------------------
| Restricted to admin and manager roles with verified accounts.
*/
Route::middleware(['auth', 'verified', 'role:admin,manager'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/finance', [AdminFinanceController::class, 'index'])->name('finance');
        Route::resource('courts', AdminCourtController::class)
            ->except(['show'])
            ->whereNumber('court');
        Route::resource('events', AdminEventController::class);
        Route::get('/customization', [AdminCustomizationController::class, 'index'])->name('customization.index');
        Route::patch('/customization', [AdminCustomizationController::class, 'update'])->name('customization.update');
    });

/*
|--------------------------------------------------------------------------
| Staff Operations Workspace
|--------------------------------------------------------------------------
| Restricted to staff roles; includes parameter validation and rate limiting.
*/
Route::middleware(['auth', 'verified', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/today', [StaffTodayController::class, 'index'])->name('today');
        Route::patch('/bookings/{booking}/status', [StaffTodayController::class, 'updateStatus'])
            ->whereNumber('booking')
            ->middleware('throttle:60,1')
            ->name('bookings.status');
        Route::get('/walk-in', [StaffWalkInController::class, 'create'])->name('walkin.create');
        Route::post('/walk-in', [StaffWalkInController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('walkin.store');
    });

/*
|--------------------------------------------------------------------------
| Player Booking Portal
|--------------------------------------------------------------------------
| Restricted to players; prevents staff and admin from booking slots.
| Throttled to prevent reservation flood attacks.
*/
Route::middleware(['auth', 'verified', 'prevent.staff_admin_booking'])
    ->group(function () {
        Route::get('/booking', [BookingController::class, 'create'])->name('booking');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [BookingController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('bookings.store');
    });

/*
|--------------------------------------------------------------------------
| User Account & Profile Management
|--------------------------------------------------------------------------
| Requires authenticated session; rate limited against brute force/tampering.
*/
Route::middleware('auth')
    ->prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])
            ->middleware('throttle:15,1')
            ->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])
            ->middleware('throttle:5,1')
            ->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze / OTP / Password Management)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';