<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OrderItemExport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make([
                TextInput::make('order_number')->required()->unique(ignoreRecord: true),
                DatePicker::make('order_date')->required(),
                Select::make('user_id')
                    ->label('Sales')
                    ->options(User::role('sales')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                // Select::make('user_id')
                //     ->relationship(
                //         name: 'user',
                //         titleAttribute: 'name',
                //         modifyQueryUsing: fn ($query) => $query->role('sales')
                //     )
                //     ->label('Sales')
                //     ->searchable()
                //     ->required(),
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->required(),

                Repeater::make('items')
                    ->relationship()
                    ->label('Produk')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->options(Product::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $items = $get('../../items'); // semua item dari repeater
                                $index = $get('__index');     // index saat ini
                                $qty   = $items[$index]['quantity'] ?? 1;

                                $product = Product::find($state);
                                if ($product) {
                                    $set('price', $product->price);
                                    $set('subtotal', $product->price * $qty);
                                }

                                // Hitung ulang total
                                $newItems = $get('../../items');
                                $total    = collect($newItems)->sum('subtotal');
                                $set('../../total_amount', $total);
                            }),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = $get('price') ?? 0;
                                $set('subtotal', $state * $price);

                                $items = $get('../../items');
                                $total = collect($items)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 1));
                                $set('../../total_amount', $total);
                            }),

                        TextInput::make('price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $qty = $get('quantity') ?? 1;
                                $set('subtotal', $state * $qty);

                                $items = $get('../../items');
                                $total = collect($items)->sum(fn ($item) => ($item['price'] ?? 0) * ($item['quantity'] ?? 1));
                                $set('../../total_amount', $total);
                            }),

                        TextInput::make('subtotal')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                    ])
                    ->createItemButtonLabel('Tambah Produk')
                    ->defaultItems(1)
                    ->columns(4)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $total = collect($state)->sum('subtotal');
                        $set('total_amount', $total);
                    }),

                TextInput::make('total_amount')
                    ->label('Total')
                    ->numeric()
                    ->readOnly()
                    ->afterStateHydrated(function (callable $set, $record) {
                        $total = $record?->items->sum('subtotal') ?? 0;
                        $set('total_amount', $total);
                    }),

                Textarea::make('notes'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Order')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Sales')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Filter Sales')
                    ->relationship('user', 'name'),

                SelectFilter::make('customer_id')
                    ->label('Filter Customer')
                    ->relationship('customer', 'name'),
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
                Action::make('Export Excel')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('to')->label('Sampai Tanggal'),
                    ])
                    ->action(function (array $data) {
                        $filename = 'laporan-order-' . now()->format('Ymd_His') . '.xlsx';
                        return Excel::download(new OrderExport($data['from'], $data['to']), $filename);
                    })
                    ->color('gray'),

                Action::make('Export PDF')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('to')->label('Sampai Tanggal'),
                    ])
                    ->action(function (array $data) {
                        $orders = (new OrderExport($data['from'], $data['to']))->collection();
                        $pdf    = Pdf::loadView('exports.orders', ['orders' => $orders]);
                        return response()->streamDownload(fn () => print ($pdf->stream()), 'laporan-order.pdf');
                    })
                    ->color('gray'),
                Action::make('Export Detail Order (Excel)')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('to')->label('Sampai Tanggal'),
                    ])
                    ->action(function (array $data) {
                        $filename = 'laporan-order-items-' . now()->format('Ymd_His') . '.xlsx';
                        return Excel::download(new OrderItemExport($data['from'], $data['to']), $filename);
                    })
                    ->color('gray'),
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
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
