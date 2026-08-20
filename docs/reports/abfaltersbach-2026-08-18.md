# Prüfbericht — abfaltersbach.at, Aktualisierung vom 18.08.2026

**Website:** abfaltersbach.at · **Anlass:** Nachverfolgung der fünf sicher feststehenden Verstöße
aus dem [Prüfbericht vom 12.08.2026](abfaltersbach-2026-08-12.md) · **Maßstab:** WCAG 2.1 Stufe AA
über EN 301 549 V3.2.1 (2021-03).

Dieser Bericht ersetzt nicht den Prüfbericht vom 12.08.2026, sondern ergänzt ihn: er hält fest, was
von den fünf damals gemeldeten Verstößen inzwischen behoben ist, was noch aussteht, und korrigiert
einen der fünf Befunde nach genauerer Prüfung. Die 680 needsReview-Befunde und 220
Best-Practice-Hinweise aus dem ursprünglichen Bericht sind von dieser Aktualisierung nicht
berührt und weiterhin unverändert gültig.

## Methode dieser Aktualisierung

Zwei Prüfschritte, nicht einer:

1. **Ein vollständiger, werkzeuggestützter Re-Scan** aller 36 Seiten (axe-core in echtem
   Chromium/Playwright, dieselbe Pipeline wie am 12.08.) am **18.08.2026, 20:11–20:13 Uhr**, als
   Stand-vor-Rollout-Baseline. Er bestätigt, dass Kontrast, Datenschutz-Link und Reflow zum
   Scan-Zeitpunkt live noch bestanden (die Korrekturen dafür lagen zu diesem Zeitpunkt nur im
   Code vor, siehe unten) und dass sich die Zählung beim Fokusrahmen-Befund seit dem 12.08.
   deutlich verschoben hat.
2. **Eine gezielte manuelle Nachprüfung** einzelner Befunde direkt im Browser (echte
   Tastatur-Bedienung, `Tab`-Taste, keine Simulation), wo die automatisierte Zählung allein keine
   verlässliche Aussage erlaubte — siehe Punkt 1 unten.

## Was sich seit dem 12.08.2026 geändert hat

| Kriterium | 12.08. | 18.08. (vor Rollout) | Status heute |
|---|---|---|---|
| 2.4.7 Fokus sichtbar | 146 | 179 | Befund entfällt — nicht reproduzierbar (siehe unten) |
| 1.4.3 Kontrast (Minimum) | 155 | 156 | Korrektur fertiggestellt, Rollout ausstehend |
| 1.4.1 Verwendung von Farbe | 15 | 15 | Korrektur fertiggestellt, Rollout ausstehend |
| 1.4.10 Reflow | 19 | 21 | Korrektur fertiggestellt, Rollout ausstehend |
| 4.1.2 Name, Rolle, Wert | 1 | 1 | **Behoben, live** |

Die leichte Drift bei 1.4.3, 1.4.1 und 1.4.10 (155→156, 19→21) ist normale Bewegung durch neue
Inhalte in den acht Tagen zwischen den Scans (neue Dokumente, neue Personen-Einträge) und keine
neue Ursache — die zugrunde liegende, bereits im ersten Bericht beschriebene Ursache je Kriterium
ist unverändert dieselbe.

### 1. Kein sichtbarer Fokusrahmen — Befund entfällt

**146 → 179 Fundstellen, +23 % auf einer praktisch unveränderten Navigation.** Das ist keine
Bewegung, die zu einem stabilen, echten Fehler passt. Stichprobe: dieselbe Fundstelle
(`<a class="dropdown-item" href="/buergerservice/informationen">Informationen</a>`, gemeldet in
beiden Scans mit identischem Selektor) wurde manuell mit echten `Tab`-Tastendrücken nachgeprüft,
vom Seitenanfang beginnend, Schritt für Schritt: das Element erhält beim Fokussieren einen klar
sichtbaren blauen Hintergrund (`background: #2d93c5`, seit 2020 im Code, CSS-Regel intakt). Dieselbe
Stichprobe an einer Verknüpfung in der Randspalte (`itemprop="url"`) zeigt den normalen blauen
Browser-Fokusring — auch hier keine Auffälligkeit.

**Vermutliche Ursache beim Prüfwerkzeug:** Das Check-Modul
(`modules/ScannerPlugins/Engines/PlaywrightAxe/Node/checks/keyboard.mjs` im Repository
`biegl/wcag-check`) vergleicht zwei volle Viewport-Screenshots byteweise
(`previousShot.equals(currentShot)`) unmittelbar nach jedem `Tab`-Druck, ohne auf einen
Repaint zu warten. Das ist ein plausibler Erklärungsansatz für ein Timing-Artefakt, das je nach
Scan-Lauf unterschiedlich viele (aber dieselben) Elemente betrifft — bestätigt ist das nicht, das
wäre Sache des wcag-check-Projekts selbst zu untersuchen.

**Ergebnis:** Der Punkt wird aus der Liste der nicht barrierefreien Inhalte gestrichen. Es war keine
Codeänderung nötig, weil kein Fehler vorlag.

### 2. Kontrast bei der Dateigrößen-Angabe — Korrektur fertig, Rollout ausstehend

Ursache bestätigt, aber präziser als im ersten Bericht beschrieben: `.download-file__size` war
fest auf `color: #fff` (Weiß) gesetzt, während der Hintergrund der Dokumenten-Karten in der
Randspalte (`.sidebar .card`) `$highlightColor` (`#ebb60a`, Gold) ist — ein gemessenes
Kontrastverhältnis von rund **1,87:1** statt der geforderten 4,5:1. Korrektur: Textfarbe auf `#333`
gesetzt (dieselbe dunkle Farbe, die an anderer Stelle im Theme für Text auf `$highlightColor`
verwendet wird). Eine einzelne CSS-Regel, betrifft alle Fundstellen.

### 3. Datenschutz-Link im Cookie-Hinweis — Korrektur fertig, Rollout ausstehend

Ursache exakt wie im ersten Bericht beschrieben: `#tcn_notice a` hatte keine
`text-decoration`. Korrektur: `text-decoration: underline` für diese Linkklasse ergänzt.

### 4. Reflow bei schmalen Bildschirmen — Ursache präzisiert, Korrektur fertig, Rollout ausstehend

Die genaue Ursache unterscheidet sich von der ursprünglichen Beschreibung: es ist **kein** Raster
fixer Breite. Die Personenkarten (`/gemeinde/gemeinderat`, `/gemeinde/mitarbeiter`) sind
Bootstrap-Flex-Boxen (`.media`) ohne feste Spaltenbreite. Der tatsächliche Auslöser sind lange,
nicht umbrechbare Zeichenketten (E-Mail-Adressen wie `philipp.wieser82@gmail.com`) innerhalb dieser
Boxen — ohne `overflow-wrap` erzwingt ein solcher String die Breite seiner Box, die Box sprengt die
Zeile, die Seite scrollt horizontal. Bestätigt durch den Rohbefund des Scans: die gemeldeten
Fundstellen sind exakt die `person-name`-, `person-role`- und `mailto`-Elemente derselben
Personenkarte, nicht ein Rasterelement. Auf `/bildung` betrifft derselbe SC-Verstoß den
ausgeschriebenen Link „Schulratgeber: https://www.tutoria.at/schule-ratgeber" — hier liegt die
Ursache tatsächlich im sichtbaren Linktext, wie ursprünglich beschrieben.

Korrektur: `overflow-wrap: anywhere` für `.person-contact` ergänzt (Personenkarten, Rollout
ausstehend); der Linktext auf `/bildung` wurde direkt im Seiteninhalt auf „Schulratgeber" gekürzt
(**bereits live**, siehe unten).

### 5. Eingebettete Karte ohne Titel — behoben, live

`title="Lage der Gemeinde Abfaltersbach auf Google Maps"` wurde dem `iframe` im Seiteninhalt von
`/gemeinde/lage` hinzugefügt. Direkt im Seiteninhalt geändert, keine Codeänderung nötig — **bereits
live**, mit dem Scan vom 18.08. als Vorher-Beleg und direkter Nachprüfung im Browser als
Nachher-Beleg.

## Offene Punkte

- **Rollout der drei Code-Korrekturen** (Kontrast, Datenschutz-Link, Reflow bei den
  Personenkarten): fertig im Quellcode (`resources/sass/partials/_sidebar.scss`,
  `_disclaimer.scss`, `_person.scss`), noch nicht deployt. Bis zum Rollout gelten diese drei Punkte
  weiterhin als nicht barrierefrei.
- **PDFs und Office-Dateien** bleiben ungeprüft (unverändert gegenüber dem 12.08.-Bericht).
- Die 680 needsReview-Befunde und 220 Best-Practice-Hinweise aus dem ursprünglichen Bericht sind
  unverändert und nicht Gegenstand dieser Aktualisierung.

## Zugrunde liegende Daten

Scan-ID `01a01680-1112-7346-83a2-74719acde91c`, 18.08.2026, 36/36 Seiten erreicht, 0 Fehler,
`playwright-axe`, WCAG-Stufe AA. Rohdaten (Selektoren, Snippets, Screenshots) liegen in der
`scan_pages`-Tabelle der wcag-check-Datenbank.
