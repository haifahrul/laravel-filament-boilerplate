<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Traits\HasResourcePermissions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->can('roles.view');
    // }

    public static function form(Form $form): Form
    {
        $permissionsGrouped = Permission::all()
            ->groupBy(fn ($perm) => Str::of($perm->name)->before('.')->ucfirst());

        $permissionSections = [];

        foreach ($permissionsGrouped as $group => $permissions) {
            $permissionSections[] = CheckboxList::make('permissions')
                ->label($group)
                ->relationship('permissions', 'name') // Ini penting!
                ->options(
                    $permissions->mapWithKeys(fn ($permission) => [
                        $permission->id => Str::of($permission->name)->after('.')->replace('_', ' ')->ucfirst(),
                    ])->toArray()
                )
                ->bulkToggleable();
        }

        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('guard_name')
                    ->default('web')
                    ->required(),

                Section::make('Permissions')
                    ->schema($permissionSections)
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('guard_name')->sortable(),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions'),
                TextColumn::make('created_at')->dateTime()->label('Created'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}