<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Attendance;
use Carbon\Carbon;
use Dom\Text;

class AbsentStudentsTableWidget extends BaseWidget

{
    protected static ?int $sort = 1; // Menentukan posisi widget di dashboard
    protected static ?string $heading = 'Students Absent Today';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->whereDate('created_at', Carbon::today()) // Filter berdasarkan hari ini
                    ->whereNotIn('status', ['hadir']) // Kecuali yang hadir
                    ->with('student') // Pastikan relasi ke siswa
            )
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student Name')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state))),
                TextColumn::make('student.class_room.name')
                    ->label('Class')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date(),
            ]);
    }
}
