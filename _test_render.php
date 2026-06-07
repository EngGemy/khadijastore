<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
try {
    $response = $kernel->handle($request);
    echo 'Status: '.$response->getStatusCode().PHP_EOL;
    if ($response->getStatusCode() === 200) {
        echo 'OK - Length: '.strlen($response->getContent()).PHP_EOL;
    } else {
        // Get last log entry
        $logFile = __DIR__.'/storage/logs/laravel.log';
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $last = array_slice($lines, -5);
            echo implode('', $last).PHP_EOL;
        }
    }
} catch (Throwable $e) {
    echo get_class($e).': '.$e->getMessage().PHP_EOL;
    echo 'At: '.$e->getFile().':'.$e->getLine().PHP_EOL;
}
