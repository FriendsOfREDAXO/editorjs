# 🚀 Hero Block für EditorJS

Ein moderner, vollständig anpassbarer Hero-Block für EditorJS mit integrierter REDAXO Medienpool-Unterstützung.

## ✨ Features

### 🎨 **Visuelles Design**
- **Responsive Design** - Automatische Anpassung an alle Bildschirmgrößen
- **4 Höhen-Varianten** - Klein, Mittel, Groß, Vollbild
- **4 Overlay-Optionen** - Keine, Hell, Dunkel, Gradient
- **3 Ausrichtungen** - Links, Mitte, Rechts
- **Smooth Animationen** - Moderne CSS-Animationen und Hover-Effekte

### 📝 **Content-Features**
- **Rich-Text Editing** - Vollständige Formatierung für alle Textelemente
- **Titel & Untertitel** - Separate Felder mit individueller Styling
- **Beschreibungstext** - Mehrzeiliger Text mit Inline-Formatierung
- **Call-to-Action Buttons** - Zwei konfigurierbare Buttons (primär & sekundär)

### 🖼️ **Medien-Integration**
- **REDAXO Medienpool** - Nahtlose Integration für Hintergrundbilder
- **Bildvorschau** - Live-Vorschau der ausgewählten Bilder
- **Einfache Verwaltung** - Upload, Entfernen und Ersetzen von Bildern

### ⚙️ **Konfiguration**
- **Live-Einstellungen** - Echtzeit-Anpassung über die EditorJS-Toolbar
- **URL-Management** - Einfache Button-URL-Konfiguration via Doppelklick
- **Daten-Persistierung** - Automatisches Speichern aller Einstellungen

## 🚀 Installation

### 1. Dateien kopieren
```bash
# JavaScript-Datei
src/blocks/hero.js

# CSS-Datei  
src/blocks/hero.css
```

### 2. In EditorJS registrieren
```javascript
// Import in editorjs.js
import HeroBlock from './blocks/hero.js';
import './blocks/hero.css';

// Global verfügbar machen
window.HeroBlock = HeroBlock;
```

### 3. In EditorJS-Konfiguration hinzufügen
```javascript
const editor = new EditorJS({
    holder: 'editorjs',
    tools: {
        hero: {
            class: HeroBlock,
            config: {
                // Optional: Medienpool-Konfiguration
                mediaConfig: {
                    types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
                    category: null
                }
            }
        },
        // ... andere Tools
    }
});
```

## 🎯 Verwendung

### Block hinzufügen
1. Klicken Sie auf das **Plus-Symbol** in der EditorJS-Toolbar
2. Wählen Sie **"Hero Section"** aus der Tool-Liste
3. Der Block wird mit Standard-Inhalten eingefügt

### Inhalte bearbeiten
- **Titel**: Direkt anklicken und bearbeiten
- **Untertitel**: Direkt anklicken und bearbeiten  
- **Text**: Direkt anklicken und bearbeiten
- **Buttons**: Text direkt bearbeiten, URLs via Doppelklick

### Hintergrundbild
- **Hinzufügen**: Auf Upload-Bereich klicken → REDAXO Medienpool öffnet sich
- **Entfernen**: X-Button in der Bildvorschau klicken
- **Ersetzen**: Erneut auf Upload-Bereich klicken

### Einstellungen anpassen
Nutzen Sie die **Toolbar-Einstellungen** (Zahnrad-Symbol):

#### 🔧 Höhe
- **Klein** (300px) - Kompakte Hero-Section
- **Mittel** (400px) - Standard-Höhe  
- **Groß** (500px) - Prominente Darstellung
- **Vollbild** (100vh) - Komplette Bildschirmhöhe

#### 🎭 Overlay
- **Keine** - Transparenter Hintergrund
- **Hell** - Heller Overlay für dunkle Bilder
- **Dunkel** - Dunkler Overlay für helle Bilder  
- **Gradient** - Gradient-Overlay für dynamische Effekte

#### 📐 Ausrichtung
- **Links** - Linksbündiger Inhalt
- **Mitte** - Zentrierter Inhalt (Standard)
- **Rechts** - Rechtsbündiger Inhalt

## 🎨 Styling & Anpassung

### CSS-Variablen anpassen
```css
.cdx-hero {
    /* Grundfarben */
    --hero-text-color: white;
    --hero-overlay-dark: rgba(0, 0, 0, 0.4);
    --hero-overlay-light: rgba(255, 255, 255, 0.3);
    
    /* Button-Styling */
    --hero-button-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --hero-button-hover-shadow: rgba(102, 126, 234, 0.4);
}
```

### Responsive Breakpoints
```css
/* Tablet */
@media (max-width: 768px) {
    .cdx-hero__title { font-size: 2.5rem; }
    .cdx-hero__actions { flex-direction: column; }
}

/* Mobile */
@media (max-width: 480px) {
    .cdx-hero__title { font-size: 2rem; }
    .cdx-hero__button { width: 100%; }
}
```

## 📊 Datenstruktur

### Gespeicherte Daten
```javascript
{
    "type": "hero",
    "data": {
        "title": "Ihr Titel hier",
        "subtitle": "Ein aussagekräftiger Untertitel",
        "text": "Beschreibender Text...",
        "backgroundImage": "https://example.com/image.jpg",
        "primaryButton": {
            "text": "Jetzt starten",
            "url": "#action"
        },
        "secondaryButton": {
            "text": "Mehr erfahren", 
            "url": "#info"
        },
        "height": "medium",
        "overlay": "dark",
        "alignment": "center"
    }
}
```

### Validierung
Der Block validiert automatisch:
- ✅ **Titel** muss vorhanden sein
- ✅ **Button-URLs** werden auf gültige Formate geprüft
- ✅ **Bildformate** werden durch REXMediaTool validiert

## 🔧 Erweiterte Konfiguration

### REXMediaTool-Optionen
```javascript
{
    class: HeroBlock,
    config: {
        mediaConfig: {
            types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], // Erlaubte Dateitypen
            category: 1, // Medienpool-Kategorie-ID
            multiple: false, // Mehrfachauswahl deaktiviert
            context: 'hero_backgrounds' // Kontext für Medienpool
        }
    }
}
```

### Inline-Toolbar anpassen
```javascript
static get inlineToolbar() {
    return ['Marker', 'inlineCode', 'rexLink']; // Verfügbare Inline-Tools
}
```

## 🐛 Fehlerbehebung

### Häufige Probleme

**Problem**: Hintergrundbild wird nicht angezeigt
- ✅ **Lösung**: Prüfen Sie REDAXO-Medienpool-Berechtigungen
- ✅ **Lösung**: Kontrollieren Sie die Bild-URL in den Entwicklertools

**Problem**: Buttons reagieren nicht auf Klicks
- ✅ **Lösung**: URLs müssen mit `http://`, `https://` oder `#` beginnen
- ✅ **Lösung**: ReadOnly-Modus deaktivieren für Bearbeitung

**Problem**: Styling wird nicht übernommen
- ✅ **Lösung**: hero.css muss korrekt eingebunden sein
- ✅ **Lösung**: CSS-Konflikte mit anderen Styles prüfen

### Debug-Modus
```javascript
// Debug-Informationen anzeigen
console.log('Hero Block Data:', heroBlock.save());
```

## 🚀 Roadmap & Erweiterungen

### Geplante Features
- [ ] **Video-Hintergründe** - MP4/WebM-Support
- [ ] **Parallax-Effekte** - Scroll-basierte Animationen
- [ ] **Icon-Integration** - FontAwesome/Custom Icons für Buttons
- [ ] **A/B-Testing** - Mehrere Varianten pro Block
- [ ] **Analytics** - Click-Tracking für Buttons

### Beiträge
Contributions sind willkommen! Bitte folgen Sie den REDAXO-Coding-Standards.

## 📄 Lizenz

Dieses Addon steht unter der MIT-Lizenz und ist kompatibel mit REDAXO 5.x.

---

**Entwickelt für REDAXO EditorJS Addon** 🦎
