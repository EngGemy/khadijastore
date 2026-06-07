<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$compiler = $app->make('Illuminate\View\Compilers\BladeCompiler');

$files = [
    'resources/views/shop/index.blade.php',
    'resources/views/shop/product.blade.php',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    try {
        $compiled = $compiler->compileString($content);
        echo basename($file) . " compiled OK\n";
    } catch (\Throwable $e) {
        echo basename($file) . " ERROR: " . $e->getMessage() . "\n";
    }
}
