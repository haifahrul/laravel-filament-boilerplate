<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Traits\HasResourcePermissions;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    use HasResourcePermissions;
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('sku')
                ->required()
                ->rule(fn ($record) => \Illuminate\Validation\Rule::unique('products', 'sku')->ignore($record)),
            Textarea::make('description'),
            TextInput::make('price')->numeric()->required(),
            FileUpload::make('image_path')
                ->directory('products')
                ->image()
                ->label('Image'),
            Toggle::make('status')
                ->label('Aktif')
                ->inline(false)
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('sku'),
            TextColumn::make('price')->money('IDR'),
            ImageColumn::make('image_path')
                ->square()
                ->label('Image'),
            IconColumn::make('status')
                ->boolean()
                ->label('Aktif'),
        ])->filters([
                    SelectFilter::make('status')
                        ->label('Status')
                        ->options([
                            1 => 'Aktif',
                            0 => 'Non Aktif',
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
