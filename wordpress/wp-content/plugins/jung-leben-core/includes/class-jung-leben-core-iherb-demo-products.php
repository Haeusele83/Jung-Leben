<?php
/**
 * Assistent für die iHerb-Partner-Demo.
 *
 * Erstellt echte, aktuell bei iHerb gelistete
 * Produkte als geschützte Jung-Leben-Demoentwürfe.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/**
 * iHerb-Demo-Produkte.
 */
final class Jung_Leben_Core_IHerb_Demo_Products
{
    /**
     * Admin-Seite.
     */
    private const PAGE_SLUG =
        'jung-leben-iherb-demo-products';


    /**
     * Formular-Action.
     */
    private const ACTION_CREATE =
        'jung_leben_create_iherb_demo_products';


    /**
     * Demo-Partner.
     */
    private const PARTNER_NAME =
        'iHerb';


    /**
     * Demo-Partner-Slug.
     */
    private const PARTNER_SLUG =
        'iherb';


    /**
     * Meta-Feld zur eindeutigen Kennzeichnung
     * eines Demo-Produkts.
     */
    private const META_DEMO_ONLY =
        '_jl_partner_demo_only';


    /**
     * Partnerzuordnung.
     */
    private const META_DEMO_PARTNER =
        '_jl_partner_demo_partner';


    /**
     * Externe Produkt-ID.
     */
    private const META_SOURCE_ID =
        '_jl_iherb_demo_product_id';


    /* =========================================================
       INITIALISIERUNG
       ========================================================= */

    /**
     * Hooks registrieren.
     */
    public static function init(): void
    {
        add_action(
            'admin_menu',
            [
                self::class,
                'register_admin_page',
            ]
        );


        add_action(
            'admin_post_'
                . self::ACTION_CREATE,
            [
                self::class,
                'handle_create',
            ]
        );
    }


    /* =========================================================
       PRODUKTDEFINITIONEN
       ========================================================= */

    /**
     * Echte iHerb-Produkte für die Demo.
     *
     * Die redaktionellen Jung-Leben-Texte sind
     * bewusst eigenständig formuliert.
     *
     * Preise werden nicht importiert, da diese
     * dynamisch sind.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function get_products(): array
    {
        return [

            /* =================================================
               MAGNESIUM
               ================================================= */

            [
                'source_id' =>
                    '103273',

                'title' =>
                    'Magnesium Bisglycinate Chelate, Albion TRAACS®',

                'brand' =>
                    'California Gold Nutrition',

                'original_url' =>
                    'https://ch.iherb.com/pr/california-gold-nutrition-magnesium-bisglycinate-chelate-albion-traacs-60-veggie-capsules-100-mg-per-capsule/103273',

                'categories' => [
                    'Schlaf & Regeneration',
                    'Vitamine & Mineralstoffe',
                ],

                'routines' => [
                    'evening',
                ],

                'excerpt' =>
                    'Magnesiumbisglycinat in Chelatform als Beispiel für eine klar eingeordnete Mineralstoff-Empfehlung innerhalb einer bewussten Abendroutine.',

                'purpose' =>
                    'Dieses Produkt zeigt beispielhaft, wie ein Magnesiumprodukt auf Jung Leben den Bereichen Mineralstoffversorgung sowie Schlaf und Regeneration zugeordnet werden kann.',

                'priority' =>
                    30,
            ],


            /* =================================================
               OMEGA-3
               ================================================= */

            [
                'source_id' =>
                    '109322',

                'title' =>
                    'Omega-3 Fish Oil, Triple Strength',

                'brand' =>
                    'Sports Research',

                'original_url' =>
                    'https://ch.iherb.com/pr/sports-research-omega-3-fish-oil-triple-strength-120-softgels/109322',

                'categories' => [
                    'Zellgesundheit & Longevity',
                ],

                'routines' => [
                    'flexible',
                ],

                'excerpt' =>
                    'Ein konzentriertes Omega-3-Produkt als Beispiel für die redaktionelle Einordnung eines klassischen Longevity-Themas.',

                'purpose' =>
                    'Dieses Produkt zeigt beispielhaft, wie Omega-3 auf Jung Leben innerhalb des Themenbereichs Zellgesundheit und Longevity eingeordnet werden kann, ohne eine fixe Tageszeit vorzugeben.',

                'priority' =>
                    20,
            ],


            /* =================================================
               ASHWAGANDHA
               ================================================= */

            [
                'source_id' =>
                    '310',

                'title' =>
                    'Standardized Extract Ashwagandha, 450 mg',

                'brand' =>
                    'NOW Foods',

                'original_url' =>
                    'https://ch.iherb.com/pr/now-foods-ashwagandha-standardized-extract-450-mg-90-veg-capsules/310',

                'categories' => [
                    'Stress & Balance',
                    'Pflanzenstoffe',
                ],

                'routines' => [
                    'evening',
                ],

                'excerpt' =>
                    'Standardisierter Ashwagandha-Extrakt als Beispiel für die Verbindung von Pflanzenstoffen, Balance und einer bewussten Abendroutine.',

                'purpose' =>
                    'Dieses Produkt zeigt beispielhaft, wie Ashwagandha auf Jung Leben redaktionell in die Themen Stress, Balance und Pflanzenstoffe eingeordnet und mit einer Abendroutine verbunden werden kann.',

                'priority' =>
                    10,
            ],
        ];
    }


    /* =========================================================
       ADMIN-SEITE
       ========================================================= */

    /**
     * Werkzeug registrieren.
     */
    public static function register_admin_page(): void
    {
        add_management_page(
            __(
                'iHerb Demo-Produkte',
                'jung-leben-core'
            ),
            __(
                'iHerb Demo-Produkte',
                'jung-leben-core'
            ),
            'manage_options',
            self::PAGE_SLUG,
            [
                self::class,
                'render_admin_page',
            ]
        );
    }


    /**
     * Admin-Seite ausgeben.
     */
    public static function render_admin_page(): void
    {
        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diese Seite.',
                    'jung-leben-core'
                )
            );
        }


        $definitions =
            self::get_products();


        $rows = [];

        $created_count = 0;

        $available_count = 0;

        $conflict_count = 0;


        foreach (
            $definitions
            as $definition
        ) {
            $existing_demo =
                self::find_existing_demo_product(
                    (string)
                    $definition[
                        'source_id'
                    ]
                );


            if (
                $existing_demo
                instanceof WP_Post
            ) {
                $status =
                    'created';

                $created_count++;
            } else {
                $conflict =
                    self::find_existing_product_by_title(
                        (string)
                        $definition[
                            'title'
                        ]
                    );


                if (
                    $conflict
                    instanceof WP_Post
                ) {
                    $status =
                        'conflict';

                    $conflict_count++;
                } else {
                    $status =
                        'available';

                    $available_count++;
                }
            }


            $rows[] = [
                'definition' =>
                    $definition,

                'status' =>
                    $status,

                'existing' =>
                    $existing_demo
                    ??
                    $conflict
                    ??
                    null,
            ];
        }


        $result =
            isset(
                $_GET[
                    'jl_result'
                ]
            )
                ? sanitize_key(
                    wp_unslash(
                        $_GET[
                            'jl_result'
                        ]
                    )
                )
                : '';


        $created =
            isset(
                $_GET[
                    'jl_created'
                ]
            )
                ? absint(
                    $_GET[
                        'jl_created'
                    ]
                )
                : 0;


        $skipped =
            isset(
                $_GET[
                    'jl_skipped'
                ]
            )
                ? absint(
                    $_GET[
                        'jl_skipped'
                    ]
                )
                : 0;
        ?>

        <div class="wrap">

            <h1>
                <?php
                esc_html_e(
                    'iHerb Demo-Produkte',
                    'jung-leben-core'
                );
                ?>
            </h1>


            <p style="max-width:850px;">
                <?php
                esc_html_e(
                    'Dieses Werkzeug erstellt drei echte, bei iHerb gelistete Produkte als geschützte Demo-Entwürfe. Die Produkte werden nicht als öffentliche Empfehlungen veröffentlicht.',
                    'jung-leben-core'
                );
                ?>
            </p>


            <div
                class="notice notice-info"
                style="
                    max-width:850px;
                    margin:22px 0;
                "
            >

                <p>
                    <strong>
                        <?php
                        esc_html_e(
                            'Wichtig:',
                            'jung-leben-core'
                        );
                        ?>
                    </strong>

                    <?php
                    esc_html_e(
                        'Es werden keine iHerb-Produkttexte oder Produktbilder kopiert. Verwendet werden nur Produktname, Hersteller, iHerb-Produkt-ID und die normale Produkt-URL. Die Jung-Leben-Texte sind eigenständig formuliert.',
                        'jung-leben-core'
                    );
                    ?>
                </p>

            </div>


            <?php
            if (
                $result ===
                'success'
            ) :
                ?>

                <div
                    class="
                        notice
                        notice-success
                        is-dismissible
                    "
                >

                    <p>
                        <?php
                        printf(
                            esc_html__(
                                '%1$d Demo-Produkte wurden erstellt. %2$d Produkte wurden übersprungen.',
                                'jung-leben-core'
                            ),
                            $created,
                            $skipped
                        );
                        ?>
                    </p>

                </div>

            <?php elseif (
                $result ===
                'error'
            ) : ?>

                <div class="notice notice-error">

                    <p>
                        <?php
                        esc_html_e(
                            'Bei der Erstellung ist mindestens ein Fehler aufgetreten.',
                            'jung-leben-core'
                        );
                        ?>
                    </p>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 STATISTIK
                 ================================================= -->

            <div
                style="
                    display:grid;
                    grid-template-columns:
                        repeat(
                            auto-fit,
                            minmax(
                                180px,
                                1fr
                            )
                        );
                    gap:16px;
                    max-width:850px;
                    margin:28px 0;
                "
            >

                <?php
                self::render_stat_box(
                    __(
                        'Demo-Produkte',
                        'jung-leben-core'
                    ),
                    count(
                        $definitions
                    )
                );


                self::render_stat_box(
                    __(
                        'Bereits erstellt',
                        'jung-leben-core'
                    ),
                    $created_count
                );


                self::render_stat_box(
                    __(
                        'Bereit',
                        'jung-leben-core'
                    ),
                    $available_count
                );


                self::render_stat_box(
                    __(
                        'Namenskonflikt',
                        'jung-leben-core'
                    ),
                    $conflict_count
                );
                ?>

            </div>


            <!-- =================================================
                 PRODUKTLISTE
                 ================================================= -->

            <table
                class="
                    widefat
                    striped
                "
                style="
                    max-width:1150px;
                "
            >

                <thead>

                    <tr>

                        <th>
                            <?php
                            esc_html_e(
                                'Produkt',
                                'jung-leben-core'
                            );
                            ?>
                        </th>


                        <th>
                            <?php
                            esc_html_e(
                                'Hersteller',
                                'jung-leben-core'
                            );
                            ?>
                        </th>


                        <th>
                            <?php
                            esc_html_e(
                                'iHerb-ID',
                                'jung-leben-core'
                            );
                            ?>
                        </th>


                        <th>
                            <?php
                            esc_html_e(
                                'Einordnung',
                                'jung-leben-core'
                            );
                            ?>
                        </th>


                        <th>
                            <?php
                            esc_html_e(
                                'Status',
                                'jung-leben-core'
                            );
                            ?>
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php
                    foreach (
                        $rows
                        as $row
                    ) :
                        $definition =
                            $row[
                                'definition'
                            ];

                        $status =
                            $row[
                                'status'
                            ];
                        ?>

                        <tr>

                            <td>

                                <strong>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        $definition[
                                            'title'
                                        ]
                                    );
                                    ?>
                                </strong>


                                <br>


                                <a
                                    href="<?php
                                    echo esc_url(
                                        (string)
                                        $definition[
                                            'original_url'
                                        ]
                                    );
                                    ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <?php
                                    esc_html_e(
                                        'iHerb-Produktseite öffnen ↗',
                                        'jung-leben-core'
                                    );
                                    ?>
                                </a>

                            </td>


                            <td>
                                <?php
                                echo esc_html(
                                    (string)
                                    $definition[
                                        'brand'
                                    ]
                                );
                                ?>
                            </td>


                            <td>
                                <code>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        $definition[
                                            'source_id'
                                        ]
                                    );
                                    ?>
                                </code>
                            </td>


                            <td>
                                <?php
                                echo esc_html(
                                    implode(
                                        ', ',
                                        $definition[
                                            'categories'
                                        ]
                                    )
                                );
                                ?>
                            </td>


                            <td>

                                <?php
                                if (
                                    $status ===
                                    'created'
                                ) :
                                    ?>

                                    <span
                                        style="
                                            color:#227447;
                                            font-weight:600;
                                        "
                                    >
                                        <?php
                                        esc_html_e(
                                            'Erstellt',
                                            'jung-leben-core'
                                        );
                                        ?>
                                    </span>

                                <?php elseif (
                                    $status ===
                                    'conflict'
                                ) : ?>

                                    <span
                                        style="
                                            color:#b32d2e;
                                            font-weight:600;
                                        "
                                    >
                                        <?php
                                        esc_html_e(
                                            'Prüfen',
                                            'jung-leben-core'
                                        );
                                        ?>
                                    </span>

                                <?php else : ?>

                                    <span
                                        style="
                                            color:#8a6300;
                                            font-weight:600;
                                        "
                                    >
                                        <?php
                                        esc_html_e(
                                            'Bereit',
                                            'jung-leben-core'
                                        );
                                        ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>


            <!-- =================================================
                 AKTION
                 ================================================= -->

            <?php
            if (
                $available_count > 0
            ) :
                ?>

                <div
                    class="card"
                    style="
                        max-width:850px;
                        margin-top:28px;
                        padding:22px 24px;
                    "
                >

                    <h2 style="margin-top:0;">
                        <?php
                        esc_html_e(
                            'Demo-Produkte erstellen',
                            'jung-leben-core'
                        );
                        ?>
                    </h2>


                    <p>
                        <?php
                        esc_html_e(
                            'Die Produkte werden als Entwürfe angelegt, mit iHerb als Demo-Partner gekennzeichnet und automatisch aus dem normalen öffentlichen Produktkatalog ausgeschlossen.',
                            'jung-leben-core'
                        );
                        ?>
                    </p>


                    <form
                        method="post"
                        action="<?php
                        echo esc_url(
                            admin_url(
                                'admin-post.php'
                            )
                        );
                        ?>"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="<?php
                            echo esc_attr(
                                self::ACTION_CREATE
                            );
                            ?>"
                        >


                        <?php
                        wp_nonce_field(
                            self::ACTION_CREATE
                        );
                        ?>


                        <?php
                        submit_button(
                            __(
                                '3 iHerb-Demo-Produkte anlegen',
                                'jung-leben-core'
                            ),
                            'primary',
                            'submit',
                            false
                        );
                        ?>

                    </form>

                </div>

            <?php else : ?>

                <p
                    style="
                        margin-top:25px;
                    "
                >
                    <strong>
                        <?php
                        esc_html_e(
                            'Aktuell sind keine weiteren Demo-Produkte anzulegen.',
                            'jung-leben-core'
                        );
                        ?>
                    </strong>
                </p>

            <?php endif; ?>

        </div>

        <?php
    }


    /* =========================================================
       STATISTIK
       ========================================================= */

    /**
     * Statistik-Karte.
     */
    private static function render_stat_box(
        string $label,
        int $value
    ): void {
        ?>

        <div
            style="
                background:#fff;
                border:1px solid #dcdcde;
                border-radius:6px;
                padding:20px;
            "
        >

            <strong
                style="
                    display:block;
                    margin-bottom:8px;
                    font-size:30px;
                    line-height:1;
                "
            >
                <?php
                echo esc_html(
                    (string)
                    $value
                );
                ?>
            </strong>


            <span style="color:#646970;">
                <?php
                echo esc_html(
                    $label
                );
                ?>
            </span>

        </div>

        <?php
    }


    /* =========================================================
       FORMULAR
       ========================================================= */

    /**
     * Produkte erstellen.
     */
    public static function handle_create(): void
    {
        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diese Aktion.',
                    'jung-leben-core'
                )
            );
        }


        check_admin_referer(
            self::ACTION_CREATE
        );


        /**
         * iHerb als Demo-Partner sicherstellen.
         */
        $partner_term =
            self::ensure_partner_term();


        if (
            is_wp_error(
                $partner_term
            )
        ) {
            self::redirect(
                'error',
                0,
                0
            );
        }


        $created = 0;

        $skipped = 0;

        $has_error = false;


        foreach (
            self::get_products()
            as $definition
        ) {
            $source_id =
                (string)
                $definition[
                    'source_id'
                ];


            /**
             * Bereits durch diesen Assistenten
             * angelegt.
             */
            if (
                self::find_existing_demo_product(
                    $source_id
                )
                instanceof WP_Post
            ) {
                $skipped++;

                continue;
            }


            /**
             * Ein bestehendes echtes Produkt mit
             * demselben Namen niemals überschreiben.
             */
            if (
                self::find_existing_product_by_title(
                    (string)
                    $definition[
                        'title'
                    ]
                )
                instanceof WP_Post
            ) {
                $skipped++;

                continue;
            }


            $result =
                self::create_product(
                    $definition
                );


            if (
                is_wp_error(
                    $result
                )
            ) {
                $has_error = true;

                continue;
            }


            $created++;
        }


        self::redirect(
            $has_error
                ? 'error'
                : 'success',
            $created,
            $skipped
        );
    }


    /* =========================================================
       PRODUKT ERSTELLEN
       ========================================================= */

    /**
     * Einzelnes Demo-Produkt erstellen.
     *
     * @param array<string, mixed> $definition
     *
     * @return int|WP_Error
     */
    private static function create_product(
        array $definition
    ): int|WP_Error {
        $post_id =
            wp_insert_post(
                [
                    'post_type' =>
                        Jung_Leben_Core_Products::POST_TYPE,

                    'post_status' =>
                        'draft',

                    'post_title' =>
                        (string)
                        $definition[
                            'title'
                        ],

                    'post_excerpt' =>
                        (string)
                        $definition[
                            'excerpt'
                        ],

                    'post_content' =>
                        '',
                ],
                true
            );


        if (
            is_wp_error(
                $post_id
            )
        ) {
            return $post_id;
        }


        /* =====================================================
           DEMO-META
           ===================================================== */

        update_post_meta(
            $post_id,
            self::META_DEMO_ONLY,
            '1'
        );


        update_post_meta(
            $post_id,
            self::META_DEMO_PARTNER,
            self::PARTNER_SLUG
        );


        update_post_meta(
            $post_id,
            self::META_SOURCE_ID,
            (string)
            $definition[
                'source_id'
            ]
        );


        /* =====================================================
           HERSTELLER
           ===================================================== */

        $brand_id =
            self::get_or_create_term(
                (string)
                $definition[
                    'brand'
                ],
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        if (
            is_wp_error(
                $brand_id
            )
        ) {
            return $brand_id;
        }


        wp_set_object_terms(
            $post_id,
            [
                $brand_id,
            ],
            Jung_Leben_Core_Products::TAXONOMY_BRAND,
            false
        );


        /* =====================================================
           KATEGORIEN
           ===================================================== */

        $category_ids = [];


        foreach (
            $definition[
                'categories'
            ]
            as $category_name
        ) {
            $category_id =
                self::get_or_create_term(
                    (string)
                    $category_name,
                    Jung_Leben_Core_Products::TAXONOMY_CATEGORY
                );


            if (
                is_wp_error(
                    $category_id
                )
            ) {
                return $category_id;
            }


            $category_ids[] =
                $category_id;
        }


        wp_set_object_terms(
            $post_id,
            $category_ids,
            Jung_Leben_Core_Products::TAXONOMY_CATEGORY,
            false
        );


        /* =====================================================
           PRODUKTFELDER
           ===================================================== */

        self::update_product_field(
            $post_id,
            'jl_product_recommendation_status',
            'interesting'
        );


        self::update_product_field(
            $post_id,
            'jl_product_personally_tested',
            false
        );


        self::update_product_field(
            $post_id,
            'jl_product_featured',
            false
        );


        self::update_product_field(
            $post_id,
            'jl_product_sort_priority',
            (int)
            $definition[
                'priority'
            ]
        );


        self::update_product_field(
            $post_id,
            'jl_product_purpose',
            (string)
            $definition[
                'purpose'
            ]
        );


        self::update_product_field(
            $post_id,
            'jl_product_routine_time',
            $definition[
                'routines'
            ]
        );


        /* =====================================================
           KAUF-/PARTNERDATEN
           ===================================================== */

        self::update_product_field(
            $post_id,
            'jl_product_partner_name',
            self::PARTNER_NAME
        );


        self::update_product_field(
            $post_id,
            'jl_product_partner_product_id',
            (string)
            $definition[
                'source_id'
            ]
        );


        self::update_product_field(
            $post_id,
            'jl_product_original_url',
            (string)
            $definition[
                'original_url'
            ]
        );


        /**
         * Noch keine Affiliate-Zulassung.
         */
        self::update_product_field(
            $post_id,
            'jl_product_affiliate_url',
            ''
        );


        self::update_product_field(
            $post_id,
            'jl_product_price_display',
            ''
        );


        self::update_product_field(
            $post_id,
            'jl_product_discount_code',
            ''
        );


        self::update_product_field(
            $post_id,
            'jl_product_button_text',
            __(
                'Bei iHerb ansehen',
                'jung-leben-core'
            )
        );


        self::update_product_field(
            $post_id,
            'jl_product_link_new_tab',
            true
        );


        /* =====================================================
           TRANSPARENZ
           ===================================================== */

        self::update_product_field(
            $post_id,
            'jl_product_health_notice',
            __(
                'Die Informationen auf Jung Leben dienen der allgemeinen Orientierung und ersetzen keine medizinische Beratung.',
                'jung-leben-core'
            )
        );


        self::update_product_field(
            $post_id,
            'jl_product_internal_source_note',
            sprintf(
                __(
                    'Geschütztes iHerb-Demo-Produkt. iHerb Produkt-ID: %1$s. Produktseite am 21.08.2026 für die Partner-Demo geprüft. Produkttexte auf Jung Leben eigenständig formuliert. Verfügbarkeit und Produktdaten vor einer späteren öffentlichen Nutzung erneut prüfen.',
                    'jung-leben-core'
                ),
                (string)
                $definition[
                    'source_id'
                ]
            )
        );


        self::update_product_field(
            $post_id,
            'jl_product_last_checked',
            '2026-08-21'
        );


        return
            $post_id;
    }


    /* =========================================================
       ACF / META
       ========================================================= */

    /**
     * Produktfeld speichern.
     */
    private static function update_product_field(
        int $post_id,
        string $field_name,
        mixed $value
    ): void {
        if (
            function_exists(
                'update_field'
            )
        ) {
            update_field(
                $field_name,
                $value,
                $post_id
            );

            return;
        }


        update_post_meta(
            $post_id,
            $field_name,
            $value
        );
    }


    /* =========================================================
       TERMS
       ========================================================= */

    /**
     * Taxonomie-Term holen oder erstellen.
     *
     * @return int|WP_Error
     */
    private static function get_or_create_term(
        string $name,
        string $taxonomy
    ): int|WP_Error {
        $existing =
            term_exists(
                $name,
                $taxonomy
            );


        if (
            is_array(
                $existing
            )
            && isset(
                $existing[
                    'term_id'
                ]
            )
        ) {
            return
                (int)
                $existing[
                    'term_id'
                ];
        }


        if (
            is_int(
                $existing
            )
        ) {
            return $existing;
        }


        $created =
            wp_insert_term(
                $name,
                $taxonomy
            );


        if (
            is_wp_error(
                $created
            )
        ) {
            return $created;
        }


        return
            (int)
            $created[
                'term_id'
            ];
    }


    /* =========================================================
       IHERB PARTNER-TERM
       ========================================================= */

    /**
     * iHerb als Partner-Konfiguration sicherstellen.
     *
     * iHerb wird NICHT den Demo-Produkten als
     * Hersteller-Marke zugewiesen.
     *
     * @return WP_Term|WP_Error
     */
    private static function ensure_partner_term(): WP_Term|WP_Error
    {
        $taxonomy =
            Jung_Leben_Core_Products::TAXONOMY_BRAND;


        $term =
            get_term_by(
                'slug',
                self::PARTNER_SLUG,
                $taxonomy
            );


        if (
            ! $term
            instanceof WP_Term
        ) {
            $created =
                wp_insert_term(
                    self::PARTNER_NAME,
                    $taxonomy,
                    [
                        'slug' =>
                            self::PARTNER_SLUG,
                    ]
                );


            if (
                is_wp_error(
                    $created
                )
            ) {
                return $created;
            }


            $term =
                get_term(
                    (int)
                    $created[
                        'term_id'
                    ],
                    $taxonomy
                );
        }


        if (
            ! $term
            instanceof WP_Term
        ) {
            return
                new WP_Error(
                    'jl_iherb_term_error',
                    __(
                        'iHerb konnte nicht als Demo-Partner angelegt werden.',
                        'jung-leben-core'
                    )
                );
        }


        /**
         * Partnerstatus automatisch auf Demo setzen.
         */
        if (
            function_exists(
                'update_field'
            )
        ) {
            update_field(
                'jl_brand_partner_status',
                'demo',
                'term_'
                    . $term->term_id
            );
        } else {
            update_term_meta(
                $term->term_id,
                'jl_brand_partner_status',
                'demo'
            );
        }


        return $term;
    }


    /* =========================================================
       BESTEHENDE PRODUKTE
       ========================================================= */

    /**
     * Bereits angelegtes Demo-Produkt über
     * die iHerb-ID suchen.
     */
    private static function find_existing_demo_product(
        string $source_id
    ): ?WP_Post {
        $posts =
            get_posts([
                'post_type' =>
                    Jung_Leben_Core_Products::POST_TYPE,

                'post_status' =>
                    'any',

                'posts_per_page' =>
                    1,

                'meta_key' =>
                    self::META_SOURCE_ID,

                'meta_value' =>
                    $source_id,

                'no_found_rows' =>
                    true,
            ]);


        if (
            empty(
                $posts
            )
            || ! $posts[0]
            instanceof WP_Post
        ) {
            return null;
        }


        return $posts[0];
    }


    /**
     * Existierendes reales Produkt mit identischem
     * Titel erkennen.
     */
    private static function find_existing_product_by_title(
        string $title
    ): ?WP_Post {
        $query =
            new WP_Query([
                'post_type' =>
                    Jung_Leben_Core_Products::POST_TYPE,

                'post_status' =>
                    'any',

                'posts_per_page' =>
                    1,

                'title' =>
                    $title,

                'no_found_rows' =>
                    true,
            ]);


        if (
            empty(
                $query->posts
            )
            || ! $query->posts[0]
            instanceof WP_Post
        ) {
            return null;
        }


        return
            $query->posts[0];
    }


    /* =========================================================
       REDIRECT
       ========================================================= */

    /**
     * Zur Admin-Seite zurückleiten.
     */
    private static function redirect(
        string $result,
        int $created,
        int $skipped
    ): never {
        $url =
            add_query_arg(
                [
                    'page' =>
                        self::PAGE_SLUG,

                    'jl_result' =>
                        $result,

                    'jl_created' =>
                        $created,

                    'jl_skipped' =>
                        $skipped,
                ],
                admin_url(
                    'tools.php'
                )
            );


        wp_safe_redirect(
            $url
        );

        exit;
    }
}