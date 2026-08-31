<?php
/**
 * Myprotein-Demo-Produkte für Jung Leben.
 *
 * Die Produkte dienen ausschliesslich der geschützten
 * Partner-Vorschau und erscheinen nicht im öffentlichen
 * Jung-Leben-Angebot.
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


final class Jung_Leben_Core_Myprotein_Demo_Products
{
    private const ADMIN_PAGE_SLUG =
        'jung-leben-myprotein-demo-products';


    private const ACTION =
        'jl_create_myprotein_demo_products';


    private const NONCE_ACTION =
        'jl_create_myprotein_demo_products';


    private const PARTNER_NAME =
        'Myprotein';


    private const PARTNER_SLUG =
        'myprotein';


    private const META_DEMO_ONLY =
        '_jl_partner_demo_only';


    private const META_DEMO_PARTNER =
        '_jl_partner_demo_partner';


    private const META_SOURCE_ID =
        '_jl_partner_demo_source_id';


    private const META_MYPROTEIN_SOURCE_ID =
        '_jl_myprotein_demo_product_id';


    /* =========================================================
       INIT
       ========================================================= */

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
            'admin_post_' . self::ACTION,
            [
                self::class,
                'handle_create_products',
            ]
        );
    }


    /* =========================================================
       PRODUKTE
       ========================================================= */

    /**
     * Eigene Jung-Leben-Demotexte.
     *
     * Es werden keine Produktbeschreibungen oder Preise
     * von Myprotein übernommen.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function get_products(): array
    {
        return [
            [
                'source_id' =>
                    '15736555',

                'title' =>
                    'Magnesiumbisglycinat-Tabletten',

                'excerpt' =>
                    'Magnesiumbisglycinat als Beispiel für eine klar eingeordnete Mineralstoff-Empfehlung innerhalb einer bewussten Abendroutine.',

                'purpose' =>
                    'Dieses Myprotein-Produkt zeigt beispielhaft, wie eine Magnesium-Empfehlung auf Jung Leben den Themen Mineralstoffe sowie Schlaf und Regeneration zugeordnet werden kann.',

                'url' =>
                    'https://www.myprotein.ch/p/sporternahrung/magnesiumbisglycinat-tabletten/15736555/',

                'categories' => [
                    'Schlaf & Regeneration',
                    'Vitamine & Mineralstoffe',
                ],

                'routine' => [
                    'evening',
                ],

                'priority' =>
                    30,
            ],

            [
                'source_id' =>
                    '10529329',

                'title' =>
                    'Essentielle Omega-3',

                'excerpt' =>
                    'Ein Omega-3-Produkt als Beispiel für die redaktionelle Einordnung eines klassischen Themas rund um Ernährung und Longevity.',

                'purpose' =>
                    'Dieses Produkt dient in der Partner-Demo als Beispiel dafür, wie eine Omega-3-Empfehlung innerhalb eines übergeordneten Longevity- und Ernährungs-Kontexts eingeordnet werden kann.',

                'url' =>
                    'https://www.myprotein.ch/p/sporternahrung/essentielle-omega-3/10529329/',

                'categories' => [
                    'Zellgesundheit & Longevity',
                ],

                'routine' => [
                    'flexible',
                ],

                'priority' =>
                    20,
            ],

            [
                'source_id' =>
                    '13528456',

                'title' =>
                    'Ashwagandha-Fruchtgummis',

                'excerpt' =>
                    'Ashwagandha als Beispiel für die Verbindung von Pflanzenstoffen, redaktioneller Einordnung und einer bewussten Alltagsroutine.',

                'purpose' =>
                    'Dieses Produkt zeigt beispielhaft, wie ein Pflanzenstoff auf Jung Leben einem Thema und einer passenden Routine zugeordnet werden kann.',

                'url' =>
                    'https://www.myprotein.ch/p/vitamine/ashwagandha-fruchtgummis-60-fruchtgummis/13528456/',

                'categories' => [
                    'Stress & Balance',
                    'Pflanzenstoffe',
                ],

                'routine' => [
                    'evening',
                ],

                'priority' =>
                    10,
            ],
        ];
    }


    /* =========================================================
       ADMIN
       ========================================================= */

    public static function register_admin_page(): void
    {
        add_management_page(
            __(
                'Myprotein Demo-Produkte',
                'jung-leben-core'
            ),
            __(
                'Myprotein Demo-Produkte',
                'jung-leben-core'
            ),
            'manage_options',
            self::ADMIN_PAGE_SLUG,
            [
                self::class,
                'render_admin_page',
            ]
        );
    }


    public static function render_admin_page(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diese Seite.',
                    'jung-leben-core'
                )
            );
        }


        $products =
            self::get_products();


        $existing_count = 0;
        $ready_count = 0;
        $conflict_count = 0;


        foreach ($products as $product) {
            $source_id =
                (string)
                $product['source_id'];


            if (
                self::find_demo_product(
                    $source_id
                ) > 0
            ) {
                $existing_count++;

                continue;
            }


            if (
                self::find_exact_title_product(
                    (string)
                    $product['title']
                ) > 0
            ) {
                $conflict_count++;

                continue;
            }


            $ready_count++;
        }


        $result =
            isset($_GET['jl_myprotein_demo'])
                ? sanitize_key(
                    wp_unslash(
                        $_GET['jl_myprotein_demo']
                    )
                )
                : '';


        $created =
            isset($_GET['created'])
                ? absint(
                    $_GET['created']
                )
                : 0;


        $skipped =
            isset($_GET['skipped'])
                ? absint(
                    $_GET['skipped']
                )
                : 0;


        $conflicts =
            isset($_GET['conflicts'])
                ? absint(
                    $_GET['conflicts']
                )
                : 0;
        ?>

        <div class="wrap">

            <h1>
                Myprotein Demo-Produkte
            </h1>


            <p style="max-width:900px;">
                Diese Produkte werden ausschliesslich für
                die geschützte Myprotein-Partner-Demo
                angelegt. Sie erscheinen nicht im normalen
                Jung-Leben-Angebot.
            </p>


            <?php if ($result === 'done') : ?>

                <div
                    class="
                        notice
                        notice-success
                        is-dismissible
                    "
                >

                    <p>

                        <strong>
                            Demo-Produkte verarbeitet.
                        </strong>

                        <?php
                        echo esc_html(
                            sprintf(
                                'Neu: %1$d · Bereits vorhanden: %2$d · Namenskonflikte: %3$d',
                                $created,
                                $skipped,
                                $conflicts
                            )
                        );
                        ?>

                    </p>

                </div>

            <?php endif; ?>


            <div
                style="
                    max-width:900px;
                    margin-top:24px;
                    padding:24px;
                    background:#fff;
                    border:1px solid #dcdcde;
                    border-radius:8px;
                "
            >

                <h2 style="margin-top:0;">
                    Status
                </h2>


                <table class="widefat striped">

                    <tbody>

                        <tr>

                            <td>
                                Demo-Produkte
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        count(
                                            $products
                                        )
                                    );
                                    ?>
                                </strong>
                            </td>

                        </tr>


                        <tr>

                            <td>
                                Bereits erstellt
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        $existing_count
                                    );
                                    ?>
                                </strong>
                            </td>

                        </tr>


                        <tr>

                            <td>
                                Bereit
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        $ready_count
                                    );
                                    ?>
                                </strong>
                            </td>

                        </tr>


                        <tr>

                            <td>
                                Namenskonflikt
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        $conflict_count
                                    );
                                    ?>
                                </strong>
                            </td>

                        </tr>

                    </tbody>

                </table>


                <p style="margin-top:20px;">
                    Es werden drei reale, aktuell auf
                    Myprotein CH gelistete Produkte als
                    Entwürfe angelegt.
                </p>


                <p>
                    Preise, Produkttexte und Produktbilder
                    werden bewusst nicht automatisch übernommen.
                </p>


                <form
                    action="<?php
                    echo esc_url(
                        admin_url(
                            'admin-post.php'
                        )
                    );
                    ?>"
                    method="post"
                    style="margin-top:22px;"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="<?php
                        echo esc_attr(
                            self::ACTION
                        );
                        ?>"
                    >


                    <?php
                    wp_nonce_field(
                        self::NONCE_ACTION
                    );
                    ?>


                    <?php
                    submit_button(
                        __(
                            '3 Myprotein-Demo-Produkte anlegen',
                            'jung-leben-core'
                        ),
                        'primary',
                        'submit',
                        false
                    );
                    ?>

                </form>

            </div>

        </div>

        <?php
    }


    /* =========================================================
       IMPORT
       ========================================================= */

    public static function handle_create_products(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diesen Vorgang.',
                    'jung-leben-core'
                )
            );
        }


        check_admin_referer(
            self::NONCE_ACTION
        );


        $partner =
            self::ensure_partner_term();


        if (! $partner instanceof WP_Term) {
            wp_die(
                esc_html__(
                    'Die Marke Myprotein konnte nicht angelegt werden.',
                    'jung-leben-core'
                )
            );
        }


        $created = 0;
        $skipped = 0;
        $conflicts = 0;


        foreach (
            self::get_products()
            as $product
        ) {
            $source_id =
                (string)
                $product['source_id'];


            if (
                self::find_demo_product(
                    $source_id
                ) > 0
            ) {
                $skipped++;

                continue;
            }


            if (
                self::find_exact_title_product(
                    (string)
                    $product['title']
                ) > 0
            ) {
                $conflicts++;

                continue;
            }


            $product_id =
                self::create_product(
                    $product,
                    $partner
                );


            if ($product_id > 0) {
                $created++;
            }
        }


        $redirect =
            add_query_arg(
                [
                    'page' =>
                        self::ADMIN_PAGE_SLUG,

                    'jl_myprotein_demo' =>
                        'done',

                    'created' =>
                        $created,

                    'skipped' =>
                        $skipped,

                    'conflicts' =>
                        $conflicts,
                ],
                admin_url(
                    'tools.php'
                )
            );


        wp_safe_redirect(
            $redirect
        );

        exit;
    }


    /* =========================================================
       PRODUKT ANLEGEN
       ========================================================= */

    /**
     * @param array<string, mixed> $product
     */
    private static function create_product(
        array $product,
        WP_Term $partner
    ): int {
        $product_id =
            wp_insert_post(
                [
                    'post_type' =>
                        Jung_Leben_Core_Products::POST_TYPE,

                    'post_status' =>
                        'draft',

                    'post_title' =>
                        sanitize_text_field(
                            (string)
                            $product['title']
                        ),

                    'post_excerpt' =>
                        sanitize_textarea_field(
                            (string)
                            $product['excerpt']
                        ),

                    'post_content' =>
                        '',
                ],
                true
            );


        if (is_wp_error($product_id)) {
            return 0;
        }


        $product_id =
            (int)
            $product_id;


        update_post_meta(
            $product_id,
            self::META_DEMO_ONLY,
            '1'
        );


        update_post_meta(
            $product_id,
            self::META_DEMO_PARTNER,
            self::PARTNER_SLUG
        );


        update_post_meta(
            $product_id,
            self::META_SOURCE_ID,
            (string)
            $product['source_id']
        );


        update_post_meta(
            $product_id,
            self::META_MYPROTEIN_SOURCE_ID,
            (string)
            $product['source_id']
        );


        /*
         * Myprotein ist in dieser Demo sowohl
         * Partner als auch Produktmarke.
         */
        wp_set_object_terms(
            $product_id,
            [
                $partner->term_id,
            ],
            Jung_Leben_Core_Products::TAXONOMY_BRAND,
            false
        );


        self::assign_categories(
            $product_id,
            (array)
            $product['categories']
        );


        self::update_product_field(
            $product_id,
            'jl_product_recommendation_status',
            'interesting'
        );


        self::update_product_field(
            $product_id,
            'jl_product_personally_tested',
            0
        );


        self::update_product_field(
            $product_id,
            'jl_product_featured',
            0
        );


        self::update_product_field(
            $product_id,
            'jl_product_priority',
            (int)
            $product['priority']
        );


        self::update_product_field(
            $product_id,
            'jl_product_purpose',
            (string)
            $product['purpose']
        );


        self::update_product_field(
            $product_id,
            'jl_product_routine_time',
            (array)
            $product['routine']
        );


        self::update_product_field(
            $product_id,
            'jl_product_partner_name',
            self::PARTNER_NAME
        );


        self::update_product_field(
            $product_id,
            'jl_product_partner_product_id',
            (string)
            $product['source_id']
        );


        self::update_product_field(
            $product_id,
            'jl_product_original_url',
            (string)
            $product['url']
        );


        self::update_product_field(
            $product_id,
            'jl_product_affiliate_url',
            ''
        );


        self::update_product_field(
            $product_id,
            'jl_product_price_display',
            ''
        );


        self::update_product_field(
            $product_id,
            'jl_product_discount_code',
            ''
        );


        self::update_product_field(
            $product_id,
            'jl_product_button_text',
            'Bei Myprotein ansehen'
        );


        self::update_product_field(
            $product_id,
            'jl_product_link_new_tab',
            1
        );


        self::update_product_field(
            $product_id,
            'jl_product_health_notice',
            'Hinweis: Die dargestellten Inhalte dienen der allgemeinen Information und persönlichen Einordnung und ersetzen keine medizinische Beratung.'
        );


        self::update_product_field(
            $product_id,
            'jl_product_internal_source_note',
            'Geschützte Myprotein-Partner-Demo. Reales Produkt auf myprotein.ch, zuletzt geprüft am 30.08.2026. Eigene Jung-Leben-Demotexte. Keine kopierten Produkttexte, keine automatisch übernommenen Bilder und kein Affiliate-Tracking. Vor öffentlicher Verwendung Produkt, Link und Angaben erneut prüfen.'
        );


        self::update_product_field(
            $product_id,
            'jl_product_last_checked',
            '2026-08-30'
        );


        return $product_id;
    }


    /* =========================================================
       PARTNER / MARKE
       ========================================================= */

    private static function ensure_partner_term(): ?WP_Term
    {
        $term =
            get_term_by(
                'slug',
                self::PARTNER_SLUG,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        if (! $term instanceof WP_Term) {
            $created =
                wp_insert_term(
                    self::PARTNER_NAME,
                    Jung_Leben_Core_Products::TAXONOMY_BRAND,
                    [
                        'slug' =>
                            self::PARTNER_SLUG,
                    ]
                );


            if (is_wp_error($created)) {
                return null;
            }


            $term =
                get_term(
                    (int)
                    $created['term_id'],
                    Jung_Leben_Core_Products::TAXONOMY_BRAND
                );
        }


        if (! $term instanceof WP_Term) {
            return null;
        }


        self::update_brand_field(
            $term,
            'jl_brand_partner_status',
            'demo'
        );


        $website =
            self::get_brand_field(
                $term,
                'jl_brand_website'
            );


        if ($website === '') {
            self::update_brand_field(
                $term,
                'jl_brand_website',
                'https://www.myprotein.ch/'
            );
        }


        return $term;
    }


    /* =========================================================
       KATEGORIEN
       ========================================================= */

    /**
     * @param array<int, string> $categories
     */
    private static function assign_categories(
        int $product_id,
        array $categories
    ): void {
        $term_ids = [];


        foreach ($categories as $category_name) {
            $category_name =
                sanitize_text_field(
                    (string)
                    $category_name
                );


            if ($category_name === '') {
                continue;
            }


            $existing =
                term_exists(
                    $category_name,
                    Jung_Leben_Core_Products::TAXONOMY_CATEGORY
                );


            if (! $existing) {
                $existing =
                    wp_insert_term(
                        $category_name,
                        Jung_Leben_Core_Products::TAXONOMY_CATEGORY
                    );
            }


            if (is_wp_error($existing)) {
                continue;
            }


            $term_ids[] =
                is_array($existing)
                    ? (int)
                    $existing['term_id']
                    : (int)
                    $existing;
        }


        if (! empty($term_ids)) {
            wp_set_object_terms(
                $product_id,
                $term_ids,
                Jung_Leben_Core_Products::TAXONOMY_CATEGORY,
                false
            );
        }
    }


    /* =========================================================
       SUCHE
       ========================================================= */

    private static function find_demo_product(
        string $source_id
    ): int {
        $query =
            new WP_Query([
                'post_type' =>
                    Jung_Leben_Core_Products::POST_TYPE,

                'post_status' => [
                    'publish',
                    'draft',
                    'pending',
                    'private',
                    'future',
                ],

                'posts_per_page' =>
                    1,

                'fields' =>
                    'ids',

                'meta_query' => [
                    'relation' =>
                        'OR',

                    [
                        'key' =>
                            self::META_SOURCE_ID,

                        'value' =>
                            $source_id,
                    ],

                    [
                        'key' =>
                            self::META_MYPROTEIN_SOURCE_ID,

                        'value' =>
                            $source_id,
                    ],
                ],

                'no_found_rows' =>
                    true,
            ]);


        return
            empty($query->posts)
                ? 0
                : (int)
                $query->posts[0];
    }


    private static function find_exact_title_product(
        string $title
    ): int {
        $posts =
            get_posts([
                'post_type' =>
                    Jung_Leben_Core_Products::POST_TYPE,

                'post_status' => [
                    'publish',
                    'draft',
                    'pending',
                    'private',
                    'future',
                ],

                'posts_per_page' =>
                    50,

                's' =>
                    $title,
            ]);


        foreach ($posts as $post) {
            if (! $post instanceof WP_Post) {
                continue;
            }


            if (
                trim($post->post_title)
                ===
                trim($title)
            ) {
                return
                    (int)
                    $post->ID;
            }
        }


        return 0;
    }


    /* =========================================================
       ACF / META
       ========================================================= */

    private static function update_product_field(
        int $product_id,
        string $field_name,
        mixed $value
    ): void {
        if (function_exists('update_field')) {
            update_field(
                $field_name,
                $value,
                $product_id
            );

            return;
        }


        update_post_meta(
            $product_id,
            $field_name,
            $value
        );
    }


    private static function update_brand_field(
        WP_Term $term,
        string $field_name,
        mixed $value
    ): void {
        if (function_exists('update_field')) {
            update_field(
                $field_name,
                $value,
                'term_'
                    . $term->term_id
            );

            return;
        }


        update_term_meta(
            $term->term_id,
            $field_name,
            $value
        );
    }


    private static function get_brand_field(
        WP_Term $term,
        string $field_name
    ): string {
        if (function_exists('get_field')) {
            return
                trim(
                    (string)
                    get_field(
                        $field_name,
                        'term_'
                            . $term->term_id
                    )
                );
        }


        return
            trim(
                (string)
                get_term_meta(
                    $term->term_id,
                    $field_name,
                    true
                )
            );
    }
}