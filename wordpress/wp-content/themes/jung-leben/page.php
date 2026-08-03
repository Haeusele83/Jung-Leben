<?php
/**
 * Template für normale Seiten.
 *
 * @package Jung_Leben
 */

get_header();
?>

<main class="site-main">
    <div class="site-container">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>

                <div class="page-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();