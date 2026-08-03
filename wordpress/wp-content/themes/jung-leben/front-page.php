<?php
/**
 * Startseite von Jung Leben.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

get_header();

/**
 * URL der WordPress-Beitragsseite ermitteln.
 */
$ratgeber_page_id = (int) get_option('page_for_posts');

$ratgeber_url = $ratgeber_page_id > 0
    ? get_permalink($ratgeber_page_id)
    : home_url('/ratgeber/');

/**
 * Empfehlungsseite ermitteln.
 */
$empfehlungen_page = get_page_by_path('empfehlungen');

$empfehlungen_url = $empfehlungen_page instanceof WP_Post
    ? get_permalink($empfehlungen_page)
    : home_url('/empfehlungen/');

/**
 * Seite «Über mich» ermitteln.
 */
$ueber_mich_page = get_page_by_path('ueber-mich');

$ueber_mich_url = $ueber_mich_page instanceof WP_Post
    ? get_permalink($ueber_mich_page)
    : home_url('/ueber-mich/');
?>

<main id="main-content">

    <!-- Hero-Bereich -->
    <section class="hero home-hero">
        <div class="hero-overlay"></div>

        <div class="container hero-content home-hero-content">
            <div class="hero-text">

                <p class="eyebrow">
                    <?php
                    esc_html_e(
                        'Longevity ist kein Sprint und kein Zufall',
                        'jung-leben'
                    );
                    ?>
                </p>

                <h1>
                    <?php
                    esc_html_e(
                        'The goal is to die young – as late as possible',
                        'jung-leben'
                    );
                    ?>
                </h1>

                <p class="hero-lead">
                    <?php
                    esc_html_e(
                        'Es sind die täglichen Entscheidungen, die darüber bestimmen, wie wir altern. Jung Leben hilft dir dabei, evidenzbasierte Produkte, Routinen und Erfahrungen zu entdecken, die Vitalität, Wohlbefinden und Langlebigkeit unterstützen.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <div class="hero-actions">

                    <a
                        href="<?php echo esc_url($empfehlungen_url); ?>"
                        class="btn btn-primary"
                    >
                        <?php
                        esc_html_e(
                            'Empfehlungen entdecken',
                            'jung-leben'
                        );
                        ?>
                    </a>

                    <a
                        href="<?php echo esc_url($ratgeber_url); ?>"
                        class="btn btn-light"
                    >
                        <?php
                        esc_html_e(
                            'Erfahrungen lesen',
                            'jung-leben'
                        );
                        ?>
                    </a>

                </div>

                <div class="hero-benefits">

                    <span>
                        🌿
                        <?php
                        esc_html_e(
                            'Longevity',
                            'jung-leben'
                        );
                        ?>
                    </span>

                    <span>
                        ✨
                        <?php
                        esc_html_e(
                            'Persönliche Erfahrungen',
                            'jung-leben'
                        );
                        ?>
                    </span>

                    <span>
                        💡
                        <?php
                        esc_html_e(
                            'Bewusste Routinen',
                            'jung-leben'
                        );
                        ?>
                    </span>

                </div>

            </div>
        </div>
    </section>

    <!-- Über Jung Leben -->
    <section class="home-curator-section">
        <div class="container home-curator-grid">

            <div
                class="home-curator-image"
                aria-hidden="true"
            ></div>

            <div class="home-curator-text">

                <p class="eyebrow">
                    <?php
                    esc_html_e(
                        'Über Jung Leben',
                        'jung-leben'
                    );
                    ?>
                </p>

                <h2>
                    <?php
                    esc_html_e(
                        'Praktische Erfahrung statt Produkt-Blabla.',
                        'jung-leben'
                    );
                    ?>
                </h2>

                <p>
                    <?php
                    esc_html_e(
                        'Jung Leben ist aus Robertos persönlicher Auseinandersetzung mit Gesundheit, Vitalität und Langlebigkeit entstanden. Im Mittelpunkt stehen Erfahrungen aus dem Alltag – ehrlich, nachvollziehbar und ohne leere Versprechen.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <p>
                    <?php
                    esc_html_e(
                        'Die Plattform verbindet persönliche Beobachtungen mit sorgfältig ausgewählten Produkten, Routinen und fundierten Informationen. Ziel ist nicht, die eine perfekte Lösung zu präsentieren, sondern Orientierung für bewusste Entscheidungen zu geben.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <a
                    href="<?php echo esc_url($ueber_mich_url); ?>"
                    class="btn btn-primary"
                >
                    <?php
                    esc_html_e(
                        'Mehr über Roberto',
                        'jung-leben'
                    );
                    ?>
                </a>

            </div>
        </div>
    </section>

    <!-- Ein Ort der Orientierung -->
    <section
        class="home-orientation-section"
        aria-labelledby="orientation-title"
    >
        <div class="container home-orientation-grid">

            <div class="home-orientation-copy">

                <p class="eyebrow">
                    <?php
                    esc_html_e(
                        'Wie funktioniert Jung Leben',
                        'jung-leben'
                    );
                    ?>
                </p>

                <h2 id="orientation-title">
                    <?php
                    esc_html_e(
                        'Ein Ort der Orientierung',
                        'jung-leben'
                    );
                    ?>
                </h2>

                <p class="home-orientation-intro">
                    <?php
                    esc_html_e(
                        'Es gibt keinen vorgeschriebenen Einstiegspunkt. Du kannst Jung Leben über persönliche Erfahrungen, konkrete Empfehlungen, einzelne Produkte oder mögliche Routinen entdecken.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <p>
                    <?php
                    esc_html_e(
                        'Alle Wege führen zu einem gemeinsamen Ziel: Informationen besser einzuordnen und bewusste Entscheidungen für dein persönliches Wohlbefinden zu treffen.',
                        'jung-leben'
                    );
                    ?>
                </p>

            </div>

            <div
                class="orientation-map"
                aria-label="<?php esc_attr_e(
                    'Verschiedene Einstiegspunkte führen zu Jung Leben',
                    'jung-leben'
                ); ?>"
            >

                <svg
                    class="orientation-map__lines"
                    viewBox="0 0 600 520"
                    preserveAspectRatio="none"
                    aria-hidden="true"
                    focusable="false"
                >
                    <line x1="300" y1="260" x2="300" y2="65"></line>
                    <line x1="300" y1="260" x2="510" y2="155"></line>
                    <line x1="300" y1="260" x2="510" y2="390"></line>
                    <line x1="300" y1="260" x2="300" y2="465"></line>
                    <line x1="300" y1="260" x2="90" y2="390"></line>
                    <line x1="300" y1="260" x2="90" y2="155"></line>
                </svg>

                <div class="orientation-map__center">
                    <span class="orientation-map__brand">
                        <?php
                        esc_html_e(
                            'Jung Leben',
                            'jung-leben'
                        );
                        ?>
                    </span>

                    <span class="orientation-map__purpose">
                        <?php
                        esc_html_e(
                            'Orientierung',
                            'jung-leben'
                        );
                        ?>
                    </span>
                </div>

                <div
                    class="orientation-map__node orientation-map__node--experiences"
                >
                    <span aria-hidden="true">✨</span>

                    <?php
                    esc_html_e(
                        'Erfahrungen',
                        'jung-leben'
                    );
                    ?>
                </div>

                <div
                    class="orientation-map__node orientation-map__node--recommendations"
                >
                    <span aria-hidden="true">✓</span>

                    <?php
                    esc_html_e(
                        'Empfehlungen',
                        'jung-leben'
                    );
                    ?>
                </div>

                <div
                    class="orientation-map__node orientation-map__node--products"
                >
                    <span aria-hidden="true">🌿</span>

                    <?php
                    esc_html_e(
                        'Produkte',
                        'jung-leben'
                    );
                    ?>
                </div>

                <div
                    class="orientation-map__node orientation-map__node--routines"
                >
                    <span aria-hidden="true">☀</span>

                    <?php
                    esc_html_e(
                        'Routinen',
                        'jung-leben'
                    );
                    ?>
                </div>

                <div
                    class="orientation-map__node orientation-map__node--knowledge"
                >
                    <span aria-hidden="true">💡</span>

                    <?php
                    esc_html_e(
                        'Wissen',
                        'jung-leben'
                    );
                    ?>
                </div>

                <div
                    class="orientation-map__node orientation-map__node--exchange"
                >
                    <span aria-hidden="true">↔</span>

                    <?php
                    esc_html_e(
                        'Austausch',
                        'jung-leben'
                    );
                    ?>
                </div>

            </div>

        </div>
    </section>

    <!-- Inhalte aus dem WordPress-Editor -->
    <?php while (have_posts()) : ?>
        <?php the_post(); ?>

        <?php
        $editor_content = trim(
            (string) get_the_content()
        );
        ?>

        <?php if ($editor_content !== '') : ?>
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