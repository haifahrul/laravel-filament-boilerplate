<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Audit Log';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('log_name')
                    ->label('Tabel')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model'),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('search_in_properties')
                    ->label('Cari di Properties')
                    ->form([
                        TextInput::make('query')->label('Kata Kunci'),
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['query'])
                            return $query;

                        return $query->where(function ($q) use ($data) {
                            $q->where('properties->attributes->name', 'like', "%{$data['query']}%")
                                ->orWhere('properties->attributes->title', 'like', "%{$data['query']}%")
                                ->orWhere('properties->attributes->description', 'like', "%{$data['query']}%");
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return $data['query']
                            ? "Cari: {$data['query']}"
                            : null;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->modalHeading('Detail Log')
                    ->modalContent(function (Activity $record) {
                        $changes = $record->properties->toArray();
                        $old     = data_get($changes, 'old', []);
                        $new     = data_get($changes, 'attributes', []);

                        return view('filament.resources.activity-resource.pages.activity-details', [
                            'old' => $old,
                            'new' => $new,
                        ]);
                    }),
            ])
            ->bulkActions([]); // Matikan bulk delete
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
