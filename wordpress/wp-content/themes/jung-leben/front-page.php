<?php
/**
 * Template für die Startseite.
 *
 * Die Inhalte werden über die in WordPress definierte
 * statische Startseite gepflegt.
 *
 * @package Jung_Leben
 */

get_header();
?>

<main id="main-content" class="site-main site-main--front-page">
    <?php while (have_posts()) : ?>
        <?php the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('front-page'); ?>>
            <div class="site-container">
                <?php if (get_the_content()) : ?>
                    <div class="front-page__content">
                        <?php the_content(); ?>
                    </div>
                <?php else : ?>
                    <section class="hero">
                        <p class="hero__eyebrow">
                            <?php esc_html_e(
                                'Bewusst auswählen. Besser leben.',
                                'jung-leben'
                            ); ?>
                        </p>

                        <h1 class="hero__title">
                            <?php echo esc_html(get_bloginfo('name')); ?>
                        </h1>

                        <p class="hero__text">
                            <?php esc_html_e(
                                'Ausgewählte Produkte, ehrliche Empfehlungen und fundierte Ratgeber für einen bewussten Alltag.',
                                'jung-leben'
                            ); ?>
                        </p>
                    </section>
                <?php endif; ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();