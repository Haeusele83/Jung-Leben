<?php
/**
 * Theme-Funktionen für Jung Leben.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Grundlegende Theme-Unterstützung registrieren.
 */
function jung_leben_setup(): void
{
    load_theme_textdomain(
        'jung-leben',
        get_template_directory() . '/languages'
    );

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 120,
        'width'       => 320,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');

    register_nav_menus([
        'primary' => __('Hauptnavigation', 'jung-leben'),
        'footer'  => __('Footer-Navigation', 'jung-leben'),
    ]);
}
add_action('after_setup_theme', 'jung_leben_setup');

/**
 * Styles und Skripte laden.
 */
function jung_leben_enqueue_assets(): void
{
    $theme = wp_get_theme();

    wp_enqueue_style(
        'jung-leben-style',
        get_stylesheet_uri(),
        [],
        $theme->get('Version')
    );

    $site_css_path = get_template_directory() . '/assets/css/site.css';

    if (file_exists($site_css_path)) {
        wp_enqueue_style(
            'jung-leben-site',
            get_template_directory_uri() . '/assets/css/site.css',
            ['jung-leben-style'],
            (string) filemtime($site_css_path)
        );
    }

    $site_js_path = get_template_directory() . '/assets/js/site.js';

    if (file_exists($site_js_path)) {
        wp_enqueue_script(
            'jung-leben-site',
            get_template_directory_uri() . '/assets/js/site.js',
            [],
            (string) filemtime($site_js_path),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'jung_leben_enqueue_assets');