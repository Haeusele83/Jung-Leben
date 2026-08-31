/**
 * Produktfilter für Jung Leben.
 */

document.addEventListener("DOMContentLoaded", () => {
    const catalogue = document.querySelector(
        "[data-product-catalogue]"
    );

    if (!catalogue) {
        return;
    }

    const cards = Array.from(
        catalogue.querySelectorAll(
            "[data-product-card]"
        )
    );

    const categoryFilters = Array.from(
        catalogue.querySelectorAll(
            "[data-product-filter]"
        )
    );

    const searchInput =
        catalogue.querySelector(
            "[data-product-search]"
        );

    const brandFilter =
        catalogue.querySelector(
            "[data-product-brand-filter]"
        );

    const routineFilter =
        catalogue.querySelector(
            "[data-product-routine-filter]"
        );

    const resetButton =
        catalogue.querySelector(
            "[data-product-reset]"
        );

    const countElement =
        catalogue.querySelector(
            "[data-product-count]"
        );

    const emptyElement =
        catalogue.querySelector(
            "[data-product-empty]"
        );

    const validRoutineFilters = [
        "morning",
        "midday",
        "evening",
        "flexible",
    ];

    let activeCategory = "all";
    let activeBrand = "all";
    let activeRoutine = "all";
    let searchTerm = "";

    /**
     * Text vereinheitlichen.
     *
     * @param {string} value
     * @returns {string}
     */
    const normalizeText = (value) => {
        return String(value)
            .toLocaleLowerCase("de-CH")
            .trim();
    };

    /**
     * Leerzeichengetrennte Data-Attribute als Array einlesen.
     *
     * @param {string} value
     * @returns {string[]}
     */
    const getValues = (value) => {
        return String(value || "")
            .split(" ")
            .map((item) => item.trim())
            .filter(Boolean);
    };

    /**
     * Prüfen, ob ein Select eine bestimmte Option besitzt.
     *
     * @param {HTMLSelectElement|null} select
     * @param {string} value
     * @returns {boolean}
     */
    const selectHasValue = (
        select,
        value
    ) => {
        if (!select) {
            return false;
        }

        return Array.from(
            select.options
        ).some(
            (option) => option.value === value
        );
    };

    /**
     * Aktiven Kategoriebutton visuell aktualisieren.
     */
    const updateCategoryButtons = () => {
        categoryFilters.forEach(
            (filterButton) => {
                const isActive =
                    filterButton.dataset
                        .productFilter
                    === activeCategory;

                filterButton.classList.toggle(
                    "is-active",
                    isActive
                );

                filterButton.setAttribute(
                    "aria-pressed",
                    isActive
                        ? "true"
                        : "false"
                );
            }
        );
    };

    /**
     * Aktuelle Filter in der URL abbilden.
     * Dadurch können gefilterte Ansichten geteilt oder direkt
     * von anderen Bereichen der Website aufgerufen werden.
     */
    const updateUrl = () => {
        const url = new URL(
            window.location.href
        );

        const setOrDelete = (
            key,
            value,
            defaultValue = "all"
        ) => {
            if (
                value
                && value !== defaultValue
            ) {
                url.searchParams.set(
                    key,
                    value
                );
            } else {
                url.searchParams.delete(key);
            }
        };

        setOrDelete(
            "category",
            activeCategory
        );

        setOrDelete(
            "brand",
            activeBrand
        );

        setOrDelete(
            "routine",
            activeRoutine
        );

        if (searchTerm !== "") {
            url.searchParams.set(
                "q",
                searchInput
                    ? searchInput.value.trim()
                    : searchTerm
            );
        } else {
            url.searchParams.delete("q");
        }

        window.history.replaceState(
            {},
            "",
            url
        );
    };

    /**
     * Produkte entsprechend der aktiven Filter ein- oder
     * ausblenden.
     *
     * @param {boolean} syncUrl
     */
    const updateProducts = (
        syncUrl = true
    ) => {
        let visibleCount = 0;

        cards.forEach((card) => {
            const categories = getValues(
                card.dataset.productCategories
            );

            const brands = getValues(
                card.dataset.productBrands
            );

            const routineTimes = getValues(
                card.dataset.productRoutineTimes
            );

            const searchableText =
                normalizeText(
                    card.dataset.productSearchText
                );

            const matchesCategory =
                activeCategory === "all"
                || categories.includes(
                    activeCategory
                );

            const matchesBrand =
                activeBrand === "all"
                || brands.includes(
                    activeBrand
                );

            const matchesRoutine =
                activeRoutine === "all"
                || routineTimes.includes(
                    activeRoutine
                );

            const matchesSearch =
                searchTerm === ""
                || searchableText.includes(
                    searchTerm
                );

            const isVisible =
                matchesCategory
                && matchesBrand
                && matchesRoutine
                && matchesSearch;

            card.hidden = !isVisible;

            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (countElement) {
            countElement.textContent =
                String(visibleCount);
        }

        if (emptyElement) {
            emptyElement.hidden =
                visibleCount !== 0;
        }

        if (syncUrl) {
            updateUrl();
        }
    };

    /**
     * Filterwerte aus der URL übernehmen.
     * Unterstützte Parameter:
     * ?category=...
     * ?brand=...
     * ?routine=morning|midday|evening|flexible
     * ?q=...
     *
     * @returns {boolean}
     */
    const applyUrlFilters = () => {
        const params = new URLSearchParams(
            window.location.search
        );

        let hasInitialFilter = false;

        const requestedCategory =
            params.get("category") || "";

        const requestedBrand =
            params.get("brand") || "";

        const requestedRoutine =
            params.get("routine") || "";

        const requestedSearch =
            params.get("q") || "";

        const validCategory =
            categoryFilters.some(
                (filterButton) => {
                    return filterButton.dataset
                        .productFilter
                        === requestedCategory;
                }
            );

        if (
            requestedCategory
            && validCategory
        ) {
            activeCategory =
                requestedCategory;
            hasInitialFilter = true;
        }

        if (
            requestedBrand
            && selectHasValue(
                brandFilter,
                requestedBrand
            )
        ) {
            activeBrand = requestedBrand;

            if (brandFilter) {
                brandFilter.value =
                    requestedBrand;
            }

            hasInitialFilter = true;
        }

        if (
            requestedRoutine
            && validRoutineFilters.includes(
                requestedRoutine
            )
            && selectHasValue(
                routineFilter,
                requestedRoutine
            )
        ) {
            activeRoutine =
                requestedRoutine;

            if (routineFilter) {
                routineFilter.value =
                    requestedRoutine;
            }

            hasInitialFilter = true;
        }

        if (
            requestedSearch.trim() !== ""
        ) {
            searchTerm = normalizeText(
                requestedSearch
            );

            if (searchInput) {
                searchInput.value =
                    requestedSearch;
            }

            hasInitialFilter = true;
        }

        updateCategoryButtons();

        return hasInitialFilter;
    };

    /**
     * Kategorie auswählen.
     */
    categoryFilters.forEach((filter) => {
        filter.addEventListener(
            "click",
            () => {
                activeCategory =
                    filter.dataset.productFilter
                    || "all";

                updateCategoryButtons();
                updateProducts();
            }
        );
    });

    /**
     * Suche.
     */
    if (searchInput) {
        searchInput.addEventListener(
            "input",
            () => {
                searchTerm =
                    normalizeText(
                        searchInput.value
                    );

                updateProducts();
            }
        );
    }

    /**
     * Marke.
     */
    if (brandFilter) {
        brandFilter.addEventListener(
            "change",
            () => {
                activeBrand =
                    brandFilter.value
                    || "all";

                updateProducts();
            }
        );
    }

    /**
     * Tageszeit.
     */
    if (routineFilter) {
        routineFilter.addEventListener(
            "change",
            () => {
                activeRoutine =
                    routineFilter.value
                    || "all";

                updateProducts();
            }
        );
    }

    /**
     * Alle Filter zurücksetzen.
     */
    if (resetButton) {
        resetButton.addEventListener(
            "click",
            () => {
                activeCategory = "all";
                activeBrand = "all";
                activeRoutine = "all";
                searchTerm = "";

                if (searchInput) {
                    searchInput.value = "";
                }

                if (brandFilter) {
                    brandFilter.value = "all";
                }

                if (routineFilter) {
                    routineFilter.value = "all";
                }

                updateCategoryButtons();
                updateProducts();
            }
        );
    }

    const hasInitialFilter =
        applyUrlFilters();

    updateProducts(false);

    /**
     * Bei einem direkten Filterlink aus einem anderen Bereich
     * direkt zum Katalog scrollen.
     */
    if (hasInitialFilter) {
        window.requestAnimationFrame(
            () => {
                catalogue.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        );
    }
});
