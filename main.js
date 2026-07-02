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

// Lokale Test-Datenbank für Demo-Affiliate-Produkte
const testProducts = [
  {
    id: 1,
    name: "Vital Glow Collagen",
    category: "beauty",
    categoryLabel: "Beauty",
    provider: "Demo Partner",
    price: "CHF 39.90",
    rating: "4.6",
    imageClass: "image-beauty",
    focus: "Haut & Glow",
    type: "Beauty Supplement",
    description:
      "Ein modernes Testprodukt für Menschen, die Beauty, Pflege und bewusste Routinen verbinden möchten.",
    affiliateUrl: "#",
    badge: "Beliebt"
  },
  {
    id: 2,
    name: "Green Balance Complex",
    category: "balance",
    categoryLabel: "Balance",
    provider: "Demo Partner",
    price: "CHF 34.90",
    rating: "4.4",
    imageClass: "image-balance",
    focus: "Innere Balance",
    type: "Daily Support",
    description:
      "Eine Demo-Auswahl für mehr Struktur, Achtsamkeit und Balance im Alltag.",
    affiliateUrl: "#",
    badge: "Neu"
  },
  {
    id: 3,
    name: "Calm Night Magnesium",
    category: "schlaf",
    categoryLabel: "Schlaf",
    provider: "Demo Partner",
    price: "CHF 29.90",
    rating: "4.5",
    imageClass: "image-sleep",
    focus: "Abendroutine",
    type: "Mineralstoff",
    description:
      "Ein Testprodukt für ruhige Abendrituale und eine bewusst gestaltete Schlafroutine.",
    affiliateUrl: "#",
    badge: "Top bewertet"
  },
  {
    id: 4,
    name: "Focus Energy Capsules",
    category: "energie",
    categoryLabel: "Energie",
    provider: "Demo Partner",
    price: "CHF 32.90",
    rating: "4.3",
    imageClass: "image-energy",
    focus: "Fokus & Energie",
    type: "Kapseln",
    description:
      "Für einen aktiven Start in den Tag und mehr Klarheit in anspruchsvollen Alltagssituationen.",
    affiliateUrl: "#",
    badge: "Empfohlen"
  },
  {
    id: 5,
    name: "Gut Harmony Probiotic",
    category: "verdauung",
    categoryLabel: "Verdauung",
    provider: "Demo Partner",
    price: "CHF 44.90",
    rating: "4.2",
    imageClass: "image-digestion",
    focus: "Bauchgefühl",
    type: "Probiotikum",
    description:
      "Ein Demo-Produkt für Personen, die Verdauung, Ernährung und Wohlbefinden bewusster betrachten möchten.",
    affiliateUrl: "#",
    badge: "Demo"
  },
  {
    id: 6,
    name: "Skin Fresh Serum",
    category: "beauty",
    categoryLabel: "Beauty",
    provider: "Demo Partner",
    price: "CHF 49.90",
    rating: "4.7",
    imageClass: "image-serum",
    focus: "Frische Haut",
    type: "Pflege",
    description:
      "Ein hochwertig inszeniertes Testprodukt für Self-Care, Pflege und ein frisches Hautgefühl.",
    affiliateUrl: "#",
    badge: "Premium"
  },
  {
    id: 7,
    name: "Morning Greens Powder",
    category: "energie",
    categoryLabel: "Energie",
    provider: "Demo Partner",
    price: "CHF 36.90",
    rating: "4.1",
    imageClass: "image-greens",
    focus: "Morgenroutine",
    type: "Pulver",
    description:
      "Ein Demo-Produkt für grüne Morgenroutinen und einen bewussten Start in den Tag.",
    affiliateUrl: "#",
    badge: "Routine"
  },
  {
    id: 8,
    name: "Relax Herbal Drops",
    category: "balance",
    categoryLabel: "Balance",
    provider: "Demo Partner",
    price: "CHF 27.90",
    rating: "4.0",
    imageClass: "image-relax",
    focus: "Entspannung",
    type: "Tropfen",
    description:
      "Eine Demo-Auswahl für kurze Entspannungsmomente und kleine Ruheinseln im Alltag.",
    affiliateUrl: "#",
    badge: "Sanft"
  }
];

// Produktkarten rendern
const productGrid = document.getElementById("productGrid");

function renderProducts(products) {
  if (!productGrid) {
    return;
  }

  productGrid.innerHTML = "";

  products.forEach((product) => {
    const productCard = document.createElement("article");
    productCard.className = "shop-product-card";
    productCard.dataset.category = product.category;

    productCard.innerHTML = `
      <div class="shop-product-image ${product.imageClass}">
        <span class="product-badge">${product.badge}</span>
      </div>

      <div class="shop-product-content">
        <div class="product-topline">
          <span class="product-tag">${product.categoryLabel}</span>
          <span class="product-rating">★ ${product.rating}</span>
        </div>

        <h3>${product.name}</h3>

        <p>${product.description}</p>

        <div class="product-meta">
          <span>Anbieter</span>
          <strong>${product.provider}</strong>
        </div>

        <div class="product-meta">
          <span>Fokus</span>
          <strong>${product.focus}</strong>
        </div>

        <div class="product-meta">
          <span>Typ</span>
          <strong>${product.type}</strong>
        </div>

        <div class="product-price-row">
          <span>Demo-Preis</span>
          <strong>${product.price}</strong>
        </div>

        <a href="${product.affiliateUrl}" class="product-cta" target="_blank" rel="nofollow sponsored">
          Zum Anbieter
        </a>
      </div>
    `;

    productGrid.appendChild(productCard);
  });
}

// Produkte initial laden
renderProducts(testProducts);

// Produktfilter
const filterButtons = document.querySelectorAll(".filter-btn");

filterButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const selectedFilter = button.dataset.filter;

    filterButtons.forEach((btn) => {
      btn.classList.remove("active");
    });

    button.classList.add("active");

    if (selectedFilter === "all") {
      renderProducts(testProducts);
      return;
    }

    const filteredProducts = testProducts.filter((product) => {
      return product.category === selectedFilter;
    });

    renderProducts(filteredProducts);
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