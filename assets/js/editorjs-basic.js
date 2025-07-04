/* EditorJS Basic JavaScript */

/* Globale EditorJS Utilities */
window.EditorJSUtils = {
    
    /**
     * Erstellt einen neuen EditorJS mit Standard-Konfiguration
     */
    createEditor: function(options) {
        var defaultOptions = {
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
                }
            }
        };
        
        // Merge user options with defaults
        var config = Object.assign({}, defaultOptions, options);
        
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

/* Ready Event für EditorJS */
document.addEventListener('DOMContentLoaded', function() {
    console.log('EditorJS Basic loaded');
});
