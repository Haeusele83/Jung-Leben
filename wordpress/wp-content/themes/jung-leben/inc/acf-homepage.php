<?php
/**
 * ACF-Felder für die Startseite.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Felder für die Startseite registrieren.
 */
function jung_leben_register_homepage_fields(): void
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'   => 'group_jung_leben_homepage',
        'title' => __('Startseite – Inhalte', 'jung-leben'),

        'fields' => [
            [
                'key'       => 'field_jl_home_hero_tab',
                'label'     => __('Hero-Bereich', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_hero_eyebrow',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_hero_eyebrow',
                'type'          => 'text',
                'instructions'  => __(
                    'Kleine Zeile oberhalb der Hauptüberschrift.',
                    'jung-leben'
                ),
                'default_value' => 'Longevity ist kein Sprint und kein Zufall',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_hero_title',
                'label'         => __('Hauptüberschrift', 'jung-leben'),
                'name'          => 'jl_home_hero_title',
                'type'          => 'textarea',
                'instructions'  => __(
                    'Zentrale Überschrift im Hero-Bereich.',
                    'jung-leben'
                ),
                'default_value' =>
                    'The goal is to die young – as late as possible',
                'rows'          => 3,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_hero_text',
                'label'         => __('Einleitungstext', 'jung-leben'),
                'name'          => 'jl_home_hero_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Es sind die täglichen Entscheidungen, die darüber bestimmen, wie wir altern. Jung Leben hilft dir dabei, evidenzbasierte Produkte, Routinen und Erfahrungen zu entdecken, die Vitalität, Wohlbefinden und Langlebigkeit unterstützen.',
                'rows'          => 5,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_hero_background',
                'label'         => __('Hintergrundbild', 'jung-leben'),
                'name'          => 'jl_home_hero_background',
                'type'          => 'image',
                'instructions'  => __(
                    'Optional. Ohne Auswahl wird das bisherige Naturbild verwendet.',
                    'jung-leben'
                ),
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'mime_types'    => 'jpg,jpeg,png,webp',
            ],

            [
                'key'       => 'field_jl_home_hero_button_one_tab',
                'label'     => __('Erster Button', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_hero_button_one_text',
                'label'         => __('Button-Text', 'jung-leben'),
                'name'          => 'jl_home_hero_button_one_text',
                'type'          => 'text',
                'default_value' => 'Empfehlungen entdecken',
                'maxlength'     => 60,
            ],
            [
                'key'          => 'field_jl_home_hero_button_one_url',
                'label'        => __('Button-Ziel', 'jung-leben'),
                'name'         => 'jl_home_hero_button_one_url',
                'type'         => 'url',
                'instructions' => __(
                    'Leer lassen, um automatisch auf die Empfehlungsseite zu verlinken.',
                    'jung-leben'
                ),
            ],

            [
                'key'       => 'field_jl_home_hero_button_two_tab',
                'label'     => __('Zweiter Button', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_hero_button_two_text',
                'label'         => __('Button-Text', 'jung-leben'),
                'name'          => 'jl_home_hero_button_two_text',
                'type'          => 'text',
                'default_value' => 'Erfahrungen lesen',
                'maxlength'     => 60,
            ],
            [
                'key'          => 'field_jl_home_hero_button_two_url',
                'label'        => __('Button-Ziel', 'jung-leben'),
                'name'         => 'jl_home_hero_button_two_url',
                'type'         => 'url',
                'instructions' => __(
                    'Leer lassen, um automatisch auf die Ratgeberseite zu verlinken.',
                    'jung-leben'
                ),
            ],

            [
                'key'       => 'field_jl_home_hero_benefits_tab',
                'label'     => __('Hinweise im Hero', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_hero_benefit_one',
                'label'         => __('Erster Hinweis', 'jung-leben'),
                'name'          => 'jl_home_hero_benefit_one',
                'type'          => 'text',
                'default_value' => '🌿 Longevity',
                'maxlength'     => 60,
            ],
            [
                'key'           => 'field_jl_home_hero_benefit_two',
                'label'         => __('Zweiter Hinweis', 'jung-leben'),
                'name'          => 'jl_home_hero_benefit_two',
                'type'          => 'text',
                'default_value' => '✨ Persönliche Erfahrungen',
                'maxlength'     => 60,
            ],
            [
                'key'           => 'field_jl_home_hero_benefit_three',
                'label'         => __('Dritter Hinweis', 'jung-leben'),
                'name'          => 'jl_home_hero_benefit_three',
                'type'          => 'text',
                'default_value' => '💡 Bewusste Routinen',
                'maxlength'     => 60,
            ],
                        [
                'key'       => 'field_jl_home_about_tab',
                'label'     => __('Über Jung Leben', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_about_eyebrow',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_about_eyebrow',
                'type'          => 'text',
                'instructions'  => __(
                    'Kleine Zeile oberhalb der Überschrift.',
                    'jung-leben'
                ),
                'default_value' => 'Über Jung Leben',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_about_title',
                'label'         => __('Überschrift', 'jung-leben'),
                'name'          => 'jl_home_about_title',
                'type'          => 'textarea',
                'default_value' =>
                    'Praktische Erfahrung statt Produkt-Blabla.',
                'rows'          => 2,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_about_text_one',
                'label'         => __('Erster Textabschnitt', 'jung-leben'),
                'name'          => 'jl_home_about_text_one',
                'type'          => 'textarea',
                'default_value' =>
                    'Jung Leben ist aus Robertos persönlicher Auseinandersetzung mit Gesundheit, Vitalität und Langlebigkeit entstanden. Im Mittelpunkt stehen Erfahrungen aus dem Alltag – ehrlich, nachvollziehbar und ohne leere Versprechen.',
                'rows'          => 5,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_about_text_two',
                'label'         => __('Zweiter Textabschnitt', 'jung-leben'),
                'name'          => 'jl_home_about_text_two',
                'type'          => 'textarea',
                'default_value' =>
                    'Die Plattform verbindet persönliche Beobachtungen mit sorgfältig ausgewählten Produkten, Routinen und fundierten Informationen. Ziel ist nicht, die eine perfekte Lösung zu präsentieren, sondern Orientierung für bewusste Entscheidungen zu geben.',
                'rows'          => 5,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_about_image',
                'label'         => __('Bild', 'jung-leben'),
                'name'          => 'jl_home_about_image',
                'type'          => 'image',
                'instructions'  => __(
                    'Optional. Ohne Auswahl bleibt das bisherige Bild bestehen.',
                    'jung-leben'
                ),
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'mime_types'    => 'jpg,jpeg,png,webp',
            ],
            [
                'key'           => 'field_jl_home_about_button_text',
                'label'         => __('Button-Text', 'jung-leben'),
                'name'          => 'jl_home_about_button_text',
                'type'          => 'text',
                'default_value' => 'Mehr über Roberto',
                'maxlength'     => 60,
            ],
            [
                'key'          => 'field_jl_home_about_button_url',
                'label'        => __('Button-Ziel', 'jung-leben'),
                'name'         => 'jl_home_about_button_url',
                'type'         => 'url',
                'instructions' => __(
                    'Leer lassen, um automatisch auf die Seite «Über mich» zu verlinken.',
                    'jung-leben'
                ),
            ],
                        [
                'key'       => 'field_jl_home_orientation_tab',
                'label'     => __('Orientierung', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_orientation_eyebrow',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_orientation_eyebrow',
                'type'          => 'text',
                'default_value' => 'Wie funktioniert Jung Leben',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_orientation_title',
                'label'         => __('Überschrift', 'jung-leben'),
                'name'          => 'jl_home_orientation_title',
                'type'          => 'text',
                'default_value' => 'Ein Ort der Orientierung',
                'maxlength'     => 120,
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_orientation_intro',
                'label'         => __('Einleitungstext', 'jung-leben'),
                'name'          => 'jl_home_orientation_intro',
                'type'          => 'textarea',
                'default_value' =>
                    'Es gibt keinen vorgeschriebenen Einstiegspunkt. Du kannst Jung Leben über persönliche Erfahrungen, konkrete Empfehlungen, einzelne Produkte oder mögliche Routinen entdecken.',
                'rows'          => 4,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_orientation_text',
                'label'         => __('Zweiter Textabschnitt', 'jung-leben'),
                'name'          => 'jl_home_orientation_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Alle Wege führen zu einem gemeinsamen Ziel: Informationen besser einzuordnen und bewusste Entscheidungen für dein persönliches Wohlbefinden zu treffen.',
                'rows'          => 4,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_orientation_center_title',
                'label'         => __('Text im Zentrum', 'jung-leben'),
                'name'          => 'jl_home_orientation_center_title',
                'type'          => 'text',
                'default_value' => 'Jung Leben',
                'maxlength'     => 60,
            ],
            [
                'key'           => 'field_jl_home_orientation_center_subtitle',
                'label'         => __('Untertitel im Zentrum', 'jung-leben'),
                'name'          => 'jl_home_orientation_center_subtitle',
                'type'          => 'text',
                'default_value' => 'Orientierung',
                'maxlength'     => 60,
            ],
            [
                'key'           => 'field_jl_home_orientation_node_one',
                'label'         => __('Einstiegspunkt 1', 'jung-leben'),
                'name'          => 'jl_home_orientation_node_one',
                'type'          => 'text',
                'default_value' => 'Erfahrungen',
                'maxlength'     => 50,
            ],
            [
                'key'           => 'field_jl_home_orientation_node_two',
                'label'         => __('Einstiegspunkt 2', 'jung-leben'),
                'name'          => 'jl_home_orientation_node_two',
                'type'          => 'text',
                'default_value' => 'Empfehlungen',
                'maxlength'     => 50,
            ],
            [
                'key'           => 'field_jl_home_orientation_node_three',
                'label'         => __('Einstiegspunkt 3', 'jung-leben'),
                'name'          => 'jl_home_orientation_node_three',
                'type'          => 'text',
                'default_value' => 'Produkte',
                'maxlength'     => 50,
            ],
            [
                'key'           => 'field_jl_home_orientation_node_four',
                'label'         => __('Einstiegspunkt 4', 'jung-leben'),
                'name'          => 'jl_home_orientation_node_four',
                'type'          => 'text',
                'default_value' => 'Routinen',
                'maxlength'     => 50,
            ],
            [
                'key'           => 'field_jl_home_orientation_node_five',
                'label'         => __('Einstiegspunkt 5', 'jung-leben'),
                'name'          => 'jl_home_orientation_node_five',
                'type'          => 'text',
                'default_value' => 'Wissen',
                'maxlength'     => 50,
            ],
            [
                'key'           => 'field_jl_home_orientation_node_six',
                'label'         => __('Einstiegspunkt 6', 'jung-leben'),
                'name'          => 'jl_home_orientation_node_six',
                'type'          => 'text',
                'default_value' => 'Austausch',
                'maxlength'     => 50,
            ],
                        [
                'key'       => 'field_jl_home_journey_tab',
                'label'     => __('Erfahrungen', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],
            [
                'key'           => 'field_jl_home_journey_eyebrow',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_journey_eyebrow',
                'type'          => 'text',
                'default_value' => 'Erfahrungen und Empfehlungen',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_journey_title',
                'label'         => __('Überschrift', 'jung-leben'),
                'name'          => 'jl_home_journey_title',
                'type'          => 'textarea',
                'default_value' => 'Robertos Reise und Erfahrungen',
                'rows'          => 2,
                'new_lines'     => '',
                'required'      => 1,
            ],
            [
                'key'           => 'field_jl_home_journey_intro',
                'label'         => __('Einleitungstext', 'jung-leben'),
                'name'          => 'jl_home_journey_intro',
                'type'          => 'textarea',
                'default_value' =>
                    'Persönliche Beobachtungen, bewusste Routinen und ehrliche Einordnungen bilden die Grundlage von Jung Leben.',
                'rows'          => 4,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_button_text',
                'label'         => __('Button-Text', 'jung-leben'),
                'name'          => 'jl_home_journey_button_text',
                'type'          => 'text',
                'default_value' => 'Erfahrungen entdecken',
                'maxlength'     => 60,
            ],
            [
                'key'          => 'field_jl_home_journey_button_url',
                'label'        => __('Button-Ziel', 'jung-leben'),
                'name'         => 'jl_home_journey_button_url',
                'type'         => 'url',
                'instructions' => __(
                    'Leer lassen, um automatisch auf die Ratgeberseite zu verlinken.',
                    'jung-leben'
                ),
            ],

            /*
             * Erfahrung 1
             */
            [
                'key'     => 'field_jl_home_journey_slide_one_heading',
                'label'   => __('Erfahrung 1', 'jung-leben'),
                'name'    => '',
                'type'    => 'message',
                'message' => __(
                    'Inhalte des ersten Eintrags im Erfahrungs-Slider.',
                    'jung-leben'
                ),
            ],
            [
                'key'           => 'field_jl_home_journey_slide_one_enabled',
                'label'         => __('Erfahrung anzeigen', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_one_enabled',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
                'ui_on_text'    => __('Ja', 'jung-leben'),
                'ui_off_text'   => __('Nein', 'jung-leben'),
            ],
            [
                'key'           => 'field_jl_home_journey_slide_one_label',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_one_label',
                'type'          => 'text',
                'default_value' => 'Der Ausgangspunkt',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_journey_slide_one_title',
                'label'         => __('Titel', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_one_title',
                'type'          => 'textarea',
                'default_value' =>
                    'Gesundheit bewusster betrachten.',
                'rows'          => 2,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_slide_one_text',
                'label'         => __('Text', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_one_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Jung Leben entstand aus der persönlichen Auseinandersetzung mit Vitalität, Wohlbefinden und der Frage, welche Entscheidungen langfristig wirklich guttun.',
                'rows'          => 5,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_slide_one_tag',
                'label'         => __('Schlagwort', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_one_tag',
                'type'          => 'text',
                'default_value' => 'Bewusstsein',
                'maxlength'     => 60,
            ],

            /*
             * Erfahrung 2
             */
            [
                'key'     => 'field_jl_home_journey_slide_two_heading',
                'label'   => __('Erfahrung 2', 'jung-leben'),
                'name'    => '',
                'type'    => 'message',
                'message' => __(
                    'Inhalte des zweiten Eintrags im Erfahrungs-Slider.',
                    'jung-leben'
                ),
            ],
            [
                'key'           => 'field_jl_home_journey_slide_two_enabled',
                'label'         => __('Erfahrung anzeigen', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_two_enabled',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
                'ui_on_text'    => __('Ja', 'jung-leben'),
                'ui_off_text'   => __('Nein', 'jung-leben'),
            ],
            [
                'key'           => 'field_jl_home_journey_slide_two_label',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_two_label',
                'type'          => 'text',
                'default_value' => 'Beobachten und ausprobieren',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_journey_slide_two_title',
                'label'         => __('Titel', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_two_title',
                'type'          => 'textarea',
                'default_value' =>
                    'Erfahrungen entstehen im Alltag.',
                'rows'          => 2,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_slide_two_text',
                'label'         => __('Text', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_two_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Produkte und Routinen werden nicht isoliert betrachtet. Entscheidend ist, wie verständlich sie sind, wie sie sich in den Alltag integrieren lassen und ob sie zur persönlichen Situation passen.',
                'rows'          => 5,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_slide_two_tag',
                'label'         => __('Schlagwort', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_two_tag',
                'type'          => 'text',
                'default_value' => 'Alltagserfahrung',
                'maxlength'     => 60,
            ],

            /*
             * Erfahrung 3
             */
            [
                'key'     => 'field_jl_home_journey_slide_three_heading',
                'label'   => __('Erfahrung 3', 'jung-leben'),
                'name'    => '',
                'type'    => 'message',
                'message' => __(
                    'Inhalte des dritten Eintrags im Erfahrungs-Slider.',
                    'jung-leben'
                ),
            ],
            [
                'key'           => 'field_jl_home_journey_slide_three_enabled',
                'label'         => __('Erfahrung anzeigen', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_three_enabled',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
                'ui_on_text'    => __('Ja', 'jung-leben'),
                'ui_off_text'   => __('Nein', 'jung-leben'),
            ],
            [
                'key'           => 'field_jl_home_journey_slide_three_label',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_three_label',
                'type'          => 'text',
                'default_value' => 'Einordnen und weitergeben',
                'maxlength'     => 100,
            ],
            [
                'key'           => 'field_jl_home_journey_slide_three_title',
                'label'         => __('Titel', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_three_title',
                'type'          => 'textarea',
                'default_value' =>
                    'Orientierung statt allgemeiner Versprechen.',
                'rows'          => 2,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_slide_three_text',
                'label'         => __('Text', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_three_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Erfahrungen werden offen eingeordnet und mit ergänzenden Informationen verbunden. Jung Leben möchte Möglichkeiten aufzeigen, ohne die eine richtige Lösung vorzuschreiben.',
                'rows'          => 5,
                'new_lines'     => '',
            ],
            [
                'key'           => 'field_jl_home_journey_slide_three_tag',
                'label'         => __('Schlagwort', 'jung-leben'),
                'name'          => 'jl_home_journey_slide_three_tag',
                'type'          => 'text',
                'default_value' => 'Orientierung',
                'maxlength'     => 60,
            ],
        ],

        'location' => [
            [
                [
                    'param'    => 'page_type',
                    'operator' => '==',
                    'value'    => 'front_page',
                ],
            ],
        ],

        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'show_in_rest'          => 0,
    ]);
}
add_action(
    'acf/init',
    'jung_leben_register_homepage_fields'
);