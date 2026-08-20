# Entwurf: Aktualisierung der Erklärung zur Barrierefreiheit

**Zweck:** Vorlage für die Aktualisierung von `/barrierefreiheitserklaerung` im CMS. Grundlage ist
der [Prüfbericht vom 21.08.2026](abfaltersbach-2026-08-21.md).

> **Erst nach dem Rollout dieses Branches veröffentlichen.** SC 1.4.10 hat bis dahin noch drei
> Fundstellen live (`/gemeinde/mitarbeiter`, lange Rollenbezeichnung). Der Entwurf beschreibt den
> Stand **nach** dem Deploy. Wird er vorher veröffentlicht, behauptet die Erklärung eine
> Vereinbarkeit, die noch nicht ausgeliefert ist.

**Nicht ändern:** Der Abschnitt „Stand der Vereinbarkeit mit den Anforderungen" bleibt bei
**„teilweise vereinbar"**. Die PDF- und Office-Dokumente sind weiterhin ungeprüft und damit als
nicht bestätigt vereinbar zu behandeln — das allein hält die Einschränkung aufrecht. Auch die
Abschnitte b) und c) bleiben unverändert.

---

## a) Unvereinbarkeit mit den Barrierefreiheitsbestimmungen

**Ersetzen** — die drei technischen Punkte (Kontrast der Dateigrößen-Angabe, Datenschutz-Link im
Cookie-Hinweis, Reflow bei schmalen Bildschirmen) entfallen, weil sie behoben und live nachgemessen
sind. Der Absatz zu den Dokumenten bleibt als einziger Eintrag stehen:

> Die auf der Website zum Download bereitgestellten PDF- und Office-Dokumente (u. a. auf der
> Amtstafel, bei den Formularen und den Rechnungsabschlüssen sowie weitere hochgeladene Dokumente)
> wurden im zugrunde liegenden Prüflauf nicht auf ihre Vereinbarkeit mit den
> Barrierefreiheitsanforderungen geprüft (EN 301 549 V3.2.1, Kapitel 10 Nicht-Web-Dokumente). Bis
> zur Nachholung dieser Prüfung sind sie als nicht bestätigt vereinbar zu behandeln.

## Zusammenfassender Absatz

**Ersetzen** (bisher beginnend mit „Von den fünf am 12. August 2026 gemeldeten technischen
Unvereinbarkeiten …"):

> Die fünf am 12. August 2026 gemeldeten technischen Unvereinbarkeiten sind erledigt. Der fehlende
> Titel der eingebetteten Karte auf der Seite „Lage" wurde bereits im August behoben. Der zu
> geringe Kontrast der Dateigrößen-Angabe, der ausschließlich farblich erkennbare Link zu den
> Datenschutzbestimmungen im Cookie-Hinweis und das horizontale Scrollen bei schmalen Bildschirmen
> sind mit dem Rollout vom 20. August 2026 wirksam geworden und auf der ausgelieferten Website
> nachgemessen. Der gemeldete fehlende Fokusrahmen ließ sich bei der manuellen Nachprüfung am
> 18. August 2026 nicht reproduzieren: die betroffenen Verknüpfungen zeigen bei Tastaturbedienung
> einen sichtbaren Fokusindikator; der Punkt entfällt daher.
>
> Ein Prüflauf über alle 36 Seiten am 21. August 2026 hat keine weiteren Verstöße gegen die
> Erfolgskriterien der WCAG 2.1 Stufe AA ergeben. Die zuvor offene Frage einer möglichen
> Tastaturfalle in der eingebetteten Karte auf der Seite „Lage" wurde dabei manuell geprüft: der
> Fokus lässt sich mit der Tabulatortaste in beide Richtungen wieder aus der Karte herausbewegen,
> das Erfolgskriterium 2.1.2 ist erfüllt.
>
> Einzelheiten und die zugrunde liegenden Belege stehen in der Aktualisierung des Prüfberichts vom
> 21. August 2026.

Den Link am Ende auf den Bericht vom 21.08. umstellen (bisher zeigt er auf den vom 18.08.).

## Erstellung dieser Erklärung zur Barrierefreiheit

> Diese Erklärung wurde am **12. August 2026** erstellt und zuletzt am **21. August 2026** überprüft
> und aktualisiert.

---

## Was bewusst *nicht* in die Erklärung kommt

- **568 Befunde `color-contrast` vom Typ „zu prüfen"** — axe kann das Kontrastverhältnis dort nicht
  selbst entscheiden (Hintergrundbilder, Transparenz). Nach ADR-0008 belegen needsReview-Befunde
  keine Unvereinbarkeit.
- **Fünf Überschriftensprünge im CMS-Seiteninhalt** (SC 1.3.1, „zu prüfen") — redaktionell zu
  korrigieren, kein festgestellter Verstoß.
- **`forms-static/validation-unverifiable`, 36 Fundstellen** (SC 3.3.1, „zu prüfen") — das
  Suchformular hat kein `action`-Ziel, sein Fehlerverhalten ist ohne Absenden nicht prüfbar.
- **177 Befunde „Fokusrahmen fehlt"** — Werkzeugartefakt, am 18.08. widerlegt und aus der Erklärung
  gestrichen.
- **42 Best-Practice-Hinweise** — keine WCAG-Pflicht, gehören nicht in die Erklärung.
