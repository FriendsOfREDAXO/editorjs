# Card Element & Enhanced Features Documentation

This document describes the new Card element and enhanced features added to the EditorJS addon for REDAXO.

## New Card Element

The Card element is a versatile block that combines media (images or videos) with text content and supports various layout options.

### Features

- **Media Support**: Automatic detection of images and videos
- **Layouts**: Vertical, horizontal, and grid/tile layouts
- **Lightbox**: Click-to-zoom functionality for images
- **Video Controls**: Autoplay, muting, looping, and controls
- **Aspect Ratios**: Auto, 16:9, 4:3, 1:1 support
- **Grid Columns**: 1-4 column layouts for tile display
- **Links**: Internal page linking and YForm dataset support
- **Responsive**: Mobile-optimized layouts

### JavaScript API

```javascript
// Card block is automatically available in EditorJS
{
    card: {
        class: window.CardBlock,
        inlineToolbar: true,
        config: {
            defaultLayout: 'vertical'
        }
    }
}
```

### Data Structure

```json
{
    "type": "card",
    "data": {
        "title": "Card Title",
        "text": "<p>Rich text content with <strong>formatting</strong></p>",
        "mediaFile": "image.jpg",
        "mediaUrl": "/media/image.jpg",
        "mediaType": "image",
        "mediaAlt": "Alt text for accessibility",
        "layout": "vertical",
        "gridColumns": 2,
        "aspectRatio": "16-9",
        "lightbox": true,
        "videoAutoplay": false,
        "videoMuted": false,
        "videoLoop": false,
        "videoControls": true,
        "linkUrl": "",
        "linkTarget": "_self"
    }
}
```

## Enhanced Text+Image Element

The existing TextImage element has been significantly enhanced with new features.

### New Features

- **Video Support**: Automatic detection and playback of video files
- **Lightbox**: Image enlargement with keyboard controls
- **Enhanced Media Detection**: Automatic image/video type detection
- **Video Controls**: Autoplay, muting, looping options
- **Improved UX**: Better user interface and interactions

### Updated Data Structure

```json
{
    "type": "textimage",
    "data": {
        "text": "<p>Text content</p>",
        "mediaFile": "video.mp4",
        "mediaUrl": "/media/video.mp4",
        "mediaType": "video",
        "mediaAlt": "Alt text",
        "caption": "Media caption",
        "layout": "left",
        "lightbox": true,
        "videoAutoplay": false,
        "videoMuted": false,
        "videoLoop": false,
        "videoControls": true
    }
}
```

## REDAXO Fragment System

A complete fragment-based rendering system for different frontend frameworks.

### Supported Frameworks

#### Bootstrap 5
- Modern responsive layouts
- Bootstrap Modal lightbox
- Card components
- Grid system integration

#### UIKit 3
- Advanced animations and transitions
- Native UIKit lightbox
- Flex-based layouts
- Icon integration

#### Custom CSS
- Flexible custom framework support
- Configurable CSS classes
- Full control over styling

### Usage

```php
<?php
use FriendsOfRedaxo\EditorJs\EditorJSFragmentRenderer;

// Get framework from settings
$framework = rex_addon::get('editorjs')->getConfig('framework', 'bootstrap');

// Create renderer
$renderer = new EditorJSFragmentRenderer($framework, [
    'lightbox' => true,
    'lazyLoading' => true
]);

// Render content
$editorjs_content = rex_article::getCurrent()->getValue('editorjs_content');
echo $renderer->render($editorjs_content);
?>
```

### Framework Selection

Framework selection is available in the addon settings:

1. Go to **AddOns → EditorJS → Einstellungen**
2. Select your preferred framework
3. Configure lightbox and lazy loading options
4. For custom CSS, define your own CSS classes

## CSS Integration

### Bootstrap 5

```html
<!-- In template head -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= rex_addon::get('editorjs')->getAssetsUrl('css/editorjs-bootstrap.css') ?>" rel="stylesheet">

<!-- Before closing body tag -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```

### UIKit 3

```html
<!-- In template head -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.14/dist/css/uikit.min.css">
<link href="<?= rex_addon::get('editorjs')->getAssetsUrl('css/editorjs-uikit.css') ?>" rel="stylesheet">

<!-- Before closing body tag -->
<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.14/dist/js/uikit.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.14/dist/js/uikit-icons.min.js"></script>
```

## Lightbox Functionality

### Features
- Click-to-zoom for images
- Keyboard navigation (ESC to close)
- Mobile-friendly touch controls
- Smooth animations
- Dark overlay background

### Implementation Details

#### Bootstrap Lightbox
Uses Bootstrap Modal component with custom styling for optimal image display.

#### UIKit Lightbox
Utilizes UIKit's native lightbox component with enhanced styling and transitions.

## Video Support

### Supported Formats
- MP4 (recommended)
- WebM
- OGV/OGG
- AVI
- MOV
- WMV

### Features
- **Autoplay**: Automatic playback on page load
- **Muted**: Start videos without sound
- **Loop**: Continuous playback
- **Controls**: Show/hide video controls
- **Responsive**: Adaptive sizing
- **Aspect Ratios**: Consistent video dimensions

## Grid Layouts

### Card Block Grid
- 1-4 column layouts
- Bootstrap/UIKit responsive breakpoints
- Automatic spacing and alignment
- Mobile-optimized stacking

### Configuration
```javascript
// In EditorJS
gridColumns: 3, // 1-4 columns
aspectRatio: '16-9', // Consistent sizing
```

## Accessibility Features

- **Alt Text**: Proper image descriptions
- **Keyboard Navigation**: Full keyboard support
- **Screen Reader**: ARIA labels and descriptions
- **High Contrast**: Dark mode support
- **Focus Management**: Visible focus indicators

## Performance Optimizations

- **Lazy Loading**: Images load on scroll
- **Responsive Images**: Appropriate sizing
- **CSS Optimization**: Minimal CSS footprint
- **Bundle Splitting**: Framework-specific assets

## Browser Support

- **Modern Browsers**: Chrome 60+, Firefox 55+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 60+
- **Graceful Degradation**: Fallbacks for older browsers

## Migration Notes

### From Old TextImage
Old TextImage blocks are automatically compatible with the new enhanced version. The following fields are mapped:

- `imageFile` → `mediaFile`
- `imageUrl` → `mediaUrl`
- `imageAlt` → `mediaAlt`

New fields default to sensible values:
- `mediaType` defaults to 'image'
- `lightbox` defaults to `true`
- Video options default to `false`

## Development

### Creating Custom Fragments

1. Create fragment file in `fragments/[framework]/[blocktype].php`
2. Use `$this->data` to access block data
3. Implement framework-specific HTML structure
4. Follow existing fragment patterns

### Example Custom Fragment

```php
<?php
// fragments/custom/card.php
$title = $this->data['title'] ?? '';
$text = $this->data['text'] ?? '';
?>

<div class="my-custom-card">
    <?php if ($title): ?>
        <h3 class="my-card-title"><?= htmlspecialchars($title) ?></h3>
    <?php endif; ?>
    
    <?php if ($text): ?>
        <div class="my-card-content"><?= $text ?></div>
    <?php endif; ?>
</div>
```

## Troubleshooting

### Common Issues

1. **Lightbox not working**: Ensure framework JavaScript is loaded
2. **Videos not playing**: Check file formats and browser support
3. **Layout issues**: Verify framework CSS is included
4. **Fragment not found**: Check fragment file exists and is readable

### Debug Mode

Enable debug mode in fragment renderer:

```php
$renderer = new EditorJSFragmentRenderer($framework, [
    'debug' => true
]);
```

This will show fragment errors as HTML comments.

## Changelog

### Version 1.1.0
- ✅ New Card element with media support
- ✅ Enhanced TextImage with video and lightbox
- ✅ REDAXO Fragment system
- ✅ Bootstrap 5 and UIKit 3 support
- ✅ Settings page for framework selection
- ✅ Responsive design improvements
- ✅ Accessibility enhancements
- ✅ Performance optimizations

---

For more information, see the main README.md file or visit the project repository.