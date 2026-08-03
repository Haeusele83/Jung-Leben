<?php
/**
 * Fussbereich des Themes.
 *
 * @package Jung_Leben
 */
?>

<footer class="site-footer">
    <div class="site-container">
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => 'nav',
            'fallback_cb'    => false,
        ]);
        ?>

        <p>
            &copy; <?php echo esc_html(wp_date('Y')); ?>
            <?php echo esc_html(get_bloginfo('name')); ?>
        </p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>