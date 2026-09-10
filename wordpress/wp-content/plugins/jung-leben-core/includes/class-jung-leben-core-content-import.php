<?php
/**
 * Excel-Inhaltsimport für Jung Leben.
 *
 * Unterstützt die aktuelle Kundendatei mit den Tabellen
 * «Products» und «Blogs».
 *
 * @package Jung_Leben_Core
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

final class Jung_Leben_Core_Content_Import
{
    private const MENU_SLUG = 'jung-leben-content-import';
    private const ACTION = 'jung_leben_core_content_import';
    private const NONCE_ACTION = 'jung_leben_core_content_import';
    private const IMPORT_META_KEY = '_jl_import_key';
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;
    private const AI_FONT_COLOR = '00B0F0';

    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'register_admin_page']);
        add_action('admin_post_' . self::ACTION, [self::class, 'handle_import']);
    }

    public static function register_admin_page(): void
    {
        add_management_page(
            __('Jung Leben Import', 'jung-leben-core'),
            __('Jung Leben Import', 'jung-leben-core'),
            'manage_options',
            self::MENU_SLUG,
            [self::class, 'render_admin_page']
        );
    }

    public static function render_admin_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $result = isset($_GET['jl_import'])
            ? sanitize_key(wp_unslash($_GET['jl_import']))
            : '';

        $products = isset($_GET['products']) ? absint($_GET['products']) : 0;
        $experiences = isset($_GET['experiences']) ? absint($_GET['experiences']) : 0;
        $updated = isset($_GET['updated']) ? absint($_GET['updated']) : 0;
        $inactive = isset($_GET['inactive']) ? absint($_GET['inactive']) : 0;
        $errors = isset($_GET['errors']) ? absint($_GET['errors']) : 0;
        $ai = isset($_GET['ai']) ? absint($_GET['ai']) : 0;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Jung Leben – Excel-Import', 'jung-leben-core'); ?></h1>

            <p>
                <?php esc_html_e(
                    'Lade hier die aktuelle Kundendatei mit den Tabellen «Products» und «Blogs» hoch. Die Datei wird nur während des Imports gelesen und nicht dauerhaft gespeichert.',
                    'jung-leben-core'
                ); ?>
            </p>

            <?php if ($result === 'done') : ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <strong><?php esc_html_e('Import abgeschlossen.', 'jung-leben-core'); ?></strong>

                        <?php
                        printf(
                            esc_html__(
                                '%1$d Produkte und %2$d Beiträge wurden neu angelegt. %3$d bestehende Datensätze wurden synchronisiert. %4$d inaktive Produktzeilen wurden berücksichtigt. KI-markierte Beiträge: %5$d. Fehler: %6$d.',
                                'jung-leben-core'
                            ),
                            $products,
                            $experiences,
                            $updated,
                            $inactive,
                            $ai,
                            $errors
                        );
                        ?>
                    </p>
                </div>

            <?php elseif ($result === 'error') : ?>

                <div class="notice notice-error is-dismissible">
                    <p>
                        <?php esc_html_e(
                            'Der Import konnte nicht durchgeführt werden. Bitte prüfe die XLSX-Datei.',
                            'jung-leben-core'
                        ); ?>
                    </p>
                </div>

            <?php endif; ?>

            <div
                style="
                    max-width:920px;
                    margin-top:24px;
                    padding:24px;
                    background:#fff;
                    border:1px solid #dcdcde;
                    border-radius:8px;
                "
            >

                <h2 style="margin-top:0;">
                    <?php esc_html_e(
                        'Kundendatei importieren',
                        'jung-leben-core'
                    ); ?>
                </h2>

                <p>
                    <?php esc_html_e(
                        'Produkte mit Status «Aktiv» werden als Entwurf angelegt. Bereits importierte Produkte werden mit den Excel-Zuordnungen synchronisiert, ohne redaktionelle Inhalte oder manuell gepflegte Affiliate-Daten zu überschreiben. Produkte mit Status «Inaktiv» werden nicht neu angelegt und bestehende Import-Produkte werden auf Entwurf gesetzt.',
                        'jung-leben-core'
                    ); ?>
                </p>

                <p>
                    <?php esc_html_e(
                        'Eindeutige Altwerte aus der früheren Partnerlogik werden bei Produkten mit einem zentral hinterlegten Affiliate-Partner bereinigt. Dadurch kann beispielsweise der zentrale Buttontext «Bei Luvy ansehen» wieder greifen.',
                        'jung-leben-core'
                    ); ?>
                </p>

                <p>
                    <?php esc_html_e(
                        'Blau formatierte Blogtexte werden mit dem Prüfstatus «KI-generiert – muss geprüft werden» gekennzeichnet. Die Zeilen «Test» und «Blauer Text» werden ignoriert.',
                        'jung-leben-core'
                    ); ?>
                </p>

                <?php if (! function_exists('update_field')) : ?>

                    <div class="notice notice-error inline">
                        <p>
                            <?php esc_html_e(
                                'Advanced Custom Fields muss für den Import aktiv sein.',
                                'jung-leben-core'
                            ); ?>
                        </p>
                    </div>

                <?php else : ?>

                    <form
                        action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                        method="post"
                        enctype="multipart/form-data"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="<?php echo esc_attr(self::ACTION); ?>"
                        >

                        <?php wp_nonce_field(self::NONCE_ACTION); ?>

                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="jl-import-file">
                                            <?php esc_html_e(
                                                'Excel-Datei',
                                                'jung-leben-core'
                                            ); ?>
                                        </label>
                                    </th>

                                    <td>
                                        <input
                                            id="jl-import-file"
                                            type="file"
                                            name="jl_import_file"
                                            accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                            required
                                        >

                                        <p class="description">
                                            <?php esc_html_e(
                                                'XLSX mit den Tabellen «Products» und «Blogs». Maximale Dateigrösse: 10 MB.',
                                                'jung-leben-core'
                                            ); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        submit_button(
                            __(
                                'Inhalte importieren / synchronisieren',
                                'jung-leben-core'
                            ),
                            'primary',
                            'submit',
                            false
                        );
                        ?>

                    </form>

                <?php endif; ?>

            </div>
        </div>

        <?php
    }

    public static function handle_import(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diesen Import.',
                    'jung-leben-core'
                )
            );
        }

        check_admin_referer(self::NONCE_ACTION);

        if (! function_exists('update_field')) {
            wp_die(
                esc_html__(
                    'Advanced Custom Fields muss für den Import aktiv sein.',
                    'jung-leben-core'
                )
            );
        }

        if (
            ! isset($_FILES['jl_import_file'])
            || ! is_array($_FILES['jl_import_file'])
        ) {
            self::redirect_with_error();
        }

        $file = $_FILES['jl_import_file'];

        if (
            ! isset(
                $file['error'],
                $file['tmp_name'],
                $file['name'],
                $file['size']
            )
            || (int) $file['error'] !== UPLOAD_ERR_OK
            || ! is_uploaded_file((string) $file['tmp_name'])
        ) {
            self::redirect_with_error();
        }

        if ((int) $file['size'] > self::MAX_FILE_SIZE) {
            wp_die(
                esc_html__(
                    'Die Excel-Datei ist grösser als 10 MB.',
                    'jung-leben-core'
                )
            );
        }

        if (
            strtolower(
                pathinfo(
                    (string) $file['name'],
                    PATHINFO_EXTENSION
                )
            ) !== 'xlsx'
        ) {
            wp_die(
                esc_html__(
                    'Bitte eine XLSX-Datei hochladen.',
                    'jung-leben-core'
                )
            );
        }

        try {
            $workbook = self::read_xlsx(
                (string) $file['tmp_name']
            );

            $data = self::build_import_data(
                $workbook
            );

        } catch (Throwable $exception) {

            error_log(
                '[Jung Leben Import] '
                . sanitize_text_field(
                    $exception->getMessage()
                )
            );

            self::redirect_with_error();
        }

        if (
            empty($data['products'])
            && empty($data['experiences'])
        ) {
            self::redirect_with_error();
        }

        $result = [
            'products' => 0,
            'experiences' => 0,
            'updated' => 0,
            'inactive' => 0,
            'errors' => 0,
            'ai' => self::count_ai_experiences(
                $data['experiences']
            ),
        ];

        /**
         * Excel Produkt-ID → WordPress Produkt-ID.
         */
        $product_map = [];

        /* =====================================================
           PRODUKTE
           ===================================================== */

        foreach ($data['products'] as $product) {
            $source_id = self::canonical_source_id(
                (string) (
                    $product['source_id']
                    ?? ''
                )
            );

            $status = self::normalize_product_status(
                (string) (
                    $product['status']
                    ?? ''
                )
            );

            if (
                $source_id === ''
                || $status === ''
            ) {
                $result['errors']++;
                continue;
            }

            $existing_id = self::find_existing_product(
                $source_id
            );

            if ($existing_id > 0) {
                $product_map[$source_id] = $existing_id;

                self::migrate_product_import_identity(
                    $existing_id,
                    $source_id
                );

                self::sync_existing_product_source_data(
                    $existing_id,
                    $product
                );

                if ($status === 'inactive') {
                    self::deactivate_imported_product(
                        $existing_id
                    );

                    $result['inactive']++;
                }

                $result['updated']++;

                continue;
            }

            /**
             * Inaktive Produkte werden nicht neu angelegt.
             */
            if ($status === 'inactive') {
                $result['inactive']++;
                continue;
            }

            $product_id = self::import_product(
                $product
            );

            if ($product_id <= 0) {
                $result['errors']++;
                continue;
            }

            $product_map[$source_id] = $product_id;

            $result['products']++;
        }

        /**
         * Auch Produkte auflösen, die bereits vorhanden sind,
         * aber im aktuellen Lauf nicht neu erstellt wurden.
         */
        foreach ($data['products'] as $product) {
            $source_id = self::canonical_source_id(
                (string) (
                    $product['source_id']
                    ?? ''
                )
            );

            if (
                $source_id === ''
                || isset($product_map[$source_id])
            ) {
                continue;
            }

            $existing_id = self::find_existing_product(
                $source_id
            );

            if ($existing_id > 0) {
                $product_map[$source_id] = $existing_id;
            }
        }

        /* =====================================================
           BLOGS / BEITRÄGE
           ===================================================== */

        foreach ($data['experiences'] as $experience) {
            $topic = sanitize_text_field(
                (string) (
                    $experience['topic']
                    ?? ''
                )
            );

            if ($topic === '') {
                $result['errors']++;
                continue;
            }

            $import_key =
                'experience:'
                . sanitize_title($topic);

            $existing_id = self::find_imported_post(
                $import_key,
                'post'
            );

            if ($existing_id > 0) {
                self::sync_existing_experience_source_data(
                    $existing_id,
                    $experience,
                    $product_map
                );

                $result['updated']++;

                continue;
            }

            $experience_id = self::import_experience(
                $experience,
                $product_map,
                $import_key
            );

            if ($experience_id <= 0) {
                $result['errors']++;
                continue;
            }

            $result['experiences']++;
        }

        $url = add_query_arg(
            [
                'page' => self::MENU_SLUG,
                'jl_import' => 'done',
                'products' => $result['products'],
                'experiences' => $result['experiences'],
                'updated' => $result['updated'],
                'inactive' => $result['inactive'],
                'errors' => $result['errors'],
                'ai' => $result['ai'],
            ],
            admin_url('tools.php')
        );

        wp_safe_redirect($url);

        exit;
    }

    /* =========================================================
       XLSX
       ========================================================= */

    private static function read_xlsx(
        string $path
    ): array {
        require_once
            ABSPATH
            . 'wp-admin/includes/file.php';

        if (
            ! function_exists('WP_Filesystem')
            || ! WP_Filesystem()
        ) {
            throw new RuntimeException(
                'Das WordPress-Dateisystem konnte nicht initialisiert werden.'
            );
        }

        global $wp_filesystem;

        if (! $wp_filesystem) {
            throw new RuntimeException(
                'Das WordPress-Dateisystem ist nicht verfügbar.'
            );
        }

        $temp_dir =
            trailingslashit(
                get_temp_dir()
            )
            . 'jung-leben-xlsx-'
            . wp_generate_password(
                16,
                false,
                false
            );

        if (! wp_mkdir_p($temp_dir)) {
            throw new RuntimeException(
                'Das temporäre Importverzeichnis konnte nicht erstellt werden.'
            );
        }

        try {
            $unzipped = unzip_file(
                $path,
                $temp_dir
            );

            if (is_wp_error($unzipped)) {
                throw new RuntimeException(
                    $unzipped->get_error_message()
                );
            }

            return self::read_extracted_xlsx(
                $temp_dir
            );

        } finally {
            $wp_filesystem->delete(
                $temp_dir,
                true
            );
        }
    }

    private static function read_extracted_xlsx(
        string $base_dir
    ): array {
        $shared_strings = self::read_shared_strings(
            $base_dir
        );

        $style_colors = self::read_style_colors(
            $base_dir
        );

        $sheet_targets = self::read_sheet_targets(
            $base_dir
        );

        $result = [];

        foreach (
            [
                'Products',
                'Blogs',
            ]
            as $sheet_name
        ) {
            if (! isset($sheet_targets[$sheet_name])) {
                throw new RuntimeException(
                    sprintf(
                        'Die Tabelle «%s» wurde nicht gefunden.',
                        $sheet_name
                    )
                );
            }

            $result[$sheet_name] =
                self::read_sheet(
                    $base_dir,
                    $sheet_targets[$sheet_name],
                    $shared_strings,
                    $style_colors
                );
        }

        return $result;
    }

    private static function read_shared_strings(
        string $base_dir
    ): array {
        $path =
            trailingslashit($base_dir)
            . 'xl/sharedStrings.xml';

        if (! file_exists($path)) {
            return [];
        }

        $xml = file_get_contents($path);

        if (
            ! is_string($xml)
            || $xml === ''
        ) {
            return [];
        }

        $document = self::load_xml_document(
            $xml
        );

        $xpath = new DOMXPath(
            $document
        );

        $items = $xpath->query(
            '//*[local-name()="si"]'
        );

        $strings = [];

        if (! $items) {
            return $strings;
        }

        foreach ($items as $item) {
            $value = '';

            $nodes = $xpath->query(
                './/*[local-name()="t"]',
                $item
            );

            if ($nodes) {
                foreach ($nodes as $node) {
                    $value .= $node->textContent;
                }
            }

            $strings[] = $value;
        }

        return $strings;
    }

    private static function read_style_colors(
        string $base_dir
    ): array {
        $path =
            trailingslashit($base_dir)
            . 'xl/styles.xml';

        if (! file_exists($path)) {
            return [];
        }

        $xml = file_get_contents($path);

        if (
            ! is_string($xml)
            || $xml === ''
        ) {
            return [];
        }

        $document = self::load_xml_document(
            $xml
        );

        $xpath = new DOMXPath(
            $document
        );

        $font_colors = [];

        $fonts = $xpath->query(
            '/*[local-name()="styleSheet"]'
            . '/*[local-name()="fonts"]'
            . '/*[local-name()="font"]'
        );

        if ($fonts) {
            foreach ($fonts as $index => $font) {
                $color = '';

                $nodes = $xpath->query(
                    './*[local-name()="color"]',
                    $font
                );

                if (
                    $nodes
                    && $nodes->length > 0
                ) {
                    $node = $nodes->item(0);

                    if (
                        $node instanceof DOMElement
                        && $node->hasAttribute('rgb')
                    ) {
                        $color = substr(
                            strtoupper(
                                $node->getAttribute('rgb')
                            ),
                            -6
                        );
                    }
                }

                $font_colors[(int) $index] = $color;
            }
        }

        $style_colors = [];

        $xfs = $xpath->query(
            '/*[local-name()="styleSheet"]'
            . '/*[local-name()="cellXfs"]'
            . '/*[local-name()="xf"]'
        );

        if ($xfs) {
            foreach ($xfs as $index => $xf) {
                $font_id =
                    (
                        $xf instanceof DOMElement
                        && $xf->hasAttribute('fontId')
                    )
                        ? (int) $xf->getAttribute('fontId')
                        : 0;

                $style_colors[(int) $index] =
                    $font_colors[$font_id]
                    ?? '';
            }
        }

        return $style_colors;
    }

    private static function read_sheet_targets(
        string $base_dir
    ): array {
        $workbook_path =
            trailingslashit($base_dir)
            . 'xl/workbook.xml';

        $rels_path =
            trailingslashit($base_dir)
            . 'xl/_rels/workbook.xml.rels';

        if (
            ! file_exists($workbook_path)
            || ! file_exists($rels_path)
        ) {
            throw new RuntimeException(
                'Die Excel-Arbeitsmappe ist unvollständig.'
            );
        }

        $workbook_xml =
            file_get_contents(
                $workbook_path
            );

        $rels_xml =
            file_get_contents(
                $rels_path
            );

        if (
            ! is_string($workbook_xml)
            || ! is_string($rels_xml)
        ) {
            throw new RuntimeException(
                'Die Excel-Arbeitsmappe konnte nicht gelesen werden.'
            );
        }

        $rels_document =
            self::load_xml_document(
                $rels_xml
            );

        $rels_xpath =
            new DOMXPath(
                $rels_document
            );

        $relationships = [];

        $nodes = $rels_xpath->query(
            '//*[local-name()="Relationship"]'
        );

        if ($nodes) {
            foreach ($nodes as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                $id =
                    $node->getAttribute('Id');

                $target =
                    $node->getAttribute('Target');

                if (
                    $id !== ''
                    && $target !== ''
                ) {
                    $relationships[$id] =
                        self::normalize_xlsx_target(
                            $target
                        );
                }
            }
        }

        $workbook_document =
            self::load_xml_document(
                $workbook_xml
            );

        $xpath = new DOMXPath(
            $workbook_document
        );

        $sheets = $xpath->query(
            '//*[local-name()="sheet"]'
        );

        $targets = [];

        if ($sheets) {
            foreach ($sheets as $sheet) {
                if (! $sheet instanceof DOMElement) {
                    continue;
                }

                $name =
                    $sheet->getAttribute(
                        'name'
                    );

                $relationship_id =
                    $sheet->getAttributeNS(
                        'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                        'id'
                    );

                if (
                    $name !== ''
                    && isset(
                        $relationships[$relationship_id]
                    )
                ) {
                    $targets[$name] =
                        $relationships[$relationship_id];
                }
            }
        }

        return $targets;
    }

    private static function read_sheet(
        string $base_dir,
        string $target,
        array $shared_strings,
        array $style_colors
    ): array {
        $path =
            trailingslashit($base_dir)
            . ltrim(
                $target,
                '/'
            );

        if (! file_exists($path)) {
            throw new RuntimeException(
                'Eine Excel-Tabelle konnte nicht gefunden werden.'
            );
        }

        $xml =
            file_get_contents(
                $path
            );

        if (
            ! is_string($xml)
            || $xml === ''
        ) {
            throw new RuntimeException(
                'Eine Excel-Tabelle konnte nicht gelesen werden.'
            );
        }

        $document =
            self::load_xml_document(
                $xml
            );

        $xpath =
            new DOMXPath(
                $document
            );

        $cells =
            $xpath->query(
                '//*[local-name()="sheetData"]'
                . '//*[local-name()="c"]'
            );

        $rows = [];

        if ($cells) {
            foreach ($cells as $cell) {
                if (! $cell instanceof DOMElement) {
                    continue;
                }

                $reference =
                    $cell->getAttribute(
                        'r'
                    );

                if (
                    ! preg_match(
                        '/^([A-Z]+)(\d+)$/',
                        $reference,
                        $matches
                    )
                ) {
                    continue;
                }

                $column = $matches[1];
                $row_number = (int) $matches[2];

                $type =
                    $cell->getAttribute(
                        't'
                    );

                $style_id =
                    $cell->hasAttribute('s')
                        ? (int) $cell->getAttribute('s')
                        : 0;

                $value = '';

                if ($type === 'inlineStr') {
                    $nodes =
                        $xpath->query(
                            './/*[local-name()="is"]'
                            . '//*[local-name()="t"]',
                            $cell
                        );

                    if ($nodes) {
                        foreach ($nodes as $node) {
                            $value .= $node->textContent;
                        }
                    }

                } else {
                    $nodes =
                        $xpath->query(
                            './*[local-name()="v"]',
                            $cell
                        );

                    $raw =
                        (
                            $nodes
                            && $nodes->length > 0
                        )
                            ? $nodes->item(0)->textContent
                            : '';

                    $value =
                        $type === 's'
                            ? (
                                $shared_strings[(int) $raw]
                                ?? ''
                            )
                            : $raw;
                }

                $rows[$row_number][$column] = [
                    'value' => $value,
                    'font_color' =>
                        $style_colors[$style_id]
                        ?? '',
                    'hyperlink' => '',
                ];
            }
        }

        /**
         * Tatsächliche Excel-Hyperlinks ergänzen.
         */
        $relationships =
            self::read_sheet_external_relationships(
                $base_dir,
                $target
            );

        $hyperlinks =
            $xpath->query(
                '//*[local-name()="hyperlinks"]'
                . '/*[local-name()="hyperlink"]'
            );

        if ($hyperlinks) {
            foreach ($hyperlinks as $hyperlink) {
                if (! $hyperlink instanceof DOMElement) {
                    continue;
                }

                $reference =
                    $hyperlink->getAttribute(
                        'ref'
                    );

                $relationship_id =
                    $hyperlink->getAttributeNS(
                        'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                        'id'
                    );

                if (
                    $reference === ''
                    || ! isset(
                        $relationships[$relationship_id]
                    )
                    || ! preg_match(
                        '/^([A-Z]+)(\d+)$/',
                        $reference,
                        $matches
                    )
                ) {
                    continue;
                }

                $column = $matches[1];
                $row_number = (int) $matches[2];

                if (! isset($rows[$row_number][$column])) {
                    $rows[$row_number][$column] = [
                        'value' => '',
                        'font_color' => '',
                        'hyperlink' => '',
                    ];
                }

                $rows[$row_number][$column]['hyperlink'] =
                    $relationships[$relationship_id];
            }
        }

        ksort($rows);

        return $rows;
    }

    private static function read_sheet_external_relationships(
        string $base_dir,
        string $sheet_target
    ): array {
        $sheet_target =
            str_replace(
                '\\',
                '/',
                ltrim(
                    $sheet_target,
                    '/'
                )
            );

        $rels_path =
            trailingslashit($base_dir)
            . dirname($sheet_target)
            . '/_rels/'
            . basename($sheet_target)
            . '.rels';

        if (! file_exists($rels_path)) {
            return [];
        }

        $xml =
            file_get_contents(
                $rels_path
            );

        if (
            ! is_string($xml)
            || $xml === ''
        ) {
            return [];
        }

        $document =
            self::load_xml_document(
                $xml
            );

        $xpath =
            new DOMXPath(
                $document
            );

        $nodes =
            $xpath->query(
                '//*[local-name()="Relationship"]'
            );

        $relationships = [];

        if (! $nodes) {
            return $relationships;
        }

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $id =
                $node->getAttribute(
                    'Id'
                );

            $type =
                $node->getAttribute(
                    'Type'
                );

            $target =
                $node->getAttribute(
                    'Target'
                );

            $mode =
                $node->getAttribute(
                    'TargetMode'
                );

            if (
                $id !== ''
                && $target !== ''
                && $mode === 'External'
                && str_ends_with(
                    $type,
                    '/hyperlink'
                )
            ) {
                $relationships[$id] = $target;
            }
        }

        return $relationships;
    }

    /* =========================================================
       EXCEL → IMPORTDATEN
       ========================================================= */

    private static function build_import_data(
        array $workbook
    ): array {
        $products =
            self::build_products(
                $workbook['Products']
                ?? []
            );

        $experiences =
            self::build_experiences(
                $workbook['Blogs']
                ?? [],
                $products
            );

        $products =
            self::apply_experience_flags_to_products(
                $products,
                $experiences
            );

        return [
            'products' => $products,
            'experiences' => $experiences,
        ];
    }

    private static function build_products(
        array $rows
    ): array {
        $header_row_number =
            self::find_header_row(
                $rows,
                [
                    'id',
                    'produkt',
                    'status',
                ]
            );

        if ($header_row_number <= 0) {
            throw new RuntimeException(
                'Die Kopfzeile der Tabelle «Products» wurde nicht gefunden.'
            );
        }

        $columns =
            self::build_product_column_map(
                $rows[$header_row_number]
                ?? []
            );

        $required = [
            'id',
            'product',
            'area',
            'description',
            'status',
            'favorite',
            'main_reference',
            'main_remark',
            'alternative_1',
            'alternative_1_reference',
            'alternative_1_remark',
            'alternative_2',
            'alternative_2_reference',
        ];

        foreach ($required as $key) {
            if (! isset($columns[$key])) {
                throw new RuntimeException(
                    sprintf(
                        'Die erwartete Spalte «%s» in «Products» wurde nicht gefunden.',
                        $key
                    )
                );
            }
        }

        $products = [];

        foreach (
            $rows
            as $row_number => $row
        ) {
            if (
                (int) $row_number
                <= $header_row_number
            ) {
                continue;
            }

            $source_id =
                self::canonical_source_id(
                    self::cell_value(
                        $row,
                        $columns['id']
                    )
                );

            $name =
                self::cell_value(
                    $row,
                    $columns['product']
                );

            if (
                $source_id === ''
                || $name === ''
            ) {
                continue;
            }

            $products[] = [
                'source_id' => $source_id,

                'name' => $name,

                'area' =>
                    self::cell_value(
                        $row,
                        $columns['area']
                    ),

                'description' =>
                    self::cell_value(
                        $row,
                        $columns['description']
                    ),

                'status' =>
                    self::normalize_product_status(
                        self::cell_value(
                            $row,
                            $columns['status']
                        )
                    ),

                'favorite_brand' =>
                    self::cell_value(
                        $row,
                        $columns['favorite']
                    ),

                'favorite_reference' =>
                    self::cell_value(
                        $row,
                        $columns['main_reference']
                    ),

                'favorite_url' =>
                    self::cell_effective_url(
                        $row,
                        $columns['main_reference']
                    ),

                'remark' =>
                    self::cell_value(
                        $row,
                        $columns['main_remark']
                    ),

                'alternative_1_brand' =>
                    self::cell_value(
                        $row,
                        $columns['alternative_1']
                    ),

                'alternative_1_reference' =>
                    self::cell_value(
                        $row,
                        $columns['alternative_1_reference']
                    ),

                'alternative_1_url' =>
                    self::cell_effective_url(
                        $row,
                        $columns['alternative_1_reference']
                    ),

                'alternative_1_remark' =>
                    self::cell_value(
                        $row,
                        $columns['alternative_1_remark']
                    ),

                'alternative_2_brand' =>
                    self::cell_value(
                        $row,
                        $columns['alternative_2']
                    ),

                'alternative_2_reference' =>
                    self::cell_value(
                        $row,
                        $columns['alternative_2_reference']
                    ),

                'alternative_2_url' =>
                    self::cell_effective_url(
                        $row,
                        $columns['alternative_2_reference']
                    ),

                'personally_tested' => false,

                'source_row' =>
                    (int) $row_number,
            ];
        }

        return $products;
    }

    private static function build_product_column_map(
        array $header_row
    ): array {
        $map = [];

        $url_count = 0;
        $remark_count = 0;

        foreach (
            $header_row
            as $column => $cell
        ) {
            $label =
                self::normalize_header(
                    is_array($cell)
                        ? (string) (
                            $cell['value']
                            ?? ''
                        )
                        : ''
                );

            switch ($label) {
                case 'id':
                    $map['id'] = $column;
                    break;

                case 'produkt':
                    $map['product'] = $column;
                    break;

                case 'gebiet':
                    $map['area'] = $column;
                    break;

                case 'beschrieb wirkung':
                    $map['description'] = $column;
                    break;

                case 'status':
                    $map['status'] = $column;
                    break;

                case 'roberto s favorit':
                case 'robertos favorit':
                    $map['favorite'] = $column;
                    break;

                case 'alternative 1':
                    $map['alternative_1'] = $column;
                    break;

                case 'alternative 2':
                    $map['alternative_2'] = $column;
                    break;

                case 'url':
                    $url_count++;

                    if ($url_count === 1) {
                        $map['main_reference'] = $column;

                    } elseif ($url_count === 2) {
                        $map['alternative_1_reference'] = $column;

                    } elseif ($url_count === 3) {
                        $map['alternative_2_reference'] = $column;
                    }

                    break;

                case 'bemerkung':
                    $remark_count++;

                    if ($remark_count === 1) {
                        $map['main_remark'] = $column;

                    } elseif ($remark_count === 2) {
                        $map['alternative_1_remark'] = $column;
                    }

                    break;
            }
        }

        return $map;
    }

    private static function build_experiences(
        array $rows,
        array $products
    ): array {
        $header_row_number =
            self::find_header_row(
                $rows,
                [
                    'thema',
                    'blog',
                    'wirkungsbeschrieb',
                ]
            );

        if ($header_row_number <= 0) {
            throw new RuntimeException(
                'Die Kopfzeile der Tabelle «Blogs» wurde nicht gefunden.'
            );
        }

        $columns =
            self::build_simple_column_map(
                $rows[$header_row_number]
                ?? []
            );

        if (
            ! isset(
                $columns['thema'],
                $columns['blog'],
                $columns['wirkungsbeschrieb']
            )
        ) {
            throw new RuntimeException(
                'Die erwarteten Spalten in «Blogs» wurden nicht gefunden.'
            );
        }

        $product_map =
            self::build_product_source_map(
                $products
            );

        $experiences = [];

        foreach (
            $rows
            as $row_number => $row
        ) {
            if (
                (int) $row_number
                <= $header_row_number
            ) {
                continue;
            }

            $topic =
                self::cell_value(
                    $row,
                    $columns['thema']
                );

            $normalized_topic =
                self::normalize_match_text(
                    $topic
                );

            if (
                $topic === ''
                || in_array(
                    $normalized_topic,
                    [
                        'test',
                        'blauer text',
                    ],
                    true
                )
            ) {
                continue;
            }

            $blog =
                self::cell_value(
                    $row,
                    $columns['blog']
                );

            $background =
                self::cell_value(
                    $row,
                    $columns['wirkungsbeschrieb']
                );

            if (
                $blog === ''
                && $background === ''
            ) {
                continue;
            }

            [
                $title,
                $body,
            ] = self::split_title_and_body(
                $blog,
                $topic
            );

            [
                $background_title,
                $background_body,
            ] = self::split_title_and_body(
                $background,
                ''
            );

            /**
             * Blaue Excel-Schrift = noch zu prüfender KI-Inhalt.
             */
            $is_ai =
                self::cell_font_color(
                    $row,
                    $columns['blog']
                ) === self::AI_FONT_COLOR
                ||
                self::cell_font_color(
                    $row,
                    $columns['wirkungsbeschrieb']
                ) === self::AI_FONT_COLOR;

            $experiences[] = [
                'topic' => $topic,

                'title' => $title,

                'body' => $body,

                'background_title' =>
                    $background_title,

                'background' =>
                    $background_body,

                'review_status' =>
                    $is_ai
                        ? 'ai_review'
                        : 'customer_source',

                'related_product_source_id' =>
                    self::match_product_source_id(
                        $topic,
                        $product_map
                    ),

                'source_row' =>
                    (int) $row_number,
            ];
        }

        return $experiences;
    }

    private static function build_simple_column_map(
        array $header_row
    ): array {
        $map = [];

        foreach (
            $header_row
            as $column => $cell
        ) {
            $label =
                self::normalize_header(
                    is_array($cell)
                        ? (string) (
                            $cell['value']
                            ?? ''
                        )
                        : ''
                );

            if ($label !== '') {
                $map[$label] = $column;
            }
        }

        return $map;
    }

    private static function find_header_row(
        array $rows,
        array $required_labels
    ): int {
        foreach (
            $rows
            as $row_number => $row
        ) {
            $labels = [];

            foreach ($row as $cell) {
                if (! is_array($cell)) {
                    continue;
                }

                $label =
                    self::normalize_header(
                        (string) (
                            $cell['value']
                            ?? ''
                        )
                    );

                if ($label !== '') {
                    $labels[] = $label;
                }
            }

            if (
                count(
                    array_intersect(
                        $required_labels,
                        $labels
                    )
                )
                === count($required_labels)
            ) {
                return (int) $row_number;
            }
        }

        return 0;
    }

    private static function normalize_header(
        string $value
    ): string {
        return self::normalize_match_text(
            str_replace(
                [
                    "'",
                    '’',
                ],
                ' ',
                $value
            )
        );
    }

    private static function build_product_source_map(
        array $products
    ): array {
        $map = [];

        foreach ($products as $product) {
            $source_id =
                self::canonical_source_id(
                    (string) (
                        $product['source_id']
                        ?? ''
                    )
                );

            $name =
                (string) (
                    $product['name']
                    ?? ''
                );

            if (
                $source_id !== ''
                && $name !== ''
            ) {
                $map[
                    self::normalize_match_text(
                        $name
                    )
                ] = $source_id;
            }
        }

        return $map;
    }

    private static function match_product_source_id(
        string $topic,
        array $product_map
    ): string {
        $normalized =
            self::normalize_match_text(
                $topic
            );

        if (isset($product_map[$normalized])) {
            return $product_map[$normalized];
        }

        /**
         * Schreibweisen aus der Kundendatei.
         */
        $aliases = [
            'deo roll on' => '003',
            'hydroxiapatite zahnpasta' => '002',
            'hydroxyapatite zahnpasta' => '002',
            'l tryptophan' => '023',
            'omega 3 6 9' => '004',
            'ashwaganda' => '005',
            'ashwagandha' => '005',
        ];

        return
            $aliases[$normalized]
            ?? '';
    }

    private static function apply_experience_flags_to_products(
        array $products,
        array $experiences
    ): array {
        $tested = [];

        foreach ($experiences as $experience) {
            /**
             * Nur echte, nicht blau markierte Kunden-/Roberto-
             * Quellen gelten automatisch als persönlich getestet.
             */
            if (
                (
                    $experience['review_status']
                    ?? ''
                )
                !== 'customer_source'
            ) {
                continue;
            }

            $source_id =
                self::canonical_source_id(
                    (string) (
                        $experience['related_product_source_id']
                        ?? ''
                    )
                );

            if ($source_id !== '') {
                $tested[$source_id] = true;
            }
        }

        foreach (
            $products
            as $index => $product
        ) {
            $source_id =
                self::canonical_source_id(
                    (string) (
                        $product['source_id']
                        ?? ''
                    )
                );

            $products[$index]['personally_tested'] =
                isset($tested[$source_id]);
        }

        return $products;
    }

    /* =========================================================
       PRODUKTE
       ========================================================= */

    private static function import_product(
        array $product
    ): int {
        $name =
            sanitize_text_field(
                (string) (
                    $product['name']
                    ?? ''
                )
            );

        $source_id =
            self::canonical_source_id(
                (string) (
                    $product['source_id']
                    ?? ''
                )
            );

        $description =
            sanitize_textarea_field(
                (string) (
                    $product['description']
                    ?? ''
                )
            );

        if (
            $name === ''
            || $source_id === ''
        ) {
            return 0;
        }

        $post_id =
            wp_insert_post(
                [
                    'post_type' =>
                        Jung_Leben_Core_Products::POST_TYPE,

                    'post_status' =>
                        'draft',

                    'post_title' =>
                        $name,

                    'post_excerpt' =>
                        $description,

                    'post_content' =>
                        self::text_to_blocks(
                            $description
                        ),

                    'post_author' =>
                        get_current_user_id(),
                ],
                true
            );

        if (is_wp_error($post_id)) {
            return 0;
        }

        $post_id =
            (int) $post_id;

        self::migrate_product_import_identity(
            $post_id,
            $source_id
        );

        self::sync_product_source_meta(
            $post_id,
            $product
        );

        self::sync_product_taxonomies(
            $post_id,
            $product
        );

        /* -----------------------------------------------------
           Einordnung
           ----------------------------------------------------- */

        update_field(
            'jl_product_recommendation_status',
            'neutral',
            $post_id
        );

        update_field(
            'jl_product_personally_tested',
            ! empty(
                $product['personally_tested']
            )
                ? 1
                : 0,
            $post_id
        );

        update_field(
            'jl_product_featured',
            0,
            $post_id
        );

        update_field(
            'jl_product_sort_priority',
            0,
            $post_id
        );

        /* -----------------------------------------------------
           Anwendung / Erfahrung
           ----------------------------------------------------- */

        update_field(
            'jl_product_purpose',
            '',
            $post_id
        );

        update_field(
            'jl_product_routine_time',
            [],
            $post_id
        );

        update_field(
            'jl_product_personal_experience',
            '',
            $post_id
        );

        update_field(
            'jl_product_benefits',
            '',
            $post_id
        );

        update_field(
            'jl_product_limitations',
            '',
            $post_id
        );

        /* -----------------------------------------------------
           Kauf- und Partnerdaten
           ----------------------------------------------------- */

        /**
         * Neue Partnerlogik:
         *
         * Produkt → Marke → Affiliate-Partner.
         *
         * Deshalb keine alten Partner-Freitexte erzeugen.
         */
        update_field(
            'jl_product_partner_name',
            '',
            $post_id
        );

        update_field(
            'jl_product_original_url',
            esc_url_raw(
                (string) (
                    $product['favorite_url']
                    ?? ''
                )
            ),
            $post_id
        );

        /**
         * Excel-URLs sind nicht automatisch Affiliate-Links.
         */
        update_field(
            'jl_product_affiliate_url',
            '',
            $post_id
        );

        update_field(
            'jl_product_price_display',
            '',
            $post_id
        );

        update_field(
            'jl_product_discount_code',
            '',
            $post_id
        );

        /**
         * Leer lassen:
         * zentraler Partnerbutton darf greifen.
         */
        update_field(
            'jl_product_button_text',
            '',
            $post_id
        );

        update_field(
            'jl_product_link_new_tab',
            1,
            $post_id
        );

        /* -----------------------------------------------------
           Transparenz
           ----------------------------------------------------- */

        update_field(
            'jl_product_affiliate_notice',
            '',
            $post_id
        );

        update_field(
            'jl_product_health_notice',
            __(
                'Die Informationen auf Jung Leben dienen der allgemeinen Orientierung und ersetzen keine medizinische Beratung.',
                'jung-leben-core'
            ),
            $post_id
        );

        /**
         * Tatsächliches ACF-Feld für die interne Quellennotiz.
         */
        update_field(
            'jl_product_source_note',
            self::build_product_source_note(
                $product
            ),
            $post_id
        );

        return $post_id;
    }

    private static function sync_existing_product_source_data(
        int $post_id,
        array $product
    ): void {
        /**
         * Zuerst Marke aktualisieren.
         * Danach kann geprüft werden, ob ein zentraler Partner
         * vorhanden ist.
         */
        self::sync_product_taxonomies(
            $post_id,
            $product
        );

        /**
         * Altbestand aus früheren Importversionen bereinigen.
         */
        self::migrate_legacy_product_purchase_fields(
            $post_id
        );

        self::sync_product_source_meta(
            $post_id,
            $product
        );

        /**
         * Persönlich getestet wird aus den echten
         * Kunden-/Roberto-Beiträgen synchronisiert.
         */
        update_field(
            'jl_product_personally_tested',
            ! empty(
                $product['personally_tested']
            )
                ? 1
                : 0,
            $post_id
        );

        /**
         * Bestehende manuell gepflegte URL schützen.
         * Nur ergänzen, wenn noch keine direkte URL vorhanden ist.
         */
        $current_original_url =
            function_exists('get_field')
                ? trim(
                    (string) get_field(
                        'jl_product_original_url',
                        $post_id
                    )
                )
                : trim(
                    (string) get_post_meta(
                        $post_id,
                        'jl_product_original_url',
                        true
                    )
                );

        $favorite_url =
            trim(
                (string) (
                    $product['favorite_url']
                    ?? ''
                )
            );

        if (
            $current_original_url === ''
            && $favorite_url !== ''
        ) {
            update_field(
                'jl_product_original_url',
                esc_url_raw(
                    $favorite_url
                ),
                $post_id
            );
        }

        /**
         * Interne Excel-Daten sichtbar im ACF-Feld halten.
         */
        update_field(
            'jl_product_source_note',
            self::build_product_source_note(
                $product
            ),
            $post_id
        );
    }

    /**
     * Alte Standardwerte entfernen, welche die neue
     * Partnervererbung blockieren.
     *
     * Individuelle Angaben werden NICHT gelöscht.
     */
    private static function migrate_legacy_product_purchase_fields(
        int $post_id
    ): void {
        /**
         * Nur bereinigen, wenn tatsächlich eine zentrale
         * Partnerverknüpfung vorhanden ist.
         */
        if (! self::product_has_central_partner($post_id)) {
            return;
        }

        $button_text =
            function_exists('get_field')
                ? trim(
                    (string) get_field(
                        'jl_product_button_text',
                        $post_id
                    )
                )
                : trim(
                    (string) get_post_meta(
                        $post_id,
                        'jl_product_button_text',
                        true
                    )
                );

        /**
         * Nur exakt den früheren Jung-Leben-Standard entfernen.
         *
         * Eigene Buttontexte bleiben erhalten.
         */
        if (
            $button_text
            === 'Produkt beim Partner ansehen'
        ) {
            update_field(
                'jl_product_button_text',
                '',
                $post_id
            );
        }

        $legacy_partner_name =
            function_exists('get_field')
                ? trim(
                    (string) get_field(
                        'jl_product_partner_name',
                        $post_id
                    )
                )
                : trim(
                    (string) get_post_meta(
                        $post_id,
                        'jl_product_partner_name',
                        true
                    )
                );

        /**
         * Das alte Freitextfeld wird nicht mehr benötigt,
         * wenn Marke → Partner vorhanden ist.
         */
        if ($legacy_partner_name !== '') {
            update_field(
                'jl_product_partner_name',
                '',
                $post_id
            );
        }
    }

    private static function product_has_central_partner(
        int $post_id
    ): bool {
        /**
         * Aktuelle zentrale Produktlogik bevorzugen.
         */
        if (
            class_exists(
                'Jung_Leben_Core_Product_Fields'
            )
            && method_exists(
                'Jung_Leben_Core_Product_Fields',
                'get_partner_id'
            )
        ) {
            return
                Jung_Leben_Core_Product_Fields
                    ::get_partner_id(
                        $post_id
                    ) > 0;
        }

        /**
         * Fallback direkt über die Marke.
         */
        $brands =
            get_the_terms(
                $post_id,
                Jung_Leben_Core_Products::TAXONOMY_BRAND
            );

        if (
            ! is_array($brands)
            || is_wp_error($brands)
            || empty($brands)
            || ! $brands[0] instanceof WP_Term
        ) {
            return false;
        }

        $brand = $brands[0];

        if (function_exists('get_field')) {
            $partner =
                get_field(
                    'jl_brand_affiliate_partner',
                    'term_' . $brand->term_id
                );

            if ($partner instanceof WP_Post) {
                return $partner->ID > 0;
            }

            if (is_numeric($partner)) {
                return absint($partner) > 0;
            }
        }

        return
            absint(
                get_term_meta(
                    $brand->term_id,
                    'jl_brand_affiliate_partner',
                    true
                )
            ) > 0;
    }

    private static function sync_product_source_meta(
        int $post_id,
        array $product
    ): void {
        $source_id =
            self::canonical_source_id(
                (string) (
                    $product['source_id']
                    ?? ''
                )
            );

        update_post_meta(
            $post_id,
            '_jl_source_product_id',
            $source_id
        );

        update_post_meta(
            $post_id,
            '_jl_source_sheet',
            'Products'
        );

        update_post_meta(
            $post_id,
            '_jl_source_row',
            absint(
                $product['source_row']
                ?? 0
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_product_status',
            sanitize_key(
                (string) (
                    $product['status']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_favorite_brand',
            sanitize_text_field(
                (string) (
                    $product['favorite_brand']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_favorite_reference',
            sanitize_textarea_field(
                (string) (
                    $product['favorite_reference']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_favorite_url',
            esc_url_raw(
                (string) (
                    $product['favorite_url']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_product_remark',
            sanitize_textarea_field(
                (string) (
                    $product['remark']
                    ?? ''
                )
            )
        );

        /* -----------------------------------------------------
           Alternative 1
           ----------------------------------------------------- */

        update_post_meta(
            $post_id,
            '_jl_source_alternative_1_brand',
            sanitize_text_field(
                (string) (
                    $product['alternative_1_brand']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_alternative_1_reference',
            sanitize_textarea_field(
                (string) (
                    $product['alternative_1_reference']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_alternative_1_url',
            esc_url_raw(
                (string) (
                    $product['alternative_1_url']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_alternative_1_remark',
            sanitize_textarea_field(
                (string) (
                    $product['alternative_1_remark']
                    ?? ''
                )
            )
        );

        /* -----------------------------------------------------
           Alternative 2
           ----------------------------------------------------- */

        update_post_meta(
            $post_id,
            '_jl_source_alternative_2_brand',
            sanitize_text_field(
                (string) (
                    $product['alternative_2_brand']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_alternative_2_reference',
            sanitize_textarea_field(
                (string) (
                    $product['alternative_2_reference']
                    ?? ''
                )
            )
        );

        update_post_meta(
            $post_id,
            '_jl_source_alternative_2_url',
            esc_url_raw(
                (string) (
                    $product['alternative_2_url']
                    ?? ''
                )
            )
        );
    }

    private static function sync_product_taxonomies(
        int $post_id,
        array $product
    ): void {
        $favorite_brand =
            sanitize_text_field(
                (string) (
                    $product['favorite_brand']
                    ?? ''
                )
            );

        $area =
            sanitize_text_field(
                (string) (
                    $product['area']
                    ?? ''
                )
            );

        if ($favorite_brand !== '') {
            wp_set_object_terms(
                $post_id,
                [
                    $favorite_brand,
                ],
                Jung_Leben_Core_Products::TAXONOMY_BRAND,
                false
            );
        }

        if ($area !== '') {
            wp_set_object_terms(
                $post_id,
                [
                    $area,
                ],
                Jung_Leben_Core_Products::TAXONOMY_CATEGORY,
                false
            );
        }
    }

    private static function migrate_product_import_identity(
        int $post_id,
        string $source_id
    ): void {
        $source_id =
            self::canonical_source_id(
                $source_id
            );

        if ($source_id === '') {
            return;
        }

        update_post_meta(
            $post_id,
            self::IMPORT_META_KEY,
            'product:' . $source_id
        );

        update_post_meta(
            $post_id,
            '_jl_source_product_id',
            $source_id
        );
    }

    private static function deactivate_imported_product(
        int $post_id
    ): void {
        $status =
            get_post_status(
                $post_id
            );

        if (
            ! is_string($status)
            || in_array(
                $status,
                [
                    'draft',
                    'trash',
                    'auto-draft',
                ],
                true
            )
        ) {
            return;
        }

        wp_update_post([
            'ID' => $post_id,
            'post_status' => 'draft',
        ]);
    }

    /* =========================================================
       BLOGS / ERFAHRUNGEN
       ========================================================= */

    private static function import_experience(
        array $experience,
        array $product_map,
        string $import_key
    ): int {
        $title =
            sanitize_text_field(
                (string) (
                    $experience['title']
                    ?? ''
                )
            );

        if ($title === '') {
            return 0;
        }

        $body =
            sanitize_textarea_field(
                (string) (
                    $experience['body']
                    ?? ''
                )
            );

        $background_title =
            sanitize_text_field(
                (string) (
                    $experience['background_title']
                    ?? ''
                )
            );

        $background =
            sanitize_textarea_field(
                (string) (
                    $experience['background']
                    ?? ''
                )
            );

        $content =
            self::text_to_blocks(
                $body
            );

        if ($background_title !== '') {
            $content .=
                "\n\n"
                . "<!-- wp:heading -->\n"
                . '<h2 class="wp-block-heading">'
                . esc_html(
                    $background_title
                )
                . "</h2>\n"
                . "<!-- /wp:heading -->";
        }

        if ($background !== '') {
            $content .=
                "\n\n"
                . self::text_to_blocks(
                    $background
                );
        }

        $post_id =
            wp_insert_post(
                [
                    'post_type' =>
                        'post',

                    'post_status' =>
                        'draft',

                    'post_title' =>
                        $title,

                    'post_excerpt' =>
                        wp_trim_words(
                            $body !== ''
                                ? $body
                                : $background,
                            32,
                            ' …'
                        ),

                    'post_content' =>
                        $content,

                    'post_author' =>
                        get_current_user_id(),
                ],
                true
            );

        if (is_wp_error($post_id)) {
            return 0;
        }

        $post_id =
            (int) $post_id;

        update_post_meta(
            $post_id,
            self::IMPORT_META_KEY,
            $import_key
        );

        update_post_meta(
            $post_id,
            '_jl_source_sheet',
            'Blogs'
        );

        update_post_meta(
            $post_id,
            '_jl_source_row',
            absint(
                $experience['source_row']
                ?? 0
            )
        );

        self::assign_experience_category(
            $post_id
        );

        self::sync_experience_fields(
            $post_id,
            $experience,
            $product_map
        );

        return $post_id;
    }

    private static function sync_existing_experience_source_data(
        int $post_id,
        array $experience,
        array $product_map
    ): void {
        update_post_meta(
            $post_id,
            '_jl_source_sheet',
            'Blogs'
        );

        update_post_meta(
            $post_id,
            '_jl_source_row',
            absint(
                $experience['source_row']
                ?? 0
            )
        );

        self::assign_experience_category(
            $post_id
        );

        self::sync_experience_fields(
            $post_id,
            $experience,
            $product_map
        );
    }

    private static function sync_experience_fields(
        int $post_id,
        array $experience,
        array $product_map
    ): void {
        $review_status =
            sanitize_key(
                (string) (
                    $experience['review_status']
                    ?? 'customer_source'
                )
            );

        if (
            ! in_array(
                $review_status,
                [
                    'customer_source',
                    'ai_review',
                    'reviewed',
                ],
                true
            )
        ) {
            $review_status =
                'customer_source';
        }

        update_field(
            'jl_experience_review_status',
            $review_status,
            $post_id
        );

        update_field(
            'jl_experience_source_topic',
            sanitize_text_field(
                (string) (
                    $experience['topic']
                    ?? ''
                )
            ),
            $post_id
        );

        $related_source_id =
            self::canonical_source_id(
                (string) (
                    $experience['related_product_source_id']
                    ?? ''
                )
            );

        if (
            $related_source_id !== ''
            && isset(
                $product_map[$related_source_id]
            )
        ) {
            update_field(
                'jl_experience_related_product',
                (int) $product_map[
                    $related_source_id
                ],
                $post_id
            );
        }
    }

    private static function assign_experience_category(
        int $post_id
    ): void {
        $category =
            term_exists(
                'Erfahrungen',
                'category'
            );

        if (! $category) {
            $category =
                wp_insert_term(
                    'Erfahrungen',
                    'category'
                );
        }

        if (is_wp_error($category)) {
            return;
        }

        $category_id =
            is_array($category)
                ? (int) (
                    $category['term_id']
                    ?? 0
                )
                : (int) $category;

        if ($category_id > 0) {
            wp_set_post_categories(
                $post_id,
                [
                    $category_id,
                ],
                false
            );
        }
    }

    /* =========================================================
       DUPLIKATE / IDS
       ========================================================= */

    private static function find_existing_product(
        string $source_id
    ): int {
        $source_id =
            self::canonical_source_id(
                $source_id
            );

        if ($source_id === '') {
            return 0;
        }

        /**
         * Neue und alte Import-ID unterstützen.
         */
        $candidates = [
            'product:' . $source_id,
            'product:P_RO_' . $source_id,
        ];

        foreach ($candidates as $key) {
            $post_id =
                self::find_imported_post(
                    $key,
                    Jung_Leben_Core_Products::POST_TYPE
                );

            if ($post_id > 0) {
                return $post_id;
            }
        }

        /**
         * Zusätzlicher Fallback über die frühere
         * Quell-ID.
         */
        $ids =
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
                    1,

                'fields' =>
                    'ids',

                'meta_query' => [
                    'relation' => 'OR',

                    [
                        'key' =>
                            '_jl_source_product_id',

                        'value' =>
                            $source_id,

                        'compare' =>
                            '=',
                    ],

                    [
                        'key' =>
                            '_jl_source_product_id',

                        'value' =>
                            'P_RO_' . $source_id,

                        'compare' =>
                            '=',
                    ],
                ],

                'no_found_rows' =>
                    true,
            ]);

        return
            is_array($ids)
            && ! empty($ids)
                ? (int) $ids[0]
                : 0;
    }

    private static function find_imported_post(
        string $import_key,
        string $post_type
    ): int {
        $posts =
            get_posts([
                'post_type' =>
                    $post_type,

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

                'meta_key' =>
                    self::IMPORT_META_KEY,

                'meta_value' =>
                    $import_key,

                'no_found_rows' =>
                    true,
            ]);

        return
            is_array($posts)
            && ! empty($posts)
                ? (int) $posts[0]
                : 0;
    }

    private static function canonical_source_id(
        string $value
    ): string {
        $value =
            trim(
                $value
            );

        if ($value === '') {
            return '';
        }

        /**
         * Unterstützt:
         *
         * 1
         * 001
         * P_RO_001
         *
         * Ergebnis immer:
         *
         * 001
         */
        if (
            preg_match(
                '/^(?:P_RO_)?0*(\d+)$/i',
                $value,
                $matches
            )
        ) {
            $number =
                (int) $matches[1];

            if ($number > 0) {
                return
                    str_pad(
                        (string) $number,
                        3,
                        '0',
                        STR_PAD_LEFT
                    );
            }
        }

        return
            sanitize_text_field(
                $value
            );
    }

    private static function normalize_product_status(
        string $value
    ): string {
        $value =
            self::normalize_match_text(
                $value
            );

        if (
            in_array(
                $value,
                [
                    'aktiv',
                    'active',
                ],
                true
            )
        ) {
            return 'active';
        }

        if (
            in_array(
                $value,
                [
                    'inaktiv',
                    'inactive',
                ],
                true
            )
        ) {
            return 'inactive';
        }

        return '';
    }

    private static function count_ai_experiences(
        array $experiences
    ): int {
        $count = 0;

        foreach ($experiences as $experience) {
            if (
                (
                    $experience['review_status']
                    ?? ''
                )
                === 'ai_review'
            ) {
                $count++;
            }
        }

        return $count;
    }

    /* =========================================================
       ZELLEN / TEXT
       ========================================================= */

    private static function cell_value(
        array $row,
        string $column
    ): string {
        return
            isset($row[$column])
            && is_array($row[$column])
                ? trim(
                    (string) (
                        $row[$column]['value']
                        ?? ''
                    )
                )
                : '';
    }

    private static function cell_hyperlink(
        array $row,
        string $column
    ): string {
        return
            isset($row[$column])
            && is_array($row[$column])
                ? trim(
                    (string) (
                        $row[$column]['hyperlink']
                        ?? ''
                    )
                )
                : '';
    }

    /**
     * Excel-Hyperlink hat Vorrang.
     * Sichtbarer Zelltext wird nur verwendet, wenn er
     * tatsächlich eine URL ist.
     */
    private static function cell_effective_url(
        array $row,
        string $column
    ): string {
        $hyperlink =
            self::normalize_web_url(
                self::cell_hyperlink(
                    $row,
                    $column
                )
            );

        if ($hyperlink !== '') {
            return $hyperlink;
        }

        return
            self::normalize_web_url(
                self::cell_value(
                    $row,
                    $column
                )
            );
    }

    private static function normalize_web_url(
        string $value
    ): string {
        $value =
            trim(
                $value
            );

        if ($value === '') {
            return '';
        }

        if (
            str_starts_with(
                mb_strtolower($value),
                'www.'
            )
        ) {
            $value =
                'https://'
                . $value;
        }

        if (
            ! preg_match(
                '#^https?://#i',
                $value
            )
        ) {
            return '';
        }

        $validated =
            wp_http_validate_url(
                $value
            );

        return
            is_string($validated)
                ? esc_url_raw(
                    $validated
                )
                : '';
    }

    private static function cell_font_color(
        array $row,
        string $column
    ): string {
        return
            isset($row[$column])
            && is_array($row[$column])
                ? strtoupper(
                    (string) (
                        $row[$column]['font_color']
                        ?? ''
                    )
                )
                : '';
    }

    private static function split_title_and_body(
        string $text,
        string $fallback_title
    ): array {
        $text =
            trim(
                $text
            );

        if ($text === '') {
            return [
                $fallback_title,
                '',
            ];
        }

        $parts =
            preg_split(
                '/\R/u',
                $text,
                2
            );

        if (
            ! is_array($parts)
            || empty($parts)
        ) {
            return [
                $fallback_title,
                $text,
            ];
        }

        $title =
            trim(
                (string) (
                    $parts[0]
                    ?? ''
                )
            );

        $body =
            trim(
                (string) (
                    $parts[1]
                    ?? ''
                )
            );

        return [
            $title !== ''
                ? $title
                : $fallback_title,

            $body,
        ];
    }

    private static function normalize_match_text(
        string $value
    ): string {
        $value =
            mb_strtolower(
                trim(
                    $value
                )
            );

        $value =
            str_replace(
                [
                    '-',
                    '–',
                    '—',
                    '-',
                    '_',
                    '/',
                    '&',
                ],
                ' ',
                $value
            );

        $value =
            preg_replace(
                '/[^\p{L}\p{N}]+/u',
                ' ',
                $value
            )
            ?? $value;

        $value =
            preg_replace(
                '/\s+/u',
                ' ',
                $value
            )
            ?? $value;

        return
            trim(
                $value
            );
    }

    /* =========================================================
       XLSX-PFADE
       ========================================================= */

    private static function normalize_xlsx_target(
        string $target
    ): string {
        $target =
            str_replace(
                '\\',
                '/',
                ltrim(
                    $target,
                    '/'
                )
            );

        if (
            str_starts_with(
                $target,
                'xl/'
            )
        ) {
            return $target;
        }

        $parts =
            explode(
                '/',
                'xl/' . $target
            );

        $normalized = [];

        foreach ($parts as $part) {
            if (
                $part === ''
                || $part === '.'
            ) {
                continue;
            }

            if ($part === '..') {
                array_pop(
                    $normalized
                );

                continue;
            }

            $normalized[] = $part;
        }

        return
            implode(
                '/',
                $normalized
            );
    }

    /* =========================================================
       XML
       ========================================================= */

    private static function load_xml_document(
        string $xml
    ): DOMDocument {
        $document =
            new DOMDocument();

        $previous =
            libxml_use_internal_errors(
                true
            );

        $loaded =
            $document->loadXML(
                $xml,
                LIBXML_NONET
                | LIBXML_NOERROR
                | LIBXML_NOWARNING
            );

        libxml_clear_errors();

        libxml_use_internal_errors(
            $previous
        );

        if (! $loaded) {
            throw new RuntimeException(
                'Eine XML-Datei innerhalb der XLSX-Datei ist ungültig.'
            );
        }

        return $document;
    }

    /* =========================================================
       TEXT → GUTENBERG
       ========================================================= */

    private static function text_to_blocks(
        string $text
    ): string {
        $text =
            trim(
                $text
            );

        if ($text === '') {
            return '';
        }

        $paragraphs =
            preg_split(
                '/\R{2,}/u',
                $text
            );

        if (! is_array($paragraphs)) {
            return '';
        }

        $blocks = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph =
                trim(
                    $paragraph
                );

            if ($paragraph === '') {
                continue;
            }

            $safe =
                nl2br(
                    esc_html(
                        $paragraph
                    ),
                    false
                );

            $blocks[] =
                "<!-- wp:paragraph -->\n"
                . '<p>'
                . $safe
                . "</p>\n"
                . '<!-- /wp:paragraph -->';
        }

        return
            implode(
                "\n\n",
                $blocks
            );
    }

    /* =========================================================
       INTERNE QUELLENNOTIZ
       ========================================================= */

    private static function build_product_source_note(
        array $product
    ): string {
        $lines = [
            'Importquelle: Kundendatei / Products',

            'Excel-Zeile: '
                . absint(
                    $product['source_row']
                    ?? 0
                ),

            'Interne Produkt-ID: '
                . (string) (
                    $product['source_id']
                    ?? ''
                ),

            'Status: '
                . (string) (
                    $product['status']
                    ?? ''
                ),

            'Gebiet: '
                . (string) (
                    $product['area']
                    ?? ''
                ),

            "Roberto's Favorit: "
                . (string) (
                    $product['favorite_brand']
                    ?? ''
                ),
        ];

        $optional = [
            'Favorit – Zellinhalt' =>
                $product['favorite_reference']
                ?? '',

            'Favorit – effektive URL' =>
                $product['favorite_url']
                ?? '',

            'Bemerkung Favorit' =>
                $product['remark']
                ?? '',

            'Alternative 1' =>
                $product['alternative_1_brand']
                ?? '',

            'Alternative 1 – Zellinhalt' =>
                $product['alternative_1_reference']
                ?? '',

            'Alternative 1 – effektive URL' =>
                $product['alternative_1_url']
                ?? '',

            'Bemerkung Alternative 1' =>
                $product['alternative_1_remark']
                ?? '',

            'Alternative 2' =>
                $product['alternative_2_brand']
                ?? '',

            'Alternative 2 – Zellinhalt' =>
                $product['alternative_2_reference']
                ?? '',

            'Alternative 2 – effektive URL' =>
                $product['alternative_2_url']
                ?? '',
        ];

        foreach (
            $optional
            as $label => $value
        ) {
            $value =
                trim(
                    (string) $value
                );

            if ($value !== '') {
                $lines[] =
                    $label
                    . ': '
                    . $value;
            }
        }

        return
            implode(
                "\n",
                $lines
            );
    }

    /* =========================================================
       FEHLER
       ========================================================= */

    private static function redirect_with_error(): never
    {
        wp_safe_redirect(
            add_query_arg(
                [
                    'page' =>
                        self::MENU_SLUG,

                    'jl_import' =>
                        'error',
                ],
                admin_url(
                    'tools.php'
                )
            )
        );

        exit;
    }
}