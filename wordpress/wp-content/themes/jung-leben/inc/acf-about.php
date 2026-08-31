<?php
/**
 * ACF-Felder für die Seite «Über mich».
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Felder für die Seite «Über mich» registrieren.
 */
function jung_leben_register_about_fields(): void
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }


    $about_page =
        get_page_by_path(
            'ueber-mich',
            OBJECT,
            'page'
        );


    if (! $about_page instanceof WP_Post) {
        return;
    }


    acf_add_local_field_group([
        'key' =>
            'group_jung_leben_about',

        'title' =>
            __(
                'Über mich – Inhalte',
                'jung-leben'
            ),

        'fields' => [

            /* =================================================
               SEITENKOPF
               ================================================= */

            [
                'key' =>
                    'field_jl_about_header_tab',

                'label' =>
                    __(
                        'Seitenkopf',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'tab',

                'placement' =>
                    'top',
            ],

            [
                'key' =>
                    'field_jl_about_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Über Jung Leben',

                'maxlength' =>
                    100,
            ],

            [
                'key' =>
                    'field_jl_about_title',

                'label' =>
                    __(
                        'Hauptüberschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Gesundheit bewusster betrachten.',

                'rows' =>
                    2,

                'new_lines' =>
                    '',

                'required' =>
                    1,
            ],

            [
                'key' =>
                    'field_jl_about_lead',

                'label' =>
                    __(
                        'Einleitungstext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_lead',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Jung Leben ist aus der persönlichen Auseinandersetzung mit Gesundheit, Vitalität und der Frage entstanden, welche Entscheidungen langfristig wirklich guttun.',

                'rows' =>
                    5,

                'new_lines' =>
                    '',

                'required' =>
                    1,
            ],

            [
                'key' =>
                    'field_jl_about_portrait',

                'label' =>
                    __(
                        'Bild',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_portrait',

                'type' =>
                    'image',

                'instructions' =>
                    __(
                        'Optionales Bild für den oberen Bereich der Seite.',
                        'jung-leben'
                    ),

                'return_format' =>
                    'array',

                'preview_size' =>
                    'medium',

                'library' =>
                    'all',

                'mime_types' =>
                    'jpg,jpeg,png,webp',
            ],


            /* =================================================
               GESCHICHTE
               ================================================= */

            [
                'key' =>
                    'field_jl_about_story_tab',

                'label' =>
                    __(
                        'Meine Geschichte',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'tab',

                'placement' =>
                    'top',
            ],

            [
                'key' =>
                    'field_jl_about_story_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Der persönliche Ausgangspunkt',

                'maxlength' =>
                    100,
            ],

            [
                'key' =>
                    'field_jl_about_story_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Aus persönlicher Neugier wurde Jung Leben.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_story_text_one',

                'label' =>
                    __(
                        'Erster Textabschnitt',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_text_one',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Im Mittelpunkt stand von Anfang an die Frage, wie sich Gesundheit und Wohlbefinden mit bewussten Entscheidungen im Alltag unterstützen lassen. Dabei entstanden persönliche Erfahrungen mit Produkten, Routinen und unterschiedlichen Ansätzen.',

                'rows' =>
                    6,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_story_text_two',

                'label' =>
                    __(
                        'Zweiter Textabschnitt',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_text_two',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Jung Leben soll diese Erfahrungen nicht als allgemeingültige Wahrheit präsentieren. Die Plattform möchte vielmehr zeigen, was ausprobiert wurde, welche Beobachtungen daraus entstanden sind und wie Produkte und Informationen sinnvoll eingeordnet werden können.',

                'rows' =>
                    6,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_story_text_three',

                'label' =>
                    __(
                        'Dritter Textabschnitt',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_text_three',

                'type' =>
                    'textarea',

                'default_value' =>
                    'So entsteht ein Ort für Menschen, die sich selbst informieren, Zusammenhänge verstehen und bewusste Entscheidungen treffen möchten.',

                'rows' =>
                    5,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_story_statement_label',

                'label' =>
                    __(
                        'Bezeichnung der hervorgehobenen Aussage',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_statement_label',

                'type' =>
                    'text',

                'default_value' =>
                    'Jung Leben',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_about_story_statement_text',

                'label' =>
                    __(
                        'Hervorgehobene Aussage',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_story_statement_text',

                'type' =>
                    'textarea',

                'instructions' =>
                    __(
                        'Text in der hervorgehobenen Box unterhalb der Geschichte.',
                        'jung-leben'
                    ),

                'default_value' =>
                    'Nicht möglichst viel verändern – sondern bewusster entscheiden, was langfristig zum eigenen Leben passt.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               HALTUNG
               ================================================= */

            [
                'key' =>
                    'field_jl_about_values_tab',

                'label' =>
                    __(
                        'Haltung',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'tab',

                'placement' =>
                    'top',
            ],

            [
                'key' =>
                    'field_jl_about_values_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_values_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Wofür Jung Leben steht',

                'maxlength' =>
                    100,
            ],

            [
                'key' =>
                    'field_jl_about_values_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_values_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Orientierung statt Versprechen.',

                'rows' =>
                    2,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_values_intro',

                'label' =>
                    __(
                        'Einleitungstext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_values_intro',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Drei Grundsätze bestimmen, wie Inhalte und Empfehlungen auf Jung Leben entstehen.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],


            /* Prinzip 1 */

            [
                'key' =>
                    'field_jl_about_value_one_heading',

                'label' =>
                    __(
                        'Prinzip 1',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'message',

                'message' =>
                    __(
                        'Erstes Grundprinzip von Jung Leben.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_about_value_one_title',

                'label' =>
                    __(
                        'Titel',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_value_one_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Persönlich',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_about_value_one_text',

                'label' =>
                    __(
                        'Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_value_one_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Erfahrungen werden als persönliche Beobachtungen beschrieben und nicht als allgemeingültige Versprechen dargestellt.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* Prinzip 2 */

            [
                'key' =>
                    'field_jl_about_value_two_heading',

                'label' =>
                    __(
                        'Prinzip 2',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'message',

                'message' =>
                    __(
                        'Zweites Grundprinzip von Jung Leben.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_about_value_two_title',

                'label' =>
                    __(
                        'Titel',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_value_two_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Nachvollziehbar',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_about_value_two_text',

                'label' =>
                    __(
                        'Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_value_two_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Produkte und Routinen werden in einen verständlichen Kontext eingeordnet, statt isoliert präsentiert zu werden.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* Prinzip 3 */

            [
                'key' =>
                    'field_jl_about_value_three_heading',

                'label' =>
                    __(
                        'Prinzip 3',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'message',

                'message' =>
                    __(
                        'Drittes Grundprinzip von Jung Leben.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_about_value_three_title',

                'label' =>
                    __(
                        'Titel',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_value_three_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Transparent',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_about_value_three_text',

                'label' =>
                    __(
                        'Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_value_three_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Partnerschaften und Affiliate-Links werden transparent gekennzeichnet. Sie bestimmen nicht, welche Inhalte oder Erfahrungen auf Jung Leben erscheinen.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               WARUM JUNG LEBEN
               ================================================= */

            [
                'key' =>
                    'field_jl_about_why_tab',

                'label' =>
                    __(
                        'Warum Jung Leben',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'tab',

                'placement' =>
                    'top',
            ],

            [
                'key' =>
                    'field_jl_about_why_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_why_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Warum Jung Leben',

                'maxlength' =>
                    100,
            ],

            [
                'key' =>
                    'field_jl_about_why_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_why_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Nicht das eine perfekte Produkt. Sondern bessere Orientierung.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_why_text_one',

                'label' =>
                    __(
                        'Erster Textabschnitt',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_why_text_one',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Die Welt rund um Longevity, Nahrungsergänzungen und Wohlbefinden ist gross. Produkte versprechen viel, Informationen widersprechen sich und persönliche Bedürfnisse unterscheiden sich.',

                'rows' =>
                    5,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_why_text_two',

                'label' =>
                    __(
                        'Zweiter Textabschnitt',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_why_text_two',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Jung Leben möchte dabei helfen, Produkte, Erfahrungen und Routinen besser einzuordnen – ohne vorzugeben, dass es für alle dieselbe richtige Lösung gibt.',

                'rows' =>
                    5,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               ABSCHLUSS / CTA
               ================================================= */

            [
                'key' =>
                    'field_jl_about_cta_tab',

                'label' =>
                    __(
                        'Abschluss',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'tab',

                'placement' =>
                    'top',
            ],

            [
                'key' =>
                    'field_jl_about_cta_enabled',

                'label' =>
                    __(
                        'Abschlussbereich anzeigen',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_cta_enabled',

                'type' =>
                    'true_false',

                'default_value' =>
                    1,

                'ui' =>
                    1,

                'ui_on_text' =>
                    __(
                        'Ja',
                        'jung-leben'
                    ),

                'ui_off_text' =>
                    __(
                        'Nein',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_about_cta_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_cta_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Entdecke Jung Leben',

                'maxlength' =>
                    100,
            ],

            [
                'key' =>
                    'field_jl_about_cta_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_cta_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Starte dort, wo es für dich interessant wird.',

                'rows' =>
                    2,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_cta_text',

                'label' =>
                    __(
                        'Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_cta_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Entdecke persönliche Erfahrungen, ausgewählte Empfehlungen und mögliche Routinen.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_about_cta_button_text',

                'label' =>
                    __(
                        'Button-Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_cta_button_text',

                'type' =>
                    'text',

                'default_value' =>
                    'Erfahrungen entdecken',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_about_cta_button_url',

                'label' =>
                    __(
                        'Button-Ziel',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_about_cta_button_url',

                'type' =>
                    'url',

                'instructions' =>
                    __(
                        'Leer lassen, um automatisch auf die Erfahrungsseite zu verlinken.',
                        'jung-leben'
                    ),
            ],
        ],


        'location' => [
            [
                [
                    'param' =>
                        'page',

                    'operator' =>
                        '==',

                    'value' =>
                        (string)
                        $about_page->ID,
                ],
            ],
        ],


        'menu_order' =>
            0,

        'position' =>
            'normal',

        'style' =>
            'default',

        'label_placement' =>
            'top',

        'instruction_placement' =>
            'label',

        'active' =>
            true,

        'show_in_rest' =>
            0,
    ]);
}


add_action(
    'acf/init',
    'jung_leben_register_about_fields'
);