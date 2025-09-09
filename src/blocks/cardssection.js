/**
 * Cards Section Block für EditorJS
 * Ermöglicht das Hinzufügen mehrerer Cards (Bild/Video + Text) aus dem REDAXO Medienpool
 * Funktioniert ähnlich wie Downloads, aber für Cards mit Media-Inhalten
 */
class CardsSectionBlock {
    static get toolbox() {
        return {
            title: 'Cards',
            icon: '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="3" width="8" height="10" rx="2" ry="2" stroke="currentColor" fill="none" stroke-width="1.5"/><rect x="14" y="3" width="8" height="10" rx="2" ry="2" stroke="currentColor" fill="none" stroke-width="1.5"/><rect x="8" y="16" width="8" height="6" rx="2" ry="2" stroke="currentColor" fill="none" stroke-width="1.5"/><rect x="4" y="5" width="4" height="3" rx="1" fill="currentColor" opacity="0.3"/><line x1="4" y1="9" x2="6" y2="9" stroke="currentColor" stroke-width="1"/><line x1="4" y1="10" x2="7" y2="10" stroke="currentColor" stroke-width="1"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        
        // REXMediaTool für Bilder und Videos
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'avi', 'mov'], 
                context: 'editorjs_cards'
            }
        });
        
        this.CSS = {
            wrapper: 'cdx-cards-section',
            container: 'cdx-cards-section__container',
            item: 'cdx-cards-section__item',
            itemHeader: 'cdx-cards-section__item-header',
            itemContent: 'cdx-cards-section__item-content',
            itemActions: 'cdx-cards-section__item-actions',
            mediaSelect: 'cdx-cards-section__media-select',
            mediaPreview: 'cdx-cards-section__media-preview',
            mediaWrapper: 'cdx-cards-section__media-wrapper',
            image: 'cdx-cards-section__image',
            video: 'cdx-cards-section__video',
            altTextWrapper: 'cdx-cards-section__alt-text-wrapper',
            altTextInput: 'cdx-cards-section__alt-text-input',
            altTextLabel: 'cdx-cards-section__alt-text-label',
            altTextWarning: 'cdx-cards-section__alt-text-warning',
            titleInput: 'cdx-cards-section__title-input',
            textInput: 'cdx-cards-section__text-input',
            addButton: 'cdx-cards-section__add-button',
            removeButton: 'cdx-cards-section__remove-button',
            dragHandle: 'cdx-cards-section__drag-handle',
            settingsButton: 'cdx-cards-section__settings-button',
            settingsButtonActive: 'cdx-cards-section__settings-button--active'
        };

        this.nodes = {
            wrapper: null,
            container: null
        };

        this.data = {
            title: data.title || 'Cards',
            items: data.items || [this._createEmptyItem()],
            showTitle: data.showTitle !== false,
            layout: data.layout || 'grid', // list, grid, compact
            gridColumns: data.gridColumns || 3 // 1-4 columns for grid layout
        };

        this.layouts = {
            list: {
                icon: '<i class="fa-solid fa-list"></i>',
                title: 'Liste'
            },
            grid: {
                icon: '<i class="fa-solid fa-th-large"></i>',
                title: 'Raster'
            },
            compact: {
                icon: '<i class="fa-solid fa-bars"></i>',
                title: 'Kompakt'
            }
        };
    }

    render() {
        this.nodes.wrapper = this._make('div', [this.CSS.wrapper]);
        this.nodes.wrapper.dataset.layout = this.data.layout;
        this.nodes.wrapper.dataset.gridColumns = this.data.gridColumns;

        // Titel-Bereich
        if (this.data.showTitle) {
            const titleContainer = this._make('div', 'cdx-cards-section__title-container');
            const titleInput = this._make('input', 'cdx-cards-section__title-input', {
                type: 'text',
                placeholder: 'Cards Titel...',
                value: this.data.title
            });

            titleInput.addEventListener('input', (e) => {
                this.data.title = e.target.value;
            });

            titleContainer.appendChild(titleInput);
            this.nodes.wrapper.appendChild(titleContainer);
        }

        // Container für Card-Items
        this.nodes.container = this._make('div', [this.CSS.container]);
        this.nodes.wrapper.appendChild(this.nodes.container);

        // Items rendern
        this._renderItems();

        // Add Button
        const addButton = this._make('button', [this.CSS.addButton], {
            innerHTML: '<i class="fa-solid fa-plus"></i> Card hinzufügen',
            type: 'button'
        });

        addButton.addEventListener('click', () => {
            this._addItem();
        });

        this.nodes.wrapper.appendChild(addButton);

        return this.nodes.wrapper;
    }

    renderSettings() {
        const wrapper = this._make('div');

        // Layout Settings
        const layoutLabel = this._make('div', null, {
            innerHTML: '<strong>Layout:</strong>',
            style: 'margin-bottom: 8px;'
        });
        wrapper.appendChild(layoutLabel);

        Object.entries(this.layouts).forEach(([layout, config]) => {
            const button = this._make('span', [this.CSS.settingsButton], {
                innerHTML: config.icon + ' ' + config.title
            });

            button.addEventListener('click', () => {
                this.data.layout = layout;
                this.nodes.wrapper.dataset.layout = layout;
                
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
            const columnsLabel = this._make('div', null, {
                innerHTML: '<strong>Spalten:</strong>',
                style: 'margin: 15px 0 8px 0;'
            });
            wrapper.appendChild(columnsLabel);

            for (let i = 1; i <= 4; i++) {
                const colButton = this._make('span', [this.CSS.settingsButton], {
                    innerHTML: `<i class="fa-solid fa-th"></i> ${i}`,
                    title: `${i} Spalten`
                });

                colButton.addEventListener('click', () => {
                    this.data.gridColumns = i;
                    this.nodes.wrapper.dataset.gridColumns = i;
                    
                    // Update active state for column buttons
                    wrapper.querySelectorAll('.' + this.CSS.settingsButton).forEach(btn => {
                        if (btn.innerHTML.includes('fa-th') && btn.innerHTML.includes(i.toString())) {
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

        // Titel anzeigen Toggle
        const titleToggle = this._make('div', null, {
            style: 'margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;'
        });

        const titleLabel = this._make('label', null, {
            innerHTML: '<input type="checkbox" style="margin-right: 8px;"> Titel anzeigen',
            style: 'cursor: pointer; font-weight: normal;'
        });

        const checkbox = titleLabel.querySelector('input');
        checkbox.checked = this.data.showTitle;
        checkbox.addEventListener('change', (e) => {
            this.data.showTitle = e.target.checked;
            
            // Title-Container ein-/ausblenden
            const titleContainer = this.nodes.wrapper.querySelector('.cdx-cards-section__title-container');
            if (this.data.showTitle) {
                if (!titleContainer) {
                    // Title-Container erstellen
                    const newTitleContainer = this._make('div', 'cdx-cards-section__title-container');
                    const titleInput = this._make('input', 'cdx-cards-section__title-input', {
                        type: 'text',
                        placeholder: 'Cards Titel...',
                        value: this.data.title
                    });

                    titleInput.addEventListener('input', (e) => {
                        this.data.title = e.target.value;
                    });

                    newTitleContainer.appendChild(titleInput);
                    this.nodes.wrapper.insertBefore(newTitleContainer, this.nodes.container);
                }
            } else {
                if (titleContainer) {
                    titleContainer.remove();
                }
            }
        });

        titleToggle.appendChild(titleLabel);
        wrapper.appendChild(titleToggle);

        return wrapper;
    }

    save(blockContent) {
        // Titel aus dem DOM holen, falls sichtbar
        const titleInput = blockContent.querySelector('.cdx-cards-section__title-input');
        if (titleInput) {
            this.data.title = titleInput.value;
        }
        
        return {
            title: this.data.title,
            items: this.data.items,
            showTitle: this.data.showTitle,
            layout: this.data.layout,
            gridColumns: this.data.gridColumns
        };
    }

    static get sanitize() {
        return {
            title: {},
            items: {},
            showTitle: {},
            layout: {},
            gridColumns: {}
        };
    }

    _createEmptyItem() {
        return {
            mediaFile: '',
            mediaUrl: '',
            mediaType: '',
            mediaAlt: '',
            title: '',
            text: ''
        };
    }

    _renderItems() {
        this.nodes.container.innerHTML = '';

        this.data.items.forEach((item, index) => {
            const itemElement = this._createItemElement(item, index);
            this.nodes.container.appendChild(itemElement);
        });
    }

    _createItemElement(item, index) {
        const itemWrapper = this._make('div', [this.CSS.item]);
        itemWrapper.dataset.index = index;

        // Drag-Handle für dieses Item
        const dragHandle = this._make('div', [this.CSS.dragHandle], {
            innerHTML: '<i class="fa-solid fa-grip-vertical"></i>',
            title: 'Zum Sortieren ziehen'
        });

        // Drag & Drop Events hinzufügen
        this._addDragEvents(itemWrapper, dragHandle);

        // Main Content Area
        const mainContent = this._make('div', 'cdx-cards-section__main-content');

        // Header mit Remove Button
        const header = this._make('div', [this.CSS.itemHeader]);
        
        const removeButton = this._make('button', [this.CSS.removeButton], {
            innerHTML: '<i class="fa-solid fa-trash"></i>',
            title: 'Card entfernen',
            type: 'button'
        });

        removeButton.addEventListener('click', () => {
            this._removeItem(index);
        });

        header.appendChild(removeButton);

        // Content mit Inputs
        const content = this._make('div', [this.CSS.itemContent]);

        // Media Selection Area
        const mediaSelect = this._createMediaSelectArea(item, index);
        content.appendChild(mediaSelect);

        // Title Input
        const titleInput = this._make('input', [this.CSS.titleInput], {
            type: 'text',
            placeholder: 'Card Titel...',
            value: item.title
        });

        titleInput.addEventListener('input', (e) => {
            this.data.items[index].title = e.target.value;
        });

        content.appendChild(titleInput);

        // Text Input (Textarea)
        const textInput = this._make('textarea', [this.CSS.textInput], {
            placeholder: 'Card Text...',
            value: item.text,
            rows: 3
        });

        textInput.addEventListener('input', (e) => {
            this.data.items[index].text = e.target.value;
        });

        content.appendChild(textInput);

        // ALT Text Input (nur bei Bildern)
        if (item.mediaType === 'image' && item.mediaUrl) {
            const altTextWrapper = this._createAltTextInput(item, index);
            content.appendChild(altTextWrapper);
        }

        // Zusammenbau der Item-Struktur
        mainContent.appendChild(header);
        mainContent.appendChild(content);
        
        itemWrapper.appendChild(dragHandle);
        itemWrapper.appendChild(mainContent);

        return itemWrapper;
    }

    _createMediaSelectArea(item, index) {
        const mediaSelect = this._make('div', [this.CSS.mediaSelect]);

        if (item.mediaUrl) {
            // Media selected - show preview
            const preview = this._createMediaPreview(item, index);
            mediaSelect.appendChild(preview);
        } else {
            // No media selected - show select button
            const selectButton = this._make('button', 'cdx-cards-section__select-button', {
                innerHTML: '<i class="fa-solid fa-image"></i> Media aus Medienpool wählen',
                type: 'button'
            });

            selectButton.addEventListener('click', () => {
                this._openMediaPool(index);
            });

            mediaSelect.appendChild(selectButton);
        }

        return mediaSelect;
    }

    _createMediaPreview(item, index) {
        const preview = this._make('div', [this.CSS.mediaPreview]);

        // Media Wrapper
        const mediaWrapper = this._make('div', [this.CSS.mediaWrapper]);
        
        if (item.mediaType === 'video' || this._isVideoFile(item.mediaFile)) {
            // Video Element mit verbessertem Playback
            const video = this._make('video', [this.CSS.video], {
                src: item.mediaUrl,
                controls: true,
                preload: 'metadata',
                playsinline: true, // Für mobile Geräte
                muted: false, // Erlaubt Playback ohne Autoplay-Beschränkungen
                loop: false
            });
            
            // Error-Handling für Video
            video.addEventListener('error', (e) => {
                console.warn('Video load error:', e, item.mediaUrl);
            });
            
            video.addEventListener('loadedmetadata', () => {
                console.log('Video loaded successfully:', item.mediaUrl);
            });
            
            mediaWrapper.appendChild(video);
        } else {
            // Image Element
            const image = this._make('img', [this.CSS.image], {
                src: item.mediaUrl,
                alt: item.mediaAlt || item.title || ''
            });
            
            // Lightbox für Bilder
            image.style.cursor = 'pointer';
            image.addEventListener('click', (e) => {
                e.stopPropagation();
                this._openLightbox(item);
            });
            
            mediaWrapper.appendChild(image);
        }

        // Actions
        const actions = this._make('div', [this.CSS.itemActions]);
        
        const changeButton = this._make('button', 'cdx-cards-section__change-button', {
            innerHTML: '<i class="fa-solid fa-sync"></i>',
            title: 'Media ändern',
            type: 'button'
        });

        changeButton.addEventListener('click', () => {
            this._openMediaPool(index);
        });

        actions.appendChild(changeButton);

        preview.appendChild(mediaWrapper);
        preview.appendChild(actions);

        return preview;
    }

    _createAltTextInput(item, index) {
        if (this.readOnly) return this._make('div');
        
        const wrapper = this._make('div', [this.CSS.altTextWrapper]);
        
        const label = this._make('label', [this.CSS.altTextLabel], {
            innerHTML: '<i class="fa-solid fa-universal-access"></i> ALT-Text:'
        });
        
        const input = this._make('input', [this.CSS.altTextInput], {
            type: 'text',
            placeholder: 'Beschreibung für Screenreader...',
            value: item.mediaAlt || ''
        });
        
        // Warning für fehlenden ALT-Text
        const warning = this._make('div', [this.CSS.altTextWarning], {
            innerHTML: '<i class="fa-solid fa-exclamation-triangle"></i> Bild ohne ALT-Text!',
            style: item.mediaAlt ? 'display: none;' : 'display: block;'
        });
        
        // Event Listener für Input
        input.addEventListener('input', (e) => {
            this.data.items[index].mediaAlt = e.target.value.trim();
            
            // Warning ein-/ausblenden
            warning.style.display = this.data.items[index].mediaAlt ? 'none' : 'block';
        });
        
        // Auto-Suggest Button
        const suggestButton = this._make('button', 'cdx-cards-section__alt-suggest-button', {
            innerHTML: '<i class="fa-solid fa-magic"></i> Automatisch',
            type: 'button',
            title: 'ALT-Text basierend auf Titel/Dateiname vorschlagen'
        });
        
        suggestButton.addEventListener('click', () => {
            const suggestion = this._suggestAltText(item);
            input.value = suggestion;
            this.data.items[index].mediaAlt = suggestion;
            warning.style.display = 'none';
        });
        
        const buttonWrapper = this._make('div', 'cdx-cards-section__alt-button-wrapper');
        buttonWrapper.appendChild(suggestButton);
        
        wrapper.appendChild(label);
        wrapper.appendChild(input);
        wrapper.appendChild(buttonWrapper);
        wrapper.appendChild(warning);
        
        return wrapper;
    }

    _suggestAltText(item) {
        let suggestion = '';
        
        // Basis-Vorschlag aus Dateiname ableiten
        if (item.mediaFile) {
            const filename = item.mediaFile.replace(/\.[^/.]+$/, ""); // Erweiterung entfernen
            suggestion = filename.replace(/[-_]/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        }
        
        // Mit Titel kombinieren wenn vorhanden
        if (item.title && item.title.trim()) {
            const titleText = item.title.replace(/<[^>]*>/g, ''); // HTML entfernen
            if (titleText.trim()) {
                suggestion = suggestion ? `${suggestion} - ${titleText}` : titleText;
            }
        }
        
        // Fallback
        if (!suggestion) {
            suggestion = 'Bild';
        }
        
        return suggestion;
    }

    _openMediaPool(index) {
        this.mediaTool.openMediaPool({
            types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'avi', 'mov'],
            context: 'editorjs_cards'
        }, (mediaData) => {
            this._setMedia(index, mediaData);
        }).catch(error => {
            if (error.message !== 'Media pool closed without selection') {
                console.error('Fehler bei der Medienauswahl:', error);
            }
        });
    }

    _setMedia(index, mediaData) {
        // Daten aus dem MediaTool übernehmen
        this.data.items[index].mediaFile = mediaData.filename;
        this.data.items[index].mediaUrl = mediaData.url;
        this.data.items[index].mediaAlt = mediaData.alt || this.data.items[index].mediaAlt;
        
        // Medientyp automatisch erkennen
        this.data.items[index].mediaType = this._isVideoFile(mediaData.filename) ? 'video' : 'image';

        // Items neu rendern
        this._renderItems();
    }

    _openLightbox(item) {
        if (item.mediaType !== 'image' || !item.mediaUrl) return;

        // Lightbox Overlay erstellen
        const overlay = this._make('div', 'cdx-cards-section__lightbox-overlay', {
            style: 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; display: flex; align-items: center; justify-content: center; cursor: pointer;'
        });

        const image = this._make('img', 'cdx-cards-section__lightbox-image', {
            src: item.mediaUrl,
            alt: item.mediaAlt || item.title || '',
            style: 'max-width: 90%; max-height: 90%; object-fit: contain;'
        });

        overlay.appendChild(image);
        document.body.appendChild(overlay);

        // Schließen bei Klick
        overlay.addEventListener('click', () => {
            document.body.removeChild(overlay);
        });

        // Schließen mit Escape
        const closeHandler = (e) => {
            if (e.key === 'Escape') {
                document.body.removeChild(overlay);
                document.removeEventListener('keydown', closeHandler);
            }
        };
        document.addEventListener('keydown', closeHandler);
    }

    _isVideoFile(filename) {
        if (!filename) return false;
        const videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv', 'flv', 'm4v'];
        const extension = filename.toLowerCase().split('.').pop();
        return videoExtensions.includes(extension);
    }

    _addItem() {
        this.data.items.push(this._createEmptyItem());
        this._renderItems();
    }

    _removeItem(index) {
        if (this.data.items.length > 1) {
            this.data.items.splice(index, 1);
            this._renderItems();
        }
    }

    _getItemIndex(element) {
        const item = element.closest('.' + this.CSS.item);
        return parseInt(item.dataset.index);
    }

    _addDragEvents(itemWrapper, dragHandle) {
        // Implementierung für Drag & Drop zwischen Items
        // Vereinfachte Version - kann später erweitert werden
        dragHandle.addEventListener('mousedown', (e) => {
            e.preventDefault();
            // TODO: Drag & Drop Implementierung
        });
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
window.CardsSectionBlock = CardsSectionBlock;