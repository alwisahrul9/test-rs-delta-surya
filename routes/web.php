<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    // Halaman halaman yang hanya bisa diakses oleh admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('admin.home');
        Route::resource('users', UserController::class);
    });

    // Halaman halaman yang hanya bisa diakses oleh doctor
    Route::middleware('role:doctor')->prefix('doctor')->group(function () {
        Route::get('/home', [HomeController::class, 'doctorIndex'])->name('doctor.home');
        Route::resource('patients', PatientController::class)->except('show'); // Kecualikan fungsi show
        Route::get('/proxy/medicines', [PatientController::class, 'getMedicines'])->name('proxy.medicines');
    });

    // Halaman halaman yang hanya bisa diakses oleh pharmacist (apoteker)
    Route::middleware('role:pharmacist')->prefix('pharmacist')->group(function () {
        Route::get('/home', [HomeController::class, 'pharmacistIndex'])->name('pharmacist.home');
        Route::get('/download-prescription/{id}', [PatientController::class, 'printPrescription'])->name('print.prescription');
        Route::resource('prescription', PrescriptionController::class);
    });

    // Halaman halaman yang hanya bisa diakses oleh doctor maupun pharmacist
    Route::middleware('role:doctor|pharmacist')->prefix('details')->group(function () {
        Route::resource('patients', PatientController::class)->only('show'); // Ambil hanya fungsi show
    });
});
