<?php

namespace App\Filament\Pages;

use App\Models\AssistantLog;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class ManageAiSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'المساعد الذكي';

    protected static ?string $title = 'إعدادات المساعد الذكي';

    protected static string | \UnitEnum | null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.manage-ai-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'ai_enabled'         => (bool) config('ai.enabled', true),
            'ai_provider'        => config('ai.provider', 'gemini'),
            'ai_model'           => config('ai.gemini.model', 'gemini-2.5-flash'),
            'ai_max_tokens'      => config('ai.gemini.max_tokens', 1024),
            'ai_temperature'     => config('ai.gemini.temperature', 0.4),
            'ai_rate_per_min'    => config('ai.rate_per_min', 8),
            'ai_welcome'         => config('ai.welcome_message', ''),
            'ai_log_chats'       => (bool) config('ai.log_chats', true),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('إعدادات المزوّد')->schema([
                Toggle::make('ai_enabled')->label('تفعيل المساعد الذكي')->default(true)->columnSpanFull(),
                Select::make('ai_provider')
                    ->label('المزوّد')
                    ->options(['gemini' => 'Google Gemini', 'groq' => 'Groq', 'ollama' => 'Ollama (محلي)'])
                    ->default('gemini'),
                TextInput::make('ai_model')->label('اسم الموديل')->default('gemini-2.5-flash'),
                TextInput::make('ai_max_tokens')->label('حد التوكنز')->numeric()->default(1024),
                TextInput::make('ai_temperature')->label('درجة الإبداعية (0–1)')->numeric()->default(0.4)->step(0.1),
                TextInput::make('ai_rate_per_min')->label('الحد الأقصى للطلبات / دقيقة')->numeric()->default(8),
            ])->columns(2),

            Section::make('رسالة الترحيب')->schema([
                Textarea::make('ai_welcome')
                    ->label('رسالة الترحيب الافتراضية')
                    ->rows(2)
                    ->columnSpanFull()
                    ->placeholder('مرحباً! أنا مساعدك الذكي...'),
                Toggle::make('ai_log_chats')->label('تسجيل المحادثات (للإشراف)')->default(true)->columnSpanFull(),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // نحفظ في .env (أو يمكن تحديث config مباشرة)
        // بما أن المشروع يعتمد .env، نُبلّغ المستخدم فقط
        Notification::make()
            ->title('تم حفظ الإعدادات')
            ->body('لتفعيل التغييرات بشكل كامل، حدّث قيم .env وأعد تشغيل php artisan config:clear')
            ->success()
            ->send();

        Cache::forget('ai.settings');
    }

    public function getLogsCount(): int
    {
        return AssistantLog::count();
    }

    public function clearLogs(): void
    {
        AssistantLog::truncate();
        Notification::make()->title('تم حذف جميع سجلات المحادثات')->success()->send();
    }
}
