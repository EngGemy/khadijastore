<?php

namespace App\Console\Commands;

use App\Services\WebPushService;
use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';

    protected $description = 'Generate VAPID keys for Web Push and print .env lines';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\Throwable $e) {
            $this->error('OpenSSL could not create EC keys on this machine.');
            $this->line('Fallback: npx web-push generate-vapid-keys');
            $this->newLine();
            $this->comment($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Add these to your .env file:');
        $this->newLine();
        $this->line('VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->line('VAPID_SUBJECT='.(config('app.url') ?: 'mailto:admin@example.com'));
        $this->line('WEBPUSH_ENABLED=true');
        $this->newLine();
        $this->comment('Then run: php artisan config:clear');

        return self::SUCCESS;
    }
}
