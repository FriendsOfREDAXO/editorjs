/**
 * EditorJS Auto-Initialisierung für REDAXO
 * Optimiert für REDAXO's rex:ready Event und PJAX-Navigation
 */

// Globales Objekt zum Speichern aller Editor-Instanzen
window.editorInstances = window.editorInstances || {};

// Cleanup-Funktion für kaputte oder alte Editor-Instanzen
function cleanupDeadEditor(container) {
    console.log(`[EditorJS] Cleaning up dead editor: ${container.id}`);
    
    // Container-Referenzen löschen
    if (container.editorJSInstance) {
        try {
            // Versuche den Editor ordentlich zu zerstören
            if (typeof container.editorJSInstance.destroy === 'function') {
                container.editorJSInstance.destroy();
            }
        } catch (e) {
            console.warn(`[EditorJS] Editor destroy failed for ${container.id}:`, e);
        }
        delete container.editorJSInstance;
    }
    
    // Globale Referenz löschen
    if (container.id && window.editorInstances[container.id]) {
        delete window.editorInstances[container.id];
    }
    
    // Flags zurücksetzen
    container.dataset.editorjsInitialized = 'false';
    
    // Alte DOM-Elemente entfernen
    const oldEditor = container.querySelector('.codex-editor');
    if (oldEditor) {
        oldEditor.remove();
    }
    
    // Timeouts bereinigen
    if (container.editorJSSaveTimeout) {
        clearTimeout(container.editorJSSaveTimeout);
        delete container.editorJSSaveTimeout;
    }
}

// Hauptinitialisierungsfunktion
function initEditorJSFields() {
    // Prüfen ob EditorJSUtils verfügbar ist
    if (typeof EditorJSUtils === 'undefined') {
        console.log('[EditorJS] EditorJSUtils not ready, retrying in 200ms...');
        setTimeout(initEditorJSFields, 200);
        return;
    }

    console.log('[EditorJS] === Starting Auto-Initialization ===');
    console.log('[EditorJS] Location:', window.location.href);
    console.log('[EditorJS] Ready state:', document.readyState);

    // Alle potentiellen Editor-Felder finden
    const editorFields = document.querySelectorAll('[data-editorjs], .editorjs, textarea[data-editorjs-direct]');
    console.log(`[EditorJS] Found ${editorFields.length} potential editor fields`);
    
    editorFields.forEach((element, index) => {
        console.log(`[EditorJS] Processing field ${index + 1}/${editorFields.length}`);
        
        let container, dataField;
        
        // Direktinitialisierung für textareas mit data-editorjs-direct
        if (element.tagName === 'TEXTAREA' && element.hasAttribute('data-editorjs-direct')) {
            console.log('[EditorJS] === DIRECT TEXTAREA INITIALIZATION ===');
            
            dataField = element;
            const containerId = 'editorjs-' + (dataField.id || dataField.name?.replace(/[[\]]/g, '-') || Math.random().toString(36).substr(2, 9));
            
            // Prüfen ob bereits ein Container existiert
            let existingContainer = document.getElementById(containerId);
            
            if (existingContainer) {
                if (existingContainer.dataset.editorjsInitialized === 'true' && existingContainer.editorJSInstance) {
                    console.log(`[EditorJS] ✓ Direct textarea ${containerId} already properly initialized`);
                    return;
                }
                // Alten Container cleanup
                console.log(`[EditorJS] Cleaning up old container: ${containerId}`);
                cleanupDeadEditor(existingContainer);
                existingContainer.remove();
            }
            
            // Neuen Container erstellen
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'editorjs editorjs-direct';
            container.style.minHeight = '200px';
            container.style.border = '1px solid #ddd';
            container.style.padding = '10px';
            container.style.marginBottom = '10px';
            
            // Eigenschaften übertragen
            container.dataset.placeholder = dataField.placeholder || 'Inhalt hier eingeben...';
            if (dataField.dataset.editorjsTools) {
                container.dataset.editorjsTools = dataField.dataset.editorjsTools;
            }
            
            // Container vor textarea einfügen und textarea verstecken
            dataField.parentNode.insertBefore(container, dataField);
            dataField.style.display = 'none';
            
            // Starke Verknüpfung zwischen Container und Textarea
            container._linkedTextarea = dataField;
            dataField._editorContainer = container;
            
            console.log(`[EditorJS] Direct container created: ${containerId} -> ${dataField.name || dataField.id}`);
            
        } else {
            // Normale Container-Initialisierung
            container = element;
            
            // ID generieren falls keine vorhanden
            if (!container.id) {
                container.id = 'editorjs-' + Math.random().toString(36).substr(2, 9);
            }
            
            // Prüfen ob bereits funktional initialisiert
            if (container.dataset.editorjsInitialized === 'true' && container.editorJSInstance) {
                try {
                    // Test ob Editor noch funktioniert
                    if (container.editorJSInstance.blocks && typeof container.editorJSInstance.blocks.getBlocksCount === 'function') {
                        console.log(`[EditorJS] ✓ Container ${container.id} already working`);
                        return;
                    }
                } catch (error) {
                    console.log(`[EditorJS] Container ${container.id} appears broken, reinitializing`);
                }
            }
            
            // Cleanup bei defekten Editoren
            if (container.querySelector('.codex-editor')) {
                cleanupDeadEditor(container);
            }
        }
        
        console.log(`[EditorJS] Initializing container: ${container.id}`);
        
        // Datenfeld finden (nur wenn nicht bereits von Direktinitialisierung gesetzt)
        if (!dataField) {
            // 1. Verknüpftes Textarea (bei Direktinitialisierung)
            if (container._linkedTextarea) {
                dataField = container._linkedTextarea;
            }
            // 2. Explizit angegebenes Feld
            else if (container.dataset.editorjs) {
                dataField = document.getElementById(container.dataset.editorjs);
            }
            // 3. Textarea mit data-editorjs-data
            else {
                const formGroup = container.closest('.form-group');
                if (formGroup) {
                    dataField = formGroup.querySelector('textarea[data-editorjs-data]');
                } else {
                    dataField = container.querySelector('textarea[data-editorjs-data]');
                }
            }
            // 4. Nächstes textarea (Fallback)
            if (!dataField) {
                let sibling = container.nextElementSibling;
                if (sibling && sibling.tagName === 'TEXTAREA') {
                    dataField = sibling;
                } else {
                    const parent = container.parentNode;
                    dataField = parent ? parent.querySelector('textarea') : null;
                }
            }
        }
        
        // Editor-Konfiguration erstellen
        const config = {
            holder: container.id,
            placeholder: container.dataset.placeholder || 'Inhalt hier eingeben...',
            data: { blocks: [] }
        };
        
        // Tool-Filterung
        let allowedTools = container.dataset.editorjsTools || (dataField && dataField.dataset.editorjsTools) || null;
        if (allowedTools) {
            console.log(`[EditorJS] Tool restriction for ${container.id}: ${allowedTools}`);
        }
        
        // Bestehende Daten laden
        if (dataField && dataField.value && dataField.value.trim() !== '') {
            console.log(`[EditorJS] Loading existing data for ${container.id}`);
            const existingData = EditorJSUtils.parseJSON(dataField.value);
            if (existingData) {
                config.data = existingData;
                console.log(`[EditorJS] ✓ Loaded ${existingData.blocks ? existingData.blocks.length : 0} blocks`);
            } else {
                console.warn(`[EditorJS] Failed to parse data for ${container.id}`);
            }
            
            // Auto-save onChange Handler
            config.onChange = (api, event) => {
                clearTimeout(container.editorJSSaveTimeout);
                container.editorJSSaveTimeout = setTimeout(() => {
                    api.saver.save().then(data => {
                        const jsonData = JSON.stringify(data);
                        
                        // Ziel-Textarea finden
                        let targetField = dataField;
                        if (container._linkedTextarea) {
                            targetField = container._linkedTextarea;
                        }
                        
                        if (targetField && targetField.tagName === 'TEXTAREA') {
                            targetField.value = jsonData;
                            console.log(`[EditorJS] Auto-saved data for ${container.id}`);
                            
                            // Change event triggern
                            targetField.dispatchEvent(new Event('change', { bubbles: true }));
                            
                            // Custom Event
                            container.dispatchEvent(new CustomEvent('editorjs:changed', {
                                detail: { editor: api, data: data, field: targetField, event: event }
                            }));
                        }
                    }).catch(error => {
                        console.error(`[EditorJS] Save failed for ${container.id}:`, error);
                    });
                }, 300);
            };
        }
        
        // Editor erstellen
        try {
            console.log(`[EditorJS] Creating editor instance for ${container.id}`);
            container.dataset.editorjsInitialized = 'initializing';
            
            EditorJSUtils.createEditor(config, allowedTools).then(editor => {
                if (!editor) {
                    console.error(`[EditorJS] Failed to create editor for ${container.id}`);
                    cleanupDeadEditor(container);
                    return;
                }
                
                // Editor-Instanz speichern
                container.editorJSInstance = editor;
                window.editorInstances[container.id] = editor;
                container.dataset.editorjsInitialized = 'true';
                
                console.log(`[EditorJS] ✓ Editor created for ${container.id}`);
                
                // Ready Event abwarten
                editor.isReady.then(() => {
                    console.log(`[EditorJS] ✓ Editor ready for ${container.id}`);
                    
                    // Custom Event
                    container.dispatchEvent(new CustomEvent('editorjs:ready', {
                        detail: { editor: editor, container: container, field: dataField }
                    }));
                    
                    // Bei Direktinitialisierung: Textarea markieren
                    if (container._linkedTextarea) {
                        container._linkedTextarea.dataset.editorjsInitialized = 'true';
                        container._linkedTextarea.dataset.editorjsContainer = container.id;
                    }
                    
                }).catch(error => {
                    console.error(`[EditorJS] Editor ready failed for ${container.id}:`, error);
                });
                
            }).catch(error => {
                console.error(`[EditorJS] Editor creation failed for ${container.id}:`, error);
                cleanupDeadEditor(container);
            });
            
        } catch (error) {
            console.error(`[EditorJS] Initialization error for ${container.id}:`, error);
            cleanupDeadEditor(container);
        }
    });
    
    console.log('[EditorJS] === Auto-Initialization Complete ===');
}
// Event-Listener Setup
if (typeof $ !== 'undefined' && $.fn.on) {
    // REDAXO Backend: Nur rex:ready verwenden
    console.log('[EditorJS] REDAXO Backend detected - using rex:ready event');
    
    $(document).on('rex:ready', function() {
        console.log('[EditorJS] rex:ready event triggered');
        
        // Kurze Verzögerung für DOM-Stabilität
        setTimeout(() => {
            initEditorJSFields();
        }, 150);
    });
    
} else {
    // Frontend: Standard Events
    console.log('[EditorJS] Frontend detected - using DOMContentLoaded');
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEditorJSFields);
    } else {
        // Dokument bereits geladen
        setTimeout(initEditorJSFields, 50);
    }
}

// Utility-API für externe Verwendung
window.EditorJSAutoInit = {
    
    /**
     * Editor für ein bestimmtes Element manuell initialisieren
     */
    initElement: function(element) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        
        if (element && element.dataset.editorjsInitialized !== 'true') {
            // Temporary flag to make sure it gets processed
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
        
        containers.forEach(container => {
            if (container.editorJSInstance) {
                editors.push({
                    id: container.id,
                    container: container,
                    editor: container.editorJSInstance
                });
            }
        });
        
        return editors;
    },
    
    /**
     * Daten für einen Editor speichern
     */
    saveEditor: function(element) {
        const editor = this.getEditor(element);
        if (editor) {
            return editor.save();
        }
        return Promise.reject('Editor not found');
    },
    
    /**
     * Alle Editoren bereinigen (für Debugging)
     */
    cleanupAllEditors: function() {
        console.log('[EditorJS] Manual cleanup of all editors');
        const activeEditors = document.querySelectorAll('[data-editorjs-initialized]');
        activeEditors.forEach(container => {
            cleanupDeadEditor(container);
        });
        window.editorInstances = {};
        console.log('[EditorJS] All editors cleaned up');
    },
    
    /**
     * Force re-initialization aller Felder
     */
    forceReinit: function() {
        console.log('[EditorJS] Force re-initialization');
        this.cleanupAllEditors();
        setTimeout(initEditorJSFields, 200);
    }
};