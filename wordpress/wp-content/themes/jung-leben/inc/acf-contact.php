<?php
/**
 * ACF-Felder für die Kontaktseite.
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Kontaktfelder registrieren.
 */
function jung_leben_register_contact_fields(): void
{
    if (
        ! function_exists(
            'acf_add_local_field_group'
        )
    ) {
        return;
    }


    /**
     * Kontaktseite suchen.
     */
    $contact_page =
        get_page_by_path(
            'kontakt'
        );

    if (
        ! $contact_page
        instanceof WP_Post
    ) {
        return;
    }


    acf_add_local_field_group([
        'key' =>
            'group_jl_contact',

        'title' =>
            'Kontakt – Seiteninhalt',

        'fields' => [

            /* =================================================
               INHALT
               ================================================= */

            [
                'key' =>
                    'field_jl_contact_tab_content',

                'label' =>
                    'Inhalt',

                'name' =>
                    '',

                'type' =>
                    'tab',
            ],


            [
                'key' =>
                    'field_jl_contact_eyebrow',

                'label' =>
                    'Kleine Überschrift',

                'name' =>
                    'jl_contact_eyebrow',

                'type' =>
                    'text',

                'default_value' =>
                    'Kontakt',
            ],


            [
                'key' =>
                    'field_jl_contact_title',

                'label' =>
                    'Hauptüberschrift',

                'name' =>
                    'jl_contact_title',

                'type' =>
                    'text',

                'default_value' =>
                    'Hast du eine Frage oder möchtest etwas teilen?',
            ],


            [
                'key' =>
                    'field_jl_contact_intro',

                'label' =>
                    'Einleitung',

                'name' =>
                    'jl_contact_intro',

                'type' =>
                    'textarea',

                'rows' =>
                    4,

                'new_lines' =>
                    '',

                'default_value' =>
                    'Wir freuen uns über Fragen, Feedback, persönliche Erfahrungen, Produkthinweise oder Ideen für Jung Leben. Schreib uns einfach über das Formular.',
            ],


            /* =================================================
               TECHNIK
               ================================================= */

            [
                'key' =>
                    'field_jl_contact_tab_settings',

                'label' =>
                    'Einstellungen',

                'name' =>
                    '',

                'type' =>
                    'tab',
            ],


            [
                'key' =>
                    'field_jl_contact_email',

                'label' =>
                    'Empfängeradresse',

                'name' =>
                    'jl_contact_email',

                'type' =>
                    'email',

                'instructions' =>
                    'An diese Adresse werden Nachrichten aus dem Kontaktformular gesendet.',

                'default_value' =>
                    'info@jung-leben.ch',
            ],


            [
                'key' =>
                    'field_jl_contact_note',

                'label' =>
                    'Hinweis unter dem Formular',

                'name' =>
                    'jl_contact_note',

                'type' =>
                    'textarea',

                'rows' =>
                    3,

                'new_lines' =>
                    '',

                'default_value' =>
                    'Bitte beachte: Individuelle medizinische Beratungen, Diagnosen oder Therapieempfehlungen können wir nicht anbieten.',
            ],
        ],


        'location' => [
            [
                [
                    'param' =>
                        'post',

                    'operator' =>
                        '==',

                    'value' =>
                        (string) $contact_page->ID,
                ],
            ],
        ],


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
    ]);
}

add_action(
    'acf/init',
    'jung_leben_register_contact_fields'
);