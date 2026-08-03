<?php
/**
 * Produktverwaltung für Jung Leben.
 *
 * Registriert:
 * - den Inhaltstyp «Produkte»
 * - Produktkategorien
 * - Marken
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Produktverwaltung.
 */
final class Jung_Leben_Core_Products
{
    /**
     * Interner Name des Produkt-Inhaltstyps.
     */
    public const POST_TYPE = 'jl_product';

    /**
     * Interner Name der Produktkategorien.
     */
    public const TAXONOMY_CATEGORY =
        'jl_product_category';

    /**
     * Interner Name der Marken.
     */
    public const TAXONOMY_BRAND =
        'jl_product_brand';

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'init',
            [
                self::class,
                'register_content_types',
            ]
        );

        add_action(
            'admin_init',
            [
                self::class,
                'maybe_upgrade',
            ]
        );

        add_filter(
            'manage_' . self::POST_TYPE . '_posts_columns',
            [
                self::class,
                'add_admin_columns',
            ]
        );

        add_action(
            'manage_' . self::POST_TYPE
                . '_posts_custom_column',
            [
                self::class,
                'render_admin_columns',
            ],
            10,
            2
        );
    }

    /**
     * Alle Inhaltstypen und Taxonomien registrieren.
     */
    public static function register_content_types(): void
    {
        self::register_product_post_type();
        self::register_product_category_taxonomy();
        self::register_brand_taxonomy();
    }

    /**
     * Produkt-Inhaltstyp registrieren.
     */
    private static function register_product_post_type(): void
    {
        $labels = [
            'name' => __(
                'Produkte',
                'jung-leben-core'
            ),
            'singular_name' => __(
                'Produkt',
                'jung-leben-core'
            ),
            'menu_name' => __(
                'Produkte',
                'jung-leben-core'
            ),
            'name_admin_bar' => __(
                'Produkt',
                'jung-leben-core'
            ),
            'add_new' => __(
                'Neues Produkt',
                'jung-leben-core'
            ),
            'add_new_item' => __(
                'Neues Produkt erstellen',
                'jung-leben-core'
            ),
            'new_item' => __(
                'Neues Produkt',
                'jung-leben-core'
            ),
            'edit_item' => __(
                'Produkt bearbeiten',
                'jung-leben-core'
            ),
            'view_item' => __(
                'Produkt ansehen',
                'jung-leben-core'
            ),
            'view_items' => __(
                'Produkte ansehen',
                'jung-leben-core'
            ),
            'all_items' => __(
                'Alle Produkte',
                'jung-leben-core'
            ),
            'search_items' => __(
                'Produkte durchsuchen',
                'jung-leben-core'
            ),
            'parent_item_colon' => __(
                'Übergeordnetes Produkt:',
                'jung-leben-core'
            ),
            'not_found' => __(
                'Keine Produkte gefunden.',
                'jung-leben-core'
            ),
            'not_found_in_trash' => __(
                'Keine Produkte im Papierkorb gefunden.',
                'jung-leben-core'
            ),
            'archives' => __(
                'Produktarchiv',
                'jung-leben-core'
            ),
            'attributes' => __(
                'Produkteigenschaften',
                'jung-leben-core'
            ),
            'insert_into_item' => __(
                'In Produkt einfügen',
                'jung-leben-core'
            ),
            'uploaded_to_this_item' => __(
                'Zu diesem Produkt hochgeladen',
                'jung-leben-core'
            ),
            'featured_image' => __(
                'Produktbild',
                'jung-leben-core'
            ),
            'set_featured_image' => __(
                'Produktbild festlegen',
                'jung-leben-core'
            ),
            'remove_featured_image' => __(
                'Produktbild entfernen',
                'jung-leben-core'
            ),
            'use_featured_image' => __(
                'Als Produktbild verwenden',
                'jung-leben-core'
            ),
            'filter_items_list' => __(
                'Produktliste filtern',
                'jung-leben-core'
            ),
            'items_list_navigation' => __(
                'Navigation der Produktliste',
                'jung-leben-core'
            ),
            'items_list' => __(
                'Produktliste',
                'jung-leben-core'
            ),
            'item_published' => __(
                'Produkt veröffentlicht.',
                'jung-leben-core'
            ),
            'item_updated' => __(
                'Produkt aktualisiert.',
                'jung-leben-core'
            ),
        ];

        register_post_type(
            self::POST_TYPE,
            [
                'labels' => $labels,

                /*
                 * Produkte sind öffentlich sichtbar.
                 */
                'public' => true,
                'publicly_queryable' => true,
                'exclude_from_search' => false,

                /*
                 * WordPress-Backend.
                 */
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,

                /*
                 * Gutenberg und WordPress REST API.
                 */
                'show_in_rest' => true,

                /*
                 * Produkte sind keine hierarchischen Seiten.
                 */
                'hierarchical' => false,

                /*
                 * Menüposition und Symbol.
                 */
                'menu_position' => 21,
                'menu_icon' => 'dashicons-products',

                /*
                 * Verfügbare Standardfelder.
                 */
                'supports' => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                    'revisions',
                    'page-attributes',
                ],

                /*
                 * Zugeordnete Taxonomien.
                 */
                'taxonomies' => [
                    self::TAXONOMY_CATEGORY,
                    self::TAXONOMY_BRAND,
                ],

                /*
                 * Produktarchiv:
                 * https://jung-leben.ch/produkte/
                 */
                'has_archive' => 'produkte',

                /*
                 * Einzelprodukt:
                 * https://jung-leben.ch/produkt/produktname/
                 */
                'rewrite' => [
                    'slug' => 'produkt',
                    'with_front' => false,
                    'feeds' => false,
                    'pages' => true,
                ],

                'query_var' => true,
                'can_export' => true,
                'delete_with_user' => false,

                /*
                 * Produkte verwenden die normalen
                 * WordPress-Berechtigungen für Beiträge.
                 */
                'capability_type' => 'post',
                'map_meta_cap' => true,
            ]
        );
    }

    /**
     * Produktkategorien registrieren.
     *
     * Produktkategorien sind hierarchisch aufgebaut.
     * Dadurch können später auch Unterkategorien
     * verwendet werden.
     */
    private static function register_product_category_taxonomy(): void
    {
        $labels = [
            'name' => __(
                'Produktkategorien',
                'jung-leben-core'
            ),
            'singular_name' => __(
                'Produktkategorie',
                'jung-leben-core'
            ),
            'menu_name' => __(
                'Kategorien',
                'jung-leben-core'
            ),
            'all_items' => __(
                'Alle Produktkategorien',
                'jung-leben-core'
            ),
            'edit_item' => __(
                'Produktkategorie bearbeiten',
                'jung-leben-core'
            ),
            'view_item' => __(
                'Produktkategorie ansehen',
                'jung-leben-core'
            ),
            'update_item' => __(
                'Produktkategorie aktualisieren',
                'jung-leben-core'
            ),
            'add_new_item' => __(
                'Neue Produktkategorie erstellen',
                'jung-leben-core'
            ),
            'new_item_name' => __(
                'Name der neuen Produktkategorie',
                'jung-leben-core'
            ),
            'parent_item' => __(
                'Übergeordnete Produktkategorie',
                'jung-leben-core'
            ),
            'parent_item_colon' => __(
                'Übergeordnete Produktkategorie:',
                'jung-leben-core'
            ),
            'search_items' => __(
                'Produktkategorien durchsuchen',
                'jung-leben-core'
            ),
            'not_found' => __(
                'Keine Produktkategorien gefunden.',
                'jung-leben-core'
            ),
            'back_to_items' => __(
                'Zurück zu den Produktkategorien',
                'jung-leben-core'
            ),
        ];

        register_taxonomy(
            self::TAXONOMY_CATEGORY,
            [
                self::POST_TYPE,
            ],
            [
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_in_rest' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,

                'rewrite' => [
                    'slug' => 'produkt-kategorie',
                    'with_front' => false,
                    'hierarchical' => true,
                ],

                'query_var' => true,
            ]
        );
    }

    /**
     * Marken-Taxonomie registrieren.
     *
     * Die Marken werden bewusst als kontrollierte
     * Auswahl mit Checkboxen dargestellt. So entstehen
     * nicht versehentlich mehrere Schreibweisen
     * derselben Marke.
     */
    private static function register_brand_taxonomy(): void
    {
        $labels = [
            'name' => __(
                'Marken',
                'jung-leben-core'
            ),
            'singular_name' => __(
                'Marke',
                'jung-leben-core'
            ),
            'menu_name' => __(
                'Marken',
                'jung-leben-core'
            ),
            'all_items' => __(
                'Alle Marken',
                'jung-leben-core'
            ),
            'edit_item' => __(
                'Marke bearbeiten',
                'jung-leben-core'
            ),
            'view_item' => __(
                'Marke ansehen',
                'jung-leben-core'
            ),
            'update_item' => __(
                'Marke aktualisieren',
                'jung-leben-core'
            ),
            'add_new_item' => __(
                'Neue Marke erstellen',
                'jung-leben-core'
            ),
            'new_item_name' => __(
                'Name der neuen Marke',
                'jung-leben-core'
            ),
            'parent_item' => __(
                'Übergeordnete Marke',
                'jung-leben-core'
            ),
            'parent_item_colon' => __(
                'Übergeordnete Marke:',
                'jung-leben-core'
            ),
            'search_items' => __(
                'Marken durchsuchen',
                'jung-leben-core'
            ),
            'not_found' => __(
                'Keine Marken gefunden.',
                'jung-leben-core'
            ),
            'back_to_items' => __(
                'Zurück zu den Marken',
                'jung-leben-core'
            ),
        ];

        register_taxonomy(
            self::TAXONOMY_BRAND,
            [
                self::POST_TYPE,
            ],
            [
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,

                /*
                 * Hierarchisch sorgt für eine kontrollierte
                 * Checkbox-Auswahl im Produkteditor.
                 */
                'hierarchical' => true,

                'show_ui' => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'show_in_rest' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true,

                'rewrite' => [
                    'slug' => 'marke',
                    'with_front' => false,
                    'hierarchical' => false,
                ],

                'query_var' => true,
            ]
        );
    }

    /**
     * Initiale Produktkategorien anlegen.
     *
     * Bereits vorhandene Kategorien werden nicht
     * verändert oder doppelt erstellt.
     */
    private static function insert_default_categories(): void
    {
        $categories = [
            'Energie & Leistungsfähigkeit',
            'Schlaf & Regeneration',
            'Stress & Balance',
            'Immunsystem',
            'Zellgesundheit & Longevity',
            'Verdauung',
            'Gelenke & Beweglichkeit',
            'Haut & gesundes Altern',
            'Vitamine & Mineralstoffe',
            'Pflanzenstoffe',
        ];

        foreach ($categories as $category) {
            if (
                term_exists(
                    $category,
                    self::TAXONOMY_CATEGORY
                )
            ) {
                continue;
            }

            wp_insert_term(
                $category,
                self::TAXONOMY_CATEGORY
            );
        }
    }

    /**
     * Kleine Upgrade-Routine.
     *
     * Dadurch werden neue Basisdaten auch dann
     * eingerichtet, wenn später nur eine neue
     * Plugin-Version hochgeladen wird.
     */
    public static function maybe_upgrade(): void
    {
        $installed_version = (string) get_option(
            'jung_leben_core_version',
            ''
        );

        if (
            $installed_version ===
            JUNG_LEBEN_CORE_VERSION
        ) {
            return;
        }

        self::register_content_types();
        self::insert_default_categories();

        update_option(
            'jung_leben_core_version',
            JUNG_LEBEN_CORE_VERSION
        );

        flush_rewrite_rules(false);
    }

    /**
     * Zusätzliche Spalte für das Produktbild.
     *
     * Produktkategorien und Marken werden von
     * WordPress automatisch als Spalten ergänzt.
     */
    public static function add_admin_columns(
        array $columns
    ): array {
        $new_columns = [];

        foreach ($columns as $key => $label) {
            $new_columns[$key] = $label;

            if ($key === 'cb') {
                $new_columns['jl_product_image'] = __(
                    'Bild',
                    'jung-leben-core'
                );
            }
        }

        return $new_columns;
    }

    /**
     * Inhalt der zusätzlichen Produktbild-Spalte.
     */
    public static function render_admin_columns(
        string $column,
        int $post_id
    ): void {
        if ($column !== 'jl_product_image') {
            return;
        }

        if (! has_post_thumbnail($post_id)) {
            echo '<span aria-hidden="true">—</span>';
            return;
        }

        echo get_the_post_thumbnail(
            $post_id,
            [
                60,
                60,
            ],
            [
                'style' => implode(
                    '',
                    [
                        'width:60px;',
                        'height:60px;',
                        'object-fit:cover;',
                        'border-radius:8px;',
                    ]
                ),
            ]
        );
    }

    /**
     * Plugin aktivieren.
     */
    public static function activate(): void
    {
        self::register_content_types();
        self::insert_default_categories();

        update_option(
            'jung_leben_core_version',
            JUNG_LEBEN_CORE_VERSION
        );

        flush_rewrite_rules();
    }

    /**
     * Plugin deaktivieren.
     *
     * Produkte und Taxonomien werden nicht gelöscht.
     */
    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}