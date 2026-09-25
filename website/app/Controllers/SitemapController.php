<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Dynamische sitemap.xml mit hreflang-Alternaten.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class SitemapController
{
    public function show(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $base = App::url();
        $paths = ['', 'praxis', 'team', 'leistungen', 'vorsorge', 'impfungen', 'informationen', 'aktuelles', 'kontakt', 'impressum', 'datenschutz'];

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($paths as $path) {
            $loc = $base . '/' . $path;
            echo "  <url>\n";
            echo '    <loc>' . Security::e($loc) . "</loc>\n";
            $prio = $path === '' ? '1.0' : '0.8';
            echo "    <priority>$prio</priority>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            foreach (['de', 'en', 'ar'] as $locale) {
                $href = $locale === 'de' ? $base . '/' . $path : $base . '/' . $locale . ($path === '' ? '' : '/' . $path);
                echo '    <xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . Security::e($href) . '"/>' . "\n";
            }
            echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . Security::e($base . '/' . $path) . '"/>' . "\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }
}
