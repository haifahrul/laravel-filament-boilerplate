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
                                if (!$state) {
                                    // Reset jika product_id dihapus
                                    $set('price', 0);
                                    $set('subtotal', 0);
                                    return;
                                }

                                $product = Product::find($state);
                                if ($product) {
                                    $set('price', $product->price);
                                    $qty = $get('quantity') ?: 1;
                                    $set('subtotal', $qty * $product->price);
                                }
                            }),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = $get('price') ?: 0;
                                $set('subtotal', $state * $price);
                            }),

                        TextInput::make('price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $qty = $get('quantity') ?: 1;
                                $set('subtotal', $state * $qty);
                            }),

                        TextInput::make('subtotal')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(false)
                            ->default(0),
                    ])
                    ->createItemButtonLabel('Tambah Produk')
                    ->defaultItems(1)
                    ->columns(4),

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
