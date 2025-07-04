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
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-textimage',
            container: 'cdx-textimage__container',
            imageWrapper: 'cdx-textimage__image-wrapper',
            image: 'cdx-textimage__image',
            textWrapper: 'cdx-textimage__text-wrapper',
            text: 'cdx-textimage__text',
            button: 'cdx-textimage__button',
            caption: 'cdx-textimage__caption',
            settingsButton: 'cdx-textimage__settings-button',
            settingsButtonActive: 'cdx-textimage__settings-button--active'
        };

        this.nodes = {
            holder: null,
            container: null,
            imageWrapper: null,
            image: null,
            textWrapper: null,
            text: null,
            caption: null,
            selectButton: null
        };

        this.data = {
            text: data.text || '',
            imageFile: data.imageFile || '',
            imageUrl: data.imageUrl || '',
            imageAlt: data.imageAlt || '',
            caption: data.caption || '',
            layout: data.layout || 'left', // left, right, top
            stretched: data.stretched || false
        };

        this.layouts = {
            left: {
                title: 'Bild links',
                icon: '⬅️'
            },
            right: {
                title: 'Bild rechts', 
                icon: '➡️'
            },
            top: {
                title: 'Bild oben',
                icon: '⬆️'
            }
        };
    }

    render() {
        const holder = this._make('div', [this.CSS.baseClass, this.CSS.wrapper]);
        const container = this._make('div', [this.CSS.container]);
        
        holder.dataset.layout = this.data.layout;
        holder.appendChild(container);

        // Image Wrapper
        const imageWrapper = this._make('div', [this.CSS.imageWrapper]);
        
        if (this.data.imageUrl) {
            this._createImage();
        } else {
            this._createSelectButton();
        }
        
        imageWrapper.appendChild(this.nodes.image || this.nodes.selectButton);
        
        // Caption
        const caption = this._make('div', [this.CSS.caption], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.caption || 'Bildunterschrift...'
        });
        imageWrapper.appendChild(caption);

        // Text Wrapper
        const textWrapper = this._make('div', [this.CSS.textWrapper]);
        const text = this._make('div', [this.CSS.text], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.text || 'Geben Sie hier Ihren Text ein...'
        });
        
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
        this.nodes.caption = caption;

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
            innerHTML: '🖼️ Bild ändern',
            title: 'Bild aus Medienpool wählen'
        });
        
        changeImageButton.addEventListener('click', () => {
            this._openMediapool();
        });
        
        wrapper.appendChild(changeImageButton);

        return wrapper;
    }

    save(blockContent) {
        const text = blockContent.querySelector('.' + this.CSS.text);
        const caption = blockContent.querySelector('.' + this.CSS.caption);

        return {
            text: text.innerHTML,
            imageFile: this.data.imageFile,
            imageUrl: this.data.imageUrl,
            imageAlt: this.data.imageAlt,
            caption: caption.innerHTML,
            layout: this.data.layout,
            stretched: this.data.stretched
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
            imageFile: {},
            imageUrl: {},
            imageAlt: {},
            layout: {},
            stretched: {}
        };
    }

    _createImage() {
        const image = this._make('img', [this.CSS.image], {
            src: this.data.imageUrl,
            alt: this.data.imageAlt || this.data.imageFile
        });
        
        // Klick zum Ändern
        image.addEventListener('click', () => {
            if (!this.readOnly) {
                this._openMediapool();
            }
        });

        this.nodes.image = image;
    }

    _createSelectButton() {
        const button = this._make('div', [this.CSS.button], {
            innerHTML: '🖼️ Bild aus Medienpool wählen'
        });
        
        button.addEventListener('click', () => {
            this._openMediapool();
        });

        this.nodes.selectButton = button;
    }

    _openMediapool() {
        if (typeof openMediaPool === 'undefined') {
            alert('Medienpool ist nicht verfügbar');
            return;
        }

        const self = this;
        let params = 'editorjs_textimage';
        
        // Nur Bildtypen zulassen (wenn verfügbar)
        if (typeof rex !== 'undefined' && rex.editorjs_rex_media_getImageTypes) {
            params += '&args[types]=' + rex.editorjs_rex_media_getImageTypes.join(',');
        }

        const mediaPool = openMediaPool(params);
        
        // Event Listener für die Medienauswahl
        if (typeof $ !== 'undefined') {
            // jQuery Version (falls verfügbar)
            $(mediaPool).on('rex:selectMedia', function(event, filename) {
                event.preventDefault();
                mediaPool.close();
                self._setImage(filename);
            });
        } else {
            // Vanilla JS Fallback
            mediaPool.addEventListener('rex:selectMedia', function(event) {
                event.preventDefault();
                const filename = event.detail || event.filename;
                mediaPool.close();
                self._setImage(filename);
            });
        }
    }

    _setImage(filename) {
        this.data.imageFile = filename;
        
        // URL erstellen
        let imageUrl = '/media/' + filename;
        if (typeof rex !== 'undefined' && rex.editorjs_imageUrlPath) {
            imageUrl = rex.editorjs_imageUrlPath + filename;
        }
        
        this.data.imageUrl = imageUrl;
        this.data.imageAlt = filename;

        // DOM aktualisieren - altes Bild oder Button entfernen
        if (this.nodes.selectButton) {
            this.nodes.selectButton.remove();
            this.nodes.selectButton = null;
        }
        
        if (this.nodes.image) {
            this.nodes.image.remove();
            this.nodes.image = null;
        }

        // Neues Bild erstellen und einfügen
        this._createImage();
        this.nodes.imageWrapper.insertBefore(this.nodes.image, this.nodes.caption);
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
}

// Export für das Bundle
window.TextImageBlock = TextImageBlock;
