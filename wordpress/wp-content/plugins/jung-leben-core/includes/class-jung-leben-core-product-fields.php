<?php
/**
 * ACF-Felder und Kaufdaten für Produkte.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Strukturierte Zusatzfelder und Partnerlogik für Produkte.
 */
final class Jung_Leben_Core_Product_Fields
{
    /* =========================================================
       INITIALISIERUNG
       ========================================================= */

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


        add_action(
            'admin_notices',
            [
                self::class,
                'show_acf_notice',
            ]
        );
    }


    /* =========================================================
       ACF-FELDER
       ========================================================= */

    /**
     * Produktfelder registrieren.
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
                'group_jung_leben_product',

            'title' =>
                __(
                    'Produktinformationen',
                    'jung-leben-core'
                ),

            'fields' => [

                /* =============================================
                   REDAKTIONELLE EINORDNUNG
                   ============================================= */

                [
                    'key' =>
                        'field_jl_product_editorial_tab',

                    'label' =>
                        __(
                            'Einordnung',
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
                        'field_jl_product_recommendation_status',

                    'label' =>
                        __(
                            'Empfehlungsstatus',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_recommendation_status',

                    'type' =>
                        'select',

                    'instructions' =>
                        __(
                            'Redaktionelle Einordnung des Produkts auf Jung Leben.',
                            'jung-leben-core'
                        ),

                    'choices' => [
                        'neutral' =>
                            __(
                                'Neutral vorgestellt',
                                'jung-leben-core'
                            ),

                        'interesting' =>
                            __(
                                'Interessante Option',
                                'jung-leben-core'
                            ),

                        'recommended' =>
                            __(
                                'Empfohlen',
                                'jung-leben-core'
                            ),

                        'favorite' =>
                            __(
                                'Persönlicher Favorit',
                                'jung-leben-core'
                            ),
                    ],

                    'default_value' =>
                        'neutral',

                    'return_format' =>
                        'value',

                    'allow_null' =>
                        0,

                    'multiple' =>
                        0,

                    'ui' =>
                        1,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_personally_tested',

                    'label' =>
                        __(
                            'Persönlich getestet',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_personally_tested',

                    'type' =>
                        'true_false',

                    'instructions' =>
                        __(
                            'Aktivieren, wenn Roberto das Produkt persönlich verwendet oder getestet hat.',
                            'jung-leben-core'
                        ),

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

                    'wrapper' => [
                        'width' =>
                            '25',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_featured',

                    'label' =>
                        __(
                            'Besonders hervorheben',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_featured',

                    'type' =>
                        'true_false',

                    'instructions' =>
                        __(
                            'Das Produkt kann auf Übersichtsseiten besonders hervorgehoben werden.',
                            'jung-leben-core'
                        ),

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

                    'wrapper' => [
                        'width' =>
                            '25',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_sort_priority',

                    'label' =>
                        __(
                            'Sortierpriorität',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_sort_priority',

                    'type' =>
                        'number',

                    'instructions' =>
                        __(
                            'Produkte mit einer höheren Zahl können weiter oben angezeigt werden.',
                            'jung-leben-core'
                        ),

                    'default_value' =>
                        0,

                    'min' =>
                        0,

                    'max' =>
                        999,

                    'step' =>
                        1,

                    'wrapper' => [
                        'width' =>
                            '25',
                    ],
                ],


                /* =============================================
                   ANWENDUNG UND ERFAHRUNG
                   ============================================= */

                [
                    'key' =>
                        'field_jl_product_experience_tab',

                    'label' =>
                        __(
                            'Anwendung und Erfahrung',
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
                        'field_jl_product_purpose',

                    'label' =>
                        __(
                            'Möglicher Verwendungszweck',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_purpose',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Sachliche Beschreibung, wofür das Produkt eingesetzt oder betrachtet wird. Keine Heilversprechen verwenden.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        4,

                    'new_lines' =>
                        '',
                ],


                [
                    'key' =>
                        'field_jl_product_routine_time',

                    'label' =>
                        __(
                            'Mögliche Tageszeit',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_routine_time',

                    'type' =>
                        'checkbox',

                    'instructions' =>
                        __(
                            'Mehrere Angaben sind möglich.',
                            'jung-leben-core'
                        ),

                    'choices' => [
                        'morning' =>
                            __(
                                'Morgens',
                                'jung-leben-core'
                            ),

                        'midday' =>
                            __(
                                'Mittags',
                                'jung-leben-core'
                            ),

                        'evening' =>
                            __(
                                'Abends',
                                'jung-leben-core'
                            ),

                        'flexible' =>
                            __(
                                'Zeitlich flexibel',
                                'jung-leben-core'
                            ),
                    ],

                    'return_format' =>
                        'value',

                    'layout' =>
                        'horizontal',

                    'toggle' =>
                        0,
                ],


                [
                    'key' =>
                        'field_jl_product_personal_experience',

                    'label' =>
                        __(
                            'Persönliche Erfahrung',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_personal_experience',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Persönliche und nachvollziehbare Erfahrung von Roberto. Beobachtungen klar als persönliche Erfahrung formulieren.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        6,

                    'new_lines' =>
                        'br',
                ],


                [
                    'key' =>
                        'field_jl_product_benefits',

                    'label' =>
                        __(
                            'Positive Eigenschaften',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_benefits',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Die wichtigsten positiven Eigenschaften oder praktischen Vorteile. Pro Zeile kann ein Punkt erfasst werden.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        6,

                    'new_lines' =>
                        '',
                ],


                [
                    'key' =>
                        'field_jl_product_limitations',

                    'label' =>
                        __(
                            'Einschränkungen und Hinweise',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_limitations',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Mögliche Nachteile, Einschränkungen, Besonderheiten oder Gründe, weshalb das Produkt nicht für alle Personen geeignet sein könnte.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        6,

                    'new_lines' =>
                        'br',
                ],


                /* =============================================
                   PRODUKT- UND KAUFDATEN
                   ============================================= */

                [
                    'key' =>
                        'field_jl_product_purchase_tab',

                    'label' =>
                        __(
                            'Produkt- und Kaufdaten',
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
                        'field_jl_product_partner_info',

                    'label' =>
                        __(
                            'Partnerlogik',
                            'jung-leben-core'
                        ),

                    'name' =>
                        '',

                    'type' =>
                        'message',

                    'message' =>
                        __(
                            'Normalerweise wird der Affiliate-Partner automatisch über die Marke übernommen. Nur bei einer Ausnahme muss hier ein abweichender Partner ausgewählt werden. Ein produktspezifischer Affiliate-Link hat immer Vorrang.',
                            'jung-leben-core'
                        ),
                ],


                [
                    'key' =>
                        'field_jl_product_partner_override',

                    'label' =>
                        __(
                            'Abweichender Affiliate-Partner',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_partner_override',

                    'type' =>
                        'post_object',

                    'instructions' =>
                        __(
                            'Optional. Leer lassen, wenn der bei der Marke hinterlegte Partner verwendet werden soll.',
                            'jung-leben-core'
                        ),

                    'required' =>
                        0,

                    'post_type' => [
                        Jung_Leben_Core_Partners::POST_TYPE,
                    ],

                    'post_status' => [
                        'publish',
                    ],

                    'allow_null' =>
                        1,

                    'multiple' =>
                        0,

                    'return_format' =>
                        'id',

                    'ui' =>
                        1,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_use_partner_offer',

                    'label' =>
                        __(
                            'Kundenvorteil anzeigen',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_use_partner_offer',

                    'type' =>
                        'true_false',

                    'instructions' =>
                        __(
                            'Wenn aktiviert, kann ein beim Partner hinterlegter Rabatt oder Kundenvorteil auf der Produktseite angezeigt werden.',
                            'jung-leben-core'
                        ),

                    'default_value' =>
                        1,

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

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /*
                 * Bestehendes Feld bleibt bewusst erhalten,
                 * damit vorhandene Daten oder Importe nicht
                 * verloren gehen.
                 */
                [
                    'key' =>
                        'field_jl_product_partner_name',

                    'label' =>
                        __(
                            'Partnername / Shop – Altbestand',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_partner_name',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Nur für bestehende importierte Daten. Bei neuen Produkten den Affiliate-Partner über die Marke oder das Feld «Abweichender Affiliate-Partner» verwalten.',
                            'jung-leben-core'
                        ),

                    'maxlength' =>
                        120,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_partner_product_id',

                    'label' =>
                        __(
                            'Externe Produkt-ID',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_partner_product_id',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Artikelnummer oder Produkt-ID des Partners. Kann später für Produktfeeds, APIs oder Synchronisationen verwendet werden.',
                            'jung-leben-core'
                        ),

                    'maxlength' =>
                        150,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_original_url',

                    'label' =>
                        __(
                            'Direkte Produktseite',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_original_url',

                    'type' =>
                        'url',

                    'instructions' =>
                        __(
                            'Direkte URL dieses Produkts beim Shop. Bei einer Rabattcode-Partnerschaft wie Luvy kann diese Adresse direkt verwendet werden.',
                            'jung-leben-core'
                        ),

                    'placeholder' =>
                        'https://',
                ],


                [
                    'key' =>
                        'field_jl_product_affiliate_url',

                    'label' =>
                        __(
                            'Persönlicher Einzelprodukt-/Affiliate-Link',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_affiliate_url',

                    'type' =>
                        'url',

                    'instructions' =>
                        __(
                            'Produktspezifischer Tracking-Link. Zum Beispiel der persönliche Einzel-Artikel-Link von Haritaki. Wenn vorhanden, hat dieser Link immer Vorrang.',
                            'jung-leben-core'
                        ),

                    'placeholder' =>
                        'https://',
                ],


                [
                    'key' =>
                        'field_jl_product_price_display',

                    'label' =>
                        __(
                            'Preisanzeige',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_price_display',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Zum Beispiel «ca. CHF 24.90» oder «Preis beim Partner prüfen». Preise können sich ändern.',
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
                        'field_jl_product_discount_code',

                    'label' =>
                        __(
                            'Produkt-spezifischer Rabattcode',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_discount_code',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Normalerweise leer lassen. Der zentrale Code des Affiliate-Partners wird automatisch verwendet. Nur eintragen, wenn dieses Produkt ausnahmsweise einen eigenen Code hat.',
                            'jung-leben-core'
                        ),

                    'maxlength' =>
                        80,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_button_text',

                    'label' =>
                        __(
                            'Individueller Button-Text',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_button_text',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Optional. Leer lassen, um den Standardtext des Affiliate-Partners zu verwenden.',
                            'jung-leben-core'
                        ),

                    'default_value' =>
                        '',

                    'maxlength' =>
                        80,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                [
                    'key' =>
                        'field_jl_product_link_new_tab',

                    'label' =>
                        __(
                            'Partnerlink in neuem Tab öffnen',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_link_new_tab',

                    'type' =>
                        'true_false',

                    'default_value' =>
                        1,

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

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
                ],


                /* =============================================
                   TRANSPARENZ UND HINWEISE
                   ============================================= */

                [
                    'key' =>
                        'field_jl_product_transparency_tab',

                    'label' =>
                        __(
                            'Transparenz und Hinweise',
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
                        'field_jl_product_affiliate_notice',

                    'label' =>
                        __(
                            'Affiliate-Hinweis',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_affiliate_notice',

                    'type' =>
                        'textarea',

                    'default_value' =>
                        'Dieser Beitrag kann Affiliate-Links enthalten. Bei einem Kauf über einen solchen Link oder die Verwendung eines Partnercodes erhält Jung Leben möglicherweise eine Provision. Für dich entstehen keine zusätzlichen Kosten.',

                    'rows' =>
                        4,

                    'new_lines' =>
                        '',
                ],


                [
                    'key' =>
                        'field_jl_product_health_notice',

                    'label' =>
                        __(
                            'Gesundheitlicher Hinweis',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_health_notice',

                    'type' =>
                        'textarea',

                    'default_value' =>
                        'Die Informationen auf Jung Leben ersetzen keine medizinische Beratung, Diagnose oder Behandlung. Nahrungsergänzungsmittel sind kein Ersatz für eine ausgewogene Ernährung und eine gesunde Lebensweise. Bei gesundheitlichen Fragen oder Unsicherheiten sollte fachlicher Rat eingeholt werden.',

                    'rows' =>
                        5,

                    'new_lines' =>
                        '',
                ],


                [
                    'key' =>
                        'field_jl_product_source_note',

                    'label' =>
                        __(
                            'Interne Quellen- und Prüfnotiz',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_source_note',

                    'type' =>
                        'textarea',

                    'instructions' =>
                        __(
                            'Interne Notizen zu Herstellerangaben, Quellen, Prüfdatum oder offenen Fragen. Dieses Feld wird nicht öffentlich angezeigt.',
                            'jung-leben-core'
                        ),

                    'rows' =>
                        5,

                    'new_lines' =>
                        '',
                ],


                [
                    'key' =>
                        'field_jl_product_last_checked',

                    'label' =>
                        __(
                            'Zuletzt geprüft am',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_product_last_checked',

                    'type' =>
                        'date_picker',

                    'instructions' =>
                        __(
                            'Datum der letzten inhaltlichen oder preislichen Prüfung.',
                            'jung-leben-core'
                        ),

                    'display_format' =>
                        'd.m.Y',

                    'return_format' =>
                        'Y-m-d',

                    'first_day' =>
                        1,

                    'wrapper' => [
                        'width' =>
                            '50',
                    ],
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
                            Jung_Leben_Core_Products::POST_TYPE,
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

            'hide_on_screen' =>
                '',

            'active' =>
                true,

            'show_in_rest' =>
                0,
        ]);
    }


    /* =========================================================
       PRIMÄRE MARKE
       ========================================================= */

    /**
     * Erste zugeordnete Produktmarke laden.
     *
     * Jung Leben verwendet derzeit in der Regel
     * eine Hauptmarke pro Produkt.
     */
    public static function get_primary_brand(
        int $product_id
    ): ?WP_Term {
        $product_id =
            absint(
                $product_id
            );


        if (
            $product_id <= 0
        ) {
            return
                null;
        }


        $brands =
            wp_get_post_terms(
                $product_id,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        if (
            is_wp_error(
                $brands
            )
            || empty(
                $brands
            )
        ) {
            return
                null;
        }


        $brand =
            $brands[
                0
            ];


        return
            $brand
            instanceof WP_Term
                ? $brand
                : null;
    }


    /* =========================================================
       PARTNER DES PRODUKTS
       ========================================================= */

    /**
     * Partner-ID eines Produkts ermitteln.
     *
     * Priorität:
     *
     * 1. Produktspezifischer Partner
     * 2. Partner der Marke
     */
    public static function get_partner_id(
        int $product_id
    ): int {
        $product_id =
            absint(
                $product_id
            );


        if (
            $product_id <= 0
            || ! function_exists(
                'get_field'
            )
        ) {
            return
                0;
        }


        /* =====================================================
           PRODUKT-OVERRIDE
           ===================================================== */

        $override =
            get_field(
                'jl_product_partner_override',
                $product_id
            );


        if (
            $override
            instanceof WP_Post
        ) {
            $override_id =
                absint(
                    $override->ID
                );
        } else {
            $override_id =
                absint(
                    $override
                );
        }


        if (
            $override_id > 0
            && get_post_type(
                $override_id
            ) ===
            Jung_Leben_Core_Partners::POST_TYPE
        ) {
            return
                $override_id;
        }


        /* =====================================================
           MARKE
           ===================================================== */

        $brand =
            self::get_primary_brand(
                $product_id
            );


        if (
            ! $brand
            instanceof WP_Term
        ) {
            return
                0;
        }


        if (
            ! class_exists(
                Jung_Leben_Core_Brand_Fields::class
            )
        ) {
            return
                0;
        }


        $brand_data =
            Jung_Leben_Core_Brand_Fields::get_frontend_data(
                $brand
            );


        return
            isset(
                $brand_data[
                    'partner_id'
                ]
            )
                ? absint(
                    $brand_data[
                        'partner_id'
                    ]
                )
                : 0;
    }


    /* =========================================================
       KAUFDATEN
       ========================================================= */

    /**
     * Alle für das Frontend benötigten Kaufdaten ermitteln.
     *
     * @return array{
     *     product_id: int,
     *     url: string,
     *     url_source: string,
     *     button_text: string,
     *     new_tab: bool,
     *     rel: string,
     *     price_display: string,
     *     partner_id: int,
     *     partner_active: bool,
     *     partner: array<string, mixed>,
     *     brand: array<string, mixed>,
     *     show_offer: bool,
     *     offer_title: string,
     *     discount_text: string,
     *     discount_code: string,
     *     public_note: string
     * }
     */
    public static function get_purchase_data(
        int $product_id
    ): array {
        $product_id =
            absint(
                $product_id
            );


        $result = [
            'product_id' =>
                $product_id,

            'url' =>
                '',

            'url_source' =>
                'none',

            'button_text' =>
                __(
                    'Produkt ansehen',
                    'jung-leben-core'
                ),

            'new_tab' =>
                true,

            'rel' =>
                'noopener noreferrer external',

            'price_display' =>
                '',

            'partner_id' =>
                0,

            'partner_active' =>
                false,

            'partner' =>
                [],

            'brand' =>
                [],

            'show_offer' =>
                false,

            'offer_title' =>
                '',

            'discount_text' =>
                '',

            'discount_code' =>
                '',

            'public_note' =>
                '',
        ];


        if (
            $product_id <= 0
            || get_post_type(
                $product_id
            ) !==
            Jung_Leben_Core_Products::POST_TYPE
        ) {
            return
                $result;
        }


        /* =====================================================
           PRODUKTDATEN
           ===================================================== */

        $affiliate_url = '';

        $original_url = '';

        $button_override = '';

        $price_display = '';

        $product_discount_code = '';


        if (
            function_exists(
                'get_field'
            )
        ) {
            $affiliate_url =
                esc_url_raw(
                    trim(
                        (string)
                        get_field(
                            'jl_product_affiliate_url',
                            $product_id
                        )
                    )
                );


            $original_url =
                esc_url_raw(
                    trim(
                        (string)
                        get_field(
                            'jl_product_original_url',
                            $product_id
                        )
                    )
                );


            $button_override =
                trim(
                    (string)
                    get_field(
                        'jl_product_button_text',
                        $product_id
                    )
                );


            $price_display =
                trim(
                    (string)
                    get_field(
                        'jl_product_price_display',
                        $product_id
                    )
                );


            $product_discount_code =
                trim(
                    (string)
                    get_field(
                        'jl_product_discount_code',
                        $product_id
                    )
                );
        }


        /* =====================================================
           NEUER TAB
           ===================================================== */

        if (
            metadata_exists(
                'post',
                $product_id,
                'jl_product_link_new_tab'
            )
            && function_exists(
                'get_field'
            )
        ) {
            $new_tab =
                (bool)
                get_field(
                    'jl_product_link_new_tab',
                    $product_id
                );
        } else {
            $new_tab =
                true;
        }


        /* =====================================================
           PARTNERANGEBOT AUF PRODUKTSEITE
           ===================================================== */

        if (
            metadata_exists(
                'post',
                $product_id,
                'jl_product_use_partner_offer'
            )
            && function_exists(
                'get_field'
            )
        ) {
            $use_partner_offer =
                (bool)
                get_field(
                    'jl_product_use_partner_offer',
                    $product_id
                );
        } else {
            $use_partner_offer =
                true;
        }


        /* =====================================================
           MARKE
           ===================================================== */

        $brand =
            self::get_primary_brand(
                $product_id
            );


        $brand_data = [];


        if (
            $brand
            instanceof WP_Term
            && class_exists(
                Jung_Leben_Core_Brand_Fields::class
            )
        ) {
            $brand_data =
                Jung_Leben_Core_Brand_Fields::get_frontend_data(
                    $brand
                );
        }


        /* =====================================================
           PARTNER
           ===================================================== */

        $partner_id =
            self::get_partner_id(
                $product_id
            );


        $partner_data = [];


        if (
            $partner_id > 0
            && class_exists(
                Jung_Leben_Core_Partners::class
            )
        ) {
            $partner_data =
                Jung_Leben_Core_Partners::get_frontend_data(
                    $partner_id
                );
        }


        $partner_active =
            isset(
                $partner_data[
                    'status'
                ]
            )
            && $partner_data[
                'status'
            ] ===
            'active';


        $partner_model =
            isset(
                $partner_data[
                    'model'
                ]
            )
                ? sanitize_key(
                    (string)
                    $partner_data[
                        'model'
                    ]
                )
                : '';


        /* =====================================================
           ZIEL-URL
           ===================================================== */

        /**
         * 1. Produktspezifischer Affiliate-Link.
         */
        if (
            $affiliate_url !== ''
        ) {
            $result[
                'url'
            ] =
                $affiliate_url;

            $result[
                'url_source'
            ] =
                'product_affiliate';
        }


        /**
         * 2. Bei Rabattcode-Modellen darf direkt auf die
         *    konkrete Produktseite verlinkt werden.
         *
         * Beispiel: Luvy.
         */
        elseif (
            $partner_active
            && in_array(
                $partner_model,
                [
                    'discount',
                    'manual',
                ],
                true
            )
            && $original_url !== ''
        ) {
            $result[
                'url'
            ] =
                $original_url;

            $result[
                'url_source'
            ] =
                'product_direct';
        }


        /**
         * 3. Allgemeiner persönlicher Partnerlink.
         *
         * Beispiel: Haritaki-Fallback.
         */
        elseif (
            $partner_active
            && class_exists(
                Jung_Leben_Core_Partners::class
            )
        ) {
            $partner_shop_url =
                Jung_Leben_Core_Partners::get_default_shop_url(
                    $partner_id
                );


            if (
                $partner_shop_url !== ''
            ) {
                $result[
                    'url'
                ] =
                    $partner_shop_url;

                $result[
                    'url_source'
                ] =
                    'partner_shop';
            }
        }


        /**
         * 4. Direkte Produktseite ohne Partnertracking.
         */
        if (
            $result[
                'url'
            ] === ''
            && $original_url !== ''
        ) {
            $result[
                'url'
            ] =
                $original_url;

            $result[
                'url_source'
            ] =
                'product_direct';
        }


        /**
         * 5. Letzter Fallback: offizielle Markenwebsite.
         */
        if (
            $result[
                'url'
            ] === ''
            && isset(
                $brand_data[
                    'website'
                ]
            )
            && is_string(
                $brand_data[
                    'website'
                ]
            )
            && $brand_data[
                'website'
            ] !== ''
        ) {
            $result[
                'url'
            ] =
                esc_url_raw(
                    $brand_data[
                        'website'
                    ]
                );

            $result[
                'url_source'
            ] =
                'brand_website';
        }


        /* =====================================================
           BUTTON
           ===================================================== */

        if (
            $button_override !== ''
        ) {
            $button_text =
                $button_override;
        } elseif (
            isset(
                $partner_data[
                    'button_text'
                ]
            )
            && trim(
                (string)
                $partner_data[
                    'button_text'
                ]
            ) !== ''
        ) {
            $button_text =
                trim(
                    (string)
                    $partner_data[
                        'button_text'
                    ]
                );
        } else {
            $button_text =
                __(
                    'Produkt ansehen',
                    'jung-leben-core'
                );
        }


        /* =====================================================
           RABATTCODE
           ===================================================== */

        if (
            $product_discount_code !== ''
        ) {
            $discount_code =
                $product_discount_code;
        } elseif (
            isset(
                $partner_data[
                    'discount_code'
                ]
            )
        ) {
            $discount_code =
                trim(
                    (string)
                    $partner_data[
                        'discount_code'
                    ]
                );
        } else {
            $discount_code =
                '';
        }


        /* =====================================================
           ANGEBOT SICHTBAR?
           ===================================================== */

        $brand_allows_offer =
            ! isset(
                $brand_data[
                    'show_partner_offer'
                ]
            )
            || (bool)
            $brand_data[
                'show_partner_offer'
            ];


        $partner_public_offer =
            isset(
                $partner_data[
                    'public_offer'
                ]
            )
            && (bool)
            $partner_data[
                'public_offer'
            ];


        $show_offer =
            $partner_active
            && $use_partner_offer
            && $brand_allows_offer
            && $partner_public_offer;


        /* =====================================================
           REL-ATTRIBUTE
           ===================================================== */

        $commercial_link =
            $partner_active
            || $affiliate_url !== '';


        $rel =
            $commercial_link
                ? 'sponsored noopener noreferrer external'
                : 'noopener noreferrer external';


        /* =====================================================
           ERGEBNIS
           ===================================================== */

        $result[
            'button_text'
        ] =
            $button_text;


        $result[
            'new_tab'
        ] =
            $new_tab;


        $result[
            'rel'
        ] =
            $rel;


        $result[
            'price_display'
        ] =
            $price_display;


        $result[
            'partner_id'
        ] =
            $partner_id;


        $result[
            'partner_active'
        ] =
            $partner_active;


        $result[
            'partner'
        ] =
            $partner_data;


        $result[
            'brand'
        ] =
            $brand_data;


        $result[
            'show_offer'
        ] =
            $show_offer;


        $result[
            'offer_title'
        ] =
            isset(
                $partner_data[
                    'offer_title'
                ]
            )
                ? trim(
                    (string)
                    $partner_data[
                        'offer_title'
                    ]
                )
                : '';


        $result[
            'discount_text'
        ] =
            isset(
                $partner_data[
                    'discount_text'
                ]
            )
                ? trim(
                    (string)
                    $partner_data[
                        'discount_text'
                    ]
                )
                : '';


        $result[
            'discount_code'
        ] =
            $discount_code;


        $result[
            'public_note'
        ] =
            isset(
                $partner_data[
                    'public_note'
                ]
            )
                ? trim(
                    (string)
                    $partner_data[
                        'public_note'
                    ]
                )
                : '';


        return
            $result;
    }


    /* =========================================================
       ACF-HINWEIS
       ========================================================= */

    /**
     * Hinweis anzeigen, falls ACF nicht aktiv ist.
     */
    public static function show_acf_notice(): void
    {
        if (
            function_exists(
                'acf_add_local_field_group'
            )
            || ! current_user_can(
                'activate_plugins'
            )
        ) {
            return;
        }


        ?>
        <div class="notice notice-warning">
            <p>
                <?php
                echo esc_html__(
                    'Jung Leben Core benötigt das Plugin «Advanced Custom Fields», damit die strukturierten Produktinformationen bearbeitet werden können.',
                    'jung-leben-core'
                );
                ?>
            </p>
        </div>
        <?php
    }
}