<?php
/**
 * Plugin Name: Jung Leben Core
 * Plugin URI: https://jung-leben.ch
 * Description: Zentrale Produkt-, Partner- und Affiliate-Funktionen für Jung Leben.
 * Version: 0.1.0
 * Author: Jung Leben
 * Text Domain: jung-leben-core
 * Domain Path: /languages
 * Requires at least: 6.5
 * Requires PHP: 8.0
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Plugin-Konstanten.
 */
define(
    'JUNG_LEBEN_CORE_VERSION',
    '0.1.0'
);

define(
    'JUNG_LEBEN_CORE_FILE',
    __FILE__
);

define(
    'JUNG_LEBEN_CORE_PATH',
    plugin_dir_path(__FILE__)
);

define(
    'JUNG_LEBEN_CORE_URL',
    plugin_dir_url(__FILE__)
);

/**
 * Übersetzungen laden.
 */
function jung_leben_core_load_textdomain(): void
{
    load_plugin_textdomain(
        'jung-leben-core',
        false,
        dirname(plugin_basename(__FILE__))
            . '/languages'
    );
}
add_action(
    'plugins_loaded',
    'jung_leben_core_load_textdomain'
);

/**
 * Produktverwaltung laden.
 */
require_once JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-products.php';

/**
 * Plugin-Funktionen initialisieren.
 */
Jung_Leben_Core_Products::init();

/**
 * Aktivierung und Deaktivierung.
 */
register_activation_hook(
    __FILE__,
    [
        Jung_Leben_Core_Products::class,
        'activate',
    ]
);

register_deactivation_hook(
    __FILE__,
    [
        Jung_Leben_Core_Products::class,
        'deactivate',
    ]
);