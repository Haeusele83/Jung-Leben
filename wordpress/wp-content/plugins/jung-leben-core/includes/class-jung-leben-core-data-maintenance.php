<?php
/**
 * Datenpflege für importierte Jung-Leben-Inhalte.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Admin-Werkzeug zur vorsichtigen Bereinigung
 * und Ergänzung importierter Produktdaten.
 */
final class Jung_Leben_Core_Data_Maintenance
{
    /**
     * Slug der Admin-Seite.
     */
    private const PAGE_SLUG =
        'jung-leben-data-maintenance';


    /**
     * Action für die Kategorie-Bereinigung.
     */
    private const ACTION_CLEAN_CATEGORIES =
        'jung_leben_clean_product_categories';


    /**
     * Action für die Routine-Zuordnung.
     */
    private const ACTION_FILL_ROUTINES =
        'jung_leben_fill_product_routines';


    /**
     * Import-Kategorien, die nach erfolgreicher
     * Zuordnung vom Produkt entfernt werden.
     */
    private const LEGACY_CATEGORIES = [
        'Wohlbefinden',
        'Hygiene/Beauty',
    ];


    /* =========================================================
       INITIALISIERUNG
       ========================================================= */

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'admin_menu',
            [
                self::class,
                'register_admin_page',
            ]
        );


        add_action(
            'admin_post_'
                . self::ACTION_CLEAN_CATEGORIES,
            [
                self::class,
                'handle_category_cleanup',
            ]
        );


        add_action(
            'admin_post_'
                . self::ACTION_FILL_ROUTINES,
            [
                self::class,
                'handle_routine_fill',
            ]
        );
    }


    /* =========================================================
       ADMIN-SEITE
       ========================================================= */

    /**
     * Seite unter Werkzeuge registrieren.
     */
    public static function register_admin_page(): void
    {
        add_management_page(
            __(
                'Jung Leben Datenpflege',
                'jung-leben-core'
            ),
            __(
                'Jung Leben Datenpflege',
                'jung-leben-core'
            ),
            'manage_options',
            self::PAGE_SLUG,
            [
                self::class,
                'render_admin_page',
            ]
        );
    }


    /* =========================================================
       PRODUKT-KATEGORIEN
       ========================================================= */

    /**
     * Gewünschte Kategorien pro Produkt.
     *
     * @return array<string, array<int, string>>
     */
    private static function get_category_mapping(): array
    {
        return [

            'shilajit' => [
                'Energie & Leistungsfähigkeit',
                'Pflanzenstoffe',
            ],


            'hydroxiapatite zahnpasta' => [
                'Pflege & Hygiene',
            ],

            'hydroxyapatit zahnpasta' => [
                'Pflege & Hygiene',
            ],

            'hydroxyapatite zahnpasta' => [
                'Pflege & Hygiene',
            ],


            'deo roll on medcare' => [
                'Pflege & Hygiene',
            ],

            'deo roll on' => [
                'Pflege & Hygiene',
            ],


            'omega 3 6 9' => [
                'Zellgesundheit & Longevity',
            ],


            'ashwaganda' => [
                'Stress & Balance',
                'Pflanzenstoffe',
            ],

            'ashwagandha' => [
                'Stress & Balance',
                'Pflanzenstoffe',
            ],


            'weihrauchkapseln' => [
                'Gelenke & Beweglichkeit',
                'Pflanzenstoffe',
            ],

            'weihrauch kapseln' => [
                'Gelenke & Beweglichkeit',
                'Pflanzenstoffe',
            ],


            'turmeric 95' => [
                'Gelenke & Beweglichkeit',
                'Pflanzenstoffe',
            ],


            'msm curcuma' => [
                'Gelenke & Beweglichkeit',
                'Pflanzenstoffe',
            ],


            'resveratrol' => [
                'Zellgesundheit & Longevity',
                'Pflanzenstoffe',
            ],


            'opc' => [
                'Zellgesundheit & Longevity',
                'Pflanzenstoffe',
            ],


            'oreganool kapseln' => [
                'Immunsystem',
                'Pflanzenstoffe',
            ],

            'oreganooel kapseln' => [
                'Immunsystem',
                'Pflanzenstoffe',
            ],

            'oregano öl kapseln' => [
                'Immunsystem',
                'Pflanzenstoffe',
            ],

            'oreganoöl kapseln' => [
                'Immunsystem',
                'Pflanzenstoffe',
            ],


            'tongkat ali' => [
                'Energie & Leistungsfähigkeit',
                'Pflanzenstoffe',
            ],


            'coenzym q10' => [
                'Energie & Leistungsfähigkeit',
                'Zellgesundheit & Longevity',
            ],

            'coenzym 10' => [
                'Energie & Leistungsfähigkeit',
                'Zellgesundheit & Longevity',
            ],


            'zink' => [
                'Vitamine & Mineralstoffe',
                'Immunsystem',
            ],


            'astaxanthin' => [
                'Zellgesundheit & Longevity',
                'Pflanzenstoffe',
            ],


            'magnesium komplex' => [
                'Vitamine & Mineralstoffe',
            ],


            'quercetin' => [
                'Immunsystem',
                'Pflanzenstoffe',
            ],


            'guduchi' => [
                'Pflanzenstoffe',
            ],

            'guducchi' => [
                'Pflanzenstoffe',
            ],


            'bodylotion' => [
                'Haut & gesundes Altern',
                'Pflege & Hygiene',
            ],

            'body lotion' => [
                'Haut & gesundes Altern',
                'Pflege & Hygiene',
            ],


            'anti aging creme' => [
                'Haut & gesundes Altern',
                'Pflege & Hygiene',
            ],

            'anti ageing creme' => [
                'Haut & gesundes Altern',
                'Pflege & Hygiene',
            ],


            'face cleanser' => [
                'Pflege & Hygiene',
            ],


            'tallow face cream' => [
                'Haut & gesundes Altern',
                'Pflege & Hygiene',
            ],


            'l tryptophane' => [
                'Schlaf & Regeneration',
            ],

            'l tryptophan' => [
                'Schlaf & Regeneration',
            ],


            'nadh' => [
                'Energie & Leistungsfähigkeit',
                'Zellgesundheit & Longevity',
            ],
        ];
    }


    /* =========================================================
       ROUTINE-ZEITEN
       ========================================================= */

    /**
     * Vorgeschlagene Routine-Zeiten.
     *
     * Wichtig:
     *
     * Diese Werte stellen keine medizinische
     * Einnahmeempfehlung dar. Sie dienen ausschliesslich
     * der redaktionellen Einordnung innerhalb der
     * Jung-Leben-Tagesroutinen.
     *
     * @return array<string, array<int, string>>
     */
    private static function get_routine_mapping(): array
    {
        return [

            'shilajit' => [
                'morning',
            ],


            'hydroxiapatite zahnpasta' => [
                'morning',
                'evening',
            ],

            'hydroxyapatit zahnpasta' => [
                'morning',
                'evening',
            ],

            'hydroxyapatite zahnpasta' => [
                'morning',
                'evening',
            ],


            'deo roll on medcare' => [
                'morning',
            ],

            'deo roll on' => [
                'morning',
            ],


            'omega 3 6 9' => [
                'flexible',
            ],


            'ashwaganda' => [
                'evening',
            ],

            'ashwagandha' => [
                'evening',
            ],


            'weihrauchkapseln' => [
                'flexible',
            ],

            'weihrauch kapseln' => [
                'flexible',
            ],


            'turmeric 95' => [
                'flexible',
            ],


            'msm curcuma' => [
                'flexible',
            ],


            'resveratrol' => [
                'morning',
            ],


            'opc' => [
                'morning',
            ],


            'oreganool kapseln' => [
                'flexible',
            ],

            'oreganooel kapseln' => [
                'flexible',
            ],

            'oregano öl kapseln' => [
                'flexible',
            ],

            'oreganoöl kapseln' => [
                'flexible',
            ],


            'tongkat ali' => [
                'morning',
            ],


            'coenzym q10' => [
                'morning',
            ],

            'coenzym 10' => [
                'morning',
            ],


            'zink' => [
                'flexible',
            ],


            'astaxanthin' => [
                'flexible',
            ],


            'magnesium komplex' => [
                'evening',
            ],

            /**
             * Bestehendes Testprodukt.
             */
            'magnesium' => [
                'evening',
            ],


            'quercetin' => [
                'flexible',
            ],


            'guduchi' => [
                'flexible',
            ],

            'guducchi' => [
                'flexible',
            ],


            'bodylotion' => [
                'evening',
            ],

            'body lotion' => [
                'evening',
            ],


            'anti aging creme' => [
                'evening',
            ],

            'anti ageing creme' => [
                'evening',
            ],


            'face cleanser' => [
                'morning',
                'evening',
            ],


            'tallow face cream' => [
                'evening',
            ],


            'l tryptophane' => [
                'evening',
            ],

            'l tryptophan' => [
                'evening',
            ],


            'nadh' => [
                'morning',
            ],
        ];
    }


    /**
     * Anzeigenamen der Routine-Zeiten.
     *
     * @return array<string, string>
     */
    private static function get_routine_labels(): array
    {
        return [
            'morning' =>
                __(
                    'Morgens',
                    'jung-leben-core'
                ),

            'midday' =>
                __(
                    'Mittags',
                    'jung-leben-core'
                ),

            'evening' =>
                __(
                    'Abends',
                    'jung-leben-core'
                ),

            'flexible' =>
                __(
                    'Flexibel',
                    'jung-leben-core'
                ),
        ];
    }


    /* =========================================================
       NORMALISIERUNG
       ========================================================= */

    /**
     * Produktnamen für den Vergleich vereinheitlichen.
     */
    private static function normalize_product_name(
        string $value
    ): string {
        $value =
            wp_strip_all_tags(
                $value
            );


        $value =
            remove_accents(
                $value
            );


        $value =
            strtolower(
                $value
            );


        /**
         * Bindestriche und ähnliche Zeichen
         * wie Leerzeichen behandeln.
         */
        $value =
            preg_replace(
                '/[\-_–—\/]+/u',
                ' ',
                $value
            );


        if (
            ! is_string(
                $value
            )
        ) {
            return '';
        }


        /**
         * Sonderzeichen entfernen.
         */
        $value =
            preg_replace(
                '/[^a-z0-9\s]+/u',
                '',
                $value
            );


        if (
            ! is_string(
                $value
            )
        ) {
            return '';
        }


        /**
         * Mehrfache Leerzeichen reduzieren.
         */
        $value =
            preg_replace(
                '/\s+/u',
                ' ',
                $value
            );


        return
            trim(
                is_string(
                    $value
                )
                    ? $value
                    : ''
            );
    }


    /* =========================================================
       PRODUKTE LADEN
       ========================================================= */

    /**
     * Alle relevanten Jung-Leben-Produkte laden.
     *
     * @return array<int, WP_Post>
     */
    private static function get_products(): array
    {
        $products =
            get_posts([
                'post_type' =>
                    Jung_Leben_Core_Products::POST_TYPE,

                'post_status' => [
                    'publish',
                    'draft',
                    'pending',
                    'private',
                    'future',
                ],

                'posts_per_page' =>
                    -1,

                'orderby' =>
                    'title',

                'order' =>
                    'ASC',
            ]);


        return
            is_array(
                $products
            )
                ? $products
                : [];
    }


    /* =========================================================
       ZIELKATEGORIEN ERMITTELN
       ========================================================= */

    /**
     * Kategorien für ein Produkt anhand
     * des Produktnamens ermitteln.
     *
     * @return array<int, string>
     */
    private static function get_target_categories(
        WP_Post $product
    ): array {
        $mapping =
            self::get_category_mapping();


        $normalized_title =
            self::normalize_product_name(
                $product->post_title
            );


        if (
            $normalized_title === ''
        ) {
            return [];
        }


        foreach (
            $mapping
            as $source_name => $categories
        ) {
            $normalized_source =
                self::normalize_product_name(
                    $source_name
                );


            if (
                $normalized_source ===
                $normalized_title
            ) {
                return $categories;
            }
        }


        return [];
    }


    /* =========================================================
       ZIEL-ROUTINEN ERMITTELN
       ========================================================= */

    /**
     * Vorgesehene Routine-Zeiten anhand
     * des Produktnamens ermitteln.
     *
     * @return array<int, string>
     */
    private static function get_target_routines(
        WP_Post $product
    ): array {
        $mapping =
            self::get_routine_mapping();


        $normalized_title =
            self::normalize_product_name(
                $product->post_title
            );


        if (
            $normalized_title === ''
        ) {
            return [];
        }


        foreach (
            $mapping
            as $source_name => $routines
        ) {
            $normalized_source =
                self::normalize_product_name(
                    $source_name
                );


            if (
                $normalized_source ===
                $normalized_title
            ) {
                return $routines;
            }
        }


        return [];
    }


    /* =========================================================
       IMPORT-KATEGORIE PRÜFEN
       ========================================================= */

    /**
     * Prüfen, ob ein Produkt noch eine
     * der ursprünglichen Import-Kategorien besitzt.
     */
    private static function has_legacy_category(
        int $post_id
    ): bool {
        $terms =
            wp_get_post_terms(
                $post_id,
                Jung_Leben_Core_Products::TAXONOMY_CATEGORY
            );


        if (
            is_wp_error(
                $terms
            )
            || empty(
                $terms
            )
        ) {
            return false;
        }


        foreach (
            $terms
            as $term
        ) {
            if (
                ! $term
                instanceof WP_Term
            ) {
                continue;
            }


            if (
                in_array(
                    $term->name,
                    self::LEGACY_CATEGORIES,
                    true
                )
            ) {
                return true;
            }
        }


        return false;
    }


    /* =========================================================
       KATEGORIEN SICHERSTELLEN
       ========================================================= */

    /**
     * Kategorie holen oder bei Bedarf erstellen.
     *
     * @return int|WP_Error
     */
    private static function get_or_create_category(
        string $category_name
    ): int|WP_Error {
        $taxonomy =
            Jung_Leben_Core_Products::TAXONOMY_CATEGORY;


        $existing =
            term_exists(
                $category_name,
                $taxonomy
            );


        if (
            is_array(
                $existing
            )
            && isset(
                $existing[
                    'term_id'
                ]
            )
        ) {
            return
                (int)
                $existing[
                    'term_id'
                ];
        }


        if (
            is_int(
                $existing
            )
        ) {
            return $existing;
        }


        $created =
            wp_insert_term(
                $category_name,
                $taxonomy
            );


        if (
            is_wp_error(
                $created
            )
        ) {
            return $created;
        }


        return
            (int)
            $created[
                'term_id'
            ];
    }


    /* =========================================================
       KATEGORIEN EINES PRODUKTS BEREINIGEN
       ========================================================= */

    /**
     * Produktkategorien aktualisieren.
     *
     * Bestehende manuell ergänzte Kategorien
     * bleiben erhalten.
     *
     * Entfernt werden nur:
     *
     * - Wohlbefinden
     * - Hygiene/Beauty
     *
     * Danach werden die neuen Zielkategorien
     * hinzugefügt.
     *
     * @return true|WP_Error
     */
    private static function clean_product_categories(
        WP_Post $product,
        array $target_categories
    ): true|WP_Error {
        $taxonomy =
            Jung_Leben_Core_Products::TAXONOMY_CATEGORY;


        $current_terms =
            wp_get_post_terms(
                $product->ID,
                $taxonomy
            );


        if (
            is_wp_error(
                $current_terms
            )
        ) {
            return $current_terms;
        }


        $final_term_ids = [];


        /**
         * Bereits vorhandene Kategorien erhalten,
         * ausser den beiden Import-Kategorien.
         */
        foreach (
            $current_terms
            as $term
        ) {
            if (
                ! $term
                instanceof WP_Term
            ) {
                continue;
            }


            if (
                in_array(
                    $term->name,
                    self::LEGACY_CATEGORIES,
                    true
                )
            ) {
                continue;
            }


            $final_term_ids[] =
                (int)
                $term->term_id;
        }


        /**
         * Gewünschte neue Kategorien ergänzen.
         */
        foreach (
            $target_categories
            as $category_name
        ) {
            $term_id =
                self::get_or_create_category(
                    $category_name
                );


            if (
                is_wp_error(
                    $term_id
                )
            ) {
                return $term_id;
            }


            $final_term_ids[] =
                $term_id;
        }


        $final_term_ids =
            array_values(
                array_unique(
                    array_map(
                        'intval',
                        $final_term_ids
                    )
                )
            );


        $result =
            wp_set_object_terms(
                $product->ID,
                $final_term_ids,
                $taxonomy,
                false
            );


        if (
            is_wp_error(
                $result
            )
        ) {
            return $result;
        }


        return true;
    }


    /* =========================================================
       AKTUELLE ROUTINE-ZEITEN
       ========================================================= */

    /**
     * Bereits hinterlegte Routine-Zeiten eines
     * Produkts lesen.
     *
     * @return array<int, string>
     */
    private static function get_current_routines(
        int $post_id
    ): array {
        $value = [];


        if (
            function_exists(
                'get_field'
            )
        ) {
            $value =
                get_field(
                    'jl_product_routine_time',
                    $post_id
                );
        } else {
            $value =
                get_post_meta(
                    $post_id,
                    'jl_product_routine_time',
                    true
                );
        }


        if (
            is_string(
                $value
            )
            && $value !== ''
        ) {
            $value = [
                $value,
            ];
        }


        if (
            ! is_array(
                $value
            )
        ) {
            return [];
        }


        $valid_values =
            array_keys(
                self::get_routine_labels()
            );


        $result = [];


        foreach (
            $value
            as $routine
        ) {
            $routine =
                sanitize_key(
                    (string)
                    $routine
                );


            if (
                in_array(
                    $routine,
                    $valid_values,
                    true
                )
            ) {
                $result[] =
                    $routine;
            }
        }


        return
            array_values(
                array_unique(
                    $result
                )
            );
    }


    /* =========================================================
       ROUTINE-ZEITEN SPEICHERN
       ========================================================= */

    /**
     * Routine-Zeiten speichern.
     *
     * @return true|WP_Error
     */
    private static function update_product_routines(
        int $post_id,
        array $target_routines
    ): true|WP_Error {
        $valid_values =
            array_keys(
                self::get_routine_labels()
            );


        $target_routines =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            'sanitize_key',
                            $target_routines
                        ),
                        static function (
                            string $routine
                        ) use (
                            $valid_values
                        ): bool {
                            return
                                in_array(
                                    $routine,
                                    $valid_values,
                                    true
                                );
                        }
                    )
                )
            );


        if (
            empty(
                $target_routines
            )
        ) {
            return
                new WP_Error(
                    'jl_empty_routine',
                    __(
                        'Es wurden keine gültigen Routine-Zeiten übergeben.',
                        'jung-leben-core'
                    )
                );
        }


        /**
         * Wenn ACF vorhanden ist, speichern wir über
         * die Felddefinition.
         */
        if (
            function_exists(
                'update_field'
            )
        ) {
            $field_selector =
                'jl_product_routine_time';


            if (
                function_exists(
                    'get_field_object'
                )
            ) {
                $field_object =
                    get_field_object(
                        'jl_product_routine_time',
                        $post_id,
                        false,
                        false
                    );


                if (
                    is_array(
                        $field_object
                    )
                    && isset(
                        $field_object[
                            'key'
                        ]
                    )
                    && is_string(
                        $field_object[
                            'key'
                        ]
                    )
                    && $field_object[
                        'key'
                    ] !== ''
                ) {
                    $field_selector =
                        $field_object[
                            'key'
                        ];
                }
            }


            update_field(
                $field_selector,
                $target_routines,
                $post_id
            );
        } else {
            update_post_meta(
                $post_id,
                'jl_product_routine_time',
                $target_routines
            );
        }


        /**
         * Anschliessend überprüfen, ob die Werte
         * wirklich lesbar gespeichert wurden.
         */
        $saved =
            self::get_current_routines(
                $post_id
            );


        sort(
            $saved
        );

        $expected =
            $target_routines;

        sort(
            $expected
        );


        if (
            $saved !==
            $expected
        ) {
            return
                new WP_Error(
                    'jl_routine_save_failed',
                    __(
                        'Die Routine-Zeiten konnten nicht korrekt gespeichert werden.',
                        'jung-leben-core'
                    )
                );
        }


        return true;
    }


    /* =========================================================
       ROUTINE-LABELS FORMATIEREN
       ========================================================= */

    /**
     * Technische Routine-Werte für die
     * Admin-Anzeige übersetzen.
     */
    private static function format_routine_labels(
        array $routines
    ): string {
        if (
            empty(
                $routines
            )
        ) {
            return '–';
        }


        $labels =
            self::get_routine_labels();


        $result = [];


        foreach (
            $routines
            as $routine
        ) {
            if (
                isset(
                    $labels[
                        $routine
                    ]
                )
            ) {
                $result[] =
                    $labels[
                        $routine
                    ];
            }
        }


        return
            ! empty(
                $result
            )
                ? implode(
                    ', ',
                    $result
                )
                : '–';
    }


    /* =========================================================
       STATUS KATEGORIEN
       ========================================================= */

    /**
     * Produktstatus für die Kategorie-Vorschau.
     *
     * @return array{
     *     total:int,
     *     candidates:int,
     *     already_clean:int,
     *     unmatched:int,
     *     rows:array<int, array<string, mixed>>
     * }
     */
    private static function get_cleanup_status(): array
    {
        $products =
            self::get_products();


        $status = [
            'total' =>
                count(
                    $products
                ),

            'candidates' =>
                0,

            'already_clean' =>
                0,

            'unmatched' =>
                0,

            'rows' =>
                [],
        ];


        foreach (
            $products
            as $product
        ) {
            $target_categories =
                self::get_target_categories(
                    $product
                );


            $has_legacy =
                self::has_legacy_category(
                    $product->ID
                );


            $current_terms =
                wp_get_post_terms(
                    $product->ID,
                    Jung_Leben_Core_Products::TAXONOMY_CATEGORY,
                    [
                        'fields' =>
                            'names',
                    ]
                );


            if (
                is_wp_error(
                    $current_terms
                )
            ) {
                $current_terms = [];
            }


            $row_status =
                'clean';


            if (
                empty(
                    $target_categories
                )
            ) {
                $status[
                    'unmatched'
                ]++;

                $row_status =
                    'unmatched';
            } elseif (
                $has_legacy
            ) {
                $status[
                    'candidates'
                ]++;

                $row_status =
                    'candidate';
            } else {
                $status[
                    'already_clean'
                ]++;

                $row_status =
                    'clean';
            }


            $status[
                'rows'
            ][] = [
                'product' =>
                    $product,

                'current_categories' =>
                    $current_terms,

                'target_categories' =>
                    $target_categories,

                'status' =>
                    $row_status,
            ];
        }


        return $status;
    }


    /* =========================================================
       STATUS ROUTINEN
       ========================================================= */

    /**
     * Status der Routine-Zuordnungen zusammenstellen.
     *
     * @return array{
     *     total:int,
     *     candidates:int,
     *     already_filled:int,
     *     unmatched:int,
     *     rows:array<int, array<string, mixed>>
     * }
     */
    private static function get_routine_status(): array
    {
        $products =
            self::get_products();


        $status = [
            'total' =>
                count(
                    $products
                ),

            'candidates' =>
                0,

            'already_filled' =>
                0,

            'unmatched' =>
                0,

            'rows' =>
                [],
        ];


        foreach (
            $products
            as $product
        ) {
            $current_routines =
                self::get_current_routines(
                    $product->ID
                );


            $target_routines =
                self::get_target_routines(
                    $product
                );


            if (
                ! empty(
                    $current_routines
                )
            ) {
                $status[
                    'already_filled'
                ]++;

                $row_status =
                    'filled';
            } elseif (
                ! empty(
                    $target_routines
                )
            ) {
                $status[
                    'candidates'
                ]++;

                $row_status =
                    'candidate';
            } else {
                $status[
                    'unmatched'
                ]++;

                $row_status =
                    'unmatched';
            }


            $status[
                'rows'
            ][] = [
                'product' =>
                    $product,

                'current_routines' =>
                    $current_routines,

                'target_routines' =>
                    $target_routines,

                'status' =>
                    $row_status,
            ];
        }


        return $status;
    }


    /* =========================================================
       ADMIN-SEITE AUSGEBEN
       ========================================================= */

    /**
     * Datenpflege-Seite darstellen.
     */
    public static function render_admin_page(): void
    {
        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diese Seite.',
                    'jung-leben-core'
                )
            );
        }


        $category_status =
            self::get_cleanup_status();


        $routine_status =
            self::get_routine_status();


        $result =
            isset(
                $_GET[
                    'jl_result'
                ]
            )
                ? sanitize_key(
                    wp_unslash(
                        $_GET[
                            'jl_result'
                        ]
                    )
                )
                : '';


        $updated =
            isset(
                $_GET[
                    'jl_updated'
                ]
            )
                ? absint(
                    $_GET[
                        'jl_updated'
                    ]
                )
                : 0;
        ?>

        <div class="wrap">

            <h1>
                <?php
                esc_html_e(
                    'Jung Leben Datenpflege',
                    'jung-leben-core'
                );
                ?>
            </h1>


            <p style="max-width: 850px;">
                <?php
                esc_html_e(
                    'Dieses Werkzeug bereinigt und ergänzt importierte Produktdaten. Produkttexte, Produktnamen und Marken werden dabei nicht automatisch verändert.',
                    'jung-leben-core'
                );
                ?>
            </p>


            <?php
            if (
                $result === 'category_success'
                || $result === 'success'
            ) :
                ?>

                <div
                    class="
                        notice
                        notice-success
                        is-dismissible
                    "
                >
                    <p>
                        <?php
                        printf(
                            esc_html__(
                                '%d Produktkategorien wurden erfolgreich aktualisiert.',
                                'jung-leben-core'
                            ),
                            $updated
                        );
                        ?>
                    </p>
                </div>

            <?php elseif (
                $result === 'routine_success'
            ) : ?>

                <div
                    class="
                        notice
                        notice-success
                        is-dismissible
                    "
                >
                    <p>
                        <?php
                        printf(
                            esc_html__(
                                'Bei %d Produkten wurden die Routine-Zeiten erfolgreich ergänzt.',
                                'jung-leben-core'
                            ),
                            $updated
                        );
                        ?>
                    </p>
                </div>

            <?php elseif (
                $result === 'category_error'
                || $result === 'routine_error'
                || $result === 'error'
            ) : ?>

                <div
                    class="
                        notice
                        notice-error
                    "
                >
                    <p>
                        <?php
                        esc_html_e(
                            'Bei der Datenpflege ist mindestens ein Fehler aufgetreten. Bereits bestehende redaktionelle Werte wurden nicht absichtlich überschrieben.',
                            'jung-leben-core'
                        );
                        ?>
                    </p>
                </div>

            <?php endif; ?>


            <!-- =================================================
                 KATEGORIEN
                 ================================================= -->

            <hr
                style="
                    margin:
                        30px
                        0;
                "
            >


            <h2>
                <?php
                esc_html_e(
                    '1. Produktkategorien',
                    'jung-leben-core'
                );
                ?>
            </h2>


            <div
                style="
                    display:grid;
                    grid-template-columns:
                        repeat(
                            auto-fit,
                            minmax(
                                180px,
                                1fr
                            )
                        );
                    gap:16px;
                    max-width:900px;
                    margin:20px 0 28px;
                "
            >

                <?php
                self::render_stat_box(
                    __(
                        'Produkte',
                        'jung-leben-core'
                    ),
                    $category_status[
                        'total'
                    ]
                );


                self::render_stat_box(
                    __(
                        'Bereit zur Bereinigung',
                        'jung-leben-core'
                    ),
                    $category_status[
                        'candidates'
                    ]
                );


                self::render_stat_box(
                    __(
                        'Bereits bereinigt',
                        'jung-leben-core'
                    ),
                    $category_status[
                        'already_clean'
                    ]
                );


                self::render_stat_box(
                    __(
                        'Nicht automatisch erkannt',
                        'jung-leben-core'
                    ),
                    $category_status[
                        'unmatched'
                    ]
                );
                ?>

            </div>


            <div
                class="card"
                style="
                    max-width:900px;
                    margin-bottom:30px;
                    padding:22px 24px;
                "
            >

                <h2 style="margin-top:0;">
                    <?php
                    esc_html_e(
                        'Produktkategorien bereinigen',
                        'jung-leben-core'
                    );
                    ?>
                </h2>


                <p>
                    <?php
                    esc_html_e(
                        'Die Import-Kategorien «Wohlbefinden» und «Hygiene/Beauty» werden bei eindeutig erkannten Produkten entfernt und durch die vorgesehenen Jung-Leben-Kategorien ersetzt.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <p>
                    <strong>
                        <?php
                        esc_html_e(
                            'Bestehende zusätzliche Kategorien bleiben erhalten.',
                            'jung-leben-core'
                        );
                        ?>
                    </strong>
                </p>


                <?php
                if (
                    $category_status[
                        'candidates'
                    ] > 0
                ) :
                    ?>

                    <form
                        method="post"
                        action="<?php
                        echo esc_url(
                            admin_url(
                                'admin-post.php'
                            )
                        );
                        ?>"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="<?php
                            echo esc_attr(
                                self::ACTION_CLEAN_CATEGORIES
                            );
                            ?>"
                        >


                        <?php
                        wp_nonce_field(
                            self::ACTION_CLEAN_CATEGORIES
                        );
                        ?>


                        <?php
                        submit_button(
                            __(
                                'Kategorien jetzt bereinigen',
                                'jung-leben-core'
                            ),
                            'primary',
                            'submit',
                            false
                        );
                        ?>

                    </form>

                <?php else : ?>

                    <p>
                        <strong>
                            <?php
                            esc_html_e(
                                'Aktuell ist keine automatische Bereinigung notwendig.',
                                'jung-leben-core'
                            );
                            ?>
                        </strong>
                    </p>

                <?php endif; ?>

            </div>


            <h3>
                <?php
                esc_html_e(
                    'Vorschau der Kategorie-Zuordnung',
                    'jung-leben-core'
                );
                ?>
            </h3>


            <table
                class="
                    widefat
                    striped
                "
                style="
                    max-width:1200px;
                "
            >

                <thead>

                    <tr>

                        <th>
                            <?php
                            esc_html_e(
                                'Produkt',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                        <th>
                            <?php
                            esc_html_e(
                                'Aktuelle Kategorien',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                        <th>
                            <?php
                            esc_html_e(
                                'Vorgesehene Kategorien',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                        <th>
                            <?php
                            esc_html_e(
                                'Status',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php
                    foreach (
                        $category_status[
                            'rows'
                        ]
                        as $row
                    ) :
                        $product =
                            $row[
                                'product'
                            ];

                        $current =
                            $row[
                                'current_categories'
                            ];

                        $target =
                            $row[
                                'target_categories'
                            ];

                        $row_status =
                            $row[
                                'status'
                            ];
                        ?>

                        <tr>

                            <td>
                                <?php
                                self::render_product_name(
                                    $product
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo esc_html(
                                    ! empty(
                                        $current
                                    )
                                        ? implode(
                                            ', ',
                                            $current
                                        )
                                        : '–'
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo esc_html(
                                    ! empty(
                                        $target
                                    )
                                        ? implode(
                                            ', ',
                                            $target
                                        )
                                        : '–'
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                self::render_status(
                                    $row_status
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>


            <!-- =================================================
                 ROUTINE-ZEITEN
                 ================================================= -->

            <hr
                style="
                    margin:
                        55px
                        0
                        30px;
                "
            >


            <h2>
                <?php
                esc_html_e(
                    '2. Routine-Zeiten',
                    'jung-leben-core'
                );
                ?>
            </h2>


            <p
                style="
                    max-width:900px;
                "
            >
                <?php
                esc_html_e(
                    'Die Routine-Zeit beschreibt, wo ein Produkt redaktionell innerhalb der Jung-Leben-Tagesroutine eingeordnet wird. Sie ist keine medizinische Einnahmeempfehlung.',
                    'jung-leben-core'
                );
                ?>
            </p>


            <div
                style="
                    display:grid;
                    grid-template-columns:
                        repeat(
                            auto-fit,
                            minmax(
                                180px,
                                1fr
                            )
                        );
                    gap:16px;
                    max-width:900px;
                    margin:20px 0 28px;
                "
            >

                <?php
                self::render_stat_box(
                    __(
                        'Produkte',
                        'jung-leben-core'
                    ),
                    $routine_status[
                        'total'
                    ]
                );


                self::render_stat_box(
                    __(
                        'Bereit zur Ergänzung',
                        'jung-leben-core'
                    ),
                    $routine_status[
                        'candidates'
                    ]
                );


                self::render_stat_box(
                    __(
                        'Bereits gepflegt',
                        'jung-leben-core'
                    ),
                    $routine_status[
                        'already_filled'
                    ]
                );


                self::render_stat_box(
                    __(
                        'Nicht automatisch erkannt',
                        'jung-leben-core'
                    ),
                    $routine_status[
                        'unmatched'
                    ]
                );
                ?>

            </div>


            <div
                class="card"
                style="
                    max-width:900px;
                    margin-bottom:30px;
                    padding:22px 24px;
                "
            >

                <h2 style="margin-top:0;">
                    <?php
                    esc_html_e(
                        'Routine-Zeiten ergänzen',
                        'jung-leben-core'
                    );
                    ?>
                </h2>


                <p>
                    <?php
                    esc_html_e(
                        'Bei Produkten ohne bestehende Routine-Zuordnung werden die vorgesehenen Werte Morgens, Abends oder Flexibel ergänzt.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <p>
                    <strong>
                        <?php
                        esc_html_e(
                            'Bereits gepflegte Routine-Zeiten werden nicht verändert.',
                            'jung-leben-core'
                        );
                        ?>
                    </strong>
                </p>


                <?php
                if (
                    $routine_status[
                        'candidates'
                    ] > 0
                ) :
                    ?>

                    <form
                        method="post"
                        action="<?php
                        echo esc_url(
                            admin_url(
                                'admin-post.php'
                            )
                        );
                        ?>"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="<?php
                            echo esc_attr(
                                self::ACTION_FILL_ROUTINES
                            );
                            ?>"
                        >


                        <?php
                        wp_nonce_field(
                            self::ACTION_FILL_ROUTINES
                        );
                        ?>


                        <?php
                        submit_button(
                            __(
                                'Routine-Zeiten jetzt ergänzen',
                                'jung-leben-core'
                            ),
                            'primary',
                            'submit',
                            false
                        );
                        ?>

                    </form>

                <?php else : ?>

                    <p>
                        <strong>
                            <?php
                            esc_html_e(
                                'Aktuell sind keine automatischen Ergänzungen notwendig.',
                                'jung-leben-core'
                            );
                            ?>
                        </strong>
                    </p>

                <?php endif; ?>

            </div>


            <h3>
                <?php
                esc_html_e(
                    'Vorschau der Routine-Zuordnung',
                    'jung-leben-core'
                );
                ?>
            </h3>


            <table
                class="
                    widefat
                    striped
                "
                style="
                    max-width:1200px;
                "
            >

                <thead>

                    <tr>

                        <th>
                            <?php
                            esc_html_e(
                                'Produkt',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                        <th>
                            <?php
                            esc_html_e(
                                'Aktuelle Routine',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                        <th>
                            <?php
                            esc_html_e(
                                'Vorgesehene Routine',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                        <th>
                            <?php
                            esc_html_e(
                                'Status',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php
                    foreach (
                        $routine_status[
                            'rows'
                        ]
                        as $row
                    ) :
                        $product =
                            $row[
                                'product'
                            ];

                        $current =
                            $row[
                                'current_routines'
                            ];

                        $target =
                            $row[
                                'target_routines'
                            ];

                        $row_status =
                            $row[
                                'status'
                            ];
                        ?>

                        <tr>

                            <td>
                                <?php
                                self::render_product_name(
                                    $product
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo esc_html(
                                    self::format_routine_labels(
                                        $current
                                    )
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo esc_html(
                                    self::format_routine_labels(
                                        $target
                                    )
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                self::render_status(
                                    $row_status
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php
    }


    /* =========================================================
       PRODUKTNAME
       ========================================================= */

    /**
     * Produktname und Status ausgeben.
     */
    private static function render_product_name(
        WP_Post $product
    ): void {
        $status_object =
            get_post_status_object(
                $product->post_status
            );
        ?>

        <strong>
            <?php
            echo esc_html(
                $product->post_title
            );
            ?>
        </strong>

        <br>

        <small>
            <?php
            echo esc_html(
                $status_object
                instanceof stdClass
                    ? (string)
                        $status_object->label
                    : $product->post_status
            );
            ?>
        </small>

        <?php
    }


    /* =========================================================
       STATUS
       ========================================================= */

    /**
     * Status der Datenpflege ausgeben.
     */
    private static function render_status(
        string $status
    ): void {
        if (
            $status === 'candidate'
        ) :
            ?>

            <span
                style="
                    color:#8a6300;
                    font-weight:600;
                "
            >
                <?php
                esc_html_e(
                    'Bereit',
                    'jung-leben-core'
                );
                ?>
            </span>

        <?php elseif (
            $status === 'unmatched'
        ) : ?>

            <span
                style="
                    color:#b32d2e;
                    font-weight:600;
                "
            >
                <?php
                esc_html_e(
                    'Prüfen',
                    'jung-leben-core'
                );
                ?>
            </span>

        <?php else : ?>

            <span
                style="
                    color:#227447;
                    font-weight:600;
                "
            >
                <?php
                esc_html_e(
                    'OK',
                    'jung-leben-core'
                );
                ?>
            </span>

        <?php
        endif;
    }


    /* =========================================================
       STATISTIK-KARTE
       ========================================================= */

    /**
     * Kleine Statistik-Karte ausgeben.
     */
    private static function render_stat_box(
        string $label,
        int $value
    ): void {
        ?>

        <div
            style="
                background:#fff;
                border:1px solid #dcdcde;
                border-radius:6px;
                padding:20px;
            "
        >

            <strong
                style="
                    display:block;
                    font-size:30px;
                    line-height:1;
                    margin-bottom:8px;
                "
            >
                <?php
                echo esc_html(
                    (string)
                    $value
                );
                ?>
            </strong>


            <span
                style="
                    color:#646970;
                "
            >
                <?php
                echo esc_html(
                    $label
                );
                ?>
            </span>

        </div>

        <?php
    }


    /* =========================================================
       KATEGORIEN VERARBEITEN
       ========================================================= */

    /**
     * Kategorie-Bereinigung durchführen.
     */
    public static function handle_category_cleanup(): void
    {
        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diese Aktion.',
                    'jung-leben-core'
                )
            );
        }


        check_admin_referer(
            self::ACTION_CLEAN_CATEGORIES
        );


        $products =
            self::get_products();


        $updated = 0;

        $has_error = false;


        foreach (
            $products
            as $product
        ) {
            if (
                ! self::has_legacy_category(
                    $product->ID
                )
            ) {
                continue;
            }


            $target_categories =
                self::get_target_categories(
                    $product
                );


            if (
                empty(
                    $target_categories
                )
            ) {
                continue;
            }


            $result =
                self::clean_product_categories(
                    $product,
                    $target_categories
                );


            if (
                is_wp_error(
                    $result
                )
            ) {
                $has_error = true;

                continue;
            }


            $updated++;
        }


        self::redirect_after_action(
            $has_error
                ? 'category_error'
                : 'category_success',
            $updated
        );
    }


    /* =========================================================
       ROUTINEN VERARBEITEN
       ========================================================= */

    /**
     * Leere Routine-Zeiten ergänzen.
     */
    public static function handle_routine_fill(): void
    {
        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diese Aktion.',
                    'jung-leben-core'
                )
            );
        }


        check_admin_referer(
            self::ACTION_FILL_ROUTINES
        );


        $products =
            self::get_products();


        $updated = 0;

        $has_error = false;


        foreach (
            $products
            as $product
        ) {
            /**
             * Bereits gepflegte Produkte niemals
             * automatisch überschreiben.
             */
            $current_routines =
                self::get_current_routines(
                    $product->ID
                );


            if (
                ! empty(
                    $current_routines
                )
            ) {
                continue;
            }


            $target_routines =
                self::get_target_routines(
                    $product
                );


            /**
             * Nicht erkannte Produkte bleiben
             * unangetastet.
             */
            if (
                empty(
                    $target_routines
                )
            ) {
                continue;
            }


            $result =
                self::update_product_routines(
                    $product->ID,
                    $target_routines
                );


            if (
                is_wp_error(
                    $result
                )
            ) {
                $has_error = true;

                continue;
            }


            $updated++;
        }


        self::redirect_after_action(
            $has_error
                ? 'routine_error'
                : 'routine_success',
            $updated
        );
    }


    /* =========================================================
       REDIRECT
       ========================================================= */

    /**
     * Nach einer Datenpflege-Aktion zurück
     * zur Werkzeugseite leiten.
     */
    private static function redirect_after_action(
        string $result,
        int $updated
    ): never {
        $redirect_url =
            add_query_arg(
                [
                    'page' =>
                        self::PAGE_SLUG,

                    'jl_result' =>
                        $result,

                    'jl_updated' =>
                        $updated,
                ],
                admin_url(
                    'tools.php'
                )
            );


        wp_safe_redirect(
            $redirect_url
        );

        exit;
    }
}