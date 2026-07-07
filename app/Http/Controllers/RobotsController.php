<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /platform\nDisallow: /platform/*\nDisallow: /merchant\nDisallow: /merchant/*\nDisallow: /panel-api\nDisallow: /panel-api/*\n\nSitemap: ".url('/sitemap.xml');

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
