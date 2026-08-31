<?php
/**
 * Coming-Soon-Seite von Jung Leben.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

$logo_url =
    get_template_directory_uri()
    . '/assets/images/logo-jung-leben.png';

$contact_email = 'info@jung-leben.ch';

$current_year = wp_date('Y');
$launch_date =
    Jung_Leben_Core_Coming_Soon::get_launch_date();

$launch_iso =
    $launch_date->format(DATE_ATOM);
?>
<!doctype html>

<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="theme-color"
        content="#ffffff"
    >

    <?php wp_head(); ?>
</head>

<body <?php body_class('jung-leben-coming-soon'); ?>>

<?php wp_body_open(); ?>

<!-- Header im Stil der Live-Website -->
<header class="coming-soon-header">

    <div class="coming-soon-header__inner">

        <div class="coming-soon-header__brand">

            <img
                src="<?php echo esc_url($logo_url); ?>"
                class="coming-soon-header__logo"
                alt="<?php esc_attr_e(
                    'Jung Leben',
                    'jung-leben'
                ); ?>"
            >

        </div>

        <div class="coming-soon-header__status">

            <span
                class="coming-soon-header__status-dot"
                aria-hidden="true"
            ></span>

            <span>
                <?php
                esc_html_e(
                    'Website im Aufbau',
                    'jung-leben'
                );
                ?>
            </span>

        </div>

    </div>

</header>

<main class="coming-soon">

    <div
        class="coming-soon__background"
        aria-hidden="true"
    ></div>

    <div
        class="coming-soon__overlay"
        aria-hidden="true"
    ></div>

    <div class="coming-soon__shell">

        <section class="coming-soon__content">

            <p class="coming-soon__eyebrow">
                <?php
                esc_html_e(
                    'Coming Soon',
                    'jung-leben'
                );
                ?>
            </p>

            <h1 class="coming-soon__title">

                <?php
                esc_html_e(
                    'Jung Leben',
                    'jung-leben'
                );
                ?>

                <span>
                    <?php
                    esc_html_e(
                        'startet bald.',
                        'jung-leben'
                    );
                    ?>
                </span>

            </h1>
            <div
    class="coming-soon__countdown"
    data-countdown
    data-launch="<?php echo esc_attr($launch_iso); ?>"
    aria-label="<?php esc_attr_e(
        'Zeit bis zum Start von Jung Leben',
        'jung-leben'
    ); ?>"
>
    <div class="coming-soon__countdown-item">
        <strong data-countdown-days>00</strong>

        <span>
            <?php esc_html_e(
                'Tage',
                'jung-leben'
            ); ?>
        </span>
    </div>

    <div class="coming-soon__countdown-item">
        <strong data-countdown-hours>00</strong>

        <span>
            <?php esc_html_e(
                'Stunden',
                'jung-leben'
            ); ?>
        </span>
    </div>

    <div class="coming-soon__countdown-item">
        <strong data-countdown-minutes>00</strong>

        <span>
            <?php esc_html_e(
                'Minuten',
                'jung-leben'
            ); ?>
        </span>
    </div>

    <div class="coming-soon__countdown-item">
        <strong data-countdown-seconds>00</strong>

        <span>
            <?php esc_html_e(
                'Sekunden',
                'jung-leben'
            ); ?>
        </span>
    </div>
</div>

            <p class="coming-soon__lead">
                <?php
                esc_html_e(
                    'Wir arbeiten gerade an einer neuen Plattform für Longevity, persönliche Erfahrungen und ausgewählte Empfehlungen.',
                    'jung-leben'
                );
                ?>
            </p>

            <a
                href="<?php echo esc_attr(
                    'mailto:' . $contact_email
                ); ?>"
                class="coming-soon__contact"
            >
                <?php echo esc_html($contact_email); ?>

                <span aria-hidden="true">→</span>
            </a>

        </section>

        <footer class="coming-soon__footer">

            <span>
                © <?php echo esc_html($current_year); ?>
                Jung Leben
            </span>

        </footer>

    </div>

</main>

<?php wp_footer(); ?>

</body>
</html>