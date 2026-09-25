<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Öffentliche Seiten der Website.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class PageController
{
    private function pageData(string $slug): array
    {
        $row = null;
        if (Database::tryConn() !== null) {
            try {
                $row = Database::fetch(
                    'SELECT * FROM pages WHERE slug = ? AND locale = ?',
                    [$slug, App::locale() === 'de' ? 'de' : App::locale()]
                );
            } catch (Throwable $e) {
            }
        }
        return $row ?? [];
    }

    public function home(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/home', [
            'pageTitle' => 'Kinderarztpraxis Königslutter am Elm – Kinder- und Jugendmedizin',
            'metaDescription' => 'Kinderarztpraxis Königslutter am Elm: Kinder- und Jugendmedizin mit Erfahrung, Zeit und Herz. Online Termine buchen, Öffnungszeiten, Team und Leistungen.',
            'doctors' => Database::tryConn() ? TeamMember::doctors() : [],
            'services' => Database::tryConn() ? Service::homepage() : [],
            'news' => Database::tryConn() ? News::active(6) : [],
            'important' => Database::tryConn() ? News::important() : [],
        ])->renderWithLayout();
    }

    public function praxis(): void
    {
        $this->withLocaleFromPath();
        $page = $this->pageData('praxis');
        View::make('pages/praxis', [
            'pageTitle' => 'Unsere Praxis – ' . Setting::get('practice_name'),
            'metaDescription' => 'Kinderarztpraxis Königslutter am Elm – Praxis, Philosophie, Schwerpunkte Kinder- und Jugendmedizin und Neonatologie, gesprochene Sprachen.',
            'page' => $page,
        ])->renderWithLayout();
    }

    public function team(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/team', [
            'pageTitle' => 'Unser Team – ' . Setting::get('practice_name'),
            'metaDescription' => 'Lernen Sie das Team der Kinderarztpraxis Königslutter am Elm kennen: Ärzte, Medizinische Fachangestellte und Assistenz.',
            'doctors' => Database::tryConn() ? TeamMember::doctors() : [],
            'staff' => Database::tryConn() ? TeamMember::staff() : [],
        ])->renderWithLayout();
    }

    public function teamDetail(array $params): void
    {
        $this->withLocaleFromPath();
        $member = Database::tryConn() ? TeamMember::findBySlug((string) $params['slug']) : null;
        if ($member === null) {
            Response::notFound();
        }
        View::make('pages/team-detail', [
            'pageTitle' => $member['name'] . ' – ' . Setting::get('practice_name'),
            'metaDescription' => $member['name'] . ', ' . $member['role'] . ' – Kinderarztpraxis Königslutter am Elm.',
            'member' => $member,
        ])->renderWithLayout();
    }

    public function leistungen(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/leistungen', [
            'pageTitle' => 'Unsere Leistungen – ' . Setting::get('practice_name'),
            'metaDescription' => 'Leistungen der Kinderarztpraxis Königslutter: Vorsorge, Ultraschall, Allergiediagnostik, Impfungen, Asthma- und Neurodermitis-Therapie, Beratung.',
            'groups' => Database::tryConn() ? Service::byGroup() : [],
        ])->renderWithLayout();
    }

    public function vorsorge(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/vorsorge', [
            'pageTitle' => 'Vorsorge – ' . Setting::get('practice_name'),
            'metaDescription' => 'Vorsorgeuntersuchungen U2 bis J2, Jugendarbeitsschutzuntersuchungen, Sporttauglichkeit und reisemedizinische Beratung in Königslutter am Elm.',
            'services' => Database::tryConn() ? Service::byGroup()['vorsorge'] ?? [] : [],
        ])->renderWithLayout();
    }

    public function impfungen(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/impfungen', [
            'pageTitle' => 'Impfungen – ' . Setting::get('practice_name'),
            'metaDescription' => 'Impfungen gemäß STIKO und Reiseimpfungen in der Kinderarztpraxis Königslutter am Elm. Individuelle Impfberatung erfolgt in der Praxis.',
            'services' => Database::tryConn() ? Service::byGroup()['impfungen'] ?? [] : [],
        ])->renderWithLayout();
    }

    public function informationen(): void
    {
        $this->withLocaleFromPath();
        $page = $this->pageData('informationen');
        View::make('pages/informationen', [
            'pageTitle' => 'Informationen für Eltern – ' . Setting::get('practice_name'),
            'metaDescription' => 'Terminvorbereitung, Unterlagen, Besuchsregelungen, Notfälle und Notdienste – alle Informationen für Eltern in der Kinderarztpraxis Königslutter.',
            'page' => $page,
        ])->renderWithLayout();
    }

    public function aktuelles(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/aktuelles', [
            'pageTitle' => 'Aktuelles – ' . Setting::get('practice_name'),
            'metaDescription' => 'Aktuelle Informationen, Urlaub, Vertretungen und besondere Hinweise der Kinderarztpraxis Königslutter am Elm.',
            'news' => Database::tryConn() ? News::active(30) : [],
        ])->renderWithLayout();
    }

    public function kontakt(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/kontakt', [
            'pageTitle' => 'Kontakt – ' . Setting::get('practice_name'),
            'metaDescription' => 'Kontakt zur Kinderarztpraxis Königslutter am Elm: Westernstraße 31, 38154 Königslutter, Tel. 05353 5511. Termin online buchen.',
        ])->renderWithLayout();
    }

    public function impressum(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/impressum', [
            'pageTitle' => 'Impressum – ' . Setting::get('practice_name'),
            'metaDescription' => 'Impressum der Kinderarztpraxis Königslutter am Elm.',
        ])->renderWithLayout();
    }

    public function datenschutz(): void
    {
        $this->withLocaleFromPath();
        View::make('pages/datenschutz', [
            'pageTitle' => 'Datenschutz – ' . Setting::get('practice_name'),
            'metaDescription' => 'Datenschutzerklärung der Kinderarztpraxis Königslutter am Elm.',
        ])->renderWithLayout();
    }

    /**
     * Setzt das Locale anhand des URL-Pfades (/en/..., /ar/...).
     * Wird vor dem Rendern aufgerufen, damit App::t() korrekt arbeitet.
     */
    private function withLocaleFromPath(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = Url::base();
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        if (preg_match('#^/(en|ar)(?:/|$)#u', $uri, $m)) {
            App::setLocale($m[1]);
        }
    }
}
