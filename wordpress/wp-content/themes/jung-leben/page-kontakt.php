<?php
/**
 * Seite «Kontakt».
 *
 * @package Jung_Leben
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

get_header();


/**
 * ACF-Feld sicher laden.
 */
$get_contact_field = static function (
    string $field_name,
    mixed $fallback = ''
): mixed {
    if (! function_exists('get_field')) {
        return $fallback;
    }

    $value = get_field(
        $field_name
    );

    if (
        $value === null
        || $value === ''
    ) {
        return $fallback;
    }

    return $value;
};


/* =========================================================
   INHALTE
   ========================================================= */

$eyebrow = (string) $get_contact_field(
    'jl_contact_eyebrow',
    'Kontakt'
);

$title = (string) $get_contact_field(
    'jl_contact_title',
    'Hast du eine Frage oder möchtest etwas teilen?'
);

$intro = (string) $get_contact_field(
    'jl_contact_intro',
    'Wir freuen uns über Fragen, Feedback, persönliche Erfahrungen, Produkthinweise oder Ideen für Jung Leben. Schreib uns einfach über das Formular.'
);

$note = (string) $get_contact_field(
    'jl_contact_note',
    'Bitte beachte: Individuelle medizinische Beratungen, Diagnosen oder Therapieempfehlungen können wir nicht anbieten.'
);


/* =========================================================
   STATUS
   ========================================================= */

$contact_status =
    isset(
        $_GET['contact']
    )
        ? sanitize_key(
            wp_unslash(
                $_GET['contact']
            )
        )
        : '';


$status_messages = [
    'success' => [
        'class' =>
            'contact-form-message--success',

        'title' =>
            'Vielen Dank!',

        'text' =>
            'Deine Nachricht wurde erfolgreich übermittelt.',
    ],

    'request' => [
        'class' =>
            'contact-form-message--error',

        'title' =>
            'Die Anfrage konnte nicht verarbeitet werden.',

        'text' =>
            'Bitte lade die Seite neu und versuche es nochmals.',
    ],

    'timing' => [
        'class' =>
            'contact-form-message--error',

        'title' =>
            'Die Formularprüfung konnte nicht abgeschlossen werden.',

        'text' =>
            'Bitte lade die Seite neu, warte kurz und versuche es nochmals.',
    ],

    'nonce' => [
        'class' =>
            'contact-form-message--error',

        'title' =>
            'Die Sicherheitsprüfung ist fehlgeschlagen.',

        'text' =>
            'Bitte lade die Seite neu und versuche es nochmals.',
    ],

    'validation' => [
        'class' =>
            'contact-form-message--error',

        'title' =>
            'Bitte prüfe deine Angaben.',

        'text' =>
            'Mindestens eine Eingabe konnte nicht korrekt validiert werden.',
    ],

    'smtp' => [
        'class' =>
            'contact-form-message--error',

        'title' =>
            'Die Nachricht konnte nicht gesendet werden.',

        'text' =>
            'Bitte versuche es später nochmals.',
    ],

    'mail' => [
        'class' =>
            'contact-form-message--error',

        'title' =>
            'Die Nachricht konnte nicht gesendet werden.',

        'text' =>
            'Bitte versuche es später nochmals.',
    ],

    'limit' => [
        'class' =>
            'contact-form-message--notice',

        'title' =>
            'Bitte kurz warten.',

        'text' =>
            'Es wurden innerhalb kurzer Zeit mehrere Nachrichten gesendet. Bitte versuche es in einigen Minuten erneut.',
    ],
];


$current_status =
    isset(
        $status_messages[
            $contact_status
        ]
    )
        ? $status_messages[
            $contact_status
        ]
        : null;


/* =========================================================
   DATENSCHUTZ
   ========================================================= */

$privacy_page =
    get_page_by_path(
        'datenschutz'
    );

$privacy_url =
    $privacy_page instanceof WP_Post
        ? get_permalink(
            $privacy_page
        )
        : home_url(
            '/datenschutz/'
        );
?>

<main
    id="main-content"
    class="contact-page"
>

    <section
        id="kontaktformular"
        class="contact-section"
    >

        <div class="container contact-section__inner">

            <!-- =================================================
                 HEADER
                 ================================================= -->

            <header class="contact-section__header">

                <p class="eyebrow">
                    <?php
                    echo esc_html(
                        $eyebrow
                    );
                    ?>
                </p>

                <h1>
                    <?php
                    echo esc_html(
                        $title
                    );
                    ?>
                </h1>

                <p class="contact-section__lead">
                    <?php
                    echo esc_html(
                        $intro
                    );
                    ?>
                </p>

            </header>


            <!-- =================================================
                 FORMULAR
                 ================================================= -->

            <div class="contact-form-card">

                <?php
                if (
                    is_array(
                        $current_status
                    )
                ) :
                    ?>

                    <div
                        class="
                            contact-form-message
                            <?php
                            echo esc_attr(
                                $current_status[
                                    'class'
                                ]
                            );
                            ?>
                        "
                        role="<?php
                        echo $contact_status === 'success'
                            ? 'status'
                            : 'alert';
                        ?>"
                    >

                        <strong>
                            <?php
                            echo esc_html(
                                $current_status[
                                    'title'
                                ]
                            );
                            ?>
                        </strong>

                        <p>
                            <?php
                            echo esc_html(
                                $current_status[
                                    'text'
                                ]
                            );
                            ?>
                        </p>

                    </div>

                <?php endif; ?>


                <form
                    class="contact-form"
                    action="<?php
                    echo esc_url(
                        admin_url(
                            'admin-post.php'
                        )
                    );
                    ?>"
                    method="post"
                >

                    <!-- Action -->
                    <input
                        type="hidden"
                        name="action"
                        value="jung_leben_contact_submit"
                    >


                    <!-- Startzeit -->
                    <input
                        type="hidden"
                        name="jl_contact_started"
                        value="<?php
                        echo esc_attr(
                            (string) time()
                        );
                        ?>"
                    >


                    <!-- Nonce -->
                    <?php
                    wp_nonce_field(
                        'jung_leben_contact_submit',
                        'jung_leben_contact_nonce'
                    );
                    ?>


                    <!-- Honeypot -->
                    <div
                        class="contact-form__honeypot"
                        aria-hidden="true"
                    >

                        <label for="jl-company-website">
                            Website
                        </label>

                        <input
                            id="jl-company-website"
                            type="text"
                            name="jl_company_website"
                            value=""
                            autocomplete="off"
                            tabindex="-1"
                        >

                    </div>


                    <!-- Name / E-Mail -->
                    <div class="contact-form__row">

                        <div class="contact-form__field">

                            <label for="jl-contact-name">

                                <?php
                                esc_html_e(
                                    'Name',
                                    'jung-leben'
                                );
                                ?>

                                <span aria-hidden="true">
                                    *
                                </span>

                            </label>

                            <input
                                id="jl-contact-name"
                                type="text"
                                name="jl_contact_name"
                                autocomplete="name"
                                maxlength="120"
                                required
                            >

                        </div>


                        <div class="contact-form__field">

                            <label for="jl-contact-email">

                                <?php
                                esc_html_e(
                                    'E-Mail',
                                    'jung-leben'
                                );
                                ?>

                                <span aria-hidden="true">
                                    *
                                </span>

                            </label>

                            <input
                                id="jl-contact-email"
                                type="email"
                                name="jl_contact_email"
                                autocomplete="email"
                                maxlength="190"
                                required
                            >

                        </div>

                    </div>


                    <!-- Thema -->
                    <div class="contact-form__field">

                        <label for="jl-contact-topic">

                            <?php
                            esc_html_e(
                                'Thema',
                                'jung-leben'
                            );
                            ?>

                            <span aria-hidden="true">
                                *
                            </span>

                        </label>

                        <select
                            id="jl-contact-topic"
                            name="jl_contact_topic"
                            required
                        >

                            <option
                                value=""
                                selected
                                disabled
                            >
                                <?php
                                esc_html_e(
                                    'Bitte auswählen …',
                                    'jung-leben'
                                );
                                ?>
                            </option>

                            <option value="frage">
                                <?php
                                esc_html_e(
                                    'Frage',
                                    'jung-leben'
                                );
                                ?>
                            </option>

                            <option value="feedback">
                                <?php
                                esc_html_e(
                                    'Feedback',
                                    'jung-leben'
                                );
                                ?>
                            </option>

                            <option value="produkt">
                                <?php
                                esc_html_e(
                                    'Produkt oder Empfehlung',
                                    'jung-leben'
                                );
                                ?>
                            </option>

                            <option value="kooperation">
                                <?php
                                esc_html_e(
                                    'Kooperation',
                                    'jung-leben'
                                );
                                ?>
                            </option>

                            <option value="sonstiges">
                                <?php
                                esc_html_e(
                                    'Sonstiges',
                                    'jung-leben'
                                );
                                ?>
                            </option>

                        </select>

                    </div>


                    <!-- Nachricht -->
                    <div class="contact-form__field">

                        <label for="jl-contact-message">

                            <?php
                            esc_html_e(
                                'Nachricht',
                                'jung-leben'
                            );
                            ?>

                            <span aria-hidden="true">
                                *
                            </span>

                        </label>

                        <textarea
                            id="jl-contact-message"
                            name="jl_contact_message"
                            rows="8"
                            minlength="10"
                            maxlength="5000"
                            required
                        ></textarea>

                        <p class="contact-form__field-note">
                            <?php
                            esc_html_e(
                                'Maximal 5’000 Zeichen.',
                                'jung-leben'
                            );
                            ?>
                        </p>

                    </div>


                    <!-- Datenschutz -->
                    <div class="contact-form__privacy">

                        <p>

                            <?php
                            esc_html_e(
                                'Mit dem Absenden werden deine Angaben zur Bearbeitung deiner Anfrage übermittelt.',
                                'jung-leben'
                            );
                            ?>

                            <a
                                href="<?php
                                echo esc_url(
                                    $privacy_url
                                );
                                ?>"
                            >
                                <?php
                                esc_html_e(
                                    'Mehr zum Datenschutz',
                                    'jung-leben'
                                );
                                ?>
                            </a>

                        </p>

                    </div>


                    <!-- Submit -->
                    <button
                        type="submit"
                        class="contact-form__submit"
                    >

                        <span>
                            <?php
                            esc_html_e(
                                'Nachricht senden',
                                'jung-leben'
                            );
                            ?>
                        </span>

                        <span
                            class="contact-form__submit-arrow"
                            aria-hidden="true"
                        >
                            →
                        </span>

                    </button>


                    <!-- Meta -->
                    <div class="contact-form__meta">

                        <span>
                            <?php
                            esc_html_e(
                                '* Pflichtfelder',
                                'jung-leben'
                            );
                            ?>
                        </span>

                        <span class="contact-form__security">

                            <span aria-hidden="true">
                                ✓
                            </span>

                            <?php
                            esc_html_e(
                                'Spamgeschützt',
                                'jung-leben'
                            );
                            ?>

                        </span>

                    </div>

                </form>

            </div>


            <!-- =================================================
                 HINWEIS
                 ================================================= -->

            <?php if ($note !== '') : ?>

                <aside class="contact-section__note">

                    <span class="contact-section__note-label">
                        <?php
                        esc_html_e(
                            'Hinweis',
                            'jung-leben'
                        );
                        ?>
                    </span>

                    <p>
                        <?php
                        echo esc_html(
                            $note
                        );
                        ?>
                    </p>

                </aside>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php
get_footer();