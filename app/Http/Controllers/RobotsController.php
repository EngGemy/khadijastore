<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /admin/*\n\nSitemap: " . url('/sitemap.xml');

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
