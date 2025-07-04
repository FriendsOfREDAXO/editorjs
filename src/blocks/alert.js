/**
 * Alert Block für EditorJS
 * Ein einfacher Alert-Block mit verschiedenen Typen (info, warning, error, success)
 */
class AlertBlock {
    static get toolbox() {
        return {
            title: 'Alert',
            icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 106-71z"/><path d="M79 213h178c19 0 34-15 34-34v-42l-106 71-81-72-67 44v33c0 19 15 34 34 34z"/></svg>'
        };
    }

    static get isReadOnlySupported() {
        return true;
    }

    constructor({ data, config, api, readOnly }) {
        this.api = api;
        this.readOnly = readOnly;
        
        this.CSS = {
            baseClass: this.api.styles.block,
            wrapper: 'cdx-alert',
            title: 'cdx-alert__title',
            message: 'cdx-alert__message',
            settingsButton: 'cdx-alert__settings-button',
            settingsButtonActive: 'cdx-alert__settings-button--active'
        };

        this.nodes = {
            holder: null,
            title: null,
            message: null
        };

        this.data = {
            type: data.type || 'info',
            title: data.title || '',
            message: data.message || ''
        };

        this.types = {
            info: {
                icon: '<i class="fa-solid fa-info-circle"></i>',
                title: 'Info'
            },
            warning: {
                icon: '<i class="fa-solid fa-exclamation-triangle"></i>',
                title: 'Warnung'
            },
            error: {
                icon: '<i class="fa-solid fa-times-circle"></i>',
                title: 'Fehler'
            },
            success: {
                icon: '<i class="fa-solid fa-check-circle"></i>',
                title: 'Erfolg'
            }
        };
    }

    render() {
        const holder = this._make('div', [this.CSS.baseClass, this.CSS.wrapper]);
        const title = this._make('div', [this.CSS.title], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.title || this.types[this.data.type].title
        });
        const message = this._make('div', [this.CSS.message], {
            contentEditable: !this.readOnly,
            innerHTML: this.data.message || 'Geben Sie hier Ihre Nachricht ein...'
        });

        holder.dataset.type = this.data.type;
        holder.appendChild(title);
        holder.appendChild(message);

        this.nodes.holder = holder;
        this.nodes.title = title;
        this.nodes.message = message;

        return holder;
    }

    renderSettings() {
        const wrapper = this._make('div');

        Object.entries(this.types).forEach(([type, config]) => {
            const button = this._make('span', [this.CSS.settingsButton], {
                innerHTML: config.icon + ' ' + config.title
            });

            button.addEventListener('click', () => {
                this._toggleType(type);
                
                // Update active state
                wrapper.querySelectorAll('.' + this.CSS.settingsButton).forEach(btn => {
                    btn.classList.remove(this.CSS.settingsButtonActive);
                });
                button.classList.add(this.CSS.settingsButtonActive);
            });

            if (type === this.data.type) {
                button.classList.add(this.CSS.settingsButtonActive);
            }

            wrapper.appendChild(button);
        });

        return wrapper;
    }

    save(blockContent) {
        const title = blockContent.querySelector('.' + this.CSS.title);
        const message = blockContent.querySelector('.' + this.CSS.message);

        return {
            type: this.data.type,
            title: title.innerHTML,
            message: message.innerHTML
        };
    }

    static get sanitize() {
        return {
            type: {},
            title: {
                br: true,
                strong: true,
                em: true
            },
            message: {
                br: true,
                strong: true,
                em: true,
                a: {
                    href: true,
                    target: '_blank'
                }
            }
        };
    }

    _toggleType(type) {
        this.data.type = type;
        this.nodes.holder.dataset.type = type;
        
        if (this.nodes.title.innerHTML === '' || Object.values(this.types).some(t => this.nodes.title.innerHTML === t.title)) {
            this.nodes.title.innerHTML = this.types[type].title;
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
window.AlertBlock = AlertBlock;
