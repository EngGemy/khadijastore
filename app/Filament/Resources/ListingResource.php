<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages\CreateListing;
use App\Filament\Resources\ListingResource\Pages\EditListing;
use App\Filament\Resources\ListingResource\Pages\ListListings;
use App\Models\Listing;
use App\Models\ServiceCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'الأطباء والحضانات';

    protected static ?string $modelLabel = 'إدراج';

    protected static ?string $pluralModelLabel = 'الأطباء والحضانات';

    protected static string|\UnitEnum|null $navigationGroup = 'الدليل';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('الأساسيات')->schema([
                Select::make('type')
                    ->label('النوع')
                    ->options(Listing::types())
                    ->required()
                    ->live(),
                Select::make('service_category_id')
                    ->label('القسم')
                    ->options(function (\Filament\Forms\Get $get) {
                        $type = $get('type');
                        if (! $type) {
                            return [];
                        }

                        return ServiceCategory::withoutGlobalScopes()
                            ->where('type', $type)
                            ->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('الاسم بالعربية')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('name_en')
                    ->label('Name (English)')
                    ->nullable()
                    ->columnSpanFull(),
                Textarea::make('summary')
                    ->label('ملخّص بالعربية')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('summary_en')
                    ->label('Summary (English)')
                    ->rows(2)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label('الوصف الكامل')
                    ->columnSpanFull(),
                RichEditor::make('description_en')
                    ->label('Full Description (English)')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('التواصل')->schema([
                TextInput::make('phone')
                    ->label('الهاتف')
                    ->tel()
                    ->nullable(),
                TextInput::make('whatsapp')
                    ->label('واتساب')
                    ->tel()
                    ->nullable(),
                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->nullable(),
                TextInput::make('governorate')
                    ->label('المحافظة')
                    ->nullable(),
                TextInput::make('address')
                    ->label('العنوان بالعربية')
                    ->nullable()
                    ->columnSpanFull(),
                TextInput::make('address_en')
                    ->label('Address (English)')
                    ->nullable()
                    ->columnSpanFull(),
                TextInput::make('map_url')
                    ->label('رابط الخريطة')
                    ->url()
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('الصور')->schema([
                SpatieMediaLibraryFileUpload::make('cover')
                    ->label('الصورة الرئيسية')
                    ->collection('cover')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->panelLayout('integrated')
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1'),
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->label('معرض الصور')
                    ->collection('gallery')
                    ->multiple()
                    ->reorderable()
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->panelLayout('grid')
                    ->imageEditor()
                    ->imageResizeMode('cover'),
            ])->columns(2),

            // ── تفاصيل الطبيب ─────────────────────────────────────────────────
            Section::make('تفاصيل الطبيب')
                ->schema([
                    Select::make('data.title')
                        ->label('اللقب')
                        ->options([
                            'استشاري'  => 'استشاري',
                            'أخصائي'   => 'أخصائي',
                            'طبيب عام' => 'طبيب عام',
                        ])
                        ->nullable(),
                    TextInput::make('data.specialty')
                        ->label('التخصص بالعربية')
                        ->nullable(),
                    TextInput::make('data.specialty_en')
                        ->label('Specialty (English)')
                        ->nullable(),
                    TextInput::make('data.clinic_name')
                        ->label('اسم العيادة')
                        ->nullable(),
                    TextInput::make('data.experience_years')
                        ->label('سنوات الخبرة')
                        ->numeric()
                        ->suffix('سنة')
                        ->nullable(),
                    TextInput::make('data.consultation_fee')
                        ->label('سعر الكشف (عرض فقط — لا حجز)')
                        ->numeric()
                        ->suffix('ج.م')
                        ->helperText('للعرض التوضيحي فقط، لا يتضمن أي آلية حجز أو دفع')
                        ->nullable(),
                    TextInput::make('data.working_hours')
                        ->label('مواعيد العمل')
                        ->nullable()
                        ->columnSpanFull(),
                    TagsInput::make('data.services')
                        ->label('الخدمات المقدّمة')
                        ->separator(',')
                        ->nullable()
                        ->columnSpanFull(),
                    TagsInput::make('data.languages')
                        ->label('اللغات')
                        ->separator(',')
                        ->nullable(),
                ])
                ->columns(2)
                ->visible(fn (\Filament\Forms\Get $get) => $get('type') === Listing::TYPE_DOCTOR),

            // ── تفاصيل الحضانة ────────────────────────────────────────────────
            Section::make('تفاصيل الحضانة')
                ->schema([
                    TextInput::make('data.age_from_months')
                        ->label('العمر من (بالأشهر)')
                        ->numeric()
                        ->suffix('شهر')
                        ->nullable(),
                    TextInput::make('data.age_to_months')
                        ->label('العمر حتى (بالأشهر)')
                        ->numeric()
                        ->suffix('شهر')
                        ->nullable(),
                    TextInput::make('data.capacity')
                        ->label('الطاقة الاستيعابية')
                        ->numeric()
                        ->suffix('طفل')
                        ->nullable(),
                    TextInput::make('data.working_days')
                        ->label('أيام العمل')
                        ->nullable(),
                    TextInput::make('data.working_hours')
                        ->label('مواعيد العمل')
                        ->nullable(),
                    TextInput::make('data.monthly_fee_from')
                        ->label('الرسوم من')
                        ->numeric()
                        ->suffix('ج.م')
                        ->nullable(),
                    TextInput::make('data.monthly_fee_to')
                        ->label('الرسوم حتى')
                        ->numeric()
                        ->suffix('ج.م')
                        ->nullable(),
                    TagsInput::make('data.programs')
                        ->label('البرامج (عربي)')
                        ->separator(',')
                        ->nullable()
                        ->columnSpanFull(),
                    TagsInput::make('data.programs_en')
                        ->label('Programs (English)')
                        ->separator(',')
                        ->nullable()
                        ->columnSpanFull(),
                    TagsInput::make('data.facilities')
                        ->label('المرافق والخدمات')
                        ->separator(',')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn (\Filament\Forms\Get $get) => $get('type') === Listing::TYPE_NURSERY),

            // ── الحالة والـ SEO ───────────────────────────────────────────────
            Section::make('الحالة والـ SEO')->schema([
                Toggle::make('is_active')
                    ->label('نشط')
                    ->default(true),
                Toggle::make('is_featured')
                    ->label('مميّز')
                    ->default(false),
                TextInput::make('sort')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->nullable()
                    ->columnSpanFull(),
                Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->rows(2)
                    ->nullable()
                    ->columnSpanFull(),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('الصورة')
                    ->collection('cover')
                    ->conversion('thumb')
                    ->circular(),
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Listing::types()[$state] ?? $state),
                TextColumn::make('serviceCategory.name')
                    ->label('القسم')
                    ->placeholder('—'),
                TextColumn::make('governorate')
                    ->label('المحافظة')
                    ->placeholder('—'),
                TextColumn::make('rating')
                    ->label('التقييم')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . '★' : '—'),
                TextColumn::make('views')
                    ->label('المشاهدات')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('مميّز')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('النوع')
                    ->options(Listing::types()),
                SelectFilter::make('service_category_id')
                    ->label('القسم')
                    ->relationship('serviceCategory', 'name'),
                TernaryFilter::make('is_active')
                    ->label('الحالة'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('sort');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListListings::route('/'),
            'create' => CreateListing::route('/create'),
            'edit'   => EditListing::route('/{record}/edit'),
        ];
    }
}
