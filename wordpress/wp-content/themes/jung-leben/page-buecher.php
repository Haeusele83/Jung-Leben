<?php
/**
 * Buchempfehlungen / Partner-Demo Orell Füssli.
 *
 * Diese Seite zeigt die geplante redaktionelle
 * Integration von Büchern auf Jung Leben.
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

$books_css_path =
    get_template_directory()
    . '/assets/css/books.css';


add_action(
    'wp_enqueue_scripts',
    static function () use (
        $books_css_path
    ): void {
        if (
            ! file_exists(
                $books_css_path
            )
        ) {
            return;
        }


        wp_enqueue_style(
            'jung-leben-books',
            get_template_directory_uri()
                . '/assets/css/books.css',
            [
                'jung-leben-site',
            ],
            (string)
            filemtime(
                $books_css_path
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
   DEMO-BÜCHER
   ========================================================= */

/**
 * shop_url bleibt absichtlich leer.
 *
 * Nach einer Freischaltung durch Orell Füssli / Awin
 * können hier die jeweiligen Affiliate-Deeplinks
 * eingetragen werden.
 *
 * Solange keine Partnerschaft besteht, wird öffentlich
 * kein Affiliate-Link vorgetäuscht.
 */
$books = [

    [
        'title' =>
            'Outlive',

        'author' =>
            'Peter Attia & Bill Gifford',

        'category' =>
            'Longevity',

        'mark' =>
            'OL',

        'cover_variant' =>
            'sage',

        'intro' =>
            'Ein umfassender Blick darauf, wie Gesundheit und Lebensqualität möglichst lange erhalten werden können.',

        'focus' =>
            'Das Buch verbindet Prävention, Bewegung, Stoffwechsel, Schlaf und weitere Lebensstilfaktoren zu einem langfristigen Blick auf gesundes Altern.',

        'suitable_for' =>
            'Für Menschen, die Longevity nicht als kurzfristigen Trend, sondern als langfristiges Gesundheitsthema verstehen möchten.',

        'shop_url' =>
            '',
    ],

    [
        'title' =>
            'Why We Sleep',

        'author' =>
            'Matthew Walker',

        'category' =>
            'Schlaf & Regeneration',

        'mark' =>
            'WS',

        'cover_variant' =>
            'night',

        'intro' =>
            'Ein populärwissenschaftlicher Einstieg in die Bedeutung von Schlaf für Körper, Gehirn und Alltag.',

        'focus' =>
            'Schlaf wird nicht als passive Ruhephase betrachtet, sondern als grundlegender Bestandteil von Regeneration, Leistungsfähigkeit und Wohlbefinden.',

        'suitable_for' =>
            'Für alle, die besser verstehen möchten, weshalb Schlaf eine zentrale Rolle innerhalb einer gesunden Alltagsroutine spielt.',

        'shop_url' =>
            '',
    ],

    [
        'title' =>
            'Der Ernährungskompass',

        'author' =>
            'Bas Kast',

        'category' =>
            'Ernährung',

        'mark' =>
            'EK',

        'cover_variant' =>
            'sand',

        'intro' =>
            'Eine verständliche Auseinandersetzung mit wissenschaftlichen Erkenntnissen rund um Ernährung und Gesundheit.',

        'focus' =>
            'Besonders interessant ist der Versuch, widersprüchliche Aussagen rund um Lebensmittel und Ernährungsweisen einzuordnen und verständlich zusammenzuführen.',

        'suitable_for' =>
            'Für Leserinnen und Leser, die sich einen breiten Einstieg in das Thema Ernährung wünschen und Zusammenhänge besser verstehen möchten.',

        'shop_url' =>
            '',
    ],

    [
        'title' =>
            'Der Vitamin- und Nährstoffkompass',

        'author' =>
            'Bas Kast',

        'category' =>
            'Nährstoffe & Supplemente',

        'mark' =>
            'VN',

        'cover_variant' =>
            'mineral',

        'intro' =>
            'Ein aktueller Einstieg in Vitamine, Mineralstoffe und die kontrovers diskutierte Welt der Nahrungsergänzung.',

        'focus' =>
            'Das Thema passt besonders gut zu Jung Leben, weil Nahrungsergänzungsmittel nicht isoliert, sondern im Kontext von Ernährung, Bewegung, Schlaf und Lebensstil betrachtet werden sollten.',

        'suitable_for' =>
            'Für Menschen, die bei Supplementen stärker verstehen möchten, was hinter einzelnen Nährstoffen und deren Einordnung steckt.',

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
            class="books-page"
        >

            <!-- =================================================
                 BREADCRUMB
                 ================================================= -->

            <div class="container">

                <nav
                    class="books-breadcrumb"
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
                            'Bücher',
                            'jung-leben'
                        );
                        ?>
                    </span>

                </nav>

            </div>


            <!-- =================================================
                 HERO
                 ================================================= -->

            <section class="books-hero">

                <div class="container books-hero__grid">

                    <div class="books-hero__content">

                        <p class="eyebrow">
                            <?php
                            esc_html_e(
                                'Lesen · verstehen · einordnen',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <h1>
                            <?php
                            esc_html_e(
                                'Bücher, die Wissen vertiefen.',
                                'jung-leben'
                            );
                            ?>
                        </h1>


                        <p class="books-hero__lead">
                            <?php
                            esc_html_e(
                                'Nicht jede gute Idee passt in einen kurzen Artikel. In unserer Buchrubrik möchten wir Werke sammeln, die Themen wie Longevity, Schlaf, Ernährung, Psychologie und Wohlbefinden fundiert vertiefen.',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <p class="books-hero__text">
                            <?php
                            esc_html_e(
                                'Dabei geht es nicht um möglichst lange Bestsellerlisten. Entscheidend ist, welche Bücher uns fachlich oder persönlich weiterbringen und welche Gedanken auch nach dem Lesen im Alltag hängen bleiben.',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <div class="books-hero__tags">

                            <span>
                                <?php
                                esc_html_e(
                                    'Longevity',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                esc_html_e(
                                    'Schlaf',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                esc_html_e(
                                    'Ernährung',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                            <span>
                                <?php
                                esc_html_e(
                                    'Psychologie',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                        </div>

                    </div>


                    <!-- =========================================
                         VISUELLES BÜCHERREGAL
                         ========================================= -->

                    <div
                        class="books-hero__visual"
                        aria-hidden="true"
                    >

                        <div class="books-hero-stack">

                            <div
                                class="
                                    books-hero-stack__book
                                    books-hero-stack__book--one
                                "
                            >
                                <span>
                                    JL
                                </span>
                            </div>


                            <div
                                class="
                                    books-hero-stack__book
                                    books-hero-stack__book--two
                                "
                            >
                                <span>
                                    READ
                                </span>
                            </div>


                            <div
                                class="
                                    books-hero-stack__book
                                    books-hero-stack__book--three
                                "
                            >
                                <span>
                                    01
                                </span>
                            </div>

                        </div>


                        <p class="books-hero__visual-note">
                            <?php
                            esc_html_e(
                                'Wissen, das über die letzte Seite hinaus wirkt.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 REDAKTIONELLER ANSATZ
                 ================================================= -->

            <section class="books-principles">

                <div class="container">

                    <div class="books-section-heading">

                        <p class="eyebrow">
                            <?php
                            esc_html_e(
                                'Unsere Einordnung',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <h2>
                            <?php
                            esc_html_e(
                                'Keine beliebige Bücherliste.',
                                'jung-leben'
                            );
                            ?>
                        </h2>


                        <p>
                            <?php
                            esc_html_e(
                                'Buchempfehlungen sollen auf Jung Leben denselben Grundsätzen folgen wie Produkte und Routinen: nachvollziehbar, thematisch passend und mit einer klaren redaktionellen Einordnung.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>


                    <div class="books-principles__grid">

                        <article class="books-principle">

                            <span
                                class="books-principle__number"
                                aria-hidden="true"
                            >
                                01
                            </span>


                            <h3>
                                <?php
                                esc_html_e(
                                    'Lesen statt sammeln',
                                    'jung-leben'
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                esc_html_e(
                                    'Langfristig möchten wir vor allem Bücher vorstellen, mit denen wir uns tatsächlich auseinandergesetzt haben.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </article>


                        <article class="books-principle">

                            <span
                                class="books-principle__number"
                                aria-hidden="true"
                            >
                                02
                            </span>


                            <h3>
                                <?php
                                esc_html_e(
                                    'Einordnen statt nacherzählen',
                                    'jung-leben'
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                esc_html_e(
                                    'Nicht die Inhaltsangabe steht im Vordergrund, sondern die Frage: Was war besonders spannend, hilfreich oder diskussionswürdig?',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </article>


                        <article class="books-principle">

                            <span
                                class="books-principle__number"
                                aria-hidden="true"
                            >
                                03
                            </span>


                            <h3>
                                <?php
                                esc_html_e(
                                    'Zum Thema passend',
                                    'jung-leben'
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                esc_html_e(
                                    'Ein Buch erscheint nicht bei uns, weil es gerade populär ist, sondern weil es einen sinnvollen Bezug zu den Themen von Jung Leben hat.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </article>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 BUCHAUSWAHL
                 ================================================= -->

            <section class="books-selection">

                <div class="container">

                    <header class="books-selection__header">

                        <div>

                            <p class="eyebrow">
                                <?php
                                esc_html_e(
                                    'Beispielhafte Auswahl',
                                    'jung-leben'
                                );
                                ?>
                            </p>


                            <h2>
                                <?php
                                esc_html_e(
                                    'Themen, in die wir tiefer eintauchen möchten.',
                                    'jung-leben'
                                );
                                ?>
                            </h2>

                        </div>


                        <p class="books-selection__intro">
                            <?php
                            esc_html_e(
                                'Diese Auswahl zeigt beispielhaft, wie Bücher künftig auf Jung Leben vorgestellt und mit persönlichen Erfahrungen ergänzt werden können.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </header>


                    <div class="books-grid">

                        <?php
                        foreach (
                            $books
                            as $book
                        ) :
                            ?>

                            <article class="book-card">

                                <!-- =============================
                                     COVER
                                     ============================= -->

                                <div
                                    class="
                                        book-card__cover
                                        book-card__cover--<?php
                                        echo esc_attr(
                                            $book[
                                                'cover_variant'
                                            ]
                                        );
                                        ?>
                                    "
                                    aria-hidden="true"
                                >

                                    <div class="book-card__cover-inner">

                                        <span class="book-card__cover-mark">
                                            <?php
                                            echo esc_html(
                                                $book[
                                                    'mark'
                                                ]
                                            );
                                            ?>
                                        </span>


                                        <span class="book-card__cover-line"></span>


                                        <span class="book-card__cover-brand">
                                            Jung Leben
                                        </span>

                                    </div>

                                </div>


                                <!-- =============================
                                     CONTENT
                                     ============================= -->

                                <div class="book-card__content">

                                    <div class="book-card__meta">

                                        <span class="book-card__category">
                                            <?php
                                            echo esc_html(
                                                $book[
                                                    'category'
                                                ]
                                            );
                                            ?>
                                        </span>


                                        <span class="book-card__format">
                                            <?php
                                            esc_html_e(
                                                'Buch',
                                                'jung-leben'
                                            );
                                            ?>
                                        </span>

                                    </div>


                                    <h3 class="book-card__title">
                                        <?php
                                        echo esc_html(
                                            $book[
                                                'title'
                                            ]
                                        );
                                        ?>
                                    </h3>


                                    <p class="book-card__author">
                                        <?php
                                        printf(
                                            esc_html__(
                                                'von %s',
                                                'jung-leben'
                                            ),
                                            esc_html(
                                                $book[
                                                    'author'
                                                ]
                                            )
                                        );
                                        ?>
                                    </p>


                                    <p class="book-card__intro">
                                        <?php
                                        echo esc_html(
                                            $book[
                                                'intro'
                                            ]
                                        );
                                        ?>
                                    </p>


                                    <div class="book-card__detail">

                                        <span class="book-card__detail-label">
                                            <?php
                                            esc_html_e(
                                                'Warum das Thema spannend ist',
                                                'jung-leben'
                                            );
                                            ?>
                                        </span>


                                        <p>
                                            <?php
                                            echo esc_html(
                                                $book[
                                                    'focus'
                                                ]
                                            );
                                            ?>
                                        </p>

                                    </div>


                                    <div class="book-card__detail">

                                        <span class="book-card__detail-label">
                                            <?php
                                            esc_html_e(
                                                'Für wen interessant',
                                                'jung-leben'
                                            );
                                            ?>
                                        </span>


                                        <p>
                                            <?php
                                            echo esc_html(
                                                $book[
                                                    'suitable_for'
                                                ]
                                            );
                                            ?>
                                        </p>

                                    </div>


                                    <div class="book-card__footer">

                                        <?php
                                        if (
                                            $book[
                                                'shop_url'
                                            ] !== ''
                                        ) :
                                            ?>

                                            <a
                                                href="<?php
                                                echo esc_url(
                                                    $book[
                                                        'shop_url'
                                                    ]
                                                );
                                                ?>"
                                                class="book-card__shop-link"
                                                target="_blank"
                                                rel="noopener noreferrer sponsored"
                                            >

                                                <span>
                                                    <?php
                                                    esc_html_e(
                                                        'Bei Orell Füssli ansehen',
                                                        'jung-leben'
                                                    );
                                                    ?>
                                                </span>


                                                <span aria-hidden="true">
                                                    ↗
                                                </span>

                                            </a>

                                        <?php else : ?>

                                            <div class="book-card__planned-link">

                                                <span class="book-card__planned-dot"></span>


                                                <span>
                                                    <?php
                                                    esc_html_e(
                                                        'Geplante Verlinkung über Orell Füssli',
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
                 ZUKÜNFTIGE PERSÖNLICHE ERFAHRUNG
                 ================================================= -->

            <section class="books-experience">

                <div class="container books-experience__grid">

                    <div class="books-experience__intro">

                        <p class="eyebrow">
                            <?php
                            esc_html_e(
                                'Mehr als eine Empfehlung',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <h2>
                            <?php
                            esc_html_e(
                                'Was bleibt nach dem Lesen?',
                                'jung-leben'
                            );
                            ?>
                        </h2>

                    </div>


                    <div class="books-experience__content">

                        <p class="books-experience__lead">
                            <?php
                            esc_html_e(
                                'Genau dort soll unsere Buchrubrik künftig interessant werden.',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <p>
                            <?php
                            esc_html_e(
                                'Bei gelesenen Büchern möchten wir ergänzen, welche Gedanken besonders hängen geblieben sind, was wir kritisch sehen und ob das Buch etwas an der eigenen Sichtweise oder am Alltag verändert hat.',
                                'jung-leben'
                            );
                            ?>
                        </p>


                        <p>
                            <?php
                            esc_html_e(
                                'So wird aus einem einfachen Shoplink eine persönliche Empfehlung mit Kontext – und Leserinnen und Leser können besser einschätzen, ob ein Buch für sie wirklich interessant sein könnte.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 PARTNER-DEMO-HINWEIS
                 ================================================= -->

            <section class="books-partner-preview">

                <div class="container">

                    <div class="books-partner-preview__box">

                        <div class="books-partner-preview__mark">
                            <span aria-hidden="true">
                                ↗
                            </span>
                        </div>


                        <div class="books-partner-preview__content">

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
                                    'Geplante Einbindung von Orell Füssli.',
                                    'jung-leben'
                                );
                                ?>
                            </h2>


                            <p>
                                <?php
                                esc_html_e(
                                    'Diese Seite zeigt die geplante redaktionelle Integration von Buchempfehlungen auf Jung Leben. Aktuell besteht noch keine Affiliate-Partnerschaft mit Orell Füssli. Deshalb werden an dieser Stelle noch keine Affiliate-Links eingesetzt.',
                                    'jung-leben'
                                );
                                ?>
                            </p>


                            <p>
                                <?php
                                esc_html_e(
                                    'Nach einer Freischaltung sollen ausgewählte Bücher direkt mit dem jeweiligen Titel bei Orell Füssli verknüpft werden. Die redaktionelle Auswahl und Bewertung bleibt dabei unabhängig von einer Partnerschaft.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 ZURÜCK ZU EMPFEHLUNGEN
                 ================================================= -->

            <section class="books-back">

                <div class="container">

                    <a
                        href="<?php
                        echo esc_url(
                            $recommendations_url
                        );
                        ?>"
                        class="books-back__link"
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