<?php
/**
 * Excel-Inhaltsimport für Jung Leben.
 *
 * Liest die Tabellen «Products» und «Blogs» direkt aus einer
 * hochgeladenen XLSX-Datei und legt Produkte sowie Erfahrungen
 * als WordPress-Entwürfe an.
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
            __(
                'Jung Leben Import',
                'jung-leben-core'
            ),

            __(
                'Jung Leben Import',
                'jung-leben-core'
            ),

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
        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            return;
        }


        $result =
            isset(
                $_GET['jl_import']
            )
                ? sanitize_key(
                    wp_unslash(
                        $_GET['jl_import']
                    )
                )
                : '';


        $imported_products =
            isset(
                $_GET['products']
            )
                ? absint(
                    $_GET['products']
                )
                : 0;


        $imported_experiences =
            isset(
                $_GET['experiences']
            )
                ? absint(
                    $_GET['experiences']
                )
                : 0;


        $skipped =
            isset(
                $_GET['skipped']
            )
                ? absint(
                    $_GET['skipped']
                )
                : 0;


        $errors =
            isset(
                $_GET['errors']
            )
                ? absint(
                    $_GET['errors']
                )
                : 0;


        $ai_count =
            isset(
                $_GET['ai']
            )
                ? absint(
                    $_GET['ai']
                )
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


            <?php
            if (
                $result === 'done'
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
                                '%1$d Produkte und %2$d Erfahrungen wurden neu angelegt. %3$d Datensätze waren bereits vorhanden und wurden übersprungen. %4$d KI-markierte Erfahrungen wurden erkannt. Fehler: %5$d.',
                                'jung-leben-core'
                            ),
                            $imported_products,
                            $imported_experiences,
                            $skipped,
                            $ai_count,
                            $errors
                        );
                        ?>

                    </p>

                </div>

            <?php
            elseif (
                $result === 'error'
            ) :
                ?>

                <div
                    class="
                        notice
                        notice-error
                        is-dismissible
                    "
                >

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

                <h2
                    style="
                        margin-top:0;
                    "
                >
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
                        'Importiert werden alle ausgefüllten Produktzeilen aus «Products» und alle Erfahrungen aus «Blogs». Sämtliche Inhalte werden zunächst als Entwurf angelegt.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <p>
                    <?php
                    esc_html_e(
                        'Blau formatierte Blogtexte werden automatisch mit dem Prüfstatus «KI-generiert – muss geprüft werden» versehen. Bereits importierte Datensätze werden nicht überschrieben.',
                        'jung-leben-core'
                    );
                    ?>
                </p>


                <?php
                if (
                    ! function_exists(
                        'update_field'
                    )
                ) :
                    ?>

                    <div
                        class="
                            notice
                            notice-error
                            inline
                        "
                    >

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
                        action="<?php
                        echo esc_url(
                            admin_url(
                                'admin-post.php'
                            )
                        );
                        ?>"
                        method="post"
                        enctype="multipart/form-data"
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


                        <table
                            class="form-table"
                            role="presentation"
                        >

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
                            __(
                                'Alle Inhalte als Entwürfe importieren',
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


    /* =========================================================
       IMPORT STARTEN
       ========================================================= */

    /**
     * Import ausführen.
     */
    public static function handle_import(): void
    {
        /* -----------------------------------------------------
           Berechtigung
           ----------------------------------------------------- */

        if (
            ! current_user_can(
                'manage_options'
            )
        ) {
            wp_die(
                esc_html__(
                    'Du hast keine Berechtigung für diesen Import.',
                    'jung-leben-core'
                )
            );
        }


        /* -----------------------------------------------------
           Nonce
           ----------------------------------------------------- */

        check_admin_referer(
            self::NONCE_ACTION
        );


        /* -----------------------------------------------------
           ACF
           ----------------------------------------------------- */

        if (
            ! function_exists(
                'update_field'
            )
        ) {
            wp_die(
                esc_html__(
                    'Advanced Custom Fields muss für den Import aktiv sein.',
                    'jung-leben-core'
                )
            );
        }


        /* -----------------------------------------------------
           Datei vorhanden?
           ----------------------------------------------------- */

        if (
            ! isset(
                $_FILES[
                    'jl_import_file'
                ]
            )
            || ! is_array(
                $_FILES[
                    'jl_import_file'
                ]
            )
        ) {
            self::redirect_with_error();
        }


        $file =
            $_FILES[
                'jl_import_file'
            ];


        /* -----------------------------------------------------
           Upload prüfen
           ----------------------------------------------------- */

        if (
            ! isset(
                $file['error'],
                $file['tmp_name'],
                $file['name'],
                $file['size']
            )
            || (
                (int)
                $file['error']
            ) !== UPLOAD_ERR_OK
            || ! is_uploaded_file(
                (string)
                $file['tmp_name']
            )
        ) {
            self::redirect_with_error();
        }


        /* -----------------------------------------------------
           Dateigrösse
           ----------------------------------------------------- */

        if (
            (
                (int)
                $file['size']
            ) > self::MAX_FILE_SIZE
        ) {
            wp_die(
                esc_html__(
                    'Die Excel-Datei ist grösser als 10 MB.',
                    'jung-leben-core'
                )
            );
        }


        /* -----------------------------------------------------
           Dateiendung
           ----------------------------------------------------- */

        $extension =
            strtolower(
                pathinfo(
                    (string)
                    $file['name'],
                    PATHINFO_EXTENSION
                )
            );


        if (
            $extension !== 'xlsx'
        ) {
            wp_die(
                esc_html__(
                    'Bitte eine XLSX-Datei hochladen.',
                    'jung-leben-core'
                )
            );
        }


        /* -----------------------------------------------------
           Excel lesen
           ----------------------------------------------------- */

        try {

            $workbook =
                self::read_xlsx(
                    (string)
                    $file['tmp_name']
                );


            $data =
                self::build_import_data(
                    $workbook
                );

        } catch (
            Throwable $exception
        ) {

            error_log(
                '[Jung Leben Import] '
                . sanitize_text_field(
                    $exception->getMessage()
                )
            );


            self::redirect_with_error();
        }


        /* -----------------------------------------------------
           Daten vorhanden?
           ----------------------------------------------------- */

        if (
            empty(
                $data['products']
            )
            && empty(
                $data['experiences']
            )
        ) {
            self::redirect_with_error();
        }


        /* -----------------------------------------------------
           Resultat
           ----------------------------------------------------- */

        $result = [

            'products' =>
                0,

            'experiences' =>
                0,

            'skipped' =>
                0,

            'errors' =>
                0,

            'ai' =>
                0,
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

        foreach (
            $data['products']
            as $product
        ) {

            $source_id =
                sanitize_text_field(
                    (string)
                    (
                        $product[
                            'source_id'
                        ]
                        ?? ''
                    )
                );


            if (
                $source_id === ''
            ) {
                $result[
                    'errors'
                ]++;

                continue;
            }


            /**
             * Bereits importiert?
             */
            $existing_id =
                self::find_imported_post(
                    'product:'
                    . $source_id,

                    Jung_Leben_Core_Products::POST_TYPE
                );


            if (
                $existing_id > 0
            ) {

                $product_map[
                    $source_id
                ] = $existing_id;


                $result[
                    'skipped'
                ]++;

                continue;
            }


            /**
             * Neues Produkt.
             */
            $product_id =
                self::import_product(
                    $product
                );


            if (
                $product_id <= 0
            ) {

                $result[
                    'errors'
                ]++;

                continue;
            }


            $product_map[
                $source_id
            ] = $product_id;


            $result[
                'products'
            ]++;
        }


        /* =====================================================
           BEREITS IMPORTIERTE PRODUKTE ZUORDNEN
           ===================================================== */

        /**
         * Wenn der Import ein zweites Mal ausgeführt wird,
         * müssen bestehende Produkte trotzdem für die
         * Erfahrungs-Verknüpfung zur Verfügung stehen.
         */
        foreach (
            $data['products']
            as $product
        ) {

            $source_id =
                sanitize_text_field(
                    (string)
                    (
                        $product[
                            'source_id'
                        ]
                        ?? ''
                    )
                );


            if (
                $source_id === ''
                || isset(
                    $product_map[
                        $source_id
                    ]
                )
            ) {
                continue;
            }


            $existing_id =
                self::find_imported_post(
                    'product:'
                    . $source_id,

                    Jung_Leben_Core_Products::POST_TYPE
                );


            if (
                $existing_id > 0
            ) {
                $product_map[
                    $source_id
                ] = $existing_id;
            }
        }


        /* =====================================================
           ERFAHRUNGEN
           ===================================================== */

        foreach (
            $data['experiences']
            as $experience
        ) {

            $topic =
                sanitize_text_field(
                    (string)
                    (
                        $experience[
                            'topic'
                        ]
                        ?? ''
                    )
                );


            if (
                $topic === ''
            ) {

                $result[
                    'errors'
                ]++;

                continue;
            }


            /**
             * Eindeutige Import-ID.
             */
            $import_key =
                'experience:'
                . sanitize_title(
                    $topic
                );


            /**
             * Bereits importiert?
             */
            $existing_id =
                self::find_imported_post(
                    $import_key,
                    'post'
                );


            if (
                $existing_id > 0
            ) {

                $result[
                    'skipped'
                ]++;

                continue;
            }


            /**
             * KI-Text mitzählen.
             */
            if (
                isset(
                    $experience[
                        'review_status'
                    ]
                )
                && $experience[
                    'review_status'
                ] === 'ai_review'
            ) {

                $result[
                    'ai'
                ]++;
            }


            /**
             * Erfahrung importieren.
             */
            $experience_id =
                self::import_experience(
                    $experience,
                    $product_map,
                    $import_key
                );


            if (
                $experience_id <= 0
            ) {

                $result[
                    'errors'
                ]++;

                continue;
            }


            $result[
                'experiences'
            ]++;
        }


        /* =====================================================
           ERGEBNIS
           ===================================================== */

        $redirect_url =
            add_query_arg(
                [

                    'page' =>
                        self::MENU_SLUG,

                    'jl_import' =>
                        'done',

                    'products' =>
                        $result[
                            'products'
                        ],

                    'experiences' =>
                        $result[
                            'experiences'
                        ],

                    'skipped' =>
                        $result[
                            'skipped'
                        ],

                    'errors' =>
                        $result[
                            'errors'
                        ],

                    'ai' =>
                        $result[
                            'ai'
                        ],
                ],

                admin_url(
                    'tools.php'
                )
            );


        wp_safe_redirect(
            $redirect_url
        );

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


        if (
            ! function_exists(
                'WP_Filesystem'
            )
        ) {
            throw new RuntimeException(
                'Die WordPress-Dateifunktionen sind nicht verfügbar.'
            );
        }


        if (
            ! WP_Filesystem()
        ) {
            throw new RuntimeException(
                'Das WordPress-Dateisystem konnte nicht initialisiert werden.'
            );
        }


        global $wp_filesystem;


        if (
            ! $wp_filesystem
        ) {
            throw new RuntimeException(
                'Das WordPress-Dateisystem ist nicht verfügbar.'
            );
        }


        /**
         * Temporäres Verzeichnis.
         */
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


        if (
            ! wp_mkdir_p(
                $temp_dir
            )
        ) {
            throw new RuntimeException(
                'Das temporäre Importverzeichnis konnte nicht erstellt werden.'
            );
        }


        try {

            /**
             * XLSX ist technisch ein ZIP-Archiv.
             */
            $unzipped =
                unzip_file(
                    $path,
                    $temp_dir
                );


            if (
                is_wp_error(
                    $unzipped
                )
            ) {
                throw new RuntimeException(
                    $unzipped
                        ->get_error_message()
                );
            }


            return
                self::read_extracted_xlsx(
                    $temp_dir
                );

        } finally {

            /**
             * Temporäre Excel-Daten wieder entfernen.
             */
            $wp_filesystem
                ->delete(
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

            if (
                ! isset(
                    $sheet_targets[
                        $sheet_name
                    ]
                )
            ) {
                throw new RuntimeException(
                    sprintf(
                        'Die Tabelle «%s» wurde nicht gefunden.',
                        $sheet_name
                    )
                );
            }


            $result[
                $sheet_name
            ] = self::read_sheet(
                $base_dir,
                $sheet_targets[
                    $sheet_name
                ],
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
            trailingslashit(
                $base_dir
            )
            . 'xl/sharedStrings.xml';


        if (
            ! file_exists(
                $path
            )
        ) {
            return [];
        }


        $xml =
            file_get_contents(
                $path
            );


        if (
            ! is_string(
                $xml
            )
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


        $items =
            $xpath->query(
                '//*[local-name()="si"]'
            );


        if (
            ! $items
        ) {
            return [];
        }


        $strings = [];


        foreach (
            $items
            as $item
        ) {

            /**
             * Auch Rich-Text-Zellen können mehrere
             * <t>-Elemente enthalten.
             */
            $text_nodes =
                $xpath->query(
                    './/*[local-name()="t"]',
                    $item
                );


            $value = '';


            if (
                $text_nodes
            ) {

                foreach (
                    $text_nodes
                    as $text_node
                ) {

                    $value .=
                        $text_node
                            ->textContent;
                }
            }


            $strings[] =
                $value;
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
            trailingslashit(
                $base_dir
            )
            . 'xl/styles.xml';


        if (
            ! file_exists(
                $path
            )
        ) {
            return [];
        }


        $xml =
            file_get_contents(
                $path
            );


        if (
            ! is_string(
                $xml
            )
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


        /* -----------------------------------------------------
           Fonts
           ----------------------------------------------------- */

        $font_colors = [];


        $fonts =
            $xpath->query(
                '/*[local-name()="styleSheet"]'
                . '/*[local-name()="fonts"]'
                . '/*[local-name()="font"]'
            );


        if (
            $fonts
        ) {

            foreach (
                $fonts
                as $index => $font
            ) {

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
                        $color_nodes
                            ->item(
                                0
                            );


                    if (
                        $color_node
                        instanceof DOMElement
                    ) {

                        if (
                            $color_node
                                ->hasAttribute(
                                    'rgb'
                                )
                        ) {

                            /**
                             * Excel kann ARGB verwenden:
                             *
                             * FF00B0F0
                             *
                             * Wir benötigen nur:
                             *
                             * 00B0F0
                             */
                            $rgb =
                                strtoupper(
                                    $color_node
                                        ->getAttribute(
                                            'rgb'
                                        )
                                );


                            $color =
                                substr(
                                    $rgb,
                                    -6
                                );

                        } elseif (
                            $color_node
                                ->hasAttribute(
                                    'theme'
                                )
                        ) {

                            $color =
                                'theme:'
                                . $color_node
                                    ->getAttribute(
                                        'theme'
                                    );
                        }
                    }
                }


                $font_colors[
                    (int)
                    $index
                ] = $color;
            }
        }


        /* -----------------------------------------------------
           Zellstyles
           ----------------------------------------------------- */

        $style_colors = [];


        $xfs =
            $xpath->query(
                '/*[local-name()="styleSheet"]'
                . '/*[local-name()="cellXfs"]'
                . '/*[local-name()="xf"]'
            );


        if (
            $xfs
        ) {

            foreach (
                $xfs
                as $index => $xf
            ) {

                $font_id = 0;


                if (
                    $xf
                    instanceof DOMElement
                    && $xf
                        ->hasAttribute(
                            'fontId'
                        )
                ) {

                    $font_id =
                        (int)
                        $xf
                            ->getAttribute(
                                'fontId'
                            );
                }


                $style_colors[
                    (int)
                    $index
                ] =
                    $font_colors[
                        $font_id
                    ]
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
            trailingslashit(
                $base_dir
            )
            . 'xl/workbook.xml';


        $rels_path =
            trailingslashit(
                $base_dir
            )
            . 'xl/_rels/workbook.xml.rels';


        if (
            ! file_exists(
                $workbook_path
            )
            || ! file_exists(
                $rels_path
            )
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
            ! is_string(
                $workbook_xml
            )
            || ! is_string(
                $rels_xml
            )
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


        /* -----------------------------------------------------
           Relationships
           ----------------------------------------------------- */

        $rels_xpath =
            new DOMXPath(
                $rels_document
            );


        $relationships = [];


        $relationship_nodes =
            $rels_xpath->query(
                '//*[local-name()="Relationship"]'
            );


        if (
            $relationship_nodes
        ) {

            foreach (
                $relationship_nodes
                as $relationship
            ) {

                if (
                    ! $relationship
                    instanceof DOMElement
                ) {
                    continue;
                }


                $id =
                    $relationship
                        ->getAttribute(
                            'Id'
                        );


                $target =
                    $relationship
                        ->getAttribute(
                            'Target'
                        );


                if (
                    $id !== ''
                    && $target !== ''
                ) {

                    $relationships[
                        $id
                    ] =
                        self::normalize_xlsx_target(
                            $target
                        );
                }
            }
        }


        /* -----------------------------------------------------
           Tabellen
           ----------------------------------------------------- */

        $workbook_xpath =
            new DOMXPath(
                $workbook_document
            );


        $sheet_nodes =
            $workbook_xpath->query(
                '//*[local-name()="sheet"]'
            );


        $targets = [];


        if (
            $sheet_nodes
        ) {

            foreach (
                $sheet_nodes
                as $sheet
            ) {

                if (
                    ! $sheet
                    instanceof DOMElement
                ) {
                    continue;
                }


                $name =
                    $sheet
                        ->getAttribute(
                            'name'
                        );


                $relationship_id =
                    $sheet
                        ->getAttributeNS(
                            'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                            'id'
                        );


                if (
                    $name !== ''
                    && $relationship_id !== ''
                    && isset(
                        $relationships[
                            $relationship_id
                        ]
                    )
                ) {

                    $targets[
                        $name
                    ] =
                        $relationships[
                            $relationship_id
                        ];
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
     */
    private static function read_sheet(
        string $base_dir,
        string $target,
        array $shared_strings,
        array $style_colors
    ): array {

        $path =
            trailingslashit(
                $base_dir
            )
            . ltrim(
                $target,
                '/'
            );


        if (
            ! file_exists(
                $path
            )
        ) {
            throw new RuntimeException(
                'Eine Excel-Tabelle konnte nicht gefunden werden.'
            );
        }


        $xml =
            file_get_contents(
                $path
            );


        if (
            ! is_string(
                $xml
            )
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


        $cell_nodes =
            $xpath->query(
                '//*[local-name()="sheetData"]'
                . '//*[local-name()="c"]'
            );


        $rows = [];


        if (
            ! $cell_nodes
        ) {
            return $rows;
        }


        foreach (
            $cell_nodes
            as $cell
        ) {

            if (
                ! $cell
                instanceof DOMElement
            ) {
                continue;
            }


            /**
             * Beispiel:
             *
             * C12
             */
            $reference =
                $cell
                    ->getAttribute(
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


            $column =
                $matches[1];


            $row =
                (int)
                $matches[2];


            $type =
                $cell
                    ->getAttribute(
                        't'
                    );


            $style_id =
                $cell
                    ->hasAttribute(
                        's'
                    )
                    ? (int)
                        $cell
                            ->getAttribute(
                                's'
                            )
                    : 0;


            $value = '';


            /* -------------------------------------------------
               Inline String
               ------------------------------------------------- */

            if (
                $type === 'inlineStr'
            ) {

                $text_nodes =
                    $xpath->query(
                        './/*[local-name()="is"]'
                        . '//*[local-name()="t"]',
                        $cell
                    );


                if (
                    $text_nodes
                ) {

                    foreach (
                        $text_nodes
                        as $text_node
                    ) {

                        $value .=
                            $text_node
                                ->textContent;
                    }
                }

            } else {

                /* ---------------------------------------------
                   Standardwert
                   --------------------------------------------- */

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
                            ->item(
                                0
                            )
                            ->textContent;
                }


                /* ---------------------------------------------
                   Shared String
                   --------------------------------------------- */

                if (
                    $type === 's'
                ) {

                    $shared_index =
                        (int)
                        $raw_value;


                    $value =
                        $shared_strings[
                            $shared_index
                        ]
                        ?? '';

                } else {

                    $value =
                        $raw_value;
                }
            }


            $rows[
                $row
            ][
                $column
            ] = [

                'value' =>
                    $value,

                'font_color' =>
                    $style_colors[
                        $style_id
                    ]
                    ?? '',
            ];
        }


        return $rows;
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
                $workbook[
                    'Products'
                ]
                ?? []
            );


        $experiences =
            self::build_experiences(
                $workbook[
                    'Blogs'
                ]
                ?? [],
                $products
            );


        return [

            'products' =>
                $products,

            'experiences' =>
                $experiences,
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

        $products = [];


        foreach (
            $rows
            as $row_number => $row
        ) {

            /**
             * Zeilen 1–4 sind Kopfbereich.
             */
            if (
                (
                    (int)
                    $row_number
                ) < 5
            ) {
                continue;
            }


            $source_id =
                self::cell_value(
                    $row,
                    'B'
                );


            $name =
                self::cell_value(
                    $row,
                    'C'
                );


            /**
             * Leere vorbereitete Produktzeilen
             * wie P_RO_025 und P_RO_026
             * werden nicht importiert.
             */
            if (
                $source_id === ''
                || $name === ''
            ) {
                continue;
            }


            $products[] = [

                'source_id' =>
                    $source_id,

                'name' =>
                    $name,

                'area' =>
                    self::cell_value(
                        $row,
                        'D'
                    ),

                'description' =>
                    self::cell_value(
                        $row,
                        'E'
                    ),

                'manufacturer' =>
                    self::cell_value(
                        $row,
                        'F'
                    ),

                'manufacturer_url' =>
                    self::cell_value(
                        $row,
                        'G'
                    ),

                'source' =>
                    self::cell_value(
                        $row,
                        'H'
                    ),

                'source_url' =>
                    self::cell_value(
                        $row,
                        'I'
                    ),

                'remark' =>
                    self::cell_value(
                        $row,
                        'J'
                    ),

                'b2b' =>
                    self::cell_value(
                        $row,
                        'K'
                    ),

                'alternative_product' =>
                    self::cell_value(
                        $row,
                        'L'
                    ),

                'alternative_manufacturer' =>
                    self::cell_value(
                        $row,
                        'M'
                    ),

                'alternative_url' =>
                    self::cell_value(
                        $row,
                        'N'
                    ),

                'alternative_remark' =>
                    self::cell_value(
                        $row,
                        'O'
                    ),

                'alternative_b2b' =>
                    self::cell_value(
                        $row,
                        'P'
                    ),

                'source_row' =>
                    (int)
                    $row_number,
            ];
        }


        return $products;
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
                (
                    (int)
                    $row_number
                ) < 5
            ) {
                continue;
            }


            $topic =
                self::cell_value(
                    $row,
                    'B'
                );


            /**
             * Leerzeilen sowie die Legende
             * «Blauer Text» überspringen.
             */
            if (
                $topic === ''
                || mb_strtolower(
                    $topic
                ) === 'blauer text'
            ) {
                continue;
            }


            $blog =
                self::cell_value(
                    $row,
                    'C'
                );


            $background =
                self::cell_value(
                    $row,
                    'D'
                );


            if (
                $blog === ''
                && $background === ''
            ) {
                continue;
            }


            /**
             * Erste Zeile des Blogtextes
             * wird zum Beitragstitel.
             */
            [
                $title,
                $body,
            ] =
                self::split_title_and_body(
                    $blog,
                    $topic
                );


            /**
             * Erste Zeile des Wirkungsbeschriebs
             * wird zur Zwischenüberschrift.
             */
            [
                $background_title,
                $background_body,
            ] =
                self::split_title_and_body(
                    $background,
                    ''
                );


            /**
             * Blau = KI-generiert.
             */
            $is_ai =
                self::cell_font_color(
                    $row,
                    'C'
                ) === self::AI_FONT_COLOR
                || self::cell_font_color(
                    $row,
                    'D'
                ) === self::AI_FONT_COLOR;


            $experiences[] = [

                'topic' =>
                    $topic,

                'title' =>
                    $title,

                'body' =>
                    $body,

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
                    (int)
                    $row_number,
            ];
        }


        return $experiences;
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


        foreach (
            $products
            as $product
        ) {

            $source_id =
                (string)
                (
                    $product[
                        'source_id'
                    ]
                    ?? ''
                );


            $name =
                (string)
                (
                    $product[
                        'name'
                    ]
                    ?? ''
                );


            if (
                $source_id === ''
                || $name === ''
            ) {
                continue;
            }


            $map[
                self::normalize_match_text(
                    $name
                )
            ] =
                $source_id;
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


        /**
         * Direkte Übereinstimmung.
         */
        if (
            isset(
                $product_map[
                    $normalized
                ]
            )
        ) {
            return
                $product_map[
                    $normalized
                ];
        }


        /**
         * Namensabweichungen in der Kundendatei.
         */
        $aliases = [

            'deo roll on' =>
                'P_RO_003',

            'hydroxiapatite zahnpasta' =>
                'P_RO_002',

            'l tryptophan' =>
                'P_RO_023',

            'omega 3 6 9' =>
                'P_RO_004',

            'ashwaganda' =>
                'P_RO_005',
        ];


        return
            $aliases[
                $normalized
            ]
            ?? '';
    }


    /* =========================================================
       PRODUKT IMPORTIEREN
       ========================================================= */

    /**
     * Einzelnes Produkt importieren.
     */
    private static function import_product(
        array $product
    ): int {

        $name =
            sanitize_text_field(
                (string)
                (
                    $product[
                        'name'
                    ]
                    ?? ''
                )
            );


        $source_id =
            sanitize_text_field(
                (string)
                (
                    $product[
                        'source_id'
                    ]
                    ?? ''
                )
            );


        if (
            $name === ''
            || $source_id === ''
        ) {
            return 0;
        }


        $description =
            sanitize_textarea_field(
                (string)
                (
                    $product[
                        'description'
                    ]
                    ?? ''
                )
            );


        /* -----------------------------------------------------
           WordPress-Produkt
           ----------------------------------------------------- */

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


        if (
            is_wp_error(
                $post_id
            )
        ) {
            return 0;
        }


        $post_id =
            (int)
            $post_id;


        /* -----------------------------------------------------
           Import-Metadaten
           ----------------------------------------------------- */

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


        update_post_meta(
            $post_id,
            '_jl_source_sheet',
            'Products'
        );


        update_post_meta(
            $post_id,
            '_jl_source_row',
            absint(
                $product[
                    'source_row'
                ]
                ?? 0
            )
        );


        /* -----------------------------------------------------
           Marke
           ----------------------------------------------------- */

        $manufacturer =
            sanitize_text_field(
                (string)
                (
                    $product[
                        'manufacturer'
                    ]
                    ?? ''
                )
            );


        if (
            $manufacturer !== ''
        ) {

            wp_set_object_terms(
                $post_id,
                [
                    $manufacturer,
                ],
                Jung_Leben_Core_Products::TAXONOMY_BRAND,
                false
            );
        }


        /* -----------------------------------------------------
           Gebiet / Produktkategorie
           ----------------------------------------------------- */

        $area =
            sanitize_text_field(
                (string)
                (
                    $product[
                        'area'
                    ]
                    ?? ''
                )
            );


        if (
            $area !== ''
        ) {

            /**
             * Die Kundenbezeichnungen werden zunächst
             * unverändert übernommen:
             *
             * Wohlbefinden
             * Hygiene/Beauty
             *
             * Die Kategorien können danach direkt
             * in WordPress verfeinert werden.
             */
            wp_set_object_terms(
                $post_id,
                [
                    $area,
                ],
                Jung_Leben_Core_Products::TAXONOMY_CATEGORY,
                false
            );
        }


        /* -----------------------------------------------------
           Persönlich getestet
           ----------------------------------------------------- */

        /**
         * Bei diesen vier Produkten existiert bereits
         * ein nicht blau markierter persönlicher
         * Erfahrungsbeitrag in der Kundendatei.
         */
        $personally_tested =
            in_array(
                $source_id,
                [
                    'P_RO_002',
                    'P_RO_003',
                    'P_RO_004',
                    'P_RO_023',
                ],
                true
            )
                ? 1
                : 0;


        /* -----------------------------------------------------
           ACF – Einordnung
           ----------------------------------------------------- */

        update_field(
            'jl_product_recommendation_status',
            'neutral',
            $post_id
        );


        update_field(
            'jl_product_personally_tested',
            $personally_tested,
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
           ACF – Anwendung & Erfahrung
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


        /**
         * Lange Erfahrungen werden bewusst nicht
         * nochmals im Produkt gespeichert.
         *
         * Dafür haben wir den verknüpften
         * Erfahrungsbeitrag.
         */
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
           ACF – Partner
           ----------------------------------------------------- */

        update_field(
            'jl_product_partner_name',
            sanitize_text_field(
                (string)
                (
                    $product[
                        'source'
                    ]
                    ?? ''
                )
            ),
            $post_id
        );


        /**
         * Die Excel-Datei enthält teilweise nur
         * allgemeine Shop-Websites und keine
         * konkreten Produktlinks.
         *
         * Deshalb werden noch keine öffentlichen
         * Kaufbuttons erzeugt.
         */
        update_field(
            'jl_product_original_url',
            '',
            $post_id
        );


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


        update_field(
            'jl_product_button_text',
            __(
                'Produkt beim Partner ansehen',
                'jung-leben-core'
            ),
            $post_id
        );


        update_field(
            'jl_product_link_new_tab',
            1,
            $post_id
        );


        /* -----------------------------------------------------
           ACF – Transparenz
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


        /* -----------------------------------------------------
           Interne Quellenangaben
           ----------------------------------------------------- */

        update_field(
            'jl_product_internal_source_note',
            self::build_product_source_note(
                $product
            ),
            $post_id
        );


        return $post_id;
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
                (string)
                (
                    $experience[
                        'title'
                    ]
                    ?? ''
                )
            );


        if (
            $title === ''
        ) {
            return 0;
        }


        $body =
            sanitize_textarea_field(
                (string)
                (
                    $experience[
                        'body'
                    ]
                    ?? ''
                )
            );


        $background_title =
            sanitize_text_field(
                (string)
                (
                    $experience[
                        'background_title'
                    ]
                    ?? ''
                )
            );


        $background =
            sanitize_textarea_field(
                (string)
                (
                    $experience[
                        'background'
                    ]
                    ?? ''
                )
            );


        /* -----------------------------------------------------
           Gutenberg-Content
           ----------------------------------------------------- */

        $content =
            self::text_to_blocks(
                $body
            );


        /**
         * Wirkungsbeschrieb als Zwischenüberschrift.
         */
        if (
            $background_title !== ''
        ) {

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


        /**
         * Fachlicher Hintergrund.
         */
        if (
            $background !== ''
        ) {

            $content .=
                "\n\n"
                . self::text_to_blocks(
                    $background
                );
        }


        /* -----------------------------------------------------
           Beitrag
           ----------------------------------------------------- */

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
                            $body,
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


        if (
            is_wp_error(
                $post_id
            )
        ) {
            return 0;
        }


        $post_id =
            (int)
            $post_id;


        /* -----------------------------------------------------
           Import-Metadaten
           ----------------------------------------------------- */

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
                $experience[
                    'source_row'
                ]
                ?? 0
            )
        );


        /* -----------------------------------------------------
           WordPress-Kategorie
           ----------------------------------------------------- */

        $category =
            term_exists(
                'Erfahrungen',
                'category'
            );


        if (
            ! $category
        ) {

            $category =
                wp_insert_term(
                    'Erfahrungen',
                    'category'
                );
        }


        if (
            ! is_wp_error(
                $category
            )
        ) {

            $category_id =
                is_array(
                    $category
                )
                    ? (int)
                        $category[
                            'term_id'
                        ]
                    : (int)
                        $category;


            if (
                $category_id > 0
            ) {

                wp_set_post_categories(
                    $post_id,
                    [
                        $category_id,
                    ],
                    false
                );
            }
        }


        /* -----------------------------------------------------
           Prüfstatus
           ----------------------------------------------------- */

        $review_status =
            sanitize_key(
                (string)
                (
                    $experience[
                        'review_status'
                    ]
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


        /* -----------------------------------------------------
           Original-Thema
           ----------------------------------------------------- */

        update_field(
            'jl_experience_source_topic',
            sanitize_text_field(
                (string)
                (
                    $experience[
                        'topic'
                    ]
                    ?? ''
                )
            ),
            $post_id
        );


        /* -----------------------------------------------------
           Produkt verknüpfen
           ----------------------------------------------------- */

        $related_source_id =
            sanitize_text_field(
                (string)
                (
                    $experience[
                        'related_product_source_id'
                    ]
                    ?? ''
                )
            );


        if (
            $related_source_id !== ''
            && isset(
                $product_map[
                    $related_source_id
                ]
            )
        ) {

            update_field(
                'jl_experience_related_product',
                (int)
                $product_map[
                    $related_source_id
                ],
                $post_id
            );
        }


        return $post_id;
    }


    /* =========================================================
       DUPLIKATE VERHINDERN
       ========================================================= */

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
                ]
            );


        if (
            ! is_array(
                $posts
            )
            || empty(
                $posts
            )
        ) {
            return 0;
        }


        return
            (int)
            $posts[0];
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
            ! isset(
                $row[
                    $column
                ]
            )
            || ! is_array(
                $row[
                    $column
                ]
            )
        ) {
            return '';
        }


        return
            trim(
                (string)
                (
                    $row[
                        $column
                    ][
                        'value'
                    ]
                    ?? ''
                )
            );
    }


    /**
     * Schriftfarbe lesen.
     */
    private static function cell_font_color(
        array $row,
        string $column
    ): string {

        if (
            ! isset(
                $row[
                    $column
                ]
            )
            || ! is_array(
                $row[
                    $column
                ]
            )
        ) {
            return '';
        }


        return
            strtoupper(
                (string)
                (
                    $row[
                        $column
                    ][
                        'font_color'
                    ]
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

        $text =
            trim(
                $text
            );


        if (
            $text === ''
        ) {
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
            ! is_array(
                $parts
            )
            || empty(
                $parts
            )
        ) {
            return [
                $fallback_title,
                $text,
            ];
        }


        $title =
            trim(
                (string)
                (
                    $parts[0]
                    ?? ''
                )
            );


        $body =
            trim(
                (string)
                (
                    $parts[1]
                    ?? ''
                )
            );


        if (
            $title === ''
        ) {
            $title =
                $fallback_title;
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
                'xl/'
                . $target
            );


        $normalized = [];


        foreach (
            $parts
            as $part
        ) {

            if (
                $part === ''
                || $part === '.'
            ) {
                continue;
            }


            if (
                $part === '..'
            ) {

                array_pop(
                    $normalized
                );

                continue;
            }


            $normalized[] =
                $part;
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


        libxml_use_internal_errors(
            $previous
        );


        if (
            ! $loaded
        ) {
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

        $text =
            trim(
                $text
            );


        if (
            $text === ''
        ) {
            return '';
        }


        $paragraphs =
            preg_split(
                '/\R{2,}/u',
                $text
            );


        if (
            ! is_array(
                $paragraphs
            )
        ) {
            return '';
        }


        $blocks = [];


        foreach (
            $paragraphs
            as $paragraph
        ) {

            $paragraph =
                trim(
                    $paragraph
                );


            if (
                $paragraph === ''
            ) {
                continue;
            }


            /**
             * Einzelne Zeilenumbrüche im Absatz
             * beibehalten.
             */
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
       INTERNE PRODUKTNOTIZ
       ========================================================= */

    /**
     * Nicht direkt verwendete Excel-Angaben
     * als interne Notiz speichern.
     */
    private static function build_product_source_note(
        array $product
    ): string {

        $lines = [

            'Importquelle: Kundendatei / Products',

            'Excel-Zeile: '
                . absint(
                    $product[
                        'source_row'
                    ]
                    ?? 0
                ),

            'Interne Produkt-ID: '
                . (string)
                (
                    $product[
                        'source_id'
                    ]
                    ?? ''
                ),

            'Gebiet: '
                . (string)
                (
                    $product[
                        'area'
                    ]
                    ?? ''
                ),

            'Hersteller: '
                . (string)
                (
                    $product[
                        'manufacturer'
                    ]
                    ?? ''
                ),

            'Hersteller-Website: '
                . (string)
                (
                    $product[
                        'manufacturer_url'
                    ]
                    ?? ''
                ),

            'Bezug: '
                . (string)
                (
                    $product[
                        'source'
                    ]
                    ?? ''
                ),

            'Bezugs-Website: '
                . (string)
                (
                    $product[
                        'source_url'
                    ]
                    ?? ''
                ),
        ];


        $optional = [

            'Bemerkung' =>
                $product[
                    'remark'
                ]
                ?? '',

            'B2B möglich?' =>
                $product[
                    'b2b'
                ]
                ?? '',

            'Alternative / ergänzendes Produkt' =>
                $product[
                    'alternative_product'
                ]
                ?? '',

            'Alternativer Hersteller' =>
                $product[
                    'alternative_manufacturer'
                ]
                ?? '',

            'Alternative Website' =>
                $product[
                    'alternative_url'
                ]
                ?? '',

            'Bemerkung zur Alternative' =>
                $product[
                    'alternative_remark'
                ]
                ?? '',

            'B2B Alternative möglich?' =>
                $product[
                    'alternative_b2b'
                ]
                ?? '',
        ];


        foreach (
            $optional
            as $label => $value
        ) {

            $value =
                trim(
                    (string)
                    $value
                );


            if (
                $value === ''
            ) {
                continue;
            }


            $lines[] =
                $label
                . ': '
                . $value;
        }


        return
            implode(
                "\n",
                $lines
            );
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

                    'page' =>
                        self::MENU_SLUG,

                    'jl_import' =>
                        'error',
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