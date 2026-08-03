<?php
/**
 * Kopfbereich des Jung-Leben-Themes.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container header-content">

        <a
            href="<?php echo esc_url(home_url('/')); ?>"
            class="logo"
            aria-label="<?php esc_attr_e(
                'Zur Startseite von Jung Leben',
                'jung-leben'
            ); ?>"
        >
            <span class="logo-icon" aria-hidden="true">JL</span>

            <span class="logo-text">
                <?php echo esc_html(get_bloginfo('name')); ?>
            </span>
        </a>

        <nav
            class="main-nav"
            id="mainNav"
            aria-label="<?php esc_attr_e(
                'Hauptnavigation',
                'jung-leben'
            ); ?>"
        >
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'main-nav-list',
                'fallback_cb'    => false,
                'depth'          => 1,
            ]);
            ?>
        </nav>

        <button
            class="nav-toggle"
            id="navToggle"
            type="button"
            aria-label="<?php esc_attr_e(
                'Navigation öffnen',
                'jung-leben'
            ); ?>"
            aria-controls="mainNav"
            aria-expanded="false"
        >
            <span aria-hidden="true">☰</span>
        </button>

    </div>
</header>