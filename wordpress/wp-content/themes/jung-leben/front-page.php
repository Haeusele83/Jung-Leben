<?php
/**
 * Startseite von Jung Leben.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

get_header();

$ratgeber_url = get_permalink(
    get_option('page_for_posts')
);

$ueber_mich_page = get_page_by_path('ueber-mich');

$ueber_mich_url = $ueber_mich_page
    ? get_permalink($ueber_mich_page)
    : home_url('/ueber-mich/');
?>

<main id="main-content">

    <section class="hero home-hero">
        <div class="hero-overlay"></div>

        <div class="container hero-content home-hero-content">
            <div class="hero-text">

                <p class="eyebrow">
                    <?php esc_html_e(
                        'Longevity Curator',
                        'jung-leben'
                    ); ?>
                </p>

                <h1>
                    <?php esc_html_e(
                        'Jung bleiben beginnt mit bewussten Routinen.',
                        'jung-leben'
                    ); ?>
                </h1>

                <p class="hero-lead">
                    <?php esc_html_e(
                        'Roberto teilt auf Jung Leben persönliche Erfahrungen, sorgfältig ausgewählte Produkte und alltagsnahe Impulse für mehr Vitalität, Balance und bewusste Langlebigkeit.',
                        'jung-leben'
                    ); ?>
                </p>

                <div class="hero-actions">
                    <a
                        href="<?php echo esc_url($ratgeber_url); ?>"
                        class="btn btn-primary"
                    >
                        <?php esc_html_e(
                            'Erfahrungen lesen',
                            'jung-leben'
                        ); ?>
                    </a>

                    <a
                        href="<?php echo esc_url(
                            home_url('/produkte/')
                        ); ?>"
                        class="btn btn-light"
                    >
                        <?php esc_html_e(
                            'Produkte entdecken',
                            'jung-leben'
                        ); ?>
                    </a>
                </div>

                <div class="hero-benefits">
                    <span>🌿 Longevity</span>
                    <span>✨ Persönliche Erfahrung</span>
                    <span>🛒 Affiliate-ready</span>
                </div>

            </div>
        </div>
    </section>

    <section class="home-curator-section">
        <div class="container home-curator-grid">

            <div
                class="home-curator-image"
                aria-hidden="true"
            ></div>

            <div class="home-curator-text">
                <p class="eyebrow">
                    <?php esc_html_e(
                        'Über Roberto',
                        'jung-leben'
                    ); ?>
                </p>

                <h2>
                    <?php esc_html_e(
                        'Erfahrung statt leere Produktversprechen.',
                        'jung-leben'
                    ); ?>
                </h2>

                <p>
                    <?php esc_html_e(
                        'Jung Leben ist aus Robertos persönlichem Weg entstanden: aus Selbstreflexion, Körperbewusstsein und dem Wunsch, Gesundheit ganzheitlicher zu betrachten.',
                        'jung-leben'
                    ); ?>
                </p>

                <p>
                    <?php esc_html_e(
                        'Als Longevity Curator verbindet er persönliche Beobachtungen mit kuratierten Produkten aus den Bereichen Vitalität, Pflanzenkraft, Mundpflege, Hygiene, Beauty und Balance.',
                        'jung-leben'
                    ); ?>
                </p>

                <a
                    href="<?php echo esc_url($ueber_mich_url); ?>"
                    class="btn btn-primary"
                >
                    <?php esc_html_e(
                        'Mehr über Roberto',
                        'jung-leben'
                    ); ?>
                </a>
            </div>

        </div>
    </section>

    <?php while (have_posts()) : ?>
        <?php the_post(); ?>

        <?php if (trim((string) get_the_content()) !== '') : ?>
            <section class="front-page-editor-content">
                <div class="container">
                    <?php the_content(); ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endwhile; ?>

</main>

<?php
get_footer();