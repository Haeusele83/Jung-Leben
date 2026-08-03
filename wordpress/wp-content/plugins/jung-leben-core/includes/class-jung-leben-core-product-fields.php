<?php
/**
 * ACF-Felder für Produkte.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Strukturierte Zusatzfelder für Produkte.
 */
final class Jung_Leben_Core_Product_Fields
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

        add_action(
            'admin_notices',
            [
                self::class,
                'show_acf_notice',
            ]
        );
    }

    /**
     * Produktfelder registrieren.
     */
    public static function register_fields(): void
    {
        if (! function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key'   => 'group_jung_leben_product',
            'title' => __(
                'Produktinformationen',
                'jung-leben-core'
            ),

            'fields' => [

                /*
                 * Redaktionelle Einordnung
                 */
                [
                    'key'       => 'field_jl_product_editorial_tab',
                    'label'     => __(
                        'Einordnung',
                        'jung-leben-core'
                    ),
                    'name'      => '',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'           => 'field_jl_product_recommendation_status',
                    'label'         => __(
                        'Empfehlungsstatus',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_recommendation_status',
                    'type'          => 'select',
                    'instructions'  => __(
                        'Redaktionelle Einordnung des Produkts auf Jung Leben.',
                        'jung-leben-core'
                    ),
                    'choices'       => [
                        'neutral' => __(
                            'Neutral vorgestellt',
                            'jung-leben-core'
                        ),
                        'interesting' => __(
                            'Interessante Option',
                            'jung-leben-core'
                        ),
                        'recommended' => __(
                            'Empfohlen',
                            'jung-leben-core'
                        ),
                        'favorite' => __(
                            'Persönlicher Favorit',
                            'jung-leben-core'
                        ),
                    ],
                    'default_value' => 'neutral',
                    'return_format' => 'value',
                    'allow_null'    => 0,
                    'multiple'      => 0,
                    'ui'            => 1,
                    'wrapper'       => [
                        'width' => '50',
                    ],
                ],
                [
                    'key'           => 'field_jl_product_personally_tested',
                    'label'         => __(
                        'Persönlich getestet',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_personally_tested',
                    'type'          => 'true_false',
                    'instructions'  => __(
                        'Aktivieren, wenn Roberto das Produkt persönlich verwendet oder getestet hat.',
                        'jung-leben-core'
                    ),
                    'default_value' => 0,
                    'ui'            => 1,
                    'ui_on_text'    => __(
                        'Ja',
                        'jung-leben-core'
                    ),
                    'ui_off_text'   => __(
                        'Nein',
                        'jung-leben-core'
                    ),
                    'wrapper'       => [
                        'width' => '25',
                    ],
                ],
                [
                    'key'           => 'field_jl_product_featured',
                    'label'         => __(
                        'Besonders hervorheben',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_featured',
                    'type'          => 'true_false',
                    'instructions'  => __(
                        'Das Produkt kann später auf Übersichtsseiten besonders hervorgehoben werden.',
                        'jung-leben-core'
                    ),
                    'default_value' => 0,
                    'ui'            => 1,
                    'ui_on_text'    => __(
                        'Ja',
                        'jung-leben-core'
                    ),
                    'ui_off_text'   => __(
                        'Nein',
                        'jung-leben-core'
                    ),
                    'wrapper'       => [
                        'width' => '25',
                    ],
                ],
                [
                    'key'           => 'field_jl_product_sort_priority',
                    'label'         => __(
                        'Sortierpriorität',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_sort_priority',
                    'type'          => 'number',
                    'instructions'  => __(
                        'Produkte mit einer höheren Zahl können später weiter oben angezeigt werden.',
                        'jung-leben-core'
                    ),
                    'default_value' => 0,
                    'min'           => 0,
                    'max'           => 999,
                    'step'          => 1,
                    'wrapper'       => [
                        'width' => '25',
                    ],
                ],

                /*
                 * Anwendung und Erfahrung
                 */
                [
                    'key'       => 'field_jl_product_experience_tab',
                    'label'     => __(
                        'Anwendung und Erfahrung',
                        'jung-leben-core'
                    ),
                    'name'      => '',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_jl_product_purpose',
                    'label'        => __(
                        'Möglicher Verwendungszweck',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_purpose',
                    'type'         => 'textarea',
                    'instructions' => __(
                        'Sachliche Beschreibung, wofür das Produkt eingesetzt oder betrachtet wird. Keine Heilversprechen verwenden.',
                        'jung-leben-core'
                    ),
                    'rows'         => 4,
                    'new_lines'    => '',
                ],
                [
                    'key'          => 'field_jl_product_routine_time',
                    'label'        => __(
                        'Mögliche Tageszeit',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_routine_time',
                    'type'         => 'checkbox',
                    'instructions' => __(
                        'Mehrere Angaben sind möglich.',
                        'jung-leben-core'
                    ),
                    'choices'      => [
                        'morning' => __(
                            'Morgens',
                            'jung-leben-core'
                        ),
                        'midday' => __(
                            'Mittags',
                            'jung-leben-core'
                        ),
                        'evening' => __(
                            'Abends',
                            'jung-leben-core'
                        ),
                        'flexible' => __(
                            'Zeitlich flexibel',
                            'jung-leben-core'
                        ),
                    ],
                    'return_format' => 'value',
                    'layout'        => 'horizontal',
                    'toggle'        => 0,
                ],
                [
                    'key'          => 'field_jl_product_personal_experience',
                    'label'        => __(
                        'Persönliche Erfahrung',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_personal_experience',
                    'type'         => 'textarea',
                    'instructions' => __(
                        'Persönliche und nachvollziehbare Erfahrung von Roberto. Beobachtungen klar als persönliche Erfahrung formulieren.',
                        'jung-leben-core'
                    ),
                    'rows'         => 6,
                    'new_lines'    => 'br',
                ],
                [
                    'key'          => 'field_jl_product_benefits',
                    'label'        => __(
                        'Positive Eigenschaften',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_benefits',
                    'type'         => 'textarea',
                    'instructions' => __(
                        'Die wichtigsten positiven Eigenschaften oder praktischen Vorteile. Pro Zeile kann ein Punkt erfasst werden.',
                        'jung-leben-core'
                    ),
                    'rows'         => 6,
                    'new_lines'    => '',
                ],
                [
                    'key'          => 'field_jl_product_limitations',
                    'label'        => __(
                        'Einschränkungen und Hinweise',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_limitations',
                    'type'         => 'textarea',
                    'instructions' => __(
                        'Mögliche Nachteile, Einschränkungen, Besonderheiten oder Gründe, weshalb das Produkt nicht für alle Personen geeignet sein könnte.',
                        'jung-leben-core'
                    ),
                    'rows'         => 6,
                    'new_lines'    => 'br',
                ],

                /*
                 * Produkt- und Kaufdaten
                 */
                [
                    'key'       => 'field_jl_product_purchase_tab',
                    'label'     => __(
                        'Produkt- und Kaufdaten',
                        'jung-leben-core'
                    ),
                    'name'      => '',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'          => 'field_jl_product_partner_name',
                    'label'        => __(
                        'Shop oder Partner',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_partner_name',
                    'type'         => 'text',
                    'instructions' => __(
                        'Zum Beispiel iHerb, Sunday Natural oder ein anderer Partnershop.',
                        'jung-leben-core'
                    ),
                    'maxlength'    => 120,
                    'wrapper'      => [
                        'width' => '50',
                    ],
                ],
                [
                    'key'          => 'field_jl_product_partner_product_id',
                    'label'        => __(
                        'Externe Produkt-ID',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_partner_product_id',
                    'type'         => 'text',
                    'instructions' => __(
                        'Optionale Artikelnummer oder Produkt-ID des Partners. Wird später für Schnittstellen und Produktfeeds verwendet.',
                        'jung-leben-core'
                    ),
                    'maxlength'    => 150,
                    'wrapper'      => [
                        'width' => '50',
                    ],
                ],
                [
                    'key'          => 'field_jl_product_original_url',
                    'label'        => __(
                        'Originale Produkt-URL',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_original_url',
                    'type'         => 'url',
                    'instructions' => __(
                        'Direkte Produktadresse beim Partnershop ohne Affiliate-Tracking.',
                        'jung-leben-core'
                    ),
                ],
                [
                    'key'          => 'field_jl_product_affiliate_url',
                    'label'        => __(
                        'Affiliate-Link',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_affiliate_url',
                    'type'         => 'url',
                    'instructions' => __(
                        'Kann vorerst leer bleiben. Der Tracking-Link wird nach der Partneranbindung ergänzt oder automatisch erzeugt.',
                        'jung-leben-core'
                    ),
                ],
                [
                    'key'          => 'field_jl_product_price_display',
                    'label'        => __(
                        'Preisanzeige',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_price_display',
                    'type'         => 'text',
                    'instructions' => __(
                        'Zum Beispiel «ca. CHF 24.90» oder «Preis beim Partner prüfen». Preise können sich ändern.',
                        'jung-leben-core'
                    ),
                    'maxlength'    => 100,
                    'wrapper'      => [
                        'width' => '50',
                    ],
                ],
                [
                    'key'          => 'field_jl_product_discount_code',
                    'label'        => __(
                        'Rabattcode',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_discount_code',
                    'type'         => 'text',
                    'instructions' => __(
                        'Nur einen offiziell freigegebenen und aktuell gültigen Rabattcode erfassen.',
                        'jung-leben-core'
                    ),
                    'maxlength'    => 80,
                    'wrapper'      => [
                        'width' => '50',
                    ],
                ],
                [
                    'key'           => 'field_jl_product_button_text',
                    'label'         => __(
                        'Text des Kaufbuttons',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_button_text',
                    'type'          => 'text',
                    'default_value' => 'Produkt beim Partner ansehen',
                    'maxlength'     => 80,
                    'wrapper'       => [
                        'width' => '50',
                    ],
                ],
                [
                    'key'           => 'field_jl_product_link_new_tab',
                    'label'         => __(
                        'Partnerlink in neuem Tab öffnen',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_link_new_tab',
                    'type'          => 'true_false',
                    'default_value' => 1,
                    'ui'            => 1,
                    'ui_on_text'    => __(
                        'Ja',
                        'jung-leben-core'
                    ),
                    'ui_off_text'   => __(
                        'Nein',
                        'jung-leben-core'
                    ),
                    'wrapper'       => [
                        'width' => '50',
                    ],
                ],

                /*
                 * Transparenz und Hinweise
                 */
                [
                    'key'       => 'field_jl_product_transparency_tab',
                    'label'     => __(
                        'Transparenz und Hinweise',
                        'jung-leben-core'
                    ),
                    'name'      => '',
                    'type'      => 'tab',
                    'placement' => 'top',
                ],
                [
                    'key'           => 'field_jl_product_affiliate_notice',
                    'label'         => __(
                        'Affiliate-Hinweis',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_affiliate_notice',
                    'type'          => 'textarea',
                    'default_value' =>
                        'Dieser Beitrag kann Affiliate-Links enthalten. Bei einem Kauf über einen solchen Link erhält Jung Leben möglicherweise eine Provision. Für dich entstehen keine zusätzlichen Kosten.',
                    'rows'          => 4,
                    'new_lines'     => '',
                ],
                [
                    'key'           => 'field_jl_product_health_notice',
                    'label'         => __(
                        'Gesundheitlicher Hinweis',
                        'jung-leben-core'
                    ),
                    'name'          => 'jl_product_health_notice',
                    'type'          => 'textarea',
                    'default_value' =>
                        'Die Informationen auf Jung Leben ersetzen keine medizinische Beratung, Diagnose oder Behandlung. Nahrungsergänzungsmittel sind kein Ersatz für eine ausgewogene Ernährung und eine gesunde Lebensweise. Bei gesundheitlichen Fragen oder Unsicherheiten sollte fachlicher Rat eingeholt werden.',
                    'rows'          => 5,
                    'new_lines'     => '',
                ],
                [
                    'key'          => 'field_jl_product_source_note',
                    'label'        => __(
                        'Interne Quellen- und Prüfnotiz',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_source_note',
                    'type'         => 'textarea',
                    'instructions' => __(
                        'Interne Notizen zu Herstellerangaben, Quellen, Prüfdatum oder offenen Fragen. Dieses Feld wird später nicht öffentlich angezeigt.',
                        'jung-leben-core'
                    ),
                    'rows'         => 5,
                    'new_lines'    => '',
                ],
                [
                    'key'          => 'field_jl_product_last_checked',
                    'label'        => __(
                        'Zuletzt geprüft am',
                        'jung-leben-core'
                    ),
                    'name'         => 'jl_product_last_checked',
                    'type'         => 'date_picker',
                    'instructions' => __(
                        'Datum der letzten inhaltlichen oder preislichen Prüfung.',
                        'jung-leben-core'
                    ),
                    'display_format' => 'd.m.Y',
                    'return_format'  => 'Y-m-d',
                    'first_day'      => 1,
                    'wrapper'        => [
                        'width' => '50',
                    ],
                ],
            ],

            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    =>
                            Jung_Leben_Core_Products::POST_TYPE,
                    ],
                ],
            ],

            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'show_in_rest'          => 0,
        ]);
    }

    /**
     * Hinweis anzeigen, falls ACF nicht aktiv ist.
     */
    public static function show_acf_notice(): void
    {
        if (
            function_exists('acf_add_local_field_group')
            || ! current_user_can('activate_plugins')
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