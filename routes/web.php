<?php

use App\Http\Controllers\AppartementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\ChargeMensuelleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\DegatReparationController;
use App\Http\Controllers\DisponibiliteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'web', 'auth', 'verified', 'locale'],
    ],
    function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Reservations
        Route::resource('reservations', ReservationController::class);

        // Dépenses
        Route::resource('depenses', DepenseController::class);

        // Charges mensuelles
        Route::resource('charges', ChargeMensuelleController::class);

        // Stocks
        Route::resource('stocks', StockController::class);

        // Dégâts et réparations
        Route::resource('degats', DegatReparationController::class);

        // Disponibilités
        Route::resource('disponibilites', DisponibiliteController::class);

        // appartements
        Route::resource('appartements', AppartementController::class);
        Route::patch('appartements/{appartement}/toggle-status', [AppartementController::class, 'toggleStatus'])->name('appartements.toggle-status');

        // Reports
        Route::get('/reports/monthly', [App\Http\Controllers\ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/reports/occupancy', [App\Http\Controllers\ReportController::class, 'occupancy'])->name('reports.occupancy');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Settings
        Route::get('/settings', function () {
            return Inertia::render('Settings');
        })->name('settings');

        // Language switching
        Route::post('/language', [LanguageController::class, 'switch'])->name('language.switch');


       Route::post('/settings/push-sqlite', [SettingsController::class, 'pushSqlite'])
    ->name('settings.push-sqlite');
    },
);

// Theme switching
Route::post('/user/theme', function () {
    // Handle theme preference saving
    return response()->json(['success' => true]);
})->name('user.theme');

// Notifications
Route::post('/notifications/mark-all-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return response()->json(['success' => true]);
})->name('notifications.mark-all-read');

require __DIR__ . '/auth.php';
