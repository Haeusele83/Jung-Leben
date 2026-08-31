<?php
/**
 * Seite «Über mich».
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

get_header();


/* =========================================================
   HILFSFUNKTION
   ========================================================= */

/**
 * ACF-Feld sicher lesen.
 */
$get_about_field = static function (
    string $field_name,
    mixed $fallback = ''
): mixed {
    if (! function_exists('get_field')) {
        return $fallback;
    }

    $value =
        get_field(
            $field_name
        );

    if (
        $value === null
        || $value === ''
    ) {
        return $fallback;
    }

    return $value;
};


/* =========================================================
   SEITENKOPF
   ========================================================= */

$eyebrow =
    (string)
    $get_about_field(
        'jl_about_eyebrow',
        'Über Jung Leben'
    );

$title =
    (string)
    $get_about_field(
        'jl_about_title',
        'Gesundheit bewusster betrachten.'
    );

$lead =
    (string)
    $get_about_field(
        'jl_about_lead',
        'Jung Leben ist aus der persönlichen Auseinandersetzung mit Gesundheit, Vitalität und der Frage entstanden, welche Entscheidungen langfristig wirklich guttun.'
    );


$portrait =
    $get_about_field(
        'jl_about_portrait',
        []
    );


$portrait_id = 0;


if (
    is_array(
        $portrait
    )
    && isset(
        $portrait[
            'ID'
        ]
    )
) {
    $portrait_id =
        absint(
            $portrait[
                'ID'
            ]
        );
} else {
    $portrait_id =
        absint(
            $portrait
        );
}


/* =========================================================
   GESCHICHTE
   ========================================================= */

$story_eyebrow =
    (string)
    $get_about_field(
        'jl_about_story_eyebrow',
        'Der persönliche Ausgangspunkt'
    );

$story_title =
    (string)
    $get_about_field(
        'jl_about_story_title',
        'Aus persönlicher Neugier wurde Jung Leben.'
    );

$story_text_one =
    (string)
    $get_about_field(
        'jl_about_story_text_one',
        'Im Mittelpunkt stand von Anfang an die Frage, wie sich Gesundheit und Wohlbefinden mit bewussten Entscheidungen im Alltag unterstützen lassen. Dabei entstanden persönliche Erfahrungen mit Produkten, Routinen und unterschiedlichen Ansätzen.'
    );

$story_text_two =
    (string)
    $get_about_field(
        'jl_about_story_text_two',
        'Jung Leben soll diese Erfahrungen nicht als allgemeingültige Wahrheit präsentieren. Die Plattform möchte vielmehr zeigen, was ausprobiert wurde, welche Beobachtungen daraus entstanden sind und wie Produkte und Informationen sinnvoll eingeordnet werden können.'
    );

$story_text_three =
    (string)
    $get_about_field(
        'jl_about_story_text_three',
        'So entsteht ein Ort für Menschen, die sich selbst informieren, Zusammenhänge verstehen und bewusste Entscheidungen treffen möchten.'
    );

$story_statement_label =
    (string)
    $get_about_field(
        'jl_about_story_statement_label',
        'Jung Leben'
    );

$story_statement_text =
    (string)
    $get_about_field(
        'jl_about_story_statement_text',
        'Nicht möglichst viel verändern – sondern bewusster entscheiden, was langfristig zum eigenen Leben passt.'
    );


/* =========================================================
   HALTUNG
   ========================================================= */

$values_eyebrow =
    (string)
    $get_about_field(
        'jl_about_values_eyebrow',
        'Wofür Jung Leben steht'
    );

$values_title =
    (string)
    $get_about_field(
        'jl_about_values_title',
        'Orientierung statt Versprechen.'
    );

$values_intro =
    (string)
    $get_about_field(
        'jl_about_values_intro',
        'Drei Grundsätze bestimmen, wie Inhalte und Empfehlungen auf Jung Leben entstehen.'
    );


$value_one_title =
    (string)
    $get_about_field(
        'jl_about_value_one_title',
        'Persönlich'
    );

$value_one_text =
    (string)
    $get_about_field(
        'jl_about_value_one_text',
        'Erfahrungen werden als persönliche Beobachtungen beschrieben und nicht als allgemeingültige Versprechen dargestellt.'
    );


$value_two_title =
    (string)
    $get_about_field(
        'jl_about_value_two_title',
        'Nachvollziehbar'
    );

$value_two_text =
    (string)
    $get_about_field(
        'jl_about_value_two_text',
        'Produkte und Routinen werden in einen verständlichen Kontext eingeordnet, statt isoliert präsentiert zu werden.'
    );


$value_three_title =
    (string)
    $get_about_field(
        'jl_about_value_three_title',
        'Transparent'
    );

$value_three_text =
    (string)
    $get_about_field(
        'jl_about_value_three_text',
        'Partnerschaften und Affiliate-Links werden transparent gekennzeichnet. Sie bestimmen nicht, welche Inhalte oder Erfahrungen auf Jung Leben erscheinen.'
    );


/* =========================================================
   WARUM JUNG LEBEN
   ========================================================= */

$why_eyebrow =
    (string)
    $get_about_field(
        'jl_about_why_eyebrow',
        'Warum Jung Leben'
    );

$why_title =
    (string)
    $get_about_field(
        'jl_about_why_title',
        'Nicht das eine perfekte Produkt. Sondern bessere Orientierung.'
    );

$why_text_one =
    (string)
    $get_about_field(
        'jl_about_why_text_one',
        'Die Welt rund um Longevity, Nahrungsergänzungen und Wohlbefinden ist gross. Produkte versprechen viel, Informationen widersprechen sich und persönliche Bedürfnisse unterscheiden sich.'
    );

$why_text_two =
    (string)
    $get_about_field(
        'jl_about_why_text_two',
        'Jung Leben möchte dabei helfen, Produkte, Erfahrungen und Routinen besser einzuordnen – ohne vorzugeben, dass es für alle dieselbe richtige Lösung gibt.'
    );


/* =========================================================
   ABSCHLUSS
   ========================================================= */

$cta_enabled_value =
    $get_about_field(
        'jl_about_cta_enabled',
        true
    );

$cta_enabled =
    (bool)
    $cta_enabled_value;


$cta_eyebrow =
    (string)
    $get_about_field(
        'jl_about_cta_eyebrow',
        'Entdecke Jung Leben'
    );

$cta_title =
    (string)
    $get_about_field(
        'jl_about_cta_title',
        'Starte dort, wo es für dich interessant wird.'
    );

$cta_text =
    (string)
    $get_about_field(
        'jl_about_cta_text',
        'Entdecke persönliche Erfahrungen, ausgewählte Empfehlungen und mögliche Routinen.'
    );

$cta_button_text =
    (string)
    $get_about_field(
        'jl_about_cta_button_text',
        'Erfahrungen entdecken'
    );


/* =========================================================
   LINKS
   ========================================================= */

/**
 * Ratgeber / Erfahrungen.
 */
$posts_page_id =
    (int)
    get_option(
        'page_for_posts'
    );

$experiences_url =
    $posts_page_id > 0
        ? (string)
            get_permalink(
                $posts_page_id
            )
        : home_url(
            '/ratgeber/'
        );


/**
 * Individuelles CTA-Ziel.
 */
$cta_button_url =
    trim(
        (string)
        $get_about_field(
            'jl_about_cta_button_url',
            ''
        )
    );


if (
    $cta_button_url === ''
) {
    $cta_button_url =
        $experiences_url;
}


/**
 * Empfehlungen.
 */
$recommendations_page =
    get_page_by_path(
        'empfehlungen'
    );

$recommendations_url =
    $recommendations_page instanceof WP_Post
        ? (string)
            get_permalink(
                $recommendations_page
            )
        : home_url(
            '/empfehlungen/'
        );
?>

<main
    id="main-content"
    class="about-page"
>

    <!-- =====================================================
         SEITENKOPF
         ===================================================== -->

    <section class="about-hero">

        <div
            class="
                container
                about-hero__grid
                <?php
                echo $portrait_id <= 0
                    ? 'about-hero__grid--without-image'
                    : '';
                ?>
            "
        >

            <div class="about-hero__content">

                <?php if ($eyebrow !== '') : ?>

                    <p class="eyebrow">
                        <?php
                        echo esc_html(
                            $eyebrow
                        );
                        ?>
                    </p>

                <?php endif; ?>


                <h1>
                    <?php
                    echo esc_html(
                        $title
                    );
                    ?>
                </h1>


                <?php if ($lead !== '') : ?>

                    <p class="about-hero__lead">
                        <?php
                        echo esc_html(
                            $lead
                        );
                        ?>
                    </p>

                <?php endif; ?>


                <div class="about-hero__keywords">

                    <span>
                        Persönlich
                    </span>

                    <span>
                        Transparent
                    </span>

                    <span>
                        Alltagstauglich
                    </span>

                </div>

            </div>


            <?php if ($portrait_id > 0) : ?>

                <figure class="about-hero__media">

                    <?php
                    echo wp_get_attachment_image(
                        $portrait_id,
                        'large',
                        false,
                        [
                            'class' =>
                                'about-hero__image',

                            'loading' =>
                                'eager',
                        ]
                    );
                    ?>


                    <figcaption class="about-hero__caption">

                        <strong>
                            Roberto
                        </strong>

                        <span>
                            Hinter Jung Leben
                        </span>

                    </figcaption>

                </figure>

            <?php endif; ?>

        </div>

    </section>


    <!-- =====================================================
         GESCHICHTE
         ===================================================== -->

    <section class="about-story">

        <div class="container about-story__grid">

            <div class="about-story__heading">

                <?php if ($story_eyebrow !== '') : ?>

                    <p class="eyebrow">
                        <?php
                        echo esc_html(
                            $story_eyebrow
                        );
                        ?>
                    </p>

                <?php endif; ?>


                <h2>
                    <?php
                    echo esc_html(
                        $story_title
                    );
                    ?>
                </h2>

            </div>


            <div class="about-story__content">

                <div class="about-story__text">

                    <?php if ($story_text_one !== '') : ?>

                        <p>
                            <?php
                            echo esc_html(
                                $story_text_one
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($story_text_two !== '') : ?>

                        <p>
                            <?php
                            echo esc_html(
                                $story_text_two
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($story_text_three !== '') : ?>

                        <p>
                            <?php
                            echo esc_html(
                                $story_text_three
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>


                <?php if ($story_statement_text !== '') : ?>

                    <div class="about-story__statement">

                        <?php if ($story_statement_label !== '') : ?>

                            <span>
                                <?php
                                echo esc_html(
                                    $story_statement_label
                                );
                                ?>
                            </span>

                        <?php endif; ?>


                        <p>
                            <?php
                            echo esc_html(
                                $story_statement_text
                            );
                            ?>
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =====================================================
         HALTUNG / GRUNDSÄTZE
         ===================================================== -->

    <section class="about-principles">

        <div class="container">

            <div class="about-principles__header">

                <?php if ($values_eyebrow !== '') : ?>

                    <p class="eyebrow">
                        <?php
                        echo esc_html(
                            $values_eyebrow
                        );
                        ?>
                    </p>

                <?php endif; ?>


                <h2>
                    <?php
                    echo esc_html(
                        $values_title
                    );
                    ?>
                </h2>


                <?php if ($values_intro !== '') : ?>

                    <p class="about-principles__intro">
                        <?php
                        echo esc_html(
                            $values_intro
                        );
                        ?>
                    </p>

                <?php endif; ?>

            </div>


            <div class="about-principles__grid">

                <article class="about-principle">

                    <h3>
                        <?php
                        echo esc_html(
                            $value_one_title
                        );
                        ?>
                    </h3>

                    <p>
                        <?php
                        echo esc_html(
                            $value_one_text
                        );
                        ?>
                    </p>

                </article>


                <article class="about-principle">

                    <h3>
                        <?php
                        echo esc_html(
                            $value_two_title
                        );
                        ?>
                    </h3>

                    <p>
                        <?php
                        echo esc_html(
                            $value_two_text
                        );
                        ?>
                    </p>

                </article>


                <article class="about-principle">

                    <h3>
                        <?php
                        echo esc_html(
                            $value_three_title
                        );
                        ?>
                    </h3>

                    <p>
                        <?php
                        echo esc_html(
                            $value_three_text
                        );
                        ?>
                    </p>

                </article>

            </div>

        </div>

    </section>


    <!-- =====================================================
         WARUM JUNG LEBEN
         ===================================================== -->

    <section class="about-story about-why">

        <div class="container about-story__grid">

            <div class="about-story__heading">

                <?php if ($why_eyebrow !== '') : ?>

                    <p class="eyebrow">
                        <?php
                        echo esc_html(
                            $why_eyebrow
                        );
                        ?>
                    </p>

                <?php endif; ?>


                <h2>
                    <?php
                    echo esc_html(
                        $why_title
                    );
                    ?>
                </h2>

            </div>


            <div class="about-story__content">

                <div class="about-story__text">

                    <?php if ($why_text_one !== '') : ?>

                        <p>
                            <?php
                            echo esc_html(
                                $why_text_one
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($why_text_two !== '') : ?>

                        <p>
                            <?php
                            echo esc_html(
                                $why_text_two
                            );
                            ?>
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ABSCHLUSS
         ===================================================== -->

    <?php if ($cta_enabled) : ?>

        <section class="about-discover">

            <div class="container">

                <div class="about-discover__header">

                    <?php if ($cta_eyebrow !== '') : ?>

                        <p class="eyebrow">
                            <?php
                            echo esc_html(
                                $cta_eyebrow
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <div class="about-discover__heading-grid">

                        <h2>
                            <?php
                            echo esc_html(
                                $cta_title
                            );
                            ?>
                        </h2>


                        <div>

                            <?php if ($cta_text !== '') : ?>

                                <p>
                                    <?php
                                    echo esc_html(
                                        $cta_text
                                    );
                                    ?>
                                </p>

                            <?php endif; ?>


                            <?php if ($cta_button_text !== '') : ?>

                                <a
                                    href="<?php
                                    echo esc_url(
                                        $cta_button_url
                                    );
                                    ?>"
                                    class="btn btn-primary"
                                >
                                    <?php
                                    echo esc_html(
                                        $cta_button_text
                                    );
                                    ?>
                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>


                <div class="about-discover__grid">

                    <!-- Erfahrungen -->
                    <a
                        href="<?php
                        echo esc_url(
                            $experiences_url
                        );
                        ?>"
                        class="
                            about-discover-card
                            about-discover-card--experiences
                        "
                    >

                        <div class="about-discover-card__top">

                            <span class="about-discover-card__label">
                                Wissen & persönliche Einblicke
                            </span>

                            <span
                                class="about-discover-card__arrow"
                                aria-hidden="true"
                            >
                                ↗
                            </span>

                        </div>


                        <div class="about-discover-card__content">

                            <h3>
                                Erfahrungen
                            </h3>

                            <p>
                                Persönliche Erfahrungen, Hintergründe und Impulse rund um Gesundheit, Longevity und einen bewussten Alltag.
                            </p>

                        </div>

                    </a>


                    <!-- Empfehlungen -->
                    <a
                        href="<?php
                        echo esc_url(
                            $recommendations_url
                        );
                        ?>"
                        class="
                            about-discover-card
                            about-discover-card--recommendations
                        "
                    >

                        <div class="about-discover-card__top">

                            <span class="about-discover-card__label">
                                Ausgewählt & eingeordnet
                            </span>

                            <span
                                class="about-discover-card__arrow"
                                aria-hidden="true"
                            >
                                ↗
                            </span>

                        </div>


                        <div class="about-discover-card__content">

                            <h3>
                                Empfehlungen
                            </h3>

                            <p>
                                Produkte und Lösungen, die transparent eingeordnet und mit Erfahrungen und Hintergrundwissen verbunden werden.
                            </p>

                        </div>

                    </a>

                </div>

            </div>

        </section>

    <?php endif; ?>

</main>

<?php
get_footer();