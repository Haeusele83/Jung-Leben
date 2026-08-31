<?php
/**
 * Zentrale Verwaltung der Affiliate-Partner.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Affiliate-Partner von Jung Leben.
 */
final class Jung_Leben_Core_Partners
{
    /**
     * Interner Post Type.
     */
    public const POST_TYPE =
        'jl_partner';


    /* =========================================================
       INITIALISIERUNG
       ========================================================= */

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'init',
            [
                self::class,
                'register_post_type',
            ]
        );


        add_filter(
            'manage_'
            . self::POST_TYPE
            . '_posts_columns',
            [
                self::class,
                'add_admin_columns',
            ]
        );


        add_action(
            'manage_'
            . self::POST_TYPE
            . '_posts_custom_column',
            [
                self::class,
                'render_admin_column',
            ],
            10,
            2
        );
    }


    /* =========================================================
       POST TYPE
       ========================================================= */

    /**
     * Affiliate-Partner registrieren.
     *
     * Partner sind interne Verwaltungsobjekte.
     * Es gibt deshalb keine öffentliche Partner-Detailseite.
     */
    public static function register_post_type(): void
    {
        $labels = [
            'name' =>
                __(
                    'Affiliate-Partner',
                    'jung-leben-core'
                ),

            'singular_name' =>
                __(
                    'Affiliate-Partner',
                    'jung-leben-core'
                ),

            'menu_name' =>
                __(
                    'Affiliate-Partner',
                    'jung-leben-core'
                ),

            'name_admin_bar' =>
                __(
                    'Affiliate-Partner',
                    'jung-leben-core'
                ),

            'add_new' =>
                __(
                    'Neuer Partner',
                    'jung-leben-core'
                ),

            'add_new_item' =>
                __(
                    'Neuen Affiliate-Partner erstellen',
                    'jung-leben-core'
                ),

            'edit_item' =>
                __(
                    'Affiliate-Partner bearbeiten',
                    'jung-leben-core'
                ),

            'new_item' =>
                __(
                    'Neuer Affiliate-Partner',
                    'jung-leben-core'
                ),

            'all_items' =>
                __(
                    'Alle Partner',
                    'jung-leben-core'
                ),

            'search_items' =>
                __(
                    'Affiliate-Partner durchsuchen',
                    'jung-leben-core'
                ),

            'not_found' =>
                __(
                    'Keine Affiliate-Partner gefunden.',
                    'jung-leben-core'
                ),

            'not_found_in_trash' =>
                __(
                    'Keine Affiliate-Partner im Papierkorb gefunden.',
                    'jung-leben-core'
                ),
        ];


        register_post_type(
            self::POST_TYPE,
            [
                'labels' =>
                    $labels,

                'public' =>
                    false,

                'publicly_queryable' =>
                    false,

                'exclude_from_search' =>
                    true,

                'show_ui' =>
                    true,

                'show_in_menu' =>
                    true,

                'show_in_admin_bar' =>
                    true,

                'show_in_nav_menus' =>
                    false,

                'show_in_rest' =>
                    true,

                'menu_position' =>
                    22,

                'menu_icon' =>
                    'dashicons-admin-links',

                'supports' => [
                    'title',
                    'thumbnail',
                    'revisions',
                ],

                'hierarchical' =>
                    false,

                'has_archive' =>
                    false,

                'rewrite' =>
                    false,

                'query_var' =>
                    false,

                'can_export' =>
                    true,

                'capability_type' =>
                    'post',

                'map_meta_cap' =>
                    true,
            ]
        );
    }


    /* =========================================================
       FRONTEND-DATEN
       ========================================================= */

    /**
     * Zentrale Daten eines Partners laden.
     *
     * @return array{
     *     id: int,
     *     name: string,
     *     status: string,
     *     model: string,
     *     website: string,
     *     shop_url_de: string,
     *     shop_url_en: string,
     *     button_text: string,
     *     public_offer: bool,
     *     offer_title: string,
     *     discount_text: string,
     *     discount_code: string,
     *     public_note: string
     * }
     */
    public static function get_frontend_data(
        int $partner_id
    ): array {
        $partner_id =
            absint(
                $partner_id
            );


        $defaults = [
            'id' =>
                $partner_id,

            'name' =>
                '',

            'status' =>
                'planned',

            'model' =>
                'affiliate',

            'website' =>
                '',

            'shop_url_de' =>
                '',

            'shop_url_en' =>
                '',

            'button_text' =>
                __(
                    'Beim Partner ansehen',
                    'jung-leben-core'
                ),

            'public_offer' =>
                false,

            'offer_title' =>
                '',

            'discount_text' =>
                '',

            'discount_code' =>
                '',

            'public_note' =>
                '',
        ];


        if (
            $partner_id <= 0
        ) {
            return $defaults;
        }


        $partner =
            get_post(
                $partner_id
            );


        if (
            ! $partner
            instanceof WP_Post
            || $partner->post_type
            !== self::POST_TYPE
        ) {
            return $defaults;
        }


        $defaults[
            'name'
        ] =
            get_the_title(
                $partner_id
            );


        if (
            ! function_exists(
                'get_field'
            )
        ) {
            return $defaults;
        }


        $status =
            sanitize_key(
                (string)
                get_field(
                    'jl_partner_status',
                    $partner_id
                )
            );


        $model =
            sanitize_key(
                (string)
                get_field(
                    'jl_partner_model',
                    $partner_id
                )
            );


        $button_text =
            trim(
                (string)
                get_field(
                    'jl_partner_button_text',
                    $partner_id
                )
            );


        return [
            'id' =>
                $partner_id,

            'name' =>
                $defaults[
                    'name'
                ],

            'status' =>
                $status !== ''
                    ? $status
                    : 'planned',

            'model' =>
                $model !== ''
                    ? $model
                    : 'affiliate',

            'website' =>
                esc_url_raw(
                    trim(
                        (string)
                        get_field(
                            'jl_partner_website',
                            $partner_id
                        )
                    )
                ),

            'shop_url_de' =>
                esc_url_raw(
                    trim(
                        (string)
                        get_field(
                            'jl_partner_shop_url_de',
                            $partner_id
                        )
                    )
                ),

            'shop_url_en' =>
                esc_url_raw(
                    trim(
                        (string)
                        get_field(
                            'jl_partner_shop_url_en',
                            $partner_id
                        )
                    )
                ),

            'button_text' =>
                $button_text !== ''
                    ? $button_text
                    : __(
                        'Beim Partner ansehen',
                        'jung-leben-core'
                    ),

            'public_offer' =>
                (bool)
                get_field(
                    'jl_partner_public_offer',
                    $partner_id
                ),

            'offer_title' =>
                trim(
                    (string)
                    get_field(
                        'jl_partner_offer_title',
                        $partner_id
                    )
                ),

            'discount_text' =>
                trim(
                    (string)
                    get_field(
                        'jl_partner_discount_text',
                        $partner_id
                    )
                ),

            'discount_code' =>
                trim(
                    (string)
                    get_field(
                        'jl_partner_discount_code',
                        $partner_id
                    )
                ),

            'public_note' =>
                trim(
                    (string)
                    get_field(
                        'jl_partner_public_note',
                        $partner_id
                    )
                ),
        ];
    }


    /* =========================================================
       FALLBACK-LINK
       ========================================================= */

    /**
     * Allgemeinen Shop-Link eines Partners ermitteln.
     *
     * DE hat für Jung Leben Vorrang.
     */
    public static function get_default_shop_url(
        int $partner_id
    ): string {
        $data =
            self::get_frontend_data(
                $partner_id
            );


        if (
            $data[
                'shop_url_de'
            ] !== ''
        ) {
            return
                $data[
                    'shop_url_de'
                ];
        }


        return
            $data[
                'website'
            ];
    }


    /* =========================================================
       ADMIN-SPALTEN
       ========================================================= */

    /**
     * Backend-Spalten ergänzen.
     *
     * @param array<string, string> $columns
     *
     * @return array<string, string>
     */
    public static function add_admin_columns(
        array $columns
    ): array {
        $new_columns = [];


        foreach (
            $columns
            as $key => $label
        ) {
            $new_columns[
                $key
            ] =
                $label;


            if (
                $key === 'title'
            ) {
                $new_columns[
                    'jl_partner_status'
                ] =
                    __(
                        'Status',
                        'jung-leben-core'
                    );


                $new_columns[
                    'jl_partner_model'
                ] =
                    __(
                        'Modell',
                        'jung-leben-core'
                    );


                $new_columns[
                    'jl_partner_discount'
                ] =
                    __(
                        'Kundenvorteil',
                        'jung-leben-core'
                    );
            }
        }


        return
            $new_columns;
    }


    /**
     * Inhalt der Backend-Spalten.
     */
    public static function render_admin_column(
        string $column_name,
        int $post_id
    ): void {
        if (
            ! function_exists(
                'get_field'
            )
        ) {
            echo '–';
            return;
        }


        if (
            $column_name ===
            'jl_partner_status'
        ) {
            $status =
                sanitize_key(
                    (string)
                    get_field(
                        'jl_partner_status',
                        $post_id
                    )
                );


            $labels = [
                'planned' =>
                    __(
                        'Geplant',
                        'jung-leben-core'
                    ),

                'active' =>
                    __(
                        'Aktiv',
                        'jung-leben-core'
                    ),

                'paused' =>
                    __(
                        'Pausiert',
                        'jung-leben-core'
                    ),

                'ended' =>
                    __(
                        'Beendet',
                        'jung-leben-core'
                    ),
            ];


            echo esc_html(
                $labels[
                    $status
                ]
                ?? '–'
            );

            return;
        }


        if (
            $column_name ===
            'jl_partner_model'
        ) {
            $model =
                sanitize_key(
                    (string)
                    get_field(
                        'jl_partner_model',
                        $post_id
                    )
                );


            $labels = [
                'affiliate' =>
                    __(
                        'Affiliate-Link',
                        'jung-leben-core'
                    ),

                'discount' =>
                    __(
                        'Rabattcode',
                        'jung-leben-core'
                    ),

                'affiliate_discount' =>
                    __(
                        'Affiliate-Link + Rabattcode',
                        'jung-leben-core'
                    ),

                'manual' =>
                    __(
                        'Individuelle Vereinbarung',
                        'jung-leben-core'
                    ),
            ];


            echo esc_html(
                $labels[
                    $model
                ]
                ?? '–'
            );

            return;
        }


        if (
            $column_name ===
            'jl_partner_discount'
        ) {
            $discount =
                trim(
                    (string)
                    get_field(
                        'jl_partner_discount_text',
                        $post_id
                    )
                );


            $code =
                trim(
                    (string)
                    get_field(
                        'jl_partner_discount_code',
                        $post_id
                    )
                );


            if (
                $discount === ''
                && $code === ''
            ) {
                echo '–';
                return;
            }


            if (
                $discount !== ''
            ) {
                echo esc_html(
                    $discount
                );
            }


            if (
                $code !== ''
            ) {
                echo
                    '<br><code>'
                    . esc_html(
                        $code
                    )
                    . '</code>';
            }
        }
    }
}