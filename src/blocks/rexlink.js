/**
 * REDAXO Link Tool für EditorJS
 * Unterstützt interne Links (Linkmap) und externe Links
 */
export default class RexLink {
    static get title() {
        return 'REDAXO Link';
    }

    static get icon() {
        return '<svg width="15" height="14" viewBox="0 0 15 14" xmlns="http://www.w3.org/2000/svg"><path d="M11.84 1.16a3.25 3.25 0 0 0-4.6 0L5.5 2.9a.5.5 0 0 0 .7.71l1.74-1.74a2.25 2.25 0 0 1 3.18 3.18L9.38 6.79a2.25 2.25 0 0 1-3.18 0 .5.5 0 0 0-.71.7 3.25 3.25 0 0 0 4.6 0l1.75-1.74a3.25 3.25 0 0 0 0-4.6z"/><path d="M6.21 7.21a2.25 2.25 0 0 1 3.18 0 .5.5 0 0 0 .71-.7 3.25 3.25 0 0 0-4.6 0L3.76 8.25a3.25 3.25 0 0 0 0 4.6 3.25 3.25 0 0 0 4.6 0L10.1 11.1a.5.5 0 0 0-.7-.71L7.66 12.13a2.25 2.25 0 0 1-3.18-3.18L6.21 7.21z"/></svg>';
    }

    static get isInline() {
        return true;
    }

    static get sanitize() {
        return {
            a: {
                href: true,
                target: '_blank',
                rel: 'noopener'
            }
        };
    }

    constructor({ api, config }) {
        this.api = api;
        this.config = config || {};
        
        this.button = null;
        this.actions = [
            {
                name: 'internal',
                icon: '<i class="rex-icon rex-icon-open-linkmap"></i>',
                title: 'Interner Link (Linkmap)'
            },
            {
                name: 'external',
                icon: '<i class="re-icon-link"></i>',
                title: 'Externer Link'
            },
            {
                name: 'unlink',
                icon: '<svg width="15" height="14" viewBox="0 0 15 14"><path d="M8.5 2.9L6.76 1.16a3.25 3.25 0 0 0-4.6 4.6L3.9 7.5a.5.5 0 0 0 .71-.7L2.87 5.05a2.25 2.25 0 0 1 3.18-3.18L7.79 3.61a.5.5 0 0 0 .71-.7zM11.1 6.5L9.36 8.24a.5.5 0 0 0 .71.7l1.74-1.74a2.25 2.25 0 0 1-3.18 3.18L6.89 8.64a.5.5 0 0 0-.71.7l1.74 1.74a3.25 3.25 0 0 0 4.6-4.6L10.78 4.74a.5.5 0 0 0-.71.7L11.1 6.5z"/><path d="M3 11L11 3" stroke="currentColor" stroke-width="1.5"/></svg>',
                title: 'Link entfernen'
            }
        ];
    }

    render() {
        this.button = document.createElement('button');
        this.button.type = 'button';
        this.button.innerHTML = REXLinkTool.icon;
        this.button.classList.add(this.api.styles.inlineToolButton);
        this.button.title = 'REDAXO Link';

        return this.button;
    }

    surround(range) {
        if (!range) {
            return;
        }

        // Speichere die aktuelle Selection
        const selection = window.getSelection();
        const savedRange = range.cloneRange();
        
        // Prüfe ob bereits ein Link vorhanden ist
        const parentAnchor = this.api.selection.findParentTag('A');
        
        if (parentAnchor) {
            // Link entfernen
            this.unwrap(parentAnchor);
        } else {
            // Neuen Link erstellen - zeige Dialog aber behalte Selection
            this.showLinkDialog(savedRange);
        }
    }

    showLinkDialog(range) {
        // Erstelle ein modales Dialog
        const modal = this.createModal();
        
        // Interne Link-Option
        const internalButton = this.createActionButton(this.actions[0]);
        internalButton.addEventListener('click', () => {
            modal.remove();
            this.openInternalLinkDialog(range);
        });
        
        // Externe Link-Option
        const externalButton = this.createActionButton(this.actions[1]);
        externalButton.addEventListener('click', () => {
            modal.remove();
            this.openExternalLinkDialog(range);
        });
        
        const content = modal.querySelector('.modal-content');
        content.appendChild(internalButton);
        content.appendChild(externalButton);
        
        document.body.appendChild(modal);
    }

    createModal() {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        
        const content = document.createElement('div');
        content.className = 'modal-content';
        content.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            display: flex;
            gap: 15px;
            align-items: center;
        `;
        
        const title = document.createElement('div');
        title.textContent = 'Link-Typ wählen:';
        title.style.cssText = `
            font-weight: bold;
            margin-right: 10px;
        `;
        
        content.appendChild(title);
        modal.appendChild(content);
        
        // Schließen bei Klick außerhalb
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        return modal;
    }

    openInternalLinkDialog(range) {
        if (typeof openLinkMap === 'undefined') {
            alert('Linkmap ist nicht verfügbar');
            return;
        }

        const selectedText = range.toString();
        const self = this;
        let clangParam = '';
        if (typeof rex !== 'undefined' && rex.redactor_rex_clang_getCurrentId) {
            clangParam = '&clang=' + rex.redactor_rex_clang_getCurrentId;
        }

        const linkMap = openLinkMap('', clangParam);
        
        // Event Listener für die Link-Auswahl
        if (typeof $ !== 'undefined') {
            // jQuery Version (falls verfügbar)
            $(linkMap).on('rex:selectLink', function(event, url, label) {
                event.preventDefault();
                linkMap.close();
                
                // Bereinige das Label
                if (label && typeof rex !== 'undefined' && rex.redactor_regex_id) {
                    label = label.replace(new RegExp(rex.redactor_regex_id, 'gi'), "$1");
                }
                
                const linkText = selectedText || label || url;
                self.wrap(range, linkText, url);
            });
        } else {
            // Vanilla JS Fallback
            linkMap.addEventListener('rex:selectLink', function(event) {
                event.preventDefault();
                const url = event.detail.url || event.url;
                let label = event.detail.label || event.label;
                linkMap.close();
                
                if (label && typeof rex !== 'undefined' && rex.redactor_regex_id) {
                    label = label.replace(new RegExp(rex.redactor_regex_id, 'gi'), "$1");
                }
                
                const linkText = selectedText || label || url;
                self.wrap(range, linkText, url);
            });
        }
    }

    openExternalLinkDialog(range) {
        const selectedText = range.toString();
        const url = prompt('Link-URL eingeben:', 'https://');
        if (url && url.trim() !== '' && url !== 'https://') {
            const linkText = selectedText || url;
            this.wrap(range, linkText, url);
        }
    }

    wrap(range, text, url) {
        // Stelle sicher, dass der Range noch gültig ist
        const selection = window.getSelection();
        
        // Wähle den Range wieder aus
        selection.removeAllRanges();
        selection.addRange(range);
        
        const link = document.createElement('a');
        link.href = url;
        
        // Verwende den ausgewählten Text oder den bereitgestellten Text
        const selectedText = range.toString();
        link.textContent = selectedText || text;
        
        // Externe Links öffnen in neuem Tab
        if (url.startsWith('http') && !url.includes(window.location.hostname)) {
            link.target = '_blank';
            link.rel = 'noopener';
        }

        try {
            // Entferne den Inhalt und ersetze ihn durch den Link
            range.deleteContents();
            range.insertNode(link);
            
            // Setze den Cursor nach dem Link
            range.setStartAfter(link);
            range.collapse(true);
            selection.removeAllRanges();
            selection.addRange(range);
        } catch (error) {
            console.warn('Fehler beim Einfügen des Links:', error);
        }
    }

    unwrap(linkElement) {
        const parent = linkElement.parentNode;
        const textNode = document.createTextNode(linkElement.textContent);
        parent.replaceChild(textNode, linkElement);
    }

    createPopup() {
        const popup = document.createElement('div');
        popup.style.cssText = `
            position: absolute;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 8px;
            z-index: 1000;
            display: flex;
            gap: 8px;
        `;
        return popup;
    }

    createActionButton(action) {
        const button = document.createElement('button');
        button.type = 'button';
        button.innerHTML = action.icon;
        button.title = action.title;
        button.style.cssText = `
            border: none;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
        `;
        
        button.addEventListener('mouseenter', () => {
            button.style.backgroundColor = '#e9ecef';
        });
        
        button.addEventListener('mouseleave', () => {
            button.style.backgroundColor = '#f8f9fa';
        });
        
        return button;
    }

    showPopup(popup) {
        document.body.appendChild(popup);
        
        // Position relativ zur Auswahl
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            
            popup.style.left = rect.left + 'px';
            popup.style.top = (rect.bottom + 5) + 'px';
        }
        
        // Automatisch entfernen bei Klick außerhalb
        setTimeout(() => {
            const handleClickOutside = (event) => {
                if (!popup.contains(event.target)) {
                    popup.remove();
                    document.removeEventListener('click', handleClickOutside);
                }
            };
            document.addEventListener('click', handleClickOutside);
        }, 100);
    }

    checkState() {
        const anchorTag = this.api.selection.findParentTag('A');
        return !!anchorTag;
    }

    static get shortcut() {
        return 'CMD+K';
    }
}
