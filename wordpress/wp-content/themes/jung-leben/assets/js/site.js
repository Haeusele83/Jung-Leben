// Mobile Navigation
const navToggle = document.getElementById("navToggle");
const mainNav = document.getElementById("mainNav");

if (navToggle && mainNav) {
  navToggle.addEventListener("click", () => {
    const isOpen = mainNav.classList.toggle("open");

    navToggle.setAttribute("aria-expanded", String(isOpen));
    navToggle.setAttribute(
      "aria-label",
      isOpen ? "Navigation schliessen" : "Navigation öffnen"
    );
  });
}

// Navigation schliessen, wenn ein Link gewählt wird
const navLinks = document.querySelectorAll(".main-nav a");

navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    if (mainNav) {
      mainNav.classList.remove("open");
    }

    if (navToggle) {
      navToggle.setAttribute("aria-expanded", "false");
      navToggle.setAttribute("aria-label", "Navigation öffnen");
    }
  });
});

// Produktdaten
let products = [];

// HTML-Elemente
const productGrid = document.getElementById("productGrid");
const productDetail = document.getElementById("productDetail");
const filterButtons = document.querySelectorAll(".filter-btn");

// Produkte aus products.json laden
async function loadProducts() {
  if (!productGrid && !productDetail) {
    return;
  }

  try {
    const response = await fetch("./products.json");

    if (!response.ok) {
      throw new Error(`products.json wurde nicht gefunden. Status: ${response.status}`);
    }

    products = await response.json();

    if (productGrid) {
      renderProductOverview(products);
    }

    if (productDetail) {
      renderProductDetail();
    }
  } catch (error) {
    const errorHtml = `
      <div class="products-error">
        <h3>Produktdaten konnten nicht geladen werden.</h3>
        <p>${error.message}</p>
        <p>
          Bitte prüfe, ob die Datei <strong>products.json</strong> im Hauptordner liegt
          und ob du die Seite über Live Server gestartet hast.
        </p>
      </div>
    `;

    if (productGrid) {
      productGrid.innerHTML = errorHtml;
    }

    if (productDetail) {
      productDetail.innerHTML = errorHtml;
    }

    console.error("Fehler beim Laden oder Anzeigen der Produktdaten:", error);
  }
}

// Produktübersicht bewusst reduziert anzeigen
function renderProductOverview(productList) {
  if (!productGrid) {
    return;
  }

  productGrid.innerHTML = "";

  if (!productList || productList.length === 0) {
    productGrid.innerHTML = `
      <div class="products-error">
        <h3>Keine Produkte gefunden.</h3>
        <p>Für diese Kategorie sind aktuell keine Produkte hinterlegt.</p>
      </div>
    `;
    return;
  }

  productList.forEach((product) => {
    const productCard = document.createElement("article");
    productCard.className = "shop-product-card compact-product-card";
    productCard.dataset.category = product.category;

    productCard.innerHTML = `
      <div class="shop-product-image ${product.imageClass}">
        <span class="product-badge">${product.badge}</span>
      </div>

      <div class="shop-product-content compact-product-content">
        <span class="product-tag">${product.categoryLabel}</span>

        <h3>${product.name}</h3>

        <p class="product-teaser">${product.teaser}</p>

        <div class="product-card-actions">
          <a href="${product.reviewUrl}" class="product-cta">
            Erfahrung lesen
          </a>

          <a href="${product.detailUrl}" class="detail-secondary-link">
            Details
          </a>
        </div>
      </div>
    `;

    productGrid.appendChild(productCard);
  });
}

// Produktdetailseite rendern
function renderProductDetail() {
  if (!productDetail) {
    return;
  }

  const params = new URLSearchParams(window.location.search);
  const productId = params.get("id");

  const selectedProduct = products.find((product) => {
    return String(product.id) === String(productId);
  });

  if (!selectedProduct) {
    productDetail.innerHTML = `
      <div class="products-error">
        <h3>Produkt nicht gefunden.</h3>
        <p>
          Das gewünschte Produkt konnte nicht gefunden werden.
          Bitte gehe zurück zur Produktübersicht oder zu den Erfahrungsberichten.
        </p>
        <div class="article-actions">
          <a href="produkte.html" class="btn btn-primary">Zur Produktübersicht</a>
          <a href="wissen.html" class="detail-secondary-link">Zu den Erfahrungsberichten</a>
        </div>
      </div>
    `;
    return;
  }

  const provider = selectedProduct.provider || "";
  const affiliateUrl = selectedProduct.affiliateUrl || "#";

  const isIherbProduct =
    provider.toLowerCase().includes("iherb") ||
    affiliateUrl.toLowerCase().includes("iherb");

  const providerButtonText = isIherbProduct
    ? "Bei iHerb ansehen"
    : "Zum Anbieter";

  const providerHint = isIherbProduct
    ? "Dieses Produkt ist für die spätere iHerb-Anbindung vorbereitet."
    : "Dieses Produkt verweist auf den aktuell vorgesehenen Anbieter.";

  productDetail.innerHTML = `
    <div class="product-detail-layout">
      <div class="product-detail-visual">
        <div class="product-detail-image ${selectedProduct.imageClass}">
          <span class="product-badge">${selectedProduct.badge}</span>
        </div>

        <div class="product-detail-side-note">
          <strong>Robertos Einordnung</strong>
          <p>
            Dieses Produkt wird nicht isoliert betrachtet, sondern im Zusammenhang mit Alltag,
            Routine und persönlicher Verträglichkeit eingeordnet.
          </p>
        </div>
      </div>

      <div class="product-detail-card improved-detail-card">
        <span class="product-tag">${selectedProduct.categoryLabel}</span>

        <h2>${selectedProduct.name}</h2>

        <p class="product-detail-lead">
          ${selectedProduct.description}
        </p>

        <div class="detail-provider-box">
          <div>
            <span>Anbieter</span>
            <strong>${selectedProduct.provider}</strong>
          </div>

          <div>
            <span>Preis</span>
            <strong>${selectedProduct.price}</strong>
          </div>
        </div>

        <div class="detail-meta-list improved-meta-list">
          <div class="product-meta">
            <span>Fokus</span>
            <strong>${selectedProduct.focus}</strong>
          </div>

          <div class="product-meta">
            <span>Produkttyp</span>
            <strong>${selectedProduct.type}</strong>
          </div>

          <div class="product-meta">
            <span>Einordnung</span>
            <strong>${selectedProduct.categoryLabel}</strong>
          </div>

          <div class="product-meta">
            <span>Bewertung</span>
            <strong>★ ${selectedProduct.rating} / 5</strong>
          </div>
        </div>

        <div class="product-detail-actions improved-detail-actions">
          <a href="${selectedProduct.affiliateUrl}" class="product-cta" target="_blank" rel="nofollow sponsored">
            ${providerButtonText}
          </a>

          <a href="${selectedProduct.reviewUrl}" class="detail-secondary-link">
            Erfahrungsbericht lesen
          </a>

          <a href="wissen.html#routine" class="detail-secondary-link">
            Routine ansehen
          </a>
        </div>

        <p class="provider-hint">
          ${providerHint}
        </p>
      </div>
    </div>

    <div class="product-detail-text improved-detail-text">
      <p class="eyebrow">Einordnung</p>
      <h2>Warum dieses Produkt interessant sein kann</h2>
      <p>${selectedProduct.detailText}</p>

      <div class="article-verdict">
        <strong>Wichtiger Hinweis</strong>
        <p>
          Die Angaben auf Jung Leben dienen der persönlichen Orientierung und ersetzen keine medizinische Beratung.
          Preise, Verfügbarkeit und Produktinformationen können sich beim Anbieter ändern.
          Prüfe vor dem Kauf immer die Angaben auf der Anbieterseite.
        </p>
      </div>
    </div>
  `;
}

// Produktfilter
filterButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const selectedFilter = button.dataset.filter;

    filterButtons.forEach((btn) => {
      btn.classList.remove("active");
    });

    button.classList.add("active");

    if (selectedFilter === "all") {
      renderProductOverview(products);
      return;
    }

    const filteredProducts = products.filter((product) => {
      return product.category === selectedFilter;
    });

    renderProductOverview(filteredProducts);
  });
});

// Newsletter-Formular als Prototyp
const newsletterForm = document.querySelector(".newsletter-form");

if (newsletterForm) {
  newsletterForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const input = newsletterForm.querySelector("input");

    if (input && input.value.trim() !== "") {
      alert("Danke für deine Anmeldung. Das Formular ist aktuell als Prototyp umgesetzt.");
      input.value = "";
    }
  });
}

// Produktdaten beim Laden der Seite abrufen
loadProducts();
