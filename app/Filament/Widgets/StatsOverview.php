<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array

    {
        // Mengambil waktu hari ini di zona waktu GMT+7
        $today = now()->startOfDay(); // Mulai hari ini (00:00)
        $endOfDay = now()->endOfDay(); // Akhir hari ini (23:59)

        // Mengambil data yang relevan dari model
        $totalStudents = Student::count(); // Jumlah total siswa
        $totalAttendance = Attendance::whereBetween('date', [$today, $endOfDay])->count(); // Jumlah absensi hari ini

        // Menambahkan statistik berdasarkan status absensi
        $totalPresent = Attendance::where('status', 'Hadir')
            ->whereBetween('date', [$today, $endOfDay])
            ->count(); // Jumlah siswa hadir hari ini

        $totalAlpa = Attendance::where('status', 'Alpa')
            ->whereBetween('date', [$today, $endOfDay])
            ->count(); // Jumlah siswa alpha hari ini

        $totalSick = Attendance::where('status', 'Sakit')
            ->whereBetween('date', [$today, $endOfDay])
            ->count(); // Jumlah siswa sakit hari ini

        return [
            Stat::make('Students', $totalStudents),
            Stat::make('Attendance Today', $totalAttendance),
            Stat::make('Students Present', $totalPresent),
            Stat::make('Students Alpha', $totalAlpa),
            Stat::make('Students Sick', $totalSick),
        ];
    }
}
