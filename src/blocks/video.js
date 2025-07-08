/**
 * Video Block für EditorJS mit REXMediaTool
 * Erweiterte Video-Funktionalität mit lokalen Videodateien
 */
class VideoBlock {
    static get toolbox() {
        return {
            title: 'Video',
            icon: '<svg width="17" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17c0 .55-.45 1-1 1s-1-.45-1-1V7c0-.55.45-1 1-1s1 .45 1 1v10zm8 0c0 .55-.45 1-1 1s-1-.45-1-1V7c0-.55.45-1 1-1s1 .45 1 1v10z"/><polygon points="8,5 19,12 8,19"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        this.config = config || {};
        
        // REXMediaTool für Video-Dateien
        this.mediaTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv'], // Nur Dateiendungen
                context: 'editorjs_video'
            }
        });

        // REXMediaTool für Poster-Bilder
        this.posterTool = new REXMediaTool({ 
            api: this.api, 
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], // Nur Bilder für Poster
                context: 'editorjs_video_poster'
            }
        });
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-video',
            videoWrapper: 'cdx-video__wrapper',
            video: 'cdx-video__video',
            poster: 'cdx-video__poster',
            caption: 'cdx-video__caption',
            button: 'cdx-video__button',
            posterButton: 'cdx-video__poster-button',
            settingsButton: 'cdx-video__settings-button',
            settingsButtonActive: 'cdx-video__settings-button--active',
            placeholder: 'cdx-video__placeholder'
        };

        this.nodes = {
            holder: null,
            wrapper: null,
            video: null,
            caption: null,
            selectButton: null,
            posterButton: null
        };

        this.data = {
            videoFile: data.videoFile || '',
            videoUrl: data.videoUrl || '',
            poster: data.poster || '',
            posterUrl: data.posterUrl || '',
            caption: data.caption || '',
            stretched: data.stretched !== undefined ? data.stretched : false,
            withBorder: data.withBorder !== undefined ? data.withBorder : false,
            withBackground: data.withBackground !== undefined ? data.withBackground : false,
            aspectRatio: data.aspectRatio || '',
            autoplay: data.autoplay !== undefined ? data.autoplay : false,
            muted: data.muted !== undefined ? data.muted : false,
            loop: data.loop !== undefined ? data.loop : false,
            controls: data.controls !== undefined ? data.controls : true
        };

        // Aspect Ratios
        this.aspectRatios = {
            '': 'Automatisch',
            '16-9': '16:9 (Widescreen)',
            '4-3': '4:3 (Standard)',
            '1-1': '1:1 (Quadrat)',
            '3-2': '3:2'
        };
    }

    render() {
        const wrapper = this._make('div', [this.CSS.wrapper]);
        const videoWrapper = this._make('div', [this.CSS.videoWrapper]);

        if (this.data.videoUrl) {
            this._createVideo(videoWrapper);
        } else {
            this._createVideoPlaceholder(videoWrapper);
        }

        this._createCaption(wrapper);
        
        wrapper.appendChild(videoWrapper);

        this.nodes.holder = wrapper;
        this.nodes.wrapper = videoWrapper;

        // Wrapper-Klassen sofort nach dem Setzen der Referenzen aktualisieren
        if (this.data.videoUrl) {
            this._updateWrapperClasses();
        }

        return wrapper;
    }

    renderSettings() {
        const wrapper = this._make('div', ['cdx-video-settings']);

        // Gestreckt-Button
        const stretchButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-arrows-alt-h"></i>',
            title: 'Video auf volle Breite strecken'
        });
        
        stretchButton.addEventListener('click', () => {
            this._toggleTune('stretched');
            stretchButton.classList.toggle(this.CSS.settingsButtonActive, this.data.stretched);
        });

        if (this.data.stretched) {
            stretchButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Rahmen-Button
        const borderButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-border-style"></i>',
            title: 'Rahmen um Video'
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
            innerHTML: '<i class="fa-solid fa-palette"></i>',
            title: 'Hintergrund um Video'
        });
        
        backgroundButton.addEventListener('click', () => {
            this._toggleTune('withBackground');
            backgroundButton.classList.toggle(this.CSS.settingsButtonActive, this.data.withBackground);
        });

        if (this.data.withBackground) {
            backgroundButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Autoplay-Button
        const autoplayButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-play-circle"></i>',
            title: 'Video automatisch abspielen'
        });
        
        autoplayButton.addEventListener('click', () => {
            this._toggleTune('autoplay');
            autoplayButton.classList.toggle(this.CSS.settingsButtonActive, this.data.autoplay);
        });

        if (this.data.autoplay) {
            autoplayButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Stumm-Button
        const mutedButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-volume-mute"></i>',
            title: 'Video stumm starten'
        });
        
        mutedButton.addEventListener('click', () => {
            this._toggleTune('muted');
            mutedButton.classList.toggle(this.CSS.settingsButtonActive, this.data.muted);
        });

        if (this.data.muted) {
            mutedButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Loop-Button
        const loopButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-repeat"></i>',
            title: 'Video in Endlosschleife'
        });
        
        loopButton.addEventListener('click', () => {
            this._toggleTune('loop');
            loopButton.classList.toggle(this.CSS.settingsButtonActive, this.data.loop);
        });

        if (this.data.loop) {
            loopButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Controls-Button
        const controlsButton = this._make('span', [this.CSS.settingsButton], {
            innerHTML: '<i class="fa-solid fa-sliders-h"></i>',
            title: 'Video-Steuerelemente anzeigen'
        });
        
        controlsButton.addEventListener('click', () => {
            this._toggleTune('controls');
            controlsButton.classList.toggle(this.CSS.settingsButtonActive, this.data.controls);
        });

        if (this.data.controls) {
            controlsButton.classList.add(this.CSS.settingsButtonActive);
        }

        // Seitenverhältnis-Dropdown (als erstes)
        const aspectRatioDropdown = this._createDropdown(
            'Seitenverhältnis',
            '<i class="fa-solid fa-crop"></i>',
            this.aspectRatios,
            this.data.aspectRatio,
            (value) => this._setAspectRatio(value)
        );

        wrapper.appendChild(aspectRatioDropdown);
        wrapper.appendChild(stretchButton);
        wrapper.appendChild(borderButton);
        wrapper.appendChild(backgroundButton);
        wrapper.appendChild(autoplayButton);
        wrapper.appendChild(mutedButton);
        wrapper.appendChild(loopButton);
        wrapper.appendChild(controlsButton);

        return wrapper;
    }

    _createVideo(container) {
        const videoWrapper = this._make('div', ['cdx-video__video-wrapper']);
        
        const video = this._make('video', [this.CSS.video], {
            src: this.data.videoUrl,
            controls: this.data.controls,
            autoplay: this.data.autoplay,
            muted: this.data.muted,
            loop: this.data.loop
        });

        if (this.data.posterUrl) {
            video.poster = this.data.posterUrl;
        }

        this.nodes.video = video;

        if (!this.readOnly) {
            video.addEventListener('click', () => {
                this._showVideoSelector();
            });
            
            // "Video ersetzen" Button hinzufügen
            const replaceButton = this._make('button', ['cdx-video__replace-button'], {
                type: 'button',
                innerHTML: '<i class="fa-solid fa-exchange-alt"></i> Video ersetzen',
                title: 'Video ersetzen'
            });

            replaceButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this._showVideoSelector();
            });

            videoWrapper.appendChild(video);
            videoWrapper.appendChild(replaceButton);
        } else {
            videoWrapper.appendChild(video);
        }

        container.appendChild(videoWrapper);
    }

    _createVideoPlaceholder(container) {
        const placeholder = this._make('div', [this.CSS.placeholder]);
        
        const icon = this._make('div', [], {
            innerHTML: '<i class="fa-solid fa-video" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>'
        });
        
        const text = this._make('div', [], {
            textContent: 'Video auswählen'
        });

        if (!this.readOnly) {
            const button = this._make('button', [this.CSS.button], {
                type: 'button',
                textContent: 'Video aus Medienpool wählen'
            });

            button.addEventListener('click', () => {
                this._showVideoSelector();
            });

            this.nodes.selectButton = button;
            placeholder.appendChild(button);
        }

        placeholder.appendChild(icon);
        placeholder.appendChild(text);
        
        container.appendChild(placeholder);
    }

    _createCaption(container) {
        const caption = this._make('div', [this.CSS.caption], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.caption || 'Video-Beschreibung eingeben...'
        });

        caption.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        caption.addEventListener('blur', () => {
            this.data.caption = caption.innerHTML;
        });

        this.nodes.caption = caption;
        container.appendChild(caption);
    }

    _showVideoSelector() {
        // Verwendung der zentralen REXMediaTool-Komponente für Videos
        this.mediaTool.openMediaPool({
            types: ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv'],
            context: 'editorjs_video'
        }, (mediaData) => {
            this.data.videoFile = mediaData.filename;
            this.data.videoUrl = `/media/${mediaData.filename}`;
            
            this._recreateVideo();
            this._updateWrapperClasses();
        }).catch(error => {
            // Nur echte Fehler anzeigen, nicht wenn der User den Dialog abbricht
            if (error.message !== 'Media pool closed without selection') {
                console.error('Fehler bei der Video-Auswahl:', error);
                if (window.EditorJSDebug) {
                    alert('Fehler beim Öffnen des Medienpools: ' + error.message);
                }
            }
            // Bei normalem Abbruch: stumm ignorieren
        });
    }

    _showPosterSelector() {
        // Verwendung der zentralen REXMediaTool-Komponente für Poster-Bilder
        this.posterTool.openMediaPool({
            types: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            context: 'editorjs_poster'
        }, (mediaData) => {
            this.data.poster = mediaData.filename;
            this.data.posterUrl = `/media/${mediaData.filename}`;
            
            if (this.nodes.video) {
                this.nodes.video.poster = this.data.posterUrl;
            }
        }).catch(error => {
            // Nur echte Fehler anzeigen, nicht wenn der User den Dialog abbricht
            if (error.message !== 'Media pool closed without selection') {
                console.error('Fehler bei der Poster-Auswahl:', error);
                if (window.EditorJSDebug) {
                    alert('Fehler beim Öffnen des Medienpools: ' + error.message);
                }
            }
            // Bei normalem Abbruch: stumm ignorieren
        });
    }

    _recreateVideo() {
        if (this.nodes.wrapper) {
            this.nodes.wrapper.innerHTML = '';
            this._createVideo(this.nodes.wrapper);
        }
    }

    _updateWrapperClasses() {
        if (!this.nodes.holder) return;

        // Alle Klassen zurücksetzen
        this.nodes.holder.className = this.CSS.wrapper;

        // Dynamische Klassen hinzufügen
        if (this.data.stretched) {
            this.nodes.holder.classList.add('cdx-video--stretched');
        }
        
        if (this.data.withBorder) {
            this.nodes.holder.classList.add('cdx-video--with-border');
        }
        
        if (this.data.withBackground) {
            this.nodes.holder.classList.add('cdx-video--with-background');
        }
        
        if (this.data.aspectRatio) {
            this.nodes.holder.classList.add(`cdx-video--aspect-${this.data.aspectRatio}`);
        }
    }

    _toggleTune(tune) {
        this.data[tune] = !this.data[tune];
        this._updateWrapperClasses();
        
        // Video-Attribute aktualisieren
        if (this.nodes.video) {
            if (tune === 'autoplay') this.nodes.video.autoplay = this.data.autoplay;
            if (tune === 'muted') this.nodes.video.muted = this.data.muted;
            if (tune === 'loop') this.nodes.video.loop = this.data.loop;
            if (tune === 'controls') this.nodes.video.controls = this.data.controls;
        }
    }

    _setAspectRatio(value) {
        this.data.aspectRatio = value;
        this._updateWrapperClasses();
    }

    _createDropdown(title, icon, options, currentValue, onSelect) {
        const dropdown = this._make('div', ['cdx-video-dropdown']);
        
        const button = this._make('span', [this.CSS.settingsButton], {
            innerHTML: icon,
            title: title
        });
        
        const menu = this._make('div', ['cdx-video-dropdown-menu']);
        
        Object.entries(options).forEach(([value, label]) => {
            const item = this._make('div', ['cdx-video-dropdown-item'], {
                textContent: label,
                'data-value': value
            });
            
            if (value === currentValue) {
                item.classList.add('cdx-video-dropdown-item--active');
                button.classList.add(this.CSS.settingsButtonActive);
            }
            
            item.addEventListener('click', () => {
                // Alle Items deaktivieren
                menu.querySelectorAll('.cdx-video-dropdown-item').forEach(i => {
                    i.classList.remove('cdx-video-dropdown-item--active');
                });
                
                // Aktuelles Item aktivieren
                item.classList.add('cdx-video-dropdown-item--active');
                
                // Button-Status aktualisieren
                if (value === '') {
                    button.classList.remove(this.CSS.settingsButtonActive);
                } else {
                    button.classList.add(this.CSS.settingsButtonActive);
                }
                
                onSelect(value);
                dropdown.classList.remove('cdx-video-dropdown--open');
            });
            
            menu.appendChild(item);
        });
        
        button.addEventListener('click', () => {
            dropdown.classList.toggle('cdx-video-dropdown--open');
        });
        
        dropdown.appendChild(button);
        dropdown.appendChild(menu);
        
        return dropdown;
    }

    _make(tagName, classNames = [], attributes = {}) {
        const el = document.createElement(tagName);

        if (Array.isArray(classNames)) {
            el.classList.add(...classNames);
        } else {
            el.classList.add(classNames);
        }

        for (const attrName in attributes) {
            if (attrName === 'innerHTML') {
                el.innerHTML = attributes[attrName];
            } else if (attrName === 'textContent') {
                el.textContent = attributes[attrName];
            } else {
                el.setAttribute(attrName, attributes[attrName]);
            }
        }

        return el;
    }

    save() {
        return this.data;
    }

    validate(savedData) {
        return savedData.videoFile || savedData.videoUrl;
    }
}

// VideoBlock global verfügbar machen
window.VideoBlock = VideoBlock;
