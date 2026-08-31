<?php
/**
 * Seite «Empfehlungen».
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/* =========================================================
   SEITENSPEZIFISCHES CSS
   ========================================================= */

$recommendations_css_path =
    get_template_directory()
    . '/assets/css/recommendations-page.css';


add_action(
    'wp_enqueue_scripts',
    static function () use (
        $recommendations_css_path
    ): void {
        if (
            ! file_exists(
                $recommendations_css_path
            )
        ) {
            return;
        }

        wp_enqueue_style(
            'jung-leben-recommendations-page',
            get_template_directory_uri()
                . '/assets/css/recommendations-page.css',
            [
                'jung-leben-site',
            ],
            (string) filemtime(
                $recommendations_css_path
            )
        );
    },
    30
);


get_header();


/* =========================================================
   ACF-HILFSFUNKTION
   ========================================================= */

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


/* =========================================================
   PRODUKT-URL
   ========================================================= */

$get_product_url =
    static function (
        WP_Post $product
    ): string {
        if (
            $product->post_status ===
            'publish'
        ) {
            $url =
                get_permalink(
                    $product
                );


            return
                is_string(
                    $url
                )
                    ? $url
                    : '';
        }


        if (
            ! is_user_logged_in()
            || ! current_user_can(
                'edit_post',
                $product->ID
            )
        ) {
            return '';
        }


        $preview_url =
            get_preview_post_link(
                $product
            );


        return
            is_string(
                $preview_url
            )
            && $preview_url !== ''
                ? $preview_url
                : '';
    };


/* =========================================================
   PRODUKT-MONOGRAMM
   ========================================================= */

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


/* =========================================================
   REDAKTIONSVORSCHAU
   ========================================================= */

$editor_preview =
    is_user_logged_in()
    && current_user_can(
        'edit_posts'
    );


$product_statuses =
    $editor_preview
        ? [
            'publish',
            'draft',
            'pending',
            'private',
            'future',
        ]
        : [
            'publish',
        ];


/* =========================================================
   PRODUKTE LADEN
   ========================================================= */

$product_query =
    new WP_Query([
        'post_type' =>
            'jl_product',

        'post_status' =>
            $product_statuses,

        'posts_per_page' =>
            -1,

        'orderby' =>
            'date',

        'order' =>
            'DESC',

        'no_found_rows' =>
            true,
    ]);


$products =
    is_array(
        $product_query->posts
    )
        ? $product_query->posts
        : [];


/* =========================================================
   SORTIERUNG
   ========================================================= */

usort(
    $products,
    static function (
        WP_Post $product_a,
        WP_Post $product_b
    ) use (
        $get_product_field
    ): int {
        $featured_a =
            (bool)
            $get_product_field(
                'jl_product_featured',
                $product_a->ID,
                false
            );


        $featured_b =
            (bool)
            $get_product_field(
                'jl_product_featured',
                $product_b->ID,
                false
            );


        if (
            $featured_a !==
            $featured_b
        ) {
            return
                $featured_a
                    ? -1
                    : 1;
        }


        $priority_a =
            (int)
            $get_product_field(
                'jl_product_sort_priority',
                $product_a->ID,
                0
            );


        $priority_b =
            (int)
            $get_product_field(
                'jl_product_sort_priority',
                $product_b->ID,
                0
            );


        if (
            $priority_a !==
            $priority_b
        ) {
            return
                $priority_b
                <=>
                $priority_a;
        }


        return
            strcmp(
                $product_b->post_date,
                $product_a->post_date
            );
    }
);


/* =========================================================
   FILTERDATEN
   ========================================================= */

$category_map = [];

$brand_map = [];

$available_routine_times = [];

$draft_count = 0;


foreach (
    $products
    as $product
) {
    $product_id =
        (int)
        $product->ID;


    if (
        $product->post_status !==
        'publish'
    ) {
        $draft_count++;
    }


    /* =====================================================
       KATEGORIEN
       ===================================================== */

    $categories =
        get_the_terms(
            $product_id,
            'jl_product_category'
        );


    if (
        is_array(
            $categories
        )
        && ! is_wp_error(
            $categories
        )
    ) {
        foreach (
            $categories
            as $category
        ) {
            if (
                ! $category
                instanceof WP_Term
            ) {
                continue;
            }


            if (
                ! isset(
                    $category_map[
                        $category->slug
                    ]
                )
            ) {
                $category_map[
                    $category->slug
                ] = [
                    'term' =>
                        $category,

                    'count' =>
                        0,
                ];
            }


            $category_map[
                $category->slug
            ]['count']++;
        }
    }


    /* =====================================================
       MARKEN
       ===================================================== */

    $brands =
        get_the_terms(
            $product_id,
            'jl_product_brand'
        );


    if (
        is_array(
            $brands
        )
        && ! is_wp_error(
            $brands
        )
    ) {
        foreach (
            $brands
            as $brand
        ) {
            if (
                ! $brand
                instanceof WP_Term
            ) {
                continue;
            }


            if (
                ! isset(
                    $brand_map[
                        $brand->slug
                    ]
                )
            ) {
                $brand_map[
                    $brand->slug
                ] = [
                    'term' =>
                        $brand,

                    'count' =>
                        0,
                ];
            }


            $brand_map[
                $brand->slug
            ]['count']++;
        }
    }


    /* =====================================================
       ROUTINE
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
            $routine_time !== ''
        ) {
            $available_routine_times[
                $routine_time
            ] = true;
        }
    }
}


/* =========================================================
   FILTER SORTIEREN
   ========================================================= */

uasort(
    $category_map,
    static function (
        array $item_a,
        array $item_b
    ): int {
        return
            strnatcasecmp(
                $item_a['term']->name,
                $item_b['term']->name
            );
    }
);


uasort(
    $brand_map,
    static function (
        array $item_a,
        array $item_b
    ): int {
        return
            strnatcasecmp(
                $item_a['term']->name,
                $item_b['term']->name
            );
    }
);


/* =========================================================
   ROUTINEN
   ========================================================= */

$routine_filter_labels = [
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
            'Zeitlich flexibel',
            'jung-leben'
        ),
];


/* =========================================================
   EMPFEHLUNGSSTATUS
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


/* =========================================================
   ENTWURFSSTATUS
   ========================================================= */

$editor_status_labels = [
    'draft' =>
        __(
            'Entwurf',
            'jung-leben'
        ),

    'pending' =>
        __(
            'Ausstehend',
            'jung-leben'
        ),

    'private' =>
        __(
            'Privat',
            'jung-leben'
        ),

    'future' =>
        __(
            'Geplant',
            'jung-leben'
        ),
];
?>

<main
    id="main-content"
    class="recommendations-page"
>

    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="recommendations-hero">

        <div class="container recommendations-hero__inner">

            <p class="eyebrow">
                <?php
                esc_html_e(
                    'Ausgewählt und eingeordnet',
                    'jung-leben'
                );
                ?>
            </p>


            <h1>
                <?php
                the_title();
                ?>
            </h1>


            <?php
            while (
                have_posts()
            ) :
                the_post();

                $page_content =
                    trim(
                        (string)
                        get_the_content()
                    );
                ?>

                <?php
                if (
                    $page_content !== ''
                ) :
                    ?>

                    <div class="recommendations-hero__text">
                        <?php
                        the_content();
                        ?>
                    </div>

                <?php else : ?>

                    <div class="recommendations-hero__text">

                        <p>
                            <?php
                            esc_html_e(
                                'Hier findest du ausgewählte Produkte, die wir auf Jung Leben einordnen, vergleichen und mit persönlichen Erfahrungen verbinden. Nicht jedes Produkt passt zu jeder Person – deshalb steht Orientierung vor einem schnellen Kauf.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>

                <?php endif; ?>

            <?php endwhile; ?>

        </div>

    </section>


    <!-- =====================================================
         KATALOG
         ===================================================== -->

    <section
        class="recommendations-catalogue"
        data-product-catalogue
    >

        <div class="container">

            <!-- =================================================
                 REDAKTIONSVORSCHAU
                 ================================================= -->

            <?php
            if (
                $editor_preview
                && $draft_count > 0
            ) :
                ?>

                <div class="recommendations-editor-preview">

                    <span
                        class="recommendations-editor-preview__mark"
                        aria-hidden="true"
                    >
                        ✓
                    </span>


                    <div>

                        <strong>
                            <?php
                            esc_html_e(
                                'Redaktionsvorschau',
                                'jung-leben'
                            );
                            ?>
                        </strong>


                        <p>
                            <?php
                            printf(
                                esc_html__(
                                    '%d noch nicht veröffentlichte Produkte sind für dich als eingeloggten Redakteur sichtbar. Besucher sehen diese Produkte nicht.',
                                    'jung-leben'
                                ),
                                $draft_count
                            );
                            ?>
                        </p>

                    </div>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 SUCHE / ANZAHL
                 ================================================= -->

            <div class="recommendations-tools">

                <div class="recommendations-search">

                    <label
                        for="product-search"
                        class="screen-reader-text"
                    >
                        <?php
                        esc_html_e(
                            'Produkte durchsuchen',
                            'jung-leben'
                        );
                        ?>
                    </label>


                    <div class="recommendations-search__field">

                        <span
                            class="recommendations-search__icon"
                            aria-hidden="true"
                        >
                            ⌕
                        </span>


                        <input
                            id="product-search"
                            type="search"
                            placeholder="<?php
                            esc_attr_e(
                                'Produkt, Marke oder Kategorie suchen …',
                                'jung-leben'
                            );
                            ?>"
                            autocomplete="off"
                            data-product-search
                        >

                    </div>

                </div>


                <p
                    class="recommendations-count"
                    aria-live="polite"
                >

                    <strong data-product-count>
                        <?php
                        echo esc_html(
                            (string)
                            count(
                                $products
                            )
                        );
                        ?>
                    </strong>

                    <span>
                        <?php
                        esc_html_e(
                            'Produkte',
                            'jung-leben'
                        );
                        ?>
                    </span>

                </p>

            </div>


            <!-- =================================================
                 KATEGORIEN
                 ================================================= -->

            <?php
            if (
                ! empty(
                    $category_map
                )
            ) :
                ?>

                <div class="recommendations-filters">

                    <button
                        type="button"
                        class="
                            recommendations-filter
                            is-active
                        "
                        data-product-filter="all"
                        aria-pressed="true"
                    >

                        <span>
                            <?php
                            esc_html_e(
                                'Alle',
                                'jung-leben'
                            );
                            ?>
                        </span>

                        <small>
                            <?php
                            echo esc_html(
                                (string)
                                count(
                                    $products
                                )
                            );
                            ?>
                        </small>

                    </button>


                    <?php
                    foreach (
                        $category_map
                        as $category_data
                    ) :
                        $category =
                            $category_data['term'];

                        $category_count =
                            (int)
                            $category_data['count'];
                        ?>

                        <button
                            type="button"
                            class="recommendations-filter"
                            data-product-filter="<?php
                            echo esc_attr(
                                $category->slug
                            );
                            ?>"
                            aria-pressed="false"
                        >

                            <span>
                                <?php
                                echo esc_html(
                                    $category->name
                                );
                                ?>
                            </span>

                            <small>
                                <?php
                                echo esc_html(
                                    (string)
                                    $category_count
                                );
                                ?>
                            </small>

                        </button>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 SEKUNDÄRFILTER
                 ================================================= -->

            <div class="recommendations-secondary-filters">

                <?php
                if (
                    ! empty(
                        $brand_map
                    )
                ) :
                    ?>

                    <div class="recommendations-select">

                        <label for="product-brand-filter">
                            <?php
                            esc_html_e(
                                'Marke',
                                'jung-leben'
                            );
                            ?>
                        </label>


                        <select
                            id="product-brand-filter"
                            data-product-brand-filter
                        >

                            <option value="all">
                                <?php
                                esc_html_e(
                                    'Alle Marken',
                                    'jung-leben'
                                );
                                ?>
                            </option>


                            <?php
                            foreach (
                                $brand_map
                                as $brand_data
                            ) :
                                $brand =
                                    $brand_data['term'];

                                $brand_count =
                                    (int)
                                    $brand_data['count'];
                                ?>

                                <option
                                    value="<?php
                                    echo esc_attr(
                                        $brand->slug
                                    );
                                    ?>"
                                >
                                    <?php
                                    echo esc_html(
                                        sprintf(
                                            '%s (%d)',
                                            $brand->name,
                                            $brand_count
                                        )
                                    );
                                    ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                <?php endif; ?>


                <?php
                if (
                    ! empty(
                        $available_routine_times
                    )
                ) :
                    ?>

                    <div class="recommendations-select">

                        <label for="product-routine-filter">
                            <?php
                            esc_html_e(
                                'Tageszeit',
                                'jung-leben'
                            );
                            ?>
                        </label>


                        <select
                            id="product-routine-filter"
                            data-product-routine-filter
                        >

                            <option value="all">
                                <?php
                                esc_html_e(
                                    'Alle Tageszeiten',
                                    'jung-leben'
                                );
                                ?>
                            </option>


                            <?php
                            foreach (
                                $routine_filter_labels
                                as $value => $label
                            ) :
                                if (
                                    ! isset(
                                        $available_routine_times[
                                            $value
                                        ]
                                    )
                                ) {
                                    continue;
                                }
                                ?>

                                <option
                                    value="<?php
                                    echo esc_attr(
                                        $value
                                    );
                                    ?>"
                                >
                                    <?php
                                    echo esc_html(
                                        $label
                                    );
                                    ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                <?php endif; ?>


                <button
                    type="button"
                    class="recommendations-reset"
                    data-product-reset
                >
                    <?php
                    esc_html_e(
                        'Filter zurücksetzen',
                        'jung-leben'
                    );
                    ?>
                </button>

            </div>


            <!-- =================================================
                 PRODUKTE
                 ================================================= -->

            <?php
            if (
                ! empty(
                    $products
                )
            ) :
                ?>

                <div
                    class="recommendations-grid"
                    data-product-grid
                >

                    <?php
                    foreach (
                        $products
                        as $product
                    ) :
                        $product_id =
                            (int)
                            $product->ID;


                        $product_url =
                            $get_product_url(
                                $product
                            );


                        if (
                            $product_url === ''
                        ) {
                            continue;
                        }


                        /* =====================================
                           FELDER
                           ===================================== */

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


                        $purpose =
                            trim(
                                (string)
                                $get_product_field(
                                    'jl_product_purpose',
                                    $product_id,
                                    ''
                                )
                            );


                        $partner_name =
                            trim(
                                (string)
                                $get_product_field(
                                    'jl_product_partner_name',
                                    $product_id,
                                    ''
                                )
                            );


                        /* =====================================
                           MARKEN
                           ===================================== */

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


                        $brand_slugs =
                            wp_list_pluck(
                                $brands,
                                'slug'
                            );


                        $brand_names =
                            wp_list_pluck(
                                $brands,
                                'name'
                            );


                        $primary_brand =
                            ! empty(
                                $brands
                            )
                            && $brands[0]
                            instanceof WP_Term
                                ? $brands[0]
                                : null;


                        /* =====================================
                           ZENTRALE MARKENAUSGABE
                           ===================================== */

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
                                            'recommendation'
                                        );
                            } else {
                                $brand_markup =
                                    '<span class="jl-brand jl-brand--recommendation">'
                                    . '<span class="jl-brand__name">'
                                    . esc_html(
                                        $primary_brand
                                            ->name
                                    )
                                    . '</span>'
                                    . '</span>';
                            }
                        } else {
                            $brand_markup =
                                '<span class="jl-brand jl-brand--recommendation">'
                                . '<span class="jl-brand__name">'
                                . esc_html__(
                                    'Jung Leben',
                                    'jung-leben'
                                )
                                . '</span>'
                                . '</span>';
                        }


                        /* =====================================
                           KATEGORIEN
                           ===================================== */

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


                        $category_slugs =
                            wp_list_pluck(
                                $categories,
                                'slug'
                            );


                        $category_names =
                            wp_list_pluck(
                                $categories,
                                'name'
                            );


                        $primary_category =
                            ! empty(
                                $category_names
                            )
                                ? (string)
                                    $category_names[0]
                                : '';


                        /* =====================================
                           ROUTINEN
                           ===================================== */

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


                        /* =====================================
                           SUCHTEXT
                           ===================================== */

                        $search_text =
                            implode(
                                ' ',
                                [
                                    $product->post_title,
                                    $product->post_excerpt,
                                    $purpose,
                                    $partner_name,
                                    implode(
                                        ' ',
                                        $brand_names
                                    ),
                                    implode(
                                        ' ',
                                        $category_names
                                    ),
                                ]
                            );


                        /* =====================================
                           BILD
                           ===================================== */

                        $has_image =
                            has_post_thumbnail(
                                $product_id
                            );


                        $product_mark =
                            $get_product_mark(
                                $product
                                    ->post_title
                            );


                        /* =====================================
                           KLASSEN
                           ===================================== */

                        $card_classes = [
                            'recommendation-card',
                        ];


                        if ($featured) {
                            $card_classes[] =
                                'recommendation-card--featured';
                        }


                        if (
                            ! $has_image
                        ) {
                            $card_classes[] =
                                'recommendation-card--no-image';
                        }


                        if (
                            $product->post_status !==
                            'publish'
                        ) {
                            $card_classes[] =
                                'recommendation-card--editor-preview';
                        }


                        $editor_status_label =
                            $product->post_status !==
                                'publish'
                            && isset(
                                $editor_status_labels[
                                    $product->post_status
                                ]
                            )
                                ? $editor_status_labels[
                                    $product->post_status
                                ]
                                : '';
                        ?>

                        <article
                            class="<?php
                            echo esc_attr(
                                implode(
                                    ' ',
                                    $card_classes
                                )
                            );
                            ?>"
                            data-product-card
                            data-product-categories="<?php
                            echo esc_attr(
                                implode(
                                    ' ',
                                    $category_slugs
                                )
                            );
                            ?>"
                            data-product-brands="<?php
                            echo esc_attr(
                                implode(
                                    ' ',
                                    $brand_slugs
                                )
                            );
                            ?>"
                            data-product-routine-times="<?php
                            echo esc_attr(
                                implode(
                                    ' ',
                                    $routine_times
                                )
                            );
                            ?>"
                            data-product-search-text="<?php
                            echo esc_attr(
                                wp_strip_all_tags(
                                    $search_text
                                )
                            );
                            ?>"
                        >

                            <div class="recommendation-card__card">

                                <!-- =========================
                                     MEDIA
                                     ========================= -->

                                <a
                                    href="<?php
                                    echo esc_url(
                                        $product_url
                                    );
                                    ?>"
                                    class="recommendation-card__media"
                                    aria-label="<?php
                                    echo esc_attr(
                                        sprintf(
                                            __(
                                                '%s ansehen',
                                                'jung-leben'
                                            ),
                                            $product
                                                ->post_title
                                        )
                                    );
                                    ?>"
                                >

                                    <?php
                                    if (
                                        $has_image
                                    ) :
                                        ?>

                                        <?php
                                        echo wp_kses_post(
                                            get_the_post_thumbnail(
                                                $product_id,
                                                'medium_large',
                                                [
                                                    'class' =>
                                                        'recommendation-card__image',

                                                    'loading' =>
                                                        'lazy',
                                                ]
                                            )
                                        );
                                        ?>

                                    <?php else : ?>

                                        <div
                                            class="recommendation-card__placeholder"
                                            aria-hidden="true"
                                        >

                                            <span class="recommendation-card__placeholder-mark">
                                                <?php
                                                echo esc_html(
                                                    $product_mark
                                                );
                                                ?>
                                            </span>

                                        </div>

                                    <?php endif; ?>


                                    <?php
                                    if (
                                        $featured
                                    ) :
                                        ?>

                                        <span class="recommendation-card__featured">
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
                                        $editor_preview
                                        && $editor_status_label !== ''
                                    ) :
                                        ?>

                                        <span class="recommendation-card__editor-status">
                                            <?php
                                            echo esc_html(
                                                $editor_status_label
                                            );
                                            ?>
                                        </span>

                                    <?php endif; ?>

                                </a>


                                <!-- =========================
                                     CONTENT
                                     ========================= -->

                                <div class="recommendation-card__content">

                                    <div class="recommendation-card__brand">
                                        <?php
                                        echo wp_kses_post(
                                            $brand_markup
                                        );
                                        ?>
                                    </div>


                                    <?php
                                    $show_recommendation_badge =
                                        isset(
                                            $recommendation_labels[
                                                $recommendation_status
                                            ]
                                        );


                                    if (
                                        $show_recommendation_badge
                                        || $personally_tested
                                    ) :
                                        ?>

                                        <div class="recommendation-card__badges">

                                            <?php
                                            if (
                                                $show_recommendation_badge
                                            ) :
                                                ?>

                                                <span
                                                    class="
                                                        recommendation-card__badge
                                                        recommendation-card__badge--<?php
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
                                                        recommendation-card__badge
                                                        recommendation-card__badge--tested
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


                                    <h2 class="recommendation-card__title">

                                        <a
                                            href="<?php
                                            echo esc_url(
                                                $product_url
                                            );
                                            ?>"
                                        >
                                            <?php
                                            echo esc_html(
                                                $product
                                                    ->post_title
                                            );
                                            ?>
                                        </a>

                                    </h2>


                                    <?php
                                    if (
                                        trim(
                                            $product
                                                ->post_excerpt
                                        ) !== ''
                                    ) :
                                        ?>

                                        <p class="recommendation-card__excerpt">

                                            <?php
                                            echo esc_html(
                                                wp_trim_words(
                                                    $product
                                                        ->post_excerpt,
                                                    27,
                                                    ' …'
                                                )
                                            );
                                            ?>

                                        </p>

                                    <?php endif; ?>


                                    <div class="recommendation-card__footer">

                                        <?php
                                        if (
                                            $primary_category !== ''
                                        ) :
                                            ?>

                                            <span class="recommendation-card__category">
                                                <?php
                                                echo esc_html(
                                                    $primary_category
                                                );
                                                ?>
                                            </span>

                                        <?php else : ?>

                                            <span></span>

                                        <?php endif; ?>


                                        <a
                                            href="<?php
                                            echo esc_url(
                                                $product_url
                                            );
                                            ?>"
                                            class="recommendation-card__cta"
                                        >

                                            <span>
                                                <?php
                                                esc_html_e(
                                                    'Mehr erfahren',
                                                    'jung-leben'
                                                );
                                                ?>
                                            </span>


                                            <span
                                                class="recommendation-card__arrow"
                                                aria-hidden="true"
                                            >
                                                →
                                            </span>

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>


                <div
                    class="recommendations-empty"
                    data-product-empty
                    hidden
                >

                    <div
                        class="recommendations-empty__icon"
                        aria-hidden="true"
                    >
                        ⌕
                    </div>


                    <h2>
                        <?php
                        esc_html_e(
                            'Keine passenden Produkte gefunden.',
                            'jung-leben'
                        );
                        ?>
                    </h2>


                    <p>
                        <?php
                        esc_html_e(
                            'Passe deine Suche oder die ausgewählten Filter an.',
                            'jung-leben'
                        );
                        ?>
                    </p>

                </div>

            <?php else : ?>

                <div class="recommendations-empty">

                    <div
                        class="recommendations-empty__icon"
                        aria-hidden="true"
                    >
                        JL
                    </div>


                    <h2>
                        <?php
                        esc_html_e(
                            'Die ersten Empfehlungen folgen bald.',
                            'jung-leben'
                        );
                        ?>
                    </h2>


                    <p>
                        <?php
                        esc_html_e(
                            'Wir stellen die ersten Produkte gerade sorgfältig zusammen.',
                            'jung-leben'
                        );
                        ?>
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php
wp_reset_postdata();

get_footer();