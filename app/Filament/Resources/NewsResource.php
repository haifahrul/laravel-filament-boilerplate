<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use App\Traits\HasResourcePermissions;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\FileUpload;
use FilamentTiptapEditor\TiptapEditor;

class NewsResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = News::class;
    protected static ?string $navigationLabel = 'Berita';
    protected static ?string $navigationIcon = 'heroicon-o-newspaper'; // ⬅️ pakai ikon koran

    public static function form(Form $form): Form
    {
        return $form
            ->schema([ 
                Tabs::make('Bahasa')
                    ->tabs([ 
                        Tabs\Tab::make('Indonesia')->schema([ 
                            TextInput::make('title.id')->label('Judul (ID)')->required(),
                            TextInput::make('slug.id')->label('Slug (ID)')->required(),
                            TiptapEditor::make('content.id')
                                ->label('Konten (ID)')
                                ->profile('default')
                                ->columnSpanFull()
                                ->required(),
                        ]),
                        Tabs\Tab::make('English')->schema([ 
                            TextInput::make('title.en')->label('Title (EN)')->required(),
                            TextInput::make('slug.en')->label('Slug (EN)')->required(),
                            TiptapEditor::make('content.en')
                                ->label('Content (EN)')
                                ->profile('default')
                                ->columnSpanFull()
                                ->required(),
                        ]),
                        Tabs\Tab::make('Arabic')->schema([ 
                            TextInput::make('title.ar')->label('العنوان')->required()
                                ->extraAttributes([ 'dir' => 'rtl', 'style' => 'text-align: right' ]),
                            TextInput::make('slug.ar')->label('الرابط')->required()
                                ->extraAttributes([ 'dir' => 'rtl', 'style' => 'text-align: right' ]),
                            TiptapEditor::make('content.ar')
                                ->label('المحتوى')
                                ->profile('default')
                                ->extraAttributes([ 'dir' => 'rtl', 'style' => 'text-align: right' ])
                                ->columnSpanFull()
                                ->required(),
                        ]),
                    ])->columnSpanFull(),

                FileUpload::make('thumbnail')
                    ->image()
                    ->directory('news/thumbnail')
                    ->label('Thumbnail'),

                Toggle::make('is_published')
                    ->label('Tampilkan?')
                    ->default(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([ 
                TextColumn::make('title.id')->label('Judul (ID)')->searchable()->limit(30),
                TextColumn::make('is_published')->label('Publikasi'),
                // ->boolean(),
                TextColumn::make('created_at')->label('Dibuat')->date(),
            ])
            ->filters([])
            ->actions([ 
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [ 
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}