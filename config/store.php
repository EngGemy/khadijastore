<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Featured storefront brand
    |--------------------------------------------------------------------------
    |
    | Products from this brand surface first on homepage grids, offers, and
    | the /products listing. Override via setting `store.featured_brand_slug`
    | (admin) without redeploying. Default: سند للعطارة (slug: attar).
    |
    */
    'featured_brand_slug' => env('STORE_FEATURED_BRAND_SLUG', 'attar'),

];
