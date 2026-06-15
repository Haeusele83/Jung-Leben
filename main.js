const products = [
  {
    title: "Energie-Balance Guide",
    category: "energie",
    badge: "Ratgeber",
    score: "91%",
    description: "Ein redaktioneller Einstiegsbereich für Produkte und Routinen rund um Energie, Fokus und Alltag.",
    checks: ["Ideal als Content-Hub", "Keine medizinischen Aussagen", "Affiliate-Link frei ersetzbar"],
    affiliateUrl: "https://www.jung-leben.ch/dein-affiliate-link-energie"
  },
  {
    title: "Schlafroutine & Erholung",
    category: "schlaf",
    badge: "Routine",
    score: "89%",
    description: "Vorlage für Empfehlungen zu Abendroutine, Entspannung, Licht, Geräuschen und passenden Wellness-Produkten.",
    checks: ["Fokus auf Gewohnheiten", "Vorsichtige Formulierungen", "Gut für Vergleichsartikel"],
    affiliateUrl: "https://www.jung-leben.ch/dein-affiliate-link-schlaf"
  },
  {
    title: "Supplemente transparent vergleichen",
    category: "ernaehrung",
    badge: "Vergleich",
    score: "93%",
    description: "Bereich für Nahrungsergänzungsmittel mit klarer Deklaration, Anbieterprüfung und neutraler Einordnung.",
    checks: ["Deklaration prüfen", "Health Claims beachten", "Preis und Versand vergleichen"],
    affiliateUrl: "https://www.jung-leben.ch/dein-affiliate-link-supplemente"
  },
  {
    title: "Pflege & Anti-Aging bewusst einordnen",
    category: "pflege",
    badge: "Pflege",
    score: "87%",
    description: "Kartenbereich für Pflegeprodukte, Beauty-Routinen und Inhaltsstoff-Checks ohne überzogene Versprechen.",
    checks: ["Inhaltsstoffe sichtbar", "Routinen statt Wunderwirkung", "Zielgruppe klar"],
    affiliateUrl: "https://www.jung-leben.ch/dein-affiliate-link-pflege"
  },
  {
    title: "SellHealth Partnerbereich",
    category: "ernaehrung",
    badge: "Partnerprogramm",
    score: "Offen",
    description: "Platzhalter für geprüfte Angebote aus einem Gesundheits-Affiliate-Netzwerk. Nur nach eigener Prüfung live schalten.",
    checks: ["Programmbedingungen prüfen", "Linktracking testen", "Rechtliche Texte anpassen"],
    affiliateUrl: "https://www.sellhealth.com/"
  },
  {
    title: "Bewegung & Regeneration",
    category: "energie",
    badge: "Lifestyle",
    score: "88%",
    description: "Bereich für Produkte, Apps oder Hilfsmittel rund um Bewegung, Mobilität und regenerative Gewohnheiten.",
    checks: ["Alltagstauglich", "Einfache Erklärung", "Keine Therapieaussagen"],
    affiliateUrl: "https://www.jung-leben.ch/dein-affiliate-link-bewegung"
  }
];

const productGrid = document.querySelector("#product-grid");
const filterButtons = document.querySelectorAll(".filter-chip");
const navToggle = document.querySelector(".nav-toggle");
const navMenu = document.querySelector(".nav-menu");
const newsletterForm = document.querySelector("#newsletter-form");
const formMessage = document.querySelector("#form-message");
const quizButtons = document.querySelectorAll(".quiz-option");
const quizResult = document.querySelector("#quiz-result");
const cookieBanner = document.querySelector("#cookie-banner");
const cookieAccept = document.querySelector("#cookie-accept");

function renderProducts(filter = "all") {
  const visibleProducts = products.filter((product) => filter === "all" || product.category === filter);

  productGrid.innerHTML = visibleProducts.map((product) => `
    <article class="product-card" data-category="${product.category}">
      <div class="product-card-header">
        <span class="product-badge">${product.badge}</span>
        <span class="product-score">${product.score}</span>
      </div>
      <h3>${product.title}</h3>
      <p>${product.description}</p>
      <ul class="check-list">
        ${product.checks.map((check) => `<li>${check}</li>`).join("")}
      </ul>
      <a class="btn btn-primary" href="${product.affiliateUrl}" target="_blank" rel="sponsored noopener noreferrer">Zum Anbieter</a>
      <p class="affiliate-note">Anzeige / Affiliate-Link</p>
    </article>
  `).join("");
}

filterButtons.forEach((button) => {
  button.addEventListener("click", () => {
    filterButtons.forEach((item) => item.classList.remove("active"));
    button.classList.add("active");
    renderProducts(button.dataset.filter);
  });
});

if (navToggle && navMenu) {
  navToggle.addEventListener("click", () => {
    const isExpanded = navToggle.getAttribute("aria-expanded") === "true";
    navToggle.setAttribute("aria-expanded", String(!isExpanded));
    navMenu.classList.toggle("is-open");
  });

  navMenu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      navToggle.setAttribute("aria-expanded", "false");
      navMenu.classList.remove("is-open");
    });
  });
}

quizButtons.forEach((button) => {
  button.addEventListener("click", () => {
    quizResult.innerHTML = `<strong>${button.dataset.result}</strong><br><span>Starte mit den passenden Empfehlungen und lies zuerst die Prüfkriterien.</span>`;
  });
});

if (newsletterForm) {
  newsletterForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const formData = new FormData(newsletterForm);
    const email = formData.get("email");

    if (!email || !String(email).includes("@")) {
      formMessage.textContent = "Bitte gib eine gültige E-Mail-Adresse ein.";
      return;
    }

    formMessage.textContent = "Danke. Das Formular ist aktuell eine Demo und versendet noch keine Daten.";
    newsletterForm.reset();
  });
}

if (cookieBanner && cookieAccept) {
  const hasAccepted = localStorage.getItem("jungLebenLocalNoticeAccepted") === "true";

  if (!hasAccepted) {
    cookieBanner.classList.add("is-visible");
  }

  cookieAccept.addEventListener("click", () => {
    localStorage.setItem("jungLebenLocalNoticeAccepted", "true");
    cookieBanner.classList.remove("is-visible");
  });
}

renderProducts();
