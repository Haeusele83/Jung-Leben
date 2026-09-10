<?php
/**
 * Frontend-Verknüpfungen zwischen Produkten und Beiträgen.
 *
 * Nur bestätigte persönliche Quellen werden als
 * «Robertos Erfahrung» bezeichnet. KI-markierte oder
 * redaktionell geprüfte Beiträge erscheinen als
 * Hintergrund & Einordnung.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

final class Jung_Leben_Core_Content_Relations
{
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

        if (! file_exists($css_path)) {
            return;
        }

        wp_enqueue_style(
            'jung-leben-content-relations',
            JUNG_LEBEN_CORE_URL
                . 'assets/css/content-relations.css',
            [],
            (string) filemtime(
                $css_path
            )
        );
    }

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

        if ($post_id <= 0) {
            return $content;
        }

        $post_type =
            get_post_type(
                $post_id
            );

        /* =====================================================
           PRODUKT → BEITRÄGE
           ===================================================== */

        if (
            $post_type
            === Jung_Leben_Core_Products::POST_TYPE
        ) {
            return
                $content
                . self::render_product_experiences(
                    $post_id
                );
        }

        /* =====================================================
           BEITRAG → PRODUKT
           ===================================================== */

        if ($post_type === 'post') {
            return
                $content
                . self::render_experience_product(
                    $post_id
                );
        }

        return $content;
    }

    /* =========================================================
       PRODUKT → BEITRÄGE
       ========================================================= */

    private static function render_product_experiences(
        int $product_id
    ): string {
        $statuses = [
            'publish',
        ];

        /**
         * Eingeloggte Redakteure dürfen auch Entwürfe sehen.
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

        $query =
            new WP_Query([
                'post_type' =>
                    'post',

                'post_status' =>
                    $statuses,

                'posts_per_page' =>
                    6,

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

        if (! $query->have_posts()) {
            return '';
        }

        $personal_cards = '';
        $background_cards = '';

        foreach (
            $query->posts
            as $experience
        ) {
            if (
                ! $experience
                instanceof WP_Post
            ) {
                continue;
            }

            $url =
                self::get_frontend_url(
                    $experience
                );

            if ($url === '') {
                continue;
            }

            /**
             * Entscheidend:
             *
             * Nicht jeder Blogbeitrag ist automatisch
             * eine persönliche Erfahrung.
             */
            $type =
                self::get_experience_relation_type(
                    $experience
                );

            $card =
                self::render_experience_card(
                    $experience,
                    $url,
                    $type
                );

            if ($type === 'personal') {
                $personal_cards .= $card;

            } else {
                $background_cards .= $card;
            }
        }

        wp_reset_postdata();

        $product_title =
            get_the_title(
                $product_id
            );

        $html = '';

        /* -----------------------------------------------------
           Persönliche Erfahrungen
           ----------------------------------------------------- */

        if ($personal_cards !== '') {
            $html .=
                self::render_relation_section(
                    'personal',

                    __(
                        'Persönliche Erfahrung',
                        'jung-leben-core'
                    ),

                    sprintf(
                        __(
                            'Robertos Erfahrung mit %s',
                            'jung-leben-core'
                        ),
                        $product_title
                    ),

                    $personal_cards
                );
        }

        /* -----------------------------------------------------
           Hintergrund / Einordnung
           ----------------------------------------------------- */

        if ($background_cards !== '') {
            $html .=
                self::render_relation_section(
                    'background',

                    __(
                        'Hintergrund & Einordnung',
                        'jung-leben-core'
                    ),

                    sprintf(
                        __(
                            'Mehr über %s',
                            'jung-leben-core'
                        ),
                        $product_title
                    ),

                    $background_cards
                );
        }

        return $html;
    }

    /**
     * Einheitlichen Beziehungsbereich ausgeben.
     */
    private static function render_relation_section(
        string $type,
        string $eyebrow,
        string $title,
        string $cards
    ): string {
        if ($cards === '') {
            return '';
        }

        return
            sprintf(
                '<section class="jl-relation jl-relation--experience jl-relation--%1$s">

                    <div class="jl-relation__inner">

                        <header class="jl-relation__header">

                            <span class="jl-relation__eyebrow">
                                %2$s
                            </span>

                            <h2>
                                %3$s
                            </h2>

                        </header>

                        <div class="jl-relation__cards">
                            %4$s
                        </div>

                    </div>

                </section>',

                esc_attr(
                    sanitize_html_class(
                        $type
                    )
                ),

                esc_html(
                    $eyebrow
                ),

                esc_html(
                    $title
                ),

                $cards
            );
    }

    /**
     * Beziehungstyp bestimmen.
     *
     * customer_source
     * = bestätigte persönliche Quelle
     *
     * ai_review
     * = KI-/Redaktionsentwurf
     *
     * reviewed
     * = redaktionell geprüfter Beitrag
     *
     * Nur customer_source darf automatisch als
     * persönliche Erfahrung von Roberto bezeichnet werden.
     */
    private static function get_experience_relation_type(
        WP_Post $experience
    ): string {
        $review_status =
            sanitize_key(
                (string) get_post_meta(
                    $experience->ID,
                    'jl_experience_review_status',
                    true
                )
            );

        return
            $review_status === 'customer_source'
                ? 'personal'
                : 'background';
    }

    /* =========================================================
       BEITRAG → PRODUKT
       ========================================================= */

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

        if ($product_id <= 0) {
            return '';
        }

        $product =
            get_post(
                $product_id
            );

        if (
            ! $product instanceof WP_Post
            || $product->post_type
            !== Jung_Leben_Core_Products::POST_TYPE
        ) {
            return '';
        }

        $url =
            self::get_frontend_url(
                $product
            );

        if ($url === '') {
            return '';
        }

        $card =
            self::render_product_card(
                $product,
                $url
            );

        if ($card === '') {
            return '';
        }

        return
            sprintf(
                '<section class="jl-relation jl-relation--product">

                    <div class="jl-relation__inner">

                        <header class="jl-relation__header">

                            <span class="jl-relation__eyebrow">
                                %1$s
                            </span>

                            <h2>
                                %2$s
                            </h2>

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

    private static function render_product_card(
        WP_Post $product,
        string $product_url
    ): string {
        $product_id =
            (int) $product->ID;

        $has_image =
            has_post_thumbnail(
                $product_id
            );

        $classes = [
            'jl-relation-card',
            'jl-relation-card--product',

            $has_image
                ? 'jl-relation-card--with-image'
                : 'jl-relation-card--no-image',
        ];

        $brand_markup =
            self::get_product_brand_markup(
                $product_id
            );

        $media_html = '';

        if ($has_image) {
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
                is_string($image)
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
                                $product->post_title
                            )
                        ),

                        $image
                    );
            }
        }

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

                            <span>
                                %7$s
                            </span>

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
                        $classes
                    )
                ),

                $media_html,

                $brand_markup,

                esc_url(
                    $product_url
                ),

                esc_html(
                    $product->post_title
                ),

                $excerpt_html,

                esc_html__(
                    'Produkt ansehen',
                    'jung-leben-core'
                )
            );
    }

    /* =========================================================
       BEITRAGSKARTE
       ========================================================= */

    private static function render_experience_card(
        WP_Post $experience,
        string $experience_url,
        string $relation_type
    ): string {
        $experience_id =
            (int) $experience->ID;

        $has_image =
            has_post_thumbnail(
                $experience_id
            );

        $is_personal =
            $relation_type === 'personal';

        $classes = [
            'jl-relation-card',
            'jl-relation-card--experience',

            $is_personal
                ? 'jl-relation-card--personal'
                : 'jl-relation-card--background',

            $has_image
                ? 'jl-relation-card--with-image'
                : 'jl-relation-card--no-image',
        ];

        $media_html = '';

        if ($has_image) {
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
                is_string($image)
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
                                $experience->post_title
                            )
                        ),

                        $image
                    );
            }
        }

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

        /**
         * Persönliche und redaktionelle Inhalte
         * klar unterschiedlich benennen.
         */
        $label =
            $is_personal
                ? __(
                    'Erfahrungsbericht',
                    'jung-leben-core'
                )
                : __(
                    'Hintergrundbeitrag',
                    'jung-leben-core'
                );

        $link_text =
            $is_personal
                ? __(
                    'Erfahrung lesen',
                    'jung-leben-core'
                )
                : __(
                    'Beitrag lesen',
                    'jung-leben-core'
                );

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

                            <span>
                                %7$s
                            </span>

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
                        $classes
                    )
                ),

                $media_html,

                esc_html(
                    $label
                ),

                esc_url(
                    $experience_url
                ),

                esc_html(
                    $experience->post_title
                ),

                $excerpt_html,

                esc_html(
                    $link_text
                )
            );
    }

    /* =========================================================
       MARKE
       ========================================================= */

    private static function get_product_brand_markup(
        int $product_id
    ): string {
        $brands =
            get_the_terms(
                $product_id,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );

        if (
            ! is_array($brands)
            || is_wp_error($brands)
            || empty($brands)
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

        if (! $brand instanceof WP_Term) {
            return '';
        }

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
       URL
       ========================================================= */

    private static function get_frontend_url(
        WP_Post $post
    ): string {
        if (
            $post->post_status
            === 'publish'
        ) {
            $url =
                get_permalink(
                    $post
                );

            return
                is_string($url)
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
            is_string($preview_url)
            && $preview_url !== ''
        ) {
            return $preview_url;
        }

        $permalink =
            get_permalink(
                $post
            );

        return
            is_string($permalink)
                ? $permalink
                : '';
    }

    /* =========================================================
       EXCERPT
       ========================================================= */

    private static function get_content_excerpt(
        WP_Post $post
    ): string {
        $excerpt =
            trim(
                (string) $post->post_excerpt
            );

        if ($excerpt !== '') {
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
            wp_strip_all_tags(
                strip_shortcodes(
                    $post->post_content
                )
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

        return
            $content !== ''
                ? wp_trim_words(
                    $content,
                    28,
                    ' …'
                )
                : '';
    }
}