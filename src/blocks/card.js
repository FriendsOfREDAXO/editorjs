/**
 * Card Block für EditorJS
 * Ein vielseitiger Block mit Bild/Video, Titel, Text und Grid-Layout-Optionen
 * Unterstützt Lightbox für Bilder und Video-Vergrößerung
 */
class CardBlock {
    static get toolbox() {
        return {
            title: 'Karte',
            icon: '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" fill="none" stroke-width="2"/><rect x="7" y="7" width="10" height="6" rx="1" fill="currentColor" opacity="0.3"/><line x1="7" y1="16" x2="13" y2="16" stroke="currentColor" stroke-width="2"/><line x1="7" y1="19" x2="17" y2="19" stroke="currentColor" stroke-width="2"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    /**
     * Inline-Toolbar für Rich-Text-Editing aktivieren
     */
    static get enableLineBreaks() {
        return true;
    }

    /**
     * Inline-Tools für Rich-Text-Formatierung
     */
    static get inlineToolbar() {
        return ['Marker', 'inlineCode', 'rexLink']; // Verwende REDAXO Link-Tool
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
        
        // REXMediaTool für Bilder und Videos
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'avi', 'mov'], 
                context: 'editorjs_card'
            }
        });
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-card',
            container: 'cdx-card__container',
            mediaWrapper: 'cdx-card__media-wrapper',
            media: 'cdx-card__media',
            image: 'cdx-card__image',
            video: 'cdx-card__video',
            contentWrapper: 'cdx-card__content-wrapper',
            title: 'cdx-card__title',
            text: 'cdx-card__text',
            button: 'cdx-card__button',
            settingsButton: 'cdx-card__settings-button',
            settingsButtonActive: 'cdx-card__settings-button--active',
            lightboxOverlay: 'cdx-card__lightbox-overlay',
            lightboxImage: 'cdx-card__lightbox-image',
            lightboxVideo: 'cdx-card__lightbox-video'
        };

        this.nodes = {
            holder: null,
            container: null,
            mediaWrapper: null,
            media: null,
            contentWrapper: null,
            title: null,
            text: null,
            selectButton: null
        };

        this.data = {
            title: data.title || '',
            text: data.text || '',
            mediaFile: data.mediaFile || '',
            mediaUrl: data.mediaUrl || '',
            mediaType: data.mediaType || '', // 'image' or 'video'
            mediaAlt: data.mediaAlt || '',
            layout: data.layout || 'vertical', // vertical, horizontal, grid
            gridColumns: data.gridColumns || 2, // 1-4 columns for grid layout
            aspectRatio: data.aspectRatio || 'auto', // auto, 16-9, 4-3, 1-1
            lightbox: data.lightbox !== undefined ? data.lightbox : true, // Lightbox für Bilder
            videoAutoplay: data.videoAutoplay !== undefined ? data.videoAutoplay : false,
            videoMuted: data.videoMuted !== undefined ? data.videoMuted : false,
            videoLoop: data.videoLoop !== undefined ? data.videoLoop : false,
            videoControls: data.videoControls !== undefined ? data.videoControls : true,
            linkUrl: data.linkUrl || '', // Interne Verlinkung
            linkTarget: data.linkTarget || '_self'
        };

        this.layouts = {
            vertical: { title: 'Vertikal', icon: '<i class="fa-solid fa-grip-lines"></i>' },
            horizontal: { title: 'Horizontal', icon: '<i class="fa-solid fa-grip-lines-vertical"></i>' },
            grid: { title: 'Kachel', icon: '<i class="fa-solid fa-th"></i>' }
        };

        this.aspectRatios = {
            'auto': { title: 'Automatisch', icon: '<i class="fa-solid fa-expand-arrows-alt"></i>' },
            '16-9': { title: '16:9', icon: '<i class="fa-solid fa-tv"></i>' },
            '4-3': { title: '4:3', icon: '<i class="fa-solid fa-image"></i>' },
            '1-1': { title: '1:1', icon: '<i class="fa-solid fa-square"></i>' }
        };
    }

    render() {
        const holder = this._make('div', [this.CSS.baseClass, this.CSS.wrapper]);
        const container = this._make('div', [this.CSS.container]);
        
        holder.dataset.layout = this.data.layout;
        holder.dataset.gridColumns = this.data.gridColumns;
        holder.dataset.aspectRatio = this.data.aspectRatio;
        holder.appendChild(container);

        // Media Wrapper
        const mediaWrapper = this._make('div', [this.CSS.mediaWrapper]);
        
        if (this.data.mediaUrl) {
            this._createMedia();
            mediaWrapper.appendChild(this.nodes.media);
        } else {
            this._createSelectButton();
            mediaWrapper.appendChild(this.nodes.selectButton);
        }

        // Content Wrapper
        const contentWrapper = this._make('div', [this.CSS.contentWrapper]);
        
        // Title
        const title = this._make('div', [this.CSS.title], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.title || 'Titel eingeben...'
        });
        
        if (!this.readOnly) {
            title.addEventListener('keydown', this._handleEnter.bind(this));
        }

        // Text
        const text = this._make('div', [this.CSS.text], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.text || 'Text eingeben...'
        });
        
        if (!this.readOnly) {
            text.addEventListener('keydown', this._handleEnter.bind(this));
        }

        contentWrapper.appendChild(title);
        contentWrapper.appendChild(text);

        // Layout-spezifische Anordnung
        if (this.data.layout === 'horizontal') {
            container.appendChild(mediaWrapper);
            container.appendChild(contentWrapper);
        } else {
            // vertical und grid: Media oben, Content unten
            container.appendChild(mediaWrapper);
            container.appendChild(contentWrapper);
        }

        this.nodes.holder = holder;
        this.nodes.container = container;
        this.nodes.mediaWrapper = mediaWrapper;
        this.nodes.contentWrapper = contentWrapper;
        this.nodes.title = title;
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

        // Grid Columns (nur bei grid layout)
        if (this.data.layout === 'grid') {
            for (let i = 1; i <= 4; i++) {
                const colButton = this._make('span', [this.CSS.settingsButton], {
                    innerHTML: `<i class="fa-solid fa-th"></i> ${i}`,
                    title: `${i} Spalten`
                });

                colButton.addEventListener('click', () => {
                    this._changeGridColumns(i);
                    
                    // Update active state for column buttons
                    wrapper.querySelectorAll('.' + this.CSS.settingsButton).forEach(btn => {
                        if (btn.innerHTML.includes('fa-th')) {
                            btn.classList.remove(this.CSS.settingsButtonActive);
                        }
                    });
                    colButton.classList.add(this.CSS.settingsButtonActive);
                });

                if (i === this.data.gridColumns) {
                    colButton.classList.add(this.CSS.settingsButtonActive);
                }

                wrapper.appendChild(colButton);
            }
        }

        // Aspect Ratio
        Object.entries(this.aspectRatios).forEach(([ratio, config]) => {
            const button = this._make('span', [this.CSS.settingsButton], {
                innerHTML: config.icon,
                title: config.title
            });

            button.addEventListener('click', () => {
                this._changeAspectRatio(ratio);
            });

            if (ratio === this.data.aspectRatio) {
                button.classList.add(this.CSS.settingsButtonActive);
            }

            wrapper.appendChild(button);
        });

        // Lightbox Toggle (nur bei Bildern)
        if (this.data.mediaType === 'image') {
            const lightboxButton = this._make('span', [this.CSS.settingsButton], {
                innerHTML: '<i class="fa-solid fa-search-plus"></i>',
                title: 'Lightbox aktivieren'
            });
            
            lightboxButton.addEventListener('click', () => {
                this._toggleLightbox();
                lightboxButton.classList.toggle(this.CSS.settingsButtonActive, this.data.lightbox);
            });

            if (this.data.lightbox) {
                lightboxButton.classList.add(this.CSS.settingsButtonActive);
            }

            wrapper.appendChild(lightboxButton);
        }

        // Video-Optionen (nur bei Videos)
        if (this.data.mediaType === 'video') {
            const autoplayButton = this._make('span', [this.CSS.settingsButton], {
                innerHTML: '<i class="fa-solid fa-play"></i>',
                title: 'Autoplay'
            });
            
            autoplayButton.addEventListener('click', () => {
                this._toggleVideoOption('videoAutoplay');
                autoplayButton.classList.toggle(this.CSS.settingsButtonActive, this.data.videoAutoplay);
            });

            if (this.data.videoAutoplay) {
                autoplayButton.classList.add(this.CSS.settingsButtonActive);
            }

            wrapper.appendChild(autoplayButton);
        }

        // Media ändern Button
        const changeMediaButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-image"></i>',
            title: 'Media aus Medienpool wählen'
        });
        
        changeMediaButton.addEventListener('click', () => {
            this._openMediapool();
        });

        // Link bearbeiten Button
        const linkButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-link"></i>',
            title: 'Link bearbeiten'
        });
        
        linkButton.addEventListener('click', () => {
            this._editLink();
        });

        if (this.data.linkUrl) {
            linkButton.classList.add(this.CSS.settingsButtonActive);
        }

        wrapper.appendChild(changeMediaButton);
        wrapper.appendChild(linkButton);

        return wrapper;
    }

    save(blockContent) {
        const title = blockContent.querySelector('.' + this.CSS.title);
        const text = blockContent.querySelector('.' + this.CSS.text);

        return {
            title: title.innerHTML,
            text: text.innerHTML,
            mediaFile: this.data.mediaFile,
            mediaUrl: this.data.mediaUrl,
            mediaType: this.data.mediaType,
            mediaAlt: this.data.mediaAlt,
            layout: this.data.layout,
            gridColumns: this.data.gridColumns,
            aspectRatio: this.data.aspectRatio,
            lightbox: this.data.lightbox,
            videoAutoplay: this.data.videoAutoplay,
            videoMuted: this.data.videoMuted,
            videoLoop: this.data.videoLoop,
            videoControls: this.data.videoControls,
            linkUrl: this.data.linkUrl,
            linkTarget: this.data.linkTarget
        };
    }

    static get sanitize() {
        return {
            title: {
                br: true,
                strong: true,
                em: true,
                u: true,
                s: true,
                a: {
                    href: true,
                    target: '_blank'
                }
            },
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
                ul: true,
                ol: true,
                li: true
            },
            mediaFile: {},
            mediaUrl: {},
            mediaType: {},
            mediaAlt: {},
            layout: {},
            gridColumns: {},
            aspectRatio: {},
            lightbox: {},
            videoAutoplay: {},
            videoMuted: {},
            videoLoop: {},
            videoControls: {},
            linkUrl: {},
            linkTarget: {}
        };
    }

    _createMedia() {
        if (this.data.mediaType === 'video' || this._isVideoFile(this.data.mediaFile)) {
            this._createVideo();
        } else {
            this._createImage();
        }
    }

    _createImage() {
        const image = this._make('img', [this.CSS.image, this.CSS.media], {
            src: this.data.mediaUrl,
            alt: this.data.mediaAlt || this.data.title || ''
        });
        
        // Lightbox-Funktionalität
        if (this.data.lightbox && !this.readOnly) {
            image.style.cursor = 'pointer';
            image.addEventListener('click', (e) => {
                e.stopPropagation();
                this._openLightbox();
            });
        }

        // Klick zum Ändern (wenn nicht Lightbox)
        if (!this.data.lightbox) {
            image.addEventListener('click', () => {
                if (!this.readOnly) {
                    this._openMediapool();
                }
            });
        }

        this.nodes.media = image;
        this.data.mediaType = 'image';
    }

    _createVideo() {
        const video = this._make('video', [this.CSS.video, this.CSS.media], {
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

        this.nodes.media = video;
        this.data.mediaType = 'video';
    }

    _createSelectButton() {
        const button = this._make('div', [this.CSS.button], {
            innerHTML: '<i class="fa-solid fa-plus"></i> Media aus Medienpool wählen'
        });
        
        button.addEventListener('click', () => {
            this._openMediapool();
        });

        this.nodes.selectButton = button;
    }

    _openMediapool() {
        this.mediaTool.selectImage((mediaData) => {
            this._setMedia(mediaData);
        }).catch(error => {
            if (error.message !== 'Media pool closed without selection') {
                console.error('Fehler bei der Medienauswahl:', error);
                if (window.EditorJSDebug) {
                    alert('Fehler beim Öffnen des Medienpools: ' + error.message);
                }
            }
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
        
        if (this.nodes.media) {
            this.nodes.media.remove();
            this.nodes.media = null;
        }

        // Neues Media erstellen und einfügen
        this._createMedia();
        this.nodes.mediaWrapper.insertBefore(this.nodes.media, this.nodes.mediaWrapper.firstChild);
    }

    _changeLayout(layout) {
        this.data.layout = layout;
        this.nodes.holder.dataset.layout = layout;
        
        // Bei Grid-Layout auch Spalten-Buttons anzeigen
        if (layout === 'grid') {
            this.nodes.holder.dataset.gridColumns = this.data.gridColumns;
        }
    }

    _changeGridColumns(columns) {
        this.data.gridColumns = columns;
        this.nodes.holder.dataset.gridColumns = columns;
    }

    _changeAspectRatio(ratio) {
        this.data.aspectRatio = ratio;
        this.nodes.holder.dataset.aspectRatio = ratio;
    }

    _toggleLightbox() {
        this.data.lightbox = !this.data.lightbox;
        
        // Image-Event-Listener aktualisieren
        if (this.nodes.media && this.data.mediaType === 'image') {
            // Alte Event-Listener entfernen und neue hinzufügen
            const newImage = this.nodes.media.cloneNode(true);
            
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
            
            this.nodes.media.replaceWith(newImage);
            this.nodes.media = newImage;
        }
    }

    _toggleVideoOption(option) {
        this.data[option] = !this.data[option];
        
        // Video-Attribute aktualisieren
        if (this.nodes.media && this.data.mediaType === 'video') {
            if (option === 'videoAutoplay') this.nodes.media.autoplay = this.data.videoAutoplay;
            if (option === 'videoMuted') this.nodes.media.muted = this.data.videoMuted;
            if (option === 'videoLoop') this.nodes.media.loop = this.data.videoLoop;
            if (option === 'videoControls') this.nodes.media.controls = this.data.videoControls;
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
            alt: this.data.mediaAlt || this.data.title || '',
            style: 'max-width: 90%; max-height: 90%; object-fit: contain;'
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

    _editLink() {
        // Verwende REXLinkTool für interne Verlinkungen
        if (typeof REXLinkTool !== 'undefined') {
            const linkTool = new REXLinkTool({ api: this.api });
            linkTool.showLinkDialog((linkData) => {
                this.data.linkUrl = linkData.url;
                this.data.linkTarget = linkData.target || '_self';
            });
        } else {
            // Fallback: einfacher Prompt
            const newUrl = prompt('Link-URL eingeben:', this.data.linkUrl);
            if (newUrl !== null) {
                this.data.linkUrl = newUrl;
            }
        }
    }

    _isVideoFile(filename) {
        if (!filename) return false;
        const videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv', 'flv', 'm4v'];
        const extension = filename.toLowerCase().split('.').pop();
        return videoExtensions.includes(extension);
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
            if (attrName === 'style') {
                el.style.cssText = attributes[attrName];
            } else {
                el[attrName] = attributes[attrName];
            }
        }

        return el;
    }
}

// Export für das Bundle
window.CardBlock = CardBlock;