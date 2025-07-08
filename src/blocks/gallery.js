/**
 * Image Gallery Block für EditorJS mit REXMediaTool
 * Ermöglicht das Hinzufügen mehrerer Bilder aus dem REDAXO Medienpool
 */
class ImageGalleryBlock {
    static get toolbox() {
        return {
            title: 'Bildergalerie',
            icon: '<svg width="17" height="15" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M448 80c8.8 0 16 7.2 16 16V415.8l-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3V96c0-8.8 7.2-16 16-16H448zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        
        this.CSS = {
            wrapper: 'cdx-gallery',
            container: 'cdx-gallery__container',
            item: 'cdx-gallery__item',
            itemHeader: 'cdx-gallery__item-header',
            itemContent: 'cdx-gallery__item-content',
            itemActions: 'cdx-gallery__item-actions',
            imageSelect: 'cdx-gallery__image-select',
            imagePreview: 'cdx-gallery__image-preview',
            imageContainer: 'cdx-gallery__image-container',
            image: 'cdx-gallery__image',
            imageInfo: 'cdx-gallery__image-info',
            imageName: 'cdx-gallery__image-name',
            titleInput: 'cdx-gallery__title-input',
            descriptionInput: 'cdx-gallery__description-input',
            addButton: 'cdx-gallery__add-button',
            removeButton: 'cdx-gallery__remove-button',
            dragHandle: 'cdx-gallery__drag-handle',
            settingsButton: 'cdx-gallery__settings-button',
            settingsButtonActive: 'cdx-gallery__settings-button--active'
        };

        this.nodes = {
            wrapper: null,
            container: null
        };

        this.data = {
            title: data.title || 'Bildergalerie',
            items: data.items || [this._createEmptyItem()],
            showTitle: data.showTitle !== false,
            layout: data.layout || 'grid', // grid, masonry, carousel, list
            imageSize: data.imageSize || 'medium', // small, medium, large, full
            showCaptions: data.showCaptions !== false,
            aspectRatio: data.aspectRatio || '', // '', '1-1', '16-9', '4-3'
            spacing: data.spacing || 'normal' // tight, normal, wide
        };

        this.layouts = {
            grid: {
                icon: '<i class="fa-solid fa-th"></i>',
                title: 'Raster'
            },
            carousel: {
                icon: '<i class="fa-solid fa-images"></i>',
                title: 'Karussell'
            },
            list: {
                icon: '<i class="fa-solid fa-list"></i>',
                title: 'Liste'
            }
        };

        this.imageSizes = {
            small: 'Klein',
            medium: 'Mittel',
            large: 'Groß',
            full: 'Original'
        };

        this.aspectRatios = {
            '': 'Automatisch',
            '1-1': '1:1 (Quadrat)',
            '16-9': '16:9 (Widescreen)',
            '4-3': '4:3 (Standard)',
            '3-2': '3:2'
        };

        this.spacings = {
            tight: 'Eng',
            normal: 'Normal',
            wide: 'Weit'
        };
    }

    render() {
        this.nodes.wrapper = this._make('div', [this.CSS.wrapper]);
        this.nodes.wrapper.dataset.layout = this.data.layout;
        this.nodes.wrapper.dataset.imageSize = this.data.imageSize;
        this.nodes.wrapper.dataset.spacing = this.data.spacing;
        
        if (this.data.aspectRatio) {
            this.nodes.wrapper.dataset.aspectRatio = this.data.aspectRatio;
        }

        // Titel-Bereich
        if (this.data.showTitle) {
            const titleContainer = this._make('div', 'cdx-gallery__title-container');
            const titleInput = this._make('input', [this.CSS.titleInput], {
                type: 'text',
                placeholder: 'Galerie Titel...',
                value: this.data.title
            });

            titleInput.addEventListener('input', (e) => {
                this.data.title = e.target.value;
            });

            titleContainer.appendChild(titleInput);
            this.nodes.wrapper.appendChild(titleContainer);
        }

        // Container für Galerie-Items
        this.nodes.container = this._make('div', [this.CSS.container]);
        this.nodes.wrapper.appendChild(this.nodes.container);

        // Items rendern
        this._renderItems();

        // Add Button
        const addButton = this._make('button', [this.CSS.addButton], {
            innerHTML: '<i class="fa-solid fa-plus"></i> Bild hinzufügen',
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
                this._updateWrapperAttributes();
                
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

        // Image Size Settings
        const sizeLabel = this._make('div', null, {
            innerHTML: '<strong>Bildgröße:</strong>',
            style: 'margin: 15px 0 8px 0;'
        });
        wrapper.appendChild(sizeLabel);

        Object.entries(this.imageSizes).forEach(([size, title]) => {
            const button = this._make('span', [this.CSS.settingsButton], {
                innerHTML: title
            });

            button.addEventListener('click', () => {
                this.data.imageSize = size;
                this._updateWrapperAttributes();
                
                // Update active state
                wrapper.querySelectorAll('.' + this.CSS.settingsButton).forEach(btn => {
                    btn.classList.remove(this.CSS.settingsButtonActive);
                });
                button.classList.add(this.CSS.settingsButtonActive);
            });

            if (size === this.data.imageSize) {
                button.classList.add(this.CSS.settingsButtonActive);
            }

            wrapper.appendChild(button);
        });

        // Titel anzeigen Toggle
        const titleToggle = this._make('div', null, {
            style: 'margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;'
        });

        const titleLabel = this._make('label', null, {
            innerHTML: '<input type="checkbox" style="margin-right: 8px;"> Titel anzeigen',
            style: 'cursor: pointer; font-weight: normal;'
        });

        const titleCheckbox = titleLabel.querySelector('input');
        titleCheckbox.checked = this.data.showTitle;
        titleCheckbox.addEventListener('change', (e) => {
            this.data.showTitle = e.target.checked;
            
            // Nur das Title-Container Element ein-/ausblenden, nicht den ganzen Block neu rendern
            const titleContainer = this.nodes.wrapper.querySelector('.cdx-gallery__title-container');
            if (this.data.showTitle) {
                if (!titleContainer) {
                    // Title-Container erstellen wenn er nicht existiert
                    const newTitleContainer = this._make('div', 'cdx-gallery__title-container');
                    const titleInput = this._make('input', [this.CSS.titleInput], {
                        type: 'text',
                        placeholder: 'Galerie Titel...',
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

        // Beschriftungen anzeigen Toggle
        const captionsLabel = this._make('label', null, {
            innerHTML: '<input type="checkbox" style="margin-right: 8px;"> Beschriftungen anzeigen',
            style: 'cursor: pointer; font-weight: normal; margin-top: 8px; display: block;'
        });

        const captionsCheckbox = captionsLabel.querySelector('input');
        captionsCheckbox.checked = this.data.showCaptions;
        captionsCheckbox.addEventListener('change', (e) => {
            this.data.showCaptions = e.target.checked;
            this._updateWrapperAttributes();
        });

        wrapper.appendChild(captionsLabel);

        return wrapper;
    }

    save(blockContent) {
        // Titel aus dem DOM holen, falls sichtbar, sonst aus this.data verwenden
        const titleInput = blockContent.querySelector('.' + this.CSS.titleInput);
        if (titleInput) {
            this.data.title = titleInput.value;
        }
        
        return {
            title: this.data.title,
            items: this.data.items,
            showTitle: this.data.showTitle,
            layout: this.data.layout,
            imageSize: this.data.imageSize,
            showCaptions: this.data.showCaptions,
            aspectRatio: this.data.aspectRatio,
            spacing: this.data.spacing
        };
    }

    static get sanitize() {
        return {
            title: {},
            items: {},
            showTitle: {},
            layout: {},
            imageSize: {},
            showCaptions: {},
            aspectRatio: {},
            spacing: {}
        };
    }

    _createEmptyItem() {
        return {
            imageFile: '',
            imageUrl: '',
            title: '',
            description: '',
            alt: ''
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
        const mainContent = this._make('div', 'cdx-gallery__main-content');

        // Header mit Remove Button
        const header = this._make('div', [this.CSS.itemHeader]);
        
        const removeButton = this._make('button', [this.CSS.removeButton], {
            innerHTML: '<i class="fa-solid fa-trash"></i>',
            title: 'Bild entfernen',
            type: 'button'
        });

        removeButton.addEventListener('click', () => {
            this._removeItem(index);
        });

        header.appendChild(removeButton);

        // Content mit Inputs
        const content = this._make('div', [this.CSS.itemContent]);

        // Image Selection Area
        const imageSelect = this._createImageSelectArea(item, index);
        content.appendChild(imageSelect);

        // Title Input
        const titleInput = this._make('input', [this.CSS.titleInput], {
            type: 'text',
            placeholder: 'Bildtitel (optional)',
            value: item.title
        });

        titleInput.addEventListener('input', (e) => {
            this.data.items[index].title = e.target.value;
        });

        content.appendChild(titleInput);

        // Alt Text Input
        const altInput = this._make('input', [this.CSS.titleInput], {
            type: 'text',
            placeholder: 'Alt-Text für Barrierefreiheit',
            value: item.alt
        });

        altInput.addEventListener('input', (e) => {
            this.data.items[index].alt = e.target.value;
        });

        content.appendChild(altInput);

        // Description Input
        const descriptionInput = this._make('textarea', [this.CSS.descriptionInput], {
            placeholder: 'Bildbeschreibung (optional)',
            value: item.description,
            rows: 2
        });

        descriptionInput.addEventListener('input', (e) => {
            this.data.items[index].description = e.target.value;
        });

        content.appendChild(descriptionInput);

        // Zusammenbau der Item-Struktur
        mainContent.appendChild(header);
        mainContent.appendChild(content);
        
        itemWrapper.appendChild(dragHandle);
        itemWrapper.appendChild(mainContent);

        return itemWrapper;
    }

    _createImageSelectArea(item, index) {
        const imageSelect = this._make('div', [this.CSS.imageSelect]);

        if (item.imageFile || item.imageUrl) {
            // Image selected - show preview
            const preview = this._createImagePreview(item, index);
            imageSelect.appendChild(preview);
        } else {
            // No image selected - show select button
            const selectButton = this._make('button', 'cdx-gallery__select-button', {
                innerHTML: '<i class="fa-solid fa-image"></i> Bild aus Medienpool wählen',
                type: 'button'
            });

            selectButton.addEventListener('click', () => {
                this._openMediaPool(index);
            });

            imageSelect.appendChild(selectButton);
        }

        return imageSelect;
    }

    _createImagePreview(item, index) {
        const preview = this._make('div', [this.CSS.imagePreview]);

        // Image Container - klickbar zum Ändern
        const imageContainer = this._make('div', [this.CSS.imageContainer]);
        imageContainer.style.cursor = 'pointer';
        imageContainer.title = 'Klicken um Bild zu ändern';
        
        const imageUrl = item.imageUrl || `/media/${item.imageFile}`;
        const image = this._make('img', [this.CSS.image], {
            src: imageUrl,
            alt: item.alt || item.title || item.imageFile
        });

        // Klick-Event direkt auf das Bild
        imageContainer.addEventListener('click', () => {
            this._openMediaPool(index);
        });

        imageContainer.appendChild(image);
        preview.appendChild(imageContainer);

        return preview;
    }

    _openMediaPool(index) {
        console.log('Opening media pool for image index:', index);
        
        // Erstelle eindeutige Callback-Funktion
        const callbackName = 'galleryBlockCallback_' + Date.now() + '_' + index;
        
        // Globale Callback-Funktion für REDAXO
        window[callbackName] = (filename) => {
            console.log('Global callback triggered for index', index, 'with filename:', filename);
            
            if (filename) {
                this.data.items[index].imageFile = filename;
                this.data.items[index].imageUrl = `/media/${filename}`;
                console.log('Updated item data:', this.data.items[index]);
                this._renderItems();
            }
            
            // Cleanup
            delete window[callbackName];
        };
        
        // Versuch 1: rex_selectMedia (Standard REDAXO Funktion)
        if (typeof rex_selectMedia !== 'undefined') {
            console.log('Using rex_selectMedia function');
            // Nur Bildtypen erlauben
            rex_selectMedia(callbackName, 'jpg,jpeg,png,gif,svg,webp');
        }
        // Versuch 2: REXMediaTool
        else if (typeof window.REXMediaTool !== 'undefined') {
            console.log('REXMediaTool available, calling openMediaPool...');
            
            // Nur Bildtypen für Galerie
            const imageOptions = {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
                context: 'editorjs_gallery'
            };
            
            window.REXMediaTool.openMediaPool(imageOptions, (mediaData) => {
                console.log('Media selected:', mediaData);
                
                // Prüfen ob mediaData ein String (filename) oder Objekt ist
                let filename = '';
                if (typeof mediaData === 'string') {
                    filename = mediaData;
                } else if (mediaData && mediaData.filename) {
                    filename = mediaData.filename;
                } else if (mediaData && mediaData.file) {
                    filename = mediaData.file;
                }
                
                console.log('Extracted filename:', filename);
                
                if (filename) {
                    this.data.items[index].imageFile = filename;
                    this.data.items[index].imageUrl = `/media/${filename}`;
                    console.log('Updated item data:', this.data.items[index]);
                    this._renderItems();
                } else {
                    console.warn('No filename found in media data:', mediaData);
                }
            });
        } 
        // Versuch 3: Direkte openMediaPool Funktion
        else if (typeof openMediaPool !== 'undefined') {
            console.log('Using direct openMediaPool function');
            
            // Direkte REDAXO-Integration mit Global Callback
            const params = 'editorjs_gallery&callback=' + callbackName;
            const mediaPoolWindow = openMediaPool(params);
            
            console.log('Media pool opened with params:', params);
        } 
        // Versuch 4: REDAXO-spezifischer Popup-Aufruf
        else {
            console.log('Trying REDAXO popup approach');
            
            // Konstruiere die REDAXO Medienpool URL
            const baseUrl = window.location.pathname.replace(/\/[^\/]*$/, '');
            const mediapoolUrl = baseUrl + '/index.php?page=mediapool/media&opener_input_field=' + callbackName;
            
            console.log('Opening mediapool URL:', mediapoolUrl);
            
            // Öffne Popup
            const popup = window.open(
                mediapoolUrl,
                'mediapool',
                'width=800,height=600,scrollbars=yes,resizable=yes'
            );
            
            if (!popup) {
                console.warn('Popup blocked, using fallback');
                // Fallback: Prompt für Dateiname
                const filename = prompt('Bildname aus Medienpool:');
                if (filename) {
                    this.data.items[index].imageFile = filename;
                    this.data.items[index].imageUrl = `/media/${filename}`;
                    this._renderItems();
                }
                
                // Cleanup
                delete window[callbackName];
            }
        }
    }

    _updateWrapperAttributes() {
        if (!this.nodes.wrapper) return;

        this.nodes.wrapper.dataset.layout = this.data.layout;
        this.nodes.wrapper.dataset.imageSize = this.data.imageSize;
        this.nodes.wrapper.dataset.spacing = this.data.spacing;
        
        if (this.data.aspectRatio) {
            this.nodes.wrapper.dataset.aspectRatio = this.data.aspectRatio;
        } else {
            delete this.nodes.wrapper.dataset.aspectRatio;
        }

        if (this.data.showCaptions) {
            this.nodes.wrapper.classList.add('cdx-gallery--show-captions');
        } else {
            this.nodes.wrapper.classList.remove('cdx-gallery--show-captions');
        }
    }

    _addDragEvents(itemWrapper, dragHandle) {
        let draggedItem = null;
        let draggedIndex = null;

        // Drag Start
        dragHandle.addEventListener('mousedown', (e) => {
            e.preventDefault();
            draggedItem = itemWrapper;
            draggedIndex = parseInt(itemWrapper.dataset.index);
            
            // Visuelles Feedback
            itemWrapper.classList.add('dragging');
            dragHandle.style.cursor = 'grabbing';
            
            // Globale Mouse Events
            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', handleMouseUp);
        });

        const handleMouseMove = (e) => {
            if (!draggedItem) return;
            
            e.preventDefault();
            
            // Finde das Element unter dem Cursor
            const afterElement = this._getDragAfterElement(this.nodes.container, e.clientY);
            
            if (afterElement == null) {
                this.nodes.container.appendChild(draggedItem);
            } else {
                this.nodes.container.insertBefore(draggedItem, afterElement);
            }
        };

        const handleMouseUp = (e) => {
            if (!draggedItem) return;
            
            e.preventDefault();
            
            // Aufräumen
            draggedItem.classList.remove('dragging');
            dragHandle.style.cursor = 'grab';
            
            document.removeEventListener('mousemove', handleMouseMove);
            document.removeEventListener('mouseup', handleMouseUp);
            
            // Neue Reihenfolge bestimmen
            const newIndex = Array.from(this.nodes.container.children).indexOf(draggedItem);
            
            if (newIndex !== draggedIndex) {
                // Array-Reihenfolge anpassen
                const item = this.data.items.splice(draggedIndex, 1)[0];
                this.data.items.splice(newIndex, 0, item);
                
                // Items neu rendern um Indizes zu aktualisieren
                this._renderItems();
            }
            
            draggedItem = null;
            draggedIndex = null;
        };
    }

    _getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.' + this.CSS.item + ':not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
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
window.ImageGalleryBlock = ImageGalleryBlock;
