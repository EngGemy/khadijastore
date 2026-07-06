<?php

namespace App\Console\Commands;

use App\Services\PublicStoragePublisher;
use Illuminate\Console\Command;

class PublishPublicStorageCommand extends Command
{
    protected $signature = 'storage:publish-public';

    protected $description = 'Mirror storage/app/public to public/storage (LiteSpeed-safe)';

    public function handle(): int
    {
        $count = PublicStoragePublisher::publishAll();

        $this->info("Published {$count} file(s) to public/storage.");

        return self::SUCCESS;
    }
}
