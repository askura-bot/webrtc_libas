<?php

use App\Http\Controllers\Admin\CredentialController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Officer\LiveController;
use Illuminate\Support\Facades\Route;


// Root redirect
Route::get('/', function () {
    return auth()->check()
        ? (auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('officer.live'))
        : redirect()->route('login');
});

// Auth
Route::get('/login', [LoginController::class, 'show'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Officer
Route::middleware(['auth', 'officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/live', [LiveController::class, 'index'])->name('live');
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/streams', [StreamController::class, 'index'])->name('streams');
    Route::get('/credentials', [CredentialController::class, 'index'])->name('credentials');
    Route::put('/credentials/officer', [CredentialController::class, 'updateOfficer'])->name('credentials.officer');
    Route::put('/credentials/admin', [CredentialController::class, 'updateAdmin'])->name('credentials.admin');
});

Route::get('/test-stream', function () {
    return view('test_stream');
});
