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
import './blocks/rexlink.js';

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
// als window.AlertBlock und window.TextImageBlock

// EditorJS Utilities für REDAXO
window.EditorJSUtils = {
    
    /**
     * Erstellt einen neuen EditorJS mit Standard-Konfiguration
     */
    createEditor: function(options) {
        const defaultOptions = {
            tools: {
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
                    inlineToolbar: true
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
                alert: {
                    class: AlertBlock,
                    config: {
                        defaultType: 'info'
                    }
                },
                textimage: {
                    class: TextImageBlock,
                    inlineToolbar: true,
                    config: {
                        defaultLayout: 'left'
                    }
                },
                // Inline-Tools für Rich-Text-Formatierung
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
                        endpoint: 'http://localhost:8008/fetchUrl' // Optional: für automatische Metadaten
                    }
                },
                rexLink: {
                    class: REXLinkTool,
                    shortcut: 'CMD+K'
                }
            }
        };
        
        // Merge user options with defaults
        const config = Object.assign({}, defaultOptions, options);
        
        return new EditorJS(config);
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
    }
};

console.log('EditorJS Bundle loaded successfully');
