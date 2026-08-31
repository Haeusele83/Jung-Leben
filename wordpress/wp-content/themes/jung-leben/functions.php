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


/* =========================================================
   THEME SETUP
   ========================================================= */

/**
 * Grundlegende Theme-Unterstützung registrieren.
 */
function jung_leben_setup(): void
{
    load_theme_textdomain(
        'jung-leben',
        get_template_directory()
            . '/languages'
    );


    add_theme_support(
        'title-tag'
    );


    add_theme_support(
        'post-thumbnails'
    );


    add_theme_support(
        'custom-logo',
        [
            'height'      => 120,
            'width'       => 320,
            'flex-height' => true,
            'flex-width'  => true,
        ]
    );


    add_theme_support(
        'html5',
        [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]
    );


    add_theme_support(
        'align-wide'
    );


    add_theme_support(
        'responsive-embeds'
    );


    register_nav_menus([
        'primary' =>
            __(
                'Hauptnavigation',
                'jung-leben'
            ),

        'footer' =>
            __(
                'Footer-Navigation',
                'jung-leben'
            ),
    ]);
}


add_action(
    'after_setup_theme',
    'jung_leben_setup'
);


/* =========================================================
   HILFSFUNKTIONEN FÜR ASSETS
   ========================================================= */

/**
 * Eine CSS-Datei aus assets/css laden.
 */
function jung_leben_enqueue_css_file(
    string $filename,
    string $handle,
    array $dependencies = []
): void {
    $path =
        get_template_directory()
        . '/assets/css/'
        . $filename;


    if (! file_exists($path)) {
        return;
    }


    wp_enqueue_style(
        $handle,
        get_template_directory_uri()
            . '/assets/css/'
            . $filename,
        $dependencies,
        (string)
        filemtime($path)
    );
}


/**
 * Eine JS-Datei aus assets/js laden.
 */
function jung_leben_enqueue_js_file(
    string $filename,
    string $handle,
    array $dependencies = [],
    bool $in_footer = true
): void {
    $path =
        get_template_directory()
        . '/assets/js/'
        . $filename;


    if (! file_exists($path)) {
        return;
    }


    wp_enqueue_script(
        $handle,
        get_template_directory_uri()
            . '/assets/js/'
            . $filename,
        $dependencies,
        (string)
        filemtime($path),
        $in_footer
    );
}


/* =========================================================
   FRONTEND ASSETS
   ========================================================= */

/**
 * Sämtliche Frontend-Styles und Skripte laden.
 *
 * WICHTIG:
 * Die zusätzlichen Theme-CSS-Dateien werden automatisch
 * erkannt. Damit muss bei einer neuen Seiten-CSS-Datei
 * functions.php künftig nicht nochmals angepasst werden.
 */
function jung_leben_enqueue_assets(): void
{
    $theme =
        wp_get_theme();


    /* =====================================================
       1. STYLE.CSS
       ===================================================== */

    wp_enqueue_style(
        'jung-leben-style',
        get_stylesheet_uri(),
        [],
        $theme->get(
            'Version'
        )
    );


    /* =====================================================
       2. GLOBALES SITE.CSS
       ===================================================== */

    $site_css_path =
        get_template_directory()
        . '/assets/css/site.css';


    $global_css_dependency =
        'jung-leben-style';


    if (
        file_exists(
            $site_css_path
        )
    ) {
        wp_enqueue_style(
            'jung-leben-site',
            get_template_directory_uri()
                . '/assets/css/site.css',
            [
                'jung-leben-style',
            ],
            (string)
            filemtime(
                $site_css_path
            )
        );


        $global_css_dependency =
            'jung-leben-site';
    }


    /* =====================================================
       3. ALLE WEITEREN THEME-CSS-DATEIEN
       ===================================================== */

    /**
     * Das ist bewusst automatisch.
     *
     * Beispiele:
     * - ratgeber.css
     * - recommendations-page.css
     * - routines.css
     * - about.css / about-page.css
     * - experience-single.css
     * - product-detail.css
     * - footer.css
     * - contact.css
     *
     * Neue CSS-Dateien werden automatisch berücksichtigt.
     */
    $css_directory =
        get_template_directory()
        . '/assets/css';


    if (
        is_dir(
            $css_directory
        )
    ) {
        $css_files =
            glob(
                $css_directory
                . '/*.css'
            );


        if (
            is_array(
                $css_files
            )
        ) {
            sort(
                $css_files,
                SORT_NATURAL
            );


            foreach (
                $css_files
                as $css_file
            ) {
                $filename =
                    basename(
                        $css_file
                    );


                /*
                 * site.css wurde bereits als globale
                 * Basis geladen.
                 */
                if (
                    $filename ===
                    'site.css'
                ) {
                    continue;
                }


                /*
                 * Falls später reine Admin- oder
                 * Editor-CSS-Dateien im selben Ordner
                 * liegen, nicht im Frontend laden.
                 */
                if (
                    str_starts_with(
                        $filename,
                        'admin-'
                    )
                    ||
                    str_starts_with(
                        $filename,
                        'editor-'
                    )
                ) {
                    continue;
                }


                $handle =
                    'jung-leben-'
                    . sanitize_title(
                        pathinfo(
                            $filename,
                            PATHINFO_FILENAME
                        )
                    );


                wp_enqueue_style(
                    $handle,
                    get_template_directory_uri()
                        . '/assets/css/'
                        . $filename,
                    [
                        $global_css_dependency,
                    ],
                    (string)
                    filemtime(
                        $css_file
                    )
                );
            }
        }
    }


    /* =====================================================
       4. GLOBALES SITE.JS
       ===================================================== */

    jung_leben_enqueue_js_file(
        'site.js',
        'jung-leben-site'
    );


    /* =====================================================
       5. STARTSEITE
       ===================================================== */

    if (
        is_front_page()
    ) {
        $routine_links_js_path =
            get_template_directory()
            . '/assets/js/home-routine-links.js';


        if (
            file_exists(
                $routine_links_js_path
            )
        ) {
            wp_enqueue_script(
                'jung-leben-home-routine-links',
                get_template_directory_uri()
                    . '/assets/js/home-routine-links.js',
                [],
                (string)
                filemtime(
                    $routine_links_js_path
                ),
                true
            );


            wp_localize_script(
                'jung-leben-home-routine-links',
                'JungLebenRoutineLinks',
                [
                    'recommendationsUrl' =>
                        home_url(
                            '/empfehlungen/'
                        ),
                ]
            );
        }
    }


    /* =====================================================
       6. EMPFEHLUNGEN
       ===================================================== */

    if (
        is_page(
            'empfehlungen'
        )
    ) {
        jung_leben_enqueue_js_file(
            'recommendations.js',
            'jung-leben-recommendations'
        );
    }
}


add_action(
    'wp_enqueue_scripts',
    'jung_leben_enqueue_assets',
    20
);


/* =========================================================
   ACF-FELDGRUPPEN LADEN
   ========================================================= */

/**
 * Sämtliche im Theme definierten ACF-Feldgruppen laden.
 *
 * Jede Datei registriert ihre Feldgruppe selbst über
 * den Hook acf/init.
 */
function jung_leben_load_acf_files(): void
{
    $acf_files = [
        'acf-homepage.php',
        'acf-recommendations.php',
        'acf-experiences.php',
        'acf-routines.php',
        'acf-about.php',
    ];


    foreach (
        $acf_files
        as $acf_file
    ) {
        $path =
            get_template_directory()
            . '/inc/'
            . $acf_file;


        if (
            file_exists(
                $path
            )
        ) {
            require_once
                $path;
        }
    }
}


jung_leben_load_acf_files();


/* =========================================================
   KUNDENFREUNDLICHER WORDPRESS-EDITOR
   ========================================================= */

/**
 * Prüfen, ob eine Seite über unsere ACF-Oberfläche
 * gepflegt wird.
 */
function jung_leben_is_managed_content_page(
    int $post_id
): bool {
    if ($post_id <= 0) {
        return false;
    }


    /*
     * WordPress-Beitragsseite = Erfahrungen.
     */
    $experiences_page_id =
        (int)
        get_option(
            'page_for_posts'
        );


    if (
        $experiences_page_id > 0
        &&
        $post_id ===
        $experiences_page_id
    ) {
        return true;
    }


    $post =
        get_post(
            $post_id
        );


    if (
        ! $post
        instanceof WP_Post
        ||
        $post->post_type !==
        'page'
    ) {
        return false;
    }


    $managed_slugs = [
        'startseite',
        'empfehlungen',
        'routinen',
        'ueber-mich',
    ];


    return
        in_array(
            $post->post_name,
            $managed_slugs,
            true
        );
}


/**
 * Gutenberg für die von ACF gesteuerten Seiten deaktivieren.
 *
 * Der Kunde soll dort nicht mit Layout-Blöcken arbeiten,
 * sondern ausschliesslich mit den dafür vorgesehenen
 * Inhaltsfeldern.
 */
function jung_leben_disable_block_editor_for_managed_pages(
    bool $use_block_editor,
    WP_Post $post
): bool {
    if (
        jung_leben_is_managed_content_page(
            (int)
            $post->ID
        )
    ) {
        return false;
    }


    return
        $use_block_editor;
}


add_filter(
    'use_block_editor_for_post',
    'jung_leben_disable_block_editor_for_managed_pages',
    10,
    2
);


/**
 * Den normalen WordPress-Inhaltseditor auf den
 * ACF-gesteuerten Seiten ausblenden.
 *
 * Dadurch sieht der Kunde nach dem Seitentitel direkt
 * die ACF-Felder und nicht einen leeren Texteditor.
 */
function jung_leben_hide_default_editor_on_managed_pages(): void
{
    if (! is_admin()) {
        return;
    }


    $post_id = 0;


    if (
        isset(
            $_GET['post']
        )
    ) {
        $post_id =
            absint(
                $_GET['post']
            );
    }


    if (
        $post_id <= 0
        ||
        ! jung_leben_is_managed_content_page(
            $post_id
        )
    ) {
        return;
    }


    remove_post_type_support(
        'page',
        'editor'
    );
}


add_action(
    'admin_init',
    'jung_leben_hide_default_editor_on_managed_pages'
);


/* =========================================================
   ACF-HINWEIS IM ADMIN
   ========================================================= */

/**
 * Hinweis anzeigen, falls ACF deaktiviert wurde.
 */
function jung_leben_acf_admin_notice(): void
{
    if (
        ! current_user_can(
            'manage_options'
        )
    ) {
        return;
    }


    if (
        function_exists(
            'acf_add_local_field_group'
        )
    ) {
        return;
    }
    ?>

    <div class="notice notice-warning">

        <p>

            <strong>
                Jung Leben:
            </strong>

            Advanced Custom Fields ist nicht aktiv.
            Die bearbeitbaren Seiteninhalte können deshalb
            derzeit nicht angezeigt werden.

        </p>

    </div>

    <?php
}


add_action(
    'admin_notices',
    'jung_leben_acf_admin_notice'
);