# Prüfbericht — abfaltersbach.at, Aktualisierung vom 21.08.2026

**Anlass:** Erster Re-Scan **nach** dem Rollout der Korrekturen vom 18.08. und 19.08. sowie nach der
Migration von Bootstrap 4.6.2 auf 5.3.7. Dieser Bericht ergänzt die
[Aktualisierung vom 19.08.2026](abfaltersbach-2026-08-19.md) und den
[Prüfbericht vom 12.08.2026](abfaltersbach-2026-08-12.md).

**Maßstab:** WCAG 2.1 Stufe AA über EN 301 549 V3.2.1 (2021-03).

## Rollout

Die drei Code-Korrekturen, die am 18.08. und 19.08. noch als „Rollout ausstehend" geführt wurden,
sind seit **20.08.2026** live: Kontrast der Dateigrößen-Angabe, Unterstreichung des
Datenschutz-Links im Cookie-Hinweis und `overflow-wrap` auf den Personenkarten. Im selben Rollout
ging die Bootstrap-5-Migration live.

Nachgemessen direkt auf der ausgelieferten Seite:

| Korrektur | Messung live |
|---|---|
| Kontrast Dateigröße (SC 1.4.3) | `#333` auf `#ebb60a` = **6,76:1** (gefordert 4,5:1) |
| Datenschutz-Link (SC 1.4.1) | `text-decoration: underline` |
| Reflow Personenkarten (SC 1.4.10) | `overflow-wrap: anywhere` aktiv |

## Methode

Dasselbe Werkzeug wie am 12.08. und 18.08.: axe-core 4.12.1 in echtem Chromium über Playwright,
Tags `wcag2a, wcag21a, wcag2aa, wcag21aa, best-practice`, dazu die neun Check-Module aus
`biegl/wcag-check` (`modules/ScannerPlugins/Engines/PlaywrightAxe/Node`). 36 Seiten, alle mit
HTTP 200, kein Modulabsturz (`checkErrors` leer). Der Scan lief gegen die Live-Seite, nicht gegen
eine lokale Kopie.

Zusätzlich eine gezielte manuelle Tastaturprüfung der eingebetteten Karte (siehe unten), weil die
automatisierte Zählung dort nachweislich nicht verlässlich ist.

## Ergebnis

| Kriterium | 12.08. | 18.08. | **21.08.** | Bemerkung |
|---|---|---|---|---|
| 1.4.3 Kontrast (Minimum) | 155 | 156 | **0** | behoben |
| 1.4.1 Verwendung von Farbe | 15 | 15 | **0** | behoben |
| 1.4.10 Reflow | 19 | 21 | **3** | Restbefund, siehe unten |
| 4.1.2 Name, Rolle, Wert | 1 | 1 | **0** | behoben (iframe-Titel) |
| 2.4.7 Fokus sichtbar | 146 | 179 | 177 | Befund entfällt weiterhin, siehe 18.08. |
| Best-Practice-Hinweise | 220 | 220 | **42** | `region` 36, `heading-order` 6 |

**Kein einziger WCAG-Verstoß aus axe.** Die 42 verbleibenden axe-Befunde sind ausschließlich
Best-Practice-Hinweise. `landmark-one-main`, `empty-heading` und `page-has-heading-one` sind
vollständig verschwunden.

Die 568 Befunde vom Typ „zu prüfen" sind alle `color-contrast` — axe kann dort das
Kontrastverhältnis nicht selbst entscheiden (Hintergrundbilder, Transparenz). Sie sind keine
festgestellten Verstöße und belegen nach ADR-0008 keine Unvereinbarkeit.

### Nebeneffekt der Bootstrap-5-Migration: weniger horizontaler Überlauf

Bootstrap 5 aktiviert RFS (Responsive Font Sizes) per Default; Überschriften im Seiteninhalt
skalieren auf schmalen Viewports herunter. Gemessen bei 375 px vorher/nachher: Amtstafel und
Beratungsstellen 39 → 0 px, Gemeinderatsprotokolle 48 → 0, Rechnungsabschlüsse 5 → 0,
Zimmervermieter 112 → 52. Ursache war eine lange, nicht umbrechbare Überschrift („Sprechtage der
Pensionsversicherungsanstalt"), die bei 28,8 px nicht in die Spalte passte. Bei 1280 px ist die
Darstellung gegenüber dem 4.6.2-Build unverändert — nachgewiesen über alle 35 Navigationsseiten.

## Behoben mit diesem Bericht

- **SC 1.4.10, drei Verstöße auf `/gemeinde/mitarbeiter`:** „Kindergartenpädagogin/Kindergarten&shy;leiterin"
  sprengt bei 320 px die Spalte. Die Korrektur vom 18.08. deckte nur `.person-contact` ab, `.person-name`
  und `.person-role` fehlten. Mit genau diesem Text gemessen: vorher 38 px Überlauf, jetzt 0.
- **SC 1.3.1, Überschriftensprung auf sechs Seiten:** auf der Startseite folgte auf die neue `h1`
  „News" direkt `h3`. Die Meldungstitel sind jetzt `h2`, der Schriftgrad bleibt bei 25,2 px.
  Die fünf übrigen Sprünge stehen im CMS-Seiteninhalt (`h3` ohne vorangehende `h2` auf
  `/gemeinde/rechnungsabschluesse`, `/gemeinde/voranschlaege`, `/gemeinde/protokolle`,
  `/fotogalerie`, `/impressum`) und sind redaktionell zu korrigieren.
- **axe-Regel `region` (36 Fundstellen):** der Cookie-Hinweis lag außerhalb jeder Landmarke, jetzt
  `<aside>` mit `aria-label`.

## SC 2.1.2 Tastaturfalle — geprüft, kein Befund

Der am 19.08. offen gebliebene Punkt ist jetzt geprüft. Das Check-Modul meldet für das
Google-Maps-`iframe` auf `/gemeinde/lage` weiterhin eine Tastaturfalle. Die manuelle Prüfung
widerlegt das:

- Der Fokus erreicht die Karte nach **53 Tabulatorschritten** vom Seitenanfang.
- **Ein weiterer Tabulatorschritt** verlässt sie wieder — Ziel ist der Link „View Larger Map", der
  im Hauptdokument direkt hinter `</iframe>` steht (Teil des klassischen Google-Maps-Einbettungscodes).
- **Umschalt+Tab** verlässt die Karte ebenfalls nach einem Schritt.

**Ursache der Fehlmeldung:** Der Fokus im Hauptdokument steht auf dem `iframe`-Element, sobald er in
den Frame gewandert ist. Wer nur das Hauptdokument betrachtet, sieht denselben Wert und schließt auf
einen stehengebliebenen Fokus. Belastbar ist die Prüfung nur, wenn man `document.activeElement` je
Frame einzeln ausliest — dieselbe Klasse von Werkzeugartefakt wie beim Fokusrahmen-Befund vom 18.08.
Das wäre im Projekt `biegl/wcag-check` zu beheben (`Node/checks/keyboard.mjs`).

**Ergebnis:** SC 2.1.2 ist auf `/gemeinde/lage` erfüllt.

## Offene Punkte

- **Erklärung zur Barrierefreiheit ist nicht mehr aktuell.** Unter „Nicht barrierefreie Inhalte"
  stehen weiterhin SC 1.4.3, 1.4.1 und 1.4.10 samt dem Hinweis, die Korrekturen lägen bereit und
  gölten bis zum Rollout als nicht barrierefrei. Alle drei sind live und nachgemessen. Der Abschnitt
  ist im CMS zu aktualisieren.
- **Fünf Überschriftensprünge im CMS-Seiteninhalt** (siehe oben) — redaktionell.
- **`/fotogalerie`:** enthält `media`, `media-body` und `mr-3` als Bootstrap-4-Klassen direkt im
  Seiteninhalt. Über `resources/sass/partials/_bootstrap4-compat.scss` abgedeckt, aber redaktionell
  aufzuräumen. Unabhängig davon ist der Seiteninhalt dort ein Rechenschaftsbericht und keine
  Fotogalerie (bereits am 19.08. vermerkt).
- **`forms-static/validation-unverifiable`, 36 Fundstellen (SC 3.3.1, „zu prüfen"):** das
  Suchformular hat kein `action`-Ziel; sein Fehlerverhalten lässt sich ohne Absenden nicht prüfen.
  Kein festgestellter Verstoß.
- **`alt-text/alt-empty-suspicious`, 2 Fundstellen (SC 1.1.1, „zu prüfen"):** zwei Bilder mit leerem
  Alternativtext, die informativ wirken. Redaktionell zu sichten.
- **PDFs und Office-Dateien** bleiben ungeprüft (unverändert gegenüber dem 12.08.-Bericht).

## Zugrunde liegende Daten

Scan vom 21.08.2026, 36/36 Seiten erreicht, 0 Fehler, axe-core 4.12.1, `playwright-axe`,
WCAG-Stufe AA. Rohdaten: 42 axe-Best-Practice-Befunde, 568 axe-Befunde „zu prüfen",
180 Verstöße und 56 „zu prüfen" aus den Check-Modulen (davon 177 der entfallende
Fokusrahmen-Befund).
