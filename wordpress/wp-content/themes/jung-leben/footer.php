<?php
/**
 * Footer.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Seitenlink anhand des Slugs.
 */
$get_page_url = static function (
    string $slug,
    string $fallback
): string {
    $page = get_page_by_path(
        $slug
    );

    if ($page instanceof WP_Post) {
        return (string) get_permalink(
            $page
        );
    }

    return home_url(
        $fallback
    );
};


$home_url =
    home_url('/');

$recommendations_url =
    $get_page_url(
        'empfehlungen',
        '/empfehlungen/'
    );

$routines_url =
    $get_page_url(
        'routinen',
        '/routinen/'
    );

$about_url =
    $get_page_url(
        'ueber-mich',
        '/ueber-mich/'
    );

$contact_url =
    $get_page_url(
        'kontakt',
        '/kontakt/'
    );


/**
 * Ratgeber.
 */
$posts_page_id =
    (int) get_option(
        'page_for_posts'
    );

$experiences_url =
    $posts_page_id > 0
        ? (string) get_permalink(
            $posts_page_id
        )
        : home_url(
            '/ratgeber/'
        );


/**
 * Logo.
 */
$logo_url =
    get_template_directory_uri()
    . '/assets/images/logo-jung-leben.png';

$current_year =
    wp_date('Y');
?>

<footer class="site-footer">

    <div class="container site-footer__main">

        <!-- Brand -->
        <div class="site-footer__brand">

            <a
                href="<?php
                echo esc_url(
                    $home_url
                );
                ?>"
                class="site-footer__logo-link"
                aria-label="<?php
                esc_attr_e(
                    'Jung Leben – Startseite',
                    'jung-leben'
                );
                ?>"
            >
                <img
                    src="<?php
                    echo esc_url(
                        $logo_url
                    );
                    ?>"
                    class="site-footer__logo"
                    alt="Jung Leben"
                >
            </a>

            <p class="site-footer__description">
                <?php
                esc_html_e(
                    'Persönliche Erfahrungen, verständliches Wissen und ausgewählte Empfehlungen rund um Longevity und einen bewussten Alltag.',
                    'jung-leben'
                );
                ?>
            </p>

            <a
                href="mailto:info@jung-leben.ch"
                class="site-footer__email"
            >
                info@jung-leben.ch
                <span aria-hidden="true">
                    ↗
                </span>
            </a>

        </div>


        <!-- Navigation -->
        <div class="site-footer__column">

            <p class="site-footer__heading">
                <?php
                esc_html_e(
                    'Entdecken',
                    'jung-leben'
                );
                ?>
            </p>

            <nav
                aria-label="<?php
                esc_attr_e(
                    'Footer – Inhalte',
                    'jung-leben'
                );
                ?>"
            >
                <ul class="site-footer__links">

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                $experiences_url
                            );
                            ?>"
                        >
                            Erfahrungen
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                $recommendations_url
                            );
                            ?>"
                        >
                            Empfehlungen
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                $routines_url
                            );
                            ?>"
                        >
                            Routinen
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                $about_url
                            );
                            ?>"
                        >
                            About
                        </a>
                    </li>

                </ul>
            </nav>

        </div>


        <!-- Kontakt -->
        <div class="site-footer__column">

            <p class="site-footer__heading">
                <?php
                esc_html_e(
                    'Kontakt',
                    'jung-leben'
                );
                ?>
            </p>

            <p class="site-footer__column-text">
                <?php
                esc_html_e(
                    'Fragen, Feedback oder eine interessante Idee?',
                    'jung-leben'
                );
                ?>
            </p>

            <a
                href="<?php
                echo esc_url(
                    $contact_url
                );
                ?>"
                class="site-footer__contact-link"
            >
                Kontakt aufnehmen
                <span aria-hidden="true">
                    →
                </span>
            </a>

        </div>


        <!-- Rechtliches -->
        <div class="site-footer__column">

            <p class="site-footer__heading">
                <?php
                esc_html_e(
                    'Rechtliches',
                    'jung-leben'
                );
                ?>
            </p>

            <?php
            if (
                has_nav_menu(
                    'footer'
                )
            ) :
                ?>

                <?php
                wp_nav_menu([
                    'theme_location' =>
                        'footer',

                    'container' =>
                        false,

                    'menu_class' =>
                        'site-footer__links',

                    'fallback_cb' =>
                        false,

                    'depth' =>
                        1,
                ]);
                ?>

            <?php else : ?>

                <ul class="site-footer__links">

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                home_url(
                                    '/impressum/'
                                )
                            );
                            ?>"
                        >
                            Impressum
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                home_url(
                                    '/datenschutz/'
                                )
                            );
                            ?>"
                        >
                            Datenschutz
                        </a>
                    </li>

                    <li>
                        <a
                            href="<?php
                            echo esc_url(
                                home_url(
                                    '/affiliate-hinweis/'
                                )
                            );
                            ?>"
                        >
                            Affiliate-Hinweis
                        </a>
                    </li>

                </ul>

            <?php endif; ?>

        </div>

    </div>


    <div class="container site-footer__bottom">

        <p>
            © <?php
            echo esc_html(
                $current_year
            );
            ?> Jung Leben
        </p>

        <p>
            <?php
            esc_html_e(
                'Bewusster entscheiden. Gesund älter werden.',
                'jung-leben'
            );
            ?>
        </p>

    </div>

</footer>

<?php
wp_footer();
?>

</body>
</html>