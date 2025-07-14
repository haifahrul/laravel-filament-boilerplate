<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PPdbResource\Pages;
use App\Models\Ppdb;
use App\Traits\HasResourcePermissions;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PpdbsExport;

class PpdbResource extends Resource
{
    use HasResourcePermissions;
    protected static ?string $model = Ppdb::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'PPDB';
    protected static ?int $navigationSort = -1;

    public static function form(Form $form): Form
    {
        return $form->schema([ 
            TextInput::make('full_name')->label('Nama Lengkap')->required()->maxLength(255),
            TextInput::make('place_of_birth')->label('Tempat Lahir')->required(),
            DatePicker::make('date_of_birth')->label('Tanggal Lahir')->required(),
            Textarea::make('address')->label('Alamat')->required(),
            TextInput::make('city')->label('Kota')->required(),
            TextInput::make('phone_number')->label('No. HP')->required()->maxLength(30),
            TextInput::make('email')->label('Email')->email()->required(),
            TextInput::make('origin_school')->label('Asal Sekolah')->required(),
            TextInput::make('current_class')->label('Kelas Saat Ini')->required(),
            TextInput::make('school_year')->label('Tahun Ajaran')->required()->maxLength(12),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([ 
            TextColumn::make('full_name')->label('Nama')->searchable(),
            TextColumn::make('place_of_birth')->label('Tempat Lahir'),
            TextColumn::make('date_of_birth')->label('Tgl Lahir')->date(),
            TextColumn::make('phone_number')->label('HP'),
            TextColumn::make('origin_school')->label('Asal Sekolah'),
            TextColumn::make('created_at')->label('Tanggal Daftar')->dateTime(),
        ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->headerActions([ 
                Action::make('Export ke Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => Excel::download(new PpdbsExport, 'ppdb.xlsx')),
            ])
            ->actions([ 
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [ 
            'index' => Pages\ListPpdbs::route('/'),
        ];
    }
}
