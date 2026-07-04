<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Global enable switch
    |--------------------------------------------------------------------------
    |
    | Master toggle. Per-brand settings still require is_enabled on the row.
    |
    */
    'enabled' => env('FACEBOOK_PIXEL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Meta Graph API
    |--------------------------------------------------------------------------
    */
    'graph_api_version' => env('FACEBOOK_GRAPH_API_VERSION', 'v21.0'),

    'timeout' => (int) env('FACEBOOK_PIXEL_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Consent (GDPR)
    |--------------------------------------------------------------------------
    |
    | When require_consent is true, events fire only if the consent cookie exists
    | and equals consent_cookie_value.
    |
    */
    'require_consent' => env('FACEBOOK_PIXEL_REQUIRE_CONSENT', false),

    'consent_cookie_name' => env('FACEBOOK_PIXEL_CONSENT_COOKIE', 'marketing_consent'),

    'consent_cookie_value' => env('FACEBOOK_PIXEL_CONSENT_VALUE', '1'),

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    */
    'queue' => env('FACEBOOK_PIXEL_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Event name → settings column map
    |--------------------------------------------------------------------------
    */
    'event_toggles' => [
        'PageView' => 'track_pageview',
        'ViewContent' => 'track_viewcontent',
        'AddToCart' => 'track_addtocart',
        'InitiateCheckout' => 'track_initiatecheckout',
        'Purchase' => 'track_purchase',
        'Lead' => 'track_lead',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default currency when not provided in event payload
    |--------------------------------------------------------------------------
    */
    'default_currency' => env('FACEBOOK_PIXEL_DEFAULT_CURRENCY', 'EGP'),

];
