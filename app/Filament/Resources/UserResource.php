<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'المستخدمون';

    protected static ?string $modelLabel = 'مستخدم';

    protected static ?string $pluralModelLabel = 'المستخدمون';

    protected static string | \UnitEnum | null $navigationGroup = 'الإدارة';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        $isSuper = auth()->user()->isSuperAdmin();

        return $schema->components([
            TextInput::make('name')->label('الاسم')->required(),
            TextInput::make('email')->label('البريد')
                ->email()->required()->unique(ignoreRecord: true),
            TextInput::make('password')->label('كلمة المرور')
                ->password()->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context) => $context === 'create'),

            // السوبر أدمن فقط يحدّد البراند والدور بحرية
            Select::make('brand_id')->label('البراند')
                ->relationship('brand', 'name')
                ->helperText('اتركه فارغًا لمستخدم على مستوى المنصة')
                ->visible($isSuper),

            Select::make('roles')->label('الدور')
                ->relationship('roles', 'name')
                ->multiple()->preload()
                // أدمن البراند لا يمنح دور سوبر أدمن
                ->options(fn () => $isSuper
                    ? Role::pluck('name', 'id')
                    : Role::whereIn('name', ['brand_admin', 'brand_staff'])->pluck('name', 'id')),

            Toggle::make('is_active')->label('نشط')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable()->weight('bold'),
                TextColumn::make('email')->label('البريد')->searchable(),
                TextColumn::make('brand.name')->label('البراند')->placeholder('المنصة')->badge(),
                TextColumn::make('roles.name')->label('الدور')->badge(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    // أدمن البراند يرى/يدير مستخدمي برانده فقط
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user->isSuperAdmin()) {
            $query->where('brand_id', $user->brand_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}

