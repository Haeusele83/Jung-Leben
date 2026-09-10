<?php
/**
 * Partner-Demo Redcare Apotheke CH.
 *
 * Zeigt die geplante redaktionelle Integration
 * ausgewählter Produkte auf Jung Leben.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/* =========================================================
   SEITEN-CSS
   ========================================================= */

$redcare_css_path =
    get_template_directory()
    . '/assets/css/redcare.css';


add_action(
    'wp_enqueue_scripts',
    static function () use (
        $redcare_css_path
    ): void {
        if (
            ! file_exists(
                $redcare_css_path
            )
        ) {
            return;
        }


        wp_enqueue_style(
            'jung-leben-redcare',
            get_template_directory_uri()
                . '/assets/css/redcare.css',
            [
                'jung-leben-site',
            ],
            (string)
            filemtime(
                $redcare_css_path
            )
        );
    },
    30
);


get_header();


/* =========================================================
   EMPFEHLUNGEN-SEITE
   ========================================================= */

$recommendations_page =
    get_page_by_path(
        'empfehlungen'
    );


$recommendations_url =
    $recommendations_page
    instanceof WP_Post
        ? get_permalink(
            $recommendations_page
        )
        : home_url(
            '/empfehlungen/'
        );


if (
    ! is_string(
        $recommendations_url
    )
    || $recommendations_url === ''
) {
    $recommendations_url =
        home_url(
            '/empfehlungen/'
        );
}


/* =========================================================
   DEMO-PRODUKTE
   ========================================================= */

/**
 * Die Produkte dienen als Beispiel für die
 * geplante redaktionelle Integration.
 *
 * Es wird NICHT behauptet, dass die Produkte
 * bereits persönlich getestet oder empfohlen
 * wurden.
 *
 * shop_url bleibt bis zur Freischaltung leer.
 */
$products = [

    [
        'title' =>
            'nu3 Omega-3',

        'brand' =>
            'nu3',

        'category' =>
            'Omega-3 & Ernährung',

        'mark' =>
            'Ω3',

        'variant' =>
            'ocean',

        'intro' =>
            'Ein Beispiel dafür, wie ein konkretes Nahrungsergänzungsmittel in einen grösseren redaktionellen Zusammenhang eingebettet werden kann.',

        'context' =>
            'Auf Jung Leben könnte das Produkt beispielsweise mit einem Artikel über Omega-3-Fettsäuren, Ernährung oder die Auswahl verschiedener Omega-3-Produkte verknüpft werden.',

        'approach' =>
            'Nicht der Shoplink steht im Zentrum, sondern die Einordnung: Was ist das Produkt, worauf sollte man bei der Auswahl achten und welche Alternativen gibt es?',

        'shop_url' =>
            '',
    ],

    [
        'title' =>
            'nu3 Premium Bio Ashwagandha',

        'brand' =>
            'nu3',

        'category' =>
            'Pflanzenstoffe',

        'mark' =>
            'AS',

        'variant' =>
            'sage',

        'intro' =>
            'Ashwagandha ist ein gutes Beispiel für ein Thema, bei dem persönliche Erfahrungen, aktuelle Trends und sachliche Hintergrundinformationen zusammenkommen.',

        'context' =>
            'Eine Jung-Leben-Seite könnte das Produkt mit einem ausführlicheren Beitrag über Ashwagandha, verschiedene Extrakte und die persönliche Einordnung des Themas verbinden.',

        'approach' =>
            'Produkte würden nicht isoliert präsentiert, sondern innerhalb eines thematischen Beitrags und – wo vorhanden – ergänzt um eigene Erfahrungen.',

        'shop_url' =>
            '',
    ],

    [
        'title' =>
            'nu3 Magnesium Komplex',

        'brand' =>
            'nu3',

        'category' =>
            'Vitamine & Mineralstoffe',

        'mark' =>
            'MG',

        'variant' =>
            'mineral',

        'intro' =>
            'Magnesium gehört zu den Themen, bei denen Konsumentinnen und Konsumenten mit einer grossen Zahl unterschiedlicher Produkte und Formen konfrontiert sind.',

        'context' =>
            'Hier kann Jung Leben einen Mehrwert schaffen, indem Produkte mit verständlichen Hintergrundinformationen und einer nachvollziehbaren Produktauswahl verbunden werden.',

        'approach' =>
            'Neben einem bevorzugten Produkt können weitere passende Optionen transparent als Alternativen dargestellt werden.',

        'shop_url' =>
            '',
    ],

    [
        'title' =>
            'GREEN NATURALS Trans-Resveratrol 500 mg vegan',

        'brand' =>
            'GREEN NATURALS',

        'category' =>
            'Longevity & Pflanzenstoffe',

        'mark' =>
            'RV',

        'variant' =>
            'berry',

        'intro' =>
            'Resveratrol eignet sich als Beispiel für ein Produkt aus dem stark wachsenden Themenfeld rund um Longevity und gesundes Altern.',

        'context' =>
            'Statt einzelne Produkte mit grossen Versprechen hervorzuheben, möchten wir erklären, weshalb bestimmte Stoffe diskutiert werden und wie wir sie einordnen.',

        'approach' =>
            'Damit entsteht eine Verbindung zwischen vertiefendem Inhalt, persönlichen Erfahrungen und einer konkreten Bezugsoption.',

        'shop_url' =>
            '',
    ],
];


/* =========================================================
   SEITENINHALT
   ========================================================= */

if (
    have_posts()
) :

    while (
        have_posts()
    ) :

        the_post();
        ?>

        <main
            id="main-content"
            class="redcare-page"
        >

            <!-- =================================================
                 BREADCRUMB
                 ================================================= -->

            <div class="container">

                <nav
                    class="redcare-breadcrumb"
                    aria-label="<?php
                    esc_attr_e(
                        'Breadcrumb',
                        'jung-leben'
                    );
                    ?>"
                >

                    <a
                        href="<?php
                        echo esc_url(
                            $recommendations_url
                        );
                        ?>"
                    >
                        <?php
                        esc_html_e(
                            'Empfehlungen',
                            'jung-leben'
                        );
                        ?>
                    </a>


                    <span aria-hidden="true">
                        /
                    </span>


                    <span>
                        <?php
                        esc_html_e(
                            'Redcare Apotheke',
                            'jung-leben'
                        );
                        ?>
                    </span>

                </nav>

            </div>


            <!-- =================================================
                 HERO
                 ================================================= -->

            <section class="redcare-hero">

                <div class="container redcare-hero__grid">

                    <div class="redcare-hero__content">

                        <p class="eyebrow">
                            <?php
                            esc_html_e(
                                'Produkte verstehen · bewusst auswählen',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <h1>
                            <?php
                            esc_html_e(
                                'Gesundheitsprodukte mit Kontext.',
                                'jung-leben'
                            );
                            ?>
                        </h1>


                        <p class="redcare-hero__lead">
                            <?php
                            esc_html_e(
                                'Ein Produkt allein sagt oft wenig. Jung Leben möchte Empfehlungen deshalb mit persönlichen Erfahrungen, verständlichen Hintergrundinformationen und passenden Alternativen verbinden.',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <p class="redcare-hero__text">
                            <?php
                            esc_html_e(
                                'Das breite Sortiment einer Schweizer Online-Apotheke bietet dafür interessante Möglichkeiten – von Vitaminen und Mineralstoffen über Omega-3 bis zu ausgewählten Pflanzenstoffen und Produkten aus dem Longevity-Umfeld.',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <div class="redcare-hero__tags">

                            <span>
                                <?php
                                esc_html_e(
                                    'Supplemente',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                esc_html_e(
                                    'Mineralstoffe',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                esc_html_e(
                                    'Pflanzenstoffe',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                esc_html_e(
                                    'Longevity',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                        </div>

                    </div>


                    <!-- =========================================
                         VISUELLE PRODUKTWELT
                         ========================================= -->

                    <div
                        class="redcare-hero__visual"
                        aria-hidden="true"
                    >

                        <div class="redcare-visual">

                            <div
                                class="
                                    redcare-visual__product
                                    redcare-visual__product--one
                                "
                            >
                                <span class="redcare-visual__cap"></span>

                                <span class="redcare-visual__label">
                                    JL
                                </span>
                            </div>


                            <div
                                class="
                                    redcare-visual__product
                                    redcare-visual__product--two
                                "
                            >
                                <span class="redcare-visual__cap"></span>

                                <span class="redcare-visual__label">
                                    01
                                </span>
                            </div>


                            <div
                                class="
                                    redcare-visual__product
                                    redcare-visual__product--three
                                "
                            >
                                <span class="redcare-visual__cap"></span>

                                <span class="redcare-visual__label">
                                    +
                                </span>
                            </div>

                        </div>


                        <p class="redcare-hero__visual-note">
                            <?php
                            esc_html_e(
                                'Nicht mehr Produkte. Mehr Orientierung.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 KONZEPT
                 ================================================= -->

            <section class="redcare-principles">

                <div class="container">

                    <div class="redcare-section-heading">

                        <p class="eyebrow">
                            <?php
                            esc_html_e(
                                'Unser Ansatz',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <h2>
                            <?php
                            esc_html_e(
                                'Vom Produkt zur echten Orientierung.',
                                'jung-leben'
                            );
                            ?>
                        </h2>


                        <p>
                            <?php
                            esc_html_e(
                                'Affiliate-Links sollen bei Jung Leben nicht der Ausgangspunkt eines Inhalts sein. Zuerst kommt das Thema, danach die Einordnung und erst dann eine passende Bezugsoption.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>


                    <div class="redcare-principles__grid">

                        <article class="redcare-principle">

                            <span class="redcare-principle__number">
                                01
                            </span>


                            <h3>
                                <?php
                                esc_html_e(
                                    'Thema zuerst',
                                    'jung-leben'
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                esc_html_e(
                                    'Produkte werden passend zu bestehenden Themen, Erfahrungen und Routinen ausgewählt – nicht umgekehrt.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </article>


                        <article class="redcare-principle">

                            <span class="redcare-principle__number">
                                02
                            </span>


                            <h3>
                                <?php
                                esc_html_e(
                                    'Favorit und Alternativen',
                                    'jung-leben'
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                esc_html_e(
                                    'Wo sinnvoll, zeigen wir neben einer bevorzugten Option weitere Produkte, damit Leserinnen und Leser vergleichen können.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </article>


                        <article class="redcare-principle">

                            <span class="redcare-principle__number">
                                03
                            </span>


                            <h3>
                                <?php
                                esc_html_e(
                                    'Transparent verlinkt',
                                    'jung-leben'
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                esc_html_e(
                                    'Affiliate-Partnerschaften werden klar gekennzeichnet. Die redaktionelle Einordnung bleibt davon unabhängig.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </article>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 PRODUKTBEISPIELE
                 ================================================= -->

            <section class="redcare-selection">

                <div class="container">

                    <header class="redcare-selection__header">

                        <div>

                            <p class="eyebrow">
                                <?php
                                esc_html_e(
                                    'Beispielhafte Integration',
                                    'jung-leben'
                                );
                                ?>
                            </p>


                            <h2>
                                <?php
                                esc_html_e(
                                    'So könnten Produkte eingebunden werden.',
                                    'jung-leben'
                                );
                                ?>
                            </h2>

                        </div>


                        <p class="redcare-selection__intro">
                            <?php
                            esc_html_e(
                                'Die folgenden Produkte dienen als Beispiele für die geplante Darstellung. Sie stellen auf dieser Vorschauseite noch keine persönliche Empfehlung oder Bewertung dar.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </header>


                    <div class="redcare-products">

                        <?php
                        foreach (
                            $products
                            as $product
                        ) :
                            ?>

                            <article class="redcare-product-card">

                                <!-- =============================
                                     VISUAL
                                     ============================= -->

                                <div
                                    class="
                                        redcare-product-card__visual
                                        redcare-product-card__visual--<?php
                                        echo esc_attr(
                                            $product[
                                                'variant'
                                            ]
                                        );
                                        ?>
                                    "
                                    aria-hidden="true"
                                >

                                    <div class="redcare-product-card__bottle">

                                        <span class="redcare-product-card__cap"></span>


                                        <span class="redcare-product-card__mark">
                                            <?php
                                            echo esc_html(
                                                $product[
                                                    'mark'
                                                ]
                                            );
                                            ?>
                                        </span>

                                    </div>

                                </div>


                                <!-- =============================
                                     CONTENT
                                     ============================= -->

                                <div class="redcare-product-card__content">

                                    <div class="redcare-product-card__meta">

                                        <span class="redcare-product-card__category">
                                            <?php
                                            echo esc_html(
                                                $product[
                                                    'category'
                                                ]
                                            );
                                            ?>
                                        </span>


                                        <span class="redcare-product-card__brand">
                                            <?php
                                            echo esc_html(
                                                $product[
                                                    'brand'
                                                ]
                                            );
                                            ?>
                                        </span>

                                    </div>


                                    <h3>
                                        <?php
                                        echo esc_html(
                                            $product[
                                                'title'
                                            ]
                                        );
                                        ?>
                                    </h3>


                                    <p class="redcare-product-card__intro">
                                        <?php
                                        echo esc_html(
                                            $product[
                                                'intro'
                                            ]
                                        );
                                        ?>
                                    </p>


                                    <div class="redcare-product-card__detail">

                                        <span class="redcare-product-card__detail-label">
                                            <?php
                                            esc_html_e(
                                                'Möglicher Kontext',
                                                'jung-leben'
                                            );
                                            ?>
                                        </span>


                                        <p>
                                            <?php
                                            echo esc_html(
                                                $product[
                                                    'context'
                                            ]
                                        );
                                        ?>
                                        </p>

                                    </div>


                                    <div class="redcare-product-card__detail">

                                        <span class="redcare-product-card__detail-label">
                                            <?php
                                            esc_html_e(
                                                'Jung-Leben-Ansatz',
                                                'jung-leben'
                                            );
                                            ?>
                                        </span>


                                        <p>
                                            <?php
                                            echo esc_html(
                                                $product[
                                                    'approach'
                                            ]
                                        );
                                        ?>
                                        </p>

                                    </div>


                                    <div class="redcare-product-card__footer">

                                        <?php
                                        if (
                                            $product[
                                                'shop_url'
                                            ] !== ''
                                        ) :
                                            ?>

                                            <a
                                                href="<?php
                                                echo esc_url(
                                                    $product[
                                                        'shop_url'
                                                    ]
                                                );
                                                ?>"
                                                class="redcare-product-card__link"
                                                target="_blank"
                                                rel="noopener noreferrer sponsored"
                                            >

                                                <span>
                                                    <?php
                                                    esc_html_e(
                                                        'Bei Redcare Apotheke ansehen',
                                                        'jung-leben'
                                                    );
                                                    ?>
                                                </span>


                                                <span aria-hidden="true">
                                                    ↗
                                                </span>

                                            </a>

                                        <?php else : ?>

                                            <div class="redcare-product-card__planned">

                                                <span class="redcare-product-card__planned-dot"></span>


                                                <span>
                                                    <?php
                                                    esc_html_e(
                                                        'Geplante Verlinkung über Redcare Apotheke',
                                                        'jung-leben'
                                                    );
                                                    ?>
                                                </span>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 CUSTOMER JOURNEY
                 ================================================= -->

            <section class="redcare-journey">

                <div class="container redcare-journey__grid">

                    <div class="redcare-journey__heading">

                        <p class="eyebrow">
                            <?php
                            esc_html_e(
                                'Die Customer Journey',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <h2>
                            <?php
                            esc_html_e(
                                'Der Shoplink ist der letzte Schritt.',
                                'jung-leben'
                            );
                            ?>
                        </h2>

                    </div>


                    <div class="redcare-journey__steps">

                        <div class="redcare-journey-step">

                            <span>
                                01
                            </span>


                            <div>

                                <h3>
                                    <?php
                                    esc_html_e(
                                        'Thema entdecken',
                                        'jung-leben'
                                    );
                                    ?>
                                </h3>


                                <p>
                                    <?php
                                    esc_html_e(
                                        'Ein persönlicher Erfahrungsbericht, eine Routine oder ein redaktioneller Hintergrundbeitrag bildet den Einstieg.',
                                        'jung-leben'
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>


                        <div class="redcare-journey-step">

                            <span>
                                02
                            </span>


                            <div>

                                <h3>
                                    <?php
                                    esc_html_e(
                                        'Produkt einordnen',
                                        'jung-leben'
                                    );
                                    ?>
                                </h3>


                                <p>
                                    <?php
                                    esc_html_e(
                                        'Wir zeigen, weshalb ein Produkt zum Thema gehört, welche Erfahrungen bestehen und welche Alternativen relevant sein können.',
                                        'jung-leben'
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>


                        <div class="redcare-journey-step">

                            <span>
                                03
                            </span>


                            <div>

                                <h3>
                                    <?php
                                    esc_html_e(
                                        'Gezielt weiterleiten',
                                        'jung-leben'
                                    );
                                    ?>
                                </h3>


                                <p>
                                    <?php
                                    esc_html_e(
                                        'Erst wenn sich jemand konkret für ein Produkt interessiert, erfolgt die Weiterleitung zum passenden Angebot des Partners.',
                                        'jung-leben'
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 PARTNER-VORSCHAU
                 ================================================= -->

            <section class="redcare-partner-preview">

                <div class="container">

                    <div class="redcare-partner-preview__box">

                        <div class="redcare-partner-preview__mark">
                            <span aria-hidden="true">
                                ↗
                            </span>
                        </div>


                        <div class="redcare-partner-preview__content">

                            <p class="eyebrow">
                                <?php
                                esc_html_e(
                                    'Partner-Vorschau',
                                    'jung-leben'
                                );
                                ?>
                            </p>


                            <h2>
                                <?php
                                esc_html_e(
                                    'Geplante Einbindung von Redcare Apotheke.',
                                    'jung-leben'
                                );
                                ?>
                            </h2>


                            <p>
                                <?php
                                esc_html_e(
                                    'Diese Seite zeigt die geplante redaktionelle Integration ausgewählter Gesundheitsprodukte auf Jung Leben. Aktuell besteht noch keine Affiliate-Partnerschaft mit Redcare Apotheke. Deshalb werden auf dieser Vorschauseite noch keine Affiliate-Links eingesetzt.',
                                    'jung-leben'
                                );
                                ?>
                            </p>


                            <p>
                                <?php
                                esc_html_e(
                                    'Nach einer Freischaltung möchten wir ausgewählte Produkte passend zu unseren redaktionellen Themen, Erfahrungen und Routinen direkt mit dem jeweiligen Angebot bei Redcare Apotheke verknüpfen. Die Auswahl und Einordnung der Inhalte bleibt redaktionell unabhängig.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 ZURÜCK
                 ================================================= -->

            <section class="redcare-back">

                <div class="container">

                    <a
                        href="<?php
                        echo esc_url(
                            $recommendations_url
                        );
                        ?>"
                        class="redcare-back__link"
                    >

                        <span aria-hidden="true">
                            ←
                        </span>


                        <span>
                            <?php
                            esc_html_e(
                                'Zurück zu allen Empfehlungen',
                                'jung-leben'
                            );
                            ?>
                        </span>

                    </a>

                </div>

            </section>

        </main>

        <?php
    endwhile;

endif;


get_footer();