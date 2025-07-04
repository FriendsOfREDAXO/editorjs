/**
 * Downloads Repeater Block für EditorJS
 * Ermöglicht das Hinzufügen mehrerer Downloads (PDFs, Bilder) aus dem REDAXO Medienpool
 */
class DownloadsBlock {
    static get toolbox() {
        return {
            title: 'Downloads',
            icon: '<svg width="17" height="15" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        
        this.CSS = {
            wrapper: 'cdx-downloads',
            container: 'cdx-downloads__container',
            item: 'cdx-downloads__item',
            itemHeader: 'cdx-downloads__item-header',
            itemContent: 'cdx-downloads__item-content',
            itemActions: 'cdx-downloads__item-actions',
            fileSelect: 'cdx-downloads__file-select',
            filePreview: 'cdx-downloads__file-preview',
            fileIcon: 'cdx-downloads__file-icon',
            fileInfo: 'cdx-downloads__file-info',
            fileName: 'cdx-downloads__file-name',
            fileSize: 'cdx-downloads__file-size',
            titleInput: 'cdx-downloads__title-input',
            descriptionInput: 'cdx-downloads__description-input',
            addButton: 'cdx-downloads__add-button',
            removeButton: 'cdx-downloads__remove-button',
            dragHandle: 'cdx-downloads__drag-handle',
            settingsButton: 'cdx-downloads__settings-button',
            settingsButtonActive: 'cdx-downloads__settings-button--active',
            thumb: 'cdx-downloads__thumb'
        };

        this.nodes = {
            wrapper: null,
            container: null
        };

        this.data = {
            title: data.title || 'Downloads',
            items: data.items || [this._createEmptyItem()],
            showTitle: data.showTitle !== false,
            layout: data.layout || 'list' // list, grid, compact
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

        // Titel-Bereich
        if (this.data.showTitle) {
            const titleContainer = this._make('div', 'cdx-downloads__title-container');
            const titleInput = this._make('input', [this.CSS.titleInput], {
                type: 'text',
                placeholder: 'Downloads Titel...',
                value: this.data.title
            });

            titleInput.addEventListener('input', (e) => {
                this.data.title = e.target.value;
            });

            titleContainer.appendChild(titleInput);
            this.nodes.wrapper.appendChild(titleContainer);
        }

        // Container für Download-Items
        this.nodes.container = this._make('div', [this.CSS.container]);
        this.nodes.wrapper.appendChild(this.nodes.container);

        // Items rendern
        this._renderItems();

        // Add Button
        const addButton = this._make('button', [this.CSS.addButton], {
            innerHTML: '<i class="fa-solid fa-plus"></i> Download hinzufügen',
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
            this.nodes.wrapper.remove();
            this.nodes.wrapper = this.render();
            this.api.blocks.getBlockByIndex(this.api.blocks.getCurrentBlockIndex()).holder.appendChild(this.nodes.wrapper);
        });

        titleToggle.appendChild(titleLabel);
        wrapper.appendChild(titleToggle);

        return wrapper;
    }

    save(blockContent) {
        const titleInput = blockContent.querySelector('.' + this.CSS.titleInput);
        
        return {
            title: titleInput ? titleInput.value : this.data.title,
            items: this.data.items,
            showTitle: this.data.showTitle,
            layout: this.data.layout
        };
    }

    static get sanitize() {
        return {
            title: {},
            items: {},
            showTitle: {},
            layout: {}
        };
    }

    _createEmptyItem() {
        return {
            file: '',
            title: '',
            description: '',
            customIcon: ''
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

        // Header mit Drag Handle und Remove Button
        const header = this._make('div', [this.CSS.itemHeader]);
        
        const dragHandle = this._make('span', [this.CSS.dragHandle], {
            innerHTML: '<i class="fa-solid fa-grip-vertical"></i>',
            title: 'Zum Sortieren ziehen'
        });

        const removeButton = this._make('button', [this.CSS.removeButton], {
            innerHTML: '<i class="fa-solid fa-trash"></i>',
            title: 'Download entfernen',
            type: 'button'
        });

        removeButton.addEventListener('click', () => {
            this._removeItem(index);
        });

        header.appendChild(dragHandle);
        header.appendChild(removeButton);

        // Content
        const content = this._make('div', [this.CSS.itemContent]);

        // File Selection Area
        const fileSelect = this._createFileSelectArea(item, index);
        content.appendChild(fileSelect);

        // Title Input
        const titleInput = this._make('input', [this.CSS.titleInput], {
            type: 'text',
            placeholder: 'Download Titel (optional)',
            value: item.title
        });

        titleInput.addEventListener('input', (e) => {
            this.data.items[index].title = e.target.value;
        });

        content.appendChild(titleInput);

        // Description Input
        const descriptionInput = this._make('textarea', [this.CSS.descriptionInput], {
            placeholder: 'Beschreibung (optional)',
            value: item.description,
            rows: 2
        });

        descriptionInput.addEventListener('input', (e) => {
            this.data.items[index].description = e.target.value;
        });

        content.appendChild(descriptionInput);

        itemWrapper.appendChild(header);
        itemWrapper.appendChild(content);

        return itemWrapper;
    }

    _createFileSelectArea(item, index) {
        const fileSelect = this._make('div', [this.CSS.fileSelect]);

        if (item.file) {
            // File selected - show preview
            const preview = this._createFilePreview(item);
            fileSelect.appendChild(preview);
        } else {
            // No file selected - show select button
            const selectButton = this._make('button', 'cdx-downloads__select-button', {
                innerHTML: '<i class="fa-solid fa-folder-open"></i> Datei aus Medienpool wählen',
                type: 'button'
            });

            selectButton.addEventListener('click', () => {
                this._openMediaPool(index);
            });

            fileSelect.appendChild(selectButton);
        }

        return fileSelect;
    }

    _createFilePreview(item) {
        const preview = this._make('div', [this.CSS.filePreview]);

        // Icon oder Thumbnail
        const iconContainer = this._make('div', [this.CSS.fileIcon]);
        
        if (this._isImage(item.file)) {
            // Thumbnail für Bilder
            const thumb = this._make('img', [this.CSS.thumb], {
                src: `/media/${item.file}`,
                alt: item.title || item.file
            });
            iconContainer.appendChild(thumb);
        } else {
            // Icon für andere Dateitypen
            const icon = this._getFileIcon(item.file);
            iconContainer.innerHTML = icon;
        }

        // File Info
        const fileInfo = this._make('div', [this.CSS.fileInfo]);
        
        const fileName = this._make('div', [this.CSS.fileName], {
            textContent: item.file
        });

        const fileSize = this._make('div', [this.CSS.fileSize], {
            textContent: this._getFileSize(item.file)
        });

        fileInfo.appendChild(fileName);
        fileInfo.appendChild(fileSize);

        // Actions
        const actions = this._make('div', [this.CSS.itemActions]);
        
        const changeButton = this._make('button', 'cdx-downloads__change-button', {
            innerHTML: '<i class="fa-solid fa-sync"></i>',
            title: 'Datei ändern',
            type: 'button'
        });

        changeButton.addEventListener('click', () => {
            this._openMediaPool(this._getItemIndex(preview));
        });

        actions.appendChild(changeButton);

        preview.appendChild(iconContainer);
        preview.appendChild(fileInfo);
        preview.appendChild(actions);

        return preview;
    }

    _openMediaPool(index) {
        console.log('Opening media pool for index:', index);
        
        // Erstelle eindeutige Callback-Funktion
        const callbackName = 'downloadsBlockCallback_' + Date.now() + '_' + index;
        
        // Globale Callback-Funktion für REDAXO
        window[callbackName] = (filename) => {
            console.log('Global callback triggered for index', index, 'with filename:', filename);
            
            if (filename) {
                this.data.items[index].file = filename;
                console.log('Updated item data:', this.data.items[index]);
                this._renderItems();
            }
            
            // Cleanup
            delete window[callbackName];
        };
        
        // Versuch 1: rex_selectMedia (Standard REDAXO Funktion)
        if (typeof rex_selectMedia !== 'undefined') {
            console.log('Using rex_selectMedia function');
            // Alle Dateitypen erlauben - kein Filter auf Bildtypen
            rex_selectMedia(callbackName, '');
        }
        // Versuch 2: REXMediaTool
        else if (typeof window.REXMediaTool !== 'undefined') {
            console.log('REXMediaTool available, calling openMediaPool...');
            
            // Explizit alle Dateitypen erlauben für Downloads
            const downloadOptions = {
                types: null, // null = alle Dateitypen erlauben
                context: 'editorjs_downloads'
            };
            
            window.REXMediaTool.openMediaPool(downloadOptions, (mediaData) => {
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
                    this.data.items[index].file = filename;
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
            const params = 'editorjs_downloads&callback=' + callbackName;
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
                const filename = prompt('Dateiname aus Medienpool:');
                if (filename) {
                    this.data.items[index].file = filename;
                    this._renderItems();
                }
                
                // Cleanup
                delete window[callbackName];
            }
        }
    }

    _getFileIcon(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        
        const iconMap = {
            // PDF
            'pdf': '<i class="fa-solid fa-file-pdf" style="color: #dc3545;"></i>',
            
            // Images
            'jpg': '<i class="fa-solid fa-file-image" style="color: #17a2b8;"></i>',
            'jpeg': '<i class="fa-solid fa-file-image" style="color: #17a2b8;"></i>',
            'png': '<i class="fa-solid fa-file-image" style="color: #17a2b8;"></i>',
            'gif': '<i class="fa-solid fa-file-image" style="color: #17a2b8;"></i>',
            'svg': '<i class="fa-solid fa-file-image" style="color: #17a2b8;"></i>',
            'webp': '<i class="fa-solid fa-file-image" style="color: #17a2b8;"></i>',
            
            // Documents
            'doc': '<i class="fa-solid fa-file-word" style="color: #2c5aa0;"></i>',
            'docx': '<i class="fa-solid fa-file-word" style="color: #2c5aa0;"></i>',
            'xls': '<i class="fa-solid fa-file-excel" style="color: #1d6f42;"></i>',
            'xlsx': '<i class="fa-solid fa-file-excel" style="color: #1d6f42;"></i>',
            'ppt': '<i class="fa-solid fa-file-powerpoint" style="color: #d04423;"></i>',
            'pptx': '<i class="fa-solid fa-file-powerpoint" style="color: #d04423;"></i>',
            
            // Archive
            'zip': '<i class="fa-solid fa-file-zipper" style="color: #6c757d;"></i>',
            'rar': '<i class="fa-solid fa-file-zipper" style="color: #6c757d;"></i>',
            '7z': '<i class="fa-solid fa-file-zipper" style="color: #6c757d;"></i>',
            
            // Text
            'txt': '<i class="fa-solid fa-file-lines" style="color: #6c757d;"></i>',
            'rtf': '<i class="fa-solid fa-file-lines" style="color: #6c757d;"></i>',
            
            // Audio/Video
            'mp3': '<i class="fa-solid fa-file-audio" style="color: #ff6b35;"></i>',
            'wav': '<i class="fa-solid fa-file-audio" style="color: #ff6b35;"></i>',
            'mp4': '<i class="fa-solid fa-file-video" style="color: #ff6b35;"></i>',
            'avi': '<i class="fa-solid fa-file-video" style="color: #ff6b35;"></i>'
        };
        
        return iconMap[extension] || '<i class="fa-solid fa-file" style="color: #6c757d;"></i>';
    }

    _isImage(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
        return imageExtensions.includes(extension);
    }

    _getFileSize(filename) {
        // In einer echten Implementierung würde man die Dateigröße vom Server holen
        return 'Dateigröße unbekannt';
    }

    _getItemIndex(element) {
        const item = element.closest('.' + this.CSS.item);
        return parseInt(item.dataset.index);
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
window.DownloadsBlock = DownloadsBlock;
