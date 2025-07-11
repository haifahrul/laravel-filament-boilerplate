<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Visit;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('Sales')
                ->relationship('user', 'name')
                ->default(auth()->id()), // sembunyikan di halaman create
            Select::make('customer_id')->relationship('customer', 'name')->required()->searchable(),
            Select::make('activity_type')->options([
                'visit'        => 'Visit',
                'presentation' => 'Presentation',
                'follow-up'    => 'Follow-Up',
                'offering'     => 'Offering',
            ])->required(),
            Textarea::make('note'),
            DateTimePicker::make('checked_in_at'),
            DateTimePicker::make('checked_out_at'),
            TextInput::make('latitude')->numeric(),
            TextInput::make('longitude')->numeric(),
            FileUpload::make('photo_path')->directory('visits')->image()->label('Photo'),
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
