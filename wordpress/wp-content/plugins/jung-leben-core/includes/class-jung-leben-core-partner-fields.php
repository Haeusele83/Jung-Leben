<?php
/**
 * Strukturierte Felder für Affiliate-Partner.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * ACF-Felder der Affiliate-Partner.
 */
final class Jung_Leben_Core_Partner_Fields
{
    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'acf/init',
            [
                self::class,
                'register_fields',
            ]
        );
    }


    /**
     * ACF-Felder registrieren.
     */
    public static function register_fields(): void
    {
        if (
            ! function_exists(
                'acf_add_local_field_group'
            )
        ) {
            return;
        }


        acf_add_local_field_group([
            'key' =>
                'group_jl_partner_details',

            'title' =>
                __(
                    'Jung Leben – Affiliate-Partner',
                    'jung-leben-core'
                ),

            'fields' => [

                /* =============================================
                   ALLGEMEIN
                   ============================================= */

                [
                    'key' =>
                        'field_jl_partner_general_tab',

                    'label' =>
                        __(
                            'Allgemein',
                            'jung-leben-core'
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
                        'field_jl_partner_status',

                    'label' =>
                        __(
                            'Status',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_status',

                    'type' =>
                        'select',

                    'choices' => [
                        'planned' =>
                            __(
                                'Geplant',
                                'jung-leben-core'
                            ),

                        'active' =>
                            __(
                                'Aktiv',
                                'jung-leben-core'
                            ),

                        'paused' =>
                            __(
                                'Pausiert',
                                'jung-leben-core'
                            ),

                        'ended' =>
                            __(
                                'Beendet',
                                'jung-leben-core'
                            ),
                    ],

                    'default_value' =>
                        'planned',

                    'allow_null' =>
                        0,

                    'multiple' =>
                        0,

                    'ui' =>
                        1,

                    'return_format' =>
                        'value',

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_partner_model',

                    'label' =>
                        __(
                            'Tracking- und Vergütungsmodell',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_model',

                    'type' =>
                        'select',

                    'choices' => [
                        'affiliate' =>
                            __(
                                'Affiliate-Link',
                                'jung-leben-core'
                            ),

                        'discount' =>
                            __(
                                'Rabattcode',
                                'jung-leben-core'
                            ),

                        'affiliate_discount' =>
                            __(
                                'Affiliate-Link + Rabattcode',
                                'jung-leben-core'
                            ),

                        'manual' =>
                            __(
                                'Individuelle Vereinbarung',
                                'jung-leben-core'
                            ),
                    ],

                    'default_value' =>
                        'affiliate',

                    'allow_null' =>
                        0,

                    'multiple' =>
                        0,

                    'ui' =>
                        1,

                    'return_format' =>
                        'value',

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_partner_website',

                    'label' =>
                        __(
                            'Offizielle Website',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_website',

                    'type' =>
                        'url',

                    'placeholder' =>
                        'https://',

                    'wrapper' => [
                        'width' =>
                            '100',
                    ],
                ],


                /* =============================================
                   SHOP-LINKS
                   ============================================= */

                [
                    'key' =>
                        'field_jl_partner_links_tab',

                    'label' =>
                        __(
                            'Shop-Links',
                            'jung-leben-core'
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
                        'field_jl_partner_shop_url_de',

                    'label' =>
                        __(
                            'Persönlicher Shop-Link Deutsch',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_shop_url_de',

                    'type' =>
                        'url',

                    'instructions' =>
                        __(
                            'Allgemeiner persönlicher Affiliate- oder Partnerlink. Einzelprodukt-Links werden später direkt beim Produkt hinterlegt.',
                            'jung-leben-core'
                        ),

                    'placeholder' =>
                        'https://',
                ],


                [
                    'key' =>
                        'field_jl_partner_shop_url_en',

                    'label' =>
                        __(
                            'Persönlicher Shop-Link Englisch',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_shop_url_en',

                    'type' =>
                        'url',

                    'instructions' =>
                        __(
                            'Optionaler englischsprachiger Affiliate- oder Partnerlink.',
                            'jung-leben-core'
                        ),

                    'placeholder' =>
                        'https://',
                ],


                [
                    'key' =>
                        'field_jl_partner_button_text',

                    'label' =>
                        __(
                            'Standardtext des Buttons',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_button_text',

                    'type' =>
                        'text',

                    'default_value' =>
                        'Beim Partner ansehen',

                    'maxlength' =>
                        80,
                ],


                /* =============================================
                   KUNDENVORTEIL
                   ============================================= */

                [
                    'key' =>
                        'field_jl_partner_offer_tab',

                    'label' =>
                        __(
                            'Kundenvorteil',
                            'jung-leben-core'
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
                        'field_jl_partner_public_offer',

                    'label' =>
                        __(
                            'Kundenvorteil öffentlich anzeigen',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_public_offer',

                    'type' =>
                        'true_false',

                    'default_value' =>
                        0,

                    'ui' =>
                        1,

                    'ui_on_text' =>
                        __(
                            'Ja',
                            'jung-leben-core'
                        ),

                    'ui_off_text' =>
                        __(
                            'Nein',
                            'jung-leben-core'
                        ),
                ],


                [
                    'key' =>
                        'field_jl_partner_offer_title',

                    'label' =>
                        __(
                            'Titel des Kundenvorteils',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_offer_title',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Zum Beispiel «12 % Rabatt bei Luvy».',
                            'jung-leben-core'
                        ),

                    'maxlength' =>
                        120,
                ],


                [
                    'key' =>
                        'field_jl_partner_discount_text',

                    'label' =>
                        __(
                            'Rabatt / Kundenvorteil',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_discount_text',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Zum Beispiel «12 % Rabatt».',
                            'jung-leben-core'
                        ),

                    'maxlength' =>
                        100,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_partner_discount_code',

                    'label' =>
                        __(
                            'Rabattcode',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_discount_code',

                    'type' =>
                        'text',

                    'maxlength' =>
                        80,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_partner_public_note',

                    'label' =>
                        __(
                            'Öffentlicher Hinweis',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_public_note',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Optionaler Hinweis zum Kundenvorteil. Keine interne Provisionsinformation erfassen.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        4,

                    'new_lines' =>
                        '',
                ],


                /* =============================================
                   INTERNE VERTRAGSDATEN
                   ============================================= */

                [
                    'key' =>
                        'field_jl_partner_internal_tab',

                    'label' =>
                        __(
                            'Interne Angaben',
                            'jung-leben-core'
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
                        'field_jl_partner_commission_model',

                    'label' =>
                        __(
                            'Provisionsmodell',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_commission_model',

                    'type' =>
                        'select',

                    'instructions' =>
                        __(
                            'Nur intern. Diese Information wird nicht im Frontend ausgegeben.',
                            'jung-leben-core'
                        ),

                    'choices' => [
                        'percentage' =>
                            __(
                                'Prozentuale Provision',
                                'jung-leben-core'
                            ),

                        'fixed' =>
                            __(
                                'Fixbetrag pro Bestellung',
                                'jung-leben-core'
                            ),

                        'mixed' =>
                            __(
                                'Gemischtes Modell',
                                'jung-leben-core'
                            ),

                        'other' =>
                            __(
                                'Sonstiges',
                                'jung-leben-core'
                            ),

                        'unknown' =>
                            __(
                                'Noch nicht definiert',
                                'jung-leben-core'
                            ),
                    ],

                    'default_value' =>
                        'unknown',

                    'allow_null' =>
                        0,

                    'ui' =>
                        1,

                    'return_format' =>
                        'value',

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_partner_commission_value',

                    'label' =>
                        __(
                            'Provision',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_commission_value',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Nur intern. Zum Beispiel «CHF 6 pro qualifizierter Bestellung».',
                            'jung-leben-core'
                        ),

                    'maxlength' =>
                        200,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_partner_settlement',

                    'label' =>
                        __(
                            'Abrechnung / Reporting',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_settlement',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Interne Angaben zur Auswertung, Rechnungsstellung und Auszahlung.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        4,

                    'new_lines' =>
                        '',
                ],


                [
                    'key' =>
                        'field_jl_partner_internal_notes',

                    'label' =>
                        __(
                            'Interne Notizen',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_partner_internal_notes',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Zum Beispiel Ansprechpartner, Vertragsdetails, besondere Bedingungen oder Hinweise zum Tracking.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        5,

                    'new_lines' =>
                        '',
                ],
            ],


            'location' => [
                [
                    [
                        'param' =>
                            'post_type',

                        'operator' =>
                            '==',

                        'value' =>
                            Jung_Leben_Core_Partners::POST_TYPE,
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
}