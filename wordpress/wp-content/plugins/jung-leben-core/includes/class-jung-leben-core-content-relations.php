<?php
/**
 * Frontend-Verknüpfungen zwischen
 * Produkten und Erfahrungsbeiträgen.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Stellt Beziehungen zwischen Produkten und
 * den dazugehörigen Erfahrungsartikeln dar.
 */
final class Jung_Leben_Core_Content_Relations
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
            'wp_enqueue_scripts',
            [
                self::class,
                'enqueue_assets',
            ]
        );


        add_filter(
            'the_content',
            [
                self::class,
                'append_relations',
            ],
            30
        );
    }


    /* =========================================================
       ASSETS
       ========================================================= */

    /**
     * Styles nur dort laden, wo
     * Verknüpfungen vorkommen können.
     */
    public static function enqueue_assets(): void
    {
        if (
            ! is_singular(
                [
                    Jung_Leben_Core_Products::POST_TYPE,
                    'post',
                ]
            )
        ) {
            return;
        }


        $css_path =
            JUNG_LEBEN_CORE_PATH
            . 'assets/css/content-relations.css';


        if (
            ! file_exists(
                $css_path
            )
        ) {
            return;
        }


        wp_enqueue_style(
            'jung-leben-content-relations',
            JUNG_LEBEN_CORE_URL
                . 'assets/css/content-relations.css',
            [],
            (string)
            filemtime(
                $css_path
            )
        );
    }


    /* =========================================================
       CONTENT-FILTER
       ========================================================= */

    /**
     * Passende Verknüpfung nach dem eigentlichen
     * Inhalt ergänzen.
     */
    public static function append_relations(
        string $content
    ): string {
        if (
            is_admin()
            || ! is_singular()
            || ! in_the_loop()
            || ! is_main_query()
        ) {
            return $content;
        }


        $post_id =
            get_the_ID();


        if (
            $post_id <= 0
        ) {
            return $content;
        }


        $post_type =
            get_post_type(
                $post_id
            );


        /* =====================================================
           PRODUKT → ERFAHRUNG
           ===================================================== */

        if (
            $post_type ===
            Jung_Leben_Core_Products::POST_TYPE
        ) {
            $relation =
                self::render_product_experiences(
                    $post_id
                );


            if (
                $relation === ''
            ) {
                return $content;
            }


            return
                $content
                . $relation;
        }


        /* =====================================================
           ERFAHRUNG → PRODUKT
           ===================================================== */

        if (
            $post_type ===
            'post'
        ) {
            $relation =
                self::render_experience_product(
                    $post_id
                );


            if (
                $relation === ''
            ) {
                return $content;
            }


            return
                $content
                . $relation;
        }


        return $content;
    }


    /* =========================================================
       PRODUKT → ERFAHRUNGEN
       ========================================================= */

    /**
     * Mit einem Produkt verknüpfte
     * Erfahrungsartikel ausgeben.
     */
    private static function render_product_experiences(
        int $product_id
    ): string {
        $statuses = [
            'publish',
        ];


        /**
         * Redakteure dürfen auch noch nicht
         * veröffentlichte Erfahrungen sehen.
         */
        if (
            is_user_logged_in()
            && current_user_can(
                'edit_posts'
            )
        ) {
            $statuses = [
                'publish',
                'draft',
                'pending',
                'private',
                'future',
            ];
        }


        $experience_query =
            new WP_Query([
                'post_type' =>
                    'post',

                'post_status' =>
                    $statuses,

                'posts_per_page' =>
                    3,

                'orderby' =>
                    'date',

                'order' =>
                    'DESC',

                'meta_query' => [
                    [
                        'key' =>
                            'jl_experience_related_product',

                        'value' =>
                            $product_id,

                        'compare' =>
                            '=',
                    ],
                ],

                'no_found_rows' =>
                    true,
            ]);


        if (
            ! $experience_query->have_posts()
        ) {
            return '';
        }


        $cards = '';


        foreach (
            $experience_query->posts
            as $experience
        ) {
            if (
                ! $experience
                instanceof WP_Post
            ) {
                continue;
            }


            $experience_url =
                self::get_frontend_url(
                    $experience
                );


            if (
                $experience_url === ''
            ) {
                continue;
            }


            $cards .=
                self::render_experience_card(
                    $experience,
                    $experience_url
                );
        }


        wp_reset_postdata();


        if (
            $cards === ''
        ) {
            return '';
        }


        return
            sprintf(
                '<section class="jl-relation jl-relation--experience">
                    <div class="jl-relation__inner">
                        <header class="jl-relation__header">
                            <span class="jl-relation__eyebrow">%1$s</span>
                            <h2>%2$s</h2>
                        </header>
                        <div class="jl-relation__cards">
                            %3$s
                        </div>
                    </div>
                </section>',
                esc_html__(
                    'Persönliche Erfahrung',
                    'jung-leben-core'
                ),
                esc_html(
                    sprintf(
                        __(
                            'Robertos Erfahrung mit %s',
                            'jung-leben-core'
                        ),
                        get_the_title(
                            $product_id
                        )
                    )
                ),
                $cards
            );
    }


    /* =========================================================
       ERFAHRUNG → PRODUKT
       ========================================================= */

    /**
     * Das mit einem Erfahrungsartikel
     * verknüpfte Produkt ausgeben.
     */
    private static function render_experience_product(
        int $experience_id
    ): string {
        $product_id =
            absint(
                get_post_meta(
                    $experience_id,
                    'jl_experience_related_product',
                    true
                )
            );


        if (
            $product_id <= 0
        ) {
            return '';
        }


        $product =
            get_post(
                $product_id
            );


        if (
            ! $product
            instanceof WP_Post
            || $product->post_type !==
                Jung_Leben_Core_Products::POST_TYPE
        ) {
            return '';
        }


        $product_url =
            self::get_frontend_url(
                $product
            );


        if (
            $product_url === ''
        ) {
            return '';
        }


        $card =
            self::render_product_card(
                $product,
                $product_url
            );


        if (
            $card === ''
        ) {
            return '';
        }


        return
            sprintf(
                '<section class="jl-relation jl-relation--product">
                    <div class="jl-relation__inner">
                        <header class="jl-relation__header">
                            <span class="jl-relation__eyebrow">%1$s</span>
                            <h2>%2$s</h2>
                        </header>
                        <div class="jl-relation__cards">
                            %3$s
                        </div>
                    </div>
                </section>',
                esc_html__(
                    'Passendes Produkt',
                    'jung-leben-core'
                ),
                esc_html__(
                    'Das Produkt im Überblick.',
                    'jung-leben-core'
                ),
                $card
            );
    }


    /* =========================================================
       PRODUKTKARTE
       ========================================================= */

    /**
     * Produktkarte innerhalb eines
     * Erfahrungsartikels rendern.
     */
    private static function render_product_card(
        WP_Post $product,
        string $product_url
    ): string {
        $product_id =
            (int)
            $product->ID;


        $has_image =
            has_post_thumbnail(
                $product_id
            );


        $card_classes = [
            'jl-relation-card',
            'jl-relation-card--product',
            $has_image
                ? 'jl-relation-card--with-image'
                : 'jl-relation-card--no-image',
        ];


        /* =====================================================
           MARKE
           ===================================================== */

        $brand_markup =
            self::get_product_brand_markup(
                $product_id
            );


        /* =====================================================
           BILD
           ===================================================== */

        $media_html = '';


        if (
            $has_image
        ) {
            $image =
                get_the_post_thumbnail(
                    $product_id,
                    'medium_large',
                    [
                        'class' =>
                            'jl-relation-card__image',

                        'loading' =>
                            'lazy',
                    ]
                );


            if (
                is_string(
                    $image
                )
                && $image !== ''
            ) {
                $media_html =
                    sprintf(
                        '<a
                            href="%1$s"
                            class="jl-relation-card__media"
                            aria-label="%2$s"
                        >
                            %3$s
                        </a>',
                        esc_url(
                            $product_url
                        ),
                        esc_attr(
                            sprintf(
                                __(
                                    '%s ansehen',
                                    'jung-leben-core'
                                ),
                                $product
                                    ->post_title
                            )
                        ),
                        $image
                    );
            }
        }


        /* =====================================================
           BESCHREIBUNG
           ===================================================== */

        $excerpt =
            self::get_content_excerpt(
                $product
            );


        $excerpt_html =
            $excerpt !== ''
                ? sprintf(
                    '<p class="jl-relation-card__excerpt">%s</p>',
                    esc_html(
                        $excerpt
                    )
                )
                : '';


        return
            sprintf(
                '<article class="%1$s">
                    %2$s

                    <div class="jl-relation-card__content">

                        <div class="jl-relation-card__brand">
                            %3$s
                        </div>

                        <h3 class="jl-relation-card__title">
                            <a href="%4$s">
                                %5$s
                            </a>
                        </h3>

                        %6$s

                        <a
                            href="%4$s"
                            class="jl-relation-card__link"
                        >
                            <span>%7$s</span>

                            <span
                                class="jl-relation-card__link-arrow"
                                aria-hidden="true"
                            >
                                →
                            </span>
                        </a>

                    </div>
                </article>',
                esc_attr(
                    implode(
                        ' ',
                        $card_classes
                    )
                ),
                $media_html,
                $brand_markup,
                esc_url(
                    $product_url
                ),
                esc_html(
                    $product
                        ->post_title
                ),
                $excerpt_html,
                esc_html__(
                    'Produkt ansehen',
                    'jung-leben-core'
                )
            );
    }


    /* =========================================================
       ERFAHRUNGSKARTE
       ========================================================= */

    /**
     * Erfahrungsartikel innerhalb
     * einer Produktseite rendern.
     */
    private static function render_experience_card(
        WP_Post $experience,
        string $experience_url
    ): string {
        $experience_id =
            (int)
            $experience->ID;


        $has_image =
            has_post_thumbnail(
                $experience_id
            );


        $card_classes = [
            'jl-relation-card',
            'jl-relation-card--experience',
            $has_image
                ? 'jl-relation-card--with-image'
                : 'jl-relation-card--no-image',
        ];


        /* =====================================================
           BILD
           ===================================================== */

        $media_html = '';


        if (
            $has_image
        ) {
            $image =
                get_the_post_thumbnail(
                    $experience_id,
                    'medium_large',
                    [
                        'class' =>
                            'jl-relation-card__image',

                        'loading' =>
                            'lazy',
                    ]
                );


            if (
                is_string(
                    $image
                )
                && $image !== ''
            ) {
                $media_html =
                    sprintf(
                        '<a
                            href="%1$s"
                            class="jl-relation-card__media"
                            aria-label="%2$s"
                        >
                            %3$s
                        </a>',
                        esc_url(
                            $experience_url
                        ),
                        esc_attr(
                            sprintf(
                                __(
                                    '%s lesen',
                                    'jung-leben-core'
                                ),
                                $experience
                                    ->post_title
                            )
                        ),
                        $image
                    );
            }
        }


        /* =====================================================
           BESCHREIBUNG
           ===================================================== */

        $excerpt =
            self::get_content_excerpt(
                $experience
            );


        $excerpt_html =
            $excerpt !== ''
                ? sprintf(
                    '<p class="jl-relation-card__excerpt">%s</p>',
                    esc_html(
                        $excerpt
                    )
                )
                : '';


        return
            sprintf(
                '<article class="%1$s">
                    %2$s

                    <div class="jl-relation-card__content">

                        <span class="jl-relation-card__label">
                            %3$s
                        </span>

                        <h3 class="jl-relation-card__title">
                            <a href="%4$s">
                                %5$s
                            </a>
                        </h3>

                        %6$s

                        <a
                            href="%4$s"
                            class="jl-relation-card__link"
                        >
                            <span>%7$s</span>

                            <span
                                class="jl-relation-card__link-arrow"
                                aria-hidden="true"
                            >
                                →
                            </span>
                        </a>

                    </div>
                </article>',
                esc_attr(
                    implode(
                        ' ',
                        $card_classes
                    )
                ),
                $media_html,
                esc_html__(
                    'Erfahrungsbericht',
                    'jung-leben-core'
                ),
                esc_url(
                    $experience_url
                ),
                esc_html(
                    $experience
                        ->post_title
                ),
                $excerpt_html,
                esc_html__(
                    'Erfahrung lesen',
                    'jung-leben-core'
                )
            );
    }


    /* =========================================================
       MARKENAUSGABE
       ========================================================= */

    /**
     * Zentrale Marken-/Logo-Ausgabe
     * für ein Produkt.
     */
    private static function get_product_brand_markup(
        int $product_id
    ): string {
        $brands =
            get_the_terms(
                $product_id,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        if (
            ! is_array(
                $brands
            )
            || is_wp_error(
                $brands
            )
            || empty(
                $brands
            )
        ) {
            return
                '<span class="jl-brand jl-brand--relation">'
                . '<span class="jl-brand__name">'
                . esc_html__(
                    'Jung Leben',
                    'jung-leben-core'
                )
                . '</span>'
                . '</span>';
        }


        $brand =
            $brands[0];


        if (
            ! $brand
            instanceof WP_Term
        ) {
            return '';
        }


        /**
         * Neue zentrale Markenlogik verwenden.
         */
        if (
            class_exists(
                'Jung_Leben_Core_Brand_Fields'
            )
        ) {
            return
                Jung_Leben_Core_Brand_Fields
                    ::render_frontend_brand(
                        $brand,
                        'relation'
                    );
        }


        /**
         * Fallback, falls die Markenklasse
         * aus irgendeinem Grund nicht verfügbar ist.
         */
        return
            '<span class="jl-brand jl-brand--relation">'
            . '<span class="jl-brand__name">'
            . esc_html(
                $brand->name
            )
            . '</span>'
            . '</span>';
    }


    /* =========================================================
       FRONTEND-URL
       ========================================================= */

    /**
     * Öffentliche oder – für Redakteure –
     * Vorschau-URL ermitteln.
     */
    private static function get_frontend_url(
        WP_Post $post
    ): string {
        if (
            $post->post_status ===
            'publish'
        ) {
            $url =
                get_permalink(
                    $post
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
                $post->ID
            )
        ) {
            return '';
        }


        $preview_url =
            get_preview_post_link(
                $post
            );


        if (
            is_string(
                $preview_url
            )
            && $preview_url !== ''
        ) {
            return $preview_url;
        }


        $permalink =
            get_permalink(
                $post
            );


        return
            is_string(
                $permalink
            )
                ? $permalink
                : '';
    }


    /* =========================================================
       EXCERPT
       ========================================================= */

    /**
     * Kurzen Kartentext erstellen.
     */
    private static function get_content_excerpt(
        WP_Post $post
    ): string {
        $excerpt =
            trim(
                (string)
                $post->post_excerpt
            );


        if (
            $excerpt !== ''
        ) {
            return
                wp_trim_words(
                    wp_strip_all_tags(
                        $excerpt
                    ),
                    28,
                    ' …'
                );
        }


        $content =
            strip_shortcodes(
                $post->post_content
            );


        $content =
            wp_strip_all_tags(
                $content
            );


        $content =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    $content
                )
                ?? ''
            );


        if (
            $content === ''
        ) {
            return '';
        }


        return
            wp_trim_words(
                $content,
                28,
                ' …'
            );
    }
}