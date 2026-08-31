<?php
/**
 * Einzelansicht einer Jung-Leben-Erfahrung.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/* =========================================================
   CSS DER EINZELANSICHT LADEN
   ========================================================= */

$experience_css_path =
    get_template_directory()
    . '/assets/css/experience-single.css';


if (
    file_exists(
        $experience_css_path
    )
) {
    wp_enqueue_style(
        'jung-leben-experience-single',
        get_template_directory_uri()
            . '/assets/css/experience-single.css',
        [
            'jung-leben-site',
        ],
        (string)
        filemtime(
            $experience_css_path
        )
    );
}


/* =========================================================
   HILFSFUNKTION: LESEDAUER
   ========================================================= */

$jung_leben_get_reading_time =
    static function (
        int $post_id
    ): int {
        $content =
            (string)
            get_post_field(
                'post_content',
                $post_id
            );


        $content =
            wp_strip_all_tags(
                strip_shortcodes(
                    $content
                )
            );


        preg_match_all(
            '/\p{L}+/u',
            $content,
            $matches
        );


        $word_count =
            isset(
                $matches[0]
            )
                ? count(
                    $matches[0]
                )
                : 0;


        return
            max(
                1,
                (int)
                ceil(
                    $word_count / 200
                )
            );
    };


/* =========================================================
   URLS
   ========================================================= */

$experience_page_id =
    (int)
    get_option(
        'page_for_posts'
    );


$experience_archive_url =
    $experience_page_id > 0
        ? get_permalink(
            $experience_page_id
        )
        : home_url(
            '/ratgeber/'
        );


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


get_header();


/* =========================================================
   LOOP
   ========================================================= */

while (
    have_posts()
) :
    the_post();


    $post_id =
        (int)
        get_the_ID();


    $reading_time =
        $jung_leben_get_reading_time(
            $post_id
        );


    /* =====================================================
       THEMA
       ===================================================== */

    $source_topic = '';


    if (
        function_exists(
            'get_field'
        )
    ) {
        $source_topic =
            trim(
                (string)
                get_field(
                    'jl_experience_source_topic',
                    $post_id
                )
            );
    }


    if (
        $source_topic === ''
    ) {
        $source_topic =
            trim(
                (string)
                get_post_meta(
                    $post_id,
                    'jl_experience_source_topic',
                    true
                )
            );
    }


    /* =====================================================
       VERKNÜPFTES PRODUKT
       ===================================================== */

    $related_product_id = 0;


    if (
        function_exists(
            'get_field'
        )
    ) {
        $related_product_id =
            absint(
                get_field(
                    'jl_experience_related_product',
                    $post_id
                )
            );
    }


    if (
        $related_product_id <= 0
    ) {
        $related_product_id =
            absint(
                get_post_meta(
                    $post_id,
                    'jl_experience_related_product',
                    true
                )
            );
    }


    $related_product = null;

    $related_product_url = '';

    $related_product_excerpt = '';

    $related_product_purpose = '';

    $related_product_brand = null;

    $related_product_categories = [];

    $related_product_has_image = false;


    if (
        $related_product_id > 0
    ) {
        $candidate =
            get_post(
                $related_product_id
            );


        if (
            $candidate
            instanceof WP_Post
        ) {
            $product_status =
                get_post_status(
                    $related_product_id
                );


            $can_preview_product =
                current_user_can(
                    'edit_post',
                    $related_product_id
                );


            if (
                $product_status === 'publish'
                || $can_preview_product
            ) {
                $related_product =
                    $candidate;


                if (
                    $product_status === 'publish'
                ) {
                    $related_product_url =
                        (string)
                        get_permalink(
                            $related_product_id
                        );
                } else {
                    $preview_link =
                        get_preview_post_link(
                            $related_product_id
                        );


                    $related_product_url =
                        is_string(
                            $preview_link
                        )
                            ? $preview_link
                            : (
                                (string)
                                get_permalink(
                                    $related_product_id
                                )
                            );
                }


                $related_product_excerpt =
                    trim(
                        (string)
                        $related_product->post_excerpt
                    );


                if (
                    function_exists(
                        'get_field'
                    )
                ) {
                    $related_product_purpose =
                        trim(
                            (string)
                            get_field(
                                'jl_product_purpose',
                                $related_product_id
                            )
                        );
                }


                $brands =
                    get_the_terms(
                        $related_product_id,
                        'jl_product_brand'
                    );


                if (
                    is_array(
                        $brands
                    )
                    && ! empty(
                        $brands
                    )
                    && $brands[0]
                        instanceof WP_Term
                ) {
                    $related_product_brand =
                        $brands[0];
                }


                $categories =
                    get_the_terms(
                        $related_product_id,
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
                    $related_product_categories =
                        $categories;
                }


                $related_product_has_image =
                    has_post_thumbnail(
                        $related_product_id
                    );
            }
        }
    }


    /* =====================================================
       ARTIKELINHALT

       Bewusst ohne "the_content"-Filter:
       So kontrolliert dieses Template die Produktintegration
       vollständig und verhindert doppelte Relations-Karten.
       Gutenberg-Blöcke werden weiterhin normal gerendert.
       ===================================================== */

    $raw_content =
        (string)
        get_post_field(
            'post_content',
            $post_id
        );


    if (
        has_blocks(
            $raw_content
        )
    ) {
        $rendered_content =
            do_blocks(
                $raw_content
            );
    } else {
        $rendered_content =
            wpautop(
                $raw_content
            );


        $rendered_content =
            do_shortcode(
                $rendered_content
            );
    }
    ?>

    <main
        id="main-content"
        class="experience-single"
    >

        <!-- =================================================
             HERO
             ================================================= -->

        <section class="experience-single__hero">

            <div class="experience-single__container">

                <nav
                    class="experience-single__breadcrumb"
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
                            $experience_archive_url
                        );
                        ?>"
                    >
                        <span aria-hidden="true">
                            ←
                        </span>

                        Alle Erfahrungen
                    </a>

                </nav>


                <div class="experience-single__hero-grid">

                    <div class="experience-single__hero-main">

                        <div class="experience-single__meta">

                            <span class="experience-single__meta-type">
                                Erfahrung
                            </span>


                            <span aria-hidden="true">
                                ·
                            </span>


                            <span>
                                <?php
                                echo esc_html(
                                    sprintf(
                                        '%d Min. Lesezeit',
                                        $reading_time
                                    )
                                );
                                ?>
                            </span>

                        </div>


                        <h1 class="experience-single__title">
                            <?php
                            the_title();
                            ?>
                        </h1>

                    </div>


                    <div
                        class="experience-single__hero-mark"
                        aria-hidden="true"
                    >

                        <span>
                            E
                        </span>

                    </div>

                </div>

            </div>

        </section>


        <!-- =================================================
             OPTIONAL: BEITRAGSBILD
             ================================================= -->

        <?php
        if (
            has_post_thumbnail(
                $post_id
            )
        ) :
            ?>

            <section class="experience-single__featured">

                <div class="experience-single__container">

                    <figure class="experience-single__featured-frame">

                        <?php
                        the_post_thumbnail(
                            'full',
                            [
                                'class' =>
                                    'experience-single__featured-image',

                                'loading' =>
                                    'eager',
                            ]
                        );
                        ?>

                    </figure>

                </div>

            </section>

        <?php endif; ?>


        <!-- =================================================
             ARTIKEL
             ================================================= -->

        <section class="experience-single__article-section">

            <div class="experience-single__container">

                <div class="experience-single__layout">

                    <!-- =====================================
                         HAUPTINHALT
                         ===================================== -->

                    <article
                        <?php
                        post_class(
                            'experience-single__article'
                        );
                        ?>
                    >

                        <div class="experience-single__content">

                            <?php
                            echo $rendered_content;
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>

                        </div>

                    </article>


                    <!-- =====================================
                         EDITORIAL RAIL
                         ===================================== -->

                    <aside class="experience-single__rail">

                        <div class="experience-single__rail-card">

                            <p class="experience-single__rail-label">
                                Auf einen Blick
                            </p>


                            <dl class="experience-single__facts">

                                <div>

                                    <dt>
                                        Format
                                    </dt>

                                    <dd>
                                        Erfahrung
                                    </dd>

                                </div>


                                <div>

                                    <dt>
                                        Lesezeit
                                    </dt>

                                    <dd>
                                        <?php
                                        echo esc_html(
                                            sprintf(
                                                '%d Min.',
                                                $reading_time
                                            )
                                        );
                                        ?>
                                    </dd>

                                </div>


                                <?php
                                if (
                                    $source_topic !== ''
                                ) :
                                    ?>

                                    <div>

                                        <dt>
                                            Thema
                                        </dt>

                                        <dd>
                                            <?php
                                            echo esc_html(
                                                $source_topic
                                            );
                                            ?>
                                        </dd>

                                    </div>

                                <?php endif; ?>

                            </dl>

                        </div>


                        <?php
                        if (
                            $related_product
                            instanceof WP_Post
                        ) :
                            ?>

                            <div class="experience-single__rail-product">

                                <span>
                                    Passend zur Erfahrung
                                </span>


                                <strong>
                                    <?php
                                    echo esc_html(
                                        $related_product->post_title
                                    );
                                    ?>
                                </strong>


                                <a
                                    href="<?php
                                    echo esc_url(
                                        $related_product_url
                                    );
                                    ?>"
                                >
                                    Produkt ansehen

                                    <span aria-hidden="true">
                                        →
                                    </span>
                                </a>

                            </div>

                        <?php endif; ?>

                    </aside>

                </div>

            </div>

        </section>


        <!-- =================================================
             PASSENDES PRODUKT
             ================================================= -->

        <?php
        if (
            $related_product
            instanceof WP_Post
        ) :
            ?>

            <section class="experience-single__product-section">

                <div class="experience-single__container">

                    <header class="experience-single__section-heading">

                        <p class="experience-single__eyebrow">
                            Passend zur Erfahrung
                        </p>


                        <h2>
                            Das Produkt im Überblick.
                        </h2>


                        <p>
                            Die Erfahrung steht im Vordergrund.
                            Wenn ein Produkt thematisch dazugehört,
                            findest du hier die passende Einordnung.
                        </p>

                    </header>


                    <article
                        class="
                            experience-related-product
                            <?php
                            echo
                                $related_product_has_image
                                    ? 'experience-related-product--has-image'
                                    : 'experience-related-product--no-image';
                            ?>
                        "
                    >

                        <?php
                        if (
                            $related_product_has_image
                        ) :
                            ?>

                            <a
                                href="<?php
                                echo esc_url(
                                    $related_product_url
                                );
                                ?>"
                                class="experience-related-product__media"
                                aria-label="<?php
                                echo esc_attr(
                                    sprintf(
                                        '%s ansehen',
                                        $related_product->post_title
                                    )
                                );
                                ?>"
                            >

                                <?php
                                echo get_the_post_thumbnail(
                                    $related_product_id,
                                    'large',
                                    [
                                        'class' =>
                                            'experience-related-product__image',

                                        'loading' =>
                                            'lazy',
                                    ]
                                );
                                ?>

                            </a>

                        <?php endif; ?>


                        <div class="experience-related-product__content">

                            <div>

                                <?php
                                if (
                                    $related_product_brand
                                    instanceof WP_Term
                                ) :
                                    ?>

                                    <div class="experience-related-product__brand">

                                        <?php
                                        if (
                                            class_exists(
                                                'Jung_Leben_Core_Brand_Fields'
                                            )
                                            && method_exists(
                                                'Jung_Leben_Core_Brand_Fields',
                                                'render_frontend_brand'
                                            )
                                        ) {
                                            echo wp_kses_post(
                                                Jung_Leben_Core_Brand_Fields::render_frontend_brand(
                                                    $related_product_brand,
                                                    'compact'
                                                )
                                            );
                                        } else {
                                            echo esc_html(
                                                $related_product_brand->name
                                            );
                                        }
                                        ?>

                                    </div>

                                <?php endif; ?>


                                <h3>
                                    <?php
                                    echo esc_html(
                                        $related_product->post_title
                                    );
                                    ?>
                                </h3>


                                <?php
                                $product_description =
                                    $related_product_purpose !== ''
                                        ? $related_product_purpose
                                        : $related_product_excerpt;


                                if (
                                    $product_description !== ''
                                ) :
                                    ?>

                                    <p class="experience-related-product__description">

                                        <?php
                                        echo esc_html(
                                            $product_description
                                        );
                                        ?>

                                    </p>

                                <?php endif; ?>


                                <?php
                                if (
                                    ! empty(
                                        $related_product_categories
                                    )
                                ) :
                                    ?>

                                    <div class="experience-related-product__categories">

                                        <?php
                                        foreach (
                                            array_slice(
                                                $related_product_categories,
                                                0,
                                                3
                                            )
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

                                <?php endif; ?>

                            </div>


                            <a
                                href="<?php
                                echo esc_url(
                                    $related_product_url
                                );
                                ?>"
                                class="experience-related-product__button"
                            >
                                Produkt ansehen

                                <span aria-hidden="true">
                                    →
                                </span>
                            </a>

                        </div>

                    </article>

                </div>

            </section>

        <?php endif; ?>


        <!-- =================================================
             GESUNDHEITSHINWEIS
             ================================================= -->

        <section class="experience-single__notice-section">

            <div class="experience-single__container">

                <div class="experience-single__health-notice">

                    <div
                        class="experience-single__health-icon"
                        aria-hidden="true"
                    >
                        i
                    </div>


                    <div>

                        <strong>
                            Hinweis zu gesundheitlichen Inhalten
                        </strong>


                        <p>
                            Die Inhalte auf Jung Leben dienen der
                            persönlichen Einordnung und allgemeinen
                            Information. Sie ersetzen keine medizinische
                            Beratung, Diagnose oder Behandlung.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- =================================================
             WEITER ENTDECKEN
             ================================================= -->

        <section class="experience-single__next">

            <div class="experience-single__container">

                <div class="experience-single__next-grid">

                    <div>

                        <p class="experience-single__eyebrow">
                            Weiter entdecken
                        </p>


                        <h2>
                            Noch mehr Erfahrungen und
                            bewusste Empfehlungen.
                        </h2>

                    </div>


                    <div class="experience-single__next-actions">

                        <a
                            href="<?php
                            echo esc_url(
                                $experience_archive_url
                            );
                            ?>"
                            class="experience-single__button experience-single__button--secondary"
                        >
                            <span aria-hidden="true">
                                ←
                            </span>

                            Alle Erfahrungen
                        </a>


                        <a
                            href="<?php
                            echo esc_url(
                                $recommendations_url
                            );
                            ?>"
                            class="experience-single__button"
                        >
                            Empfehlungen entdecken

                            <span aria-hidden="true">
                                →
                            </span>
                        </a>

                    </div>

                </div>

            </div>

        </section>

    </main>

    <?php

endwhile;


get_footer();