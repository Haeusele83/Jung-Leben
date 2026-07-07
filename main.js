// Mobile Navigation
const navToggle = document.getElementById("navToggle");
const mainNav = document.getElementById("mainNav");

if (navToggle && mainNav) {
  navToggle.addEventListener("click", () => {
    mainNav.classList.toggle("open");
  });
}

// Navigation schliessen, wenn ein Link geklickt wird
const navLinks = document.querySelectorAll(".main-nav a");

navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    if (mainNav) {
      mainNav.classList.remove("open");
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
    const response = await fetch("products.json");

    if (!response.ok) {
      throw new Error("Produktdaten konnten nicht geladen werden.");
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

    console.error(error);
  }
}

// Produktübersicht bewusst reduziert rendern
function renderProductOverview(productList) {
  if (!productGrid) {
    return;
  }

  productGrid.innerHTML = "";

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

        <a href="${product.reviewUrl}" class="product-cta">
          Erfahrungsbericht lesen
        </a>
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
          Bitte gehe zurück zur Produktübersicht.
        </p>
        <a href="produkte.html" class="btn btn-primary">Zur Produktübersicht</a>
      </div>
    `;
    return;
  }

  productDetail.innerHTML = `
    <div class="product-detail-grid">
      <div class="product-detail-image ${selectedProduct.imageClass}">
        <span class="product-badge">${selectedProduct.badge}</span>
      </div>

      <div class="product-detail-card">
        <span class="product-tag">${selectedProduct.categoryLabel}</span>

        <h2>${selectedProduct.name}</h2>

        <p class="product-detail-lead">
          ${selectedProduct.description}
        </p>

        <div class="product-detail-price">
          <span>Preis</span>
          <strong>${selectedProduct.price}</strong>
        </div>

        <div class="detail-meta-list">
          <div class="product-meta">
            <span>Anbieter</span>
            <strong>${selectedProduct.provider}</strong>
          </div>

          <div class="product-meta">
            <span>Bewertung</span>
            <strong>★ ${selectedProduct.rating} / 5</strong>
          </div>

          <div class="product-meta">
            <span>Fokus</span>
            <strong>${selectedProduct.focus}</strong>
          </div>

          <div class="product-meta">
            <span>Typ</span>
            <strong>${selectedProduct.type}</strong>
          </div>
        </div>

        <div class="product-detail-actions">
          <a href="${selectedProduct.affiliateUrl}" class="product-cta" target="_blank" rel="nofollow sponsored">
            Zum Anbieter
          </a>

          <a href="${selectedProduct.reviewUrl}" class="detail-secondary-link">
            Erfahrungsbericht nochmals lesen
          </a>
        </div>
      </div>
    </div>

    <div class="product-detail-text">
      <p class="eyebrow">Einordnung</p>
      <h2>Warum dieses Produkt interessant sein kann</h2>
      <p>${selectedProduct.detailText}</p>

      <div class="article-verdict">
        <strong>Hinweis</strong>
        <p>
          Der Preis und die Verfügbarkeit können sich beim Anbieter ändern. Die Angaben auf Jung Leben dienen
          als Orientierung und werden später bei einer echten iHerb-/Affiliate-Anbindung dynamisch aktualisiert.
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