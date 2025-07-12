<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Visit;
use App\Services\GeoService;
use Dotswan\MapPicker\Fields\Map;
use Dotswan\MapPicker\Infolists\MapEntry;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VisitExport;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Get;
use Filament\Forms\Set;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make([
                Grid::make(2)->schema([
                    Select::make('user_id')
                        ->label('Sales')
                        ->relationship('user', 'name')
                        ->default(auth()->id()),

                    Select::make('customer_id')
                        ->relationship('customer', 'name')
                        ->required()
                        ->searchable(),

                    Select::make('activity_type')
                        ->options([
                            'visit'        => 'Visit',
                            'presentation' => 'Presentation',
                            'follow-up'    => 'Follow-Up',
                            'offering'     => 'Offering',
                        ])
                        ->required(),

                    Textarea::make('note')->columnSpanFull(),

                    DateTimePicker::make('checked_in_at'),
                    DateTimePicker::make('checked_out_at'),

                    FileUpload::make('photo_path')
                        ->directory('visits')
                        ->image()
                        ->label('Photo')
                        ->columnSpanFull(),

                    TextInput::make('latitude')
                        ->numeric()
                        ->required()
                        ->reactive(),

                    TextInput::make('longitude')
                        ->numeric()
                        ->required()
                        ->reactive(),

                    // TextInput::make('address')->label('Alamat Lengkap')->readonly()->dehydrated(),

                    // TextInput::make('address')
                    //     ->columnSpanFull()
                    //     ->label('Cari Alamat')
                    //     ->afterStateUpdated(function (Get $get, Set $set) {
                    //         $location = GeoService::getLocationFromAddress($get('address'));
                    //         if ($location) {
                    //             $set('location', ['lat' => $location['latitude'], 'lng' => $location['longitude']]);
                    //             $set('latitude', $location['latitude']);
                    //             $set('longitude', $location['longitude']);
                    //         }
                    //     })
                    //     ->live()
                    //     ->maxLength(255),

                    // TextInput::make('address')
                    //     ->label('Alamat')
                    //     ->columnSpanFull()
                    //     ->hint('Cari alamat, misalnya "Bandung"')
                    //     ->dehydrated()
                    //     ->live(debounce: 500)
                    //     ->afterStateUpdated(function (Get $get, Set $set) {
                    //         $geo = GeoService::getLocationFromAddress($get('address'));

                    //         if ($geo) {
                    //             $set('location', ['lat' => $geo['latitude'], 'lng' => $geo['longitude']]);
                    //             $set('latitude', $geo['latitude']);
                    //             $set('longitude', $geo['longitude']);
                    //         }
                    //     }),

                    Map::make('location')
                        ->label('Pilih Lokasi')
                        ->columnSpanFull()
                        // ->defaultLocation(latitude: -6.2, longitude: 106.8)
                        ->draggable(true)
                        // ->clickable(true)
                        ->showMyLocationButton(true)
                        ->showMarker(true)
                        ->afterStateUpdated(function (Get $get, Set $set, $old, $state) {
                            $set('latitude', $state['lat']);
                            $set('longitude', $state['lng']);

                            $geo = GeoService::getAddressFromCoordinates($state['lat'], $state['lng']);
                            if ($geo) {
                                $set('address', $geo);
                            }
                        })
                        ->afterStateHydrated(function ($state, $record, Set $set) {
                            if ($record?->latitude && $record?->longitude) {
                                $set('location', [
                                    'lat' => $record->latitude,
                                    'lng' => $record->longitude,
                                ]);
                            }
                        }),

                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Sales')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('activity_type')
                    ->label('Activity')
                    ->colors([
                        'primary' => 'visit',
                        'success' => 'presentation',
                        'warning' => 'follow-up',
                        'danger'  => 'offering',
                    ])
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'visit'        => 'Visit',
                        'presentation' => 'Presentation',
                        'follow-up'    => 'Follow-Up',
                        'offering'     => 'Offering',
                        default        => ucfirst($state),
                    })
                    ->sortable(),

                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->height(40)
                    ->width(40),

                TextColumn::make('checked_in_at')
                    ->label('Check In')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('checked_out_at')
                    ->label('Check Out')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('latitude')
                    ->label('Lat')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('longitude')
                    ->label('Lng')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('note')
                    ->label('Note')
                    ->limit(40)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('activity_type')
                    ->label('Activity')
                    ->options([
                        'visit'        => 'Visit',
                        'presentation' => 'Presentation',
                        'follow-up'    => 'Follow-Up',
                        'offering'     => 'Offering',
                    ]),

                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Sales'),

                SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label('Customer'),

                Filter::make('checked_in_range')
                    ->form([
                        DateTimePicker::make('from')->label('Check In'),
                        DateTimePicker::make('until')->label('Check Out'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->where('checked_in_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->where('checked_in_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('Export Kunjungan (Excel)')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('to')->label('Sampai Tanggal'),
                    ])
                    ->action(function (array $data) {
                        $filename = 'laporan-kunjungan-' . now()->format('Ymd_His') . '.xlsx';
                        return Excel::download(new VisitExport($data['from'], $data['to']), $filename);
                    })
                    ->color('success'),
            ])
        ;
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
