<?php
/**
 * Übersicht der Jung-Leben-Erfahrungen.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Geschätzte Lesedauer eines Beitrags.
 */
$jung_leben_get_reading_time = static function (
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


get_header();
?>

<main
    id="main-content"
    class="site-main experience-archive"
>

    <!-- =====================================================
         KOMPAKTER SEITENKOPF
         ===================================================== -->

    <section class="experience-archive__intro">

        <div class="experience-archive__container">

            <div class="experience-archive__intro-content">

                <h1 class="experience-archive__title">
                    Erfahrungen
                </h1>


                <p class="experience-archive__description">
                    Persönliche Erfahrungen mit Produkten,
                    Routinen und Themen rund um Longevity
                    und Wohlbefinden – ehrlich und
                    nachvollziehbar eingeordnet.
                </p>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ERFAHRUNGEN
         ===================================================== -->

    <section class="experience-archive__content">

        <div class="experience-archive__container">

            <?php
            if (
                have_posts()
            ) :
                ?>

                <div class="experience-grid">

                    <?php
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


                        $has_image =
                            has_post_thumbnail(
                                $post_id
                            );


                        $excerpt =
                            trim(
                                (string)
                                get_the_excerpt()
                            );


                        if (
                            $excerpt === ''
                        ) {
                            $excerpt =
                                wp_trim_words(
                                    wp_strip_all_tags(
                                        (string)
                                        get_the_content()
                                    ),
                                    31,
                                    ' …'
                                );
                        }


                        $card_classes = [
                            'experience-card',
                            $has_image
                                ? 'experience-card--has-image'
                                : 'experience-card--no-image',
                        ];
                        ?>

                        <article
                            id="post-<?php the_ID(); ?>"
                            <?php
                            post_class(
                                implode(
                                    ' ',
                                    $card_classes
                                )
                            );
                            ?>
                        >

                            <?php
                            if (
                                $has_image
                            ) :
                                ?>

                                <a
                                    class="experience-card__media"
                                    href="<?php the_permalink(); ?>"
                                    aria-label="<?php
                                    echo esc_attr(
                                        sprintf(
                                            'Erfahrung ansehen: %s',
                                            get_the_title()
                                        )
                                    );
                                    ?>"
                                >

                                    <?php
                                    the_post_thumbnail(
                                        'large',
                                        [
                                            'class' =>
                                                'experience-card__image',

                                            'loading' =>
                                                'lazy',
                                        ]
                                    );
                                    ?>

                                </a>

                            <?php else : ?>

                                <div
                                    class="experience-card__accent"
                                    aria-hidden="true"
                                ></div>

                            <?php endif; ?>


                            <div class="experience-card__body">

                                <!-- =================================
                                     META
                                     ================================= -->

                                <div class="experience-card__meta">

                                    <span>
                                        Erfahrung
                                    </span>


                                    <span
                                        class="experience-card__meta-dot"
                                        aria-hidden="true"
                                    >
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


                                <!-- =================================
                                     TITEL
                                     ================================= -->

                                <h2 class="experience-card__title">

                                    <a href="<?php the_permalink(); ?>">

                                        <?php
                                        the_title();
                                        ?>

                                    </a>

                                </h2>


                                <!-- =================================
                                     BESCHREIBUNG
                                     ================================= -->

                                <?php
                                if (
                                    $excerpt !== ''
                                ) :
                                    ?>

                                    <p class="experience-card__excerpt">

                                        <?php
                                        echo esc_html(
                                            wp_trim_words(
                                                $excerpt,
                                                31,
                                                ' …'
                                            )
                                        );
                                        ?>

                                    </p>

                                <?php endif; ?>


                                <!-- =================================
                                     CTA
                                     ================================= -->

                                <div class="experience-card__footer">

                                    <a
                                        class="experience-card__link"
                                        href="<?php the_permalink(); ?>"
                                    >
                                        Erfahrung ansehen

                                        <span aria-hidden="true">
                                            →
                                        </span>
                                    </a>

                                </div>

                            </div>

                        </article>

                    <?php endwhile; ?>

                </div>


                <!-- =============================================
                     PAGINATION
                     ============================================= -->

                <nav
                    class="experience-pagination"
                    aria-label="<?php
                    esc_attr_e(
                        'Seitennavigation',
                        'jung-leben'
                    );
                    ?>"
                >

                    <?php
                    the_posts_pagination([
                        'mid_size' =>
                            1,

                        'prev_text' =>
                            __('← Zurück', 'jung-leben'),

                        'next_text' =>
                            __('Weiter →', 'jung-leben'),

                        'screen_reader_text' =>
                            __('Erfahrungsseiten', 'jung-leben'),
                    ]);
                    ?>

                </nav>

            <?php else : ?>

                <div class="experience-empty">

                    <h2>
                        Noch keine Erfahrungen veröffentlicht.
                    </h2>


                    <p>
                        Sobald neue Erfahrungen veröffentlicht
                        werden, erscheinen sie hier.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php
get_footer();