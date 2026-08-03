<?php
/**
 * Kopfbereich des Jung-Leben-Themes.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

$theme_logo_path = get_template_directory()
    . '/assets/images/logo-jung-leben.png';

$theme_logo_url = get_template_directory_uri()
    . '/assets/images/logo-jung-leben.png';
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

        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>

                <?php the_custom_logo(); ?>

            <?php elseif (file_exists($theme_logo_path)) : ?>

                <a
                    class="site-logo-link"
                    href="<?php echo esc_url(home_url('/')); ?>"
                    aria-label="<?php esc_attr_e(
                        'Zur Startseite von Jung Leben',
                        'jung-leben'
                    ); ?>"
                >
                    <img
                        class="site-logo-image"
                        src="<?php echo esc_url($theme_logo_url); ?>"
                        alt="<?php echo esc_attr(
                            get_bloginfo('name')
                        ); ?>"
                    >
                </a>

            <?php else : ?>

                <a
                    class="site-title-link"
                    href="<?php echo esc_url(home_url('/')); ?>"
                >
                    <?php echo esc_html(get_bloginfo('name')); ?>
                </a>

            <?php endif; ?>
        </div>

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
            <span class="nav-toggle__line"></span>
            <span class="nav-toggle__line"></span>
            <span class="nav-toggle__line"></span>
        </button>

    </div>
</header>