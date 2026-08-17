<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\TerminController;

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // SPK
    Route::get('/spk/create', [SpkController::class, 'create'])->name('spk.create');
    Route::get('/spk/check-duplicate', [SpkController::class, 'checkDuplicate'])->name('spk.checkDuplicate');
    Route::post('/spk/parse-pdf', [SpkController::class, 'parsePdf'])->name('spk.parsePdf');
    Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
    Route::get('/spk/{id}', [SpkController::class, 'show'])->name('spk.show');
    
    // Termin
    Route::patch('/termin/{id}/status', [TerminController::class, 'updateStatus'])->name('termin.updateStatus');
    Route::get('/termin/{id}/surat-jalan', [TerminController::class, 'suratJalan'])->name('termin.suratJalan');
    Route::post('/termin/{id}/surat-jalan', [TerminController::class, 'storeSuratJalan'])->name('termin.storeSuratJalan');
    Route::get('/termin/{id}/perincian', [TerminController::class, 'perincian'])->name('termin.perincian');
    Route::get('/termin/{id}/perincian/download', [TerminController::class, 'downloadPerincian'])->name('termin.downloadPerincian');
    Route::post('/termin/{id}/lampiran', [TerminController::class, 'uploadLampiran'])->name('termin.uploadLampiran');
    
    // Arsip
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    
    // Pembayaran
    Route::get('/pembayaran/cocokkan', [PembayaranController::class, 'cocokkan'])->name('pembayaran.cocokkan');
    Route::get('/pembayaran/konfirmasi/{terminId}', [PembayaranController::class, 'showKonfirmasi'])->name('pembayaran.konfirmasi');
    Route::post('/pembayaran/konfirmasi/{terminId}', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasiStore');
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    
    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});
