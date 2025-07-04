/**
 * EditorJS Auto-Initialisierung für REDAXO
 * Automatische Erkennung und Initialisierung von EditorJS-Feldern
 */

// Initialisierungs-Funktion
function initEditorJSFields() {
    if (typeof EditorJSUtils === 'undefined') {
        setTimeout(initEditorJSFields, 100);
        return;
    }

    // Alle Elemente mit data-editorjs Attribut oder editorjs Klasse finden
    const editorFields = document.querySelectorAll('[data-editorjs], .editorjs');
    
    editorFields.forEach(function(container) {
        // Verhindere doppelte Initialisierung
        if (container.dataset.editorjsInitialized) {
            return;
        }
        
        // ID generieren falls keine vorhanden
        if (!container.id) {
            container.id = 'editorjs-' + Math.random().toString(36).substr(2, 9);
        }
        
        // Daten-Textfeld finden (mehrere Strategien)
        let dataField = null;
        
        // 1. Explizit angegebenes Feld über data-editorjs Attribut
        if (container.dataset.editorjs) {
            dataField = document.getElementById(container.dataset.editorjs);
        }
        
        // 2. Textfeld mit data-editorjs-data im gleichen Container oder Form-Group
        if (!dataField) {
            const formGroup = container.closest('.form-group');
            if (formGroup) {
                dataField = formGroup.querySelector('textarea[data-editorjs-data]');
            } else {
                dataField = container.querySelector('textarea[data-editorjs-data]');
            }
        }
        
        // 3. Nächstes textarea Element (Fallback)
        if (!dataField) {
            dataField = container.nextElementSibling;
            if (dataField && dataField.tagName !== 'TEXTAREA') {
                // Suche im übergeordneten Container
                const parent = container.parentNode;
                dataField = parent.querySelector('textarea');
            }
        }
        
        // 4. textarea mit gleichem name wie Container-ID (für REX_INPUT_VALUE Pattern)
        if (!dataField && container.id) {
            const namePattern = container.id.replace('editorjs-', '');
            dataField = document.querySelector(`textarea[name="${namePattern}"]`);
        }
        
        // Basis-Konfiguration
        const config = {
            holder: container.id,
            placeholder: container.dataset.placeholder || 'Inhalt hier eingeben...',
            data: {blocks: []}
        };
        
        // Bestehende Daten laden falls Textfeld vorhanden
        if (dataField) {
            if (dataField.value && dataField.value.trim() !== '') {
                const existingData = EditorJSUtils.parseJSON(dataField.value);
                if (existingData) {
                    config.data = existingData;
                }
            }
            
            // onChange-Handler für automatisches Speichern
            config.onChange = function(api, event) {
                // Automatisches Speichern bei Änderungen
                clearTimeout(container.editorJSSaveTimeout);
                container.editorJSSaveTimeout = setTimeout(function() {
                    api.saver.save().then(function(data) {
                        dataField.value = JSON.stringify(data);
                        
                        // Custom Event für externe Listener
                        const changeEvent = new CustomEvent('editorjs:changed', {
                            detail: {
                                editor: api,
                                data: data,
                                field: dataField,
                                event: event
                            }
                        });
                        container.dispatchEvent(changeEvent);
                    }).catch(function(error) {
                        console.error('EditorJS save failed:', error);
                    });
                }, 300); // 300ms debounce
            };
        }
        
        // Editor erstellen
        try {
            const editor = EditorJSUtils.createEditor(config);
            
            // Editor-Instanz am Container speichern
            container.editorJSInstance = editor;
            container.dataset.editorjsInitialized = 'true';
            
            // Ready Event
            editor.isReady.then(function() {
                const event = new CustomEvent('editorjs:ready', {
                    detail: {
                        editor: editor,
                        container: container,
                        field: dataField
                    }
                });
                container.dispatchEvent(event);
            });
            
            console.log('EditorJS auto-initialized for:', container.id, '(class:', container.className, ')');
            
        } catch (error) {
            console.error('EditorJS auto-init failed for:', container.id || container.className, error);
        }
    });
}

// Event-Listener für verschiedene Umgebungen
document.addEventListener('DOMContentLoaded', initEditorJSFields);

// REDAXO Backend Events (PJAX/AJAX Navigation)
if (typeof $ !== 'undefined' && $.fn.on) {
    // jQuery rex:ready Event für REDAXO Backend
    $(document).on('rex:ready', initEditorJSFields);
} else {
    // Fallback für Vanilla JS oder wenn jQuery nicht verfügbar
    document.addEventListener('rex:ready', initEditorJSFields);
}

// PJAX Events (zusätzliche Absicherung)
document.addEventListener('pjax:success', initEditorJSFields);
document.addEventListener('pjax:end', initEditorJSFields);

// Utility-Funktionen für externe Verwendung
window.EditorJSAutoInit = {
    
    /**
     * Editor für ein bestimmtes Element manuell initialisieren
     */
    initElement: function(element) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        
        if (element && !element.dataset.editorjsInitialized) {
            element.dataset.editorjs = element.dataset.editorjs || '';
            initEditorJSFields();
        }
    },
    
    /**
     * Editor-Instanz für ein Element holen
     */
    getEditor: function(element) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        
        return element ? element.editorJSInstance : null;
    },
    
    /**
     * Alle Editor-Instanzen holen
     */
    getAllEditors: function() {
        const editors = [];
        const containers = document.querySelectorAll('[data-editorjs-initialized="true"]');
        
        containers.forEach(function(container) {
            if (container.editorJSInstance) {
                editors.push({
                    container: container,
                    editor: container.editorJSInstance
                });
            }
        });
        
        return editors;
    },
    
    /**
     * Daten manuell speichern für einen Editor
     */
    saveEditor: function(element) {
        const editor = this.getEditor(element);
        if (editor) {
            return editor.save();
        }
        return Promise.reject('Editor not found');
    }
};