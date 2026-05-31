<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // ضع نطاق الواجهة الأمامية هنا في الإنتاج بدل '*'
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
