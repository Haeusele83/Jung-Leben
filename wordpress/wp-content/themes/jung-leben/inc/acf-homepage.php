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