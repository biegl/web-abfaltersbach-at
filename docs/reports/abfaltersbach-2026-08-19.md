# Prüfbericht — abfaltersbach.at, Aktualisierung vom 19.08.2026

**Anlass:** Behebung der 220 Best-Practice-Hinweise sowie dreier gezielt ausgewählter
needsReview-Befunde aus dem [Prüfbericht vom 12.08.2026](abfaltersbach-2026-08-12.md), im Anschluss
an die [Aktualisierung vom 18.08.2026](abfaltersbach-2026-08-18.md).

Die Best-Practice-Hinweise sind laut Prüfbericht **keine WCAG-Pflicht** und gehören nicht in die
Erklärung zur Barrierefreiheit — dieser Abschnitt ist rein informativ. Die drei needsReview-Befunde
sind echte WCAG-Erfolgskriterien, waren aber bewusst nie in der Erklärung aufgeführt (ADR-0008:
needsReview-Befunde belegen keine Unvereinbarkeit), ihre Behebung ändert daher nichts an der
Erklärung.

## Best-Practice-Hinweise (220, keine WCAG-Pflicht)

Wie schon die 336 WCAG-Verstöße gehen auch die 220 Best-Practice-Hinweise auf nur fünf axe-Regeln
zurück:

| Regel | Fundstellen (Scan 18.08.) | Ursache |
|---|---|---|
| `region` | 132 | Titelbereich und Hauptinhalt lagen außerhalb jeder Landmarke |
| `landmark-one-main` | 37 | Kein `<main>`-Element auf der Seite |
| `heading-order` | 24 | Fixe Widget-Überschriften (Wetter, Lage, Downloads, Personenkarten) übersprangen Ebenen |
| `empty-heading` | 16 | Leere `<h2>`/`<h3>`-Tags als optische Absatz-Trenner in vier Seiten-Inhalten |
| `page-has-heading-one` | 10 | Zehn Seiten hatten keine `<h1>` |

**Landmarken (`region`, `landmark-one-main`, 169 von 219 Fundstellen):** Zwei Template-Änderungen.
`resources/views/layouts/base.blade.php` — `<section id="main-content">` → `<main id="main-content">`.
`resources/views/partials/header.blade.php` — der Titelbereich (Logo, Seitenname, Beschreibung) ist
jetzt in `<header>` statt `<div>` gefasst. Navigation (`<nav id="navigation">`) und Breadcrumb
(`<nav aria-label="breadcrumb">`) waren bereits korrekte Landmarken.

**Überschriftenreihenfolge (`heading-order`, 24 Fundstellen):** Vier wiederkehrende Widgets sprangen
Ebenen, weil sie fix auf einen Heading-Level programmiert waren, unabhängig vom Seiteninhalt davor:

- Footer: „Aktuelles Wetter", „Lage", „Gemeinde Abfaltersbach" (`h3`→`h2`), „Öffnungszeiten:" (`h4`→`h3`)
- Sidebar: „Downloads & Links" (`h4`→`h2`)
- Personenkarten: Name (`h5`→`h2`), Rolle (`h6`→`h3`)
- Startseite: „News"-Überschrift war `h2` ohne vorhandene `h1` auf der Seite → `h1`

Alle sichtbaren Schriftgrade wurden per expliziter `font-size`/`font-weight`-Regel exakt erhalten
(gemessene Werte vor der Änderung, siehe `_person.scss`, `_sidebar.scss`, `_footer.scss`,
`_newslist.scss`) — keine optische Änderung.

**Leere Überschriften (`empty-heading`, 16 Fundstellen, 4 Seiten):** `<h2>&nbsp;</h2>` bzw.
`<h3>&nbsp;</h3>`, im Seiteninhalt als Abstandshalter zwischen Absätzen verwendet
(`/bildung`, `/buergerservice/beratungsstellen`, `/gemeinde/protokolle`,
`/aerzte___wirtschaft___tourismus/firmen_a-z`). In `<div>` mit exakt gemessener Höhe/Marge
umgewandelt — der optische Abstand bleibt erhalten, die leere Überschrift verschwindet aus der
Dokumentgliederung.

**Fehlende `<h1>` (`page-has-heading-one`, 10 Seiten):** Auf zwei Seiten (`/bildung`,
`/pfarrgemeinde/pfarrkirche_st._andrae`, `/pfarrgemeinde/filialkirche_maria_heimsuchung`) wurde die
bereits vorhandene Titel-Überschrift zur `h1` aufgewertet. Auf sechs weiteren Seiten
(`/buergerservice/beratungsstellen`, `/buergerservice/verordnungen`,
`/buergerservice/informationen`, `/gemeinde/gemeindeverbaende`, `/fotogalerie`,
`/pfarrgemeinde/kapellen`) fehlte jede Titel-Überschrift; dort wurde eine neue `<h1>` mit dem
Namen aus der Navigation ergänzt. `/buergerservice/informationen` hatte gar keinen Seiteninhalt
(`NULL`) — jetzt zumindest eine `<h1>Informationen</h1>`.

**Nebenbefund, nicht behoben:** Der Seiteninhalt hinter `/fotogalerie` ist ein
Rechenschaftsbericht („Überblick zum Jahr 2025" usw.), keine Fotogalerie — ein
Redaktions-/Zuordnungsfehler in der Navigation, unabhängig von Barrierefreiheit. Nicht Teil dieser
Behebung, nur zur Kenntnis der Gemeinde vermerkt.

## needsReview-Befunde (3 von 680, gezielt behoben)

- **SC 4.1.3, Statusmeldungen — Wetter-Widget ohne Live-Region:** `aria-live="polite"` auf den
  Wetter-Container im Footer ergänzt (`resources/views/partials/footer.blade.php`). Betrifft alle
  Seiten, da das Widget sitezweit im Footer eingebunden ist.
- **SC 1.1.1, Alternativtext — zwei Bilder mit identischem Alt-Text „Luftballone" auf
  `/pfarrgemeinde`:** Die Bilder gesichtet und mit unterscheidenden, inhaltsbeschreibenden
  Alt-Texten versehen (Nahaufnahme zweier Herzluftballons am Gartentor; Kinder und Erwachsene, die
  bei der Nacht der 1000 Lichter Herzluftballons steigen lassen).
- **SC 1.1.1, Alternativtext „Abfaltern" = Dateiname auf `/fotogalerie`:** Bild gesichtet — zeigt
  eine frisch asphaltierte Gemeindestraße, nicht den Ort im Allgemeinen. Alt-Text auf „Neu
  asphaltierte Gemeindestraße" präzisiert.
- **SC 2.1.2, mögliche Tastaturfalle in der eingebetteten Google-Maps-Karte auf `/gemeinde/lage`:
  nicht geprüft.** Der manuelle Tastaturtest scheiterte an einem Tooling-Problem (Tab-Tastendrücke
  kamen im Browser nicht mehr an, bestätigt durch einen Kontrolltest auf einer unabhängigen Seite).
  Dieser Punkt bleibt offen und sollte bei nächster Gelegenheit erneut manuell im Browser geprüft
  werden: eine Google-Maps-Karte fokussieren, mit Tab und Umschalt+Tab prüfen, ob sich der Fokus
  wieder verlassen lässt.

## Status

Alle Code-Änderungen (Templates, SCSS, kompiliertes `app.css`) liegen im selben Branch/PR wie die
Fixes vom 18.08. — noch nicht deployt. Die Seiteninhalt-Änderungen (leere Überschriften, fehlende
`<h1>`, Alt-Texte) wurden direkt im CMS vorgenommen und sind bereits live.
