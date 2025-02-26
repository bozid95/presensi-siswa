<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Concatenate;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;
use App\Models\Student;


class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Alpa' => 'Alpa',
                    ])
                    ->required(),
                Forms\Components\RichEditor::make('notes')
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('generate daily attendance')
                    ->label('Generate Daily Attendance')
                    ->action(function () {
                        $students = Student::all();
                        $today = Carbon::today();

                        foreach ($students as $student) {
                            $attendance = Attendance::where('student_id', $student->id)
                                ->whereDate('date', $today)
                                ->first();

                            if (!$attendance) {
                                Attendance::create([
                                    'student_id' => $student->id,
                                    'date' => $today,
                                    'status' => 'Hadir',
                                    'user_id' => Auth::id(),
                                ]);
                            }
                        }

                        session()->flash('message', 'Attendance generated successfully!');
                    })
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
            ])
            ->query(Attendance::query())
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Name')
                    ->searchable(),



                Tables\Columns\TextColumn::make('student.class_room.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'name')
                    ->searchable(),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('until')->label('To Date'),
                    ])
                    ->query(
                        fn($query, array $data) =>
                        $query
                            ->when($data['from'] ?? null, fn($q, $from) => $q->whereDate('date', '>=', $from))
                            ->when($data['until'] ?? null, fn($q, $until) => $q->whereDate('date', '<=', $until))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }








    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
