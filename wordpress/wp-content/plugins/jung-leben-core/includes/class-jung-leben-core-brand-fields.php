<?php
/**
 * Zentrale Marken- und Partnerdaten.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Zusätzliche Felder und Frontend-Funktionen
 * für Jung-Leben-Produktmarken.
 */
final class Jung_Leben_Core_Brand_Fields
{
    /* =========================================================
       INITIALISIERUNG
       ========================================================= */

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'acf/init',
            [
                self::class,
                'register_fields',
            ]
        );


        add_filter(
            'manage_edit-jl_product_brand_columns',
            [
                self::class,
                'add_admin_columns',
            ]
        );


        add_filter(
            'manage_jl_product_brand_custom_column',
            [
                self::class,
                'render_admin_column',
            ],
            10,
            3
        );
    }


    /* =========================================================
       ACF-FELDER
       ========================================================= */

    /**
     * ACF-Felder für Marken registrieren.
     */
    public static function register_fields(): void
    {
        if (
            ! function_exists(
                'acf_add_local_field_group'
            )
        ) {
            return;
        }


        acf_add_local_field_group([
            'key' =>
                'group_jl_brand_details',

            'title' =>
                __(
                    'Jung Leben – Marke & Partner',
                    'jung-leben-core'
                ),

            'fields' => [

                /* =============================================
                   WEBSITE
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_website',

                    'label' =>
                        __(
                            'Offizielle Website',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_website',

                    'type' =>
                        'url',

                    'instructions' =>
                        __(
                            'Offizielle Website des Herstellers oder der Marke.',
                            'jung-leben-core'
                        ),

                    'required' =>
                        0,

                    'placeholder' =>
                        'https://',

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   LOGO
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_logo',

                    'label' =>
                        __(
                            'Logo',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_logo',

                    'type' =>
                        'image',

                    'instructions' =>
                        __(
                            'Offizielles Logo der Marke. Bevorzugt eine saubere PNG-, WebP- oder SVG-Datei verwenden.',
                            'jung-leben-core'
                        ),

                    'required' =>
                        0,

                    'return_format' =>
                        'id',

                    'preview_size' =>
                        'medium',

                    'library' =>
                        'all',

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   PARTNERSTATUS
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_partner_status',

                    'label' =>
                        __(
                            'Partnerstatus',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_partner_status',

                    'type' =>
                        'select',

                    'instructions' =>
                        __(
                            'Interne Einordnung der aktuellen Beziehung zu dieser Marke.',
                            'jung-leben-core'
                        ),

                    'choices' => [
                        'none' =>
                            __(
                                'Keine Partnerschaft',
                                'jung-leben-core'
                            ),

                        'candidate' =>
                            __(
                                'Potentieller Partner',
                                'jung-leben-core'
                            ),

                        'application' =>
                            __(
                                'Bewerbung läuft',
                                'jung-leben-core'
                            ),

                        'demo' =>
                            __(
                                'Demo / Partner-Vorschau',
                                'jung-leben-core'
                            ),

                        'active' =>
                            __(
                                'Aktiver Partner',
                                'jung-leben-core'
                            ),
                    ],

                    'default_value' =>
                        'none',

                    'allow_null' =>
                        0,

                    'multiple' =>
                        0,

                    'ui' =>
                        1,

                    'return_format' =>
                        'value',

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   AFFILIATE-PARTNER
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_affiliate_partner',

                    'label' =>
                        __(
                            'Affiliate-Partner',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_affiliate_partner',

                    'type' =>
                        'post_object',

                    'instructions' =>
                        __(
                            'Zentral verwalteten Affiliate-Partner dieser Marke auswählen. Produkte können später einen abweichenden Partner oder Einzelprodukt-Link erhalten.',
                            'jung-leben-core'
                        ),

                    'required' =>
                        0,

                    'post_type' => [
                        Jung_Leben_Core_Partners::POST_TYPE,
                    ],

                    'post_status' => [
                        'publish',
                    ],

                    'allow_null' =>
                        1,

                    'multiple' =>
                        0,

                    'return_format' =>
                        'id',

                    'ui' =>
                        1,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_brand_show_partner_offer',

                    'label' =>
                        __(
                            'Kundenvorteil der Partnerschaft anzeigen',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_show_partner_offer',

                    'type' =>
                        'true_false',

                    'instructions' =>
                        __(
                            'Wenn aktiviert, darf ein beim Affiliate-Partner hinterlegter öffentlicher Kundenvorteil auch im Zusammenhang mit dieser Marke angezeigt werden.',
                            'jung-leben-core'
                        ),

                    'default_value' =>
                        1,

                    'ui' =>
                        1,

                    'ui_on_text' =>
                        __(
                            'Ja',
                            'jung-leben-core'
                        ),

                    'ui_off_text' =>
                        __(
                            'Nein',
                            'jung-leben-core'
                        ),

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   FRONTEND-LINK
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_show_link',

                    'label' =>
                        __(
                            'Website im Frontend verlinken',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_show_link',

                    'type' =>
                        'true_false',

                    'instructions' =>
                        __(
                            'Wenn aktiviert, darf der Markenname auf Jung Leben mit der offiziellen Website verlinkt werden.',
                            'jung-leben-core'
                        ),

                    'default_value' =>
                        1,

                    'ui' =>
                        1,

                    'ui_on_text' =>
                        __(
                            'Ja',
                            'jung-leben-core'
                        ),

                    'ui_off_text' =>
                        __(
                            'Nein',
                            'jung-leben-core'
                        ),

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   LOGO IM FRONTEND
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_show_logo',

                    'label' =>
                        __(
                            'Logo im Frontend anzeigen',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_show_logo',

                    'type' =>
                        'true_false',

                    'instructions' =>
                        __(
                            'Das Logo wird nur verwendet, wenn eine Logo-Datei hinterlegt wurde.',
                            'jung-leben-core'
                        ),

                    'default_value' =>
                        0,

                    'ui' =>
                        1,

                    'ui_on_text' =>
                        __(
                            'Ja',
                            'jung-leben-core'
                        ),

                    'ui_off_text' =>
                        __(
                            'Nein',
                            'jung-leben-core'
                        ),

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   INTERNE NOTIZ
                   ============================================= */

                [
                    'key' =>
                        'field_jl_brand_internal_note',

                    'label' =>
                        __(
                            'Interne Partnernotiz',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_brand_internal_note',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Nur intern sichtbar. Zum Beispiel Ansprechpartner, Bewerbungsstand, Affiliate-Netzwerk oder Hinweise zur Logonutzung.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        4,

                    'new_lines' =>
                        '',

                    'wrapper' => [
                        'width' =>
                            '100',
                    ],
                ],
            ],


            'location' => [
                [
                    [
                        'param' =>
                            'taxonomy',

                        'operator' =>
                            '==',

                        'value' =>
                            Jung_Leben_Core_Products::TAXONOMY_BRAND,
                    ],
                ],
            ],


            'menu_order' =>
                0,

            'position' =>
                'normal',

            'style' =>
                'default',

            'label_placement' =>
                'top',

            'instruction_placement' =>
                'label',

            'active' =>
                true,
        ]);
    }


    /* =========================================================
       FRONTEND-DATEN
       ========================================================= */

    /**
     * Zentrale Frontend-Daten einer Marke laden.
     *
     * @return array{
     *     term: WP_Term,
     *     name: string,
     *     website: string,
     *     logo_id: int,
     *     show_link: bool,
     *     show_logo: bool,
     *     partner_status: string,
     *     partner_id: int,
     *     show_partner_offer: bool,
     *     partner: array<string, mixed>
     * }
     */
    public static function get_frontend_data(
        WP_Term $brand
    ): array {
        $acf_term_id =
            'term_'
            . $brand->term_id;


        $website = '';

        $logo_id = 0;

        $partner_status =
            'none';

        $partner_id = 0;


        if (
            function_exists(
                'get_field'
            )
        ) {
            $website =
                trim(
                    (string)
                    get_field(
                        'jl_brand_website',
                        $acf_term_id
                    )
                );


            $logo_value =
                get_field(
                    'jl_brand_logo',
                    $acf_term_id
                );


            if (
                is_array(
                    $logo_value
                )
                && isset(
                    $logo_value[
                        'ID'
                    ]
                )
            ) {
                $logo_id =
                    absint(
                        $logo_value[
                            'ID'
                        ]
                    );
            } else {
                $logo_id =
                    absint(
                        $logo_value
                    );
            }


            $partner_status =
                sanitize_key(
                    (string)
                    get_field(
                        'jl_brand_partner_status',
                        $acf_term_id
                    )
                );


            if (
                $partner_status === ''
            ) {
                $partner_status =
                    'none';
            }


            $partner_value =
                get_field(
                    'jl_brand_affiliate_partner',
                    $acf_term_id
                );


            if (
                $partner_value
                instanceof WP_Post
            ) {
                $partner_id =
                    absint(
                        $partner_value->ID
                    );
            } else {
                $partner_id =
                    absint(
                        $partner_value
                    );
            }
        }


        /* =====================================================
           LINK-SCHALTER
           ===================================================== */

        if (
            metadata_exists(
                'term',
                $brand->term_id,
                'jl_brand_show_link'
            )
        ) {
            $show_link =
                function_exists(
                    'get_field'
                )
                    ? (bool)
                        get_field(
                            'jl_brand_show_link',
                            $acf_term_id
                        )
                    : (bool)
                        get_term_meta(
                            $brand->term_id,
                            'jl_brand_show_link',
                            true
                        );
        } else {
            $show_link =
                true;
        }


        /* =====================================================
           LOGO-SCHALTER
           ===================================================== */

        if (
            metadata_exists(
                'term',
                $brand->term_id,
                'jl_brand_show_logo'
            )
        ) {
            $show_logo =
                function_exists(
                    'get_field'
                )
                    ? (bool)
                        get_field(
                            'jl_brand_show_logo',
                            $acf_term_id
                        )
                    : (bool)
                        get_term_meta(
                            $brand->term_id,
                            'jl_brand_show_logo',
                            true
                        );
        } else {
            $show_logo =
                false;
        }


        /* =====================================================
           PARTNERANGEBOT-SCHALTER
           ===================================================== */

        if (
            metadata_exists(
                'term',
                $brand->term_id,
                'jl_brand_show_partner_offer'
            )
        ) {
            $show_partner_offer =
                function_exists(
                    'get_field'
                )
                    ? (bool)
                        get_field(
                            'jl_brand_show_partner_offer',
                            $acf_term_id
                        )
                    : (bool)
                        get_term_meta(
                            $brand->term_id,
                            'jl_brand_show_partner_offer',
                            true
                        );
        } else {
            $show_partner_offer =
                true;
        }


        /* =====================================================
           PARTNERDATEN
           ===================================================== */

        $partner_data = [];


        if (
            $partner_id > 0
            && class_exists(
                Jung_Leben_Core_Partners::class
            )
        ) {
            $partner_data =
                Jung_Leben_Core_Partners::get_frontend_data(
                    $partner_id
                );
        }


        return [
            'term' =>
                $brand,

            'name' =>
                $brand->name,

            'website' =>
                esc_url_raw(
                    $website
                ),

            'logo_id' =>
                $logo_id,

            'show_link' =>
                $show_link,

            'show_logo' =>
                $show_logo,

            'partner_status' =>
                $partner_status,

            'partner_id' =>
                $partner_id,

            'show_partner_offer' =>
                $show_partner_offer,

            'partner' =>
                $partner_data,
        ];
    }


    /* =========================================================
       FRONTEND-AUSGABE
       ========================================================= */

    /**
     * Marke einheitlich im Frontend darstellen.
     */
    public static function render_frontend_brand(
        WP_Term $brand,
        string $context = 'default'
    ): string {
        $data =
            self::get_frontend_data(
                $brand
            );


        $context =
            sanitize_html_class(
                $context
            );


        if (
            $context === ''
        ) {
            $context =
                'default';
        }


        $classes = [
            'jl-brand',
            'jl-brand--'
                . $context,
        ];


        $logo_html = '';


        if (
            $data[
                'show_logo'
            ]
            && $data[
                'logo_id'
            ] > 0
        ) {
            $logo_html =
                wp_get_attachment_image(
                    $data[
                        'logo_id'
                    ],
                    'medium',
                    false,
                    [
                        'class' =>
                            'jl-brand__logo-image',

                        'alt' =>
                            '',

                        'loading' =>
                            'lazy',
                    ]
                );


            if (
                $logo_html !== ''
            ) {
                $classes[] =
                    'jl-brand--with-logo';
            }
        }


        $identity_html =
            '<span class="jl-brand__identity">'
            . $logo_html
            . '<span class="jl-brand__name">'
            . esc_html(
                $data[
                    'name'
                ]
            )
            . '</span>'
            . '</span>';


        $can_link =
            $data[
                'show_link'
            ]
            && $data[
                'website'
            ] !== '';


        if (
            $can_link
        ) {
            return
                sprintf(
                    '<a class="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer external" aria-label="%3$s">%4$s<span class="jl-brand__external" aria-hidden="true">↗</span></a>',
                    esc_attr(
                        implode(
                            ' ',
                            $classes
                        )
                    ),
                    esc_url(
                        $data[
                            'website'
                        ]
                    ),
                    esc_attr(
                        sprintf(
                            __(
                                'Website von %s öffnen',
                                'jung-leben-core'
                            ),
                            $data[
                                'name'
                            ]
                        )
                    ),
                    $identity_html
                );
        }


        return
            sprintf(
                '<span class="%1$s">%2$s</span>',
                esc_attr(
                    implode(
                        ' ',
                        $classes
                    )
                ),
                $identity_html
            );
    }


    /* =========================================================
       ADMIN-SPALTEN
       ========================================================= */

    /**
     * Zusätzliche Spalten bei Produkte → Marken.
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
                $key === 'name'
            ) {
                $new_columns[
                    'jl_brand_website'
                ] =
                    __(
                        'Website',
                        'jung-leben-core'
                    );


                $new_columns[
                    'jl_brand_partner_status'
                ] =
                    __(
                        'Partnerstatus',
                        'jung-leben-core'
                    );


                $new_columns[
                    'jl_brand_affiliate_partner'
                ] =
                    __(
                        'Affiliate-Partner',
                        'jung-leben-core'
                    );
            }
        }


        return
            $new_columns;
    }


    /* =========================================================
       ADMIN-SPALTEN INHALT
       ========================================================= */

    /**
     * Inhalt der zusätzlichen Admin-Spalten.
     */
    public static function render_admin_column(
        string $content,
        string $column_name,
        int $term_id
    ): string {
        if (
            ! function_exists(
                'get_field'
            )
        ) {
            return
                $content;
        }


        $acf_term_id =
            'term_'
            . $term_id;


        if (
            $column_name ===
            'jl_brand_website'
        ) {
            $website =
                trim(
                    (string)
                    get_field(
                        'jl_brand_website',
                        $acf_term_id
                    )
                );


            if (
                $website === ''
            ) {
                return
                    '–';
            }


            $host =
                wp_parse_url(
                    $website,
                    PHP_URL_HOST
                );


            $label =
                is_string(
                    $host
                )
                && $host !== ''
                    ? preg_replace(
                        '/^www\./i',
                        '',
                        $host
                    )
                    : $website;


            return
                sprintf(
                    '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
                    esc_url(
                        $website
                    ),
                    esc_html(
                        (string)
                        $label
                    )
                );
        }


        if (
            $column_name ===
            'jl_brand_partner_status'
        ) {
            $status =
                sanitize_key(
                    (string)
                    get_field(
                        'jl_brand_partner_status',
                        $acf_term_id
                    )
                );


            $labels = [
                'none' =>
                    __(
                        'Keine Partnerschaft',
                        'jung-leben-core'
                    ),

                'candidate' =>
                    __(
                        'Potentieller Partner',
                        'jung-leben-core'
                    ),

                'application' =>
                    __(
                        'Bewerbung läuft',
                        'jung-leben-core'
                    ),

                'demo' =>
                    __(
                        'Demo',
                        'jung-leben-core'
                    ),

                'active' =>
                    __(
                        'Aktiver Partner',
                        'jung-leben-core'
                    ),
            ];


            return
                isset(
                    $labels[
                        $status
                    ]
                )
                    ? esc_html(
                        $labels[
                            $status
                        ]
                    )
                    : esc_html(
                        $labels[
                            'none'
                        ]
                    );
        }


        if (
            $column_name ===
            'jl_brand_affiliate_partner'
        ) {
            $partner_value =
                get_field(
                    'jl_brand_affiliate_partner',
                    $acf_term_id
                );


            $partner_id =
                $partner_value
                instanceof WP_Post
                    ? absint(
                        $partner_value->ID
                    )
                    : absint(
                        $partner_value
                    );


            if (
                $partner_id <= 0
            ) {
                return
                    '–';
            }


            return
                esc_html(
                    get_the_title(
                        $partner_id
                    )
                );
        }


        return
            $content;
    }
}