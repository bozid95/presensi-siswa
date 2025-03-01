<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

use App\Models\ClassRoom;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nisn')
                ->numeric()
                ->requiredMapping()
                ->rules([
                    'required',
                    'digits_between:1,10',
                    Rule::unique('students', 'nisn') // Pastikan NISN unik
                ]),
            ImportColumn::make('kelas')
                ->requiredMapping()
                ->relationship('class_room', 'name')
        ];
    }
    public function resolveRecord(): ?Student
    {
        // Cari student berdasarkan NISN yang diimport
        $nisn = $this->data['nisn'] ?? null;

        if (!$nisn) {
            return null; // Jika tidak ada NISN, lewati
        }

        // Cek apakah student sudah ada berdasarkan NISN
        $existingStudent = Student::where('nisn', $nisn)->first();

        if ($existingStudent) {
            return null; // Jika sudah ada, skip (tidak membuat duplikat)
        }

        return new Student(); // Jika belum ada, buat data baru
    }


    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
