<?php
/**
 * Einzelansicht eines Jung-Leben-Produkts.
 *
 * Die Darstellung passt sich automatisch an den
 * vorhandenen Datenumfang an.
 *
 * Kauf- und Partnerdaten werden zentral über
 * Jung Leben Core ermittelt.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/* =========================================================
   PRODUKTDETAIL-CSS
   ========================================================= */

$product_detail_css_path =
    get_template_directory()
    . '/assets/css/product-detail.css';


add_action(
    'wp_enqueue_scripts',
    static function () use (
        $product_detail_css_path
    ): void {
        if (
            ! file_exists(
                $product_detail_css_path
            )
        ) {
            return;
        }


        wp_enqueue_style(
            'jung-leben-product-detail',
            get_template_directory_uri()
                . '/assets/css/product-detail.css',
            [
                'jung-leben-site',
            ],
            (string)
            filemtime(
                $product_detail_css_path
            )
        );
    },
    30
);


get_header();


/* =========================================================
   HILFSFUNKTIONEN
   ========================================================= */

/**
 * ACF-Feld sicher laden.
 */
$get_product_field =
    static function (
        string $field_name,
        int $post_id,
        mixed $fallback = ''
    ): mixed {
        if (
            ! function_exists(
                'get_field'
            )
        ) {
            return $fallback;
        }


        $value =
            get_field(
                $field_name,
                $post_id
            );


        if (
            $value === null
            || $value === ''
        ) {
            return $fallback;
        }


        return $value;
    };


/**
 * Textfeld in Listenpunkte umwandeln.
 */
$text_to_items =
    static function (
        string $value
    ): array {
        $value =
            trim(
                wp_strip_all_tags(
                    $value
                )
            );


        if (
            $value === ''
        ) {
            return [];
        }


        $parts =
            preg_split(
                '/(?:\r\n|\r|\n)+/u',
                $value
            );


        if (
            ! is_array(
                $parts
            )
        ) {
            return [];
        }


        $items = [];


        foreach (
            $parts
            as $part
        ) {
            $part =
                preg_replace(
                    '/^[\s\-\–\—•●▪✓]+/u',
                    '',
                    trim(
                        $part
                    )
                );


            if (
                is_string(
                    $part
                )
                && $part !== ''
            ) {
                $items[] =
                    $part;
            }
        }


        return $items;
    };


/**
 * Kompaktes Produkt-Monogramm.
 */
$get_product_mark =
    static function (
        string $title
    ): string {
        $clean =
            preg_replace(
                '/[^\p{L}\p{N}\s]+/u',
                ' ',
                $title
            );


        if (
            ! is_string(
                $clean
            )
        ) {
            $clean =
                $title;
        }


        $parts =
            preg_split(
                '/\s+/u',
                trim(
                    $clean
                )
            );


        if (
            ! is_array(
                $parts
            )
            || empty(
                $parts
            )
        ) {
            return 'JL';
        }


        $parts =
            array_values(
                array_filter(
                    $parts,
                    static function (
                        string $part
                    ): bool {
                        return
                            trim(
                                $part
                            ) !== '';
                    }
                )
            );


        if (
            empty(
                $parts
            )
        ) {
            return 'JL';
        }


        if (
            count(
                $parts
            ) >= 2
        ) {
            return
                mb_strtoupper(
                    mb_substr(
                        $parts[0],
                        0,
                        1
                    )
                    .
                    mb_substr(
                        $parts[1],
                        0,
                        1
                    )
                );
        }


        return
            mb_strtoupper(
                mb_substr(
                    $parts[0],
                    0,
                    2
                )
            );
    };



/**
 * Anzeigenamen für eine importierte Alternative bestimmen.
 *
 * In der Excel-Datei kann im sichtbaren URL-Feld entweder
 * ein Produktname oder bereits eine URL stehen.
 */
$get_alternative_title =
    static function (
        string $brand,
        string $reference
    ): string {
        $brand =
            trim(
                wp_strip_all_tags(
                    $brand
                )
            );


        $reference =
            trim(
                wp_strip_all_tags(
                    $reference
                )
            );


        /**
         * Ist der sichtbare Zellinhalt keine URL,
         * verwenden wir ihn als Produktbezeichnung.
         */
        if (
            $reference !== ''
            && ! preg_match(
                '#^https?://#i',
                $reference
            )
        ) {
            return $reference;
        }


        if (
            $brand !== ''
        ) {
            return
                sprintf(
                    __(
                        'Alternative von %s',
                        'jung-leben'
                    ),
                    $brand
                );
        }


        return
            __(
                'Weitere Produktoption',
                'jung-leben'
            );
    };


/* =========================================================
   LABELS
   ========================================================= */

$recommendation_labels = [

    'interesting' =>
        __(
            'Interessante Option',
            'jung-leben'
        ),

    'recommended' =>
        __(
            'Empfohlen',
            'jung-leben'
        ),

    'favorite' =>
        __(
            'Persönlicher Favorit',
            'jung-leben'
        ),
];


$routine_labels = [

    'morning' =>
        __(
            'Morgens',
            'jung-leben'
        ),

    'midday' =>
        __(
            'Mittags',
            'jung-leben'
        ),

    'evening' =>
        __(
            'Abends',
            'jung-leben'
        ),

    'flexible' =>
        __(
            'Flexibel',
            'jung-leben'
        ),
];


/* =========================================================
   EMPFEHLUNGEN-SEITE
   ========================================================= */

$recommendations_page =
    get_page_by_path(
        'empfehlungen'
    );


$recommendations_url =
    $recommendations_page
    instanceof WP_Post
        ? get_permalink(
            $recommendations_page
        )
        : home_url(
            '/empfehlungen/'
        );


if (
    ! is_string(
        $recommendations_url
    )
) {
    $recommendations_url =
        home_url(
            '/empfehlungen/'
        );
}


/* =========================================================
   LOOP
   ========================================================= */

if (
    have_posts()
) :

    while (
        have_posts()
    ) :

        the_post();


        $product_id =
            get_the_ID();


        /* =====================================================
           GRUNDDATEN
           ===================================================== */

        $has_image =
            has_post_thumbnail(
                $product_id
            );


        $product_mark =
            $get_product_mark(
                get_the_title()
            );


        $recommendation_status =
            sanitize_key(
                (string)
                $get_product_field(
                    'jl_product_recommendation_status',
                    $product_id,
                    'neutral'
                )
            );


        $personally_tested =
            (bool)
            $get_product_field(
                'jl_product_personally_tested',
                $product_id,
                false
            );


        $featured =
            (bool)
            $get_product_field(
                'jl_product_featured',
                $product_id,
                false
            );


        /* =====================================================
           REDAKTIONELLE INFORMATIONEN
           ===================================================== */

        $purpose =
            trim(
                (string)
                $get_product_field(
                    'jl_product_purpose',
                    $product_id,
                    ''
                )
            );


        $personal_experience =
            trim(
                (string)
                $get_product_field(
                    'jl_product_personal_experience',
                    $product_id,
                    ''
                )
            );


        $benefits =
            trim(
                (string)
                $get_product_field(
                    'jl_product_benefits',
                    $product_id,
                    ''
                )
            );


        $limitations =
            trim(
                (string)
                $get_product_field(
                    'jl_product_limitations',
                    $product_id,
                    ''
                )
            );


        /* =====================================================
           ROUTINEN
           ===================================================== */

        $routine_times =
            $get_product_field(
                'jl_product_routine_time',
                $product_id,
                []
            );


        if (
            is_string(
                $routine_times
            )
        ) {
            $routine_times = [
                $routine_times,
            ];
        }


        if (
            ! is_array(
                $routine_times
            )
        ) {
            $routine_times = [];
        }


        $routine_names = [];


        foreach (
            $routine_times
            as $routine_time
        ) {
            $routine_time =
                sanitize_key(
                    (string)
                    $routine_time
                );


            if (
                isset(
                    $routine_labels[
                        $routine_time
                    ]
                )
            ) {
                $routine_names[] =
                    $routine_labels[
                        $routine_time
                    ];
            }
        }


        /* =====================================================
           MARKE
           ===================================================== */

        $brands =
            get_the_terms(
                $product_id,
                'jl_product_brand'
            );


        if (
            ! is_array(
                $brands
            )
            || is_wp_error(
                $brands
            )
        ) {
            $brands = [];
        }


        $primary_brand =
            ! empty(
                $brands
            )
            && $brands[0]
            instanceof WP_Term
                ? $brands[0]
                : null;


        $brand_markup = '';


        if (
            $primary_brand
            instanceof WP_Term
        ) {
            if (
                class_exists(
                    'Jung_Leben_Core_Brand_Fields'
                )
            ) {
                $brand_markup =
                    Jung_Leben_Core_Brand_Fields
                        ::render_frontend_brand(
                            $primary_brand,
                            'product'
                        );
            } else {
                $brand_markup =
                    '<span class="jl-brand jl-brand--product">'
                    . '<span class="jl-brand__name">'
                    . esc_html(
                        $primary_brand->name
                    )
                    . '</span>'
                    . '</span>';
            }
        }


        /* =====================================================
           KAUF- UND PARTNERDATEN
           ===================================================== */

        $purchase_data = [];


        if (
            class_exists(
                'Jung_Leben_Core_Product_Fields'
            )
            && method_exists(
                'Jung_Leben_Core_Product_Fields',
                'get_purchase_data'
            )
        ) {
            $purchase_data =
                Jung_Leben_Core_Product_Fields
                    ::get_purchase_data(
                        $product_id
                    );
        }


        $purchase_url =
            isset(
                $purchase_data[
                    'url'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'url'
                    ]
                )
                : '';


        $button_text =
            isset(
                $purchase_data[
                    'button_text'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'button_text'
                    ]
                )
                : '';


        if (
            $button_text === ''
        ) {
            $button_text =
                __(
                    'Produkt ansehen',
                    'jung-leben'
                );
        }


        $link_new_tab =
            isset(
                $purchase_data[
                    'new_tab'
                ]
            )
                ? (bool)
                    $purchase_data[
                        'new_tab'
                    ]
                : true;


        $purchase_rel =
            isset(
                $purchase_data[
                    'rel'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'rel'
                    ]
                )
                : 'noopener noreferrer external';


        $price_display =
            isset(
                $purchase_data[
                    'price_display'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'price_display'
                    ]
                )
                : '';


        $partner_data =
            isset(
                $purchase_data[
                    'partner'
                ]
            )
            && is_array(
                $purchase_data[
                    'partner'
                ]
            )
                ? $purchase_data[
                    'partner'
                ]
                : [];


        $partner_name =
            isset(
                $partner_data[
                    'name'
                ]
            )
                ? trim(
                    (string)
                    $partner_data[
                        'name'
                    ]
                )
                : '';


        $show_offer =
            isset(
                $purchase_data[
                    'show_offer'
                ]
            )
            && (bool)
                $purchase_data[
                    'show_offer'
                ];


        $discount_text =
            isset(
                $purchase_data[
                    'discount_text'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'discount_text'
                    ]
                )
                : '';


        $discount_code =
            isset(
                $purchase_data[
                    'discount_code'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'discount_code'
                    ]
                )
                : '';


        $public_note =
            isset(
                $purchase_data[
                    'public_note'
                ]
            )
                ? trim(
                    (string)
                    $purchase_data[
                        'public_note'
                    ]
                )
                : '';


        /**
         * Die Kaufkarte erscheint nur, wenn tatsächlich
         * kaufrelevante Informationen vorhanden sind.
         */
        $show_purchase_card =
            $purchase_url !== ''
            || $price_display !== ''
            || (
                $show_offer
                && (
                    $discount_text !== ''
                    || $discount_code !== ''
                    || $public_note !== ''
                )
            );


        /* =====================================================
           TRANSPARENZ
           ===================================================== */

        $affiliate_notice =
            trim(
                (string)
                $get_product_field(
                    'jl_product_affiliate_notice',
                    $product_id,
                    ''
                )
            );


        $health_notice =
            trim(
                (string)
                $get_product_field(
                    'jl_product_health_notice',
                    $product_id,
                    ''
                )
            );


        /* =====================================================
           KATEGORIEN
           ===================================================== */

        $categories =
            get_the_terms(
                $product_id,
                'jl_product_category'
            );


        if (
            ! is_array(
                $categories
            )
            || is_wp_error(
                $categories
            )
        ) {
            $categories = [];
        }



        /* =====================================================
           IMPORTIERTE ALTERNATIVEN
           ===================================================== */

        /**
         * Alternative Produkte werden vom Excel-Import
         * als interne Produkt-Metadaten gespeichert.
         *
         * Bemerkungsfelder werden hier bewusst NICHT
         * öffentlich ausgegeben. Sie können interne
         * Partner- oder Prüfhinweise enthalten.
         */
        $alternatives = [];


        foreach (
            [
                1,
                2,
            ]
            as $alternative_index
        ) {
            $alternative_brand =
                trim(
                    (string)
                    get_post_meta(
                        $product_id,
                        '_jl_source_alternative_'
                        . $alternative_index
                        . '_brand',
                        true
                    )
                );


            $alternative_reference =
                trim(
                    (string)
                    get_post_meta(
                        $product_id,
                        '_jl_source_alternative_'
                        . $alternative_index
                        . '_reference',
                        true
                    )
                );


            $alternative_url =
                trim(
                    (string)
                    get_post_meta(
                        $product_id,
                        '_jl_source_alternative_'
                        . $alternative_index
                        . '_url',
                        true
                    )
                );


            /**
             * Nur gültige Webadressen als Link verwenden.
             */
            if (
                $alternative_url !== ''
                && ! wp_http_validate_url(
                    $alternative_url
                )
            ) {
                $alternative_url = '';
            }


            if (
                $alternative_brand === ''
                && $alternative_reference === ''
                && $alternative_url === ''
            ) {
                continue;
            }


            $alternatives[] = [

                'index' =>
                    $alternative_index,

                'brand' =>
                    $alternative_brand,

                'title' =>
                    $get_alternative_title(
                        $alternative_brand,
                        $alternative_reference
                    ),

                'url' =>
                    $alternative_url,
            ];
        }


        $has_alternatives =
            ! empty(
                $alternatives
            );


        /* =====================================================
           LISTEN
           ===================================================== */

        $benefit_items =
            $text_to_items(
                $benefits
            );


        $limitation_items =
            $text_to_items(
                $limitations
            );


        /* =====================================================
           LEAD
           ===================================================== */

        $excerpt =
            trim(
                (string)
                get_the_excerpt()
            );


        $lead =
            $excerpt !== ''
                ? $excerpt
                : $purpose;


        /* =====================================================
           HAUPTINHALT
           ===================================================== */

        $editor_content =
            trim(
                (string)
                get_post_field(
                    'post_content',
                    $product_id
                )
            );


        /**
         * Die Relations-Klasse hängt verknüpfte Beiträge
         * normalerweise direkt an "the_content".
         *
         * Auf der Produktdetailseite lösen wir diese Ausgabe
         * bewusst vom eigentlichen Produkttext, damit die
         * Reihenfolge sauber bleibt:
         *
         * Produkttext
         * → Alternativen
         * → Hintergrund / persönliche Erfahrung
         */
        $relations_callback = [
            'Jung_Leben_Core_Content_Relations',
            'append_relations',
        ];


        $relations_filter_removed =
            false;


        if (
            class_exists(
                'Jung_Leben_Core_Content_Relations'
            )
            && has_filter(
                'the_content',
                $relations_callback
            ) !== false
        ) {
            remove_filter(
                'the_content',
                $relations_callback,
                30
            );


            $relations_filter_removed =
                true;
        }


        $rendered_content =
            apply_filters(
                'the_content',
                $editor_content
            );


        if (
            $relations_filter_removed
        ) {
            add_filter(
                'the_content',
                $relations_callback,
                30
            );
        }


        $has_rendered_content =
            trim(
                wp_strip_all_tags(
                    $rendered_content
                )
            ) !== '';


        /**
         * Verknüpfte Beiträge anschliessend separat
         * aufbauen, damit sie hinter den Alternativen
         * platziert werden können.
         */
        $relation_content = '';


        if (
            class_exists(
                'Jung_Leben_Core_Content_Relations'
            )
            && method_exists(
                'Jung_Leben_Core_Content_Relations',
                'append_relations'
            )
        ) {
            $relation_content =
                Jung_Leben_Core_Content_Relations
                    ::append_relations(
                        ''
                    );
        }


        $has_relation_content =
            trim(
                wp_strip_all_tags(
                    $relation_content
                )
            ) !== '';


        /* =====================================================
           DETAILBEREICHE
           ===================================================== */

        $has_purpose_section =
            $purpose !== ''
            && trim(
                $purpose
            )
            !== trim(
                $lead
            );


        $has_experience_section =
            $personal_experience !== '';


        $has_pros_cons =
            ! empty(
                $benefit_items
            )
            || ! empty(
                $limitation_items
            );


        $has_main_details =
            $has_purpose_section
            || $has_rendered_content
            || $has_alternatives
            || $has_relation_content
            || $has_experience_section
            || $has_pros_cons;


        $show_detail_area =
            $has_main_details;


        $single_classes = [
            'product-single',
        ];


        if (
            ! $has_main_details
        ) {
            $single_classes[] =
                'product-single--compact';
        }


        if (
            ! $has_image
        ) {
            $single_classes[] =
                'product-single--no-image';
        }
        ?>

        <main
            id="main-content"
            class="<?php
            echo esc_attr(
                implode(
                    ' ',
                    $single_classes
                )
            );
            ?>"
        >

            <!-- =================================================
                 BREADCRUMB
                 ================================================= -->

            <div class="container">

                <nav
                    class="product-breadcrumb"
                    aria-label="<?php
                    esc_attr_e(
                        'Breadcrumb',
                        'jung-leben'
                    );
                    ?>"
                >

                    <a
                        href="<?php
                        echo esc_url(
                            $recommendations_url
                        );
                        ?>"
                    >
                        <?php
                        esc_html_e(
                            'Empfehlungen',
                            'jung-leben'
                        );
                        ?>
                    </a>


                    <span aria-hidden="true">
                        /
                    </span>


                    <span>
                        <?php
                        the_title();
                        ?>
                    </span>

                </nav>

            </div>


            <!-- =================================================
                 HERO
                 ================================================= -->

            <section
                class="
                    product-hero
                    <?php
                    echo $has_image
                        ? 'product-hero--with-image'
                        : 'product-hero--no-image';
                    ?>
                "
            >

                <div class="container product-hero__grid">

                    <!-- =========================================
                         PRODUKTBILD / MONOGRAMM
                         ========================================= -->

                    <div class="product-hero__media">

                        <?php
                        if (
                            $has_image
                        ) :
                            ?>

                            <?php
                            echo wp_kses_post(
                                get_the_post_thumbnail(
                                    $product_id,
                                    'large',
                                    [
                                        'class' =>
                                            'product-hero__image',

                                        'loading' =>
                                            'eager',
                                    ]
                                )
                            );
                            ?>

                        <?php else : ?>

                            <div
                                class="product-detail-placeholder"
                                aria-hidden="true"
                            >

                                <span class="product-detail-placeholder__mark">
                                    <?php
                                    echo esc_html(
                                        $product_mark
                                    );
                                    ?>
                                </span>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- =========================================
                         PRODUKTINFO
                         ========================================= -->

                    <div class="product-hero__content">

                        <!-- Badges -->
                        <?php
                        if (
                            $featured
                            || isset(
                                $recommendation_labels[
                                    $recommendation_status
                                ]
                            )
                            || $personally_tested
                        ) :
                            ?>

                            <div class="product-hero__meta">

                                <?php
                                if (
                                    $featured
                                ) :
                                    ?>

                                    <span
                                        class="
                                            product-badge
                                            product-badge--highlight
                                        "
                                    >
                                        <?php
                                        esc_html_e(
                                            'Highlight',
                                            'jung-leben'
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>


                                <?php
                                if (
                                    isset(
                                        $recommendation_labels[
                                            $recommendation_status
                                        ]
                                    )
                                ) :
                                    ?>

                                    <span
                                        class="
                                            product-badge
                                            product-badge--<?php
                                            echo esc_attr(
                                                $recommendation_status
                                            );
                                            ?>
                                        "
                                    >
                                        <?php
                                        echo esc_html(
                                            $recommendation_labels[
                                                $recommendation_status
                                            ]
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>


                                <?php
                                if (
                                    $personally_tested
                                ) :
                                    ?>

                                    <span
                                        class="
                                            product-badge
                                            product-badge--tested
                                        "
                                    >
                                        <?php
                                        esc_html_e(
                                            'Persönlich getestet',
                                            'jung-leben'
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>


                        <!-- Marke -->
                        <?php
                        if (
                            $brand_markup !== ''
                        ) :
                            ?>

                            <div class="product-hero__brand">

                                <?php
                                echo wp_kses_post(
                                    $brand_markup
                                );
                                ?>

                            </div>

                        <?php endif; ?>


                        <!-- Titel -->
                        <h1 class="product-hero__title">
                            <?php
                            the_title();
                            ?>
                        </h1>


                        <!-- Lead -->
                        <?php
                        if (
                            $lead !== ''
                        ) :
                            ?>

                            <p class="product-hero__excerpt">
                                <?php
                                echo esc_html(
                                    $lead
                                );
                                ?>
                            </p>

                        <?php endif; ?>


                        <!-- Schnellinformationen -->
                        <?php
                        if (
                            ! empty(
                                $categories
                            )
                            || ! empty(
                                $routine_names
                            )
                        ) :
                            ?>

                            <div class="product-hero__quickfacts">

                                <?php
                                foreach (
                                    $categories
                                    as $category
                                ) :
                                    ?>

                                    <span>
                                        <?php
                                        echo esc_html(
                                            $category->name
                                        );
                                        ?>
                                    </span>

                                <?php endforeach; ?>


                                <?php
                                foreach (
                                    $routine_names
                                    as $routine_name
                                ) :
                                    ?>

                                    <span
                                        class="product-hero__quickfact--routine"
                                    >
                                        <?php
                                        echo esc_html(
                                            $routine_name
                                        );
                                        ?>
                                    </span>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>


                        <!-- =====================================
                             BEZUGSINFORMATION
                             ===================================== -->

                        <?php
                        if (
                            $show_purchase_card
                        ) :
                            ?>

                            <aside class="product-purchase-card">

                                <div class="product-purchase-card__header">

                                    <span class="product-purchase-card__eyebrow">

                                        <?php
                                        echo esc_html(
                                            $show_offer
                                                ? __(
                                                    'Dein Vorteil',
                                                    'jung-leben'
                                                )
                                                : __(
                                                    'Bezugsinformation',
                                                    'jung-leben'
                                                )
                                        );
                                        ?>

                                    </span>


                                    <?php
                                    if (
                                        $partner_name !== ''
                                    ) :
                                        ?>

                                        <p class="product-purchase-card__partner">
                                            <?php
                                            echo esc_html(
                                                $partner_name
                                            );
                                            ?>
                                        </p>

                                    <?php endif; ?>


                                    <?php
                                    if (
                                        $price_display !== ''
                                    ) :
                                        ?>

                                        <p class="product-purchase-card__price">
                                            <?php
                                            echo esc_html(
                                                $price_display
                                            );
                                            ?>
                                        </p>

                                    <?php endif; ?>

                                </div>


                                <!-- =================================
                                     KUNDENVORTEIL
                                     ================================= -->

                                <?php
                                if (
                                    $show_offer
                                    && (
                                        $discount_text !== ''
                                        || $discount_code !== ''
                                    )
                                ) :
                                    ?>

                                    <div class="product-purchase-card__discount">

                                        <?php
                                        if (
                                            $discount_text !== ''
                                        ) :
                                            ?>

                                            <strong class="product-purchase-card__discount-value">
                                                <?php
                                                echo esc_html(
                                                    $discount_text
                                                );
                                                ?>
                                            </strong>

                                        <?php endif; ?>


                                        <?php
                                        if (
                                            $discount_code !== ''
                                        ) :
                                            ?>

                                            <div class="product-purchase-card__code">

                                                <span class="product-purchase-card__code-label">
                                                    <?php
                                                    esc_html_e(
                                                        'Code',
                                                        'jung-leben'
                                                    );
                                                    ?>
                                                </span>


                                                <button
                                                    type="button"
                                                    class="
                                                        product-purchase-card__copy
                                                        js-product-copy-code
                                                    "
                                                    data-copy-code="<?php
                                                    echo esc_attr(
                                                        $discount_code
                                                    );
                                                    ?>"
                                                    aria-label="<?php
                                                    echo esc_attr(
                                                        sprintf(
                                                            __(
                                                                'Rabattcode %s kopieren',
                                                                'jung-leben'
                                                            ),
                                                            $discount_code
                                                        )
                                                    );
                                                    ?>"
                                                >

                                                    <span class="product-purchase-card__code-value">
                                                        <?php
                                                        echo esc_html(
                                                            $discount_code
                                                        );
                                                        ?>
                                                    </span>


                                                    <span class="product-purchase-card__copy-label">
                                                        <?php
                                                        esc_html_e(
                                                            'Kopieren',
                                                            'jung-leben'
                                                        );
                                                        ?>
                                                    </span>

                                                </button>


                                                <span
                                                    class="product-purchase-card__copy-status"
                                                    aria-live="polite"
                                                ></span>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                <?php endif; ?>


                                <?php
                                if (
                                    $show_offer
                                    && $public_note !== ''
                                ) :
                                    ?>

                                    <p class="product-purchase-card__note">
                                        <?php
                                        echo esc_html(
                                            $public_note
                                        );
                                        ?>
                                    </p>

                                <?php endif; ?>


                                <!-- =================================
                                     KAUFBUTTON
                                     ================================= -->

                                <?php
                                if (
                                    $purchase_url !== ''
                                ) :
                                    ?>

                                    <a
                                        href="<?php
                                        echo esc_url(
                                            $purchase_url
                                        );
                                        ?>"
                                        class="
                                            btn
                                            btn-primary
                                            product-purchase-card__button
                                        "
                                        <?php
                                        if (
                                            $link_new_tab
                                        ) :
                                            ?>
                                            target="_blank"
                                        <?php endif; ?>

                                        rel="<?php
                                        echo esc_attr(
                                            $purchase_rel
                                        );
                                        ?>"
                                    >

                                        <span>
                                            <?php
                                            echo esc_html(
                                                $button_text
                                            );
                                            ?>
                                        </span>


                                        <span aria-hidden="true">
                                            ↗
                                        </span>

                                    </a>

                                <?php endif; ?>

                            </aside>

                        <?php endif; ?>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 AUSFÜHRLICHER DETAILBEREICH
                 ================================================= -->

            <?php
            if (
                $show_detail_area
            ) :
                ?>

                <section class="product-details">

                    <div class="container product-details__grid">

                        <!-- =====================================
                             HAUPTINHALT
                             ===================================== -->

                        <div class="product-details__main">

                            <!-- Einordnung -->
                            <?php
                            if (
                                $has_purpose_section
                            ) :
                                ?>

                                <section class="product-section">

                                    <p class="eyebrow">
                                        <?php
                                        esc_html_e(
                                            'Einordnung',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <h2>
                                        <?php
                                        esc_html_e(
                                            'Wofür Jung Leben dieses Produkt einordnet.',
                                            'jung-leben'
                                        );
                                        ?>
                                    </h2>


                                    <div class="product-rich-text">

                                        <?php
                                        echo wpautop(
                                            wp_kses_post(
                                                $purpose
                                            )
                                        );
                                        ?>

                                    </div>

                                </section>

                            <?php endif; ?>


                            <!-- Produkt -->
                            <?php
                            if (
                                $has_rendered_content
                            ) :
                                ?>

                                <section class="product-section">

                                    <?php
                                    if (
                                        $editor_content !== ''
                                    ) :
                                        ?>

                                        <p class="eyebrow">
                                            <?php
                                            esc_html_e(
                                                'Produkt',
                                                'jung-leben'
                                            );
                                            ?>
                                        </p>


                                        <h2>
                                            <?php
                                            esc_html_e(
                                                'Produkt im Überblick.',
                                                'jung-leben'
                                            );
                                            ?>
                                        </h2>

                                    <?php endif; ?>


                                    <div class="product-rich-text">
                                        <?php
                                        echo wp_kses_post(
                                            $rendered_content
                                        );
                                        ?>
                                    </div>

                                </section>

                            <?php endif; ?>



                            <!-- =================================
                                 WEITERE PRODUKTOPTIONEN
                                 ================================= -->

                            <?php
                            if (
                                $has_alternatives
                            ) :
                                ?>

                                <section
                                    class="
                                        product-section
                                        product-alternatives
                                    "
                                >

                                    <p class="eyebrow">
                                        <?php
                                        esc_html_e(
                                            'Weitere Optionen',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <h2>
                                        <?php
                                        esc_html_e(
                                            'Alternativen.',
                                            'jung-leben'
                                        );
                                        ?>
                                    </h2>


                                    <p class="product-alternatives__intro">
                                        <?php
                                        esc_html_e(
                                            'Neben dem oben gezeigten Favoriten sind für dieses Produkt weitere Optionen hinterlegt.',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <div class="product-alternatives__grid">

                                        <?php
                                        foreach (
                                            $alternatives
                                            as $alternative
                                        ) :
                                            ?>

                                            <article class="product-alternative-card">

                                                <div class="product-alternative-card__header">

                                                    <span class="product-alternative-card__label">
                                                        <?php
                                                        esc_html_e(
                                                            'Alternative',
                                                            'jung-leben'
                                                        );
                                                        ?>
                                                    </span>


                                                    <?php
                                                    if (
                                                        $alternative[
                                                            'brand'
                                                        ] !== ''
                                                    ) :
                                                        ?>

                                                        <span class="product-alternative-card__brand">
                                                            <?php
                                                            echo esc_html(
                                                                $alternative[
                                                                    'brand'
                                                                ]
                                                            );
                                                            ?>
                                                        </span>

                                                    <?php endif; ?>

                                                </div>


                                                <h3 class="product-alternative-card__title">
                                                    <?php
                                                    echo esc_html(
                                                        $alternative[
                                                            'title'
                                                        ]
                                                    );
                                                    ?>
                                                </h3>


                                                <?php
                                                if (
                                                    $alternative[
                                                        'url'
                                                    ] !== ''
                                                ) :
                                                    ?>

                                                    <a
                                                        href="<?php
                                                        echo esc_url(
                                                            $alternative[
                                                                'url'
                                                            ]
                                                        );
                                                        ?>"
                                                        class="product-alternative-card__link"
                                                        target="_blank"
                                                        rel="noopener noreferrer external"
                                                    >

                                                        <span>
                                                            <?php
                                                            esc_html_e(
                                                                'Alternative ansehen',
                                                                'jung-leben'
                                                            );
                                                            ?>
                                                        </span>

                                                        <span aria-hidden="true">
                                                            ↗
                                                        </span>

                                                    </a>

                                                <?php endif; ?>

                                            </article>

                                        <?php endforeach; ?>

                                    </div>

                                </section>

                            <?php endif; ?>


                            <!-- =================================
                                 VERKNÜPFTE BEITRÄGE
                                 ================================= -->

                            <?php
                            if (
                                $has_relation_content
                            ) :
                                ?>

                                <div class="product-relations">
                                    <?php
                                    echo wp_kses_post(
                                        $relation_content
                                    );
                                    ?>
                                </div>

                            <?php endif; ?>


                            <!-- Persönliche Erfahrung -->
                            <?php
                            if (
                                $has_experience_section
                            ) :
                                ?>

                                <section class="product-experience">

                                    <p class="eyebrow">
                                        <?php
                                        esc_html_e(
                                            'Persönliche Einordnung',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <h2>
                                        <?php
                                        esc_html_e(
                                            'Robertos Erfahrung.',
                                            'jung-leben'
                                        );
                                        ?>
                                    </h2>


                                    <div class="product-experience__text">

                                        <?php
                                        echo wpautop(
                                            wp_kses_post(
                                                $personal_experience
                                            )
                                        );
                                        ?>

                                    </div>

                                </section>

                            <?php endif; ?>


                            <!-- Vorteile / Hinweise -->
                            <?php
                            if (
                                $has_pros_cons
                            ) :
                                ?>

                                <section class="product-pros-cons">

                                    <?php
                                    if (
                                        ! empty(
                                            $benefit_items
                                        )
                                    ) :
                                        ?>

                                        <div
                                            class="
                                                product-pros-cons__card
                                                product-pros-cons__card--positive
                                            "
                                        >

                                            <h3>
                                                <?php
                                                esc_html_e(
                                                    'Was dafür spricht',
                                                    'jung-leben'
                                                );
                                                ?>
                                            </h3>


                                            <ul>

                                                <?php
                                                foreach (
                                                    $benefit_items
                                                    as $item
                                                ) :
                                                    ?>

                                                    <li>
                                                        <?php
                                                        echo esc_html(
                                                            $item
                                                        );
                                                        ?>
                                                    </li>

                                                <?php endforeach; ?>

                                            </ul>

                                        </div>

                                    <?php endif; ?>


                                    <?php
                                    if (
                                        ! empty(
                                            $limitation_items
                                        )
                                    ) :
                                        ?>

                                        <div class="product-pros-cons__card">

                                            <h3>
                                                <?php
                                                esc_html_e(
                                                    'Was zu beachten ist',
                                                    'jung-leben'
                                                );
                                                ?>
                                            </h3>


                                            <ul class="product-pros-cons__list--neutral">

                                                <?php
                                                foreach (
                                                    $limitation_items
                                                    as $item
                                                ) :
                                                    ?>

                                                    <li>
                                                        <?php
                                                        echo esc_html(
                                                            $item
                                                        );
                                                        ?>
                                                    </li>

                                                <?php endforeach; ?>

                                            </ul>

                                        </div>

                                    <?php endif; ?>

                                </section>

                            <?php endif; ?>

                        </div>


                        <!-- =====================================
                             SIDEBAR
                             ===================================== -->

                        <aside class="product-details__sidebar">

                            <?php
                            if (
                                $brand_markup !== ''
                            ) :
                                ?>

                                <div class="product-info-card">

                                    <p class="product-info-card__label">
                                        <?php
                                        esc_html_e(
                                            'Marke',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <div class="product-info-card__brand">
                                        <?php
                                        echo wp_kses_post(
                                            $brand_markup
                                        );
                                        ?>
                                    </div>

                                </div>

                            <?php endif; ?>


                            <?php
                            if (
                                ! empty(
                                    $routine_names
                                )
                            ) :
                                ?>

                                <div class="product-info-card">

                                    <p class="product-info-card__label">
                                        <?php
                                        esc_html_e(
                                            'Tagesroutine',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <div class="product-info-card__tags">

                                        <?php
                                        foreach (
                                            $routine_names
                                            as $routine_name
                                        ) :
                                            ?>

                                            <span>
                                                <?php
                                                echo esc_html(
                                                    $routine_name
                                                );
                                                ?>
                                            </span>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            <?php endif; ?>


                            <?php
                            if (
                                ! empty(
                                    $categories
                                )
                            ) :
                                ?>

                                <div class="product-info-card">

                                    <p class="product-info-card__label">
                                        <?php
                                        esc_html_e(
                                            'Einordnung',
                                            'jung-leben'
                                        );
                                        ?>
                                    </p>


                                    <div class="product-info-card__tags">

                                        <?php
                                        foreach (
                                            $categories
                                            as $category
                                        ) :
                                            ?>

                                            <span>
                                                <?php
                                                echo esc_html(
                                                    $category->name
                                                );
                                                ?>
                                            </span>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            <?php endif; ?>


                            <?php
                            if (
                                $health_notice !== ''
                            ) :
                                ?>

                                <div
                                    class="
                                        product-info-card
                                        product-info-card--notice
                                    "
                                >

                                    <span
                                        class="product-info-card__notice-icon"
                                        aria-hidden="true"
                                    >
                                        i
                                    </span>


                                    <p>
                                        <?php
                                        echo esc_html(
                                            $health_notice
                                        );
                                        ?>
                                    </p>

                                </div>

                            <?php endif; ?>

                        </aside>

                    </div>

                </section>

            <?php endif; ?>


            <!-- =================================================
                 KOMPAKTER HINWEIS
                 ================================================= -->

            <?php
            if (
                ! $show_detail_area
                && $health_notice !== ''
            ) :
                ?>

                <section class="product-compact-footer">

                    <div class="container">

                        <div class="product-compact-note">

                            <span
                                class="product-compact-note__mark"
                                aria-hidden="true"
                            >
                                i
                            </span>


                            <div>

                                <span class="product-compact-note__label">
                                    <?php
                                    esc_html_e(
                                        'Hinweis',
                                        'jung-leben'
                                    );
                                    ?>
                                </span>


                                <p>
                                    <?php
                                    echo esc_html(
                                        $health_notice
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>

                    </div>

                </section>

            <?php endif; ?>


            <!-- =================================================
                 AFFILIATE-HINWEIS
                 ================================================= -->

            <?php
            if (
                $affiliate_notice !== ''
            ) :
                ?>

                <aside class="product-affiliate-notice">

                    <div class="container">

                        <p>
                            <?php
                            echo esc_html(
                                $affiliate_notice
                            );
                            ?>
                        </p>

                    </div>

                </aside>

            <?php endif; ?>

        </main>

        <?php

    endwhile;

endif;
?>


<script>
document.addEventListener(
    'click',
    async function (event) {
        const button =
            event.target.closest(
                '.js-product-copy-code'
            );

        if (! button) {
            return;
        }


        const code =
            button.dataset.copyCode || '';

        if (! code) {
            return;
        }


        const label =
            button.querySelector(
                '.product-purchase-card__copy-label'
            );


        const status =
            button.parentElement
                ? button.parentElement.querySelector(
                    '.product-purchase-card__copy-status'
                )
                : null;


        const originalLabel =
            label
                ? label.textContent
                : 'Kopieren';


        const fallbackCopy =
            function () {
                const textarea =
                    document.createElement(
                        'textarea'
                    );

                textarea.value =
                    code;

                textarea.setAttribute(
                    'readonly',
                    ''
                );

                textarea.style.position =
                    'fixed';

                textarea.style.opacity =
                    '0';

                document.body.appendChild(
                    textarea
                );

                textarea.select();

                document.execCommand(
                    'copy'
                );

                textarea.remove();
            };


        try {
            if (
                navigator.clipboard
                && window.isSecureContext
            ) {
                await navigator.clipboard.writeText(
                    code
                );
            } else {
                fallbackCopy();
            }


            button.classList.add(
                'is-copied'
            );


            if (label) {
                label.textContent =
                    'Kopiert ✓';
            }


            if (status) {
                status.textContent =
                    'Rabattcode wurde kopiert.';
            }


            window.setTimeout(
                function () {
                    button.classList.remove(
                        'is-copied'
                    );


                    if (label) {
                        label.textContent =
                            originalLabel;
                    }


                    if (status) {
                        status.textContent =
                            '';
                    }
                },
                2200
            );
        } catch (error) {
            if (status) {
                status.textContent =
                    'Code konnte nicht automatisch kopiert werden.';
            }
        }
    }
);
</script>


<?php
get_footer();