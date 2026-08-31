<?php
/**
 * ACF-Felder für die Seite «Empfehlungen».
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Felder für die Empfehlungsseite registrieren.
 */
function jung_leben_register_recommendations_fields(): void
{
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }


    /*
     * Die Empfehlungsseite anhand ihres Slugs suchen.
     *
     * Dadurch müssen wir keine feste WordPress-Seiten-ID
     * im Theme hinterlegen.
     */
    $recommendations_page =
        get_page_by_path(
            'empfehlungen',
            OBJECT,
            'page'
        );


    if (! $recommendations_page instanceof WP_Post) {
        return;
    }


    acf_add_local_field_group([
        'key'   => 'group_jung_leben_recommendations',
        'title' => __('Empfehlungen – Inhalte', 'jung-leben'),

        'fields' => [

            /* =================================================
               SEITENKOPF
               ================================================= */

            [
                'key'       => 'field_jl_recommendations_header_tab',
                'label'     => __('Seitenkopf', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],

            [
                'key'           => 'field_jl_recommendations_eyebrow',
                'label'         => __('Kurze Einleitung', 'jung-leben'),
                'name'          => 'jl_recommendations_eyebrow',
                'type'          => 'text',
                'instructions'  => __(
                    'Kleine Zeile oberhalb der Hauptüberschrift.',
                    'jung-leben'
                ),
                'default_value' => 'Bewusst ausgewählt',
                'maxlength'     => 100,
            ],

            [
                'key'           => 'field_jl_recommendations_title',
                'label'         => __('Hauptüberschrift', 'jung-leben'),
                'name'          => 'jl_recommendations_title',
                'type'          => 'textarea',
                'instructions'  => __(
                    'Haupttitel der Empfehlungsseite.',
                    'jung-leben'
                ),
                'default_value' => 'Empfehlungen',
                'rows'          => 2,
                'new_lines'     => '',
                'required'      => 1,
            ],

            [
                'key'           => 'field_jl_recommendations_intro',
                'label'         => __('Einleitungstext', 'jung-leben'),
                'name'          => 'jl_recommendations_intro',
                'type'          => 'textarea',
                'instructions'  => __(
                    'Kurze Einführung unterhalb der Hauptüberschrift.',
                    'jung-leben'
                ),
                'default_value' =>
                    'Ausgewählte Produkte rund um Longevity, Wohlbefinden und bewusste Routinen – persönlich eingeordnet und mit dem nötigen Kontext.',
                'rows'          => 4,
                'new_lines'     => '',
                'required'      => 1,
            ],


            /* =================================================
               SUCHE UND FILTER
               ================================================= */

            [
                'key'       => 'field_jl_recommendations_filters_tab',
                'label'     => __('Suche & Filter', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],

            [
                'key'          => 'field_jl_recommendations_filters_info',
                'label'        => __('Hinweis', 'jung-leben'),
                'name'         => '',
                'type'         => 'message',
                'message'      => __(
                    'Die verfügbaren Kategorien, Marken und Routinen werden automatisch anhand der vorhandenen Produkte angezeigt. Hier können nur die sichtbaren Bezeichnungen angepasst werden.',
                    'jung-leben'
                ),
            ],

            [
                'key'           => 'field_jl_recommendations_search_placeholder',
                'label'         => __('Text im Suchfeld', 'jung-leben'),
                'name'          => 'jl_recommendations_search_placeholder',
                'type'          => 'text',
                'default_value' => 'Produkte durchsuchen …',
                'maxlength'     => 100,
            ],

            [
                'key'           => 'field_jl_recommendations_filter_all',
                'label'         => __('Bezeichnung «Alle»', 'jung-leben'),
                'name'          => 'jl_recommendations_filter_all',
                'type'          => 'text',
                'default_value' => 'Alle',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_filter_category',
                'label'         => __('Bezeichnung «Kategorie»', 'jung-leben'),
                'name'          => 'jl_recommendations_filter_category',
                'type'          => 'text',
                'default_value' => 'Kategorie',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_filter_brand',
                'label'         => __('Bezeichnung «Marke»', 'jung-leben'),
                'name'          => 'jl_recommendations_filter_brand',
                'type'          => 'text',
                'default_value' => 'Marke',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_filter_routine',
                'label'         => __('Bezeichnung «Routine»', 'jung-leben'),
                'name'          => 'jl_recommendations_filter_routine',
                'type'          => 'text',
                'default_value' => 'Routine',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_filter_reset',
                'label'         => __('Text «Filter zurücksetzen»', 'jung-leben'),
                'name'          => 'jl_recommendations_filter_reset',
                'type'          => 'text',
                'default_value' => 'Filter zurücksetzen',
                'maxlength'     => 80,
            ],


            /* =================================================
               ROUTINEN
               ================================================= */

            [
                'key'       => 'field_jl_recommendations_routines_tab',
                'label'     => __('Routinen', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],

            [
                'key'          => 'field_jl_recommendations_routines_info',
                'label'        => __('Hinweis', 'jung-leben'),
                'name'         => '',
                'type'         => 'message',
                'message'      => __(
                    'Die Zuordnung eines Produktes zu Morgen, Mittag oder Abend wird direkt beim Produkt gepflegt. Hier werden nur die sichtbaren Bezeichnungen der Filter angepasst.',
                    'jung-leben'
                ),
            ],

            [
                'key'           => 'field_jl_recommendations_routine_morning',
                'label'         => __('Morgen', 'jung-leben'),
                'name'          => 'jl_recommendations_routine_morning',
                'type'          => 'text',
                'default_value' => 'Morgens',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_routine_midday',
                'label'         => __('Mittag', 'jung-leben'),
                'name'          => 'jl_recommendations_routine_midday',
                'type'          => 'text',
                'default_value' => 'Mittags',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_routine_evening',
                'label'         => __('Abend', 'jung-leben'),
                'name'          => 'jl_recommendations_routine_evening',
                'type'          => 'text',
                'default_value' => 'Abends',
                'maxlength'     => 50,
            ],

            [
                'key'           => 'field_jl_recommendations_routine_flexible',
                'label'         => __('Flexibel', 'jung-leben'),
                'name'          => 'jl_recommendations_routine_flexible',
                'type'          => 'text',
                'default_value' => 'Flexibel',
                'maxlength'     => 50,
            ],


            /* =================================================
               PRODUKTBEREICH
               ================================================= */

            [
                'key'       => 'field_jl_recommendations_products_tab',
                'label'     => __('Produktbereich', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],

            [
                'key'          => 'field_jl_recommendations_products_info',
                'label'        => __('Hinweis', 'jung-leben'),
                'name'         => '',
                'type'         => 'message',
                'message'      => __(
                    'Die Produkte selbst werden unter «Produkte» im WordPress-Admin bearbeitet. Auf dieser Seite werden sie automatisch angezeigt.',
                    'jung-leben'
                ),
            ],

            [
                'key'           => 'field_jl_recommendations_product_button',
                'label'         => __('Button-Text auf Produktkarten', 'jung-leben'),
                'name'          => 'jl_recommendations_product_button',
                'type'          => 'text',
                'instructions'  => __(
                    'Wird verwendet, wenn für das einzelne Produkt kein eigener Button-Text hinterlegt ist.',
                    'jung-leben'
                ),
                'default_value' => 'Produkt ansehen',
                'maxlength'     => 60,
            ],


            /* =================================================
               KEINE ERGEBNISSE
               ================================================= */

            [
                'key'       => 'field_jl_recommendations_empty_tab',
                'label'     => __('Keine Ergebnisse', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],

            [
                'key'           => 'field_jl_recommendations_empty_title',
                'label'         => __('Überschrift', 'jung-leben'),
                'name'          => 'jl_recommendations_empty_title',
                'type'          => 'text',
                'default_value' => 'Keine passenden Empfehlungen gefunden.',
                'maxlength'     => 120,
            ],

            [
                'key'           => 'field_jl_recommendations_empty_text',
                'label'         => __('Hinweistext', 'jung-leben'),
                'name'          => 'jl_recommendations_empty_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Passe deine Suche oder die ausgewählten Filter an, um weitere Produkte zu entdecken.',
                'rows'          => 4,
                'new_lines'     => '',
            ],

            [
                'key'           => 'field_jl_recommendations_empty_reset',
                'label'         => __('Button-Text', 'jung-leben'),
                'name'          => 'jl_recommendations_empty_reset',
                'type'          => 'text',
                'default_value' => 'Alle Filter zurücksetzen',
                'maxlength'     => 80,
            ],


            /* =================================================
               TRANSPARENZ
               ================================================= */

            [
                'key'       => 'field_jl_recommendations_transparency_tab',
                'label'     => __('Transparenz', 'jung-leben'),
                'name'      => '',
                'type'      => 'tab',
                'placement' => 'top',
            ],

            [
                'key'           => 'field_jl_recommendations_transparency_enabled',
                'label'         => __('Transparenz-Hinweis anzeigen', 'jung-leben'),
                'name'          => 'jl_recommendations_transparency_enabled',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
                'ui_on_text'    => __('Ja', 'jung-leben'),
                'ui_off_text'   => __('Nein', 'jung-leben'),
            ],

            [
                'key'           => 'field_jl_recommendations_transparency_title',
                'label'         => __('Überschrift', 'jung-leben'),
                'name'          => 'jl_recommendations_transparency_title',
                'type'          => 'text',
                'default_value' => 'Wie Empfehlungen auf Jung Leben entstehen',
                'maxlength'     => 120,
            ],

            [
                'key'           => 'field_jl_recommendations_transparency_text',
                'label'         => __('Hinweistext', 'jung-leben'),
                'name'          => 'jl_recommendations_transparency_text',
                'type'          => 'textarea',
                'default_value' =>
                    'Produkte werden auf Jung Leben nicht allein aufgrund einer möglichen Partnerschaft ausgewählt. Entscheidend sind die thematische Einordnung, persönliche Erfahrungen und die Frage, ob ein Produkt sinnvoll zum jeweiligen Inhalt passt.',
                'rows'          => 5,
                'new_lines'     => '',
            ],

            [
                'key'           => 'field_jl_recommendations_transparency_link_text',
                'label'         => __('Link-Text', 'jung-leben'),
                'name'          => 'jl_recommendations_transparency_link_text',
                'type'          => 'text',
                'default_value' => 'Mehr zum Affiliate-Hinweis',
                'maxlength'     => 80,
            ],

            [
                'key'          => 'field_jl_recommendations_transparency_link_url',
                'label'        => __('Link-Ziel', 'jung-leben'),
                'name'         => 'jl_recommendations_transparency_link_url',
                'type'         => 'url',
                'instructions' => __(
                    'Leer lassen, um automatisch auf die Seite «Affiliate-Hinweis» zu verlinken.',
                    'jung-leben'
                ),
            ],
        ],


        /* =====================================================
           NUR AUF DER SEITE «EMPFEHLUNGEN»
           ===================================================== */

        'location' => [
            [
                [
                    'param'    => 'page',
                    'operator' => '==',
                    'value'    => (string) $recommendations_page->ID,
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
    'jung_leben_register_recommendations_fields'
);