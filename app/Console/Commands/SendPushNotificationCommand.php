<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Services\WebPushService;
use Illuminate\Console\Command;

class SendPushNotificationCommand extends Command
{
    protected $signature = 'notifications:send-push
                            {--title= : Notification title}
                            {--body= : Notification body}
                            {--url=/ : Click URL}
                            {--brand= : Brand id or slug filter}
                            {--phone= : Send only to this customer phone}';

    protected $description = 'Send a Web Push notification (broadcast or phone-targeted)';

    public function handle(WebPushService $webPush): int
    {
        if (! $webPush->isConfigured()) {
            $this->error('VAPID keys missing. Run: php artisan webpush:vapid');

            return self::FAILURE;
        }

        $title = $this->option('title') ?: $this->ask('Title', 'عرض جديد من سند');
        $body = $this->option('body') ?: $this->ask('Body', 'تصفّح أحدث العروض الآن');
        $url = $this->option('url') ?: '/';

        $brandId = null;
        if ($brandOpt = $this->option('brand')) {
            $brand = is_numeric($brandOpt)
                ? Brand::query()->find($brandOpt)
                : Brand::query()->where('slug', $brandOpt)->first();
            if (! $brand) {
                $this->error('Brand not found: '.$brandOpt);

                return self::FAILURE;
            }
            $brandId = $brand->id;
        }

        $payload = [
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'tag' => 'alamat-broadcast-'.now()->timestamp,
        ];

        if ($phone = $this->option('phone')) {
            $result = $webPush->sendToPhone($phone, $payload, $brandId);
        } else {
            $result = $webPush->broadcast($payload, $brandId);
        }

        $this->info("Sent: {$result['sent']} · Failed: {$result['failed']} · Expired removed: {$result['expired']}");

        return self::SUCCESS;
    }
}
