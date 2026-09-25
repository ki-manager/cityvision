<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Dynamische robots.txt (Domain aus Konfiguration).
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class RobotsController
{
    public function show(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $base = App::url();
        echo "User-agent: *\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /install\n";
        echo "Disallow: /storage\n";
        echo "Disallow: /config\n";
        echo "\nSitemap: " . $base . "/sitemap.xml\n";
        exit;
    }
}
