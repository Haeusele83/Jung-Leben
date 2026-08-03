<?php
/**
 * Startseite von Jung Leben.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

get_header();

/**
 * ID der aktuell angezeigten Startseite.
 */
$front_page_id = (int) get_queried_object_id();

/**
 * URL der WordPress-Beitragsseite ermitteln.
 */
$ratgeber_page_id = (int) get_option('page_for_posts');

$ratgeber_url = $ratgeber_page_id > 0
    ? (string) get_permalink($ratgeber_page_id)
    : home_url('/ratgeber/');

/**
 * Empfehlungsseite ermitteln.
 */
$empfehlungen_page = get_page_by_path('empfehlungen');

$empfehlungen_url = $empfehlungen_page instanceof WP_Post
    ? (string) get_permalink($empfehlungen_page)
    : home_url('/empfehlungen/');

/**
 * Routinen-Seite ermitteln.
 */
$routinen_page = get_page_by_path('routinen');

$routinen_url = $routinen_page instanceof WP_Post
    ? (string) get_permalink($routinen_page)
    : home_url('/routinen/');

/**
 * Kontaktseite ermitteln.
 */
$kontakt_page = get_page_by_path('kontakt');

$kontakt_url = $kontakt_page instanceof WP_Post
    ? (string) get_permalink($kontakt_page)
    : home_url('/kontakt/');

/**
 * Seite «Über mich» ermitteln.
 */
$ueber_mich_page = get_page_by_path('ueber-mich');

$ueber_mich_url = $ueber_mich_page instanceof WP_Post
    ? (string) get_permalink($ueber_mich_page)
    : home_url('/ueber-mich/');

/**
 * ACF-Textfeld laden und bei einem leeren Feld einen
 * definierten Standardwert verwenden.
 */
$get_home_text = static function (
    string $field_name,
    string $fallback
) use ($front_page_id): string {
    if (! function_exists('get_field')) {
        return $fallback;
    }

    $value = get_field(
        $field_name,
        $front_page_id
    );

    if (! is_string($value)) {
        return $fallback;
    }

    $value = trim($value);

    return $value !== ''
        ? $value
        : $fallback;
};

/**
 * ACF-URL laden und bei einem leeren Feld die automatisch
 * ermittelte WordPress-Adresse verwenden.
 */
$get_home_url = static function (
    string $field_name,
    string $fallback
) use ($front_page_id): string {
    if (! function_exists('get_field')) {
        return $fallback;
    }

    $value = get_field(
        $field_name,
        $front_page_id
    );

    if (! is_string($value)) {
        return $fallback;
    }

    $value = trim($value);

    return $value !== ''
        ? $value
        : $fallback;
};

/**
 * Inhalte des Hero-Bereichs laden.
 */
$hero_eyebrow = $get_home_text(
    'jl_home_hero_eyebrow',
    'Longevity ist kein Sprint und kein Zufall'
);

$hero_title = $get_home_text(
    'jl_home_hero_title',
    'The goal is to die young – as late as possible'
);

$hero_text = $get_home_text(
    'jl_home_hero_text',
    'Es sind die täglichen Entscheidungen, die darüber bestimmen, wie wir altern. Jung Leben hilft dir dabei, evidenzbasierte Produkte, Routinen und Erfahrungen zu entdecken, die Vitalität, Wohlbefinden und Langlebigkeit unterstützen.'
);

$hero_button_one_text = $get_home_text(
    'jl_home_hero_button_one_text',
    'Empfehlungen entdecken'
);

$hero_button_one_url = $get_home_url(
    'jl_home_hero_button_one_url',
    $empfehlungen_url
);

$hero_button_two_text = $get_home_text(
    'jl_home_hero_button_two_text',
    'Erfahrungen lesen'
);

$hero_button_two_url = $get_home_url(
    'jl_home_hero_button_two_url',
    $ratgeber_url
);

$hero_benefit_one = $get_home_text(
    'jl_home_hero_benefit_one',
    '🌿 Longevity'
);

$hero_benefit_two = $get_home_text(
    'jl_home_hero_benefit_two',
    '✨ Persönliche Erfahrungen'
);

$hero_benefit_three = $get_home_text(
    'jl_home_hero_benefit_three',
    '💡 Bewusste Routinen'
);

/**
 * Optionales ACF-Hintergrundbild laden.
 *
 * Unterstützt sicherheitshalber die Rückgabeformate:
 * - Array
 * - Attachment-ID
 * - direkte URL
 */
$hero_background = function_exists('get_field')
    ? get_field(
        'jl_home_hero_background',
        $front_page_id
    )
    : null;

$hero_background_url = '';

if (
    is_array($hero_background)
    && isset($hero_background['url'])
    && is_string($hero_background['url'])
) {
    $hero_background_url = $hero_background['url'];
} elseif (is_int($hero_background)) {
    $attachment_url = wp_get_attachment_image_url(
        $hero_background,
        'full'
    );

    if (is_string($attachment_url)) {
        $hero_background_url = $attachment_url;
    }
} elseif (is_string($hero_background)) {
    $hero_background_url = trim($hero_background);
}

$hero_style = '';

if ($hero_background_url !== '') {
    $hero_style = sprintf(
        'background-image: url("%s");',
        esc_url_raw($hero_background_url)
    );
}
/**
 * Inhalte des Bereichs «Über Jung Leben» laden.
 */
$about_eyebrow = $get_home_text(
    'jl_home_about_eyebrow',
    'Über Jung Leben'
);

$about_title = $get_home_text(
    'jl_home_about_title',
    'Praktische Erfahrung statt Produkt-Blabla.'
);

$about_text_one = $get_home_text(
    'jl_home_about_text_one',
    'Jung Leben ist aus Robertos persönlicher Auseinandersetzung mit Gesundheit, Vitalität und Langlebigkeit entstanden. Im Mittelpunkt stehen Erfahrungen aus dem Alltag – ehrlich, nachvollziehbar und ohne leere Versprechen.'
);

$about_text_two = $get_home_text(
    'jl_home_about_text_two',
    'Die Plattform verbindet persönliche Beobachtungen mit sorgfältig ausgewählten Produkten, Routinen und fundierten Informationen. Ziel ist nicht, die eine perfekte Lösung zu präsentieren, sondern Orientierung für bewusste Entscheidungen zu geben.'
);

$about_button_text = $get_home_text(
    'jl_home_about_button_text',
    'Mehr über Roberto'
);

$about_button_url = $get_home_url(
    'jl_home_about_button_url',
    $ueber_mich_url
);

/**
 * Optionales Bild für den Bereich «Über Jung Leben».
 */
$about_image = function_exists('get_field')
    ? get_field(
        'jl_home_about_image',
        $front_page_id
    )
    : null;

$about_image_url = '';

if (
    is_array($about_image)
    && isset($about_image['url'])
    && is_string($about_image['url'])
) {
    $about_image_url = $about_image['url'];
} elseif (is_int($about_image)) {
    $attachment_url = wp_get_attachment_image_url(
        $about_image,
        'full'
    );

    if (is_string($attachment_url)) {
        $about_image_url = $attachment_url;
    }
} elseif (is_string($about_image)) {
    $about_image_url = trim($about_image);
}

$about_image_style = '';

if ($about_image_url !== '') {
    $about_image_style = sprintf(
        'background-image: url("%s");',
        esc_url_raw($about_image_url)
    );
}
?>

<main id="main-content">

    <!-- Hero-Bereich -->
    <section
        class="hero home-hero"
        <?php if ($hero_style !== '') : ?>
            style="<?php echo esc_attr($hero_style); ?>"
        <?php endif; ?>
    >
        <div class="hero-overlay"></div>

        <div class="container hero-content home-hero-content">
            <div class="hero-text">

                <p class="eyebrow">
                    <?php echo esc_html($hero_eyebrow); ?>
                </p>

                <h1>
                    <?php echo esc_html($hero_title); ?>
                </h1>

                <p class="hero-lead">
                    <?php echo esc_html($hero_text); ?>
                </p>

                <div class="hero-actions">

                    <?php if ($hero_button_one_text !== '') : ?>
                        <a
                            href="<?php echo esc_url(
                                $hero_button_one_url
                            ); ?>"
                            class="btn btn-primary"
                        >
                            <?php echo esc_html(
                                $hero_button_one_text
                            ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($hero_button_two_text !== '') : ?>
                        <a
                            href="<?php echo esc_url(
                                $hero_button_two_url
                            ); ?>"
                            class="btn btn-light"
                        >
                            <?php echo esc_html(
                                $hero_button_two_text
                            ); ?>
                        </a>
                    <?php endif; ?>

                </div>

                <div class="hero-benefits">

                    <?php if ($hero_benefit_one !== '') : ?>
                        <span>
                            <?php echo esc_html(
                                $hero_benefit_one
                            ); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($hero_benefit_two !== '') : ?>
                        <span>
                            <?php echo esc_html(
                                $hero_benefit_two
                            ); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($hero_benefit_three !== '') : ?>
                        <span>
                            <?php echo esc_html(
                                $hero_benefit_three
                            ); ?>
                        </span>
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </section>

        <!-- Über Jung Leben -->
    <section class="home-curator-section">
        <div class="container home-curator-grid">

            <div
                class="home-curator-image"
                role="img"
                aria-label="<?php echo esc_attr($about_title); ?>"
                <?php if ($about_image_style !== '') : ?>
                    style="<?php echo esc_attr(
                        $about_image_style
                    ); ?>"
                <?php endif; ?>
            ></div>

            <div class="home-curator-text">

                <?php if ($about_eyebrow !== '') : ?>
                    <p class="eyebrow">
                        <?php echo esc_html($about_eyebrow); ?>
                    </p>
                <?php endif; ?>

                <h2>
                    <?php echo esc_html($about_title); ?>
                </h2>

                <?php if ($about_text_one !== '') : ?>
                    <p>
                        <?php echo esc_html($about_text_one); ?>
                    </p>
                <?php endif; ?>

                <?php if ($about_text_two !== '') : ?>
                    <p>
                        <?php echo esc_html($about_text_two); ?>
                    </p>
                <?php endif; ?>

                <?php if ($about_button_text !== '') : ?>
                    <a
                        href="<?php echo esc_url(
                            $about_button_url
                        ); ?>"
                        class="btn btn-primary"
                    >
                        <?php echo esc_html(
                            $about_button_text
                        ); ?>
                    </a>
                <?php endif; ?>

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
                    <line
                        x1="300"
                        y1="260"
                        x2="300"
                        y2="65"
                    ></line>

                    <line
                        x1="300"
                        y1="260"
                        x2="510"
                        y2="155"
                    ></line>

                    <line
                        x1="300"
                        y1="260"
                        x2="510"
                        y2="390"
                    ></line>

                    <line
                        x1="300"
                        y1="260"
                        x2="300"
                        y2="465"
                    ></line>

                    <line
                        x1="300"
                        y1="260"
                        x2="90"
                        y2="390"
                    ></line>

                    <line
                        x1="300"
                        y1="260"
                        x2="90"
                        y2="155"
                    ></line>
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
                    class="orientation-map__node
                    orientation-map__node--experiences"
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
                    class="orientation-map__node
                    orientation-map__node--recommendations"
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
                    class="orientation-map__node
                    orientation-map__node--products"
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
                    class="orientation-map__node
                    orientation-map__node--routines"
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
                    class="orientation-map__node
                    orientation-map__node--knowledge"
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
                    class="orientation-map__node
                    orientation-map__node--exchange"
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

    <!-- Robertos Reise und Erfahrungen -->
    <section
        class="home-journey-section"
        aria-labelledby="journey-title"
    >
        <div class="container home-journey-layout">

            <header class="home-journey-heading">

                <p class="eyebrow">
                    <?php
                    esc_html_e(
                        'Erfahrungen und Empfehlungen',
                        'jung-leben'
                    );
                    ?>
                </p>

                <h2 id="journey-title">
                    <?php
                    esc_html_e(
                        'Robertos Reise und Erfahrungen',
                        'jung-leben'
                    );
                    ?>
                </h2>

                <p class="home-journey-heading__text">
                    <?php
                    esc_html_e(
                        'Persönliche Beobachtungen, bewusste Routinen und ehrliche Einordnungen bilden die Grundlage von Jung Leben.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <a
                    href="<?php echo esc_url($ratgeber_url); ?>"
                    class="btn btn-primary"
                >
                    <?php
                    esc_html_e(
                        'Erfahrungen entdecken',
                        'jung-leben'
                    );
                    ?>
                </a>

            </header>

            <div
                class="journey-slider"
                data-journey-slider
                tabindex="0"
                aria-roledescription="<?php esc_attr_e(
                    'Karussell',
                    'jung-leben'
                ); ?>"
                aria-label="<?php esc_attr_e(
                    'Robertos Reise und Erfahrungen',
                    'jung-leben'
                ); ?>"
            >
                <div class="journey-slider__viewport">

                    <!-- Slide 1 -->
                    <article
                        class="journey-slide is-active"
                        data-journey-slide
                        aria-hidden="false"
                    >
                        <div class="journey-slide__content">

                            <p class="journey-slide__label">
                                <?php
                                esc_html_e(
                                    'Der Ausgangspunkt',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                            <h3>
                                <?php
                                esc_html_e(
                                    'Gesundheit bewusster betrachten.',
                                    'jung-leben'
                                );
                                ?>
                            </h3>

                            <p class="journey-slide__text">
                                <?php
                                esc_html_e(
                                    'Jung Leben entstand aus der persönlichen Auseinandersetzung mit Vitalität, Wohlbefinden und der Frage, welche Entscheidungen langfristig wirklich guttun.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                            <span class="journey-slide__tag">
                                <?php
                                esc_html_e(
                                    'Bewusstsein',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                        </div>
                    </article>

                    <!-- Slide 2 -->
                    <article
                        class="journey-slide"
                        data-journey-slide
                        aria-hidden="true"
                    >
                        <div class="journey-slide__content">

                            <p class="journey-slide__label">
                                <?php
                                esc_html_e(
                                    'Beobachten und ausprobieren',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                            <h3>
                                <?php
                                esc_html_e(
                                    'Erfahrungen entstehen im Alltag.',
                                    'jung-leben'
                                );
                                ?>
                            </h3>

                            <p class="journey-slide__text">
                                <?php
                                esc_html_e(
                                    'Produkte und Routinen werden nicht isoliert betrachtet. Entscheidend ist, wie verständlich sie sind, wie sie sich in den Alltag integrieren lassen und ob sie zur persönlichen Situation passen.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                            <span class="journey-slide__tag">
                                <?php
                                esc_html_e(
                                    'Alltagserfahrung',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                        </div>
                    </article>

                    <!-- Slide 3 -->
                    <article
                        class="journey-slide"
                        data-journey-slide
                        aria-hidden="true"
                    >
                        <div class="journey-slide__content">

                            <p class="journey-slide__label">
                                <?php
                                esc_html_e(
                                    'Einordnen und weitergeben',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                            <h3>
                                <?php
                                esc_html_e(
                                    'Orientierung statt allgemeiner Versprechen.',
                                    'jung-leben'
                                );
                                ?>
                            </h3>

                            <p class="journey-slide__text">
                                <?php
                                esc_html_e(
                                    'Erfahrungen werden offen eingeordnet und mit ergänzenden Informationen verbunden. Jung Leben möchte Möglichkeiten aufzeigen, ohne die eine richtige Lösung vorzuschreiben.',
                                    'jung-leben'
                                );
                                ?>
                            </p>

                            <span class="journey-slide__tag">
                                <?php
                                esc_html_e(
                                    'Orientierung',
                                    'jung-leben'
                                );
                                ?>
                            </span>

                        </div>
                    </article>

                </div>

                <div class="journey-slider__footer">

                    <div class="journey-slider__controls">

                        <button
                            class="journey-slider__arrow"
                            type="button"
                            data-journey-prev
                            aria-label="<?php esc_attr_e(
                                'Vorherige Erfahrung anzeigen',
                                'jung-leben'
                            ); ?>"
                        >
                            <span aria-hidden="true">←</span>
                        </button>

                        <div
                            class="journey-slider__dots"
                            aria-label="<?php esc_attr_e(
                                'Erfahrung auswählen',
                                'jung-leben'
                            ); ?>"
                        >
                            <button
                                class="journey-slider__dot is-active"
                                type="button"
                                data-journey-dot
                                aria-label="<?php esc_attr_e(
                                    'Erfahrung 1 anzeigen',
                                    'jung-leben'
                                ); ?>"
                                aria-current="true"
                            ></button>

                            <button
                                class="journey-slider__dot"
                                type="button"
                                data-journey-dot
                                aria-label="<?php esc_attr_e(
                                    'Erfahrung 2 anzeigen',
                                    'jung-leben'
                                ); ?>"
                                aria-current="false"
                            ></button>

                            <button
                                class="journey-slider__dot"
                                type="button"
                                data-journey-dot
                                aria-label="<?php esc_attr_e(
                                    'Erfahrung 3 anzeigen',
                                    'jung-leben'
                                ); ?>"
                                aria-current="false"
                            ></button>
                        </div>

                        <button
                            class="journey-slider__arrow"
                            type="button"
                            data-journey-next
                            aria-label="<?php esc_attr_e(
                                'Nächste Erfahrung anzeigen',
                                'jung-leben'
                            ); ?>"
                        >
                            <span aria-hidden="true">→</span>
                        </button>

                    </div>

                    <p
                        class="journey-slider__status"
                        aria-live="polite"
                    >
                        <span data-journey-current>1</span>
                        <span aria-hidden="true"> / </span>
                        <span>3</span>
                    </p>

                </div>
            </div>

        </div>
    </section>

    <!-- Mögliche Tagesroutinen -->
    <section
        class="home-routines-section"
        aria-labelledby="routines-title"
    >
        <div class="container home-routines-layout">

            <div class="home-routines-copy">

                <p class="eyebrow">
                    <?php
                    esc_html_e(
                        'Mögliche Tagesroutinen',
                        'jung-leben'
                    );
                    ?>
                </p>

                <h2 id="routines-title">
                    <?php
                    esc_html_e(
                        'Eine mögliche Tagesstruktur für Longevity-Produkte.',
                        'jung-leben'
                    );
                    ?>
                </h2>

                <p>
                    <?php
                    esc_html_e(
                        'Roberto ordnet ausgewählte Produkte nach Tageszeit: morgens für Energie und Zellstoffwechsel, mittags für Balance und Pflanzenstoffe, abends für Entspannung und Regeneration.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <p>
                    <?php
                    esc_html_e(
                        'Die dargestellte Routine ist keine fixe Einnahmeempfehlung, sondern eine persönliche Orientierung für Menschen, die bewusst mit Nahrungsergänzungen und Produktkombinationen umgehen möchten.',
                        'jung-leben'
                    );
                    ?>
                </p>

                <a
                    href="<?php echo esc_url($routinen_url); ?>"
                    class="btn btn-primary"
                >
                    <?php
                    esc_html_e(
                        'Routine ansehen',
                        'jung-leben'
                    );
                    ?>
                </a>

            </div>

            <div class="home-routines-card">

                <article
                    class="routine-time
                    routine-time--morning"
                >
                    <div
                        class="routine-time__icon"
                        aria-hidden="true"
                    >
                        ☀
                    </div>

                    <div class="routine-time__content">
                        <p class="routine-time__label">
                            <?php
                            esc_html_e(
                                'Morgens',
                                'jung-leben'
                            );
                            ?>
                        </p>

                        <p class="routine-time__products">
                            <?php
                            esc_html_e(
                                'NADH, Ashwagandha, Q10, Resveratrol',
                                'jung-leben'
                            );
                            ?>
                        </p>
                    </div>
                </article>

                <article
                    class="routine-time
                    routine-time--midday"
                >
                    <div
                        class="routine-time__icon"
                        aria-hidden="true"
                    >
                        🌿
                    </div>

                    <div class="routine-time__content">
                        <p class="routine-time__label">
                            <?php
                            esc_html_e(
                                'Mittags',
                                'jung-leben'
                            );
                            ?>
                        </p>

                        <p class="routine-time__products">
                            <?php
                            esc_html_e(
                                'Omega 3-6-9, Shilajit, OPC, Quercetin',
                                'jung-leben'
                            );
                            ?>
                        </p>
                    </div>
                </article>

                <article
                    class="routine-time
                    routine-time--evening"
                >
                    <div
                        class="routine-time__icon"
                        aria-hidden="true"
                    >
                        ☾
                    </div>

                    <div class="routine-time__content">
                        <p class="routine-time__label">
                            <?php
                            esc_html_e(
                                'Abends',
                                'jung-leben'
                            );
                            ?>
                        </p>

                        <p class="routine-time__products">
                            <?php
                            esc_html_e(
                                'Magnesium, Weihrauch, Oreganoöl',
                                'jung-leben'
                            );
                            ?>
                        </p>
                    </div>
                </article>

                <aside class="home-routines-notice">
                    <div
                        class="home-routines-notice__icon"
                        aria-hidden="true"
                    >
                        i
                    </div>

                    <p>
                        <?php
                        esc_html_e(
                            'Produkte, Kombinationen, Dosierungen und Einnahmedauer sind individuell. Einzelne Produkte können nur für eine begrenzte Zeit sinnvoll sein. Passe deine Routine an dein persönliches Empfinden an und hole bei Unsicherheiten fachlichen Rat ein.',
                            'jung-leben'
                        );
                        ?>
                    </p>
                </aside>

            </div>
        </div>
    </section>

    <!-- Community und Kontakt -->
    <section
        class="home-community-section"
        aria-labelledby="community-title"
    >
        <div class="container">

            <div class="home-community-card">

                <div class="home-community-content">

                    <p class="eyebrow">
                        <?php
                        esc_html_e(
                            'Du bist dran',
                            'jung-leben'
                        );
                        ?>
                    </p>

                    <h2 id="community-title">
                        <?php
                        esc_html_e(
                            'Hast du ein Anliegen oder eine Erfahrung, die du teilen möchtest?',
                            'jung-leben'
                        );
                        ?>
                    </h2>

                    <p class="home-community-lead">
                        <?php
                        esc_html_e(
                            'Jung Leben lebt nicht nur von Empfehlungen und Informationen, sondern auch vom persönlichen Austausch. Deine Erfahrungen, Fragen und Beobachtungen können wertvolle Impulse liefern.',
                            'jung-leben'
                        );
                        ?>
                    </p>

                    <p>
                        <?php
                        esc_html_e(
                            'Erzähl uns, was dich beschäftigt, welche Routinen dir helfen oder welche Themen du auf Jung Leben gerne wiederfinden möchtest.',
                            'jung-leben'
                        );
                        ?>
                    </p>

                </div>

                <div class="home-community-action">

                    <div
                        class="home-community-symbol"
                        aria-hidden="true"
                    >
                        <span
                            class="home-community-symbol__center"
                        >
                            JL
                        </span>

                        <span
                            class="home-community-symbol__point
                            home-community-symbol__point--one"
                        ></span>

                        <span
                            class="home-community-symbol__point
                            home-community-symbol__point--two"
                        ></span>

                        <span
                            class="home-community-symbol__point
                            home-community-symbol__point--three"
                        ></span>

                        <span
                            class="home-community-symbol__point
                            home-community-symbol__point--four"
                        ></span>
                    </div>

                    <a
                        href="<?php echo esc_url($kontakt_url); ?>"
                        class="btn btn-primary
                        home-community-button"
                    >
                        <?php
                        esc_html_e(
                            'Kontaktiere uns',
                            'jung-leben'
                        );
                        ?>

                        <span aria-hidden="true">→</span>
                    </a>

                    <p class="home-community-note">
                        <?php
                        esc_html_e(
                            'Wir freuen uns auf deine Nachricht.',
                            'jung-leben'
                        );
                        ?>
                    </p>

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