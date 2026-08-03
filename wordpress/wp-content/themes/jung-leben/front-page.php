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
 * ACF-Ja-/Nein-Feld laden.
 */
$get_home_bool = static function (
    string $field_name,
    bool $fallback = true
) use ($front_page_id): bool {
    if (! function_exists('get_field')) {
        return $fallback;
    }

    $value = get_field(
        $field_name,
        $front_page_id
    );

    if ($value === null || $value === '') {
        return $fallback;
    }

    return (bool) $value;
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
/**
 * Inhalte des Orientierungsbereichs laden.
 */
$orientation_eyebrow = $get_home_text(
    'jl_home_orientation_eyebrow',
    'Wie funktioniert Jung Leben'
);

$orientation_title = $get_home_text(
    'jl_home_orientation_title',
    'Ein Ort der Orientierung'
);

$orientation_intro = $get_home_text(
    'jl_home_orientation_intro',
    'Es gibt keinen vorgeschriebenen Einstiegspunkt. Du kannst Jung Leben über persönliche Erfahrungen, konkrete Empfehlungen, einzelne Produkte oder mögliche Routinen entdecken.'
);

$orientation_text = $get_home_text(
    'jl_home_orientation_text',
    'Alle Wege führen zu einem gemeinsamen Ziel: Informationen besser einzuordnen und bewusste Entscheidungen für dein persönliches Wohlbefinden zu treffen.'
);

$orientation_center_title = $get_home_text(
    'jl_home_orientation_center_title',
    'Jung Leben'
);

$orientation_center_subtitle = $get_home_text(
    'jl_home_orientation_center_subtitle',
    'Orientierung'
);

$orientation_node_one = $get_home_text(
    'jl_home_orientation_node_one',
    'Erfahrungen'
);

$orientation_node_two = $get_home_text(
    'jl_home_orientation_node_two',
    'Empfehlungen'
);

$orientation_node_three = $get_home_text(
    'jl_home_orientation_node_three',
    'Produkte'
);

$orientation_node_four = $get_home_text(
    'jl_home_orientation_node_four',
    'Routinen'
);

$orientation_node_five = $get_home_text(
    'jl_home_orientation_node_five',
    'Wissen'
);

$orientation_node_six = $get_home_text(
    'jl_home_orientation_node_six',
    'Austausch'
);
/**
 * Inhalte von «Robertos Reise und Erfahrungen» laden.
 */
$journey_eyebrow = $get_home_text(
    'jl_home_journey_eyebrow',
    'Erfahrungen und Empfehlungen'
);

$journey_title = $get_home_text(
    'jl_home_journey_title',
    'Robertos Reise und Erfahrungen'
);

$journey_intro = $get_home_text(
    'jl_home_journey_intro',
    'Persönliche Beobachtungen, bewusste Routinen und ehrliche Einordnungen bilden die Grundlage von Jung Leben.'
);

$journey_button_text = $get_home_text(
    'jl_home_journey_button_text',
    'Erfahrungen entdecken'
);

$journey_button_url = $get_home_url(
    'jl_home_journey_button_url',
    $ratgeber_url
);

/**
 * Drei feste Slides definieren.
 *
 * Mit ACF Free können die Inhalte bearbeitet und einzelne
 * Slides deaktiviert werden.
 */
$journey_slides = [
    [
        'enabled' => $get_home_bool(
            'jl_home_journey_slide_one_enabled'
        ),
        'label' => $get_home_text(
            'jl_home_journey_slide_one_label',
            'Der Ausgangspunkt'
        ),
        'title' => $get_home_text(
            'jl_home_journey_slide_one_title',
            'Gesundheit bewusster betrachten.'
        ),
        'text' => $get_home_text(
            'jl_home_journey_slide_one_text',
            'Jung Leben entstand aus der persönlichen Auseinandersetzung mit Vitalität, Wohlbefinden und der Frage, welche Entscheidungen langfristig wirklich guttun.'
        ),
        'tag' => $get_home_text(
            'jl_home_journey_slide_one_tag',
            'Bewusstsein'
        ),
    ],
    [
        'enabled' => $get_home_bool(
            'jl_home_journey_slide_two_enabled'
        ),
        'label' => $get_home_text(
            'jl_home_journey_slide_two_label',
            'Beobachten und ausprobieren'
        ),
        'title' => $get_home_text(
            'jl_home_journey_slide_two_title',
            'Erfahrungen entstehen im Alltag.'
        ),
        'text' => $get_home_text(
            'jl_home_journey_slide_two_text',
            'Produkte und Routinen werden nicht isoliert betrachtet. Entscheidend ist, wie verständlich sie sind, wie sie sich in den Alltag integrieren lassen und ob sie zur persönlichen Situation passen.'
        ),
        'tag' => $get_home_text(
            'jl_home_journey_slide_two_tag',
            'Alltagserfahrung'
        ),
    ],
    [
        'enabled' => $get_home_bool(
            'jl_home_journey_slide_three_enabled'
        ),
        'label' => $get_home_text(
            'jl_home_journey_slide_three_label',
            'Einordnen und weitergeben'
        ),
        'title' => $get_home_text(
            'jl_home_journey_slide_three_title',
            'Orientierung statt allgemeiner Versprechen.'
        ),
        'text' => $get_home_text(
            'jl_home_journey_slide_three_text',
            'Erfahrungen werden offen eingeordnet und mit ergänzenden Informationen verbunden. Jung Leben möchte Möglichkeiten aufzeigen, ohne die eine richtige Lösung vorzuschreiben.'
        ),
        'tag' => $get_home_text(
            'jl_home_journey_slide_three_tag',
            'Orientierung'
        ),
    ],
];

$journey_slides = array_values(
    array_filter(
        $journey_slides,
        static function (array $slide): bool {
            return $slide['enabled']
                && trim($slide['title']) !== '';
        }
    )
);

$journey_slide_count = count($journey_slides);
/**
 * Inhalte des Bereichs «Mögliche Tagesroutinen» laden.
 */
$routines_eyebrow = $get_home_text(
    'jl_home_routines_eyebrow',
    'Mögliche Tagesroutinen'
);

$routines_title = $get_home_text(
    'jl_home_routines_title',
    'Eine mögliche Tagesstruktur für Longevity-Produkte.'
);

$routines_text_one = $get_home_text(
    'jl_home_routines_text_one',
    'Roberto ordnet ausgewählte Produkte nach Tageszeit: morgens für Energie und Zellstoffwechsel, mittags für Balance und Pflanzenstoffe, abends für Entspannung und Regeneration.'
);

$routines_text_two = $get_home_text(
    'jl_home_routines_text_two',
    'Die dargestellte Routine ist keine fixe Einnahmeempfehlung, sondern eine persönliche Orientierung für Menschen, die bewusst mit Nahrungsergänzungen und Produktkombinationen umgehen möchten.'
);

$routines_button_text = $get_home_text(
    'jl_home_routines_button_text',
    'Routine ansehen'
);

$routines_button_url = $get_home_url(
    'jl_home_routines_button_url',
    $routinen_url
);

$routines_notice = $get_home_text(
    'jl_home_routines_notice',
    'Produkte, Kombinationen, Dosierungen und Einnahmedauer sind individuell. Einzelne Produkte können nur für eine begrenzte Zeit sinnvoll sein. Passe deine Routine an dein persönliches Empfinden an und hole bei Unsicherheiten fachlichen Rat ein.'
);

/**
 * Drei feste Tageszeiten definieren.
 */
$routine_items = [
    [
        'enabled' => $get_home_bool(
            'jl_home_routines_morning_enabled'
        ),
        'class' => 'morning',
        'icon' => $get_home_text(
            'jl_home_routines_morning_icon',
            '☀'
        ),
        'label' => $get_home_text(
            'jl_home_routines_morning_label',
            'Morgens'
        ),
        'products' => $get_home_text(
            'jl_home_routines_morning_products',
            'NADH, Ashwagandha, Q10, Resveratrol'
        ),
    ],
    [
        'enabled' => $get_home_bool(
            'jl_home_routines_midday_enabled'
        ),
        'class' => 'midday',
        'icon' => $get_home_text(
            'jl_home_routines_midday_icon',
            '🌿'
        ),
        'label' => $get_home_text(
            'jl_home_routines_midday_label',
            'Mittags'
        ),
        'products' => $get_home_text(
            'jl_home_routines_midday_products',
            'Omega 3-6-9, Shilajit, OPC, Quercetin'
        ),
    ],
    [
        'enabled' => $get_home_bool(
            'jl_home_routines_evening_enabled'
        ),
        'class' => 'evening',
        'icon' => $get_home_text(
            'jl_home_routines_evening_icon',
            '☾'
        ),
        'label' => $get_home_text(
            'jl_home_routines_evening_label',
            'Abends'
        ),
        'products' => $get_home_text(
            'jl_home_routines_evening_products',
            'Magnesium, Weihrauch, Oreganoöl'
        ),
    ],
];

$routine_items = array_values(
    array_filter(
        $routine_items,
        static function (array $item): bool {
            return $item['enabled']
                && trim($item['label']) !== '';
        }
    )
);
$routine_item_count = count($routine_items);
/**
 * Inhalte des Community- und Kontaktbereichs laden.
 */
$community_enabled = $get_home_bool(
    'jl_home_community_enabled'
);

$community_eyebrow = $get_home_text(
    'jl_home_community_eyebrow',
    'Du bist dran'
);

$community_title = $get_home_text(
    'jl_home_community_title',
    'Hast du ein Anliegen oder eine Erfahrung, die du teilen möchtest?'
);

$community_lead = $get_home_text(
    'jl_home_community_lead',
    'Jung Leben lebt nicht nur von Empfehlungen und Informationen, sondern auch vom persönlichen Austausch. Deine Erfahrungen, Fragen und Beobachtungen können wertvolle Impulse liefern.'
);

$community_text = $get_home_text(
    'jl_home_community_text',
    'Erzähl uns, was dich beschäftigt, welche Routinen dir helfen oder welche Themen du auf Jung Leben gerne wiederfinden möchtest.'
);

$community_symbol = $get_home_text(
    'jl_home_community_symbol',
    'JL'
);

$community_button_text = $get_home_text(
    'jl_home_community_button_text',
    'Kontaktiere uns'
);

$community_button_url = $get_home_url(
    'jl_home_community_button_url',
    $kontakt_url
);

$community_note = $get_home_text(
    'jl_home_community_note',
    'Wir freuen uns auf deine Nachricht.'
);
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

                <?php if ($orientation_eyebrow !== '') : ?>
                    <p class="eyebrow">
                        <?php echo esc_html(
                            $orientation_eyebrow
                        ); ?>
                    </p>
                <?php endif; ?>

                <h2 id="orientation-title">
                    <?php echo esc_html(
                        $orientation_title
                    ); ?>
                </h2>

                <?php if ($orientation_intro !== '') : ?>
                    <p class="home-orientation-intro">
                        <?php echo esc_html(
                            $orientation_intro
                        ); ?>
                    </p>
                <?php endif; ?>

                <?php if ($orientation_text !== '') : ?>
                    <p>
                        <?php echo esc_html(
                            $orientation_text
                        ); ?>
                    </p>
                <?php endif; ?>

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
                        <?php echo esc_html(
                            $orientation_center_title
                        ); ?>
                    </span>

                    <span class="orientation-map__purpose">
                        <?php echo esc_html(
                            $orientation_center_subtitle
                        ); ?>
                    </span>
                </div>

                <div
                    class="orientation-map__node
                    orientation-map__node--experiences"
                >
                    <span aria-hidden="true">✨</span>

                    <?php echo esc_html(
                        $orientation_node_one
                    ); ?>
                </div>

                <div
                    class="orientation-map__node
                    orientation-map__node--recommendations"
                >
                    <span aria-hidden="true">✓</span>

                    <?php echo esc_html(
                        $orientation_node_two
                    ); ?>
                </div>

                <div
                    class="orientation-map__node
                    orientation-map__node--products"
                >
                    <span aria-hidden="true">🌿</span>

                    <?php echo esc_html(
                        $orientation_node_three
                    ); ?>
                </div>

                <div
                    class="orientation-map__node
                    orientation-map__node--routines"
                >
                    <span aria-hidden="true">☀</span>

                    <?php echo esc_html(
                        $orientation_node_four
                    ); ?>
                </div>

                <div
                    class="orientation-map__node
                    orientation-map__node--knowledge"
                >
                    <span aria-hidden="true">💡</span>

                    <?php echo esc_html(
                        $orientation_node_five
                    ); ?>
                </div>

                <div
                    class="orientation-map__node
                    orientation-map__node--exchange"
                >
                    <span aria-hidden="true">↔</span>

                    <?php echo esc_html(
                        $orientation_node_six
                    ); ?>
                </div>

            </div>
        </div>
    </section>
      <?php if ($journey_slide_count > 0) : ?>

        <!-- Robertos Reise und Erfahrungen -->
        <section
            class="home-journey-section"
            aria-labelledby="journey-title"
        >
            <div class="container home-journey-layout">

                <header class="home-journey-heading">

                    <?php if ($journey_eyebrow !== '') : ?>
                        <p class="eyebrow">
                            <?php echo esc_html(
                                $journey_eyebrow
                            ); ?>
                        </p>
                    <?php endif; ?>

                    <h2 id="journey-title">
                        <?php echo esc_html(
                            $journey_title
                        ); ?>
                    </h2>

                    <?php if ($journey_intro !== '') : ?>
                        <p class="home-journey-heading__text">
                            <?php echo esc_html(
                                $journey_intro
                            ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($journey_button_text !== '') : ?>
                        <a
                            href="<?php echo esc_url(
                                $journey_button_url
                            ); ?>"
                            class="btn btn-primary"
                        >
                            <?php echo esc_html(
                                $journey_button_text
                            ); ?>
                        </a>
                    <?php endif; ?>

                </header>

                <div
                    class="journey-slider"
                    data-journey-slider
                    tabindex="0"
                    aria-roledescription="<?php esc_attr_e(
                        'Karussell',
                        'jung-leben'
                    ); ?>"
                    aria-label="<?php echo esc_attr(
                        $journey_title
                    ); ?>"
                >
                    <div class="journey-slider__viewport">

                        <?php
                        foreach (
                            $journey_slides as $index => $slide
                        ) :
                            $is_active = $index === 0;
                            ?>

                            <article
                                class="journey-slide<?php
                                echo $is_active
                                    ? ' is-active'
                                    : '';
                                ?>"
                                data-journey-slide
                                aria-hidden="<?php
                                echo $is_active
                                    ? 'false'
                                    : 'true';
                                ?>"
                                aria-label="<?php echo esc_attr(
                                    sprintf(
                                        __(
                                            'Erfahrung %1$d von %2$d',
                                            'jung-leben'
                                        ),
                                        $index + 1,
                                        $journey_slide_count
                                    )
                                ); ?>"
                            >
                                <div class="journey-slide__content">

                                    <?php
                                    if ($slide['label'] !== '') :
                                        ?>
                                        <p class="journey-slide__label">
                                            <?php echo esc_html(
                                                $slide['label']
                                            ); ?>
                                        </p>
                                    <?php endif; ?>

                                    <h3>
                                        <?php echo esc_html(
                                            $slide['title']
                                        ); ?>
                                    </h3>

                                    <?php
                                    if ($slide['text'] !== '') :
                                        ?>
                                        <p class="journey-slide__text">
                                            <?php echo esc_html(
                                                $slide['text']
                                            ); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php
                                    if ($slide['tag'] !== '') :
                                        ?>
                                        <span class="journey-slide__tag">
                                            <?php echo esc_html(
                                                $slide['tag']
                                            ); ?>
                                        </span>
                                    <?php endif; ?>

                                </div>
                            </article>

                        <?php endforeach; ?>

                    </div>

                    <?php if ($journey_slide_count > 1) : ?>

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
                                    <?php
                                    foreach (
                                        $journey_slides as $index => $slide
                                    ) :
                                        $is_active = $index === 0;
                                        ?>

                                        <button
                                            class="journey-slider__dot<?php
                                            echo $is_active
                                                ? ' is-active'
                                                : '';
                                            ?>"
                                            type="button"
                                            data-journey-dot
                                            aria-label="<?php echo esc_attr(
                                                sprintf(
                                                    __(
                                                        'Erfahrung %d anzeigen',
                                                        'jung-leben'
                                                    ),
                                                    $index + 1
                                                )
                                            ); ?>"
                                            aria-current="<?php
                                            echo $is_active
                                                ? 'true'
                                                : 'false';
                                            ?>"
                                        ></button>

                                    <?php endforeach; ?>
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
                                <span>
                                    <?php echo esc_html(
                                        (string) $journey_slide_count
                                    ); ?>
                                </span>
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>
        </section>

    <?php endif; ?>

        <?php if ($routine_item_count > 0) : ?>

        <!-- Mögliche Tagesroutinen -->
        <section
            class="home-routines-section"
            aria-labelledby="routines-title"
        >
            <div class="container home-routines-layout">

                <div class="home-routines-copy">

                    <?php if ($routines_eyebrow !== '') : ?>
                        <p class="eyebrow">
                            <?php echo esc_html(
                                $routines_eyebrow
                            ); ?>
                        </p>
                    <?php endif; ?>

                    <h2 id="routines-title">
                        <?php echo esc_html(
                            $routines_title
                        ); ?>
                    </h2>

                    <?php if ($routines_text_one !== '') : ?>
                        <p>
                            <?php echo esc_html(
                                $routines_text_one
                            ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($routines_text_two !== '') : ?>
                        <p>
                            <?php echo esc_html(
                                $routines_text_two
                            ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($routines_button_text !== '') : ?>
                        <a
                            href="<?php echo esc_url(
                                $routines_button_url
                            ); ?>"
                            class="btn btn-primary"
                        >
                            <?php echo esc_html(
                                $routines_button_text
                            ); ?>
                        </a>
                    <?php endif; ?>

                </div>

                <div class="home-routines-card">

                    <?php foreach ($routine_items as $item) : ?>

                        <article
                            class="routine-time routine-time--<?php
                            echo esc_attr($item['class']);
                            ?>"
                        >
                            <?php if ($item['icon'] !== '') : ?>
                                <div
                                    class="routine-time__icon"
                                    aria-hidden="true"
                                >
                                    <?php echo esc_html(
                                        $item['icon']
                                    ); ?>
                                </div>
                            <?php endif; ?>

                            <div class="routine-time__content">

                                <p class="routine-time__label">
                                    <?php echo esc_html(
                                        $item['label']
                                    ); ?>
                                </p>

                                <?php if ($item['products'] !== '') : ?>
                                    <p class="routine-time__products">
                                        <?php echo esc_html(
                                            $item['products']
                                        ); ?>
                                    </p>
                                <?php endif; ?>

                            </div>
                        </article>

                    <?php endforeach; ?>

                    <?php if ($routines_notice !== '') : ?>
                        <aside class="home-routines-notice">

                            <div
                                class="home-routines-notice__icon"
                                aria-hidden="true"
                            >
                                i
                            </div>

                            <p>
                                <?php echo esc_html(
                                    $routines_notice
                                ); ?>
                            </p>

                        </aside>
                    <?php endif; ?>

                </div>

            </div>
        </section>

    <?php endif; ?>
       <?php if ($community_enabled) : ?>

        <!-- Community und Kontakt -->
        <section
            class="home-community-section"
            aria-labelledby="community-title"
        >
            <div class="container">

                <div class="home-community-card">

                    <div class="home-community-content">

                        <?php if ($community_eyebrow !== '') : ?>
                            <p class="eyebrow">
                                <?php echo esc_html(
                                    $community_eyebrow
                                ); ?>
                            </p>
                        <?php endif; ?>

                        <h2 id="community-title">
                            <?php echo esc_html(
                                $community_title
                            ); ?>
                        </h2>

                        <?php if ($community_lead !== '') : ?>
                            <p class="home-community-lead">
                                <?php echo esc_html(
                                    $community_lead
                                ); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ($community_text !== '') : ?>
                            <p>
                                <?php echo esc_html(
                                    $community_text
                                ); ?>
                            </p>
                        <?php endif; ?>

                    </div>

                    <div class="home-community-action">

                        <div
                            class="home-community-symbol"
                            aria-hidden="true"
                        >
                            <span
                                class="home-community-symbol__center"
                            >
                                <?php echo esc_html(
                                    $community_symbol
                                ); ?>
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

                        <?php if ($community_button_text !== '') : ?>
                            <a
                                href="<?php echo esc_url(
                                    $community_button_url
                                ); ?>"
                                class="btn btn-primary
                                home-community-button"
                            >
                                <?php echo esc_html(
                                    $community_button_text
                                ); ?>

                                <span aria-hidden="true">→</span>
                            </a>
                        <?php endif; ?>

                        <?php if ($community_note !== '') : ?>
                            <p class="home-community-note">
                                <?php echo esc_html(
                                    $community_note
                                ); ?>
                            </p>
                        <?php endif; ?>

                    </div>

                </div>

            </div>
        </section>

    <?php endif; ?>

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