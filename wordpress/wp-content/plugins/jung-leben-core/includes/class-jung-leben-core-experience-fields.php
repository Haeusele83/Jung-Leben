<?php
/**
 * ACF-Felder für Jung-Leben-Erfahrungen.
 *
 * Normale WordPress-Beiträge werden als
 * Erfahrungs- und Ratgeberbeiträge verwendet.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * Erfahrungsfelder und Backend-Erweiterungen.
 */
final class Jung_Leben_Core_Experience_Fields
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

        add_filter(
            'manage_post_posts_columns',
            [
                self::class,
                'add_admin_columns',
            ]
        );

        add_action(
            'manage_post_posts_custom_column',
            [
                self::class,
                'render_admin_columns',
            ],
            10,
            2
        );

        add_action(
            'admin_notices',
            [
                self::class,
                'show_review_notice',
            ]
        );
    }


    /* =====================================================
       ACF-FELDER
       ===================================================== */

    /**
     * Zusätzliche Felder für normale Beiträge.
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
                'group_jung_leben_experience',

            'title' =>
                __(
                    'Erfahrung – Redaktion',
                    'jung-leben-core'
                ),

            'fields' => [

                /* =========================================
                   PRÜFSTATUS
                   ========================================= */

                [
                    'key' =>
                        'field_jl_experience_review_status',

                    'label' =>
                        __(
                            'Prüfstatus',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_experience_review_status',

                    'type' =>
                        'select',

                    'instructions' =>
                        __(
                            'Kennzeichnet, ob der Inhalt bereits redaktionell geprüft wurde.',
                            'jung-leben-core'
                        ),

                    'choices' => [

                        'customer_source' =>
                            __(
                                'Kundentext aus Excel – prüfen',
                                'jung-leben-core'
                            ),

                        'ai_review' =>
                            __(
                                'KI-generiert – muss geprüft werden',
                                'jung-leben-core'
                            ),

                        'reviewed' =>
                            __(
                                'Geprüft / freigegeben',
                                'jung-leben-core'
                            ),
                    ],

                    'default_value' =>
                        'customer_source',

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


                /* =========================================
                   PRODUKT
                   ========================================= */

                [
                    'key' =>
                        'field_jl_experience_related_product',

                    'label' =>
                        __(
                            'Zugehöriges Produkt',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_experience_related_product',

                    'type' =>
                        'post_object',

                    'instructions' =>
                        __(
                            'Verknüpft diese Erfahrung mit dem passenden Produkt.',
                            'jung-leben-core'
                        ),

                    'post_type' => [
                        Jung_Leben_Core_Products::POST_TYPE,
                    ],

                    'post_status' => [
                        'publish',
                        'draft',
                        'pending',
                        'private',
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


                /* =========================================
                   QUELLTHEMA
                   ========================================= */

                [
                    'key' =>
                        'field_jl_experience_source_topic',

                    'label' =>
                        __(
                            'Thema aus Kundendatei',
                            'jung-leben-core'
                        ),

                    'name' =>
                        'jl_experience_source_topic',

                    'type' =>
                        'text',

                    'instructions' =>
                        __(
                            'Ursprüngliche Bezeichnung aus der Tabelle BLOGS.',
                            'jung-leben-core'
                        ),

                    'readonly' =>
                        1,
                ],
            ],


            /* =============================================
               NUR NORMALE WORDPRESS-BEITRÄGE
               ============================================= */

            'location' => [
                [
                    [
                        'param' =>
                            'post_type',

                        'operator' =>
                            '==',

                        'value' =>
                            'post',
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


    /* =====================================================
       BACKEND-SPALTEN
       ===================================================== */

    /**
     * Zusätzliche Spalten bei
     * Beiträge → Alle Beiträge.
     */
    public static function add_admin_columns(
        array $columns
    ): array {
        $new_columns = [];

        foreach (
            $columns as $key => $label
        ) {
            $new_columns[
                $key
            ] = $label;

            if (
                $key === 'title'
            ) {
                $new_columns[
                    'jl_experience_review_status'
                ] = __(
                    'Prüfstatus',
                    'jung-leben-core'
                );

                $new_columns[
                    'jl_experience_product'
                ] = __(
                    'Produkt',
                    'jung-leben-core'
                );
            }
        }

        return $new_columns;
    }


    /**
     * Inhalt der zusätzlichen Backend-Spalten.
     */
    public static function render_admin_columns(
        string $column,
        int $post_id
    ): void {
        /* =========================================
           PRÜFSTATUS
           ========================================= */

        if (
            $column ===
            'jl_experience_review_status'
        ) {
            $status = (string) get_post_meta(
                $post_id,
                'jl_experience_review_status',
                true
            );

            $labels = [

                'customer_source' =>
                    __(
                        'Kundentext – prüfen',
                        'jung-leben-core'
                    ),

                'ai_review' =>
                    __(
                        'KI-generiert – prüfen',
                        'jung-leben-core'
                    ),

                'reviewed' =>
                    __(
                        'Geprüft',
                        'jung-leben-core'
                    ),
            ];

            echo esc_html(
                $labels[
                    $status
                ]
                ?? '—'
            );

            return;
        }


        /* =========================================
           PRODUKT
           ========================================= */

        if (
            $column ===
            'jl_experience_product'
        ) {
            $product_id = (int) get_post_meta(
                $post_id,
                'jl_experience_related_product',
                true
            );

            if (
                $product_id <= 0
            ) {
                echo '—';

                return;
            }

            $title =
                get_the_title(
                    $product_id
                );

            $edit_link =
                get_edit_post_link(
                    $product_id
                );

            if ($edit_link) {
                printf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url(
                        $edit_link
                    ),
                    esc_html(
                        $title
                    )
                );

                return;
            }

            echo esc_html(
                $title
            );
        }
    }


    /* =====================================================
       WARNUNG BEI KI-TEXTEN
       ===================================================== */

    /**
     * Bei noch nicht geprüften KI-Texten
     * einen Warnhinweis im Beitragseditor anzeigen.
     */
    public static function show_review_notice(): void
    {
        if (
            ! is_admin()
        ) {
            return;
        }


        $screen =
            get_current_screen();

        if (
            ! $screen
            || $screen->base !== 'post'
            || $screen->post_type !== 'post'
        ) {
            return;
        }


        $post_id =
            isset(
                $_GET['post']
            )
                ? absint(
                    $_GET['post']
                )
                : 0;

        if (
            $post_id <= 0
        ) {
            return;
        }


        $status = (string) get_post_meta(
            $post_id,
            'jl_experience_review_status',
            true
        );

        if (
            $status !==
            'ai_review'
        ) {
            return;
        }

        ?>

        <div
            class="
                notice
                notice-warning
            "
        >

            <p>

                <strong>
                    <?php
                    esc_html_e(
                        'Jung Leben: Dieser Inhalt wurde in der Kundendatei als KI-generiert markiert.',
                        'jung-leben-core'
                    );
                    ?>
                </strong>

                <?php
                esc_html_e(
                    'Bitte den gesamten Beitrag und insbesondere gesundheitsbezogene Aussagen vor der Veröffentlichung prüfen. Anschliessend kann der Prüfstatus auf «Geprüft / freigegeben» gesetzt werden.',
                    'jung-leben-core'
                );
                ?>

            </p>

        </div>

        <?php
    }
}