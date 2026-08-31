/**
 * Verknüpft die Tagesroutinen auf der Startseite mit den
 * passenden Filtern auf der Empfehlungsseite.
 */

document.addEventListener("DOMContentLoaded", () => {
    const routineLinks = [
        {
            selector: ".routine-time--morning",
            routine: "morning",
            label: "Empfehlungen für morgens ansehen",
        },
        {
            selector: ".routine-time--midday",
            routine: "midday",
            label: "Empfehlungen für mittags ansehen",
        },
        {
            selector: ".routine-time--evening",
            routine: "evening",
            label: "Empfehlungen für abends ansehen",
        },
    ];

    /**
     * Zieladresse für einen Routinefilter erstellen.
     *
     * @param {string} routine
     * @returns {string}
     */
    const buildRecommendationsUrl = (routine) => {
        const configuredUrl =
            window.JungLebenRoutineLinks
                ?.recommendationsUrl
            || "/empfehlungen/";

        const url = new URL(
            configuredUrl,
            window.location.origin
        );

        url.searchParams.set(
            "routine",
            routine
        );

        return url.toString();
    };

    routineLinks.forEach((item) => {
        const card = document.querySelector(
            item.selector
        );

        if (!card) {
            return;
        }

        const targetUrl = buildRecommendationsUrl(
            item.routine
        );

        card.classList.add(
            "routine-time--linkable"
        );

        card.setAttribute(
            "role",
            "link"
        );

        card.setAttribute(
            "tabindex",
            "0"
        );

        card.setAttribute(
            "aria-label",
            item.label
        );

        const arrow = document.createElement(
            "span"
        );

        arrow.className = "routine-time__arrow";
        arrow.setAttribute(
            "aria-hidden",
            "true"
        );
        arrow.textContent = "→";

        card.appendChild(arrow);

        const openRecommendations = () => {
            window.location.href = targetUrl;
        };

        card.addEventListener(
            "click",
            (event) => {
                if (
                    event.target.closest(
                        "a, button, input, select, textarea"
                    )
                ) {
                    return;
                }

                openRecommendations();
            }
        );

        card.addEventListener(
            "keydown",
            (event) => {
                if (
                    event.key !== "Enter"
                    && event.key !== " "
                ) {
                    return;
                }

                event.preventDefault();
                openRecommendations();
            }
        );
    });
});
