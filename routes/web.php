<?php

use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;


Route::get('/public/presensi', [PresensiController::class, 'index'])->name('presensi');
Route::get('/public/presensi/search', [PresensiController::class, 'getAttendanceByNisn'])->name('presensi.search');
