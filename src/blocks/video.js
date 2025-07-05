/**
 * Video Block for Editor.js with REXMediaTool
 *
 * This block is a complete rewrite based on the solid foundation of the ImageBlock.
 * It provides a robust video integration for REDAXO, including features like
 * poster images, subtitles, and various playback and layout settings.
 */
import REXMediaTool from './rexmedia.js';

class VideoBlock {
    static get toolbox() {
        return {
            title: 'Video',
            icon: '<svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 3H3C1.9 3 1 3.9 1 5v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM12 15.5l-4-4v8l4-4zm1-4.5H9V9h4v2z"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        this.config = config || {};

        // REXMediaTool for media pool integration
        this.mediaTool = new REXMediaTool({
            api: this.api,
            config: {
                types: ['mp4', 'webm', 'ogv'], // Video files
                context: 'editorjs_video'
            }
        });
        this.posterTool = new REXMediaTool({
            api: this.api,
            config: {
                types: ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], // Image files
                context: 'editorjs_video_poster'
            }
        });
        this.subtitleTool = new REXMediaTool({
            api: this.api,
            config: {
                types: ['vtt'], // VTT subtitle files
                context: 'editorjs_video_subtitles'
            }
        });

        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-video',
            videoWrapper: 'cdx-video__wrapper',
            video: 'cdx-video__video',
            caption: 'cdx-video__caption',
            button: 'cdx-video__button',
            settingsButton: 'cdx-video__settings-button',
            settingsButtonActive: 'cdx-video__settings-button--active'
        };

        this.nodes = {
            holder: null,
            wrapper: null,
            video: null,
            caption: null,
            selectButton: null
        };

        this.data = {
            videoFile: data.videoFile || '',
            videoUrl: data.videoUrl || '',
            posterFile: data.posterFile || '',
            posterUrl: data.posterUrl || '',
            subtitleFile: data.subtitleFile || '',
            subtitleUrl: data.subtitleUrl || '',
            caption: data.caption || '',
            stretched: data.stretched || false,
            autoplay: data.autoplay || false,
            loop: data.loop || false,
            muted: data.muted || false,
            controls: data.controls === undefined ? true : data.controls,
            aspectRatio: data.aspectRatio || 'auto',
        };

        this.aspectRatios = {
            'auto': { title: 'Automatisch', icon: '<i class="fa-solid fa-expand-arrows-alt"></i>' },
            '16-9': { title: '16:9', icon: '<i class="fa-solid fa-tv"></i>' },
            '4-3': { title: '4:3', icon: '<i class="fa-solid fa-desktop"></i>' },
            '1-1': { title: '1:1', icon: '<i class="fa-solid fa-square"></i>' },
            '21-9': { title: '21:9', icon: '<i class="fa-solid fa-panorama"></i>' }
        };
    }

    render() {
        const wrapper = this._make('div', [this.CSS.baseClass, this.CSS.wrapper]);
        const videoWrapper = this._make('div', [this.CSS.videoWrapper]);

        if (this.data.videoUrl) {
            this._createVideo();
            videoWrapper.appendChild(this.nodes.video);
            this._updateCaption(); // Call this to create caption element if data exists
        } else {
            this._createSelectButton();
            videoWrapper.appendChild(this.nodes.selectButton);
        }

        wrapper.appendChild(videoWrapper);

        this.nodes.holder = wrapper;
        this.nodes.wrapper = videoWrapper;

        if (this.data.videoUrl) {
            this._updateWrapperClasses();
        }

        return wrapper;
    }

    renderSettings() {
        const wrapper = this._make('div', ['cdx-video-settings']);
        const settingsGroup1 = this._make('div', ['cdx-settings-group']);
        const settingsGroup2 = this._make('div', ['cdx-settings-group']);
        const settingsGroup3 = this._make('div', ['cdx-settings-group']);

        // Group 1: File Management
        const changeVideoButton = this._createSettingButton('video', 'Video ändern', () => this._openMediapool('video'));
        const posterButton = this._createSettingButton('image', 'Posterbild', () => this._openMediapool('poster'), this.data.posterUrl);
        const subtitleButton = this._createSettingButton('closed-captioning', 'Untertitel (VTT)', () => this._openMediapool('subtitle'), this.data.subtitleUrl);
        const captionButton = this._createSettingButton('comment-dots', 'Beschreibung', () => this._editCaption(), this.data.caption);
        settingsGroup1.append(changeVideoButton, posterButton, subtitleButton, captionButton);

        // Group 2: Layout
        const stretchButton = this._createSettingButton('arrows-alt-h', 'Gestreckt', () => this._toggleTune('stretched'), this.data.stretched);
        const aspectRatioDropdown = this._createDropdown('Seitenverhältnis', 'crop-simple', this.aspectRatios, this.data.aspectRatio, (value) => this._setAspectRatio(value));
        settingsGroup2.append(stretchButton, aspectRatioDropdown.element);

        // Group 3: Playback
        const autoplayButton = this._createSettingButton('play', 'Autoplay', () => this._toggleTune('autoplay'), this.data.autoplay);
        const loopButton = this._createSettingButton('repeat', 'Loop', () => this._toggleTune('loop'), this.data.loop);
        const mutedButton = this._createSettingButton('volume-xmark', 'Stumm', () => this._toggleTune('muted'), this.data.muted);
        const controlsButton = this._createSettingButton('sliders', 'Steuerung', () => this._toggleTune('controls'), this.data.controls);
        settingsGroup3.append(autoplayButton, loopButton, mutedButton, controlsButton);

        wrapper.append(settingsGroup1, settingsGroup2, settingsGroup3);
        return wrapper;
    }

    save() {
        return this.data;
    }

    static get sanitize() {
        return {
            caption: { br: true, strong: true, em: true, a: { href: true } },
            videoFile: {}, videoUrl: {},
            posterFile: {}, posterUrl: {},
            subtitleFile: {}, subtitleUrl: {},
            stretched: {}, autoplay: {}, loop: {},
            muted: {}, controls: {}, aspectRatio: {},
        };
    }

    _createVideo() {
        const video = this._make('video', [this.CSS.video], {
            src: this.data.videoUrl,
            poster: this.data.posterUrl,
            controls: this.data.controls,
            autoplay: this.data.autoplay,
            loop: this.data.loop,
            muted: this.data.muted,
            playsinline: true
        });

        if (this.data.subtitleUrl) {
            this._addSubtitleTrack(video);
        }

        this.nodes.video = video;
    }

    _createSelectButton() {
        const button = this._make('div', [this.CSS.button], {
            innerHTML: '<i class="fa-solid fa-video"></i> Video aus Medienpool wählen'
        });
        button.addEventListener('click', () => this._openMediapool('video'));
        this.nodes.selectButton = button;
    }

    _openMediapool(type) {
        let tool, callback;

        switch (type) {
            case 'video':
                tool = this.mediaTool;
                callback = (mediaData) => this._setVideo(mediaData);
                break;
            case 'poster':
                tool = this.posterTool;
                callback = (mediaData) => this._setPoster(mediaData);
                break;
            case 'subtitle':
                tool = this.subtitleTool;
                callback = (mediaData) => this._setSubtitle(mediaData);
                break;
        }

        tool.openMediaPool({}, callback).catch(error => {
            this.api.notifier.show({
                message: `Fehler bei der Auswahl: ${error.message}`,
                style: 'error'
            });
        });
    }

    _setVideo(mediaData) {
        this.data.videoFile = mediaData.filename;
        this.data.videoUrl = `/media/${mediaData.filename}`;

        if (this.nodes.selectButton) {
            this.nodes.selectButton.remove();
            this.nodes.selectButton = null;
        }

        if (this.nodes.video) {
            this.nodes.video.remove();
        }

        this._createVideo();
        this.nodes.wrapper.insertBefore(this.nodes.video, this.nodes.wrapper.firstChild);
        this._updateWrapperClasses();
        this._updateCaption();
    }

    _setPoster(mediaData) {
        this.data.posterFile = mediaData.filename;
        this.data.posterUrl = `/media/${mediaData.filename}`;
        if (this.nodes.video) {
            this.nodes.video.poster = this.data.posterUrl;
        }
        this.api.toolbar.close(); // Force re-render of settings to show active state
    }

    _setSubtitle(mediaData) {
        this.data.subtitleFile = mediaData.filename;
        this.data.subtitleUrl = `/media/${mediaData.filename}`;
        if (this.nodes.video) {
            this._addSubtitleTrack(this.nodes.video);
        }
        this.api.toolbar.close();
    }
    
    _addSubtitleTrack(videoElement) {
        // Remove old track if it exists
        const oldTrack = videoElement.querySelector('track');
        if (oldTrack) oldTrack.remove();

        if (this.data.subtitleUrl) {
            const track = this._make('track', [], {
                src: this.data.subtitleUrl,
                kind: 'subtitles',
                srclang: 'de', // Can be made configurable
                label: 'Deutsch',
                default: true
            });
            videoElement.appendChild(track);
        }
    }

    _toggleTune(tune) {
        this.data[tune] = !this.data[tune];
        if (this.nodes.video && typeof this.nodes.video[tune] === 'boolean') {
            this.nodes.video[tune] = this.data[tune];
        }
        if (tune === 'stretched') {
            this._updateWrapperClasses();
        }
    }

    _setAspectRatio(ratio) {
        this.data.aspectRatio = ratio;
        this._updateWrapperClasses();
    }

    _updateWrapperClasses() {
        if (!this.nodes.wrapper) return;

        // Reset aspect ratio classes
        Object.keys(this.aspectRatios).forEach(r => {
            this.nodes.wrapper.classList.remove('aspect-' + r);
        });

        if (this.data.aspectRatio !== 'auto') {
            this.nodes.wrapper.classList.add('aspect-' + this.data.aspectRatio);
        }

        this.nodes.wrapper.classList.toggle('stretched', this.data.stretched);
    }

    _editCaption() {
        const newCaption = prompt('Beschreibung eingeben:', this.data.caption);
        if (newCaption !== null) {
            this.data.caption = newCaption;
            this._updateCaption();
            this.api.toolbar.close();
        }
    }

    _updateCaption() {
        if (this.data.caption) {
            if (!this.nodes.caption) {
                const caption = this._make('div', [this.CSS.caption], { contentEditable: !this.readOnly });
                this.nodes.wrapper.appendChild(caption);
                this.nodes.caption = caption;
                caption.addEventListener('blur', (event) => {
                    this.data.caption = event.target.innerHTML;
                });
            }
            this.nodes.caption.innerHTML = this.data.caption;
        } else {
            if (this.nodes.caption) {
                this.nodes.caption.remove();
                this.nodes.caption = null;
            }
        }
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
    
    _createSettingButton(icon, title, onClick, isActive = false) {
        const button = this._make('span', [this.CSS.settingsButton], {
            innerHTML: `<i class="fa-solid fa-${icon}"></i>`,
            title: title
        });
        button.addEventListener('click', onClick);
        button.classList.toggle(this.CSS.settingsButtonActive, !!isActive);
        return button;
    }

    _createDropdown(label, icon, options, currentValue, onChange) {
        const dropdown = this._make('div', ['cdx-video-dropdown']);
        const button = this._make('span', [this.CSS.settingsButton, 'cdx-video-dropdown__button'], {
            innerHTML: `<i class="fa-solid fa-${icon}"></i> ` + (options[currentValue]?.title || 'Wählen'),
            title: label
        });
        const menu = this._make('div', ['cdx-video-dropdown__menu']);

        Object.entries(options).forEach(([value, config]) => {
            const item = this._make('div', ['cdx-video-dropdown__item'], {
                innerHTML: config.icon + ' ' + config.title
            });
            if (value === currentValue) {
                item.classList.add('cdx-video-dropdown__item--active');
            }
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                menu.querySelectorAll('.cdx-video-dropdown__item').forEach(i => i.classList.remove('cdx-video-dropdown__item--active'));
                item.classList.add('cdx-video-dropdown__item--active');
                button.innerHTML = `<i class="fa-solid fa-${icon}"></i> ` + config.title;
                dropdown.classList.remove('cdx-video-dropdown--open');
                onChange(value);
            });
            menu.appendChild(item);
        });

        dropdown.appendChild(button);
        dropdown.appendChild(menu);

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('cdx-video-dropdown--open');
        });

        document.addEventListener('click', (event) => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('cdx-video-dropdown--open');
            }
        });

        return { element: dropdown };
    }
}

export default VideoBlock;
