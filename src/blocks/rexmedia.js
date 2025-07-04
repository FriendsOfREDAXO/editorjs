/**
 * REDAXO Media Tool für EditorJS
 * Zentrale Komponente für Medienpool-Integration
 */
class REXMediaTool {
    static get title() {
        return 'REDAXO Media';
    }

    static get icon() {
        return '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 3H3C1.9 3 1 3.9 1 5v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 17l3.5-4.5 2.5 3.01L14.5 11l4.5 6H5z"/></svg>';
    }

    static get isInline() {
        return false; // Kann sowohl inline als auch block sein
    }

    constructor({ api, config }) {
        this.api = api;
        this.config = config || {};
        
        // Standard-Konfiguration
        this.defaultConfig = {
            types: null, // null = alle Dateitypen, sonst Array mit erlaubten Typen
            multiple: false, // Mehrfachauswahl erlauben
            category: '', // Kategorie-ID für Filter
            context: 'editorjs_media' // Kontext für den Medienpool
        };
        
        this.settings = { ...this.defaultConfig, ...this.config };
    }

    /**
     * Öffnet den Medienpool mit konfigurierbaren Optionen
     * @param {Object} options - Optionen für die Medienauswahl
     * @param {Function} callback - Callback-Funktion, die bei Auswahl aufgerufen wird
     * @returns {Promise} Promise mit den ausgewählten Medien
     */
    openMediaPool(options = {}, callback = null) {
        return new Promise((resolve, reject) => {
            if (typeof openMediaPool === 'undefined') {
                const error = 'Medienpool ist nicht verfügbar';
                alert(error);
                reject(new Error(error));
                return;
            }

            // Optionen zusammenführen
            const config = { ...this.settings, ...options };
            
            // Parameter für den Medienpool aufbauen
            let params = config.context;
            
            // Dateitypen filtern (falls konfiguriert)
            if (config.types && Array.isArray(config.types) && config.types.length > 0) {
                params += '&args[types]=' + config.types.join(',');
            } else if (typeof rex !== 'undefined' && rex.editorjs_rex_media_getImageTypes) {
                // Fallback: nur Bildtypen wenn verfügbar
                params += '&args[types]=' + rex.editorjs_rex_media_getImageTypes.join(',');
            }
            
            // Kategorie-Filter
            if (config.category) {
                params += '&args[category]=' + encodeURIComponent(config.category);
            }
            
            // Mehrfachauswahl
            if (config.multiple) {
                params += '&args[multiple]=1';
            }

            const mediaPool = openMediaPool(params);
            
            // Event Listener für die Medienauswahl
            const handleMediaSelect = (event, filename, additionalData = {}) => {
                event.preventDefault();
                mediaPool.close();
                
                const mediaData = this._createMediaData(filename, additionalData, config);
                
                // Callback aufrufen falls vorhanden
                if (callback && typeof callback === 'function') {
                    callback(mediaData);
                }
                
                resolve(mediaData);
            };

            if (typeof $ !== 'undefined') {
                // jQuery Version (falls verfügbar)
                $(mediaPool).on('rex:selectMedia', handleMediaSelect);
            } else {
                // Vanilla JS Fallback
                mediaPool.addEventListener('rex:selectMedia', function(event) {
                    const filename = event.detail?.filename || event.filename;
                    const additionalData = event.detail || {};
                    handleMediaSelect(event, filename, additionalData);
                });
            }

            // Error handling
            mediaPool.addEventListener('error', function(event) {
                reject(new Error('Fehler beim Öffnen des Medienpools: ' + event.message));
            });
        });
    }

    /**
     * Erstellt Medien-Daten-Objekt mit allen relevanten Informationen
     * @param {string} filename - Dateiname
     * @param {Object} additionalData - Zusätzliche Daten vom Medienpool
     * @param {Object} config - Aktuelle Konfiguration
     * @returns {Object} Medien-Daten-Objekt
     */
    _createMediaData(filename, additionalData = {}, config = {}) {
        // URL erstellen
        let url = '/media/' + filename;
        if (typeof rex !== 'undefined' && rex.editorjs_imageUrlPath) {
            url = rex.editorjs_imageUrlPath + filename;
        }

        // Dateierweiterung und Typ ermitteln
        const extension = filename.split('.').pop().toLowerCase();
        const isImage = this._isImageFile(extension);
        
        return {
            filename: filename,
            url: url,
            alt: additionalData.alt || filename,
            title: additionalData.title || '',
            caption: additionalData.caption || '',
            extension: extension,
            isImage: isImage,
            type: this._getFileType(extension),
            size: additionalData.size || null,
            width: additionalData.width || null,
            height: additionalData.height || null,
            category: additionalData.category || '',
            // Rohdaten für spezielle Verwendung
            raw: additionalData
        };
    }

    /**
     * Prüft ob eine Datei ein Bild ist
     * @param {string} extension - Dateierweiterung
     * @returns {boolean}
     */
    _isImageFile(extension) {
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp', 'ico'];
        return imageExtensions.includes(extension);
    }

    /**
     * Ermittelt den Dateityp basierend auf der Erweiterung
     * @param {string} extension - Dateierweiterung
     * @returns {string}
     */
    _getFileType(extension) {
        const typeMap = {
            // Bilder
            'jpg': 'image', 'jpeg': 'image', 'png': 'image', 'gif': 'image', 
            'svg': 'image', 'webp': 'image', 'bmp': 'image', 'ico': 'image',
            
            // Videos
            'mp4': 'video', 'avi': 'video', 'mov': 'video', 'wmv': 'video', 
            'flv': 'video', 'webm': 'video', 'mkv': 'video',
            
            // Audio
            'mp3': 'audio', 'wav': 'audio', 'ogg': 'audio', 'flac': 'audio', 
            'aac': 'audio', 'm4a': 'audio',
            
            // Dokumente
            'pdf': 'document', 'doc': 'document', 'docx': 'document', 
            'xls': 'document', 'xlsx': 'document', 'ppt': 'document', 'pptx': 'document',
            'txt': 'document', 'rtf': 'document',
            
            // Archive
            'zip': 'archive', 'rar': 'archive', '7z': 'archive', 'tar': 'archive', 'gz': 'archive'
        };
        
        return typeMap[extension] || 'file';
    }

    /**
     * Erstellt eine Vorschau-URL für Medien
     * @param {string} filename - Dateiname
     * @param {Object} options - Optionen für die Vorschau (Größe, etc.)
     * @returns {string} Vorschau-URL
     */
    getPreviewUrl(filename, options = {}) {
        const { width = null, height = null, crop = false } = options;
        
        // Basis-URL
        let url = '/media/' + filename;
        if (typeof rex !== 'undefined' && rex.editorjs_imageUrlPath) {
            url = rex.editorjs_imageUrlPath + filename;
        }
        
        // REDAXO Media Manager Integration (falls verfügbar)
        if (width || height) {
            // Hier könnte eine Integration mit REDAXO Media Manager erfolgen
            // Beispiel: url = '/images/media_manager_type/' + filename;
            // Das hängt von der spezifischen REDAXO-Konfiguration ab
        }
        
        return url;
    }

    /**
     * Validiert eine Mediendatei gegen die aktuelle Konfiguration
     * @param {Object} mediaData - Medien-Daten-Objekt
     * @returns {boolean} True wenn valide
     */
    validateMedia(mediaData) {
        // Typ-Validierung
        if (this.settings.types && Array.isArray(this.settings.types)) {
            if (!this.settings.types.includes(mediaData.extension)) {
                return false;
            }
        }
        
        // Weitere Validierungen können hier hinzugefügt werden
        
        return true;
    }

    /**
     * Hilfsmethode für einfache Bildauswahl (am häufigsten verwendet)
     * @param {Function} callback - Callback-Funktion
     * @param {Object} options - Optionale Einstellungen
     */
    selectImage(callback, options = {}) {
        const imageOptions = {
            types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
            ...options
        };
        
        return this.openMediaPool(imageOptions, callback);
    }

    /**
     * Hilfsmethode für Datei-Upload (falls implementiert)
     * @param {File} file - Datei-Objekt
     * @returns {Promise} Promise mit Upload-Ergebnis
     */
    uploadFile(file) {
        // Placeholder für Upload-Funktionalität
        // Dies würde eine Upload-API zu REDAXO benötigen
        return Promise.reject(new Error('Upload-Funktionalität noch nicht implementiert'));
    }
}

// Static-Methode für globale Verwendung
REXMediaTool.openMediaPool = function(options = {}) {
    const tool = new REXMediaTool({ api: null, config: options });
    return tool.openMediaPool(options);
};

// Static-Methode für einfache Bildauswahl
REXMediaTool.selectImage = function(callback, options = {}) {
    const tool = new REXMediaTool({ api: null, config: options });
    return tool.selectImage(callback, options);
};

// Export für das Bundle
window.REXMediaTool = REXMediaTool;
