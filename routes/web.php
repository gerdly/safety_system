<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\PublicHazardReport;
use App\Livewire\HazardReview;
use App\Livewire\HazardReportPrint;

Route::get('/', function () {
     return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    // New route for Hazards Management
    Route::get('/hazards', function () {
        return view('hazards.index');
    })->name('hazards.index');
    // New route for User Management (CRUD)
    Route::get('/users', function () {
        return view('users.index');
    })->name('users.index');

    Route::get('/sms/hazard-review/{id}', HazardReview::class)->name('hazard.review');
    Route::get('/sms/hazard-report-print/{id}', HazardReportPrint::class)->name('hazard.print');
});
// Public route for reporting hazards (Mobile/QR friendly)
Route::get('/report-hazard', PublicHazardReport::class)->name('report.hazard');