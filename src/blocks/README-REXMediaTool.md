# REXMediaTool - Zentrale Medienpool-Integration

Die `REXMediaTool`-Komponente bietet eine zentrale, wiederverwendbare Lösung für die Integration des REDAXO Medienpools in EditorJS-Blöcke.

## Features

- ✅ Zentrale Medienpool-Integration für alle Blöcke
- ✅ Konfigurierbare Dateityp-Filter
- ✅ Promise-basierte API
- ✅ Unterstützung für jQuery und Vanilla JavaScript
- ✅ Automatische URL-Generierung
- ✅ Erweiterte Medien-Metadaten
- ✅ Validierung und Error-Handling

## Verwendung

### Basis-Verwendung in einem Block

```javascript
class MeinBlock {
    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        
        // REXMediaTool initialisieren
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif'], // Nur Bilder
                context: 'editorjs_meinblock'
            }
        });
    }
    
    _openMediapool() {
        // Einfache Bildauswahl
        this.mediaTool.selectImage((mediaData) => {
            this._setImage(mediaData);
        }).catch(error => {
            console.error('Fehler:', error);
        });
    }
    
    _setImage(mediaData) {
        // mediaData enthält alle benötigten Informationen
        console.log(mediaData);
        /*
        {
            filename: "bild.jpg",
            url: "/media/bild.jpg",
            alt: "bild.jpg",
            title: "",
            caption: "",
            extension: "jpg",
            isImage: true,
            type: "image",
            size: null,
            width: null,
            height: null,
            category: "",
            raw: {} // Rohdaten vom Medienpool
        }
        */
    }
}
```

### Erweiterte Konfiguration

```javascript
this.mediaTool = new REXMediaTool({ 
    api: this.api, 
    config: {
        types: ['jpg', 'jpeg', 'png', 'gif', 'pdf'], // Spezifische Dateitypen
        multiple: false, // Einzelauswahl
        category: 'bilder', // Kategorie-Filter
        context: 'editorjs_custom' // Kontext für den Medienpool
    }
});
```

### Promise-basierte API

```javascript
// Mit async/await
async _openMediapool() {
    try {
        const mediaData = await this.mediaTool.openMediaPool({
            types: ['jpg', 'png'],
            multiple: false
        });
        this._setImage(mediaData);
    } catch (error) {
        console.error('Fehler beim Medienpool:', error);
    }
}

// Mit Promises
this.mediaTool.openMediaPool()
    .then(mediaData => {
        this._setImage(mediaData);
    })
    .catch(error => {
        console.error('Fehler:', error);
    });
```

### Statische Methoden (ohne Instanz)

```javascript
// Globale Bildauswahl
REXMediaTool.selectImage((mediaData) => {
    console.log('Bild ausgewählt:', mediaData);
});

// Globaler Medienpool
REXMediaTool.openMediaPool({ 
    types: ['pdf', 'doc', 'docx'] 
}).then(mediaData => {
    console.log('Dokument ausgewählt:', mediaData);
});
```

## API-Referenz

### Konstruktor-Optionen

| Option | Typ | Standard | Beschreibung |
|--------|-----|----------|--------------|
| `types` | `Array\|null` | `null` | Erlaubte Dateierweiterungen (null = alle) |
| `multiple` | `boolean` | `false` | Mehrfachauswahl erlauben |
| `category` | `string` | `''` | Kategorie-ID für Filter |
| `context` | `string` | `'editorjs_media'` | Kontext für den Medienpool |

### Methoden

#### `openMediaPool(options, callback)`
Öffnet den Medienpool mit konfigurierbaren Optionen.

**Parameter:**
- `options` (Object): Optionen für diese Auswahl
- `callback` (Function): Optional - Callback-Funktion

**Rückgabe:** Promise mit MediaData-Objekt

#### `selectImage(callback, options)`
Hilfsmethode für einfache Bildauswahl.

**Parameter:**
- `callback` (Function): Callback-Funktion
- `options` (Object): Optional - zusätzliche Optionen

**Rückgabe:** Promise mit MediaData-Objekt

#### `getPreviewUrl(filename, options)`
Erstellt eine Vorschau-URL für Medien.

**Parameter:**
- `filename` (string): Dateiname
- `options` (Object): Optionen wie width, height, crop

**Rückgabe:** string - URL

#### `validateMedia(mediaData)`
Validiert eine Mediendatei gegen die aktuelle Konfiguration.

**Parameter:**
- `mediaData` (Object): MediaData-Objekt

**Rückgabe:** boolean

### MediaData-Objekt

```javascript
{
    filename: "datei.jpg",      // Dateiname
    url: "/media/datei.jpg",    // Vollständige URL
    alt: "datei.jpg",           // Alt-Text
    title: "",                  // Titel
    caption: "",                // Bildunterschrift
    extension: "jpg",           // Dateierweiterung
    isImage: true,              // Ist es ein Bild?
    type: "image",              // Dateityp (image, video, audio, document, archive, file)
    size: null,                 // Dateigröße (falls verfügbar)
    width: null,                // Bildbreite (falls verfügbar)
    height: null,               // Bildhöhe (falls verfügbar)
    category: "",               // Kategorie
    raw: {}                     // Rohdaten vom Medienpool
}
```

## Integration in bestehende Blöcke

### Schritt 1: REXMediaTool importieren
Das Tool wird automatisch über das Bundle geladen, wenn es im `editorjs.js` importiert wird:

```javascript
// In src/editorjs.js
import './blocks/rexmedia.js';
```

### Schritt 2: In Block verwenden
Ersetzen Sie die manuelle Medienpool-Integration durch REXMediaTool:

```javascript
// Vorher (manuell)
_openMediapool() {
    const mediaPool = openMediaPool('editorjs_block');
    $(mediaPool).on('rex:selectMedia', function(event, filename) {
        // Manuelle Verarbeitung...
    });
}

// Nachher (mit REXMediaTool)
constructor() {
    this.mediaTool = new REXMediaTool({ 
        api: this.api, 
        config: { context: 'editorjs_block' }
    });
}

_openMediapool() {
    this.mediaTool.selectImage((mediaData) => {
        this._setImage(mediaData);
    });
}
```

## Vorteile

1. **Konsistenz**: Einheitliche Medienpool-Integration in allen Blöcken
2. **Wartbarkeit**: Zentrale Stelle für Änderungen und Bugfixes
3. **Flexibilität**: Konfigurierbare Optionen für verschiedene Anwendungsfälle
4. **Error-Handling**: Robuste Fehlerbehandlung
5. **Future-Proof**: Einfache Erweiterung um neue Features
6. **TypeScript-Ready**: Klare API-Struktur für spätere TypeScript-Migration

## Beispiel-Blöcke

Die folgenden Blöcke nutzen bereits REXMediaTool:

- `TextImageBlock` - Text mit Bild
- `ImageBlock` - Einfacher Bild-Block (Beispiel)

## Migration bestehender Blöcke

1. Entfernen Sie die manuelle `openMediaPool`-Integration
2. Fügen Sie REXMediaTool zum Konstruktor hinzu
3. Ersetzen Sie `_openMediapool()` durch REXMediaTool-Aufrufe
4. Aktualisieren Sie `_setImage()` um MediaData-Objekt zu verwenden
5. Testen Sie die Funktionalität
