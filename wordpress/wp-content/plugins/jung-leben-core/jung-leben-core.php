<?php
/**
 * Plugin Name: Jung Leben Core
 * Plugin URI: https://jung-leben.ch
 * Description: Zentrale Produkt-, Content- und Affiliate-Funktionen für Jung Leben.
 * Version: 0.6.1
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


/* =========================================================
   KONSTANTEN
   ========================================================= */

define(
    'JUNG_LEBEN_CORE_VERSION',
    '0.6.1'
);

define(
    'JUNG_LEBEN_CORE_FILE',
    __FILE__
);

define(
    'JUNG_LEBEN_CORE_PATH',
    plugin_dir_path(
        __FILE__
    )
);

define(
    'JUNG_LEBEN_CORE_URL',
    plugin_dir_url(
        __FILE__
    )
);


/* =========================================================
   ÜBERSETZUNGEN
   ========================================================= */

function jung_leben_core_load_textdomain(): void
{
    load_plugin_textdomain(
        'jung-leben-core',
        false,
        dirname(
            plugin_basename(
                __FILE__
            )
        )
        . '/languages'
    );
}

add_action(
    'plugins_loaded',
    'jung_leben_core_load_textdomain'
);


/* =========================================================
   KOMPONENTEN
   ========================================================= */

require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-products.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-product-fields.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-partners.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-partner-fields.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-brand-fields.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-experience-fields.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-content-import.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-content-relations.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-data-maintenance.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-partner-demo.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-iherb-demo-products.php';


require_once
    JUNG_LEBEN_CORE_PATH
    . 'includes/class-jung-leben-core-coming-soon.php';


/* =========================================================
   INITIALISIERUNG
   ========================================================= */

Jung_Leben_Core_Products::init();

Jung_Leben_Core_Product_Fields::init();

Jung_Leben_Core_Partners::init();

Jung_Leben_Core_Partner_Fields::init();

Jung_Leben_Core_Brand_Fields::init();

Jung_Leben_Core_Experience_Fields::init();

Jung_Leben_Core_Content_Import::init();

Jung_Leben_Core_Content_Relations::init();

Jung_Leben_Core_Data_Maintenance::init();

Jung_Leben_Core_Partner_Demo::init();

Jung_Leben_Core_IHerb_Demo_Products::init();

Jung_Leben_Core_Coming_Soon::init();


/* =========================================================
   AKTIVIERUNG
   ========================================================= */

register_activation_hook(
    __FILE__,
    [
        Jung_Leben_Core_Products::class,
        'activate',
    ]
);


register_activation_hook(
    __FILE__,
    [
        Jung_Leben_Core_Partner_Demo::class,
        'activate',
    ]
);


/* =========================================================
   DEAKTIVIERUNG
   ========================================================= */

register_deactivation_hook(
    __FILE__,
    [
        Jung_Leben_Core_Products::class,
        'deactivate',
    ]
);


register_deactivation_hook(
    __FILE__,
    [
        Jung_Leben_Core_Partner_Demo::class,
        'deactivate',
    ]
);