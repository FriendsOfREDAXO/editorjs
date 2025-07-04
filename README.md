# EditorJS für REDAXO EXPERIMENTAL 🐣

Ein moderner, block-basierter Editor für REDAXO mit lokalen npm-Packages.

## Features

### ✅ **Bereits implementiert**
- ✅ EditorJS Core mit Standard-Tools
- ✅ Lokale npm-Installation (kein CDN)  
- ✅ Build-System mit esbuild
- ✅ Benutzerdefinierte Blöcke:
  - Alert-Block (Info, Warning, Error, Success)
  - TextImage-Block mit Medienpool-Integration
- ✅ REDAXO-Backend-Integration
- ✅ **PHP-Renderer für Frontend-Ausgabe**
- ✅ Responsive Design
- ✅ Template-Integration
- ✅ **REDAXO-spezifische Integrationen:**
  - **Medienpool-Picker** - Nahtlose Integration in den REDAXO-Medienpool
  - **Link-Picker** - REDAXO-Artikel und externe Links auswählen

### 🚧 **In Entwicklung / Geplant**
- 🔄 **REDAXO-spezifische Anpassungen (Basics)**
  - Erweiterte Backend-Themes-Unterstützung
  - Verbesserte PJAX/AJAX-Kompatibilität
  - Performance-Optimierungen für große Inhalte
- 🔄 **Dynamisches Nachladen individueller Tools/Blöcke/Vorlagen**
  - Plugin-System für Drittanbieter-Blöcke
  - Hot-Loading von benutzerdefinierten Blöcken
  - Template-basierte Block-Konfiguration
- 🔄 **NoCode-Blöcke für YForm-Datensätze**
  - Visuelle Datenbank-Integration
  - Drag & Drop YForm-Datensatz-Blöcke
  - Automatische Formular-Generierung
  - Datentyp-spezifische Renderer

## Installation

Addon per Installer installieren


## Auto-Initialisierung

Das Addon verwendet ein intelligentes Auto-Initialisierungs-System, das automatisch über `boot.php` geladen wird und **REDAXO-Modulen und YForm funktioniert**:

### Funktionsweise
- **Automatische Erkennung**: Alle Elemente mit `data-editorjs` Attribut oder `.editorjs` Klasse werden automatisch initialisiert
- **REDAXO-Backend optimiert**: Lauscht auf `rex:ready` Events für PJAX-Navigation im Backend
- **Modul-Integration perfekt**: Funktioniert nahtlos in Modulen ohne zusätzliche Konfiguration
- **Frontend-kompatibel**: Funktioniert auch mit `DOMContentLoaded` im Frontend
- **Intelligente Feld-Zuordnung**: Mehrere robuste Strategien zur automatischen Verknüpfung mit Textfeldern
- **Doppel-Initialisierung verhindert**: Sichere Erkennung bereits initialisierter Editoren
- **Automatisches Speichern**: Änderungen werden automatisch mit 300ms Debounce gespeichert
- **Keine manuelle JS-Einbindung nötig**: Assets werden automatisch geladen
- **PJAX/AJAX-kompatibel**: Funktioniert auch bei dynamisch geladenen Inhalten
- **Event-System**: Umfassendes Custom Event System für externe Integration

### Initialisierungs-Strategien (robuste Feld-Erkennung)

Das Auto-Initialisierungs-System verwendet mehrere intelligente Strategien zur Textfeld-Erkennung:

**1. Mit Klasse (empfohlene Methode für Module):**
```html
<div class="editorjs" data-placeholder="Inhalt eingeben..."></div>
<textarea name="REX_INPUT_VALUE[1]" style="display: none;">REX_VALUE[1]</textarea>
```

**2. Mit data-Attribut und expliziter Verknüpfung:**
```html
<div data-editorjs="field-id" data-placeholder="Inhalt eingeben..."></div>
<textarea id="field-id" name="REX_INPUT_VALUE[1]">REX_VALUE[1]</textarea>
```

**3. Mit automatischer Feld-Erkennung (Fallback-Strategien):**
- Sucht nach `textarea[data-editorjs-data]` im Container oder der Form-Group
- Sucht nach dem nächsten `textarea` Element als Geschwister-Element
- Sucht nach `textarea` mit passendem `name` Attribut (REX_INPUT_VALUE Pattern)
- Intelligent parent/container basierte Suche

**4. Robuste ID-Generierung:**
- Automatische ID-Generierung für Container ohne ID
- Sichere Referenzierung zwischen Editor und Textfeld

### Attribute und Konfiguration
- `data-editorjs="field-id"`: Verknüpft Editor explizit mit Textfeld (ID)
- `class="editorjs"`: Automatische Initialisierung mit intelligenter Feld-Erkennung
- `data-placeholder="Text"`: Platzhalter-Text für leeren Editor
- `data-editorjs-data`: Markiert Textfeld als Daten-Container für prioritäre Erkennung
- `data-editorjs-initialized="true"`: Automatisches Attribut zur Doppel-Initialisierung-Verhinderung

### API (EditorJSAutoInit)

```javascript
// Editor manuell für ein Element initialisieren
EditorJSAutoInit.initElement('editor-id');
// oder mit Element-Referenz
EditorJSAutoInit.initElement(document.getElementById('editor-id'));

// Editor-Instanz abrufen
var editor = EditorJSAutoInit.getEditor('editor-id');
// oder mit Element-Referenz
var editor = EditorJSAutoInit.getEditor(document.getElementById('editor-id'));

// Alle Editor-Instanzen abrufen
var editors = EditorJSAutoInit.getAllEditors();
// Rückgabe: Array mit {container: Element, editor: EditorJS}

// Daten manuell speichern
EditorJSAutoInit.saveEditor('editor-id').then(function(savedData) {
    console.log('Daten gespeichert:', savedData);
}).catch(function(error) {
    console.log('Fehler beim Speichern:', error);
});
```

### Events (Custom Events System)

```javascript
// Editor bereit - wird gefeuert wenn der Editor vollständig initialisiert ist
document.getElementById('my-editor').addEventListener('editorjs:ready', function(e) {
    console.log('Editor bereit:', e.detail.editor);
    console.log('Container:', e.detail.container);
    console.log('Datenfeld:', e.detail.field);
});

// Inhalt geändert - wird bei jeder Änderung mit 300ms Debounce gefeuert
document.getElementById('my-editor').addEventListener('editorjs:changed', function(e) {
    console.log('Inhalt geändert:', e.detail.data);
    console.log('Editor-Instanz:', e.detail.editor);
    console.log('Datenfeld:', e.detail.field);
    console.log('Ursprüngliches Event:', e.detail.event);
});
```

**Event Details:**
- `editorjs:ready`: Wird ausgelöst, wenn der Editor vollständig geladen und bereit ist
- `editorjs:changed`: Wird bei Inhaltsänderungen ausgelöst (mit automatischem Speichern)

### Backend vs. Frontend (Automatische Umgebungserkennung)
- **REDAXO Backend**: 
  - Primär: `rex:ready` Event (jQuery-basiert) für PJAX-Navigation
  - Fallback: `rex:ready` Event (Vanilla JS) falls jQuery nicht verfügbar
  - PJAX-Events: `pjax:success` und `pjax:end` für zusätzliche Kompatibilität
- **Frontend**: 
  - Standard: `DOMContentLoaded` für normale Seitenladevorgänge
  - Kompatibilität: Funktioniert auch mit PJAX-Events
- **Robuste Erkennung**: Das Script erkennt automatisch die Umgebung und wählt die optimalen Event-Listener
- **Keine Doppel-Initialisierung**: Sichere Erkennung bereits initialisierter Editoren über `data-editorjs-initialized` Attribut

## Verwendung

### In REDAXO-Modulen

#### 1. Modul-Eingabe (Input) - Empfohlen für Module ⭐

```php
<?php
// Aktueller Wert aus der Datenbank (RAW JSON für Editor)
$value = 'REX_VALUE[1]';
?>

<div class="form-group">
    <label>EditorJS Content:</label>
    <div class="editorjs" 
         data-placeholder="Inhalt hier eingeben..."
         style="border: 1px solid #ddd; min-height: 400px; padding: 10px;">
    </div>
    <textarea name="REX_INPUT_VALUE[1]" style="display: none;"><?= htmlspecialchars($value) ?></textarea>
</div>
```

**Warum diese Methode perfekt für Module ist:**
- ✅ Einfachste Implementierung - nur eine Klasse
- ✅ Automatische Feld-Erkennung funktioniert zuverlässig
- ✅ Keine ID-Konflikte zwischen verschiedenen Modulen
- ✅ Funktioniert sofort ohne weitere Konfiguration

#### 2. Modul-Eingabe (Input) - Mit expliziter ID-Verknüpfung

```php
<?php
// Aktueller Wert aus der Datenbank (RAW JSON für Editor)
$value = 'REX_VALUE[1]';
$valueId = 'REX_INPUT_VALUE[1]';
?>

<div class="form-group">
    <label for="<?= $valueId ?>">EditorJS Content:</label>
    <div id="editorjs-<?= $valueId ?>" 
         data-editorjs="<?= $valueId ?>" 
         data-placeholder="Inhalt hier eingeben..."
         style="border: 1px solid #ddd; min-height: 400px; padding: 10px;">
    </div>
    <textarea id="<?= $valueId ?>" 
              name="<?= $valueId ?>" 
              style="display: none;"><?= htmlspecialchars($value) ?></textarea>
</div>
```

**Verwendung wenn:** Explizite Kontrolle über die Feld-Verknüpfung gewünscht ist.

#### 3. Modul-Eingabe (Input) - Ultra-Minimalistisch

```php
<!-- Nur Klasse - Auto-Initialisierung findet textarea automatisch -->
<div class="editorjs" data-placeholder="Inhalt eingeben..."></div>
<textarea name="REX_INPUT_VALUE[1]" style="display:none;">REX_VALUE[1]</textarea>
```

**Perfekt für:** Schnelle Implementierung ohne viel Code.

#### 4. Modul-Eingabe (Input) - Mit prioritärer Feld-Erkennung

```php
<!-- Mit data-editorjs-data für prioritäre Feld-Erkennung -->
<div data-editorjs data-placeholder="Inhalt eingeben..."></div>
<textarea name="REX_INPUT_VALUE[1]" data-editorjs-data style="display:none;">REX_VALUE[1]</textarea>
```

**Vorteil:** Das `data-editorjs-data` Attribut macht das Textfeld zur ersten Wahl bei der automatischen Erkennung.

#### 5. Modul-Eingabe (Input) - Mit Event-Handling

```php
<?php
$valueId = 'REX_INPUT_VALUE[1]';
$value = 'REX_VALUE[1]'; // RAW JSON für Editor
?>

<div class="editorjs" 
     id="my-editor"
     data-placeholder="Ihr Inhalt..."
     style="border: 1px solid #ddd; min-height: 400px; padding: 10px;">
</div>
<textarea name="<?= $valueId ?>" style="display: none;"><?= htmlspecialchars($value) ?></textarea>

<script>
document.getElementById('my-editor').addEventListener('editorjs:ready', function(e) {
    console.log('Editor ready:', e.detail.editor);
    // Editor ist bereit für weitere Aktionen
});

document.getElementById('my-editor').addEventListener('editorjs:changed', function(e) {
    console.log('Content changed:', e.detail.data);
    // Inhalte automatisch gespeichert (durch Auto-Initialisierung)
    // Hier können Sie zusätzliche Aktionen ausführen
});
</script>
```

**Für:** Erweiterte Funktionalität und eigene Event-Handler.

#### 6. Modul-Ausgabe (Output) - Korrekte Verwendung ✅

```php
<?php 
use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

// Frontend-CSS einbinden
rex_view::addCssFile($this->getAssetsUrl('css/editorjs-frontend.css', 'editorjs'));

// JSON-Daten aus der Datenbank holen
$jsonContent = 'REX_VALUE[1]';

// Mit EditorJS-Renderer zu HTML konvertieren
echo EditorJsRenderer::renderJSON($jsonContent);
?>
```

**⚠️ Wichtiger Hinweis: REX_VALUE Verwendung**

- **Für Editor-Input (Eingabe)**: `REX_VALUE[1]` - Gibt rohe JSON-Daten zurück, die der Editor benötigt
- **Für HTML-Output (Ausgabe)**: `REX_VALUE[1]` + `EditorJsRenderer::renderJSON()` - JSON-Daten werden mit dem Renderer zu HTML konvertiert

**Warum muss gerendert werden?**
- EditorJS speichert Inhalte als JSON-Struktur in der Datenbank
- Für die Frontend-Ausgabe muss diese JSON-Struktur zu HTML konvertiert werden
- Der `EditorJsRenderer::renderJSON()` übernimmt diese Konvertierung und erstellt semantisches HTML

#### 7. Alternative: Manuell (nur bei Bedarf)

**Hinweis:** Mit der überarbeiteten Auto-Initialisierung sind die obigen Beispiele bereits die einfachste und zuverlässigste Form! Die Auto-Initialisierung funktioniert **perfekt in Modulen** und erfordert keine manuelle Intervention.

**Manuelle Initialisierung (normalerweise nicht nötig):**
```javascript
// Nur verwenden wenn Auto-Initialisierung deaktiviert wurde
EditorJSAutoInit.initElement('mein-editor-container');

// Editor-Instanz abrufen
var editor = EditorJSAutoInit.getEditor('mein-editor-container');

// Alle Editoren abrufen
var allEditors = EditorJSAutoInit.getAllEditors();
```

### Erweiterte Verwendung

#### Mit Custom Events (Erweiterte Integration)
```php
<div id="my-editor" data-editorjs="content_field" data-placeholder="Schreiben Sie hier..."></div>
<textarea name="content_field" data-editorjs-data style="display:none;">{"blocks":[]}</textarea>

<script>
document.getElementById('my-editor').addEventListener('editorjs:ready', function(e) {
    console.log('Editor ist bereit!', e.detail.editor);
    console.log('Container:', e.detail.container);
    console.log('Datenfeld:', e.detail.field);
    
    // Zusätzliche Konfiguration nach dem Laden
    // z.B. eigene Toolbar-Buttons hinzufügen
});

document.getElementById('my-editor').addEventListener('editorjs:changed', function(e) {
    console.log('Inhalt geändert!', e.detail.data);
    console.log('Automatisches Speichern aktiv');
    
    // Hier können Sie eigene Logik hinzufügen:
    // - Validierung
    // - Benachrichtigungen
    // - Externe API-Aufrufe
    // - etc.
});
</script>
```

### In YForm-Feldern

```php
<?php
// YForm-Feld für EditorJS
$yform->setValueField('editorjs', array('content', 'Inhalt'));
?>
```

### In Templates

```php
<?php
use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

// Frontend-CSS einbinden
rex_view::addCssFile(rex_addon::get('editorjs')->getAssetsUrl('css/editorjs-frontend.css'));

// JSON-Inhalt aus Artikel-Metafeld holen
$jsonContent = rex_article::getCurrent()->getValue('art_editorjs');

// Mit EditorJS-Renderer zu HTML konvertieren
echo EditorJsRenderer::renderJSON($jsonContent);
?>
```

### Backend
Besuchen Sie `Addons > EditorJS > Demo` für eine interaktive Demonstration.

### Frontend-Renderer
```php
<?php
use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

// JSON zu HTML konvertieren
echo EditorJsRenderer::renderJSON($jsonData);

// In Templates/Modulen - Beispiel
$jsonContent = rex_article::getCurrent()->getValue('editorjs_field');
echo EditorJsRenderer::renderJSON($jsonContent);
?>
```

**💡 Einfache Verwendung**: `EditorJsRenderer::renderJSON($jsonContent)` konvertiert EditorJS-JSON automatisch zu HTML.

Siehe `Addons > EditorJS > Frontend Renderer` für ausführliche Beispiele.

## Entwicklung

### Build-System

```bash
# Bundle erstellen
npm run build

# Watch-Modus für Entwicklung
npm run dev
```

### Eigene Blöcke hinzufügen

1. **Block-Datei erstellen**: `src/blocks/mein-block.js`
2. **CSS-Styles**: `src/blocks/mein-block.css` (optional)
3. **In editorjs.js importieren**:
   ```javascript
   import './blocks/mein-block.js';
   import './blocks/mein-block.css';
   ```
4. **Zu Tools hinzufügen**:
   ```javascript
   tools: {
       meinBlock: {
           class: MeinBlock,
           config: { ... }
       }
   }
   ```
5. **Bundle neu erstellen**: `npm run build`

### Beispiel: Alert-Block

Der Alert-Block zeigt, wie ein benutzerdefinierter Block implementiert wird:

- **Datei**: `src/blocks/alert.js`
- **CSS**: `src/blocks/alert.css`
- **Typen**: info, warning, error, success
- **Features**: Editierbare Titel und Nachrichten, Settings-Panel

### Beispiel: TextImage-Block

Der TextImage-Block kombiniert Text mit Bildern aus dem REDAXO-Medienpool:

- **Datei**: `src/blocks/textimage.js`
- **CSS**: `src/blocks/textimage.css`
- **Features**: 
  - Rich-Text-Formatierung (Bold, Italic, Listen, etc.)
  - REDAXO-Medienpool-Integration
  - 3 Layout-Optionen (Bild links/rechts/oben)
  - Responsive Design
  - Bildunterschriften

## Dateistruktur

```
src/addons/editorjs/
├── package.yml                 # Addon-Konfiguration
├── boot.php                   # Asset-Einbindung
├── pages/
│   ├── index.php             # Hauptseite
│   └── demo.php              # Demo-Seite
├── lang/
│   └── de_de.lang           # Übersetzungen
├── src/
│   ├── editorjs.js          # Haupt-JavaScript
│   └── blocks/              # Benutzerdefinierte Blöcke
│       ├── alert.js
│       ├── alert.css
│       ├── textimage.js
│       └── textimage.css
├── assets/
│   ├── js/
│   │   ├── editorjs.bundle.js       # Generiertes Bundle
│   │   ├── editorjs.bundle.css      # Generierte CSS
│   │   ├── editorjs-auto-init.js    # ⭐ Intelligente Auto-Initialisierung
│   │   └── editorjs-basic.js        # Eigene Utilities
│   └── css/
│       └── editorjs-basic.css       # Eigene Styles
├── package.json              # npm-Konfiguration
└── node_modules/            # npm-Dependencies
```

## Verfügbare Tools

### Standard-Tools
- Header (H2, H3, H4)
- Paragraph
- List (ordered/unordered)
- Quote
- Delimiter
- Code

### Benutzerdefinierte Tools
- **Alert**: Info-, Warning-, Error- und Success-Nachrichten
- **TextImage**: Text-Bild-Kombination mit REDAXO-Medienpool-Integration
- **RexLink**: REDAXO-spezifische Verlinkung (Artikel-Links + externe URLs)

### REDAXO-Integration-Tools
- **Medienpool-Picker**: Native Integration in REDAXO-Medienpool
- **Link-Picker**: Artikel-Verlinkung und externe Links
- **YForm-Integration**: (In Entwicklung) NoCode-Datenbank-Blöcke

## API

### EditorJSUtils

```javascript
// Editor mit Standard-Konfiguration erstellen
const editor = EditorJSUtils.createEditor({
    holder: 'my-editor',
    data: myData
});

// Sicheres JSON-Parsing
const data = EditorJSUtils.parseJSON(jsonString);
```

## Lizenz

Apache-2.0

### Best Practices für Module

**Empfohlener Workflow:**

1. **Einfache Implementierung wählen**: Verwenden Sie `class="editorjs"` für maximale Einfachheit
2. **Styling anpassen**: Passen Sie die CSS-Styles an Ihr Backend-Theme an
3. **Platzhalter verwenden**: Nutzen Sie `data-placeholder` für bessere UX
4. **Events nur bei Bedarf**: Verwenden Sie Custom Events nur wenn Sie erweiterte Funktionalität benötigen

**Modul-Template Beispiel:**
```php
<?php
// Standard-Implementierung für REDAXO-Module
$content = 'REX_VALUE[1]'; // RAW JSON für Editor-Input
?>

<!-- Input -->
<div class="form-group">
    <label>Inhalt (EditorJS):</label>
    <div class="editorjs" 
         data-placeholder="Beginnen Sie mit der Eingabe..."
         style="border: 1px solid #ccc; border-radius: 4px; min-height: 300px; padding: 15px;">
    </div>
    <textarea name="REX_INPUT_VALUE[1]" style="display: none;"><?= htmlspecialchars($content) ?></textarea>
</div>
```

**Styling-Tipps:**
- Mindesthöhe setzen (`min-height: 300px`) für bessere UX
- Border und Padding für klare Abgrenzung
- `display: none` für das Textarea verwenden
- Responsive Design berücksichtigen


## Author

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO


## Credits

**Project Lead**

[Thomas Skerbis](https://github.com/skerbis)

## REDAXO-spezifische Integrationen

### 🎯 **Medienpool-Picker**
Der TextImage-Block bietet eine nahtlose Integration in den REDAXO-Medienpool:

- **Native REDAXO-Integration**: Nutzt die Standard-Medienpool-API
- **Bildauswahl**: Direkter Zugriff auf alle Medienpool-Bilder
- **Metadaten**: Automatische Übernahme von Bildtiteln und Alt-Texten
- **Responsive Ausgabe**: Automatische Bildgrößen-Generierung
- **Kategorie-Filter**: Bilder nach Medienpool-Kategorien filtern

**Verwendung im TextImage-Block:**
```javascript
// Automatisch verfügbar in allen TextImage-Blöcken
// Klick auf "Bild auswählen" öffnet den REDAXO-Medienpool
```

### 🔗 **Link-Picker**
Intelligente Verlinkung mit REDAXO-Artikeln und externen URLs:

- **Artikel-Links**: Direkte Verlinkung zu REDAXO-Artikeln
- **Externe Links**: Standard-URL-Eingabe
- **Link-Titel**: Automatische Übernahme von Artikel-Titeln
- **SEO-optimiert**: Korrekte interne Verlinkung
- **Mehrsprachig**: Unterstützung für mehrsprachige REDAXO-Installationen

**Features:**
- Artikel-Suche mit Autocompletion
- Kategorie-basierte Navigation
- Link-Validierung
- Öffnen in neuem Fenster/Tab-Option

### 🎨 **Backend-Theme Integration**
- **Responsive Design**: Passt sich an REDAXO-Backend-Themes an
- **Dark/Light Mode**: Automatische Theme-Erkennung
- **Konsistente UI**: Verwendet REDAXO-Design-Patterns
- **Accessibility**: WCAG-konform und Tastatur-navigierbar

## Roadmap & Geplante Features

### 🚀 **REDAXO-Basics**
- [ ] Vollständige PJAX/AJAX-Kompatibilität
- [ ] Backend-Theme-Synchronisation
- [ ] Drag & Drop Upload direkt aus dem Editor
- [ ] Broken-Link-Detection

### 🔧 **Plugin-System**
- [ ] Plugin-API für Drittanbieter
- [ ] Hot-Reload für Entwicklung
- [ ] Vordefinierte Block-Templates
- [ ] Import/Export von Block-Konfigurationen

### 📊 **YForm-Integration**
- [ ] NoCode YForm-Blöcke
- [ ] Visuelle Datensatz-Integration
- [ ] Drag & Drop Datenbank-Blöcke
- [ ] Automatische Formular-Generierung

## Beitrag zur Entwicklung

### 🤝 **Community-Entwicklung**
Wir freuen uns über Beiträge zur Weiterentwicklung:

- **Feature-Requests**: [GitHub Issues](https://github.com/FriendsOfREDAXO/editorjs)
- **Bug-Reports**: Detaillierte Beschreibungen mit Reproduktionsschritten
- **Pull-Requests**: Code-Beiträge und Verbesserungen
- **Dokumentation**: Verbesserungen und Übersetzungen

### 📋 **Entwicklungs-Guidelines**
- **Code-Style**: ESLint + Prettier für JavaScript
- **PHP-Standards**: PSR-12 Coding Standards
- **Testing**: Automatisierte Tests für alle neuen Features
- **Dokumentation**: Inline-Kommentare und README-Updates

### 🎯 **Aktuelle Entwicklungs-Prioritäten**
1. **REDAXO-Basics vervollständigen** (höchste Priorität)
2. **Plugin-System implementieren** (mittlere Priorität)
3. **YForm-Integration** (geplant)
4. **Performance-Optimierungen** (kontinuierlich)
