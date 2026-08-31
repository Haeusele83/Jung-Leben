<?php
/**
 * Coming-Soon-Modus für Jung Leben.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Coming-Soon-Steuerung.
 */
final class Jung_Leben_Core_Coming_Soon
{
    /**
     * Geplanter Go-Live.
     *
     * Die Zeit wird in der in WordPress eingestellten
     * Zeitzone interpretiert.
     */
    private const LAUNCH_DATE = '2026-09-01 20:00:00';

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'template_redirect',
            [
                self::class,
                'redirect_requests',
            ],
            1
        );

        add_filter(
            'template_include',
            [
                self::class,
                'use_coming_soon_template',
            ],
            99
        );

        add_filter(
            'wp_robots',
            [
                self::class,
                'set_robots',
            ]
        );

        add_action(
            'wp_enqueue_scripts',
            [
                self::class,
                'enqueue_assets',
            ],
            100
        );
    }

    /**
     * Go-Live-Zeitpunkt zurückgeben.
     */
    public static function get_launch_date(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            self::LAUNCH_DATE,
            wp_timezone()
        );
    }

    /**
     * Prüfen, ob der Go-Live bereits erreicht wurde.
     */
    public static function has_launched(): bool
    {
        $now = new DateTimeImmutable(
            'now',
            wp_timezone()
        );

        return $now >= self::get_launch_date();
    }

    /**
     * Prüfen, ob Coming Soon aktiv ist.
     */
    public static function is_active(): bool
    {
        /*
         * Nach dem Go-Live wird die normale
         * Website automatisch freigegeben.
         */
        if (self::has_launched()) {
            return false;
        }

        /*
         * WordPress-Backend nicht beeinflussen.
         */
        if (is_admin()) {
            return false;
        }

        /*
         * AJAX-Aufrufe nicht blockieren.
         */
        if (
            function_exists('wp_doing_ajax')
            && wp_doing_ajax()
        ) {
            return false;
        }

        /*
         * Eingeloggte Personen mit Bearbeitungsrechten
         * dürfen die normale Website weiterhin sehen.
         */
        if (
            is_user_logged_in()
            && current_user_can('edit_pages')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Öffentliche Unterseiten während Coming Soon
     * auf die Startseite umleiten.
     */
    public static function redirect_requests(): void
    {
        if (! self::is_active()) {
            return;
        }

        if (! is_front_page()) {
            wp_safe_redirect(
                home_url('/'),
                302
            );

            exit;
        }

        nocache_headers();
    }

    /**
     * Coming-Soon-Template verwenden.
     */
    public static function use_coming_soon_template(
        string $template
    ): string {
        if (! self::is_active()) {
            return $template;
        }

        $coming_soon_template =
            get_template_directory()
            . '/coming-soon.php';

        if (! file_exists($coming_soon_template)) {
            return $template;
        }

        return $coming_soon_template;
    }

    /**
     * Suchmaschinen während Coming Soon blockieren.
     */
    public static function set_robots(
        array $robots
    ): array {
        if (! self::is_active()) {
            return $robots;
        }

        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        $robots['noarchive'] = true;
        $robots['nosnippet'] = true;

        return $robots;
    }

    /**
     * Coming-Soon-Assets laden.
     */
    public static function enqueue_assets(): void
    {
        if (! self::is_active()) {
            return;
        }

        wp_dequeue_style(
            'jung-leben-style'
        );

        wp_dequeue_style(
            'jung-leben-site'
        );

        wp_dequeue_script(
            'jung-leben-site'
        );

        $css_path =
            get_template_directory()
            . '/assets/css/coming-soon.css';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'jung-leben-coming-soon',
                get_template_directory_uri()
                    . '/assets/css/coming-soon.css',
                [],
                (string) filemtime($css_path)
            );
        }

        $js_path =
            get_template_directory()
            . '/assets/js/coming-soon.js';

        if (file_exists($js_path)) {
            wp_enqueue_script(
                'jung-leben-coming-soon',
                get_template_directory_uri()
                    . '/assets/js/coming-soon.js',
                [],
                (string) filemtime($js_path),
                true
            );
        }
    }
}