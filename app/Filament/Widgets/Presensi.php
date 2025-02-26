<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;

class Presensi extends ChartWidget
{
    protected static ?string $heading = 'Chart attendance this month';

    protected int | string | array $columnSpan = '2';

    protected static ?string $maxHeight = '370px';

    protected static ?int $sort = 2;


    /**
     * Mengambil data untuk chart
     *
     * @return array
     */
    protected function getData(): array
    {
        // Mengambil tanggal pertama dan terakhir bulan ini
        $startDate = now()->startOfMonth(); // Tanggal pertama bulan ini
        $endDate = now()->endOfMonth(); // Tanggal terakhir bulan ini

        // Mengambil jumlah absensi per status untuk setiap hari dalam rentang waktu bulan ini
        $hadir = Attendance::where('status', 'Hadir')->whereBetween('date', [$startDate, $endDate])->get()->groupBy('date');
        $sakit = Attendance::where('status', 'Sakit')->whereBetween('date', [$startDate, $endDate])->get()->groupBy('date');
        $alpa = Attendance::where('status', 'Alpa')->whereBetween('date', [$startDate, $endDate])->get()->groupBy('date');
        $izin = Attendance::where('status', 'izin')->whereBetween('date', [$startDate, $endDate])->get()->groupBy('date');

        // Menghitung jumlah absensi per status untuk setiap hari
        $dates = [];
        $hadirData = [];
        $sakitData = [];
        $alpaData = [];
        $izinData = [];

        // Looping untuk setiap tanggal dalam bulan berjalan
        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $dates[] = $date;
            $hadirData[] = $hadir->has($date) ? $hadir[$date]->count() : 0;
            $sakitData[] = $sakit->has($date) ? $sakit[$date]->count() : 0;
            $alpaData[] = $alpa->has($date) ? $alpa[$date]->count() : 0;
            $izinData[] = $izin->has($date) ? $izin[$date]->count() : 0;

            // Pindah ke hari berikutnya
            $currentDate->addDay();
        }

        // Menyusun data untuk chart (line chart)
        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $hadirData,
                    'borderColor' => '#4CAF50',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Sakit',
                    'data' => $sakitData,
                    'borderColor' => '#FFEB3B',
                    'backgroundColor' => 'rgba(255, 235, 59, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Alpha',
                    'data' => $alpaData,
                    'borderColor' => '#FF5722',
                    'backgroundColor' => 'rgba(255, 87, 34, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Tidak Hadir',
                    'data' => $izinData,
                    'borderColor' => '#F44336',
                    'backgroundColor' => 'rgba(244, 67, 54, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $dates, // Tanggal untuk setiap titik pada chart
        ];
    }

    /**
     * Mendefinisikan tipe chart yang akan digunakan
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'line'; // Menggunakan tipe line chart
    }
}
