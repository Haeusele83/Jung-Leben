<?php
/**
 * Coming-Soon-Modus für Jung Leben.
 *
 * Zeigt Besucherinnen und Besuchern bis zum Launch
 * eine reduzierte Coming-Soon-Seite mit Countdown.
 *
 * Angemeldete Administratoren können die eigentliche
 * Website weiterhin normal aufrufen und bearbeiten.
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
     * Geplanter Launch.
     *
     * Dienstag, 8. September 2026
     * um 20:00 Uhr Schweizer Zeit.
     */
    private const LAUNCH_DATE =
        '2026-09-08 20:00:00';


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
         * WordPress-Backend niemals blockieren.
         */
        if (is_admin()) {
            return;
        }


        /*
         * AJAX, REST und Cron nicht blockieren.
         */
        if (
            wp_doing_ajax()
            || wp_doing_cron()
            || defined('REST_REQUEST')
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
            && current_user_can(
                'edit_pages'
            )
        ) {
            return;
        }


        /*
         * Login-Seite nicht blockieren.
         */
        global $pagenow;

        if (
            isset($pagenow)
            && $pagenow === 'wp-login.php'
        ) {
            return;
        }


        /*
         * Nach Erreichen des Launch-Termins wird die
         * Website automatisch öffentlich zugänglich.
         */
        if (self::launch_has_started()) {
            return;
        }


        self::render_page();

        exit;
    }


    /* =========================================================
       LAUNCH-ZEIT
       ========================================================= */

    /**
     * Launch-Zeitpunkt als DateTime laden.
     */
    private static function get_launch_datetime(): DateTimeImmutable
    {
        $timezone =
            new DateTimeZone(
                'Europe/Zurich'
            );


        return
            new DateTimeImmutable(
                self::LAUNCH_DATE,
                $timezone
            );
    }


    /**
     * Prüfen, ob der Launch bereits erreicht wurde.
     */
    private static function launch_has_started(): bool
    {
        $timezone =
            new DateTimeZone(
                'Europe/Zurich'
            );


        $now =
            new DateTimeImmutable(
                'now',
                $timezone
            );


        return
            $now >=
            self::get_launch_datetime();
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
         * Customizer-Logo bevorzugen.
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


            if (is_string($logo_url)) {
                return $logo_url;
            }
        }


        /*
         * Fallback auf das Jung-Leben-Theme-Logo.
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
        $launch =
            self::get_launch_datetime();


        /*
         * ISO-Datum mit korrektem Schweizer Offset
         * für JavaScript.
         */
        $launch_iso =
            $launch->format(
                DATE_ATOM
            );


        $logo_url =
            self::get_logo_url();


        status_header(200);

        nocache_headers();
        ?>
        <!DOCTYPE html>
        <html
            <?php language_attributes(); ?>
        >

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
                    width: min(
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
                        70px
                        24px;
                }


                .jl-coming-main::before {
                    content: "";

                    position: absolute;

                    width: 520px;
                    height: 520px;

                    top: -250px;
                    right: -190px;

                    background:
                        rgba(
                            199,
                            221,
                            161,
                            0.28
                        );

                    border-radius: 50%;
                }


                .jl-coming-main::after {
                    content: "";

                    position: absolute;

                    width: 410px;
                    height: 410px;

                    bottom: -250px;
                    left: -150px;

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

                    width: min(
                        100%,
                        820px
                    );

                    margin: 0 auto;

                    text-align: center;
                }


                /* =============================================
                   EYEBROW
                   ============================================= */

                .jl-coming-eyebrow {
                    margin:
                        0
                        0
                        20px;

                    color: #73815c;

                    font-size: 0.74rem;
                    font-weight: 750;

                    letter-spacing: 0.16em;

                    text-transform: uppercase;
                }


                /* =============================================
                   TITEL
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
                            6vw,
                            5rem
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
                        28px
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


                .jl-coming-text {
                    max-width: 630px;

                    margin:
                        12px
                        auto
                        0;

                    color: #5d7066;

                    font-size:
                        clamp(
                            1rem,
                            1.6vw,
                            1.08rem
                        );

                    line-height: 1.7;
                }


                /* =============================================
                   COUNTDOWN
                   ============================================= */

                .jl-countdown {
                    max-width: 660px;

                    display: grid;

                    grid-template-columns:
                        repeat(
                            4,
                            minmax(
                                0,
                                1fr
                            )
                        );

                    gap: 12px;

                    margin:
                        42px
                        auto
                        0;
                }


                .jl-countdown__item {
                    padding:
                        21px
                        12px
                        19px;

                    background:
                        rgba(
                            255,
                            255,
                            255,
                            0.76
                        );

                    border:
                        1px solid
                        rgba(
                            23,
                            60,
                            50,
                            0.08
                        );

                    border-radius: 15px;

                    box-shadow:
                        0 12px 30px
                        rgba(
                            23,
                            60,
                            50,
                            0.035
                        );

                    backdrop-filter:
                        blur(8px);
                }


                .jl-countdown__number {
                    display: block;

                    color: #173c32;

                    font-size:
                        clamp(
                            1.9rem,
                            4vw,
                            2.7rem
                        );

                    font-weight: 720;

                    line-height: 1;
                }


                .jl-countdown__label {
                    display: block;

                    margin-top: 8px;

                    color: #7a8880;

                    font-size: 0.68rem;
                    font-weight: 700;

                    letter-spacing: 0.09em;

                    text-transform: uppercase;
                }


                /* =============================================
                   DATUM
                   ============================================= */

                .jl-coming-date {
                    margin:
                        25px
                        0
                        0;

                    color: #315b49;

                    font-size: 0.85rem;
                    font-weight: 650;
                }


                /* =============================================
                   FOOTER
                   ============================================= */

                .jl-coming-footer {
                    margin-top: 38px;

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
                            55px
                            18px;
                    }


                    .jl-coming-title {
                        font-size:
                            clamp(
                                2.4rem,
                                12vw,
                                3.5rem
                            );
                    }


                    .jl-countdown {
                        grid-template-columns:
                            repeat(
                                2,
                                minmax(
                                    0,
                                    1fr
                                )
                            );

                        max-width: 360px;

                        margin-top: 34px;
                    }


                    .jl-countdown__item {
                        padding:
                            18px
                            10px;
                    }

                }

            </style>

        </head>


        <body>

            <!-- =============================================
                 HEADER
                 ============================================= -->

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


            <!-- =============================================
                 CONTENT
                 ============================================= -->

            <main class="jl-coming-main">

                <div class="jl-coming-card">

                    <p class="jl-coming-eyebrow">
                        Webseite im Aufbau
                    </p>


                    <h1 class="jl-coming-title">
                        Jung Leben startet bald.
                    </h1>


                    <p class="jl-coming-round">
                        Wir machen noch eine kleine Ehrenrunde.
                    </p>


                    <p class="jl-coming-text">
                        Ein paar letzte Details fehlen noch –
                        dann sind wir bereit.
                        Jung Leben startet am
                        8. September um 20:00 Uhr.
                    </p>


                    <!-- =====================================
                         COUNTDOWN
                         ===================================== -->

                    <div
                        class="jl-countdown"
                        id="jl-countdown"
                        aria-label="Countdown bis zum Start von Jung Leben"
                    >

                        <div class="jl-countdown__item">

                            <span
                                class="jl-countdown__number"
                                id="jl-countdown-days"
                            >
                                00
                            </span>

                            <span class="jl-countdown__label">
                                Tage
                            </span>

                        </div>


                        <div class="jl-countdown__item">

                            <span
                                class="jl-countdown__number"
                                id="jl-countdown-hours"
                            >
                                00
                            </span>

                            <span class="jl-countdown__label">
                                Stunden
                            </span>

                        </div>


                        <div class="jl-countdown__item">

                            <span
                                class="jl-countdown__number"
                                id="jl-countdown-minutes"
                            >
                                00
                            </span>

                            <span class="jl-countdown__label">
                                Minuten
                            </span>

                        </div>


                        <div class="jl-countdown__item">

                            <span
                                class="jl-countdown__number"
                                id="jl-countdown-seconds"
                            >
                                00
                            </span>

                            <span class="jl-countdown__label">
                                Sekunden
                            </span>

                        </div>

                    </div>


                    <p class="jl-coming-date">
                        08.09.2026 · 20:00 Uhr
                    </p>


                    <p class="jl-coming-footer">
                        © <?php echo esc_html(wp_date('Y')); ?>
                        Jung Leben
                    </p>

                </div>

            </main>


            <!-- =============================================
                 COUNTDOWN SCRIPT
                 ============================================= -->

            <script>
                (() => {
                    'use strict';

                    const launchDate =
                        new Date(
                            <?php
                            echo wp_json_encode(
                                $launch_iso
                            );
                            ?>
                        ).getTime();

                    const daysElement =
                        document.getElementById(
                            'jl-countdown-days'
                        );

                    const hoursElement =
                        document.getElementById(
                            'jl-countdown-hours'
                        );

                    const minutesElement =
                        document.getElementById(
                            'jl-countdown-minutes'
                        );

                    const secondsElement =
                        document.getElementById(
                            'jl-countdown-seconds'
                        );

                    const countdown =
                        document.getElementById(
                            'jl-countdown'
                        );


                    if (
                        ! daysElement
                        || ! hoursElement
                        || ! minutesElement
                        || ! secondsElement
                        || ! countdown
                    ) {
                        return;
                    }


                    const formatNumber =
                        (number) =>
                            String(number)
                                .padStart(
                                    2,
                                    '0'
                                );


                    const updateCountdown = () => {
                        const now =
                            Date.now();

                        const distance =
                            launchDate
                            - now;


                        if (distance <= 0) {
                            daysElement.textContent =
                                '00';

                            hoursElement.textContent =
                                '00';

                            minutesElement.textContent =
                                '00';

                            secondsElement.textContent =
                                '00';


                            window.location.reload();

                            return;
                        }


                        const days =
                            Math.floor(
                                distance
                                /
                                (
                                    1000
                                    * 60
                                    * 60
                                    * 24
                                )
                            );


                        const hours =
                            Math.floor(
                                (
                                    distance
                                    %
                                    (
                                        1000
                                        * 60
                                        * 60
                                        * 24
                                    )
                                )
                                /
                                (
                                    1000
                                    * 60
                                    * 60
                                )
                            );


                        const minutes =
                            Math.floor(
                                (
                                    distance
                                    %
                                    (
                                        1000
                                        * 60
                                        * 60
                                    )
                                )
                                /
                                (
                                    1000
                                    * 60
                                )
                            );


                        const seconds =
                            Math.floor(
                                (
                                    distance
                                    %
                                    (
                                        1000
                                        * 60
                                    )
                                )
                                /
                                1000
                            );


                        daysElement.textContent =
                            formatNumber(
                                days
                            );

                        hoursElement.textContent =
                            formatNumber(
                                hours
                            );

                        minutesElement.textContent =
                            formatNumber(
                                minutes
                            );

                        secondsElement.textContent =
                            formatNumber(
                                seconds
                            );
                    };


                    updateCountdown();

                    window.setInterval(
                        updateCountdown,
                        1000
                    );
                })();
            </script>


            <?php wp_footer(); ?>

        </body>

        </html>

        <?php
    }
}