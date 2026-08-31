<?php
/**
 * Seite «Routinen».
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

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


/* =========================================================
   PRODUKTE LADEN
   ========================================================= */

$products =
    get_posts([
        'post_type' =>
            'jl_product',

        'post_status' =>
            'publish',

        'posts_per_page' =>
            -1,

        'orderby' =>
            'date',

        'order' =>
            'DESC',
    ]);


if (
    ! is_array(
        $products
    )
) {
    $products = [];
}


/* =========================================================
   PRODUKTE SORTIEREN
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
            strnatcasecmp(
                $product_a->post_title,
                $product_b->post_title
            );
    }
);


/* =========================================================
   PRODUKTE NACH ROUTINE GRUPPIEREN
   ========================================================= */

$products_by_routine = [
    'morning' =>
        [],

    'midday' =>
        [],

    'evening' =>
        [],
];


foreach (
    $products
    as $product
) {
    $routine_times =
        $get_product_field(
            'jl_product_routine_time',
            $product->ID,
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
        continue;
    }


    foreach (
        array_keys(
            $products_by_routine
        )
        as $routine_key
    ) {
        if (
            in_array(
                $routine_key,
                $routine_times,
                true
            )
        ) {
            $products_by_routine[
                $routine_key
            ][] =
                $product;
        }
    }
}


/* =========================================================
   ROUTINEN
   ========================================================= */

$routines = [

    'morning' => [
        'eyebrow' =>
            __(
                'Morgenroutine',
                'jung-leben'
            ),

        'title' =>
            __(
                'Bewusst in den Tag starten.',
                'jung-leben'
            ),

        'text' =>
            __(
                'Der Morgen setzt den Rahmen für den Tag. Hier findest du Produkte und Routinen, die Roberto bewusst dem Start in den Tag zuordnet.',
                'jung-leben'
            ),

        'link_label' =>
            __(
                'Alle Empfehlungen für morgens',
                'jung-leben'
            ),

        'symbol' =>
            '✹',

        'modifier' =>
            'morning',
    ],


    'midday' => [
        'eyebrow' =>
            __(
                'Tagsüber',
                'jung-leben'
            ),

        'title' =>
            __(
                'Energie und Fokus im Alltag.',
                'jung-leben'
            ),

        'text' =>
            __(
                'Zwischen Arbeit, Bewegung und Alltag geht es vor allem um Praktikabilität. Diese Empfehlungen lassen sich gut in den Tagesablauf integrieren.',
                'jung-leben'
            ),

        'link_label' =>
            __(
                'Alle Empfehlungen für mittags',
                'jung-leben'
            ),

        'symbol' =>
            '◐',

        'modifier' =>
            'midday',
    ],


    'evening' => [
        'eyebrow' =>
            __(
                'Abendroutine',
                'jung-leben'
            ),

        'title' =>
            __(
                'Den Tag bewusst abschliessen.',
                'jung-leben'
            ),

        'text' =>
            __(
                'Am Abend stehen Ruhe, Regeneration und ein bewusster Übergang in die Nacht im Mittelpunkt. Hier findest du entsprechend eingeordnete Empfehlungen.',
                'jung-leben'
            ),

        'link_label' =>
            __(
                'Alle Empfehlungen für abends',
                'jung-leben'
            ),

        'symbol' =>
            '☾',

        'modifier' =>
            'evening',
    ],
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
?>

<main
    id="main-content"
    class="routines-page"
>

    <!-- =====================================================
         INTRO
         ===================================================== -->

    <section class="routines-intro">

        <div class="container routines-intro__inner">

            <p class="eyebrow">
                <?php
                esc_html_e(
                    'Tagesroutinen',
                    'jung-leben'
                );
                ?>
            </p>


            <h1>
                <?php
                the_title();
                ?>
            </h1>


            <p class="routines-intro__lead">
                <?php
                esc_html_e(
                    'Nicht jede Empfehlung braucht einen festen Platz im Alltag. Hier findest du eine einfache Orientierung, welche Produkte Roberto bewusst mit bestimmten Tageszeiten verbindet.',
                    'jung-leben'
                );
                ?>
            </p>

        </div>

    </section>


    <!-- =====================================================
         ROUTINEN
         ===================================================== -->

    <div class="routines-sections">

        <?php
        foreach (
            $routines
            as $routine_key => $routine
        ) :
            $routine_products =
                array_slice(
                    $products_by_routine[
                        $routine_key
                    ],
                    0,
                    3
                );


            $routine_url =
                add_query_arg(
                    'routine',
                    $routine_key,
                    $recommendations_url
                );
            ?>

            <section
                class="
                    routine-section
                    routine-section--<?php
                    echo esc_attr(
                        $routine[
                            'modifier'
                        ]
                    );
                    ?>
                "
            >

                <div class="container">

                    <!-- =========================================
                         HEADER
                         ========================================= -->

                    <div class="routine-section__header">

                        <div class="routine-section__identity">

                            <span
                                class="routine-section__symbol"
                                aria-hidden="true"
                            >
                                <?php
                                echo esc_html(
                                    $routine[
                                        'symbol'
                                    ]
                                );
                                ?>
                            </span>


                            <div>

                                <p class="routine-section__eyebrow">
                                    <?php
                                    echo esc_html(
                                        $routine[
                                            'eyebrow'
                                        ]
                                    );
                                    ?>
                                </p>


                                <h2>
                                    <?php
                                    echo esc_html(
                                        $routine[
                                            'title'
                                        ]
                                    );
                                    ?>
                                </h2>

                            </div>

                        </div>


                        <div class="routine-section__intro">

                            <p>
                                <?php
                                echo esc_html(
                                    $routine[
                                        'text'
                                    ]
                                );
                                ?>
                            </p>


                            <a
                                href="<?php
                                echo esc_url(
                                    $routine_url
                                );
                                ?>"
                                class="routine-section__all-link"
                            >

                                <span>
                                    <?php
                                    echo esc_html(
                                        $routine[
                                            'link_label'
                                        ]
                                    );
                                    ?>
                                </span>


                                <span
                                    aria-hidden="true"
                                    class="routine-section__all-arrow"
                                >
                                    →
                                </span>

                            </a>

                        </div>

                    </div>


                    <!-- =========================================
                         PRODUKTE
                         ========================================= -->

                    <?php
                    if (
                        ! empty(
                            $routine_products
                        )
                    ) :
                        ?>

                        <div class="routine-products">

                            <?php
                            foreach (
                                $routine_products
                                as $product
                            ) :
                                $product_id =
                                    (int)
                                    $product->ID;


                                $product_url =
                                    get_permalink(
                                        $product
                                    );


                                if (
                                    ! is_string(
                                        $product_url
                                    )
                                ) {
                                    continue;
                                }


                                /* =================================
                                   MARKE
                                   ================================= */

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
                                                    'compact'
                                                );
                                    } else {
                                        $brand_markup =
                                            '<span class="jl-brand jl-brand--compact">'
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
                                        '<span class="jl-brand jl-brand--compact">'
                                        . '<span class="jl-brand__name">'
                                        . esc_html__(
                                            'Jung Leben',
                                            'jung-leben'
                                        )
                                        . '</span>'
                                        . '</span>';
                                }


                                /* =================================
                                   HIGHLIGHT / GETESTET
                                   ================================= */

                                $featured =
                                    (bool)
                                    $get_product_field(
                                        'jl_product_featured',
                                        $product_id,
                                        false
                                    );


                                $personally_tested =
                                    (bool)
                                    $get_product_field(
                                        'jl_product_personally_tested',
                                        $product_id,
                                        false
                                    );
                                ?>

                                <article class="routine-product">

                                    <div class="routine-product__card">

                                        <!-- =====================
                                             MARKE / BADGE
                                             ===================== -->

                                        <div class="routine-product__top">

                                            <div class="routine-product__brand">
                                                <?php
                                                echo wp_kses_post(
                                                    $brand_markup
                                                );
                                                ?>
                                            </div>


                                            <?php
                                            if (
                                                $featured
                                            ) :
                                                ?>

                                                <span class="routine-product__badge">
                                                    <?php
                                                    esc_html_e(
                                                        'Highlight',
                                                        'jung-leben'
                                                    );
                                                    ?>
                                                </span>

                                            <?php elseif (
                                                $personally_tested
                                            ) : ?>

                                                <span class="routine-product__badge">
                                                    <?php
                                                    esc_html_e(
                                                        'Getestet',
                                                        'jung-leben'
                                                    );
                                                    ?>
                                                </span>

                                            <?php endif; ?>

                                        </div>


                                        <!-- =====================
                                             PRODUKTNAME
                                             ===================== -->

                                        <h3 class="routine-product__title">

                                            <a
                                                href="<?php
                                                echo esc_url(
                                                    $product_url
                                                );
                                                ?>"
                                                class="routine-product__title-link"
                                            >
                                                <?php
                                                echo esc_html(
                                                    $product
                                                        ->post_title
                                                );
                                                ?>
                                            </a>

                                        </h3>


                                        <!-- =====================
                                             PRODUKT-CTA
                                             ===================== -->

                                        <a
                                            href="<?php
                                            echo esc_url(
                                                $product_url
                                            );
                                            ?>"
                                            class="routine-product__footer"
                                        >

                                            <span>
                                                <?php
                                                esc_html_e(
                                                    'Produkt ansehen',
                                                    'jung-leben'
                                                );
                                                ?>
                                            </span>


                                            <span
                                                class="routine-product__arrow"
                                                aria-hidden="true"
                                            >
                                                →
                                            </span>

                                        </a>

                                    </div>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    <?php else : ?>

                        <div class="routine-empty">

                            <span
                                class="routine-empty__mark"
                                aria-hidden="true"
                            >
                                +
                            </span>


                            <div>

                                <strong>
                                    <?php
                                    esc_html_e(
                                        'Empfehlungen folgen.',
                                        'jung-leben'
                                    );
                                    ?>
                                </strong>


                                <span>
                                    <?php
                                    esc_html_e(
                                        'Für diese Tageszeit werden aktuell noch passende Produkte zusammengestellt.',
                                        'jung-leben'
                                    );
                                    ?>
                                </span>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        <?php endforeach; ?>

    </div>

</main>

<?php
get_footer();