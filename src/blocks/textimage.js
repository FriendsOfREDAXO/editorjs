/**
 * TextImage Block für EditorJS
 * Ein Block mit Text und Bild aus dem REDAXO Medienpool
 */
class TextImageBlock {
    static get toolbox() {
        return {
            title: 'Text & Bild',
            icon: '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 3H3C1.9 3 1 3.9 1 5v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 17l3.5-4.5 2.5 3.01L14.5 11l4.5 6H5z"/><path d="M3 13h8v6H3z"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    /**
     * Inline-Toolbar aktivieren für Rich-Text-Editing
     */
    static get enableLineBreaks() {
        return true;
    }

    /**
     * Inline-Tools für Rich-Text-Formatierung
     */
    static get inlineToolbar() {
        return ['Marker', 'inlineCode', 'rexLink']; // Verwende unser REDAXO Link-Tool
    }

    /**
     * Konvertierung von/zu anderen Blöcken erlauben
     */
    static get conversionConfig() {
        return {
            export: 'text', // Export als text für Konvertierung zu anderen Blöcken
            import: 'text'  // Import aus text von anderen Blöcken
        };
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        this.config = config || {};
        
        // REXMediaTool für Medienpool-Integration (Bilder und Videos)
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'avi', 'mov'], // Bilder und Videos
                context: 'editorjs_textimage'
            }
        });
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-textimage',
            container: 'cdx-textimage__container',
            imageWrapper: 'cdx-textimage__image-wrapper',
            image: 'cdx-textimage__image',
            video: 'cdx-textimage__video',
            textWrapper: 'cdx-textimage__text-wrapper',
            text: 'cdx-textimage__text',
            button: 'cdx-textimage__button',
            caption: 'cdx-textimage__caption',
            settingsButton: 'cdx-textimage__settings-button',
            settingsButtonActive: 'cdx-textimage__settings-button--active',
            altWarning: 'cdx-image__alt-warning', // Gleiche CSS-Klasse wie ImageBlock
            lightboxOverlay: 'cdx-textimage__lightbox-overlay',
            lightboxImage: 'cdx-textimage__lightbox-image'
        };

        this.nodes = {
            holder: null,
            container: null,
            imageWrapper: null,
            image: null,
            video: null,
            textWrapper: null,
            text: null,
            caption: null,
            selectButton: null,
            altWarning: null
        };

        this.data = {
            text: data.text || '',
            mediaFile: data.mediaFile || data.imageFile || '', // Kompatibilität mit alten Daten
            mediaUrl: data.mediaUrl || data.imageUrl || '',
            mediaType: data.mediaType || (data.imageFile ? 'image' : ''), // 'image' oder 'video'
            mediaAlt: data.mediaAlt || data.imageAlt || '',
            caption: data.caption || '',
            layout: data.layout || 'left', // left, right, top
            stretched: data.stretched || false,
            lightbox: data.lightbox !== undefined ? data.lightbox : true, // Lightbox standardmäßig aktiviert
            videoAutoplay: data.videoAutoplay !== undefined ? data.videoAutoplay : false,
            videoMuted: data.videoMuted !== undefined ? data.videoMuted : false,
            videoLoop: data.videoLoop !== undefined ? data.videoLoop : false,
            videoControls: data.videoControls !== undefined ? data.videoControls : true
        };

        this.layouts = {
            left: {
                title: 'Bild links',
                icon: '<i class="fa-solid fa-align-left"></i>'
            },
            right: {
                title: 'Bild rechts', 
                icon: '<i class="fa-solid fa-align-right"></i>'
            },
            top: {
                title: 'Bild oben',
                icon: '<i class="fa-solid fa-align-center"></i>'
            }
        };
    }

    render() {
        const holder = this._make('div', [this.CSS.baseClass, this.CSS.wrapper]);
        const container = this._make('div', [this.CSS.container]);
        
        holder.dataset.layout = this.data.layout;
        holder.appendChild(container);

        // Image/Video Wrapper
        const imageWrapper = this._make('div', [this.CSS.imageWrapper]);
        
        if (this.data.mediaUrl) {
            this._createMedia();
        } else {
            this._createSelectButton();
        }
        
        imageWrapper.appendChild(this.nodes.image || this.nodes.video || this.nodes.selectButton);
        
        // Caption nur anzeigen wenn vorhanden
        if (this.data.caption) {
            const caption = this._make('div', [this.CSS.caption], {
                innerHTML: this.data.caption
            });
            imageWrapper.appendChild(caption);
            this.nodes.caption = caption;
        }

        // Text Wrapper
        const textWrapper = this._make('div', [this.CSS.textWrapper]);
        const text = this._make('div', [this.CSS.text], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.text || 'Geben Sie hier Ihren Text ein...'
        });
        
        // Enter-Verhalten anpassen für Text-Bereich
        if (!this.readOnly) {
            text.addEventListener('keydown', this._handleEnter.bind(this));
        }
        
        textWrapper.appendChild(text);

        // Immer die gleiche DOM-Reihenfolge: Image zuerst, dann Text
        // CSS flex-direction steuert die visuelle Anordnung
        container.appendChild(imageWrapper);
        container.appendChild(textWrapper);

        this.nodes.holder = holder;
        this.nodes.container = container;
        this.nodes.imageWrapper = imageWrapper;
        this.nodes.textWrapper = textWrapper;
        this.nodes.text = text;

        return holder;
    }

    renderSettings() {
        const wrapper = this._make('div');

        // Layout-Buttons
        Object.entries(this.layouts).forEach(([layout, config]) => {
            const button = this._make('span', [this.CSS.settingsButton], {
                innerHTML: config.icon + ' ' + config.title,
                title: config.title
            });

            button.addEventListener('click', () => {
                this._changeLayout(layout);
                
                // Update active state
                wrapper.querySelectorAll('.' + this.CSS.settingsButton).forEach(btn => {
                    btn.classList.remove(this.CSS.settingsButtonActive);
                });
                button.classList.add(this.CSS.settingsButtonActive);
            });

            if (layout === this.data.layout) {
                button.classList.add(this.CSS.settingsButtonActive);
            }

            wrapper.appendChild(button);
        });

        // Bild ändern Button
        const changeImageButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-image"></i>',
            title: 'Bild aus Medienpool wählen'
        });
        
        changeImageButton.addEventListener('click', () => {
            this._openMediapool();
        });
        
        // Alt-Text Button
        const altTextButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-universal-access"></i>',
            title: 'Alt-Text für Barrierefreiheit bearbeiten'
        });
        
        altTextButton.addEventListener('click', () => {
            this._editAltText();
        });
        
        // Caption bearbeiten Button
        const captionButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-closed-captioning"></i>',
            title: 'Bildunterschrift bearbeiten'
        });
        
        if (this.data.caption) {
            captionButton.classList.add(this.CSS.settingsButtonActive);
        }
        
        captionButton.addEventListener('click', () => {
            this._editCaption();
        });

        // Lightbox Button (nur bei Bildern)
        const lightboxButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-search-plus"></i>',
            title: 'Lightbox aktivieren/deaktivieren'
        });
        
        if (this.data.lightbox && this.data.mediaType === 'image') {
            lightboxButton.classList.add(this.CSS.settingsButtonActive);
        }
        
        lightboxButton.addEventListener('click', () => {
            this._toggleLightbox();
            lightboxButton.classList.toggle(this.CSS.settingsButtonActive, this.data.lightbox);
        });

        // Video-Optionen Button (nur bei Videos)
        const videoOptionsButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-play-circle"></i>',
            title: 'Video-Optionen'
        });
        
        videoOptionsButton.addEventListener('click', () => {
            this._showVideoOptions();
        });
        
        wrapper.appendChild(changeImageButton);
        wrapper.appendChild(altTextButton);
        wrapper.appendChild(captionButton);
        
        // Lightbox-Button nur bei Bildern anzeigen
        if (this.data.mediaType === 'image') {
            wrapper.appendChild(lightboxButton);
        }
        
        // Video-Optionen nur bei Videos anzeigen
        if (this.data.mediaType === 'video') {
            wrapper.appendChild(videoOptionsButton);
        }

        return wrapper;
    }

    save(blockContent) {
        const text = blockContent.querySelector('.' + this.CSS.text);

        return {
            text: text.innerHTML,
            mediaFile: this.data.mediaFile,
            mediaUrl: this.data.mediaUrl,
            mediaType: this.data.mediaType,
            mediaAlt: this.data.mediaAlt,
            caption: this.data.caption,
            layout: this.data.layout,
            stretched: this.data.stretched,
            lightbox: this.data.lightbox,
            videoAutoplay: this.data.videoAutoplay,
            videoMuted: this.data.videoMuted,
            videoLoop: this.data.videoLoop,
            videoControls: this.data.videoControls,
            // Rückwärtskompatibilität
            imageFile: this.data.mediaFile,
            imageUrl: this.data.mediaUrl,
            imageAlt: this.data.mediaAlt
        };
    }

    static get sanitize() {
        return {
            text: {
                br: true,
                strong: true,
                em: true,
                u: true,
                s: true,
                a: {
                    href: true,
                    target: '_blank'
                },
                p: true,
                h1: true,
                h2: true,
                h3: true,
                h4: true,
                h5: true,
                h6: true,
                ul: true,
                ol: true,
                li: true
            },
            caption: {
                br: true,
                strong: true,
                em: true
            },
            mediaFile: {},
            mediaUrl: {},
            mediaType: {},
            mediaAlt: {},
            layout: {},
            stretched: {},
            lightbox: {},
            videoAutoplay: {},
            videoMuted: {},
            videoLoop: {},
            videoControls: {},
            // Rückwärtskompatibilität
            imageFile: {},
            imageUrl: {},
            imageAlt: {}
        };
    }

    _createMedia() {
        // Automatisch erkennen ob Bild oder Video
        if (this.data.mediaType === 'video' || this._isVideoFile(this.data.mediaFile)) {
            this._createVideo();
        } else {
            this._createImage();
        }
    }

    _createImage() {
        const image = this._make('img', [this.CSS.image], {
            src: this.data.mediaUrl,
            alt: this.data.mediaAlt || '' // Leerer Alt-Text als Standard
        });
        
        // Lightbox-Funktionalität
        if (this.data.lightbox && !this.readOnly) {
            image.style.cursor = 'pointer';
            image.addEventListener('click', (e) => {
                e.stopPropagation();
                this._openLightbox();
            });
        } else {
            // Klick zum Ändern (wenn nicht Lightbox)
            image.addEventListener('click', () => {
                if (!this.readOnly) {
                    this._openMediapool();
                }
            });
        }

        this.nodes.image = image;
        this.data.mediaType = 'image';
        
        // Alt-Text-Warnung prüfen und hinzufügen/entfernen
        this._updateAltWarning();
    }
    
    _createVideo() {
        const video = this._make('video', [this.CSS.video], {
            src: this.data.mediaUrl,
            controls: this.data.videoControls,
            autoplay: this.data.videoAutoplay,
            muted: this.data.videoMuted,
            loop: this.data.videoLoop
        });
        
        // Klick zum Ändern
        video.addEventListener('click', () => {
            if (!this.readOnly) {
                this._openMediapool();
            }
        });

        this.nodes.video = video;
        this.data.mediaType = 'video';
    }

    _createSelectButton() {
        const button = this._make('div', [this.CSS.button], {
            innerHTML: '<i class="fa-solid fa-plus"></i> Bild oder Video aus Medienpool wählen'
        });
        
        button.addEventListener('click', () => {
            this._openMediapool();
        });

        this.nodes.selectButton = button;
    }

    _openMediapool() {
        this.mediaTool.openMediaPool({
            types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'avi', 'mov'],
            context: 'editorjs_textimage'
        }, (mediaData) => {
            this._setMedia(mediaData);
        }).catch(error => {
            // Nur echte Fehler anzeigen, nicht wenn der User den Dialog abbricht
            if (error.message !== 'Media pool closed without selection') {
                console.error('Fehler bei der Medienauswahl:', error);
                if (window.EditorJSDebug) {
                    alert('Fehler beim Öffnen des Medienpools: ' + error.message);
                }
            }
            // Bei normalem Abbruch: stumm ignorieren
        });
    }

    _setMedia(mediaData) {
        // Daten aus dem MediaTool übernehmen
        this.data.mediaFile = mediaData.filename;
        this.data.mediaUrl = mediaData.url;
        this.data.mediaAlt = mediaData.alt;
        
        // Medientyp automatisch erkennen
        this.data.mediaType = this._isVideoFile(mediaData.filename) ? 'video' : 'image';

        // DOM aktualisieren - altes Media oder Button entfernen
        if (this.nodes.selectButton) {
            this.nodes.selectButton.remove();
            this.nodes.selectButton = null;
        }
        
        if (this.nodes.image) {
            this.nodes.image.remove();
            this.nodes.image = null;
        }
        
        if (this.nodes.video) {
            this.nodes.video.remove();
            this.nodes.video = null;
        }

        // Neues Media erstellen und einfügen
        this._createMedia();
        const mediaElement = this.nodes.image || this.nodes.video;
        this.nodes.imageWrapper.insertBefore(mediaElement, this.nodes.imageWrapper.firstChild);
        
        // Caption aktualisieren
        this._updateCaption();
    }

    _changeLayout(layout) {
        console.log('Changing layout from', this.data.layout, 'to:', layout);
        this.data.layout = layout;
        
        // Dataset aktualisieren
        this.nodes.holder.dataset.layout = layout;
        console.log('Dataset updated:', this.nodes.holder.dataset.layout);

        // CSS-Klassen für zusätzliche Sicherheit aktualisieren
        this.nodes.container.classList.remove('layout-left', 'layout-right', 'layout-top');
        this.nodes.container.classList.add('layout-' + layout, 'layout-' + layout); // Doppelt für höhere Spezifität
        
        // Force CSS reflow
        this.nodes.container.style.display = 'none';
        this.nodes.container.offsetHeight; // Trigger reflow
        this.nodes.container.style.display = '';
        
        console.log('Layout changed successfully to:', layout);
        console.log('Container classes:', this.nodes.container.className);
        console.log('Container data-layout:', this.nodes.holder.dataset.layout);
    }

    _editAltText() {
        const currentAlt = this.data.mediaAlt || '';
        const newAlt = prompt('Alt-Text für Barrierefreiheit eingeben:\n(Beschreibt das Bild für Screenreader)', currentAlt);
        
        if (newAlt !== null) { // null bedeutet Abbruch
            this.data.mediaAlt = newAlt;
            // Img-Tag Alt-Attribut aktualisieren
            if (this.nodes.image) {
                this.nodes.image.alt = this.data.mediaAlt;
            }
            // Alt-Warnung aktualisieren
            this._updateAltWarning();
        }
    }
    
    _toggleLightbox() {
        this.data.lightbox = !this.data.lightbox;
        
        // Image-Event-Listener aktualisieren
        if (this.nodes.image && this.data.mediaType === 'image') {
            // Alte Event-Listener entfernen und neue hinzufügen
            const newImage = this.nodes.image.cloneNode(true);
            
            if (this.data.lightbox && !this.readOnly) {
                newImage.style.cursor = 'pointer';
                newImage.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this._openLightbox();
                });
            } else {
                newImage.style.cursor = 'default';
                newImage.addEventListener('click', () => {
                    if (!this.readOnly) {
                        this._openMediapool();
                    }
                });
            }
            
            this.nodes.image.replaceWith(newImage);
            this.nodes.image = newImage;
        }
    }
    
    _openLightbox() {
        if (this.data.mediaType !== 'image' || !this.data.mediaUrl) return;

        // Lightbox Overlay erstellen
        const overlay = this._make('div', [this.CSS.lightboxOverlay], {
            style: 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; display: flex; align-items: center; justify-content: center; cursor: pointer;'
        });

        const image = this._make('img', [this.CSS.lightboxImage], {
            src: this.data.mediaUrl,
            alt: this.data.mediaAlt || '',
            style: 'max-width: 90%; max-height: 90%; object-fit: contain; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);'
        });

        overlay.appendChild(image);
        document.body.appendChild(overlay);

        // Schließen bei Klick auf Overlay
        overlay.addEventListener('click', () => {
            document.body.removeChild(overlay);
        });

        // Schließen mit Escape-Taste
        const closeHandler = (e) => {
            if (e.key === 'Escape') {
                document.body.removeChild(overlay);
                document.removeEventListener('keydown', closeHandler);
            }
        };
        document.addEventListener('keydown', closeHandler);
    }
    
    _showVideoOptions() {
        // Einfache Video-Optionen über Confirm-Dialoge
        // In einer erweiterten Version könnte hier ein modaler Dialog verwendet werden
        if (confirm('Autoplay aktivieren?')) {
            this.data.videoAutoplay = !this.data.videoAutoplay;
        }
        
        if (confirm('Video stumm starten?')) {
            this.data.videoMuted = !this.data.videoMuted;
        }
        
        if (confirm('Video in Endlosschleife?')) {
            this.data.videoLoop = !this.data.videoLoop;
        }
        
        // Video-Attribute aktualisieren
        if (this.nodes.video) {
            this.nodes.video.autoplay = this.data.videoAutoplay;
            this.nodes.video.muted = this.data.videoMuted;
            this.nodes.video.loop = this.data.videoLoop;
        }
    }
    
    _isVideoFile(filename) {
        if (!filename) return false;
        const videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv', 'flv', 'm4v'];
        const extension = filename.toLowerCase().split('.').pop();
        return videoExtensions.includes(extension);
    }
    
    _editCaption() {
        const currentCaption = this.data.caption || '';
        const newCaption = prompt('Bildunterschrift eingeben:\n(Wird unter dem Bild angezeigt)', currentCaption);
        
        if (newCaption !== null) { // null bedeutet Abbruch
            this.data.caption = newCaption;
            this._updateCaption();
        }
    }
    
    _updateCaption() {
        // Alte Caption entfernen falls vorhanden
        if (this.nodes.caption) {
            this.nodes.caption.remove();
            this.nodes.caption = null;
        }
        
        // Neue Caption hinzufügen falls Text vorhanden
        if (this.data.caption && this.data.caption.trim()) {
            const caption = this._make('div', [this.CSS.caption], {
                innerHTML: this.data.caption
            });
            this.nodes.imageWrapper.appendChild(caption);
            this.nodes.caption = caption;
        }
    }

    _handleEnter(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            // Enter ohne Shift: Zeilenumbruch einfügen
            event.preventDefault();
            event.stopPropagation();
            
            // BR-Element an Cursor-Position einfügen
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
        return true;
    }

    _make(tagName, classNames = null, attributes = {}) {
        const el = document.createElement(tagName);

        if (Array.isArray(classNames)) {
            el.classList.add(...classNames);
        } else if (classNames) {
            el.classList.add(classNames);
        }

        for (let attrName in attributes) {
            el[attrName] = attributes[attrName];
        }

        return el;
    }

    /**
     * Prüft ob Alt-Text vorhanden ist und zeigt ggf. Warnsymbol
     */
    _updateAltWarning() {
        // Altes Warnsymbol entfernen falls vorhanden
        if (this.nodes.altWarning) {
            this.nodes.altWarning.remove();
            this.nodes.altWarning = null;
        }
        
        // Prüfen ob Alt-Text fehlt (leer oder nur Whitespace) - nur bei Bildern
        const altText = this.data.mediaAlt || '';
        const hasAltText = altText.trim().length > 0;
        
        // Warnsymbol nur anzeigen wenn Alt-Text fehlt und nicht im ReadOnly-Modus und es ein Bild ist
        if (!hasAltText && !this.readOnly && this.data.mediaType === 'image' && this.nodes.image && this.nodes.imageWrapper) {
            this.nodes.altWarning = this._make('div', [this.CSS.altWarning], {
                innerHTML: `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L1 21H23L12 2Z" fill="#ff9500" stroke="#fff" stroke-width="2"/>
                        <path d="M12 9V13" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="12" cy="17" r="1" fill="#fff"/>
                    </svg>
                `,
                title: 'Warnung: Kein Alt-Text vorhanden. Für bessere Barrierefreiheit sollten Sie einen beschreibenden Alt-Text hinzufügen.'
            });
            
            // Warnsymbol über dem Bild positionieren
            this.nodes.imageWrapper.appendChild(this.nodes.altWarning);
        }
    }
}

// Export für das Bundle
window.TextImageBlock = TextImageBlock;
