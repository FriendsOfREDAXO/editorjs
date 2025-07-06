/**
 * Hero Block für EditorJS
 * Ein moderner Hero-Block mit Hintergrundbild, Titel, Untertitel, Text und Call-to-Action Button
 */
export default class Hero {
    static get toolbox() {
        return {
            title: 'Hero Section',
            icon: '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/><circle cx="8" cy="8" r="2" fill="currentColor"/><path d="M12 12l2-2 4 4H6l3-3z" fill="currentColor"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    static get enableLineBreaks() {
        return true;
    }

    static get inlineToolbar() {
        return ['Marker', 'inlineCode'];
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        this.config = config || {};
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-hero',
            container: 'cdx-hero__container',
            backgroundImage: 'cdx-hero__background',
            overlay: 'cdx-hero__overlay',
            content: 'cdx-hero__content',
            title: 'cdx-hero__title',
            subtitle: 'cdx-hero__subtitle',
            text: 'cdx-hero__text',
            actions: 'cdx-hero__actions',
            button: 'cdx-hero__button',
            buttonSecondary: 'cdx-hero__button--secondary',
            imageUpload: 'cdx-hero__image-upload',
            imagePreview: 'cdx-hero__image-preview',
            removeImage: 'cdx-hero__remove-image',
            settingsWrapper: 'cdx-hero__settings'
        };

        this.nodes = {
            holder: null,
            backgroundImage: null,
            title: null,
            subtitle: null,
            text: null,
            primaryButton: null,
            secondaryButton: null
        };

        this.data = {
            title: data.title || 'Ihr Titel hier',
            subtitle: data.subtitle || 'Ein aussagekräftiger Untertitel',
            text: data.text || 'Beschreibender Text für Ihren Hero-Bereich. Hier können Sie Ihre wichtigste Botschaft vermitteln.',
            backgroundImage: data.backgroundImage || '',
            backgroundImageFile: data.backgroundImageFile || '',
            backgroundImageAlt: data.backgroundImageAlt || '',
            backgroundColor: data.backgroundColor || 'gradient',
            primaryButton: data.primaryButton || { text: 'Jetzt starten', url: '#' },
            secondaryButton: data.secondaryButton || { text: 'Mehr erfahren', url: '#' },
            height: data.height || 'medium',
            overlay: data.overlay || 'dark',
            alignment: data.alignment || 'center'
        };

        // REXMediaTool für Medienpool-Integration
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], // Nur Bilder
                context: 'editorjs_hero'
            }
        });

        this.settings = [
            {
                name: 'height',
                icon: '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>',
                title: 'Höhe'
            },
            {
                name: 'backgroundColor',
                icon: '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-5.52-4.48-10-10-10z"/><circle cx="6.5" cy="11.5" r="1.5"/><circle cx="9.5" cy="7.5" r="1.5"/><circle cx="14.5" cy="7.5" r="1.5"/><circle cx="17.5" cy="11.5" r="1.5"/></svg>',
                title: 'Hintergrundfarbe'
            },
            {
                name: 'overlay',
                icon: '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
                title: 'Overlay'
            },
            {
                name: 'alignment',
                icon: '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M3 21h18v-2H3v2zm0-4h18v-2H3v2zm0-4h18v-2H3v2zm0-4h18V7H3v2zm0-6v2h18V3H3z"/></svg>',
                title: 'Ausrichtung'
            }
        ];
    }

    render() {
        this.nodes.holder = this._make('div', [this.CSS.wrapper, this.CSS.baseClass]);
        
        this.nodes.holder.innerHTML = `
            <div class="${this.CSS.container}" data-height="${this.data.height}" data-alignment="${this.data.alignment}" data-background="${this.data.backgroundColor}">
                <div class="${this.CSS.backgroundImage}">
                    ${this.data.backgroundImage ? `<img src="${this.data.backgroundImage}" alt="${this.data.backgroundImageAlt || this.data.backgroundImageFile || 'Hero Background'}">` : ''}
                </div>
                <div class="${this.CSS.overlay}" data-overlay="${this.data.overlay}"></div>
                <div class="${this.CSS.content}">
                    <h1 class="${this.CSS.title}" contenteditable="${!this.readOnly}" data-placeholder="Titel eingeben...">${this.data.title}</h1>
                    <h2 class="${this.CSS.subtitle}" contenteditable="${!this.readOnly}" data-placeholder="Untertitel eingeben...">${this.data.subtitle}</h2>
                    <div class="${this.CSS.text}" contenteditable="${!this.readOnly}" data-placeholder="Beschreibungstext eingeben...">${this.data.text}</div>
                    <div class="${this.CSS.actions}">
                        <div class="${this.CSS.button}" data-button="primary">
                            <span contenteditable="${!this.readOnly}" data-placeholder="Button Text">${this.data.primaryButton.text}</span>
                        </div>
                        <div class="${this.CSS.button} ${this.CSS.buttonSecondary}" data-button="secondary">
                            <span contenteditable="${!this.readOnly}" data-placeholder="Button Text">${this.data.secondaryButton.text}</span>
                        </div>
                    </div>
                </div>
                ${!this.readOnly ? this._renderImageUpload() : ''}
            </div>
        `;

        this._bindEvents();
        return this.nodes.holder;
    }

    _renderImageUpload() {
        const hasImage = this.data.backgroundImage;
        
        return `
            <div class="${this.CSS.imageUpload}">
                ${hasImage ? `
                    <div class="${this.CSS.imagePreview}">
                        <button class="${this.CSS.removeImage}" type="button">
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                ` : `
                    <div class="cdx-hero__upload-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21,15 16,10 5,21"/>
                        </svg>
                        <p>Hintergrundbild hinzufügen</p>
                        <small>Aus dem REDAXO Medienpool auswählen</small>
                    </div>
                `}
            </div>
        `;
    }

    _bindEvents() {
        const container = this.nodes.holder.querySelector(`.${this.CSS.container}`);
        
        // Title events
        const title = container.querySelector(`.${this.CSS.title}`);
        title.addEventListener('blur', () => {
            this.data.title = title.innerHTML;
        });

        // Subtitle events
        const subtitle = container.querySelector(`.${this.CSS.subtitle}`);
        subtitle.addEventListener('blur', () => {
            this.data.subtitle = subtitle.innerHTML;
        });

        // Text events
        const text = container.querySelector(`.${this.CSS.text}`);
        text.addEventListener('blur', () => {
            this.data.text = text.innerHTML;
        });

        // Button events
        const buttons = container.querySelectorAll(`[data-button]`);
        buttons.forEach(button => {
            const span = button.querySelector('span');
            const type = button.dataset.button;
            
            span.addEventListener('blur', () => {
                this.data[type + 'Button'].text = span.innerHTML;
            });

            // Button URL editing on double click
            button.addEventListener('dblclick', (e) => {
                e.preventDefault();
                this._editButtonUrl(type);
            });
        });

        // Image upload events
        if (!this.readOnly) {
            const imageUpload = container.querySelector(`.${this.CSS.imageUpload}`);
            const removeButton = container.querySelector(`.${this.CSS.removeImage}`);
            
            imageUpload.addEventListener('click', (e) => {
                if (!e.target.closest(`.${this.CSS.removeImage}`)) {
                    this._selectImage();
                }
            });

            if (removeButton) {
                removeButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this._removeImage();
                });
            }
        }
    }

    _editButtonUrl(buttonType) {
        const currentUrl = this.data[buttonType + 'Button'].url;
        const newUrl = prompt(`URL für ${buttonType === 'primary' ? 'primären' : 'sekundären'} Button:`, currentUrl);
        
        if (newUrl !== null) {
            this.data[buttonType + 'Button'].url = newUrl;
        }
    }

    _selectImage() {
        this.mediaTool.selectImage((mediaData) => {
            this._setImage(mediaData);
        }).catch(error => {
            console.error('Fehler bei der Medienauswahl:', error);
            alert('Fehler beim Öffnen des Medienpools: ' + error.message);
        });
    }

    _setImage(mediaData) {
        // Daten aus dem MediaTool übernehmen
        this.data.backgroundImage = mediaData.url;
        this.data.backgroundImageFile = mediaData.filename;
        this.data.backgroundImageAlt = mediaData.alt;

        // DOM aktualisieren
        this._updateImageDisplay();
    }

    _removeImage() {
        this.data.backgroundImage = '';
        this._updateImageDisplay();
    }

    _updateImageDisplay() {
        const container = this.nodes.holder.querySelector(`.${this.CSS.container}`);
        const backgroundImage = container.querySelector(`.${this.CSS.backgroundImage}`);
        const imageUpload = container.querySelector(`.${this.CSS.imageUpload}`);
        
        if (this.data.backgroundImage) {
            backgroundImage.innerHTML = `<img src="${this.data.backgroundImage}" alt="${this.data.backgroundImageAlt || this.data.backgroundImageFile || 'Hero Background'}">`;
        } else {
            backgroundImage.innerHTML = '';
        }
        
        imageUpload.innerHTML = this._renderImageUpload().match(/<div class="cdx-hero__image-upload"[^>]*>(.*?)<\/div>/s)[1];
        this._bindEvents(); // Re-bind events after DOM update
    }

    renderSettings() {
        const wrapper = this._make('div', [this.CSS.settingsWrapper]);

        this.settings.forEach(tune => {
            const button = this._make('div', [this.api.styles.settingsButton], {
                innerHTML: tune.icon,
                title: tune.title
            });

            button.addEventListener('click', () => {
                this._toggleTune(tune.name);
                button.classList.toggle(this.api.styles.settingsButtonActive);
            });

            if (this.data[tune.name] && tune.name !== 'height' && tune.name !== 'overlay' && tune.name !== 'alignment') {
                button.classList.add(this.api.styles.settingsButtonActive);
            }

            wrapper.appendChild(button);
        });

        return wrapper;
    }

    _toggleTune(tuneName) {
        const tuneOptions = {
            height: ['small', 'medium', 'large', 'fullscreen'],
            backgroundColor: ['gradient', 'blue', 'purple', 'green', 'red', 'dark', 'light'],
            overlay: ['none', 'light', 'dark', 'gradient'],
            alignment: ['left', 'center', 'right']
        };

        if (tuneOptions[tuneName]) {
            const currentIndex = tuneOptions[tuneName].indexOf(this.data[tuneName]);
            const nextIndex = (currentIndex + 1) % tuneOptions[tuneName].length;
            this.data[tuneName] = tuneOptions[tuneName][nextIndex];
            
            const container = this.nodes.holder.querySelector(`.${this.CSS.container}`);
            if (tuneName === 'height' || tuneName === 'alignment') {
                container.setAttribute(`data-${tuneName}`, this.data[tuneName]);
            } else if (tuneName === 'backgroundColor') {
                container.setAttribute('data-background', this.data[tuneName]);
            } else if (tuneName === 'overlay') {
                const overlay = container.querySelector(`.${this.CSS.overlay}`);
                overlay.setAttribute('data-overlay', this.data[tuneName]);
            }
        }
    }

    save() {
        return this.data;
    }

    validate(savedData) {
        return savedData.title && savedData.title.trim() !== '';
    }

    static get sanitize() {
        return {
            title: {
                b: true,
                i: true,
                u: true,
                s: true,
                mark: true,
                code: true,
                kbd: true
            },
            subtitle: {
                b: true,
                i: true,
                u: true,
                s: true,
                mark: true,
                code: true,
                kbd: true
            },
            text: {
                b: true,
                i: true,
                u: true,
                s: true,
                mark: true,
                code: true,
                kbd: true,
                a: true,
                br: true,
                p: true
            },
            backgroundImage: {},
            backgroundImageFile: {},
            backgroundImageAlt: {},
            backgroundColor: {},
            primaryButton: {},
            secondaryButton: {},
            height: {},
            overlay: {},
            alignment: {}
        };
    }

    _make(tagName, classNames = null, attributes = {}) {
        const el = document.createElement(tagName);

        if (Array.isArray(classNames)) {
            el.classList.add(...classNames);
        } else if (classNames) {
            el.classList.add(classNames);
        }

        for (const attrName in attributes) {
            el[attrName] = attributes[attrName];
        }

        return el;
    }
}
