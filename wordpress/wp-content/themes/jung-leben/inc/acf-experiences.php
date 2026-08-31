<?php
/**
 * ACF-Felder für die Seite «Erfahrungen».
 *
 * Die einzelnen Erfahrungen selbst werden weiterhin
 * als WordPress-Beiträge bearbeitet.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Felder für die Erfahrungsseite registrieren.
 */
function jung_leben_register_experiences_fields(): void
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }


    /**
     * Die WordPress-Beitragsseite ist unsere
     * Erfahrungsübersicht.
     */
    $experiences_page_id =
        (int)
        get_option(
            'page_for_posts'
        );


    if ($experiences_page_id <= 0) {
        return;
    }


    acf_add_local_field_group([
        'key' =>
            'group_jung_leben_experiences',

        'title' =>
            __(
                'Erfahrungen – Inhalte',
                'jung-leben'
            ),

        'fields' => [

            /* =================================================
               SEITENKOPF
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_header_tab',

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
                    'field_jl_experiences_title',

                'label' =>
                    __(
                        'Hauptüberschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_title',

                'type' =>
                    'text',

                'instructions' =>
                    __(
                        'Haupttitel der Erfahrungsübersicht.',
                        'jung-leben'
                    ),

                'default_value' =>
                    'Erfahrungen',

                'maxlength' =>
                    100,

                'required' =>
                    1,
            ],

            [
                'key' =>
                    'field_jl_experiences_intro',

                'label' =>
                    __(
                        'Einleitungstext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_intro',

                'type' =>
                    'textarea',

                'instructions' =>
                    __(
                        'Kurze Einführung oberhalb der Erfahrungen.',
                        'jung-leben'
                    ),

                'default_value' =>
                    'Persönliche Erfahrungen mit Produkten, Routinen und Themen rund um Longevity und Wohlbefinden – ehrlich und nachvollziehbar eingeordnet.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',

                'required' =>
                    1,
            ],


            /* =================================================
               ERFAHRUNGSKARTEN
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_cards_tab',

                'label' =>
                    __(
                        'Erfahrungskarten',
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
                    'field_jl_experiences_cards_info',

                'label' =>
                    __(
                        'Hinweis',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'message',

                'message' =>
                    __(
                        'Die Erfahrungen selbst werden unter «Beiträge» bearbeitet. Titel, Beschreibung und Beitragsbild werden automatisch aus dem jeweiligen Beitrag übernommen.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_experiences_card_label',

                'label' =>
                    __(
                        'Bezeichnung auf den Karten',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_card_label',

                'type' =>
                    'text',

                'default_value' =>
                    'Erfahrung',

                'maxlength' =>
                    50,
            ],

            [
                'key' =>
                    'field_jl_experiences_reading_time_label',

                'label' =>
                    __(
                        'Bezeichnung der Lesezeit',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_reading_time_label',

                'type' =>
                    'text',

                'instructions' =>
                    __(
                        'Die Minutenanzahl wird automatisch berechnet.',
                        'jung-leben'
                    ),

                'default_value' =>
                    'Min. Lesezeit',

                'maxlength' =>
                    50,
            ],

            [
                'key' =>
                    'field_jl_experiences_card_button',

                'label' =>
                    __(
                        'Link-Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_card_button',

                'type' =>
                    'text',

                'default_value' =>
                    'Erfahrung ansehen',

                'maxlength' =>
                    60,
            ],


            /* =================================================
               KEINE ERFAHRUNGEN
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_empty_tab',

                'label' =>
                    __(
                        'Keine Erfahrungen',
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
                    'field_jl_experiences_empty_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_empty_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Noch keine Erfahrungen veröffentlicht.',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_experiences_empty_text',

                'label' =>
                    __(
                        'Hinweistext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_empty_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Sobald neue Erfahrungen veröffentlicht werden, erscheinen sie hier.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               EINZELNE ERFAHRUNG
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_single_tab',

                'label' =>
                    __(
                        'Einzelne Erfahrung',
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
                    'field_jl_experiences_single_info',

                'label' =>
                    __(
                        'Hinweis',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'message',

                'message' =>
                    __(
                        'Hier können allgemeine Bezeichnungen geändert werden, die auf allen einzelnen Erfahrungsseiten verwendet werden. Der eigentliche Artikelinhalt wird weiterhin direkt beim jeweiligen Beitrag bearbeitet.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_experiences_single_back',

                'label' =>
                    __(
                        'Zurück-Link',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_single_back',

                'type' =>
                    'text',

                'default_value' =>
                    'Alle Erfahrungen',

                'maxlength' =>
                    60,
            ],

            [
                'key' =>
                    'field_jl_experiences_single_label',

                'label' =>
                    __(
                        'Format-Bezeichnung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_single_label',

                'type' =>
                    'text',

                'default_value' =>
                    'Erfahrung',

                'maxlength' =>
                    50,
            ],


            /* =================================================
               PRODUKTVERKNÜPFUNG
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_product_tab',

                'label' =>
                    __(
                        'Passendes Produkt',
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
                    'field_jl_experiences_product_info',

                'label' =>
                    __(
                        'Hinweis',
                        'jung-leben'
                    ),

                'name' =>
                    '',

                'type' =>
                    'message',

                'message' =>
                    __(
                        'Welches Produkt zu einer Erfahrung gehört, wird direkt beim jeweiligen Beitrag festgelegt. Hier werden nur die allgemeinen Texte des Produktbereichs gepflegt.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_experiences_product_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_product_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Passend zur Erfahrung',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_experiences_product_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_product_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Das Produkt im Überblick.',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_experiences_product_intro',

                'label' =>
                    __(
                        'Einleitungstext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_product_intro',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Die Erfahrung steht im Vordergrund. Wenn ein Produkt thematisch dazugehört, findest du hier die passende Einordnung.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_experiences_product_button',

                'label' =>
                    __(
                        'Button-Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_product_button',

                'type' =>
                    'text',

                'default_value' =>
                    'Produkt ansehen',

                'maxlength' =>
                    60,
            ],


            /* =================================================
               GESUNDHEITSHINWEIS
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_health_tab',

                'label' =>
                    __(
                        'Gesundheitshinweis',
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
                    'field_jl_experiences_health_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_health_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Hinweis zu gesundheitlichen Inhalten',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_experiences_health_text',

                'label' =>
                    __(
                        'Hinweistext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_health_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Die Inhalte auf Jung Leben dienen der persönlichen Einordnung und allgemeinen Information. Sie ersetzen keine medizinische Beratung, Diagnose oder Behandlung.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               WEITER ENTDECKEN
               ================================================= */

            [
                'key' =>
                    'field_jl_experiences_next_tab',

                'label' =>
                    __(
                        'Weiter entdecken',
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
                    'field_jl_experiences_next_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_next_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Weiter entdecken',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_experiences_next_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_next_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Noch mehr Erfahrungen und bewusste Empfehlungen.',

                'rows' =>
                    2,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_experiences_next_experiences_button',

                'label' =>
                    __(
                        'Button «Erfahrungen»',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_next_experiences_button',

                'type' =>
                    'text',

                'default_value' =>
                    'Alle Erfahrungen',

                'maxlength' =>
                    60,
            ],

            [
                'key' =>
                    'field_jl_experiences_next_recommendations_button',

                'label' =>
                    __(
                        'Button «Empfehlungen»',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_experiences_next_recommendations_button',

                'type' =>
                    'text',

                'default_value' =>
                    'Empfehlungen entdecken',

                'maxlength' =>
                    80,
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
                        $experiences_page_id,
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
    'jung_leben_register_experiences_fields'
);