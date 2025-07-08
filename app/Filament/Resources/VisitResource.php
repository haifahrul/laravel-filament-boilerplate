<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')->relationship('user', 'name')->required()->searchable(),
            Select::make('customer_id')->relationship('customer', 'name')->required()->searchable(),
            Select::make('activity_type')->options([
                'visit'        => 'Visit',
                'presentation' => 'Presentation',
                'follow-up'    => 'Follow-Up',
                'offering'     => 'Offering',
            ])->required(),
            Textarea::make('note'),
            FileUpload::make('photo_path')->directory('visits')->image(),
            DateTimePicker::make('checked_in_at'),
            DateTimePicker::make('checked_out_at'),
            TextInput::make('latitude')->numeric(),
            TextInput::make('longitude')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index'  => Pages\ListVisits::route('/'),
            'create' => Pages\CreateVisit::route('/create'),
            'edit'   => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}
