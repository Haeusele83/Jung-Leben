<?php
/**
 * Template für die Ratgeber- und Beitragsübersicht.
 *
 * @package Jung_Leben
 */

get_header();
?>

<main id="main-content" class="site-main">
    <div class="site-container">
        <header class="archive-header">
            <h1 class="archive-title">
                <?php esc_html_e('Ratgeber', 'jung-leben'); ?>
            </h1>

            <p class="archive-description">
                <?php esc_html_e(
                    'Wissen, Orientierung und praktische Impulse für einen bewussten Alltag.',
                    'jung-leben'
                ); ?>
            </p>
        </header>

        <?php if (have_posts()) : ?>
            <div class="post-grid">
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>

                    <article
                        id="post-<?php the_ID(); ?>"
                        <?php post_class('post-card'); ?>
                    >
                        <?php if (has_post_thumbnail()) : ?>
                            <a
                                class="post-card__image"
                                href="<?php the_permalink(); ?>"
                                aria-hidden="true"
                                tabindex="-1"
                            >
                                <?php the_post_thumbnail('large'); ?>
                            </a>
                        <?php endif; ?>

                        <div class="post-card__content">
                            <p class="post-card__date">
                                <?php echo esc_html(get_the_date()); ?>
                            </p>

                            <h2 class="post-card__title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="post-card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a
                                class="post-card__link"
                                href="<?php the_permalink(); ?>"
                            >
                                <?php esc_html_e(
                                    'Beitrag lesen',
                                    'jung-leben'
                                ); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                the_posts_pagination([
                    'mid_size'  => 1,
                    'prev_text' => __('Zurück', 'jung-leben'),
                    'next_text' => __('Weiter', 'jung-leben'),
                ]);
                ?>
            </div>
        <?php else : ?>
            <p>
                <?php esc_html_e(
                    'Aktuell sind noch keine Ratgeberbeiträge vorhanden.',
                    'jung-leben'
                ); ?>
            </p>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();