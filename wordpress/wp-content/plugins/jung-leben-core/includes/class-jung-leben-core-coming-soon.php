<?php
/**
 * Coming-Soon-Modus für Jung Leben.
 *
 * Zeigt normalen Besucherinnen und Besuchern
 * eine reduzierte Coming-Soon-Seite.
 *
 * Angemeldete Benutzerinnen und Benutzer mit
 * Bearbeitungsrechten können die eigentliche
 * Website weiterhin vollständig aufrufen.
 *
 * Einzelne Partner-Demoseiten können während
 * der Aufbauphase gezielt öffentlich freigegeben
 * werden.
 *
 * Der öffentliche Launch erfolgt bewusst manuell.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Coming-Soon-Funktion.
 */
final class Jung_Leben_Core_Coming_Soon
{
    /**
     * Coming-Soon-Modus.
     *
     * true  = Coming-Soon-Seite anzeigen
     * false = Website öffentlich freigeben
     */
    private const ENABLED = true;


    /**
     * Seiten, die trotz Coming-Soon-Modus
     * öffentlich erreichbar sein dürfen.
     *
     * Es werden ausschliesslich die Slugs geprüft.
     */
    private const PUBLIC_PREVIEW_PAGE_SLUGS = [
        'buecher',
        'redcare',
    ];


    /* =========================================================
       INITIALISIERUNG
       ========================================================= */

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'template_redirect',
            [
                self::class,
                'maybe_render_coming_soon',
            ],
            0
        );
    }


    /* =========================================================
       COMING-SOON-MODUS
       ========================================================= */

    /**
     * Prüfen, ob die Coming-Soon-Seite angezeigt
     * werden soll.
     */
    public static function maybe_render_coming_soon(): void
    {
        /*
         * Coming-Soon-Modus manuell deaktiviert.
         */
        if (! self::ENABLED) {
            return;
        }


        /*
         * WordPress-Backend niemals blockieren.
         */
        if (is_admin()) {
            return;
        }


        /*
         * AJAX nicht blockieren.
         */
        if (
            function_exists('wp_doing_ajax')
            && wp_doing_ajax()
        ) {
            return;
        }


        /*
         * Cron nicht blockieren.
         */
        if (
            function_exists('wp_doing_cron')
            && wp_doing_cron()
        ) {
            return;
        }


        /*
         * REST API nicht blockieren.
         */
        if (
            defined('REST_REQUEST')
            && REST_REQUEST
        ) {
            return;
        }


        /*
         * Angemeldete Personen mit Bearbeitungsrechten
         * dürfen die echte Website sehen.
         */
        if (
            is_user_logged_in()
            && current_user_can('edit_pages')
        ) {
            return;
        }


        /*
         * WordPress-Login niemals blockieren.
         */
        global $pagenow;


        if (
            isset($pagenow)
            && $pagenow === 'wp-login.php'
        ) {
            return;
        }


        /*
         * Gezielt freigegebene Partner-Demoseiten
         * dürfen öffentlich aufgerufen werden.
         */
        if (
            self::is_public_preview_page()
        ) {
            return;
        }


        self::render_page();

        exit;
    }


    /* =========================================================
       ÖFFENTLICHE DEMOSEITEN
       ========================================================= */

    /**
     * Prüfen, ob die aktuelle Seite trotz
     * Coming-Soon-Modus öffentlich sein darf.
     */
    private static function is_public_preview_page(): bool
    {
        foreach (
            self::PUBLIC_PREVIEW_PAGE_SLUGS
            as $page_slug
        ) {
            if (
                is_page(
                    $page_slug
                )
            ) {
                return true;
            }
        }


        return false;
    }


    /* =========================================================
       LOGO
       ========================================================= */

    /**
     * Logo-URL ermitteln.
     */
    private static function get_logo_url(): string
    {
        /*
         * WordPress-Customizer-Logo bevorzugen.
         */
        $custom_logo_id =
            (int)
            get_theme_mod(
                'custom_logo'
            );


        if ($custom_logo_id > 0) {
            $logo_url =
                wp_get_attachment_image_url(
                    $custom_logo_id,
                    'full'
                );


            if (
                is_string($logo_url)
                && $logo_url !== ''
            ) {
                return $logo_url;
            }
        }


        /*
         * Fallback auf das Logo im Jung-Leben-Theme.
         */
        return
            get_template_directory_uri()
            . '/assets/images/logo-jung-leben.png';
    }


    /* =========================================================
       SEITENAUSGABE
       ========================================================= */

    /**
     * Coming-Soon-Seite ausgeben.
     */
    private static function render_page(): void
    {
        $logo_url =
            self::get_logo_url();


        status_header(200);

        nocache_headers();
        ?>

        <!DOCTYPE html>

        <html <?php language_attributes(); ?>>

        <head>

            <meta charset="<?php bloginfo('charset'); ?>">

            <meta
                name="viewport"
                content="width=device-width, initial-scale=1"
            >

            <meta
                name="robots"
                content="noindex, nofollow"
            >

            <title>
                <?php
                echo esc_html(
                    get_bloginfo('name')
                );
                ?>
                – Bald geht es los
            </title>

            <?php wp_head(); ?>


            <style>

                /* =============================================
                   BASIS
                   ============================================= */

                html,
                body {
                    margin: 0;
                    padding: 0;

                    min-height: 100%;
                }


                body {
                    min-height: 100vh;

                    color: #173c32;
                    background: #f5f7f2;

                    font-family:
                        -apple-system,
                        BlinkMacSystemFont,
                        "Segoe UI",
                        sans-serif;

                    -webkit-font-smoothing:
                        antialiased;

                    text-rendering:
                        optimizeLegibility;
                }


                *,
                *::before,
                *::after {
                    box-sizing: border-box;
                }


                /* =============================================
                   HEADER
                   ============================================= */

                .jl-coming-header {
                    width: 100%;

                    padding:
                        18px
                        28px;

                    background: #ffffff;

                    border-bottom:
                        1px solid
                        rgba(
                            23,
                            60,
                            50,
                            0.07
                        );
                }


                .jl-coming-header__inner {
                    width:
                        min(
                            100%,
                            1180px
                        );

                    margin:
                        0
                        auto;

                    display: flex;

                    align-items: center;
                    justify-content: center;
                }


                .jl-coming-logo {
                    width: auto;
                    height: 58px;

                    max-width: 210px;

                    display: block;

                    object-fit: contain;
                }


                /* =============================================
                   HAUPTBEREICH
                   ============================================= */

                .jl-coming-main {
                    position: relative;

                    min-height:
                        calc(
                            100vh - 95px
                        );

                    display: flex;

                    align-items: center;
                    justify-content: center;

                    overflow: hidden;

                    padding:
                        80px
                        24px;
                }


                .jl-coming-main::before {
                    content: "";

                    position: absolute;

                    width: 540px;
                    height: 540px;

                    top: -280px;
                    right: -190px;

                    background:
                        rgba(
                            199,
                            221,
                            161,
                            0.29
                        );

                    border-radius: 50%;
                }


                .jl-coming-main::after {
                    content: "";

                    position: absolute;

                    width: 420px;
                    height: 420px;

                    bottom: -260px;
                    left: -160px;

                    background:
                        rgba(
                            49,
                            91,
                            73,
                            0.07
                        );

                    border-radius: 50%;
                }


                .jl-coming-card {
                    position: relative;
                    z-index: 2;

                    width:
                        min(
                            100%,
                            850px
                        );

                    margin:
                        0
                        auto;

                    text-align: center;
                }


                /* =============================================
                   KURZE EINLEITUNG
                   ============================================= */

                .jl-coming-eyebrow {
                    margin:
                        0
                        0
                        19px;

                    color: #71805b;

                    font-size: 0.74rem;
                    font-weight: 750;

                    letter-spacing: 0.16em;

                    text-transform: uppercase;
                }


                /* =============================================
                   HAUPTTITEL
                   ============================================= */

                .jl-coming-title {
                    max-width: 760px;

                    margin:
                        0
                        auto;

                    color: #173c32;

                    font-size:
                        clamp(
                            2.8rem,
                            5.8vw,
                            4.8rem
                        );

                    font-weight: 700;

                    line-height: 1;

                    letter-spacing: -0.05em;
                }


                /* =============================================
                   EHRENRUNDE
                   ============================================= */

                .jl-coming-round {
                    margin:
                        31px
                        auto
                        0;

                    color: #315b49;

                    font-size:
                        clamp(
                            1.15rem,
                            2vw,
                            1.4rem
                        );

                    font-weight: 700;

                    line-height: 1.4;
                }


                /* =============================================
                   TEXT
                   ============================================= */

                .jl-coming-text {
                    max-width: 650px;

                    margin:
                        13px
                        auto
                        0;

                    color: #5d7066;

                    font-size:
                        clamp(
                            1rem,
                            1.6vw,
                            1.08rem
                        );

                    line-height: 1.72;
                }


                /* =============================================
                   THEMEN
                   ============================================= */

                .jl-coming-topics {
                    display: flex;

                    flex-wrap: wrap;

                    justify-content: center;

                    gap: 9px;

                    margin:
                        34px
                        auto
                        0;
                }


                .jl-coming-topic {
                    min-height: 35px;

                    display: inline-flex;

                    align-items: center;
                    justify-content: center;

                    padding:
                        0
                        14px;

                    color: #52685e;

                    background:
                        rgba(
                            255,
                            255,
                            255,
                            0.72
                        );

                    border:
                        1px solid
                        rgba(
                            23,
                            60,
                            50,
                            0.08
                        );

                    border-radius: 999px;

                    font-size: 0.76rem;
                    font-weight: 650;
                }


                /* =============================================
                   STATUS
                   ============================================= */

                .jl-coming-status {
                    width:
                        min(
                            100%,
                            580px
                        );

                    display: flex;

                    align-items: center;
                    justify-content: center;

                    gap: 9px;

                    margin:
                        35px
                        auto
                        0;

                    padding:
                        15px
                        18px;

                    color: #53675d;

                    background:
                        rgba(
                            255,
                            255,
                            255,
                            0.62
                        );

                    border:
                        1px solid
                        rgba(
                            23,
                            60,
                            50,
                            0.07
                        );

                    border-radius: 13px;

                    font-size: 0.8rem;
                    line-height: 1.5;
                }


                .jl-coming-status__dot {
                    width: 8px;
                    height: 8px;

                    flex: 0 0 auto;

                    background: #86a65d;

                    border-radius: 50%;

                    box-shadow:
                        0 0 0 5px
                        rgba(
                            134,
                            166,
                            93,
                            0.13
                        );
                }


                /* =============================================
                   FOOTER
                   ============================================= */

                .jl-coming-footer {
                    margin:
                        36px
                        0
                        0;

                    color: #89938d;

                    font-size: 0.72rem;
                }


                /* =============================================
                   MOBILE
                   ============================================= */

                @media (max-width: 640px) {

                    .jl-coming-header {
                        padding:
                            15px
                            20px;
                    }


                    .jl-coming-logo {
                        height: 48px;
                    }


                    .jl-coming-main {
                        min-height:
                            calc(
                                100vh - 79px
                            );

                        padding:
                            58px
                            18px;
                    }


                    .jl-coming-title {
                        font-size:
                            clamp(
                                2.35rem,
                                11vw,
                                3.4rem
                            );
                    }


                    .jl-coming-round {
                        margin-top: 26px;
                    }


                    .jl-coming-topics {
                        margin-top: 29px;

                        gap: 7px;
                    }


                    .jl-coming-topic {
                        min-height: 33px;

                        padding:
                            0
                            12px;

                        font-size: 0.72rem;
                    }


                    .jl-coming-status {
                        align-items: flex-start;

                        margin-top: 30px;

                        padding:
                            14px
                            15px;

                        text-align: left;
                    }


                    .jl-coming-status__dot {
                        margin-top: 5px;
                    }

                }

            </style>

        </head>


        <body>

            <header class="jl-coming-header">

                <div class="jl-coming-header__inner">

                    <img
                        src="<?php
                        echo esc_url(
                            $logo_url
                        );
                        ?>"
                        alt="<?php
                        echo esc_attr(
                            get_bloginfo('name')
                        );
                        ?>"
                        class="jl-coming-logo"
                    >

                </div>

            </header>


            <main class="jl-coming-main">

                <div class="jl-coming-card">

                    <p class="jl-coming-eyebrow">
                        Webseite im Aufbau
                    </p>


                    <h1 class="jl-coming-title">
                        Jung Leben startet bald.
                    </h1>


                    <p class="jl-coming-round">
                        Wir drehen noch eine kleine Ehrenrunde.
                    </p>


                    <p class="jl-coming-text">
                        Jung Leben bekommt gerade den letzten
                        Feinschliff. Bald findest du hier
                        persönliche Erfahrungen, ausgewählte
                        Empfehlungen und alltagstaugliche Routinen
                        rund um Longevity und Wohlbefinden.
                    </p>


                    <div
                        class="jl-coming-topics"
                        aria-label="Themen von Jung Leben"
                    >

                        <span class="jl-coming-topic">
                            Erfahrungen
                        </span>

                        <span class="jl-coming-topic">
                            Empfehlungen
                        </span>

                        <span class="jl-coming-topic">
                            Routinen
                        </span>

                        <span class="jl-coming-topic">
                            Longevity
                        </span>

                    </div>


                    <div class="jl-coming-status">

                        <span
                            class="jl-coming-status__dot"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Wir arbeiten weiter an den letzten
                            Details und melden uns, sobald alles
                            bereit ist.
                        </span>

                    </div>


                    <p class="jl-coming-footer">
                        © <?php
                        echo esc_html(
                            wp_date('Y')
                        );
                        ?>
                        Jung Leben
                    </p>

                </div>

            </main>


            <?php wp_footer(); ?>

        </body>

        </html>

        <?php
    }
}