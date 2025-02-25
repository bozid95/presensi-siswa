<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        return view('presensi');
    }

    // Fungsi untuk mencari absensi berdasarkan NISN
    public function getAttendanceByNisn(Request $request)
    {
        $nisn = $request->input('nisn');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Cari data siswa berdasarkan NISN
        $student = Student::where('nisn', $nisn)->first();

        if (!$student) {
            return back()->with('error', 'Siswa tidak ditemukan');
        }

        // Query absensi berdasarkan NISN dan rentang tanggal
        $attendanceQuery = Attendance::where('student_id', $student->id);

        if ($startDate) {
            $attendanceQuery->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $attendanceQuery->where('date', '<=', $endDate);
        }

        $attendance = $attendanceQuery->get();

        // Hitung summary per status
        $statusSummary = $attendance->groupBy('status')->map(function ($statusGroup) {
            return $statusGroup->count();
        });

        return view('presensi', compact('student', 'attendance', 'statusSummary'));
    }
}
