# EditorJS für REDAXO

Ein vollständig integriertes EditorJS-Addon für REDAXO mit erweiterten Blöcken und MediaPool-Integration.

## Features

### Grundfunktionen
- **Block-basierter Editor**: Moderne, intuitive Benutzeroberfläche
- **REDAXO MediaPool Integration**: Nahtlose Integration in bestehende Workflows
- **Drag & Drop**: Für alle Medien-Blöcke
- **PHP-Renderer**: Vollständige Server-seitige Ausgabe
- **Responsive Design**: Optimiert für alle Bildschirmgrößen

### Verfügbare Blöcke

#### Standard-Blöcke
- **Paragraph**: Formatierter Text mit Inline-Styling
- **Header**: Überschriften (H2-H6)
- **List**: Nummerierte und unnummerierte Listen
- **Quote**: Zitate mit Autor-Attribution
- **Delimiter**: Visuelle Trenner zwischen Inhalten

#### Erweiterte Medien-Blöcke
- **Image**: Einzelbilder mit Beschriftung und Alt-Text
- **Video**: Eingebettete Videos mit Vorschaubildern
- **Downloads**: Download-Listen mit Dateigröße und Typ-Icons
- **Image Gallery**: Responsive Bildergalerien mit Lightbox

#### Interaktive Blöcke
- **Alert**: Hervorgehobene Hinweise (Info, Warning, Error, Success)
- **REX Link**: REDAXO-interne Verlinkungen
- **REX Media**: Erweiterte MediaPool-Integration



## Verwendung

### In Templates
```php
<?php
// EditorJS-Inhalt aus Artikel-Metafeld laden
$editorjs_content = rex_article::getCurrent()->getValue('editorjs_content');

if ($editorjs_content) {
    $renderer = new EditorJSRenderer();
    echo $renderer->render($editorjs_content);
}
?>
```

### In Modulen
```php
// INPUT
<div class="form-group">
    <label>Inhalt:</label>
    <textarea name="REX_INPUT_VALUE[1]" id="editorjs-content" style="display: none;">REX_VALUE[id=1 output=html]</textarea>
    <div id="editorjs-container"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initEditorJS('editorjs-container', 'editorjs-content', {
        placeholder: 'Beginnen Sie mit der Eingabe...',
        tools: ['paragraph', 'header', 'list', 'image', 'video', 'downloads', 'gallery', 'alert']
    });
});
</script>

// OUTPUT
<?php
$content = 'REX_VALUE[id=1 output=html]';
if ($content) {
    $renderer = new EditorJSRenderer();
    echo $renderer->render($content);
}
?>
```

## Konfiguration per JS

### Tool-Auswahl
Einzelne Blöcke können gezielt aktiviert werden:

```javascript
window.initEditorJS('container', 'textarea', {
    tools: ['paragraph', 'header', 'image', 'gallery']
});
```

### Block-spezifische Optionen

#### Image Block
```javascript
{
    image: {
        config: {
            endpoints: {
                byFile: '/redaxo/index.php?rex_media_method=upload'
            },
            types: 'image/*',
            captionPlaceholder: 'Bildunterschrift eingeben...'
        }
    }
}
```

#### Gallery Block
```javascript
{
    gallery: {
        config: {
            maxImages: 20,
            allowReordering: true,
            showCaptions: true
        }
    }
}
```

## Blöcke im Detail

### Image Block
- **MediaPool-Integration**: Direkte Auswahl aus REDAXO MediaPool
- **Drag & Drop**: Dateien direkt ins Interface ziehen
- **Responsive**: Automatische Größenanpassung
- **SEO-optimiert**: Alt-Text und Bildunterschriften

**JSON-Struktur:**
```json
{
    "type": "image",
    "data": {
        "file": {
            "url": "/media/beispiel.jpg",
            "name": "beispiel.jpg",
            "size": 156789
        },
        "caption": "Eine Beispiel-Beschriftung",
        "withBorder": false,
        "withBackground": false,
        "stretched": false
    }
}
```

### Video Block
- **MediaPool-Integration**: Unterstützung für MP4, WebM, OGV
- **Vorschaubilder**: Automatische Thumbnail-Generierung
- **Responsive**: Adaptive Videoplayer
- **Fallback**: Graceful Degradation bei nicht unterstützten Formaten

**JSON-Struktur:**
```json
{
    "type": "video",
    "data": {
        "file": {
            "url": "/media/video.mp4",
            "name": "video.mp4",
            "size": 5234567
        },
        "caption": "Video-Beschreibung"
    }
}
```

### Downloads Block
- **Dateityp-Erkennung**: Automatische Icons basierend auf Dateierweiterung
- **Größenanzeige**: Menschenlesbare Dateigrößen
- **Batch-Upload**: Mehrere Dateien gleichzeitig
- **Sortierung**: Drag & Drop Neuordnung

**JSON-Struktur:**
```json
{
    "type": "downloads",
    "data": {
        "title": "Download-Bereich",
        "files": [
            {
                "url": "/media/dokument.pdf",
                "name": "dokument.pdf",
                "size": 234567,
                "title": "Wichtiges Dokument"
            }
        ]
    }
}
```

### Image Gallery Block
- **Responsive Grid**: Automatisches Layout
- **Lightbox**: Vollbild-Bildbetrachtung
- **Batch-Upload**: Mehrere Bilder gleichzeitig
- **Neuordnung**: Drag & Drop Sortierung
- **Optimiert**: Ohne problematisches Masonry-Layout

**JSON-Struktur:**
```json
{
    "type": "gallery",
    "data": {
        "title": "Bildergalerie",
        "images": [
            {
                "url": "/media/bild1.jpg",
                "name": "bild1.jpg",
                "size": 123456,
                "caption": "Bildbeschreibung"
            }
        ]
    }
}
```

### Alert Block
- **Verschiedene Typen**: Info, Warning, Error, Success
- **Anpassbares Design**: CSS-basierte Styling-Optionen
- **Rich Text**: Unterstützung für formatierte Inhalte

**JSON-Struktur:**
```json
{
    "type": "alert",
    "data": {
        "type": "info",
        "message": "Dies ist ein wichtiger Hinweis"
    }
}
```

## Frontend-Ausgabe

### CSS-Integration
```html
<link rel="stylesheet" href="assets/addons/editorjs/editorjs-frontend.css">
```

### JavaScript für Lightbox (Gallery)
```html
<script>
// Lightbox für Galerien
document.addEventListener('DOMContentLoaded', function() {
    // Wird automatisch durch Frontend-CSS aktiviert
});
</script>
```

## PHP-Renderer

Der `EditorJSRenderer` konvertiert JSON-Daten in HTML:

```php
<?php
$renderer = new EditorJSRenderer();

// Einzelnen Block rendern
$html = $renderer->renderBlock($blockData);

// Kompletten EditorJS-Output rendern
$html = $renderer->render($editorjsJson);

// Mit Custom-Optionen
$html = $renderer->render($editorjsJson, [
    'image_sizes' => ['thumbnail', 'medium', 'large'],
    'gallery_lightbox' => true,
    'download_icons' => true
]);
?>
```

## MediaPool-Integration

### Automatische Registrierung
Das Addon registriert sich automatisch im REDAXO MediaPool und stellt entsprechende Endpunkte zur Verfügung.

### Datei-Upload
- **Drag & Drop**: Direktes Ziehen von Dateien
- **Browser-Dialog**: Klassische Dateiauswahl
- **Typ-Validierung**: Automatische Prüfung der Dateiformate
- **Größen-Limits**: Konfigurierbare Upload-Grenzen

## Performance & Optimierung

### Asset-Bundling
```bash
# Entwicklung
npm run dev

# Produktion
npm run build

# Watch-Modus
npm run watch
```

### CSS-Optimierung
- **Critical CSS**: Nur benötigte Styles laden
- **Responsive Images**: Automatische Größenanpassung
- **Lazy Loading**: Verzögertes Laden von Medien

## Browser-Unterstützung

- **Modern Browsers**: Chrome 60+, Firefox 55+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 60+
- **Graceful Degradation**: Fallback für ältere Browser

## API-Referenz

### initEditorJS()
```javascript
window.initEditorJS(containerId, textareaId, options)
```

**Parameter:**
- `containerId`: ID des Container-Elements
- `textareaId`: ID des Hidden-Textarea-Elements
- `options`: Konfigurationsobjekt

**Optionen:**
```javascript
{
    placeholder: string,
    tools: array,
    i18n: object,
    logLevel: string,
    data: object
}
```

### EditorJSRenderer (PHP)
```php
class EditorJSRenderer {
    public function render(string $json, array $options = []): string
    public function renderBlock(array $block, array $options = []): string
}
```

## Entwicklung

### Setup
```bash
git clone [repository]
cd editorjs-addon
composer install
npm install
```

### Build-Prozess
```bash
# Webpack Development
npm run dev

# Webpack Production
npm run build

# PHP-Tests
composer test
```

### Verzeichnisstruktur
```
src/                    # JavaScript-Quellen
├── editorjs.js        # Haupt-Editor-Initialisierung
└── blocks/            # Block-Definitionen
    ├── image.js
    ├── video.js
    ├── downloads.js
    └── gallery.js

lib/                   # PHP-Klassen
└── EditorJSRenderer.php

assets/               # Kompilierte Assets
├── css/
└── js/

pages/               # Backend-Seiten
├── index.php       # Hauptseite
├── demo.php        # Demo & Tests
└── renderer.php    # Renderer-Tests
```

## Fehlerbehebung

### Häufige Probleme

**Editor lädt nicht:**
- Prüfen Sie die Browser-Konsole auf JavaScript-Fehler
- Stellen Sie sicher, dass alle Assets korrekt geladen werden
- Überprüfen Sie die Tool-Konfiguration

**MediaPool-Upload funktioniert nicht:**
- Prüfen Sie die REDAXO-Berechtigungen
- Überprüfen Sie die Upload-Limits in PHP
- Stellen Sie sicher, dass der Media-Addon aktiviert ist

**Rendering-Probleme:**
- Validieren Sie die JSON-Struktur
- Prüfen Sie die PHP-Fehlerprotokolle
- Stellen Sie sicher, dass der EditorJSRenderer korrekt eingebunden ist

### Debug-Modus
```javascript
window.initEditorJS('container', 'textarea', {
    logLevel: 'VERBOSE',
    tools: ['paragraph'] // Minimal-Konfiguration zum Testen
});
```

## Changelog

### Version 1.0.0
- ✅ Neue Blöcke: Video, Downloads, Image Gallery
- ✅ Verbesserte MediaPool-Integration
- ✅ Drag & Drop für alle Medien-Blöcke
- ✅ PHP-Renderer für alle Blöcke
- ✅ UI/UX-Verbesserungen
- ✅ Entfernung von instabilem Masonry-Layout
- ✅ Bugfix: "undefined" in JSON bei leeren Titeln
- ✅ Vollständige Dokumentation und Demo

- Grundlegende EditorJS-Integration
- Standard-Blöcke (Paragraph, Header, List, etc.)
- Basis MediaPool-Integration

## Lizenz

Dieses Addon steht unter der MIT-Lizenz.

## Support

Für Fragen und Support wenden Sie sich an das Entwicklungsteam oder erstellen Sie ein Issue im Repository.

**Friends Of REDAXO**
* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO/editorjs 


## Credits

**Project Lead**

[Thomas Skerbis](https://github.com/skerbis)

---

**Hinweis**: Diese Dokumentation bezieht sich auf die aktuelle Version des Addons. Alle Features wurden getestet und sind produktionsreif.