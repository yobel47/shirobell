<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Services\ThemeRegistry;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/** robots.txt and sitemap.xml for the main domain. Only production is crawlable. */
class SeoController extends Controller
{
    /** Private areas that search engines should not crawl. */
    private const DISALLOW = ['/app', '/admin', '/penerima-tamu', '/webhooks', '/livewire', '/user', '/email', '/reset-password', '/forgot-password'];

    public function robots(): Response
    {
        $lines = ['User-agent: *'];

        if (app()->isProduction()) {
            foreach (self::DISALLOW as $path) {
                $lines[] = 'Disallow: '.$path;
            }
            $lines[] = '';
            $lines[] = 'Sitemap: '.route('seo.sitemap');
        } else {
            // Staging and local: keep everything out of search results.
            $lines[] = 'Disallow: /';
        }

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(ThemeRegistry $registry): Response
    {
        $themes = Theme::query()->available()->get()->filter->hasView()->values();
        $themesUpdated = optional($themes->max('updated_at'))->toAtomString() ?? now()->toAtomString();
        $legalUpdated = Carbon::parse(config('shirobell.legal_updated_at'))->toAtomString();

        $urls = [
            ['loc' => route('home'), 'lastmod' => $themesUpdated, 'priority' => '1.0'],
            ['loc' => route('themes.index'), 'lastmod' => $themesUpdated, 'priority' => '0.9'],
        ];

        $categories = $themes->map->category()->filter()->unique();
        foreach ($registry->categories() as $key => $label) {
            if ($categories->contains($key)) {
                $urls[] = ['loc' => route('themes.index', ['kategori' => $key]), 'lastmod' => $themesUpdated, 'priority' => '0.8'];
            }
        }

        foreach ($themes as $theme) {
            $urls[] = ['loc' => route('themes.demo', $theme), 'lastmod' => $theme->updated_at?->toAtomString() ?? $themesUpdated, 'priority' => '0.7'];
        }

        foreach (['legal.contact', 'legal.terms', 'legal.privacy', 'legal.refund'] as $name) {
            $urls[] = ['loc' => route($name), 'lastmod' => $legalUpdated, 'priority' => '0.3'];
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
