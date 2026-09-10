<?php
/**
 * Excel-Inhaltsimport für Jung Leben.
 *
 * Liest die Tabellen «Products» und «Blogs» direkt aus einer
 * hochgeladenen XLSX-Datei und legt Produkte sowie Erfahrungen
 * als WordPress-Entwürfe an.
 *
 * Unterstützt die neue Kundendatei mit:
 * - Status Aktiv / Inaktiv
 * - Roberto's Favorit
 * - bis zu zwei Alternativen
 * - sichtbaren Zelltexten und hinterlegten Excel-Hyperlinks
 * - KI-Markierung über blaue Schrift
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

    /**
     * Blau aus der Kundendatei.
     *
     * Excel-Farbwert:
     * RGB 00B0F0
     */
    private const AI_FONT_COLOR = '00B0F0';


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
            'admin_post_' . self::ACTION,
            [
                self::class,
                'handle_import',
            ]
        );
    }


    /* =========================================================
       ADMIN-SEITE
       ========================================================= */

    /**
     * Importseite unter Werkzeuge registrieren.
     */
    public static function register_admin_page(): void
    {
        add_management_page(
            __('Jung Leben Import', 'jung-leben-core'),
            __('Jung Leben Import', 'jung-leben-core'),
            'manage_options',
            self::MENU_SLUG,
            [
                self::class,
                'render_admin_page',
            ]
        );
    }


    /**
     * Importseite ausgeben.
     */
    public static function render_admin_page(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        $result =
            isset($_GET['jl_import'])
                ? sanitize_key(
                    wp_unslash($_GET['jl_import'])
                )
                : '';

        $imported_products =
            isset($_GET['products'])
                ? absint($_GET['products'])
                : 0;

        $imported_experiences =
            isset($_GET['experiences'])
                ? absint($_GET['experiences'])
                : 0;

        $updated =
            isset($_GET['updated'])
                ? absint($_GET['updated'])
                : 0;

        $inactive =
            isset($_GET['inactive'])
                ? absint($_GET['inactive'])
                : 0;

        $skipped =
            isset($_GET['skipped'])
                ? absint($_GET['skipped'])
                : 0;

        $errors =
            isset($_GET['errors'])
                ? absint($_GET['errors'])
                : 0;

        $ai_count =
            isset($_GET['ai'])
                ? absint($_GET['ai'])
                : 0;
        ?>

        <div class="wrap">

            <h1>
                <?php
                esc_html_e(
                    'Jung Leben – Excel-Import',
                    'jung-leben-core'
                );
                ?>
            </h1>


            <p>
                <?php
                esc_html_e(
                    'Lade hier die Kundendatei mit den Tabellen «Products» und «Blogs» hoch. Die Datei wird nur für den Import gelesen und nicht dauerhaft auf dem Server gespeichert.',
                    'jung-leben-core'
                );
                ?>
            </p>


            <?php if ($result === 'done') : ?>

                <div class="notice notice-success is-dismissible">

                    <p>
                        <strong>
                            <?php
                            esc_html_e(
                                'Import abgeschlossen.',
                                'jung-leben-core'
                            );
                            ?>
                        </strong>

                        <?php
                        printf(
                            esc_html__(
                                '%1$d aktive Produkte und %2$d Erfahrungen wurden neu angelegt. %3$d bestehende Import-Datensätze wurden mit den neuen Quelldaten synchronisiert. %4$d inaktive Produktzeilen wurden nicht neu angelegt bzw. bestehende Import-Produkte deaktiviert. %5$d Datensätze wurden ohne Änderung übersprungen. %6$d KI-markierte Erfahrungen wurden erkannt. Fehler: %7$d.',
                                'jung-leben-core'
                            ),
                            $imported_products,
                            $imported_experiences,
                            $updated,
                            $inactive,
                            $skipped,
                            $ai_count,
                            $errors
                        );
                        ?>
                    </p>

                </div>

            <?php elseif ($result === 'error') : ?>

                <div class="notice notice-error is-dismissible">

                    <p>
                        <?php
                        esc_html_e(
                            'Der Import konnte nicht durchgeführt werden. Bitte prüfe die XLSX-Datei und versuche es erneut.',
                            'jung-leben-core'
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
                    <?php
                    esc_html_e(
                        'Kundendatei importieren',
                        'jung-leben-core'
                    );
                    ?>
                </h2>


                <p>
                    <?php
                    esc_html_e(
                        'Importiert werden Produktzeilen mit Status «Aktiv» sowie die gültigen Erfahrungen aus «Blogs». Neue Inhalte werden als Entwurf angelegt. Produktzeilen mit Status «Inaktiv» werden nicht neu angelegt.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <p>
                    <?php
                    esc_html_e(
                        'Bei bereits importierten Produkten werden redaktionelle Inhalte und manuell gepflegte Affiliate-Daten nicht überschrieben. Aktualisiert werden nur Import-Metadaten, Status, Excel-Zuordnungen, Marke, Gebiet, Alternativen und – wenn noch kein direkter Produktlink hinterlegt ist – die URL aus der Kundendatei.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <p>
                    <?php
                    esc_html_e(
                        'Blau formatierte Blogtexte werden automatisch mit dem Prüfstatus «KI-generiert – muss geprüft werden» versehen. Die Zeilen «Test» und «Blauer Text» werden ignoriert.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <?php if (! function_exists('update_field')) : ?>

                    <div class="notice notice-error inline">
                        <p>
                            <?php
                            esc_html_e(
                                'Advanced Custom Fields ist nicht aktiv. Bitte ACF aktivieren, bevor der Import ausgeführt wird.',
                                'jung-leben-core'
                            );
                            ?>
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
                                            <?php
                                            esc_html_e(
                                                'Excel-Datei',
                                                'jung-leben-core'
                                            );
                                            ?>
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
                                            <?php
                                            esc_html_e(
                                                'Erwartet wird die Kundendatei im Format .xlsx mit den Tabellen «Products» und «Blogs». Maximale Dateigrösse: 10 MB.',
                                                'jung-leben-core'
                                            );
                                            ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>


                        <?php
                        submit_button(
                            __('Import aktualisieren', 'jung-leben-core'),
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


    /* =========================================================
       IMPORT STARTEN
       ========================================================= */

    /**
     * Import ausführen.
     */
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
            || ((int) $file['error']) !== UPLOAD_ERR_OK
            || ! is_uploaded_file((string) $file['tmp_name'])
        ) {
            self::redirect_with_error();
        }


        if (((int) $file['size']) > self::MAX_FILE_SIZE) {
            wp_die(
                esc_html__(
                    'Die Excel-Datei ist grösser als 10 MB.',
                    'jung-leben-core'
                )
            );
        }


        $extension =
            strtolower(
                pathinfo(
                    (string) $file['name'],
                    PATHINFO_EXTENSION
                )
            );


        if ($extension !== 'xlsx') {
            wp_die(
                esc_html__(
                    'Bitte eine XLSX-Datei hochladen.',
                    'jung-leben-core'
                )
            );
        }


        try {
            $workbook =
                self::read_xlsx(
                    (string) $file['tmp_name']
                );

            $data =
                self::build_import_data(
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
            'skipped' => 0,
            'errors' => 0,
            'ai' => self::count_ai_experiences(
                $data['experiences']
            ),
        ];


        /**
         * Zuordnung:
         *
         * Excel Produkt-ID
         * →
         * WordPress Post-ID
         */
        $product_map = [];


        /* =====================================================
           PRODUKTE
           ===================================================== */

        foreach ($data['products'] as $product) {
            $source_id =
                self::canonical_source_id(
                    (string) (
                        $product['source_id']
                        ?? ''
                    )
                );

            $status =
                self::normalize_product_status(
                    (string) (
                        $product['status']
                        ?? ''
                    )
                );


            if ($source_id === '' || $status === '') {
                $result['errors']++;
                continue;
            }


            $existing_id =
                self::find_existing_product(
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
             * Wird der Status später im Excel auf «Aktiv»
             * gesetzt, kann der nächste Import sie anlegen.
             */
            if ($status === 'inactive') {
                $result['inactive']++;
                continue;
            }


            $product_id =
                self::import_product(
                    $product
                );


            if ($product_id <= 0) {
                $result['errors']++;
                continue;
            }


            $product_map[$source_id] = $product_id;
            $result['products']++;
        }


        /* =====================================================
           ERFAHRUNGEN
           ===================================================== */

        foreach ($data['experiences'] as $experience) {
            $topic =
                sanitize_text_field(
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


            $existing_id =
                self::find_imported_post(
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


            $experience_id =
                self::import_experience(
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


        $redirect_url =
            add_query_arg(
                [
                    'page' => self::MENU_SLUG,
                    'jl_import' => 'done',
                    'products' => $result['products'],
                    'experiences' => $result['experiences'],
                    'updated' => $result['updated'],
                    'inactive' => $result['inactive'],
                    'skipped' => $result['skipped'],
                    'errors' => $result['errors'],
                    'ai' => $result['ai'],
                ],
                admin_url('tools.php')
            );


        wp_safe_redirect($redirect_url);
        exit;
    }


    /* =========================================================
       XLSX EINLESEN
       ========================================================= */

    /**
     * XLSX-Datei über WordPress temporär entpacken.
     */
    private static function read_xlsx(
        string $path
    ): array {
        require_once
            ABSPATH
            . 'wp-admin/includes/file.php';


        if (! function_exists('WP_Filesystem')) {
            throw new RuntimeException(
                'Die WordPress-Dateifunktionen sind nicht verfügbar.'
            );
        }


        if (! WP_Filesystem()) {
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
            trailingslashit(get_temp_dir())
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
            $unzipped =
                unzip_file(
                    $path,
                    $temp_dir
                );


            if (is_wp_error($unzipped)) {
                throw new RuntimeException(
                    $unzipped->get_error_message()
                );
            }


            return
                self::read_extracted_xlsx(
                    $temp_dir
                );
        } finally {
            $wp_filesystem->delete(
                $temp_dir,
                true
            );
        }
    }


    /**
     * Entpackte XLSX-Struktur auslesen.
     */
    private static function read_extracted_xlsx(
        string $base_dir
    ): array {
        $shared_strings =
            self::read_shared_strings(
                $base_dir
            );

        $style_colors =
            self::read_style_colors(
                $base_dir
            );

        $sheet_targets =
            self::read_sheet_targets(
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


    /* =========================================================
       SHARED STRINGS
       ========================================================= */

    /**
     * Excel Shared Strings lesen.
     */
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


        if (! is_string($xml) || $xml === '') {
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

        $items =
            $xpath->query(
                '//*[local-name()="si"]'
            );


        if (! $items) {
            return [];
        }


        $strings = [];


        foreach ($items as $item) {
            $text_nodes =
                $xpath->query(
                    './/*[local-name()="t"]',
                    $item
                );

            $value = '';


            if ($text_nodes) {
                foreach ($text_nodes as $text_node) {
                    $value .= $text_node->textContent;
                }
            }


            $strings[] = $value;
        }


        return $strings;
    }


    /* =========================================================
       STYLES / BLAUE TEXTE
       ========================================================= */

    /**
     * Excel Style-ID auf Schriftfarbe abbilden.
     */
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


        if (! is_string($xml) || $xml === '') {
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


        $font_colors = [];

        $fonts =
            $xpath->query(
                '/*[local-name()="styleSheet"]'
                . '/*[local-name()="fonts"]'
                . '/*[local-name()="font"]'
            );


        if ($fonts) {
            foreach ($fonts as $index => $font) {
                $color_nodes =
                    $xpath->query(
                        './*[local-name()="color"]',
                        $font
                    );

                $color = '';


                if (
                    $color_nodes
                    && $color_nodes->length > 0
                ) {
                    $color_node =
                        $color_nodes->item(0);


                    if ($color_node instanceof DOMElement) {
                        if ($color_node->hasAttribute('rgb')) {
                            $rgb =
                                strtoupper(
                                    $color_node->getAttribute('rgb')
                                );

                            $color =
                                substr(
                                    $rgb,
                                    -6
                                );
                        } elseif ($color_node->hasAttribute('theme')) {
                            $color =
                                'theme:'
                                . $color_node->getAttribute('theme');
                        }
                    }
                }


                $font_colors[(int) $index] = $color;
            }
        }


        $style_colors = [];

        $xfs =
            $xpath->query(
                '/*[local-name()="styleSheet"]'
                . '/*[local-name()="cellXfs"]'
                . '/*[local-name()="xf"]'
            );


        if ($xfs) {
            foreach ($xfs as $index => $xf) {
                $font_id = 0;


                if (
                    $xf instanceof DOMElement
                    && $xf->hasAttribute('fontId')
                ) {
                    $font_id =
                        (int) $xf->getAttribute('fontId');
                }


                $style_colors[(int) $index] =
                    $font_colors[$font_id]
                    ?? '';
            }
        }


        return $style_colors;
    }


    /* =========================================================
       SHEETS AUFLÖSEN
       ========================================================= */

    /**
     * Tabellenname → XML-Datei bestimmen.
     */
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


        $workbook_xml = file_get_contents($workbook_path);
        $rels_xml = file_get_contents($rels_path);


        if (
            ! is_string($workbook_xml)
            || ! is_string($rels_xml)
        ) {
            throw new RuntimeException(
                'Die Excel-Arbeitsmappe konnte nicht gelesen werden.'
            );
        }


        $workbook_document =
            self::load_xml_document(
                $workbook_xml
            );

        $rels_document =
            self::load_xml_document(
                $rels_xml
            );


        $rels_xpath =
            new DOMXPath(
                $rels_document
            );

        $relationships = [];

        $relationship_nodes =
            $rels_xpath->query(
                '//*[local-name()="Relationship"]'
            );


        if ($relationship_nodes) {
            foreach ($relationship_nodes as $relationship) {
                if (! $relationship instanceof DOMElement) {
                    continue;
                }


                $id =
                    $relationship->getAttribute('Id');

                $target =
                    $relationship->getAttribute('Target');


                if ($id !== '' && $target !== '') {
                    $relationships[$id] =
                        self::normalize_xlsx_target(
                            $target
                        );
                }
            }
        }


        $workbook_xpath =
            new DOMXPath(
                $workbook_document
            );

        $sheet_nodes =
            $workbook_xpath->query(
                '//*[local-name()="sheet"]'
            );

        $targets = [];


        if ($sheet_nodes) {
            foreach ($sheet_nodes as $sheet) {
                if (! $sheet instanceof DOMElement) {
                    continue;
                }


                $name =
                    $sheet->getAttribute('name');

                $relationship_id =
                    $sheet->getAttributeNS(
                        'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                        'id'
                    );


                if (
                    $name !== ''
                    && $relationship_id !== ''
                    && isset($relationships[$relationship_id])
                ) {
                    $targets[$name] =
                        $relationships[$relationship_id];
                }
            }
        }


        return $targets;
    }


    /* =========================================================
       SHEET LESEN
       ========================================================= */

    /**
     * Einzelnes Tabellenblatt lesen.
     *
     * Zusätzlich zum sichtbaren Zellwert wird auch ein
     * hinterlegter Excel-Hyperlink ausgelesen. Das ist für
     * die neue Products-Tabelle wichtig, weil mehrere Zellen
     * einen Produktnamen anzeigen, aber im Hintergrund einen
     * echten Link enthalten.
     */
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


        $xml = file_get_contents($path);


        if (! is_string($xml) || $xml === '') {
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

        $cell_nodes =
            $xpath->query(
                '//*[local-name()="sheetData"]'
                . '//*[local-name()="c"]'
            );

        $rows = [];


        if ($cell_nodes) {
            foreach ($cell_nodes as $cell) {
                if (! $cell instanceof DOMElement) {
                    continue;
                }


                $reference =
                    $cell->getAttribute('r');


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
                $row = (int) $matches[2];

                $type =
                    $cell->getAttribute('t');

                $style_id =
                    $cell->hasAttribute('s')
                        ? (int) $cell->getAttribute('s')
                        : 0;

                $value = '';


                if ($type === 'inlineStr') {
                    $text_nodes =
                        $xpath->query(
                            './/*[local-name()="is"]'
                            . '//*[local-name()="t"]',
                            $cell
                        );


                    if ($text_nodes) {
                        foreach ($text_nodes as $text_node) {
                            $value .= $text_node->textContent;
                        }
                    }
                } else {
                    $value_nodes =
                        $xpath->query(
                            './*[local-name()="v"]',
                            $cell
                        );

                    $raw_value = '';


                    if (
                        $value_nodes
                        && $value_nodes->length > 0
                    ) {
                        $raw_value =
                            $value_nodes
                                ->item(0)
                                ->textContent;
                    }


                    if ($type === 's') {
                        $shared_index = (int) $raw_value;

                        $value =
                            $shared_strings[$shared_index]
                            ?? '';
                    } else {
                        $value = $raw_value;
                    }
                }


                $rows[$row][$column] = [
                    'value' => $value,
                    'font_color' =>
                        $style_colors[$style_id]
                        ?? '',
                    'hyperlink' => '',
                ];
            }
        }


        /* -----------------------------------------------------
           Excel-Hyperlinks ergänzen
           ----------------------------------------------------- */

        $relationships =
            self::read_sheet_external_relationships(
                $base_dir,
                $target
            );


        $hyperlink_nodes =
            $xpath->query(
                '//*[local-name()="hyperlinks"]'
                . '/*[local-name()="hyperlink"]'
            );


        if ($hyperlink_nodes) {
            foreach ($hyperlink_nodes as $hyperlink) {
                if (! $hyperlink instanceof DOMElement) {
                    continue;
                }


                $reference =
                    $hyperlink->getAttribute('ref');

                $relationship_id =
                    $hyperlink->getAttributeNS(
                        'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                        'id'
                    );


                if (
                    $reference === ''
                    || $relationship_id === ''
                    || ! isset($relationships[$relationship_id])
                ) {
                    continue;
                }


                /**
                 * In der Kundendatei sind Hyperlinks jeweils
                 * einer einzelnen Zelle zugeordnet.
                 */
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
                $row = (int) $matches[2];


                if (! isset($rows[$row][$column])) {
                    $rows[$row][$column] = [
                        'value' => '',
                        'font_color' => '',
                        'hyperlink' => '',
                    ];
                }


                $rows[$row][$column]['hyperlink'] =
                    $relationships[$relationship_id];
            }
        }


        ksort($rows);

        return $rows;
    }


    /**
     * Externe Beziehungen eines Tabellenblatts lesen.
     *
     * Benötigt für echte Excel-Hyperlinks.
     */
    private static function read_sheet_external_relationships(
        string $base_dir,
        string $sheet_target
    ): array {
        $sheet_target =
            str_replace(
                '\\',
                '/',
                ltrim($sheet_target, '/')
            );

        $directory =
            dirname($sheet_target);

        $filename =
            basename($sheet_target);

        $rels_path =
            trailingslashit($base_dir)
            . $directory
            . '/_rels/'
            . $filename
            . '.rels';


        if (! file_exists($rels_path)) {
            return [];
        }


        $xml = file_get_contents($rels_path);


        if (! is_string($xml) || $xml === '') {
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
                $node->getAttribute('Id');

            $type =
                $node->getAttribute('Type');

            $target =
                $node->getAttribute('Target');

            $target_mode =
                $node->getAttribute('TargetMode');


            if (
                $id === ''
                || $target === ''
                || $target_mode !== 'External'
                || ! str_ends_with($type, '/hyperlink')
            ) {
                continue;
            }


            $relationships[$id] = $target;
        }


        return $relationships;
    }


    /* =========================================================
       IMPORTDATEN ERSTELLEN
       ========================================================= */

    /**
     * Excel-Rohdaten in Importstruktur umwandeln.
     */
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


    /* =========================================================
       PRODUCTS
       ========================================================= */

    /**
     * Products-Tabelle verarbeiten.
     */
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


        $header_row =
            $rows[$header_row_number]
            ?? [];

        $columns =
            self::build_product_column_map(
                $header_row
            );


        foreach (
            [
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
            ]
            as $required_key
        ) {
            if (! isset($columns[$required_key])) {
                throw new RuntimeException(
                    sprintf(
                        'Die erwartete Spalte «%s» in «Products» wurde nicht gefunden.',
                        $required_key
                    )
                );
            }
        }


        $products = [];


        foreach ($rows as $row_number => $row) {
            if (((int) $row_number) <= $header_row_number) {
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


            if ($source_id === '' || $name === '') {
                continue;
            }


            $main_reference =
                self::cell_value(
                    $row,
                    $columns['main_reference']
                );

            $main_url =
                self::cell_effective_url(
                    $row,
                    $columns['main_reference']
                );

            $alternative_1_reference =
                self::cell_value(
                    $row,
                    $columns['alternative_1_reference']
                );

            $alternative_1_url =
                self::cell_effective_url(
                    $row,
                    $columns['alternative_1_reference']
                );

            $alternative_2_reference =
                self::cell_value(
                    $row,
                    $columns['alternative_2_reference']
                );

            $alternative_2_url =
                self::cell_effective_url(
                    $row,
                    $columns['alternative_2_reference']
                );


            $products[] = [
                'source_id' => $source_id,
                'name' => $name,
                'area' => self::cell_value(
                    $row,
                    $columns['area']
                ),
                'description' => self::cell_value(
                    $row,
                    $columns['description']
                ),
                'status' => self::normalize_product_status(
                    self::cell_value(
                        $row,
                        $columns['status']
                    )
                ),
                'favorite_brand' => self::cell_value(
                    $row,
                    $columns['favorite']
                ),
                'favorite_reference' => $main_reference,
                'favorite_url' => $main_url,
                'remark' => self::cell_value(
                    $row,
                    $columns['main_remark']
                ),
                'alternative_1_brand' => self::cell_value(
                    $row,
                    $columns['alternative_1']
                ),
                'alternative_1_reference' =>
                    $alternative_1_reference,
                'alternative_1_url' =>
                    $alternative_1_url,
                'alternative_1_remark' => self::cell_value(
                    $row,
                    $columns['alternative_1_remark']
                ),
                'alternative_2_brand' => self::cell_value(
                    $row,
                    $columns['alternative_2']
                ),
                'alternative_2_reference' =>
                    $alternative_2_reference,
                'alternative_2_url' =>
                    $alternative_2_url,
                'personally_tested' => false,
                'source_row' => (int) $row_number,
            ];
        }


        return $products;
    }


    /**
     * Products-Spalten anhand der tatsächlichen Kopfzeile
     * auflösen.
     *
     * Die drei Spalten mit dem Titel «URL» und die zwei
     * Spalten «Bemerkung» werden nach ihrem Auftreten
     * unterschieden.
     */
    private static function build_product_column_map(
        array $header_row
    ): array {
        $map = [];
        $url_count = 0;
        $remark_count = 0;


        foreach ($header_row as $column => $cell) {
            $label =
                self::normalize_header(
                    is_array($cell)
                        ? (string) ($cell['value'] ?? '')
                        : ''
                );


            if ($label === '') {
                continue;
            }


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


    /* =========================================================
       BLOGS
       ========================================================= */

    /**
     * Blogs-Tabelle verarbeiten.
     */
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


        foreach ($rows as $row_number => $row) {
            if (((int) $row_number) <= $header_row_number) {
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


            if ($blog === '' && $background === '') {
                continue;
            }


            [
                $title,
                $body,
            ] =
                self::split_title_and_body(
                    $blog,
                    $topic
                );

            [
                $background_title,
                $background_body,
            ] =
                self::split_title_and_body(
                    $background,
                    ''
                );


            $is_ai =
                self::cell_font_color(
                    $row,
                    $columns['blog']
                ) === self::AI_FONT_COLOR
                || self::cell_font_color(
                    $row,
                    $columns['wirkungsbeschrieb']
                ) === self::AI_FONT_COLOR;


            $experiences[] = [
                'topic' => $topic,
                'title' => $title,
                'body' => $body,
                'background_title' => $background_title,
                'background' => $background_body,
                'review_status' =>
                    $is_ai
                        ? 'ai_review'
                        : 'customer_source',
                'related_product_source_id' =>
                    self::match_product_source_id(
                        $topic,
                        $product_map
                    ),
                'source_row' => (int) $row_number,
            ];
        }


        return $experiences;
    }


    /**
     * Einfaches Header-Mapping für eindeutige Spaltennamen.
     */
    private static function build_simple_column_map(
        array $header_row
    ): array {
        $map = [];


        foreach ($header_row as $column => $cell) {
            if (! is_array($cell)) {
                continue;
            }


            $label =
                self::normalize_header(
                    (string) ($cell['value'] ?? '')
                );


            if ($label !== '') {
                $map[$label] = $column;
            }
        }


        return $map;
    }


    /**
     * Kopfzeile anhand erwarteter Bezeichnungen finden.
     */
    private static function find_header_row(
        array $rows,
        array $required_labels
    ): int {
        foreach ($rows as $row_number => $row) {
            if (! is_array($row)) {
                continue;
            }


            $labels = [];


            foreach ($row as $cell) {
                if (! is_array($cell)) {
                    continue;
                }


                $label =
                    self::normalize_header(
                        (string) ($cell['value'] ?? '')
                    );


                if ($label !== '') {
                    $labels[] = $label;
                }
            }


            $all_found = true;


            foreach ($required_labels as $required_label) {
                if (! in_array($required_label, $labels, true)) {
                    $all_found = false;
                    break;
                }
            }


            if ($all_found) {
                return (int) $row_number;
            }
        }


        return 0;
    }


    /**
     * Header-Bezeichnungen vereinheitlichen.
     */
    private static function normalize_header(
        string $value
    ): string {
        $value =
            mb_strtolower(
                trim($value)
            );

        $value =
            str_replace(
                [
                    "'",
                    '’',
                    '‘',
                    '-',
                    '–',
                    '—',
                    '/',
                    '_',
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


        return trim($value);
    }


    /* =========================================================
       PRODUKT-ZUORDNUNG
       ========================================================= */

    /**
     * Produktnamen und IDs vorbereiten.
     */
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


            if ($source_id === '' || $name === '') {
                continue;
            }


            $map[
                self::normalize_match_text($name)
            ] = $source_id;
        }


        return $map;
    }


    /**
     * Erfahrung dem passenden Produkt zuordnen.
     */
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
         * Namensabweichungen zwischen Blogs und Products.
         */
        $aliases = [
            'deo roll on' => '003',
            'deo rollon' => '003',
            'hydroxiapatite zahnpasta' => '002',
            'hydroxyapatite zahnpasta' => '002',
            'l tryptophan' => '023',
            'l tryptophane' => '023',
            'omega 3 6 9' => '004',
            'ashwaganda' => '005',
            'ashwagandha' => '005',
        ];


        return
            $aliases[$normalized]
            ?? '';
    }


    /**
     * Persönlich getestete Produkte aus nicht blau markierten
     * Erfahrungsbeiträgen ableiten.
     */
    private static function apply_experience_flags_to_products(
        array $products,
        array $experiences
    ): array {
        $tested_source_ids = [];


        foreach ($experiences as $experience) {
            if (
                ($experience['review_status'] ?? '')
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
                $tested_source_ids[$source_id] = true;
            }
        }


        foreach ($products as $index => $product) {
            $source_id =
                self::canonical_source_id(
                    (string) (
                        $product['source_id']
                        ?? ''
                    )
                );


            $products[$index]['personally_tested'] =
                isset($tested_source_ids[$source_id]);
        }


        return $products;
    }


    /* =========================================================
       PRODUKT IMPORTIEREN
       ========================================================= */

    /**
     * Einzelnes aktives Produkt importieren.
     */
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


        if ($name === '' || $source_id === '') {
            return 0;
        }


        $description =
            sanitize_textarea_field(
                (string) (
                    $product['description']
                    ?? ''
                )
            );


        $post_id =
            wp_insert_post(
                [
                    'post_type' =>
                        Jung_Leben_Core_Products::POST_TYPE,
                    'post_status' => 'draft',
                    'post_title' => $name,
                    'post_excerpt' => $description,
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


        $post_id = (int) $post_id;


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


        update_field(
            'jl_product_recommendation_status',
            'neutral',
            $post_id
        );

        update_field(
            'jl_product_personally_tested',
            ! empty($product['personally_tested'])
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


        /**
         * Die neue Partnerlogik läuft zentral über Marke →
         * Affiliate-Partner. Das alte Freitextfeld bleibt leer.
         */
        update_field(
            'jl_product_partner_name',
            '',
            $post_id
        );


        /**
         * Nur ein tatsächlich aus Excel ermittelter Weblink
         * wird als direkte Produktseite gespeichert.
         *
         * Hinterlegte Excel-Hyperlinks haben Vorrang vor dem
         * sichtbaren Zelltext.
         */
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
         * Keine Excel-URL wird automatisch als Affiliate-Link
         * interpretiert. Persönliche Affiliate-Links bleiben
         * bewusst ein separates, manuell gepflegtes Feld.
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
         * Leer lassen, damit der zentrale Partner-Buttontext
         * verwendet werden kann, z.B. «Bei Luvy ansehen».
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


        update_field(
            'jl_product_internal_source_note',
            self::build_product_source_note(
                $product
            ),
            $post_id
        );


        return $post_id;
    }


    /**
     * Bestehendes Import-Produkt mit den neuen Quelldaten
     * synchronisieren, ohne redaktionelle bzw. manuell
     * gepflegte Affiliate-Felder zu überschreiben.
     */
    private static function sync_existing_product_source_data(
        int $post_id,
        array $product
    ): void {
        self::sync_product_source_meta(
            $post_id,
            $product
        );

        self::sync_product_taxonomies(
            $post_id,
            $product
        );


        update_field(
            'jl_product_personally_tested',
            ! empty($product['personally_tested'])
                ? 1
                : 0,
            $post_id
        );


        /**
         * Direkte Produkt-URL nur ergänzen, wenn dieses Feld
         * bisher leer ist. So bleiben manuell verbesserte oder
         * persönliche Links unangetastet.
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
                esc_url_raw($favorite_url),
                $post_id
            );
        }


        update_field(
            'jl_product_internal_source_note',
            self::build_product_source_note(
                $product
            ),
            $post_id
        );
    }


    /**
     * Import-Metadaten und strukturierte Alternativen speichern.
     */
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
            (string) (
                $product['status']
                ?? ''
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


    /**
     * Marke und Gebiet aus der neuen Products-Tabelle
     * synchronisieren.
     */
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


        $area =
            sanitize_text_field(
                (string) (
                    $product['area']
                    ?? ''
                )
            );


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


    /**
     * Alte P_RO_###-Importidentität auf die neue numerische
     * ID ### migrieren.
     */
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
            'product:'
            . $source_id
        );

        update_post_meta(
            $post_id,
            '_jl_source_product_id',
            $source_id
        );
    }


    /**
     * Ein bestehendes, aus Excel importiertes Produkt bei
     * Status «Inaktiv» sicher auf Entwurf setzen.
     */
    private static function deactivate_imported_product(
        int $post_id
    ): void {
        $current_status =
            get_post_status(
                $post_id
            );


        if (
            ! is_string($current_status)
            || in_array(
                $current_status,
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


        wp_update_post(
            [
                'ID' => $post_id,
                'post_status' => 'draft',
            ]
        );
    }


    /* =========================================================
       ERFAHRUNG IMPORTIEREN
       ========================================================= */

    /**
     * Erfahrung als normalen WordPress-Beitrag importieren.
     */
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
                . esc_html($background_title)
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
                    'post_type' => 'post',
                    'post_status' => 'draft',
                    'post_title' => $title,
                    'post_excerpt' =>
                        wp_trim_words(
                            $body,
                            32,
                            ' …'
                        ),
                    'post_content' => $content,
                    'post_author' =>
                        get_current_user_id(),
                ],
                true
            );


        if (is_wp_error($post_id)) {
            return 0;
        }


        $post_id = (int) $post_id;


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


    /**
     * Bei bestehenden Erfahrungen nur Importstatus und
     * Produktbeziehung aktualisieren – nicht den redaktionellen
     * Beitragstext überschreiben.
     */
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


    /**
     * ACF-Felder einer Erfahrung synchronisieren.
     */
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
            $review_status = 'customer_source';
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
            && isset($product_map[$related_source_id])
        ) {
            update_field(
                'jl_experience_related_product',
                (int) $product_map[$related_source_id],
                $post_id
            );
        }
    }


    /**
     * WordPress-Kategorie «Erfahrungen» sicher zuweisen.
     */
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
                ? (int) $category['term_id']
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
       DUPLIKATE / LEGACY-IDs
       ========================================================= */

    /**
     * Bestehendes Produkt sowohl über die neue ID ### als auch
     * über die alte ID P_RO_### finden.
     */
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


        $candidates = [
            $source_id,
            'P_RO_' . $source_id,
        ];


        foreach ($candidates as $candidate) {
            $post_id =
                self::find_imported_post(
                    'product:'
                    . $candidate,
                    Jung_Leben_Core_Products::POST_TYPE
                );


            if ($post_id > 0) {
                return $post_id;
            }
        }


        /**
         * Zusätzlicher Fallback auf die separat gespeicherte
         * Quell-ID älterer Importversionen.
         */
        foreach ($candidates as $candidate) {
            $posts =
                get_posts(
                    [
                        'post_type' =>
                            Jung_Leben_Core_Products::POST_TYPE,
                        'post_status' => [
                            'publish',
                            'draft',
                            'pending',
                            'private',
                            'future',
                        ],
                        'posts_per_page' => 1,
                        'fields' => 'ids',
                        'meta_key' => '_jl_source_product_id',
                        'meta_value' => $candidate,
                        'no_found_rows' => true,
                    ]
                );


            if (is_array($posts) && ! empty($posts)) {
                return (int) $posts[0];
            }
        }


        return 0;
    }


    /**
     * Bereits importierten Datensatz finden.
     */
    private static function find_imported_post(
        string $import_key,
        string $post_type
    ): int {
        $posts =
            get_posts(
                [
                    'post_type' => $post_type,
                    'post_status' => [
                        'publish',
                        'draft',
                        'pending',
                        'private',
                        'future',
                    ],
                    'posts_per_page' => 1,
                    'fields' => 'ids',
                    'meta_key' => self::IMPORT_META_KEY,
                    'meta_value' => $import_key,
                    'no_found_rows' => true,
                ]
            );


        if (! is_array($posts) || empty($posts)) {
            return 0;
        }


        return (int) $posts[0];
    }


    /**
     * Neue numerische IDs und alte P_RO_###-IDs auf ein
     * gemeinsames Format ### bringen.
     */
    private static function canonical_source_id(
        string $value
    ): string {
        $value = trim($value);


        if ($value === '') {
            return '';
        }


        if (
            preg_match(
                '/(\d+)$/',
                $value,
                $matches
            )
        ) {
            $number = (int) $matches[1];


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


        return sanitize_text_field($value);
    }


    /* =========================================================
       STATUS / COUNTS
       ========================================================= */

    /**
     * Produktstatus vereinheitlichen.
     */
    private static function normalize_product_status(
        string $value
    ): string {
        $value =
            self::normalize_match_text(
                $value
            );


        if ($value === 'aktiv' || $value === 'active') {
            return 'active';
        }


        if ($value === 'inaktiv' || $value === 'inactive') {
            return 'inactive';
        }


        return '';
    }


    /**
     * KI-markierte Erfahrungen zählen.
     */
    private static function count_ai_experiences(
        array $experiences
    ): int {
        $count = 0;


        foreach ($experiences as $experience) {
            if (
                ($experience['review_status'] ?? '')
                === 'ai_review'
            ) {
                $count++;
            }
        }


        return $count;
    }


    /* =========================================================
       ZELL-HILFSFUNKTIONEN
       ========================================================= */

    /**
     * Zellwert lesen.
     */
    private static function cell_value(
        array $row,
        string $column
    ): string {
        if (
            ! isset($row[$column])
            || ! is_array($row[$column])
        ) {
            return '';
        }


        return
            trim(
                (string) (
                    $row[$column]['value']
                    ?? ''
                )
            );
    }


    /**
     * Hinterlegten Excel-Hyperlink lesen.
     */
    private static function cell_hyperlink(
        array $row,
        string $column
    ): string {
        if (
            ! isset($row[$column])
            || ! is_array($row[$column])
        ) {
            return '';
        }


        return
            trim(
                (string) (
                    $row[$column]['hyperlink']
                    ?? ''
                )
            );
    }


    /**
     * Effektive Webadresse einer Zelle bestimmen.
     *
     * 1. Excel-Hyperlink
     * 2. sichtbarer Zellwert, falls er selbst eine URL ist
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


    /**
     * Sichtbaren Text nur dann als URL akzeptieren, wenn
     * tatsächlich eine Webadresse vorliegt.
     */
    private static function normalize_web_url(
        string $value
    ): string {
        $value = trim($value);


        if ($value === '') {
            return '';
        }


        if (
            str_starts_with(
                mb_strtolower($value),
                'www.'
            )
        ) {
            $value = 'https://' . $value;
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
                ? esc_url_raw($validated)
                : '';
    }


    /**
     * Schriftfarbe lesen.
     */
    private static function cell_font_color(
        array $row,
        string $column
    ): string {
        if (
            ! isset($row[$column])
            || ! is_array($row[$column])
        ) {
            return '';
        }


        return
            strtoupper(
                (string) (
                    $row[$column]['font_color']
                    ?? ''
                )
            );
    }


    /* =========================================================
       TITEL / BODY
       ========================================================= */

    /**
     * Erste Zeile als Titel behandeln.
     */
    private static function split_title_and_body(
        string $text,
        string $fallback_title
    ): array {
        $text = trim($text);


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


        if (! is_array($parts) || empty($parts)) {
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


        if ($title === '') {
            $title = $fallback_title;
        }


        return [
            $title,
            $body,
        ];
    }


    /* =========================================================
       NAMEN NORMALISIEREN
       ========================================================= */

    /**
     * Namen für Produkt-Matching vereinheitlichen.
     */
    private static function normalize_match_text(
        string $value
    ): string {
        $value =
            mb_strtolower(
                trim($value)
            );

        $value =
            str_replace(
                [
                    '-',
                    '–',
                    '—',
                    '‑',
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


        return trim($value);
    }


    /* =========================================================
       XLSX-PFADE
       ========================================================= */

    /**
     * XLSX Relationship-Pfad normalisieren.
     */
    private static function normalize_xlsx_target(
        string $target
    ): string {
        $target =
            str_replace(
                '\\',
                '/',
                $target
            );

        $target =
            ltrim(
                $target,
                '/'
            );


        if (str_starts_with($target, 'xl/')) {
            return $target;
        }


        $parts =
            explode(
                '/',
                'xl/'
                . $target
            );

        $normalized = [];


        foreach ($parts as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }


            if ($part === '..') {
                array_pop($normalized);
                continue;
            }


            $normalized[] = $part;
        }


        return implode('/', $normalized);
    }


    /* =========================================================
       XML
       ========================================================= */

    /**
     * XML sicher laden.
     */
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
        libxml_use_internal_errors($previous);


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

    /**
     * Reinen Text in Gutenberg-Absätze umwandeln.
     */
    private static function text_to_blocks(
        string $text
    ): string {
        $text = trim($text);


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
            $paragraph = trim($paragraph);


            if ($paragraph === '') {
                continue;
            }


            $safe =
                nl2br(
                    esc_html($paragraph),
                    false
                );


            $blocks[] =
                "<!-- wp:paragraph -->\n"
                . '<p>'
                . $safe
                . "</p>\n"
                . '<!-- /wp:paragraph -->';
        }


        return implode("\n\n", $blocks);
    }


    /* =========================================================
       INTERNE PRODUKTNOTIZ
       ========================================================= */

    /**
     * Excel-Angaben zusätzlich als gut lesbare interne
     * Quellennotiz speichern.
     */
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


        foreach ($optional as $label => $value) {
            $value = trim((string) $value);


            if ($value === '') {
                continue;
            }


            $lines[] =
                $label
                . ': '
                . $value;
        }


        return implode("\n", $lines);
    }


    /* =========================================================
       FEHLER-REDIRECT
       ========================================================= */

    /**
     * Zur Importseite zurück.
     */
    private static function redirect_with_error(): never
    {
        $url =
            add_query_arg(
                [
                    'page' => self::MENU_SLUG,
                    'jl_import' => 'error',
                ],
                admin_url('tools.php')
            );


        wp_safe_redirect($url);
        exit;
    }
}
