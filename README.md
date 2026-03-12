# Contao Swiper Extension
This extension enhances the default Contao Swiper with three additional fields:
- Toggle navigation buttons
- Toggle pagination bullets
- Custom JSON options for the Swiper instance

This avoids the need to create multiple Swiper templates.

The implementation was strongly inspired by fritzmg's extension (https://github.com/fritzmg) "contao-swiper" (https://github.com/fritzmg/contao-swiper).

## Custom Options

The `Custom options` field expects a valid JSON configuration for Swiper.
Its content is merged directly into the initial Swiper options and can therefore override the other fields such as navigation or pagination.

### Example configuration for the field `Custom options`
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
