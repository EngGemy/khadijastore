<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Brand;
use App\Models\FacebookPixelSetting;
use App\Services\FacebookPixelService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class ManageFacebookPixelSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'فيسبوك بكسل';

    protected static ?string $title = 'فيسبوك بكسل و CAPI';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.manage-facebook-pixel';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasRole('brand_admin'));
    }

    public function mount(): void
    {
        $brandId = auth()->user()?->isSuperAdmin()
            ? (int) (Brand::query()->orderBy('name')->value('id') ?? 0)
            : (int) auth()->user()->brand_id;

        $settings = $brandId
            ? FacebookPixelSetting::query()->where('brand_id', $brandId)->first()
            : null;

        $this->form->fill([
            'brand_id' => $brandId,
            'pixel_id' => $settings?->pixel_id ?? '',
            'access_token' => $settings?->access_token ? '********' : '',
            'test_event_code' => $settings?->test_event_code ?? '',
            'is_enabled' => $settings?->is_enabled ?? false,
            'capi_enabled' => $settings?->capi_enabled ?? true,
            'track_pageview' => $settings?->track_pageview ?? true,
            'track_viewcontent' => $settings?->track_viewcontent ?? true,
            'track_addtocart' => $settings?->track_addtocart ?? true,
            'track_initiatecheckout' => $settings?->track_initiatecheckout ?? true,
            'track_purchase' => $settings?->track_purchase ?? true,
            'track_lead' => $settings?->track_lead ?? true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $components = [];

        if (auth()->user()?->isSuperAdmin()) {
            $components[] = Select::make('brand_id')
                ->label('البراند')
                ->options(Brand::query()->orderBy('name')->pluck('name', 'id'))
                ->required()
                ->live()
                ->afterStateUpdated(fn () => $this->mount());
        }

        $components = array_merge($components, [
            Section::make('بيانات الاعتماد')->schema([
                TextInput::make('pixel_id')
                    ->label('Pixel ID')
                    ->required()
                    ->regex('/^\d{10,20}$/')
                    ->helperText('من Events Manager → مصدر البيانات → البكسل'),
                TextInput::make('access_token')
                    ->label('Access Token (CAPI)')
                    ->password()
                    ->revealable()
                    ->required()
                    ->helperText('توكن بصلاحية ads_management أو pixel — يُخزَّن مشفّرًا'),
                TextInput::make('test_event_code')
                    ->label('Test Event Code')
                    ->nullable()
                    ->helperText('اختياري — من Test Events في Events Manager'),
                Toggle::make('is_enabled')->label('تفعيل البكسل')->default(false),
                Toggle::make('capi_enabled')->label('تفعيل Conversions API (CAPI)')->default(true),
            ])->columns(2),

            Section::make('الأحداث المتتبّعة')->schema([
                Toggle::make('track_pageview')->label('PageView')->default(true),
                Toggle::make('track_viewcontent')->label('ViewContent')->default(true),
                Toggle::make('track_addtocart')->label('AddToCart')->default(true),
                Toggle::make('track_initiatecheckout')->label('InitiateCheckout')->default(true),
                Toggle::make('track_purchase')->label('Purchase')->default(true),
                Toggle::make('track_lead')->label('Lead')->default(true),
            ])->columns(3),
        ]);

        return $schema->components($components)->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $brandId = (int) ($data['brand_id'] ?? auth()->user()->brand_id);
        $pixelService = app(FacebookPixelService::class);

        if ($data['access_token'] !== '********') {
            if (! $pixelService->validateAccessToken($data['pixel_id'], $data['access_token'])) {
                throw ValidationException::withMessages([
                    'data.access_token' => 'رمز الوصول غير صالح أو لا يملك صلاحية الوصول إلى هذا البكسل.',
                ]);
            }
        } elseif (! FacebookPixelSetting::query()->where('brand_id', $brandId)->exists()) {
            throw ValidationException::withMessages([
                'data.access_token' => 'رمز الوصول مطلوب عند الإعداد لأول مرة.',
            ]);
        }

        $pixelService->saveSettings($brandId, $data);

        Notification::make()
            ->title('تم الحفظ')
            ->body('تم تحديث إعدادات فيسبوك بكسل و CAPI.')
            ->success()
            ->send();

        $this->mount();
    }

    public function testConnection(): void
    {
        $data = $this->form->getState();
        $token = $data['access_token'] ?? '';
        $brandId = (int) ($data['brand_id'] ?? auth()->user()->brand_id);

        if ($token === '********') {
            $existing = FacebookPixelSetting::query()->where('brand_id', $brandId)->first();
            $token = $existing?->access_token;
        }

        if (! $token) {
            Notification::make()->title('أدخل رمز الوصول أولًا')->warning()->send();

            return;
        }

        $valid = app(FacebookPixelService::class)->validateAccessToken($data['pixel_id'], $token);

        Notification::make()
            ->title($valid ? 'الاتصال ناجح' : 'فشل الاتصال')
            ->body($valid ? 'يمكن للتوكن الوصول إلى البكسل.' : 'تحقق من Pixel ID والصلاحيات.')
            ->{$valid ? 'success' : 'danger'}()
            ->send();
    }

    private function resolveBrandId(): int
    {
        if (auth()->user()?->isSuperAdmin() && ! empty($this->data['brand_id'])) {
            return (int) $this->data['brand_id'];
        }

        return (int) auth()->user()->brand_id;
    }
}
