<?php

use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;


Route::get('/cari/presensi', [PresensiController::class, 'index'])->name('presensi');
Route::get('/cari/presensi', [PresensiController::class, 'getAttendanceByNisn'])->name('presensi.search');
