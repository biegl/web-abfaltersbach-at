# Prüfbericht — abfaltersbach.at, 12.08.2026

**Website:** abfaltersbach.at · **Geprüfte Seiten:** 36 von 36 erreichbar, 0 Fehler ·
**Maßstab:** WCAG 2.1 Stufe AA über EN 301 549 V3.2.1 (2021-03), 49 Erfolgskriterien für eine
öffentliche Stelle · **Bewertungsmethode:** werkzeuggestützte Prüfung (axe-core in echtem
Chromium, ergänzt um acht eigene Prüfmodule), keine manuelle Nachprüfung.

Dieser Bericht ist die technische Grundlage für die
[Erklärung zur Barrierefreiheit](https://abfaltersbach.at/barrierefreiheitserklaerung). Geprüft
wurde mit axe-core (echtes Chromium, ein Ladezustand, ein Viewport 1280×900) sowie acht eigenen
Prüfmodulen für das, was axe nicht abdeckt: Tastaturbedienung und Fokus, Kontrast in
Interaktionszuständen, Reflow/Zoom/Textabstand, Formularstatik, Überschriftenstruktur,
Alternativtext-Eignung, Medieninventar und Statusmeldungen. Jedes Modul meldet nur mechanisch
entscheidbare Verstöße als Fehler; alles andere geht als `needsReview` an ein menschliches Urteil.
**PDFs und Office-Dateien wurden in diesem Prüflauf nicht geprüft** (EN 301 549 Kapitel 10, Issue
[#16](https://github.com/biegl/wcag-check/issues/16)) — die Amtstafel, die Rechnungsabschlüsse und
die übrigen Dokumentenlisten der Website enthalten voraussichtlich Dokumente, für die keine
Aussage vorliegt.

## Kurzfassung

| | |
|---|---|
| Seiten mit einem sicher feststehenden Verstoß | 27 von 36 |
| Betroffene Erfolgskriterien (sicher) | 5 von 49 |
| Sicher feststehende Verstöße insgesamt | 336 |
| Noch zu beurteilende Befunde (needsReview) | 680 |
| Best-Practice-Hinweise (keine WCAG-Pflicht) | 220 |

Alle 336 sicher feststehenden Verstöße gehen auf **fünf wiederkehrende Ursachen** zurück, nicht auf
336 verschiedene Probleme. Jede lässt sich an einer Stelle im Template beheben und wirkt dann auf
allen betroffenen Seiten. Das ist die gute Nachricht dieses Berichts: der Umfang klingt groß, der
Reparaturaufwand ist es nicht.

## Was sicher zu beheben ist

### 1. Kein sichtbarer Fokusrahmen beim Tabulieren (SC 2.4.7 Focus Visible)

**146 Fundstellen auf allen 36 Seiten.** Betrifft vor allem die Einträge in den
Aufklapp-Menüs der Hauptnavigation (`.dropdown-item`) und mehrere Verknüpfungen in Randspalten und
Brotkrumen-Navigation (`itemprop="url"`). Wer mit der Tastatur navigiert, sieht beim Erreichen
dieser Elemente keine Veränderung — kein Rahmen, keine Farbänderung, nichts. Das ist ein
CSS-Reset, der `outline` global entfernt hat, ohne eine sichtbare Alternative zu setzen — ein
weit verbreitetes Muster, wenn ein Theme „aufgeräumt" werden sollte.

**Abhilfe:** eine `:focus-visible`-Regel für `.dropdown-item` und die betroffenen Link-Klassen
(Kontur, Hintergrund- oder Unterstreichungswechsel reicht), keine Neuentwicklung.

### 2. Zu geringer Kontrast bei der Dateigrößen-Angabe (SC 1.4.3 Kontrast (Minimum))

**155 Fundstellen auf 17 Seiten**, überall in Listen mit herunterladbaren Dokumenten (Amtstafel,
Formulare, Verordnungen, Rechnungsabschlüsse). Die Textangabe der Dateigröße
(`<small class="download-file__size">`, z. B. „2.2MB") ist in hellem Grau auf hellem Grund gesetzt
und unterschreitet das geforderte Kontrastverhältnis von 4,5:1 deutlich.

**Abhilfe:** die Textfarbe dieser Klasse dunkler setzen. Eine einzelne CSS-Änderung behebt alle 155
Fundstellen gleichzeitig.

### 3. Datenschutz-Link im Cookie-Hinweis nur durch Farbe erkennbar (SC 1.4.1 Verwendung von Farbe)

**15 Fundstellen auf 15 Seiten.** Der Link zu den Datenschutzbestimmungen im Cookie-Banner
(`<a class="datenschutz">`) unterscheidet sich vom umgebenden Text ausschließlich durch die
Linkfarbe, ohne Unterstreichung oder anderes Merkmal — für Menschen mit Farbsehschwäche ist der
Link im Fließtext nicht als solcher zu erkennen.

**Abhilfe:** eine Unterstreichung (Standard-Linkverhalten) oder ein anderes nicht-farbliches Merkmal
für diese eine Linkklasse.

### 4. Horizontales Scrollen bei schmalen Bildschirmen (SC 1.4.10 Reflow)

**19 Fundstellen auf 3 Seiten.** Bei 320 Pixel Breite (Smartphone-Format bei starker Vergrößerung)
scrollt die Seite horizontal, weil einzelne Elemente über den Bildschirmrand hinausragen:

- Die Personenkarten auf `/gemeinde/gemeinderat` und `/gemeinde/mitarbeiter` (Name, Funktion,
  Telefon, E-Mail je Person) sind in einem Raster fixer Breite gesetzt, das bei dieser
  Bildschirmbreite nicht umbricht.
- Auf `/bildung` ist eine lange Internetadresse als sichtbarer Linktext ausgeschrieben
  („Schulratgeber: https://www.tutoria.at/schule-ratgeber") und ragt dadurch über den Rand.

**Abhilfe:** die Personenkarten mit einem responsiven Raster (z. B. CSS Grid mit `auto-fit`) statt
fixer Breiten setzen; den langen Link entweder umbrechen lassen (`overflow-wrap: break-word`) oder
mit einem kürzeren Linktext versehen.

### 5. Eingebettete Karte ohne Titel (SC 4.1.2 Name, Rolle, Wert)

**1 Fundstelle auf `/gemeinde/lage`.** Die eingebettete Google-Maps-Karte
(`<iframe src="https://maps.google...">`) hat kein `title`-Attribut; assistive Technologien können
sie nur als „iframe" ohne weitere Angabe ansagen.

**Abhilfe:** `title="Lage der Gemeinde Abfaltersbach auf Google Maps"` (oder vergleichbar) am
`iframe` ergänzen.

## Was ein Mensch noch beurteilen muss

680 Befunde sind mechanisch nicht entscheidbar — das ist erwartungsgemäß bei diesem Prüfumfang. Die größten Gruppen:

- **569 Kontrastknoten (SC 1.4.3), 36 Seiten.** Überwiegend Text über Bildhintergründen oder in
  Verläufen — axe kann Bildinhalte nicht auswerten. Ein Mensch muss diese Fälle im Browser mit
  einer echten Farbpipette prüfen.
- **36 Formulare (SC 3.3.1), 36 Seiten.** Kein Formular auf der Website nutzt `novalidate` oder
  ausschließlich Skript-Absenden — bei allen greift vermutlich die native Browser-Validierung,
  was in der Regel als konform gilt. Ohne Absenden lässt sich das nicht abschließend bestätigen;
  ein einmaliger manueller Test je Formulartyp reicht.
- **24 übersprungene Überschriftenebenen und 11 fehlende h1 (SC 1.3.1), 35 Seiten.** Die
  Gliederung ist vorhanden, aber nicht lückenlos — kein Pflichtverstoß, aber ein Hinweis auf die
  Dokumentstruktur.
- **21 weitere Datenschutz-Link-Stellen (SC 1.4.1), 21 Seiten.** Dieselbe Ursache wie oben,
  automatisch als needsReview statt Verstoß eingestuft, wenn axe die Umgebung nicht eindeutig
  als Fließtext erkannt hat — dieselbe Abhilfe behebt auch diese.
- **14 weitere mögliche Randfälle des Reflow-Befunds (SC 1.4.10), 9 Seiten**, bei denen der
  Verursacher nicht eindeutig einem nicht ausgenommenen Inhalt zugeordnet werden konnte (z. B.
  Bilder, Karten).
- **Eine mögliche Tastaturfalle (SC 2.1.2)** auf `/gemeinde/lage` — vermutlich dieselbe
  eingebettete Google-Maps-Karte: ihre interne Tastatursteuerung kann den Fokus binden. Manuell
  mit Tastatur prüfen, ob man die Karte wieder verlassen kann.
- **Ein dynamisch erscheinender Text außerhalb einer Live-Region (SC 4.1.3)** auf
  `/gemeinde/rechnungsabschluesse` — ein eingebettetes Wetter-Widget aktualisiert Text
  („Mäßiger Regen") ohne `aria-live`. Ob das eine Statusmeldung im Sinn der WCAG ist, ist
  Ermessensfrage; wenn ja, wird sie von Screenreadern nicht angesagt.
- **Zwei Alternativtext-Auffälligkeiten (SC 1.1.1)** auf `/pfarrgemeinde` (zwei Bilder mit
  identischem Alternativtext „Luftballone") und `/fotogalerie` (Alternativtext „Abfaltern"
  entspricht dem Dateinamen — kann trotzdem zutreffend sein, ein Mensch muss das Foto ansehen).

Die 220 Best-Practice-Hinweise (u. a. fehlende Hauptlandmarke, doppelte Regionen) sind keine
WCAG-Pflicht und tauchen hier nur der Vollständigkeit halber auf — sie gehören nicht in die
Erklärung zur Barrierefreiheit.

## Was nicht geprüft wurde

- **PDFs und Office-Dateien** (EN 301 549 Kapitel 10). Die Website verlinkt Dokumente auf
  mindestens der Amtstafel und den Rechnungsabschlüssen; ohne Prüfung kann für diese Dokumente
  weder Vereinbarkeit noch eine Ausnahme belegt werden.
- **Audiodeskription** (SC 1.2.3, 1.2.5) — die Website hat aktuell keine Video- oder
  Audio-Inhalte; das Medieninventar-Modul lief mit, fand aber nichts zu prüfen.
- Alles, was nur durch Interaktion entsteht, die dieser Prüflauf bewusst nicht ausgelöst hat
  (Formulare absenden, Klicks in eingebettete Widgets) — siehe die Formular- und
  Statusmeldungs-Abschnitte oben.
