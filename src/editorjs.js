import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import Paragraph from '@editorjs/paragraph';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import Delimiter from '@editorjs/delimiter';
import CodeTool from '@editorjs/code';

// Inline-Tools für Rich-Text-Formatierung
import InlineCode from '@editorjs/inline-code';
import Marker from '@editorjs/marker';
import LinkTool from '@editorjs/link';

// Eigene Blöcke importieren
import './blocks/alert.js';
import './blocks/alert.css';
import './blocks/textimage.js';
import './blocks/textimage.css';
import './blocks/image.js';
import './blocks/image.css';
import './blocks/video.js';
import './blocks/video.css';
import './blocks/rexlink.js';
import './blocks/rexmedia.js';
import './blocks/downloads.js';
import './blocks/downloads.css';
import './blocks/gallery.js';
import './blocks/gallery.css';

// Globale Variablen für REDAXO Backend
window.EditorJS = EditorJS;
window.Header = Header;
window.Paragraph = Paragraph;
window.List = List;
window.Quote = Quote;
window.Delimiter = Delimiter;
window.CodeTool = CodeTool;
window.InlineCode = InlineCode;
window.Marker = Marker;
window.LinkTool = LinkTool;
window.REXLinkTool = REXLinkTool;
// Alert und TextImage Blocks sind bereits über die JS-Dateien verfügbar
// als window.AlertBlock, window.TextImageBlock, window.ImageBlock und window.DownloadsBlock

// Debug: Prüfe ob DownloadsBlock verfügbar ist
console.log('DownloadsBlock available?', typeof window.DownloadsBlock);

// EditorJS Utilities für REDAXO
window.EditorJSUtils = {
    
    /**
     * Erstellt einen neuen EditorJS mit Standard-Konfiguration
     */
    createEditor: function(options) {
        // Standard-Tools holen
        const standardTools = this.getAvailableTools();
        
        // Tools aus Optionen verwenden oder Standard-Tools
        let tools;
        if (options && options.tools) {
            tools = options.tools;
        } else {
            tools = standardTools;
        }

        const defaultOptions = {
            onReady: function() {
                console.log('EditorJS is ready with tools:', Object.keys(tools));
                // Custom Enter-Handling für alle Blöcke
                EditorJSUtils.setupEnterHandling();
            },
            tools: tools
        };
        
        // Merge user options with defaults
        const config = Object.assign({}, defaultOptions, options);
        
        // Debug: Tools-Konfiguration anzeigen
        console.log('EditorJS creating with tools:', Object.keys(config.tools));
        
        return new EditorJS(config);
    },
    
    /**
     * Gibt alle verfügbaren Tools zurück
     */
    getAvailableTools: function() {
        const tools = {
            header: {
                class: Header,
                config: {
                    placeholder: 'Überschrift eingeben...',
                    levels: [2, 3, 4],
                    defaultLevel: 2
                }
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true,
                config: {
                    placeholder: 'Text eingeben...',
                    preserveBlank: true
                }
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Zitat eingeben...',
                    captionPlaceholder: 'Autor'
                }
            },
            delimiter: {
                class: Delimiter
            },
            code: {
                class: CodeTool,
                config: {
                    placeholder: 'Code eingeben...'
                }
            },
            // Inline-Tools
            Marker: {
                class: Marker,
                shortcut: 'CMD+SHIFT+M'
            },
            inlineCode: {
                class: InlineCode,
                shortcut: 'CMD+SHIFT+C'
            },
            linkTool: {
                class: LinkTool,
                config: {
                    endpoint: 'http://localhost:8008/fetchUrl'
                }
            },
            rexLink: {
                class: REXLinkTool,
                shortcut: 'CMD+K'
            }
        };

        // Benutzerdefinierte Blöcke hinzufügen, falls verfügbar
        if (typeof window.AlertBlock !== 'undefined') {
            tools.AlertBlock = {
                class: window.AlertBlock,
                config: {
                    defaultType: 'info'
                }
            };
        }

        if (typeof window.TextImageBlock !== 'undefined') {
            tools.TextImageBlock = {
                class: window.TextImageBlock,
                inlineToolbar: true,
                config: {
                    defaultLayout: 'left'
                }
            };
        }

        if (typeof window.ImageBlock !== 'undefined') {
            tools.ImageBlock = {
                class: window.ImageBlock,
                config: {
                    stretched: false,
                    withBorder: false,
                    withBackground: false,
                    aspectRatio: 'auto',
                    cropMode: 'cover'
                }
            };
        }

        if (typeof window.DownloadsBlock !== 'undefined') {
            tools.downloads = {
                class: window.DownloadsBlock,
                config: {
                    defaultLayout: 'list',
                    showTitle: true
                }
            };
        }

        if (typeof window.ImageGalleryBlock !== 'undefined') {
            tools.gallery = {
                class: window.ImageGalleryBlock,
                config: {
                    defaultLayout: 'grid',
                    showTitle: true,
                    showCaptions: true
                }
            };
        }

        return tools;
    },
    
    /**
     * Hilfsfunktion zum sicheren JSON-Parsen
     */
    parseJSON: function(str) {
        try {
            return JSON.parse(str);
        } catch (e) {
            console.error('JSON Parse Error:', e);
            return null;
        }
    },
    
    /**
     * Setup für verbessertes Enter-Verhalten
     */
    setupEnterHandling: function() {
        // Globaler Event Listener für alle contentEditable Elemente
        document.addEventListener('keydown', function(event) {
            const target = event.target;
            
            // Nur bei contentEditable Elementen in EditorJS
            if (target.contentEditable === 'true' && 
                target.closest('.codex-editor')) {
                
                // Prüfen ob das Element bereits eigene Enter-Handler hat
                // oder ein spezielles Verhalten benötigt
                const hasCustomHandler = target.closest('.cdx-alert') || 
                                        target.closest('.cdx-textimage') ||
                                        target.closest('.cdx-list') ||
                                        target.closest('.ce-block--selected .cdx-list') ||
                                        target.closest('[data-tool="list"]');
                
                // Nur für Standard-Paragraph-Blöcke ohne eigene Handler
                if (!hasCustomHandler && event.key === 'Enter' && !event.shiftKey) {
                    // Enter ohne Shift: Zeilenumbruch im aktuellen Block
                    event.preventDefault();
                    
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        const br = document.createElement('br');
                        
                        range.deleteContents();
                        range.insertNode(br);
                        
                        // Cursor nach dem BR positionieren
                        range.setStartAfter(br);
                        range.setEndAfter(br);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                    
                    return false;
                }
                // Shift+Enter: Standard-Verhalten (neuer Block)
                // Listen: Standard EditorJS-Verhalten beibehalten
            }
        }, true);
    }
};

console.log('EditorJS Bundle loaded successfully');
