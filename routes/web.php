<?php

use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;


Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi');
Route::get('/presensi/search', [PresensiController::class, 'getAttendanceByNisn'])->name('presensi.search');
