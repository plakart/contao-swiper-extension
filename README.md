# Contao Swiper Extension
Diese Extension erweitert den Standard-Contao-Swiper um drei neue Felder:
- Steuerschaltflaechen an/aus
- Punkte-Navigation an/aus
- Custom-Optionen als JSON fuer die Swiper-Instanz

Dies erspart den Schritt, mehrere Swiper-Templates anzulegen.

Orientierung lag stark auf der Extension von fritzmg (https://github.com/fritzmg) "contao-swiper" (https://github.com/fritzmg/contao-swiper).

## Custom-Optionen

Das Feld `Eigene Optionen` erwartet eine gültige JSON-Konfiguration für die Swiper-Instanz.
Der Inhalt wird direkt in die initialen Swiper-Optionen gemerged.

### Beispielkonfiguration "Eigene Optionen"
```json
{
  "loop": true,
  "spaceBetween": 24,
  "navigation": false,
  "pagination": {
    "el": ".custom-pagination",
    "clickable": true
  },
  "breakpoints": {
    "768": {
      "slidesPerView": 2
    },
    "1200": {
      "slidesPerView": 4
    }
  }
}
```
