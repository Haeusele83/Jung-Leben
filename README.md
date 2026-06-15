# Jung Leben – Affiliate-Website für Gesundheits- und Wellnessprodukte

Dies ist ein professionelles statisches Frontend-Projekt für VS Code.

## Dateien

- `index.html` – Startseite mit Hero, Themenwelten, Produktkarten, Filter, Wellness-Kompass und Newsletter-Demo
- `assets/css/styles.css` – vollständiges Responsive Design
- `assets/js/main.js` – Produktdaten, Filterlogik, Navigation, Demo-Formular und lokaler Hinweis
- `impressum.html` – Impressum-Vorlage
- `datenschutz.html` – Datenschutz-Vorlage
- `affiliate-hinweis.html` – Transparenzseite für Affiliate-Links
- `robots.txt` und `sitemap.xml` – technische SEO-Grundlagen

## Start in VS Code

1. Ordner in VS Code öffnen.
2. Optional die Extension «Live Server» installieren.
3. Rechtsklick auf `index.html` → «Open with Live Server».

## Affiliate-Links ersetzen

Die Affiliate-Links befinden sich in `assets/js/main.js` im Array `products` beim Feld `affiliateUrl`.

Beispiel:

```js
affiliateUrl: "https://dein-partnerlink.ch/..."
```

## Wichtig vor Livegang

- Impressum mit echten Angaben ergänzen.
- Datenschutz auf Hosting, Tools, Newsletter und Tracking anpassen.
- Affiliate-Programme und Produktclaims rechtlich prüfen.
- Keine Heilversprechen verwenden.
- Bilder, Produktdaten, Preise und Anbieterinformationen aktuell halten.
