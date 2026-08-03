<?php
/**
 * Standard-Template.
 *
 * @package Jung_Leben
 */

get_header();
?>

<main class="site-main">
    <div class="site-container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h1>

                    <div class="post-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>

            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <h1><?php esc_html_e('Keine Inhalte gefunden.', 'jung-leben'); ?></h1>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();