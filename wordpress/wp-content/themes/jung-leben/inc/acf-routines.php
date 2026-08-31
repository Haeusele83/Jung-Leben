<?php
/**
 * ACF-Felder für die Seite «Routinen».
 *
 * Die Produktzuordnung zu den Tageszeiten wird weiterhin
 * direkt bei den Produkten gepflegt.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Felder für die Routinen-Seite registrieren.
 */
function jung_leben_register_routines_fields(): void
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }


    $routines_page =
        get_page_by_path(
            'routinen',
            OBJECT,
            'page'
        );


    if (! $routines_page instanceof WP_Post) {
        return;
    }


    acf_add_local_field_group([
        'key' =>
            'group_jung_leben_routines',

        'title' =>
            __(
                'Routinen – Inhalte',
                'jung-leben'
            ),

        'fields' => [

            /* =================================================
               SEITENKOPF
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_header_tab',

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
                    'field_jl_routines_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Bewusst durch den Tag',

                'maxlength' =>
                    100,
            ],

            [
                'key' =>
                    'field_jl_routines_title',

                'label' =>
                    __(
                        'Hauptüberschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Routinen',

                'rows' =>
                    2,

                'new_lines' =>
                    '',

                'required' =>
                    1,
            ],

            [
                'key' =>
                    'field_jl_routines_intro',

                'label' =>
                    __(
                        'Einleitungstext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_intro',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Produkte entfalten ihren Sinn nicht isoliert. Hier findest du mögliche Einordnungen für Morgen, Tag und Abend – als Orientierung für eine bewusste Tagesstruktur.',

                'rows' =>
                    5,

                'new_lines' =>
                    '',

                'required' =>
                    1,
            ],


            /* =================================================
               MORGEN
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_morning_tab',

                'label' =>
                    __(
                        'Morgen',
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
                    'field_jl_routines_morning_enabled',

                'label' =>
                    __(
                        'Morgenroutine anzeigen',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_morning_enabled',

                'type' =>
                    'true_false',

                'default_value' =>
                    1,

                'ui' =>
                    1,

                'ui_on_text' =>
                    __('Ja', 'jung-leben'),

                'ui_off_text' =>
                    __('Nein', 'jung-leben'),
            ],

            [
                'key' =>
                    'field_jl_routines_morning_icon',

                'label' =>
                    __(
                        'Symbol',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_morning_icon',

                'type' =>
                    'text',

                'default_value' =>
                    '☀',

                'maxlength' =>
                    10,
            ],

            [
                'key' =>
                    'field_jl_routines_morning_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_morning_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Morgens',

                'maxlength' =>
                    60,
            ],

            [
                'key' =>
                    'field_jl_routines_morning_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_morning_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Bewusst in den Tag starten.',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_routines_morning_text',

                'label' =>
                    __(
                        'Beschreibung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_morning_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Am Morgen stehen bei Jung Leben Produkte im Mittelpunkt, die thematisch zu einem bewussten Start, Energie und dem persönlichen Tagesrhythmus passen.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               MITTAG / TAG
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_midday_tab',

                'label' =>
                    __(
                        'Mittag',
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
                    'field_jl_routines_midday_enabled',

                'label' =>
                    __(
                        'Mittagsroutine anzeigen',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_midday_enabled',

                'type' =>
                    'true_false',

                'default_value' =>
                    1,

                'ui' =>
                    1,

                'ui_on_text' =>
                    __('Ja', 'jung-leben'),

                'ui_off_text' =>
                    __('Nein', 'jung-leben'),
            ],

            [
                'key' =>
                    'field_jl_routines_midday_icon',

                'label' =>
                    __(
                        'Symbol',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_midday_icon',

                'type' =>
                    'text',

                'default_value' =>
                    '🌿',

                'maxlength' =>
                    10,
            ],

            [
                'key' =>
                    'field_jl_routines_midday_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_midday_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Tagsüber',

                'maxlength' =>
                    60,
            ],

            [
                'key' =>
                    'field_jl_routines_midday_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_midday_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Balance im Alltag.',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_routines_midday_text',

                'label' =>
                    __(
                        'Beschreibung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_midday_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Tagsüber können Produkte eingeordnet werden, die sich flexibel in den Alltag integrieren lassen oder thematisch zu Balance, Ernährung und Pflanzenstoffen passen.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               ABEND
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_evening_tab',

                'label' =>
                    __(
                        'Abend',
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
                    'field_jl_routines_evening_enabled',

                'label' =>
                    __(
                        'Abendroutine anzeigen',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_evening_enabled',

                'type' =>
                    'true_false',

                'default_value' =>
                    1,

                'ui' =>
                    1,

                'ui_on_text' =>
                    __('Ja', 'jung-leben'),

                'ui_off_text' =>
                    __('Nein', 'jung-leben'),
            ],

            [
                'key' =>
                    'field_jl_routines_evening_icon',

                'label' =>
                    __(
                        'Symbol',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_evening_icon',

                'type' =>
                    'text',

                'default_value' =>
                    '☾',

                'maxlength' =>
                    10,
            ],

            [
                'key' =>
                    'field_jl_routines_evening_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_evening_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Abends',

                'maxlength' =>
                    60,
            ],

            [
                'key' =>
                    'field_jl_routines_evening_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_evening_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Den Tag bewusst ausklingen lassen.',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_routines_evening_text',

                'label' =>
                    __(
                        'Beschreibung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_evening_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Am Abend stehen Themen wie Entspannung, Regeneration und eine bewusste Vorbereitung auf die Nacht im Vordergrund.',

                'rows' =>
                    4,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               PRODUKTE
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_products_tab',

                'label' =>
                    __(
                        'Produkte',
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
                    'field_jl_routines_products_info',

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
                        'Welche Produkte morgens, mittags oder abends angezeigt werden, wird direkt unter «Produkte» über die jeweilige Tageszeit festgelegt. Diese Zuordnung wird automatisch übernommen.',
                        'jung-leben'
                    ),
            ],

            [
                'key' =>
                    'field_jl_routines_product_button',

                'label' =>
                    __(
                        'Button-Text auf Produktkarten',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_product_button',

                'type' =>
                    'text',

                'default_value' =>
                    'Produkt ansehen',

                'maxlength' =>
                    60,
            ],

            [
                'key' =>
                    'field_jl_routines_empty_text',

                'label' =>
                    __(
                        'Text ohne passende Produkte',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_empty_text',

                'type' =>
                    'text',

                'default_value' =>
                    'Für diese Tageszeit sind aktuell noch keine Produkte hinterlegt.',

                'maxlength' =>
                    160,
            ],


            /* =================================================
               HINWEIS
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_notice_tab',

                'label' =>
                    __(
                        'Hinweis',
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
                    'field_jl_routines_notice_enabled',

                'label' =>
                    __(
                        'Hinweis anzeigen',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_notice_enabled',

                'type' =>
                    'true_false',

                'default_value' =>
                    1,

                'ui' =>
                    1,

                'ui_on_text' =>
                    __('Ja', 'jung-leben'),

                'ui_off_text' =>
                    __('Nein', 'jung-leben'),
            ],

            [
                'key' =>
                    'field_jl_routines_notice_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_notice_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Eine Routine ist individuell.',

                'maxlength' =>
                    120,
            ],

            [
                'key' =>
                    'field_jl_routines_notice_text',

                'label' =>
                    __(
                        'Hinweistext',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_notice_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Die dargestellten Routinen sind Beispiele zur Orientierung. Produkte, Kombinationen, Dosierungen und Einnahmedauer sind individuell und sollten zur persönlichen Situation passen.',

                'rows' =>
                    5,

                'new_lines' =>
                    '',
            ],


            /* =================================================
               ABSCHLUSS
               ================================================= */

            [
                'key' =>
                    'field_jl_routines_cta_tab',

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
                    'field_jl_routines_cta_enabled',

                'label' =>
                    __(
                        'Abschlussbereich anzeigen',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_cta_enabled',

                'type' =>
                    'true_false',

                'default_value' =>
                    1,

                'ui' =>
                    1,

                'ui_on_text' =>
                    __('Ja', 'jung-leben'),

                'ui_off_text' =>
                    __('Nein', 'jung-leben'),
            ],

            [
                'key' =>
                    'field_jl_routines_cta_eyebrow',

                'label' =>
                    __(
                        'Kurze Einleitung',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_cta_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Weiter entdecken',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_routines_cta_title',

                'label' =>
                    __(
                        'Überschrift',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_cta_title',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Finde Produkte, die zu deinem Alltag passen.',

                'rows' =>
                    2,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_routines_cta_text',

                'label' =>
                    __(
                        'Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_cta_text',

                'type' =>
                    'textarea',

                'default_value' =>
                    'Entdecke alle Empfehlungen und filtere sie nach Themen, Marken oder Tageszeit.',

                'rows' =>
                    3,

                'new_lines' =>
                    '',
            ],

            [
                'key' =>
                    'field_jl_routines_cta_button_text',

                'label' =>
                    __(
                        'Button-Text',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_cta_button_text',

                'type' =>
                    'text',

                'default_value' =>
                    'Empfehlungen entdecken',

                'maxlength' =>
                    80,
            ],

            [
                'key' =>
                    'field_jl_routines_cta_button_url',

                'label' =>
                    __(
                        'Button-Ziel',
                        'jung-leben'
                    ),

                'name' =>
                    'jl_routines_cta_button_url',

                'type' =>
                    'url',

                'instructions' =>
                    __(
                        'Leer lassen, um automatisch auf die Empfehlungsseite zu verlinken.',
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
                        $routines_page->ID,
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
    'jung_leben_register_routines_fields'
);