<?php
/**
 * Geschützte Partner-Demos für Jung Leben.
 *
 * Unterstützte Partner:
 * - iHerb
 * - Sunday Natural
 * - Myprotein
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}


/* =========================================================
   ZUSÄTZLICHE DEMO-IMPORTER LADEN
   ========================================================= */

$sunday_demo_file =
    __DIR__
    . '/class-jung-leben-core-sunday-demo-products.php';


if (file_exists($sunday_demo_file)) {
    require_once
        $sunday_demo_file;
}


$myprotein_demo_file =
    __DIR__
    . '/class-jung-leben-core-myprotein-demo-products.php';


if (file_exists($myprotein_demo_file)) {
    require_once
        $myprotein_demo_file;
}


/**
 * Geschützte Partner-Vorschau:
 *
 * Übersicht
 * → Wissen
 * → Produkt
 * → Routine
 * → Partner
 */
final class Jung_Leben_Core_Partner_Demo
{
    private const ADMIN_PAGE_SLUG =
        'jung-leben-partner-demo';


    private const QUERY_VAR_PARTNER =
        'jl_partner_demo_brand';


    private const QUERY_VAR_TYPE =
        'jl_partner_demo_type';


    private const QUERY_VAR_SLUG =
        'jl_partner_demo_slug';


    private const META_DEMO_ONLY =
        '_jl_partner_demo_only';


    private const META_DEMO_PARTNER =
        '_jl_partner_demo_partner';


    private const META_SOURCE_ID =
        '_jl_partner_demo_source_id';


    private const META_IHERB_SOURCE_ID =
        '_jl_iherb_demo_product_id';


    private const META_SUNDAY_SOURCE_ID =
        '_jl_sunday_demo_product_id';


    private const META_MYPROTEIN_SOURCE_ID =
        '_jl_myprotein_demo_product_id';


    private const REWRITE_VERSION =
        '5';


    private const REWRITE_OPTION =
        'jung_leben_partner_demo_rewrite_version';


    /* =========================================================
       INIT
       ========================================================= */

    public static function init(): void
    {
        add_action(
            'init',
            [
                self::class,
                'register_rewrite_rules',
            ]
        );


        add_filter(
            'query_vars',
            [
                self::class,
                'register_query_vars',
            ]
        );


        add_action(
            'template_redirect',
            [
                self::class,
                'handle_demo_request',
            ],
            -100
        );


        add_action(
            'admin_menu',
            [
                self::class,
                'register_admin_page',
            ]
        );


        add_action(
            'admin_init',
            [
                self::class,
                'maybe_flush_rewrite_rules',
            ]
        );


        add_filter(
            'the_posts',
            [
                self::class,
                'hide_demo_only_products',
            ],
            20,
            2
        );


        if (
            class_exists(
                'Jung_Leben_Core_Sunday_Demo_Products'
            )
        ) {
            Jung_Leben_Core_Sunday_Demo_Products::init();
        }


        if (
            class_exists(
                'Jung_Leben_Core_Myprotein_Demo_Products'
            )
        ) {
            Jung_Leben_Core_Myprotein_Demo_Products::init();
        }
    }


    /* =========================================================
       AKTIVIERUNG
       ========================================================= */

    public static function activate(): void
    {
        self::register_rewrite_rules();

        flush_rewrite_rules();


        update_option(
            self::REWRITE_OPTION,
            self::REWRITE_VERSION,
            false
        );
    }


    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }


    /* =========================================================
       PARTNER-KONFIGURATION
       ========================================================= */

    /**
     * @return array<string, mixed>|null
     */
    private static function get_partner_config(
        WP_Term $partner
    ): ?array {
        $configs = [

            /* iHerb */

            'iherb' => [
                'primary_source_id' =>
                    '103273',

                'product_order' => [
                    '103273',
                    '109322',
                    '310',
                ],

                'partner_button' =>
                    'Bei iHerb ansehen',
            ],


            /* Sunday Natural */

            'sunday-natural' => [
                'primary_source_id' =>
                    'sunday-magnesium-glycinat-depot-150-120',

                'product_order' => [
                    'sunday-magnesium-glycinat-depot-150-120',
                    'sunday-omega3-epa400-dha225-60',
                    'sunday-ashwagandha-ksm66-100',
                ],

                'partner_button' =>
                    'Bei Sunday Natural ansehen',
            ],


            /* Myprotein */

            'myprotein' => [
                'primary_source_id' =>
                    '15736555',

                'product_order' => [
                    '15736555',
                    '10529329',
                    '13528456',
                ],

                'partner_button' =>
                    'Bei Myprotein ansehen',
            ],
        ];


        return
            $configs[
                $partner->slug
            ]
            ?? null;
    }


    /* =========================================================
       REWRITE
       ========================================================= */

    public static function register_rewrite_rules(): void
    {
        add_rewrite_rule(
            '^partner-demo/([^/]+)/(erfahrung|produkt|routine)/([^/]+)/?$',
            'index.php?'
                . self::QUERY_VAR_PARTNER
                . '=$matches[1]&'
                . self::QUERY_VAR_TYPE
                . '=$matches[2]&'
                . self::QUERY_VAR_SLUG
                . '=$matches[3]',
            'top'
        );


        add_rewrite_rule(
            '^partner-demo/([^/]+)/?$',
            'index.php?'
                . self::QUERY_VAR_PARTNER
                . '=$matches[1]',
            'top'
        );
    }


    /**
     * @param array<int, string> $vars
     *
     * @return array<int, string>
     */
    public static function register_query_vars(
        array $vars
    ): array {
        $vars[] =
            self::QUERY_VAR_PARTNER;

        $vars[] =
            self::QUERY_VAR_TYPE;

        $vars[] =
            self::QUERY_VAR_SLUG;


        return $vars;
    }


    public static function maybe_flush_rewrite_rules(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }


        $current =
            (string)
            get_option(
                self::REWRITE_OPTION,
                ''
            );


        if (
            $current
            ===
            self::REWRITE_VERSION
        ) {
            return;
        }


        self::register_rewrite_rules();

        flush_rewrite_rules(
            false
        );


        update_option(
            self::REWRITE_OPTION,
            self::REWRITE_VERSION,
            false
        );
    }


    /* =========================================================
       DEMO-PRODUKTE IM NORMALEN FRONTEND VERSTECKEN
       ========================================================= */

    /**
     * @param array<int, WP_Post> $posts
     *
     * @return array<int, WP_Post>
     */
    public static function hide_demo_only_products(
        array $posts,
        WP_Query $query
    ): array {
        if (
            is_admin()
            ||
            (bool)
            $query->get(
                'jl_include_demo_products'
            )
        ) {
            return $posts;
        }


        if (empty($posts)) {
            return $posts;
        }


        $filtered = [];


        foreach ($posts as $post) {
            if (! $post instanceof WP_Post) {
                continue;
            }


            if (
                $post->post_type
                !==
                Jung_Leben_Core_Products::POST_TYPE
            ) {
                $filtered[] =
                    $post;

                continue;
            }


            if (
                (string)
                get_post_meta(
                    $post->ID,
                    self::META_DEMO_ONLY,
                    true
                )
                ===
                '1'
            ) {
                continue;
            }


            $filtered[] =
                $post;
        }


        return
            array_values(
                $filtered
            );
    }


    /* =========================================================
       ADMIN
       ========================================================= */

    public static function register_admin_page(): void
    {
        add_management_page(
            __(
                'Jung Leben Partner-Demos',
                'jung-leben-core'
            ),
            __(
                'Jung Leben Partner-Demos',
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


        $partners =
            get_terms([
                'taxonomy' =>
                    Jung_Leben_Core_Products::TAXONOMY_BRAND,

                'hide_empty' =>
                    false,

                'orderby' =>
                    'name',

                'order' =>
                    'ASC',
            ]);


        if (is_wp_error($partners)) {
            $partners = [];
        }


        $demo_partners = [];


        foreach ($partners as $partner) {
            if (! $partner instanceof WP_Term) {
                continue;
            }


            if (
                self::get_partner_status(
                    $partner
                )
                !==
                'demo'
            ) {
                continue;
            }


            if (
                self::get_partner_config(
                    $partner
                )
                ===
                null
            ) {
                continue;
            }


            $demo_partners[] =
                $partner;
        }
        ?>

        <div class="wrap">

            <h1>
                Jung Leben Partner-Demos
            </h1>


            <p style="max-width:850px;">
                Geschützte Partner-Vorschauen mit
                beispielhaften Jung-Leben-Integrationen.
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
                        Vertraulich:
                    </strong>

                    Jede Person mit dem vollständigen
                    Demo-Link kann die jeweilige
                    Partner-Vorschau öffnen.

                </p>

            </div>


            <?php if (empty($demo_partners)) : ?>

                <p>
                    Noch keine Partner-Demo aktiv.
                </p>

            <?php else : ?>

                <table
                    class="widefat striped"
                    style="
                        max-width:1100px;
                        margin-top:25px;
                    "
                >

                    <thead>

                        <tr>

                            <th>
                                Partner
                            </th>

                            <th>
                                Demo-Produkte
                            </th>

                            <th>
                                Geschützter Demo-Link
                            </th>

                            <th>
                                Aktion
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php
                        foreach (
                            $demo_partners
                            as $partner
                        ) :
                            $demo_url =
                                self::get_demo_url(
                                    $partner
                                );


                            $product_count =
                                count(
                                    self::get_partner_products(
                                        $partner
                                    )
                                );
                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?php
                                        echo esc_html(
                                            $partner->name
                                        );
                                        ?>
                                    </strong>
                                </td>


                                <td>
                                    <?php
                                    echo esc_html(
                                        (string)
                                        $product_count
                                    );
                                    ?>
                                </td>


                                <td>

                                    <input
                                        type="text"
                                        readonly
                                        value="<?php
                                        echo esc_attr(
                                            $demo_url
                                        );
                                        ?>"
                                        style="
                                            width:100%;
                                            min-width:420px;
                                            font-family:monospace;
                                            font-size:12px;
                                        "
                                        onclick="this.select();"
                                    >

                                </td>


                                <td>

                                    <a
                                        href="<?php
                                        echo esc_url(
                                            $demo_url
                                        );
                                        ?>"
                                        class="button button-secondary"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Demo öffnen
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

        <?php
    }


    /* =========================================================
       REQUEST
       ========================================================= */

    public static function handle_demo_request(): void
    {
        $partner_slug =
            sanitize_title(
                (string)
                get_query_var(
                    self::QUERY_VAR_PARTNER
                )
            );


        if ($partner_slug === '') {
            return;
        }


        $partner =
            get_term_by(
                'slug',
                $partner_slug,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        if (! $partner instanceof WP_Term) {
            self::render_not_found();

            exit;
        }


        if (
            self::get_partner_config(
                $partner
            )
            ===
            null
            ||
            self::get_partner_status(
                $partner
            )
            !==
            'demo'
            ||
            ! self::request_has_valid_key(
                $partner
            )
        ) {
            self::render_not_found();

            exit;
        }


        $type =
            sanitize_key(
                (string)
                get_query_var(
                    self::QUERY_VAR_TYPE
                )
            );


        $slug =
            sanitize_title(
                (string)
                get_query_var(
                    self::QUERY_VAR_SLUG
                )
            );


        if (
            $type === ''
            &&
            $slug === ''
        ) {
            self::render_demo_home(
                $partner
            );

            exit;
        }


        if (
            $type === 'erfahrung'
            &&
            $slug ===
            'magnesium-abendroutine'
        ) {
            self::render_partner_experience(
                $partner
            );

            exit;
        }


        if (
            $type === 'produkt'
            &&
            $slug ===
            'magnesium-bisglycinate'
        ) {
            self::render_partner_product(
                $partner
            );

            exit;
        }


        if (
            $type === 'routine'
            &&
            $slug ===
            'abendroutine'
        ) {
            self::render_partner_routine(
                $partner
            );

            exit;
        }


        self::render_not_found();

        exit;
    }


    /* =========================================================
       KEY
       ========================================================= */

    private static function generate_demo_key(
        WP_Term $partner
    ): string {
        $payload =
            'jung-leben-partner-demo:'
            . $partner->term_id
            . ':'
            . $partner->slug;


        return
            substr(
                hash_hmac(
                    'sha256',
                    $payload,
                    wp_salt(
                        'auth'
                    )
                ),
                0,
                32
            );
    }


    private static function request_has_valid_key(
        WP_Term $partner
    ): bool {
        $provided =
            isset($_GET['key'])
                ? sanitize_text_field(
                    wp_unslash(
                        $_GET['key']
                    )
                )
                : '';


        return
            $provided !== ''
            &&
            hash_equals(
                self::generate_demo_key(
                    $partner
                ),
                $provided
            );
    }


    /* =========================================================
       URL
       ========================================================= */

    public static function get_demo_url(
        WP_Term $partner,
        string $type = '',
        string $slug = ''
    ): string {
        $path =
            '/partner-demo/'
            . rawurlencode(
                $partner->slug
            )
            . '/';


        if (
            $type !== ''
            &&
            $slug !== ''
        ) {
            $path .=
                rawurlencode(
                    $type
                )
                . '/'
                . rawurlencode(
                    $slug
                )
                . '/';
        }


        return
            add_query_arg(
                'key',
                self::generate_demo_key(
                    $partner
                ),
                home_url(
                    $path
                )
            );
    }


    /* =========================================================
       PARTNERSTATUS
       ========================================================= */

    private static function get_partner_status(
        WP_Term $partner
    ): string {
        if (
            function_exists(
                'get_field'
            )
        ) {
            $status =
                sanitize_key(
                    (string)
                    get_field(
                        'jl_brand_partner_status',
                        'term_'
                            . $partner->term_id
                    )
                );


            return
                $status !== ''
                    ? $status
                    : 'none';
        }


        return
            sanitize_key(
                (string)
                get_term_meta(
                    $partner->term_id,
                    'jl_brand_partner_status',
                    true
                )
            );
    }


    /* =========================================================
       SOURCE-ID
       ========================================================= */

    private static function get_product_source_id(
        int $product_id
    ): string {
        $keys = [
            self::META_SOURCE_ID,
            self::META_IHERB_SOURCE_ID,
            self::META_SUNDAY_SOURCE_ID,
            self::META_MYPROTEIN_SOURCE_ID,
        ];


        foreach ($keys as $key) {
            $value =
                trim(
                    (string)
                    get_post_meta(
                        $product_id,
                        $key,
                        true
                    )
                );


            if ($value !== '') {
                return $value;
            }
        }


        return '';
    }


    /* =========================================================
       PRODUKTE
       ========================================================= */

    /**
     * @return array<int, WP_Post>
     */
    private static function get_partner_products(
        WP_Term $partner
    ): array {
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
                    12,

                'meta_query' => [
                    [
                        'key' =>
                            self::META_DEMO_ONLY,

                        'value' =>
                            '1',
                    ],

                    [
                        'key' =>
                            self::META_DEMO_PARTNER,

                        'value' =>
                            $partner->slug,
                    ],
                ],

                'no_found_rows' =>
                    true,

                'jl_include_demo_products' =>
                    true,
            ]);


        $posts =
            is_array(
                $query->posts
            )
                ? $query->posts
                : [];


        $config =
            self::get_partner_config(
                $partner
            );


        if ($config === null) {
            return $posts;
        }


        $order =
            (array)
            $config['product_order'];


        usort(
            $posts,
            static function (
                WP_Post $a,
                WP_Post $b
            ) use (
                $order
            ): int {
                $a_source =
                    self::get_product_source_id(
                        $a->ID
                    );


                $b_source =
                    self::get_product_source_id(
                        $b->ID
                    );


                $a_index =
                    array_search(
                        $a_source,
                        $order,
                        true
                    );


                $b_index =
                    array_search(
                        $b_source,
                        $order,
                        true
                    );


                $a_priority =
                    $a_index === false
                        ? 999
                        : (int)
                        $a_index;


                $b_priority =
                    $b_index === false
                        ? 999
                        : (int)
                        $b_index;


                return
                    $a_priority
                    <=>
                    $b_priority;
            }
        );


        return $posts;
    }


    private static function get_demo_product_by_source_id(
        WP_Term $partner,
        string $source_id
    ): ?WP_Post {
        foreach (
            self::get_partner_products(
                $partner
            )
            as $product
        ) {
            if (
                self::get_product_source_id(
                    $product->ID
                )
                ===
                $source_id
            ) {
                return $product;
            }
        }


        return null;
    }


    private static function get_primary_product(
        WP_Term $partner
    ): ?WP_Post {
        $config =
            self::get_partner_config(
                $partner
            );


        if ($config === null) {
            return null;
        }


        return
            self::get_demo_product_by_source_id(
                $partner,
                (string)
                $config['primary_source_id']
            );
    }


    /* =========================================================
       PRODUKTFELDER
       ========================================================= */

    private static function get_product_field(
        string $field_name,
        int $post_id,
        mixed $fallback = ''
    ): mixed {
        if (
            function_exists(
                'get_field'
            )
        ) {
            $value =
                get_field(
                    $field_name,
                    $post_id
                );


            if (
                $value !== null
                &&
                $value !== ''
            ) {
                return $value;
            }
        } else {
            $value =
                get_post_meta(
                    $post_id,
                    $field_name,
                    true
                );


            if ($value !== '') {
                return $value;
            }
        }


        return $fallback;
    }


    private static function get_primary_partner_url(
        WP_Term $partner
    ): string {
        $product =
            self::get_primary_product(
                $partner
            );


        if (! $product instanceof WP_Post) {
            return '';
        }


        return
            trim(
                (string)
                self::get_product_field(
                    'jl_product_original_url',
                    $product->ID,
                    ''
                )
            );
    }


    /* =========================================================
       BILDER
       ========================================================= */

    private static function get_product_image_html(
        WP_Post $product,
        string $size,
        string $class
    ): string {
        if (
            ! has_post_thumbnail(
                $product->ID
            )
        ) {
            return '';
        }


        $html =
            get_the_post_thumbnail(
                $product->ID,
                $size,
                [
                    'class' =>
                        $class,

                    'loading' =>
                        'eager',
                ]
            );


        return
            is_string($html)
                ? $html
                : '';
    }


    private static function get_product_monogram(
        WP_Post $product
    ): string {
        $source_id =
            self::get_product_source_id(
                $product->ID
            );


        $map = [
            '103273' =>
                'Mg',

            '109322' =>
                'O3',

            '310' =>
                'AS',

            'sunday-magnesium-glycinat-depot-150-120' =>
                'Mg',

            'sunday-omega3-epa400-dha225-60' =>
                'O3',

            'sunday-ashwagandha-ksm66-100' =>
                'AS',

            '15736555' =>
                'Mg',

            '10529329' =>
                'O3',

            '13528456' =>
                'AS',
        ];


        return
            $map[
                $source_id
            ]
            ?? 'JL';
    }


    /* =========================================================
       LOGOS
       ========================================================= */

    private static function get_partner_logo_html(
        WP_Term $partner
    ): string {
        if (
            ! function_exists(
                'get_field'
            )
        ) {
            return '';
        }


        $logo_id =
            absint(
                get_field(
                    'jl_brand_logo',
                    'term_'
                        . $partner->term_id
                )
            );


        if ($logo_id <= 0) {
            return '';
        }


        $html =
            wp_get_attachment_image(
                $logo_id,
                'medium',
                false,
                [
                    'class' =>
                        'jl-partner-demo__brand-logo',

                    'loading' =>
                        'eager',
                ]
            );


        return
            is_string($html)
                ? $html
                : '';
    }


    private static function get_site_logo_html(): string
    {
        $logo_path =
            get_stylesheet_directory()
            . '/assets/images/logo-jung-leben.png';


        if (file_exists($logo_path)) {
            return
                sprintf(
                    '<img src="%1$s" alt="%2$s" class="jl-partner-demo__site-logo">',
                    esc_url(
                        get_stylesheet_directory_uri()
                        . '/assets/images/logo-jung-leben.png'
                    ),
                    esc_attr(
                        get_bloginfo(
                            'name'
                        )
                    )
                );
        }


        return
            sprintf(
                '<span class="jl-partner-demo__site-name">%s</span>',
                esc_html(
                    get_bloginfo(
                        'name'
                    )
                )
            );
    }


    /* =========================================================
       DOCUMENT
       ========================================================= */

    private static function render_document_start(
        WP_Term $partner,
        string $title,
        string $active = 'home'
    ): void {
        status_header(200);

        nocache_headers();


        header(
            'X-Robots-Tag: noindex, nofollow, noarchive, nosnippet',
            true
        );


        header(
            'Referrer-Policy: no-referrer',
            true
        );


        $css_path =
            JUNG_LEBEN_CORE_PATH
            . 'assets/css/partner-demo.css';


        $css_version =
            file_exists($css_path)
                ? (string)
                filemtime($css_path)
                : JUNG_LEBEN_CORE_VERSION;


        $home_url =
            self::get_demo_url(
                $partner
            );


        $experience_url =
            self::get_demo_url(
                $partner,
                'erfahrung',
                'magnesium-abendroutine'
            );


        $product_url =
            self::get_demo_url(
                $partner,
                'produkt',
                'magnesium-bisglycinate'
            );


        $routine_url =
            self::get_demo_url(
                $partner,
                'routine',
                'abendroutine'
            );
        ?>

<!doctype html>

<html <?php language_attributes(); ?>>

<head>

    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="robots"
        content="noindex,nofollow,noarchive,nosnippet"
    >

    <title>
        <?php
        echo esc_html(
            $title
            . ' | Jung Leben'
        );
        ?>
    </title>


    <link
        rel="stylesheet"
        href="<?php
        echo esc_url(
            add_query_arg(
                'ver',
                $css_version,
                JUNG_LEBEN_CORE_URL
                    . 'assets/css/partner-demo.css'
            )
        );
        ?>"
    >

</head>


<body class="jl-partner-demo">

<header class="jl-partner-demo__header">

    <div class="jl-partner-demo__container jl-partner-demo__header-inner">

        <a
            href="<?php echo esc_url($home_url); ?>"
            class="jl-partner-demo__logo-link"
        >
            <?php
            echo wp_kses_post(
                self::get_site_logo_html()
            );
            ?>
        </a>


        <nav
            class="jl-partner-demo__nav"
            aria-label="Partner-Demo Navigation"
        >

            <a
                href="<?php echo esc_url($home_url); ?>"
                class="<?php
                echo
                    $active === 'home'
                        ? 'is-active'
                        : '';
                ?>"
            >
                Übersicht
            </a>


            <a
                href="<?php echo esc_url($experience_url); ?>"
                class="<?php
                echo
                    $active === 'experience'
                        ? 'is-active'
                        : '';
                ?>"
            >
                Wissen
            </a>


            <a
                href="<?php echo esc_url($product_url); ?>"
                class="<?php
                echo
                    $active === 'product'
                        ? 'is-active'
                        : '';
                ?>"
            >
                Produkt
            </a>


            <a
                href="<?php echo esc_url($routine_url); ?>"
                class="<?php
                echo
                    $active === 'routine'
                        ? 'is-active'
                        : '';
                ?>"
            >
                Routine
            </a>

        </nav>


        <span class="jl-partner-demo__private-badge">
            Partner-Vorschau
        </span>

    </div>

</header>

        <?php
    }


    private static function render_document_end(
        WP_Term $partner
    ): void {
        ?>

<footer class="jl-partner-demo__footer">

    <div class="jl-partner-demo__container">

        <p>
            <?php
            echo esc_html(
                sprintf(
                    'Vertrauliche Partner-Vorschau · %s · keine aktive Partnerschaft',
                    $partner->name
                )
            );
            ?>
        </p>

    </div>

</footer>

</body>
</html>

        <?php
    }


    /* =========================================================
       JOURNEY-PROGRESS
       ========================================================= */

    private static function render_journey_progress(
        WP_Term $partner,
        int $active_step
    ): void {
        $experience_url =
            self::get_demo_url(
                $partner,
                'erfahrung',
                'magnesium-abendroutine'
            );


        $product_url =
            self::get_demo_url(
                $partner,
                'produkt',
                'magnesium-bisglycinate'
            );


        $routine_url =
            self::get_demo_url(
                $partner,
                'routine',
                'abendroutine'
            );


        $partner_url =
            self::get_primary_partner_url(
                $partner
            );
        ?>

<nav
    class="jl-demo-progress"
    aria-label="Demo-Prozess"
>

    <div class="jl-partner-demo__container">

        <div class="jl-demo-progress__track">

            <?php
            self::render_progress_item(
                1,
                'Einstieg',
                'Wissen',
                $experience_url,
                $active_step === 1
            );
            ?>

            <span
                class="jl-demo-progress__arrow"
                aria-hidden="true"
            >
                →
            </span>

            <?php
            self::render_progress_item(
                2,
                'Einordnung',
                'Produkt',
                $product_url,
                $active_step === 2
            );
            ?>

            <span
                class="jl-demo-progress__arrow"
                aria-hidden="true"
            >
                →
            </span>

            <?php
            self::render_progress_item(
                3,
                'Alltag',
                'Routine',
                $routine_url,
                $active_step === 3
            );
            ?>

            <span
                class="jl-demo-progress__arrow"
                aria-hidden="true"
            >
                →
            </span>

            <?php
            self::render_progress_item(
                4,
                'Ziel',
                $partner->name . ' ↗',
                $partner_url,
                $active_step === 4,
                true
            );
            ?>

        </div>

    </div>

</nav>

        <?php
    }


    private static function render_progress_item(
        int $number,
        string $small,
        string $label,
        string $url,
        bool $active,
        bool $external = false
    ): void {
        $class =
            'jl-demo-progress__item'
            . (
                $active
                    ? ' is-active'
                    : ''
            );


        if ($url === '') {
            ?>

            <div class="<?php echo esc_attr($class); ?>">

                <span class="jl-demo-progress__number">
                    <?php echo esc_html((string) $number); ?>
                </span>

                <span class="jl-demo-progress__copy">

                    <small>
                        <?php echo esc_html($small); ?>
                    </small>

                    <strong>
                        <?php echo esc_html($label); ?>
                    </strong>

                </span>

            </div>

            <?php

            return;
        }
        ?>

        <a
            href="<?php echo esc_url($url); ?>"
            class="<?php echo esc_attr($class); ?>"
            <?php if ($external) : ?>
                target="_blank"
                rel="noopener noreferrer"
            <?php endif; ?>
        >

            <span class="jl-demo-progress__number">
                <?php echo esc_html((string) $number); ?>
            </span>

            <span class="jl-demo-progress__copy">

                <small>
                    <?php echo esc_html($small); ?>
                </small>

                <strong>
                    <?php echo esc_html($label); ?>
                </strong>

            </span>

        </a>

        <?php
    }


    /* =========================================================
       TRANSPARENZ
       ========================================================= */

    private static function render_demo_notice(): void
    {
        ?>

<section class="jl-partner-demo__notice">

    <div class="jl-partner-demo__container">

        <div class="jl-partner-demo__notice-box">

            <span
                class="jl-partner-demo__notice-mark"
                aria-hidden="true"
            >
                i
            </span>


            <p>
                Diese geschützte Seite dient ausschliesslich
                der Partnerbewerbung und Konzeptdarstellung.
                Sie stellt keine bestehende Kooperation oder
                aktive Affiliate-Partnerschaft dar.
            </p>

        </div>

    </div>

</section>

        <?php
    }


    /* =========================================================
       DEMO HOME
       ========================================================= */

    private static function render_demo_home(
        WP_Term $partner
    ): void {
        $products =
            self::get_partner_products(
                $partner
            );


        $primary_product =
            self::get_primary_product(
                $partner
            );


        $partner_logo =
            self::get_partner_logo_html(
                $partner
            );


        $experience_url =
            self::get_demo_url(
                $partner,
                'erfahrung',
                'magnesium-abendroutine'
            );


        $product_url =
            self::get_demo_url(
                $partner,
                'produkt',
                'magnesium-bisglycinate'
            );


        $routine_url =
            self::get_demo_url(
                $partner,
                'routine',
                'abendroutine'
            );


        $partner_url =
            self::get_primary_partner_url(
                $partner
            );


        self::render_document_start(
            $partner,
            'Partner-Vorschau für '
                . $partner->name,
            'home'
        );
        ?>

<main>

    <section class="jl-demo-home-hero">

        <div class="jl-partner-demo__container">

            <div class="jl-demo-home-hero__grid">

                <div class="jl-demo-home-hero__content">

                    <p class="jl-partner-demo__eyebrow">
                        Partner Preview ·
                        <?php echo esc_html($partner->name); ?>
                    </p>


                    <h1>
                        Vom Inhalt zum
                        <span>
                            passenden Produkt.
                        </span>
                    </h1>


                    <p class="jl-demo-home-hero__lead">
                        Diese Demo zeigt nicht nur,
                        <em>wo</em> ein Produkt erscheint,
                        sondern
                        <strong>
                            wie ein Nutzer dorthin gelangt
                        </strong>:
                        über relevanten Inhalt,
                        nachvollziehbare Einordnung und
                        einen konkreten Alltagskontext.
                    </p>


                    <div class="jl-demo-home-hero__actions">

                        <a
                            href="<?php echo esc_url($experience_url); ?>"
                            class="jl-demo-button"
                        >
                            Demo starten

                            <span aria-hidden="true">
                                →
                            </span>
                        </a>


                        <a
                            href="#demo-prozess"
                            class="jl-demo-text-link"
                        >
                            Ablauf ansehen ↓
                        </a>

                    </div>

                </div>


                <aside class="jl-demo-home-card">

                    <?php if ($partner_logo !== '') : ?>

                        <div class="jl-demo-home-card__logo">

                            <?php
                            echo wp_kses_post(
                                $partner_logo
                            );
                            ?>

                        </div>

                    <?php endif; ?>


                    <span class="jl-demo-home-card__label">
                        Diese Demo in 4 Schritten
                    </span>


                    <ol class="jl-demo-home-card__list">

                        <?php
                        self::render_home_step(
                            1,
                            'Interesse wecken',
                            'Redaktioneller Inhalt',
                            $experience_url
                        );


                        self::render_home_step(
                            2,
                            'Produkt einordnen',
                            'Empfehlung im Kontext',
                            $product_url
                        );


                        self::render_home_step(
                            3,
                            'Alltag zeigen',
                            'Einbindung in eine Routine',
                            $routine_url
                        );


                        self::render_home_step(
                            4,
                            'Zum Partner führen',
                            'Erst jetzt folgt '
                                . $partner->name,
                            $partner_url,
                            true
                        );
                        ?>

                    </ol>

                </aside>

            </div>

        </div>

    </section>


    <?php self::render_demo_notice(); ?>


    <section
        id="demo-prozess"
        class="jl-demo-process"
    >

        <div class="jl-partner-demo__container">

            <header class="jl-demo-section-intro">

                <div>

                    <p class="jl-partner-demo__eyebrow">
                        Klickbare Customer Journey
                    </p>


                    <h2>
                        Vier Schritte.
                        <span>
                            Ein nachvollziehbarer Weg.
                        </span>
                    </h2>

                </div>


                <p>
                    Klicke auf einen Schritt und erlebe,
                    wie Jung Leben den Nutzer vom Inhalt
                    bis zum Partner führt.
                </p>

            </header>


            <div class="jl-demo-process__track">

                <?php
                self::render_process_step(
                    1,
                    'Aa',
                    'Wissen',
                    'Einstieg über ein relevantes Thema statt über einen Kaufbutton.',
                    'Artikel öffnen →',
                    $experience_url
                );
                ?>

                <span
                    class="jl-demo-process__connector"
                    aria-hidden="true"
                ></span>


                <?php
                self::render_process_step(
                    2,
                    'Mg',
                    'Produkt',
                    'Die Empfehlung erscheint dort, wo sie inhaltlich Sinn ergibt.',
                    'Produkt öffnen →',
                    $product_url
                );
                ?>

                <span
                    class="jl-demo-process__connector"
                    aria-hidden="true"
                ></span>


                <?php
                self::render_process_step(
                    3,
                    '☾',
                    'Routine',
                    'Das Produkt wird in einen verständlichen Alltag eingebettet.',
                    'Routine öffnen →',
                    $routine_url
                );
                ?>

                <span
                    class="jl-demo-process__connector"
                    aria-hidden="true"
                ></span>


                <?php
                self::render_process_step(
                    4,
                    '↗',
                    $partner->name,
                    'Erst nach Orientierung folgt der externe Shop.',
                    $partner->name . ' öffnen ↗',
                    $partner_url,
                    true,
                    true
                );
                ?>

            </div>


            <article class="jl-demo-start-card">

                <div class="jl-demo-start-card__visual">

                    <?php
                    if (
                        $primary_product
                        instanceof WP_Post
                    ) {
                        $image =
                            self::get_product_image_html(
                                $primary_product,
                                'large',
                                'jl-demo-start-card__image'
                            );


                        if ($image !== '') {
                            echo wp_kses_post(
                                $image
                            );
                        } else {
                            echo '<span>Mg</span>';
                        }
                    } else {
                        echo '<span>Mg</span>';
                    }
                    ?>

                </div>


                <div class="jl-demo-start-card__content">

                    <p class="jl-partner-demo__eyebrow">
                        Hier beginnt die Demo
                    </p>


                    <h3>
                        Magnesium am Abend:
                        vom Thema zur Produktempfehlung.
                    </h3>


                    <p>
                        Der Leser startet mit einem
                        redaktionellen Thema. Erst innerhalb
                        des Inhalts wird sichtbar, welches
                        Produkt dazu passen könnte.
                    </p>


                    <div class="jl-demo-start-card__actions">

                        <a
                            href="<?php echo esc_url($experience_url); ?>"
                            class="jl-demo-button"
                        >
                            Mit Schritt 1 starten

                            <span>
                                →
                            </span>
                        </a>


                        <span>
                            ca. 2 Minuten
                        </span>

                    </div>

                </div>

            </article>

        </div>

    </section>


    <section class="jl-partner-demo__products">

        <div class="jl-partner-demo__container">

            <header class="jl-demo-section-intro">

                <div>

                    <p class="jl-partner-demo__eyebrow">
                        Weitere Integrationsbeispiele
                    </p>


                    <h2>
                        Mehrere Themen.
                        <span>
                            Dasselbe Prinzip.
                        </span>
                    </h2>

                </div>


                <p>
                    Die Demo-Produkte zeigen,
                    wie unterschiedliche Themenbereiche
                    mit dem gleichen redaktionellen
                    Ansatz integriert werden können.
                </p>

            </header>


            <?php if (! empty($products)) : ?>

                <div class="jl-partner-demo__grid">

                    <?php
                    foreach ($products as $product) {
                        self::render_product_card(
                            $partner,
                            $product
                        );
                    }
                    ?>

                </div>

            <?php endif; ?>

        </div>

    </section>


    <section class="jl-partner-demo__concept">

        <div class="jl-partner-demo__container">

            <div class="jl-partner-demo__concept-grid">

                <div>

                    <p class="jl-partner-demo__eyebrow">
                        Jung Leben Prinzip
                    </p>


                    <h2>
                        Nicht einfach verlinken.
                        <span>
                            Einordnen.
                        </span>
                    </h2>

                </div>


                <div class="jl-partner-demo__concept-points">

                    <div>

                        <span>
                            01
                        </span>

                        <strong>
                            Inhalt zuerst
                        </strong>

                        <p>
                            Der Nutzer kommt wegen eines
                            relevanten Themas – nicht wegen
                            eines Affiliate-Links.
                        </p>

                    </div>


                    <div>

                        <span>
                            02
                        </span>

                        <strong>
                            Kontext schaffen
                        </strong>

                        <p>
                            Produkte werden thematisch
                            nachvollziehbar eingebunden.
                        </p>

                    </div>


                    <div>

                        <span>
                            03
                        </span>

                        <strong>
                            Alltag verbinden
                        </strong>

                        <p>
                            Routinen machen aus der einzelnen
                            Empfehlung einen verständlichen
                            Nutzungskontext.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

        <?php

        self::render_document_end(
            $partner
        );
    }


    /* =========================================================
       HOME STEP
       ========================================================= */

    private static function render_home_step(
        int $number,
        string $title,
        string $subtitle,
        string $url,
        bool $external = false
    ): void {
        ?>

        <li>

            <?php if ($url !== '') : ?>

                <a
                    href="<?php echo esc_url($url); ?>"
                    <?php if ($external) : ?>
                        target="_blank"
                        rel="noopener noreferrer"
                    <?php endif; ?>
                >

                    <span>
                        <?php echo esc_html((string) $number); ?>
                    </span>


                    <div>

                        <strong>
                            <?php echo esc_html($title); ?>
                        </strong>

                        <small>
                            <?php echo esc_html($subtitle); ?>
                        </small>

                    </div>


                    <b aria-hidden="true">
                        <?php
                        echo
                            $external
                                ? '↗'
                                : '→';
                        ?>
                    </b>

                </a>

            <?php else : ?>

                <div class="jl-demo-home-card__static">

                    <span>
                        <?php echo esc_html((string) $number); ?>
                    </span>

                    <div>

                        <strong>
                            <?php echo esc_html($title); ?>
                        </strong>

                        <small>
                            <?php echo esc_html($subtitle); ?>
                        </small>

                    </div>

                </div>

            <?php endif; ?>

        </li>

        <?php
    }


    /* =========================================================
       PROCESS STEP
       ========================================================= */

    private static function render_process_step(
        int $number,
        string $icon,
        string $title,
        string $description,
        string $action,
        string $url,
        bool $external = false,
        bool $partner_step = false
    ): void {
        $class =
            'jl-demo-process__step'
            . (
                $partner_step
                    ? ' jl-demo-process__step--partner'
                    : ''
            );


        if ($url === '') {
            echo
                '<div class="'
                . esc_attr($class)
                . '">';


            self::render_process_step_content(
                $number,
                $icon,
                $title,
                $description,
                $action
            );


            echo '</div>';

            return;
        }
        ?>

        <a
            href="<?php echo esc_url($url); ?>"
            class="<?php echo esc_attr($class); ?>"
            <?php if ($external) : ?>
                target="_blank"
                rel="noopener noreferrer"
            <?php endif; ?>
        >

            <?php
            self::render_process_step_content(
                $number,
                $icon,
                $title,
                $description,
                $action
            );
            ?>

        </a>

        <?php
    }


    private static function render_process_step_content(
        int $number,
        string $icon,
        string $title,
        string $description,
        string $action
    ): void {
        ?>

        <span class="jl-demo-process__number">
            <?php echo esc_html((string) $number); ?>
        </span>


        <span class="jl-demo-process__icon">
            <?php echo esc_html($icon); ?>
        </span>


        <div>

            <small>
                Schritt
                <?php echo esc_html((string) $number); ?>
            </small>

            <strong>
                <?php echo esc_html($title); ?>
            </strong>

            <p>
                <?php echo esc_html($description); ?>
            </p>

            <span class="jl-demo-process__action">
                <?php echo esc_html($action); ?>
            </span>

        </div>

        <?php
    }


    /* =========================================================
       ERFAHRUNG
       ========================================================= */

    private static function render_partner_experience(
        WP_Term $partner
    ): void {
        $product =
            self::get_primary_product(
                $partner
            );


        $product_url =
            self::get_demo_url(
                $partner,
                'produkt',
                'magnesium-bisglycinate'
            );


        self::render_document_start(
            $partner,
            'Magnesium am Abend',
            'experience'
        );
        ?>

<main>

    <?php
    self::render_journey_progress(
        $partner,
        1
    );
    ?>


    <section class="jl-demo-article-hero">

        <div class="jl-demo-reading">

            <nav class="jl-demo-breadcrumb">

                <a
                    href="<?php
                    echo esc_url(
                        self::get_demo_url(
                            $partner
                        )
                    );
                    ?>"
                >
                    Partner Preview
                </a>

                <span>
                    /
                </span>

                <span>
                    Schritt 1 · Wissen
                </span>

            </nav>


            <p class="jl-partner-demo__eyebrow">
                Redaktioneller Beispielinhalt
            </p>


            <h1>
                Magnesium am Abend:
                Wie ein Mineralstoff Teil einer
                bewussten Abendroutine werden kann.
            </h1>


            <p class="jl-demo-article-lead">
                Ein Produkt steht auf Jung Leben
                nicht am Anfang der Customer Journey.
                Zuerst entsteht Orientierung rund um
                ein Thema, das für den Leser relevant ist.
            </p>


            <div class="jl-demo-editorial-note">

                <strong>
                    Transparenz in der Demo
                </strong>

                <p>
                    Dieser Beitrag demonstriert die
                    redaktionelle Einbindung für eine
                    Partnerbewerbung. Er stellt keinen
                    persönlichen Test des dargestellten
                    <?php echo esc_html($partner->name); ?>-Produkts dar.
                </p>

            </div>

        </div>

    </section>


    <section class="jl-demo-article-body">

        <div class="jl-demo-reading">

            <div class="jl-demo-article-copy">

                <p class="jl-demo-drop-intro">
                    Eine gute Abendroutine besteht nicht
                    aus einem einzelnen Produkt. Sie entsteht
                    aus mehreren kleinen Entscheidungen,
                    die den Übergang vom aktiven Tag in
                    eine ruhigere Phase unterstützen.
                </p>


                <h2>
                    Warum der Kontext wichtiger ist
                    als der einzelne Kauf.
                </h2>


                <p>
                    Bei Jung Leben soll eine Empfehlung
                    möglichst aus einem konkreten Kontext
                    entstehen. Statt ein Produkt isoliert
                    zu präsentieren, wird zunächst erklärt,
                    wo es thematisch eingeordnet wird und
                    welche Rolle es innerhalb eines
                    grösseren Themas spielen kann.
                </p>


                <p>
                    Beim Beispiel Magnesium nutzen wir
                    dafür eine bewusste Abendroutine.
                    Im Zentrum steht dabei nicht ein
                    bestimmtes Einnahmeschema, sondern
                    die nachvollziehbare Verbindung
                    zwischen Thema, Produkt und Alltag.
                </p>


                <h2>
                    Erst jetzt kommt das Produkt.
                </h2>


                <p>
                    Nachdem der Leser den Kontext kennt,
                    kann eine konkrete Empfehlung sinnvoll
                    eingebettet werden. Genau an dieser
                    Stelle entsteht der Übergang vom
                    redaktionellen Inhalt zur
                    Produktinformation.
                </p>

            </div>


            <?php
            if (
                $product
                instanceof WP_Post
            ) :
                ?>

                <aside class="jl-demo-related-product">

                    <div class="jl-demo-related-product__top">

                        <span class="jl-demo-related-product__step">
                            Schritt 2
                        </span>

                        <span class="jl-demo-related-product__arrow">
                            →
                        </span>

                    </div>


                    <div class="jl-demo-related-product__body">

                        <div>

                            <p class="jl-partner-demo__eyebrow">
                                Passendes Produkt
                            </p>


                            <?php
                            self::render_product_identity(
                                $product
                            );
                            ?>

                        </div>


                        <a
                            href="<?php echo esc_url($product_url); ?>"
                            class="jl-demo-button"
                        >
                            Weiter zum Produkt

                            <span>
                                →
                            </span>
                        </a>

                    </div>

                </aside>

            <?php endif; ?>


            <div class="jl-demo-next jl-demo-next--strong">

                <span>
                    Weiter in der Demo
                </span>


                <div>

                    <small>
                        Schritt 2 von 4
                    </small>

                    <strong>
                        Die Produktempfehlung
                        im Kontext ansehen.
                    </strong>

                    <a
                        href="<?php echo esc_url($product_url); ?>"
                    >
                        Zum Produkt →
                    </a>

                </div>

            </div>

        </div>

    </section>

</main>

        <?php

        self::render_document_end(
            $partner
        );
    }


    /* =========================================================
       PRODUKT
       ========================================================= */

    private static function render_partner_product(
        WP_Term $partner
    ): void {
        $product =
            self::get_primary_product(
                $partner
            );


        if (! $product instanceof WP_Post) {
            self::render_not_found();

            return;
        }


        $product_id =
            (int)
            $product->ID;


        $purpose =
            trim(
                (string)
                self::get_product_field(
                    'jl_product_purpose',
                    $product_id,
                    ''
                )
            );


        $original_url =
            trim(
                (string)
                self::get_product_field(
                    'jl_product_original_url',
                    $product_id,
                    ''
                )
            );


        $button_text =
            trim(
                (string)
                self::get_product_field(
                    'jl_product_button_text',
                    $product_id,
                    ''
                )
            );


        if ($button_text === '') {
            $config =
                self::get_partner_config(
                    $partner
                );


            $button_text =
                is_array($config)
                    ? (string)
                    $config['partner_button']
                    : 'Beim Partner ansehen';
        }


        $brands =
            get_the_terms(
                $product_id,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        $brand_name =
            is_array($brands)
            &&
            ! empty($brands)
            &&
            $brands[0] instanceof WP_Term
                ? $brands[0]->name
                : '';


        $routine_url =
            self::get_demo_url(
                $partner,
                'routine',
                'abendroutine'
            );


        $experience_url =
            self::get_demo_url(
                $partner,
                'erfahrung',
                'magnesium-abendroutine'
            );


        self::render_document_start(
            $partner,
            $product->post_title,
            'product'
        );
        ?>

<main>

    <?php
    self::render_journey_progress(
        $partner,
        2
    );
    ?>


    <section class="jl-demo-product">

        <div class="jl-partner-demo__container">

            <nav class="jl-demo-breadcrumb">

                <a
                    href="<?php
                    echo esc_url(
                        self::get_demo_url(
                            $partner
                        )
                    );
                    ?>"
                >
                    Partner Preview
                </a>

                <span>/</span>

                <a
                    href="<?php echo esc_url($experience_url); ?>"
                >
                    Schritt 1
                </a>

                <span>/</span>

                <span>
                    Schritt 2 · Produkt
                </span>

            </nav>


            <div class="jl-demo-product__grid">

                <div class="jl-demo-product__visual">

                    <?php
                    $image =
                        self::get_product_image_html(
                            $product,
                            'large',
                            'jl-demo-product__image'
                        );


                    if ($image !== '') {
                        echo wp_kses_post(
                            $image
                        );
                    } else {
                        ?>

                        <span class="jl-demo-product__mark">
                            <?php
                            echo esc_html(
                                self::get_product_monogram(
                                    $product
                                )
                            );
                            ?>
                        </span>

                        <?php
                    }
                    ?>

                </div>


                <div class="jl-demo-product__content">

                    <p class="jl-partner-demo__eyebrow">
                        Schritt 2 · Produktempfehlung
                    </p>


                    <?php if ($brand_name !== '') : ?>

                        <p class="jl-demo-product__brand">
                            <?php echo esc_html($brand_name); ?>
                        </p>

                    <?php endif; ?>


                    <h1>
                        <?php echo esc_html($product->post_title); ?>
                    </h1>


                    <?php
                    if (
                        trim(
                            $product->post_excerpt
                        )
                        !==
                        ''
                    ) :
                        ?>

                        <p class="jl-demo-product__lead">
                            <?php
                            echo esc_html(
                                $product->post_excerpt
                            );
                            ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($purpose !== '') : ?>

                        <div class="jl-demo-product__context">

                            <span>
                                Warum dieses Produkt hier erscheint
                            </span>

                            <p>
                                <?php echo esc_html($purpose); ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <div class="jl-demo-product__actions">

                        <a
                            href="<?php echo esc_url($routine_url); ?>"
                            class="jl-demo-button"
                        >
                            Weiter zur Routine

                            <span>
                                →
                            </span>
                        </a>


                        <?php if ($original_url !== '') : ?>

                            <a
                                href="<?php echo esc_url($original_url); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="jl-demo-secondary-button"
                            >
                                <?php echo esc_html($button_text); ?>
                                ↗
                            </a>

                        <?php endif; ?>

                    </div>


                    <p class="jl-demo-product__affiliate-note">
                        Der externe Link enthält in dieser
                        Partner-Vorschau kein Affiliate-Tracking.
                    </p>

                </div>

            </div>

        </div>

    </section>


    <section class="jl-demo-product-next">

        <div class="jl-partner-demo__container">

            <div class="jl-demo-next-panel">

                <div class="jl-demo-next-panel__number">
                    3
                </div>


                <div class="jl-demo-next-panel__content">

                    <p class="jl-partner-demo__eyebrow">
                        Schritt 3 von 4
                    </p>


                    <h2>
                        Wie passt das Produkt
                        in den Alltag?
                    </h2>


                    <p>
                        Die Produktempfehlung ist nicht
                        das Ende der Journey. Als Nächstes
                        zeigen wir, wie sie in einen
                        nachvollziehbaren Alltagskontext
                        eingebettet werden kann.
                    </p>


                    <a
                        href="<?php echo esc_url($routine_url); ?>"
                        class="jl-demo-button"
                    >
                        Abendroutine entdecken

                        <span>
                            →
                        </span>
                    </a>

                </div>

            </div>

        </div>

    </section>

</main>

        <?php

        self::render_document_end(
            $partner
        );
    }


    /* =========================================================
       ROUTINE
       ========================================================= */

    private static function render_partner_routine(
        WP_Term $partner
    ): void {
        $product =
            self::get_primary_product(
                $partner
            );


        $product_url =
            self::get_demo_url(
                $partner,
                'produkt',
                'magnesium-bisglycinate'
            );


        $partner_url =
            self::get_primary_partner_url(
                $partner
            );


        self::render_document_start(
            $partner,
            'Beispielhafte Abendroutine',
            'routine'
        );
        ?>

<main>

    <?php
    self::render_journey_progress(
        $partner,
        3
    );
    ?>


    <section class="jl-demo-routine-hero">

        <div class="jl-partner-demo__container">

            <nav class="jl-demo-breadcrumb">

                <a
                    href="<?php
                    echo esc_url(
                        self::get_demo_url(
                            $partner
                        )
                    );
                    ?>"
                >
                    Partner Preview
                </a>

                <span>/</span>

                <a
                    href="<?php echo esc_url($product_url); ?>"
                >
                    Schritt 2
                </a>

                <span>/</span>

                <span>
                    Schritt 3 · Routine
                </span>

            </nav>


            <div class="jl-demo-routine-hero__grid">

                <div>

                    <p class="jl-partner-demo__eyebrow">
                        Schritt 3 · Alltag
                    </p>

                    <h1>
                        Ein ruhiger Übergang
                        vom Tag in den Abend.
                    </h1>

                </div>


                <p>
                    Eine Produktempfehlung bleibt nicht
                    isoliert. Jung Leben zeigt,
                    wie sie in einen verständlichen
                    Alltagskontext eingebettet werden kann.
                </p>

            </div>

        </div>

    </section>


    <section class="jl-demo-routine">

        <div class="jl-partner-demo__container">

            <div class="jl-demo-routine__timeline">

                <div class="jl-demo-routine__item">

                    <span class="jl-demo-routine__time">
                        20:30
                    </span>


                    <div>

                        <strong>
                            Den Tag bewusst herunterfahren.
                        </strong>

                        <p>
                            Bildschirmzeit reduzieren,
                            offene Aufgaben abschliessen
                            und den Übergang zum Abend
                            bewusst gestalten.
                        </p>

                    </div>

                </div>


                <div
                    class="
                        jl-demo-routine__item
                        jl-demo-routine__item--product
                    "
                >

                    <span class="jl-demo-routine__time">
                        21:00
                    </span>


                    <div>

                        <span class="jl-demo-routine__badge">
                            Produktempfehlung
                        </span>


                        <strong>
                            Ein Produkt im passenden Kontext.
                        </strong>


                        <p>
                            Hier kann die Magnesium-Empfehlung
                            innerhalb der Routine sichtbar werden,
                            ohne daraus eine allgemeingültige
                            Einnahmeempfehlung abzuleiten.
                        </p>


                        <?php
                        if (
                            $product
                            instanceof WP_Post
                        ) :
                            ?>

                            <a
                                href="<?php echo esc_url($product_url); ?>"
                                class="jl-demo-inline-link"
                            >
                                <?php
                                echo esc_html(
                                    $product->post_title
                                );
                                ?>
                                →
                            </a>

                        <?php endif; ?>

                    </div>

                </div>


                <div class="jl-demo-routine__item">

                    <span class="jl-demo-routine__time">
                        21:30
                    </span>


                    <div>

                        <strong>
                            Ruhe und Schlafvorbereitung.
                        </strong>

                        <p>
                            Eine ruhige Tätigkeit,
                            gedämpftes Licht und möglichst
                            wenig neue Reize bilden den
                            Abschluss der beispielhaften
                            Abendroutine.
                        </p>

                    </div>

                </div>

            </div>


            <div class="jl-demo-routine__note">

                <strong>
                    Beispiel statt Gesundheitsvorgabe
                </strong>

                <p>
                    Diese Routine zeigt ausschliesslich
                    das redaktionelle Konzept innerhalb
                    der Partner-Demo. Sie stellt keine
                    individuelle medizinische Empfehlung dar.
                </p>

            </div>


            <div class="jl-demo-final-step">

                <div class="jl-demo-final-step__number">
                    4
                </div>


                <div>

                    <p class="jl-partner-demo__eyebrow">
                        Letzter Schritt
                    </p>


                    <h2>
                        Erst jetzt führt die Journey zu
                        <?php echo esc_html($partner->name); ?>.
                    </h2>


                    <p>
                        Der Nutzer kennt nun das Thema,
                        die Produktempfehlung und den
                        Alltagskontext. Der externe Shop
                        steht bewusst am Ende – nicht
                        am Anfang der Journey.
                    </p>


                    <?php if ($partner_url !== '') : ?>

                        <a
                            href="<?php echo esc_url($partner_url); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="
                                jl-demo-button
                                jl-demo-button--partner
                            "
                        >
                            Produkt bei
                            <?php echo esc_html($partner->name); ?>
                            ansehen

                            <span>
                                ↗
                            </span>
                        </a>

                    <?php endif; ?>


                    <small>
                        Demo-Link ohne Affiliate-Tracking
                    </small>

                </div>

            </div>

        </div>

    </section>

</main>

        <?php

        self::render_document_end(
            $partner
        );
    }


    /* =========================================================
       PRODUKTKARTE
       ========================================================= */

    private static function render_product_card(
        WP_Term $partner,
        WP_Post $product
    ): void {
        $product_id =
            (int)
            $product->ID;


        $source_id =
            self::get_product_source_id(
                $product_id
            );


        $brands =
            get_the_terms(
                $product_id,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        $brand_name =
            is_array($brands)
            &&
            ! empty($brands)
            &&
            $brands[0] instanceof WP_Term
                ? $brands[0]->name
                : '';


        $categories =
            get_the_terms(
                $product_id,
                Jung_Leben_Core_Products::TAXONOMY_CATEGORY
            );


        $category_name =
            is_array($categories)
            &&
            ! empty($categories)
            &&
            $categories[0] instanceof WP_Term
                ? $categories[0]->name
                : '';


        $description =
            trim(
                (string)
                $product->post_excerpt
            );


        $original_url =
            trim(
                (string)
                self::get_product_field(
                    'jl_product_original_url',
                    $product_id,
                    ''
                )
            );


        $config =
            self::get_partner_config(
                $partner
            );


        $primary_source_id =
            is_array($config)
                ? (string)
                $config['primary_source_id']
                : '';


        $internal_url =
            $source_id ===
            $primary_source_id
                ? self::get_demo_url(
                    $partner,
                    'produkt',
                    'magnesium-bisglycinate'
                )
                : '';
        ?>

<article class="jl-partner-demo-card">

    <div class="jl-partner-demo-card__media">

        <?php
        $image =
            self::get_product_image_html(
                $product,
                'medium_large',
                'jl-partner-demo-card__image'
            );


        if ($image !== '') {
            echo wp_kses_post(
                $image
            );
        } else {
            ?>

            <span class="jl-partner-demo-card__mark">
                <?php
                echo esc_html(
                    self::get_product_monogram(
                        $product
                    )
                );
                ?>
            </span>

            <?php
        }
        ?>

    </div>


    <div class="jl-partner-demo-card__content">

        <?php if ($brand_name !== '') : ?>

            <span class="jl-partner-demo-card__brand">
                <?php echo esc_html($brand_name); ?>
            </span>

        <?php endif; ?>


        <h3>
            <?php echo esc_html($product->post_title); ?>
        </h3>


        <?php if ($description !== '') : ?>

            <p>
                <?php
                echo esc_html(
                    wp_trim_words(
                        $description,
                        24,
                        ' …'
                    )
                );
                ?>
            </p>

        <?php endif; ?>


        <div class="jl-partner-demo-card__footer">

            <span>
                <?php echo esc_html($category_name); ?>
            </span>


            <?php if ($internal_url !== '') : ?>

                <a
                    href="<?php echo esc_url($internal_url); ?>"
                >
                    Demo ansehen →
                </a>

            <?php elseif ($original_url !== '') : ?>

                <a
                    href="<?php echo esc_url($original_url); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Bei
                    <?php echo esc_html($partner->name); ?>
                    ↗
                </a>

            <?php endif; ?>

        </div>

    </div>

</article>

        <?php
    }


    /* =========================================================
       PRODUKTIDENTITÄT
       ========================================================= */

    private static function render_product_identity(
        WP_Post $product
    ): void {
        $brands =
            get_the_terms(
                $product->ID,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );


        $brand_name =
            is_array($brands)
            &&
            ! empty($brands)
            &&
            $brands[0] instanceof WP_Term
                ? $brands[0]->name
                : '';
        ?>

<div class="jl-demo-product-identity">

    <?php if ($brand_name !== '') : ?>

        <span>
            <?php echo esc_html($brand_name); ?>
        </span>

    <?php endif; ?>


    <strong>
        <?php echo esc_html($product->post_title); ?>
    </strong>


    <?php
    if (
        trim(
            $product->post_excerpt
        )
        !==
        ''
    ) :
        ?>

        <p>
            <?php echo esc_html($product->post_excerpt); ?>
        </p>

    <?php endif; ?>

</div>

        <?php
    }


    /* =========================================================
       404
       ========================================================= */

    private static function render_not_found(): void
    {
        status_header(404);

        nocache_headers();


        header(
            'X-Robots-Tag: noindex, nofollow,noarchive',
            true
        );
        ?>

<!doctype html>

<html <?php language_attributes(); ?>>

<head>

    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="robots"
        content="noindex,nofollow,noarchive"
    >

    <title>
        Seite nicht gefunden
    </title>

</head>


<body>

<main
    style="
        max-width:700px;
        margin:100px auto;
        padding:30px;
        font-family:Arial,sans-serif;
        color:#173c32;
    "
>

    <h1>
        Seite nicht gefunden.
    </h1>

    <p>
        Die angeforderte Partner-Vorschau
        ist nicht verfügbar.
    </p>

</main>

</body>
</html>

        <?php
    }
}