/**
 * Image Block für EditorJS mit REXMediaTool
 * Beispiel für die Verwendung der zentralen REXMediaTool-Komponente
 */
class ImageBlock {
    static get toolbox() {
        return {
            title: 'Bild',
            icon: '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 3H3C1.9 3 1 3.9 1 5v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM5 17l3.5-4.5 2.5 3.01L14.5 11l4.5 6H5z"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        this.config = config || {};
        
        // REXMediaTool für Medienpool-Integration
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], // Nur Bilder
                context: 'editorjs_image'
            }
        });
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-image',
            imageWrapper: 'cdx-image__wrapper',
            image: 'cdx-image__image',
            caption: 'cdx-image__caption',
            altText: 'cdx-image__alt-text',
            button: 'cdx-image__button',
            settingsButton: 'cdx-image__settings-button',
            settingsButtonActive: 'cdx-image__settings-button--active',
            altWarning: 'cdx-image__alt-warning'
        };

        this.nodes = {
            holder: null,
            wrapper: null,
            image: null,
            caption: null,
            selectButton: null,
            altWarning: null
        };

        this.data = {
            imageFile: data.imageFile || '',
            imageUrl: data.imageUrl || '',
            imageAlt: data.imageAlt || '',
            caption: data.caption || '',
            stretched: data.stretched || false,
            withBorder: data.withBorder || false,
            withBackground: data.withBackground || false,
            aspectRatio: data.aspectRatio || 'auto', // auto, 16-9, 4-3, 1-1, 3-2, 21-9
            cropMode: data.cropMode || 'cover' // cover, contain, fill
        };

        this.aspectRatios = {
            'auto': { title: 'Automatisch', icon: '<i class="fa-solid fa-expand-arrows-alt"></i>' },
            '16-9': { title: '16:9 (Widescreen)', icon: '<i class="fa-solid fa-tv"></i>' },
            '4-3': { title: '4:3 (Standard)', icon: '<i class="fa-solid fa-image"></i>' },
            '1-1': { title: '1:1 (Quadrat)', icon: '<i class="fa-solid fa-square"></i>' },
            '3-2': { title: '3:2 (Foto)', icon: '<i class="fa-solid fa-camera"></i>' },
            '21-9': { title: '21:9 (Ultrawide)', icon: '<i class="fa-solid fa-panorama"></i>' }
        };

        this.cropModes = {
            'cover': { title: 'Ausfüllen', icon: '<i class="fa-solid fa-expand"></i>' },
            'contain': { title: 'Einpassen', icon: '<i class="fa-solid fa-compress"></i>' },
            'fill': { title: 'Strecken', icon: '<i class="fa-solid fa-arrows-alt"></i>' }
        };
    }

    render() {
        const wrapper = this._make('div', [this.CSS.baseClass, this.CSS.wrapper]);
        const imageWrapper = this._make('div', [this.CSS.imageWrapper]);
        
        if (this.data.imageUrl) {
            this._createImage();
            imageWrapper.appendChild(this.nodes.image);
            
            // Caption nur anzeigen wenn vorhanden
            if (this.data.caption) {
                const caption = this._make('div', [this.CSS.caption], {
                    innerHTML: this.data.caption
                });
                imageWrapper.appendChild(caption);
                this.nodes.caption = caption;
            }
        } else {
            this._createSelectButton();
            imageWrapper.appendChild(this.nodes.selectButton);
        }
        
        wrapper.appendChild(imageWrapper);

        this.nodes.holder = wrapper;
        this.nodes.wrapper = imageWrapper;

        // Wrapper-Klassen sofort nach dem Setzen der Referenzen aktualisieren
        if (this.data.imageUrl) {
            this._updateWrapperClasses();
        }

        return wrapper;
    }

    renderSettings() {
        const wrapper = this._make('div', ['cdx-image-settings']);

        // Gestreckt-Button
        const stretchButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-arrows-alt-h"></i>',
            title: 'Bild auf volle Breite strecken'
        });
        
        stretchButton.addEventListener('click', () => {
            this._toggleTune('stretched');
            stretchButton.classList.toggle(this.CSS.settingsButtonActive, this.data.stretched);
        });

        if (this.data.stretched) {
            stretchButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Seitenverhältnis-Dropdown
        const aspectRatioDropdown = this._createDropdown(
            'Seitenverhältnis',
            '<i class="fa-solid fa-crop"></i>',
            this.aspectRatios,
            this.data.aspectRatio,
            (value) => this._setAspectRatio(value)
        );

        // Crop-Mode-Dropdown (nur wenn nicht auto aspect ratio)
        const cropModeDropdown = this._createDropdown(
            'Anpassung',
            '<i class="fa-solid fa-object-group"></i>',
            this.cropModes,
            this.data.cropMode,
            (value) => this._setCropMode(value)
        );

        // Border-Button
        const borderButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-border-style"></i>',
            title: 'Rahmen hinzufügen'
        });
        
        borderButton.addEventListener('click', () => {
            this._toggleTune('withBorder');
            borderButton.classList.toggle(this.CSS.settingsButtonActive, this.data.withBorder);
        });

        if (this.data.withBorder) {
            borderButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Hintergrund-Button
        const backgroundButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-fill-drip"></i>',
            title: 'Hintergrund hinzufügen'
        });
        
        backgroundButton.addEventListener('click', () => {
            this._toggleTune('withBackground');
            backgroundButton.classList.toggle(this.CSS.settingsButtonActive, this.data.withBackground);
        });

        if (this.data.withBackground) {
            backgroundButton.classList.add(this.CSS.settingsButtonActive);
        }

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
        
        // Caption Button
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

        wrapper.appendChild(stretchButton);
        wrapper.appendChild(aspectRatioDropdown.element);
        wrapper.appendChild(cropModeDropdown.element);
        wrapper.appendChild(borderButton);
        wrapper.appendChild(backgroundButton);
        wrapper.appendChild(changeImageButton);
        wrapper.appendChild(altTextButton);
        wrapper.appendChild(captionButton);

        // Crop-Mode nur anzeigen wenn nicht auto
        this._updateCropModeVisibility(cropModeDropdown.element);
        
        // Referenzen speichern für spätere Updates
        this.cropModeDropdown = cropModeDropdown;

        return wrapper;
    }

    save(blockContent) {
        return {
            imageFile: this.data.imageFile,
            imageUrl: this.data.imageUrl,
            imageAlt: this.data.imageAlt,
            caption: this.data.caption,
            stretched: this.data.stretched,
            withBorder: this.data.withBorder,
            withBackground: this.data.withBackground,
            aspectRatio: this.data.aspectRatio,
            cropMode: this.data.cropMode
        };
    }

    static get sanitize() {
        return {
            caption: {
                br: true,
                strong: true,
                em: true
            },
            imageFile: {},
            imageUrl: {},
            imageAlt: {},
            stretched: {},
            withBorder: {},
            withBackground: {},
            aspectRatio: {},
            cropMode: {}
        };
    }

    _createImage() {
        const image = this._make('img', [this.CSS.image], {
            src: this.data.imageUrl,
            alt: this.data.imageAlt || '' // Leerer Alt-Text als Standard
        });
        
        // CSS-Klassen für Tunes
        if (this.data.stretched) {
            image.classList.add('stretched');
        }
        if (this.data.withBorder) {
            image.classList.add('with-border');
        }
        if (this.data.withBackground) {
            image.classList.add('with-background');
        }
        
        // Wrapper-Klassen aktualisieren (für Aspect Ratio, Crop Mode, etc.)
        if (this.nodes.wrapper) {
            this._updateWrapperClasses();
        } else {
            // Falls Wrapper noch nicht existiert, später aktualisieren
            setTimeout(() => {
                if (this.nodes.wrapper) {
                    this._updateWrapperClasses();
                }
            }, 0);
        }
        
        // Klick zum Ändern
        image.addEventListener('click', () => {
            if (!this.readOnly) {
                this._openMediapool();
            }
        });

        this.nodes.image = image;
        
        // Alt-Text-Warnung prüfen und hinzufügen/entfernen
        this._updateAltWarning();
    }

    _createSelectButton() {
        const button = this._make('div', [this.CSS.button], {
            innerHTML: '<i class="fa-solid fa-image"></i> Bild aus Medienpool wählen'
        });
        
        button.addEventListener('click', () => {
            this._openMediapool();
        });

        this.nodes.selectButton = button;
    }

    _openMediapool() {
        // Verwendung der zentralen REXMediaTool-Komponente
        this.mediaTool.selectImage((mediaData) => {
            this._setImage(mediaData);
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

    _setImage(mediaData) {
        // Daten aus dem MediaTool übernehmen
        this.data.imageFile = mediaData.filename;
        this.data.imageUrl = mediaData.url;
        this.data.imageAlt = mediaData.alt;

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
        this.nodes.wrapper.insertBefore(this.nodes.image, this.nodes.wrapper.firstChild);
        
        // Caption aktualisieren
        this._updateCaption();
    }

    _toggleTune(tune) {
        this.data[tune] = !this.data[tune];
        
        if (this.nodes.image) {
            if (tune === 'stretched') {
                this.nodes.image.classList.toggle('stretched', this.data.stretched);
                // Wrapper-Klasse für Fallback auch togglen
                if (this.nodes.wrapper) {
                    this.nodes.wrapper.classList.toggle('stretched', this.data.stretched);
                }
            } else if (tune === 'withBorder') {
                this.nodes.image.classList.toggle('with-border', this.data.withBorder);
            } else if (tune === 'withBackground') {
                this.nodes.image.classList.toggle('with-background', this.data.withBackground);
            }
        }
    }

    _setAspectRatio(ratio) {
        // Alte Aspect Ratio Klassen entfernen
        Object.keys(this.aspectRatios).forEach(r => {
            if (r !== 'auto') {
                this.nodes.wrapper.classList.remove('aspect-' + r);
            }
        });
        
        this.data.aspectRatio = ratio;
        
        // Neue Aspect Ratio Klasse hinzufügen
        if (ratio !== 'auto') {
            this.nodes.wrapper.classList.add('aspect-' + ratio);
            // Crop Mode anwenden
            this._applyCropMode();
        } else {
            // Crop Mode Klassen entfernen bei auto
            Object.keys(this.cropModes).forEach(mode => {
                this.nodes.wrapper.classList.remove('crop-' + mode);
            });
        }
        
        // Settings UI aktualisieren
        this._updateCropModeVisibility();
    }

    _setCropMode(mode) {
        // Alte Crop Mode Klassen entfernen
        Object.keys(this.cropModes).forEach(m => {
            this.nodes.wrapper.classList.remove('crop-' + m);
        });
        
        this.data.cropMode = mode;
        
        // Neue Crop Mode Klasse hinzufügen (nur wenn nicht auto aspect ratio)
        if (this.data.aspectRatio !== 'auto') {
            this._applyCropMode();
        }
    }

    _applyCropMode() {
        if (this.data.aspectRatio !== 'auto') {
            this.nodes.wrapper.classList.add('crop-' + this.data.cropMode);
        }
    }

    _updateCropModeVisibility(cropModeElement = null) {
        if (!cropModeElement && this.cropModeDropdown) {
            cropModeElement = this.cropModeDropdown.element;
        }
        
        if (cropModeElement) {
            cropModeElement.style.display = this.data.aspectRatio === 'auto' ? 'none' : 'inline-block';
        }
    }

    _updateWrapperClasses() {
        if (!this.nodes.wrapper) return;
        
        // Aspect Ratio
        Object.keys(this.aspectRatios).forEach(ratio => {
            if (ratio !== 'auto') {
                this.nodes.wrapper.classList.remove('aspect-' + ratio);
            }
        });
        
        if (this.data.aspectRatio !== 'auto') {
            this.nodes.wrapper.classList.add('aspect-' + this.data.aspectRatio);
        }
        
        // Crop Mode
        Object.keys(this.cropModes).forEach(mode => {
            this.nodes.wrapper.classList.remove('crop-' + mode);
        });
        
        if (this.data.aspectRatio !== 'auto') {
            this.nodes.wrapper.classList.add('crop-' + this.data.cropMode);
        }
        
        // Stretched
        this.nodes.wrapper.classList.toggle('stretched', this.data.stretched);
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
        
        // Prüfen ob Alt-Text fehlt (leer oder nur Whitespace)
        const altText = this.data.imageAlt || '';
        const hasAltText = altText.trim().length > 0;
        
        // Warnsymbol nur anzeigen wenn Alt-Text fehlt und nicht im ReadOnly-Modus
        if (!hasAltText && !this.readOnly && this.nodes.image && this.nodes.wrapper) {
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
            this.nodes.wrapper.appendChild(this.nodes.altWarning);
        }
    }

    _createDropdown(label, icon, options, currentValue, onChange) {
        const dropdown = this._make('div', ['cdx-image-dropdown']);
        
        // Button mit akuellem Wert
        const button = this._make('span', [this.CSS.settingsButton, 'cdx-image-dropdown__button'], {
            innerHTML: icon + ' ' + options[currentValue].title,
            title: label
        });
        
        // Dropdown-Menü
        const menu = this._make('div', ['cdx-image-dropdown__menu']);
        
        Object.entries(options).forEach(([value, config]) => {
            const item = this._make('div', ['cdx-image-dropdown__item'], {
                innerHTML: config.icon + ' ' + config.title
            });
            
            if (value === currentValue) {
                item.classList.add('cdx-image-dropdown__item--active');
            }
            
            item.addEventListener('click', () => {
                // Aktive Klasse aktualisieren
                menu.querySelectorAll('.cdx-image-dropdown__item').forEach(i => {
                    i.classList.remove('cdx-image-dropdown__item--active');
                });
                item.classList.add('cdx-image-dropdown__item--active');
                
                // Button-Text aktualisieren
                button.innerHTML = icon + ' ' + config.title;
                
                // Menü schließen
                dropdown.classList.remove('cdx-image-dropdown--open');
                
                // Callback aufrufen
                onChange(value);
            });
            
            menu.appendChild(item);
        });
        
        dropdown.appendChild(button);
        dropdown.appendChild(menu);
        
        // Button Click Handler
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Andere Dropdowns schließen
            document.querySelectorAll('.cdx-image-dropdown--open').forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('cdx-image-dropdown--open');
                }
            });
            
            dropdown.classList.toggle('cdx-image-dropdown--open');
        });
        
        // Schließen bei Klick außerhalb
        document.addEventListener('click', () => {
            dropdown.classList.remove('cdx-image-dropdown--open');
        });
        
        return {
            element: dropdown,
            updateValue: (value) => {
                const config = options[value];
                button.innerHTML = icon + ' ' + config.title;
                
                menu.querySelectorAll('.cdx-image-dropdown__item').forEach(i => {
                    i.classList.remove('cdx-image-dropdown__item--active');
                });
                
                const activeItem = menu.querySelector(`[data-value="${value}"]`);
                if (activeItem) {
                    activeItem.classList.add('cdx-image-dropdown__item--active');
                }
            }
        };
    }
    
    _editAltText() {
        const currentAlt = this.data.imageAlt || '';
        const newAlt = prompt('Alt-Text für Barrierefreiheit eingeben:\n(Beschreibt das Bild für Screenreader)', currentAlt);
        
        if (newAlt !== null) { // null bedeutet Abbruch
            this.data.imageAlt = newAlt;
            // Img-Tag Alt-Attribut aktualisieren
            if (this.nodes.image) {
                this.nodes.image.alt = this.data.imageAlt;
            }
            // Alt-Warnung aktualisieren
            this._updateAltWarning();
        }
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
            this.nodes.wrapper.appendChild(caption);
            this.nodes.caption = caption;
        }
        
        // Caption-Button Zustand aktualisieren (falls Settings-Panel vorhanden)
        const captionButton = document.querySelector('.cdx-image__settings-button[title="Bildunterschrift bearbeiten"]');
        if (captionButton) {
            if (this.data.caption && this.data.caption.trim()) {
                captionButton.classList.add(this.CSS.settingsButtonActive);
            } else {
                captionButton.classList.remove(this.CSS.settingsButtonActive);
            }
        }
    }
}

// Export für das Bundle
window.ImageBlock = ImageBlock;
