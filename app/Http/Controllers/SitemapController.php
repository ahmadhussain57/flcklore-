<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $contents = Content::published()->latest('published_at')->get();
        $products = Product::published()->latest('published_at')->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $staticPages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('articles.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('shop.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('pages.about'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('pages.contact'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => route('pages.privacy'), 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => route('pages.terms'), 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($page['url']) . "</loc>\n";
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        foreach ($contents as $content) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(route('articles.show', $content->slug)) . "</loc>\n";
            $xml .= "    <lastmod>" . $content->updated_at->toAtomString() . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        foreach ($products as $product) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars(route('shop.show', $product->slug)) . "</loc>\n";
            $xml .= "    <lastmod>" . $product->updated_at->toAtomString() . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}