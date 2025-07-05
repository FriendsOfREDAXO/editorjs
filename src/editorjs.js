import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import Paragraph from '@editorjs/paragraph';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import Delimiter from '@editorjs/delimiter';
import CodeTool from '@editorjs/code';

// Inline-Tools für Rich-Text-Formatierung
import InlineCode from '@editorjs/inline-code';
import Marker from '@editorjs/marker';
import LinkTool from '@editorjs/link';

// Eigene Blöcke importieren
import AlertBlock from './blocks/alert.js';
import './blocks/alert.css';
import TextImageBlock from './blocks/textimage.js';
import './blocks/textimage.css';
import ImageBlock from './blocks/image.js';
import './blocks/image.css';
import REXLinkTool from './blocks/rexlink.js';
import REXMediaTool from './blocks/rexmedia.js';
import DownloadsBlock from './blocks/downloads.js';
import './blocks/downloads.css';
import VideoBlock from './blocks/video.js';
import './blocks/video.css';

// Globale Variablen für REDAXO Backend
window.EditorJS = EditorJS;
window.Header = Header;
window.Paragraph = Paragraph;
window.List = List;
window.Quote = Quote;
window.Delimiter = Delimiter;
window.CodeTool = CodeTool;
window.InlineCode = InlineCode;
window.Marker = Marker;
window.LinkTool = LinkTool;
window.REXLinkTool = REXLinkTool;
window.AlertBlock = AlertBlock;
window.TextImageBlock = TextImageBlock;
window.ImageBlock = ImageBlock;
window.DownloadsBlock = DownloadsBlock;
window.VideoBlock = VideoBlock; // Make the new VideoBlock globally available as well
window.REXMediaTool = REXMediaTool;

/**
 * Dynamically creates an Editor.js block class from a configuration object.
 * @param {string} blockName - The name of the block (e.g., 'headline').
 * @param {object} blockInfo - The configuration object from the API.
 * @returns {class} - A class that can be used by Editor.js.
 */
function createDynamicBlockClass(blockName, blockInfo) {
    return class DynamicBlock {
        static get toolbox() {
            return {
                title: blockInfo.config.title,
                icon: blockInfo.config.icon,
            };
        }

        static get inlineToolbar() {
            return blockInfo.config.inlineToolbar || false;
        }

        constructor({ data, api, readOnly }) {
            this.api = api;
            this.readOnly = readOnly;
            this.data = data || {};
            this.nodes = {};
            this.blockName = blockName; // Store blockName for debugging

            // Initialize data fields from config
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
                const fieldConfig = blockInfo.config.fields[fieldName];
                if (this.data[fieldName] === undefined) {
                    // Use default value if specified, otherwise empty string
                    this.data[fieldName] = fieldConfig.default || '';
                }
            });
        }

        render() {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = blockInfo.editor_view;
            this.nodes.wrapper = wrapper;

            // Make elements editable and link them to data
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
                const fieldElement = wrapper.querySelector(`[data-field="${fieldName}"]`);
                const fieldConfig = blockInfo.config.fields[fieldName];
                if (fieldElement) {
                    const fieldType = fieldConfig.type;
                    if (fieldType === 'text') {
                        fieldElement.contentEditable = !this.readOnly;
                        fieldElement.innerHTML = this.data[fieldName] || '';
                        fieldElement.addEventListener('blur', () => {
                            this.data[fieldName] = fieldElement.innerHTML;
                        });
                    } else if (fieldType === 'select') {
                        // Set the current value or default
                        const currentValue = this.data[fieldName] || fieldConfig.default || '';
                        fieldElement.value = currentValue;
                        console.log(`Debug ${this.blockName} - Setting select ${fieldName} to: ${currentValue}, Element value: ${fieldElement.value}`);
                        fieldElement.addEventListener('change', () => {
                            console.log(`Debug ${this.blockName} - Select ${fieldName} changed from ${this.data[fieldName]} to ${fieldElement.value}`);
                            this.data[fieldName] = fieldElement.value;
                        });
                    }
                    // More field types (rex-media, etc.) can be handled here
                }
            });

            return wrapper;
        }

        save() {
            console.log(`Debug ${this.blockName} - Save called, current this.data:`, this.data);
            const savedData = {};
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
                const fieldElement = this.nodes.wrapper.querySelector(`[data-field="${fieldName}"]`);
                const fieldConfig = blockInfo.config.fields[fieldName];
                if (fieldElement) {
                    const fieldType = fieldConfig.type;
                     if (fieldType === 'text') {
                        // For text fields, always read from DOM as they might have been edited
                        savedData[fieldName] = fieldElement.innerHTML;
                        console.log(`Debug ${this.blockName} - Save text field ${fieldName}: ${fieldElement.innerHTML}`);
                    } else if (fieldType === 'select') {
                        // For select fields, use this.data which is updated by change events
                        // This avoids DOM sync issues
                        savedData[fieldName] = this.data[fieldName];
                        console.log(`Debug ${this.blockName} - Save select field ${fieldName}: Using this.data value = ${this.data[fieldName]}, DOM shows = ${fieldElement.value}`);
                    }
                    // Handle other field types for saving
                }
            });
            // Update the internal data object with the fresh values
            this.data = savedData;
            console.log(`Debug ${this.blockName} - Final saved data:`, this.data);
            return this.data;
        }

        static get sanitize() {
            const rules = {};
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
                // Basic sanitization, can be expanded
                rules[fieldName] = {
                    br: true,
                    strong: true,
                    em: true,
                    a: { href: true }
                };
            });
            return rules;
        }
    };
}

// EditorJS Utilities für REDAXO
window.EditorJSUtils = {
    
    /**
     * Erstellt einen neuen EditorJS mit Standard-Konfiguration
     * @param {Object} options - Editor-Konfiguration
     * @param {string|Array} allowedTools - Optional: Liste der erlaubten Tools
     */
    createEditor: async function(options, allowedTools = null) {
        // --- Start: Dynamic Block Loading ---
        let dynamicTools = {};
        try {
            const response = await fetch('/index.php?rex-api-call=editorjs&call=get_block_configs');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const dynamicBlocks = await response.json();
            
            for (const blockName in dynamicBlocks) {
                if (dynamicBlocks.hasOwnProperty(blockName)) {
                    console.log(`Registering dynamic block: ${blockName}`);
                    dynamicTools[blockName] = {
                        class: createDynamicBlockClass(blockName, dynamicBlocks[blockName])
                    };
                }
            }
        } catch (error) {
            console.error('Could not load dynamic EditorJS blocks:', error);
        }
        // --- End: Dynamic Block Loading ---

        // Tools dynamisch zusammenstellen
        const staticTools = {
            header: {
                class: Header,
                config: {
                    placeholder: 'Überschrift eingeben...',
                    levels: [2, 3, 4],
                    defaultLevel: 2
                }
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true,
                config: {
                    placeholder: 'Text eingeben...',
                    preserveBlank: true
                }
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Zitat eingeben...',
                    captionPlaceholder: 'Autor'
                }
            },
            delimiter: {
                class: Delimiter
            },
            code: {
                class: CodeTool,
                config: {
                    placeholder: 'Code eingeben...'
                }
            },
            alert: {
                class: AlertBlock,
                config: {
                    defaultType: 'info'
                }
            },
            textimage: {
                class: TextImageBlock,
                inlineToolbar: true,
                config: {
                    defaultLayout: 'left'
                }
            },
            image: {
                class: ImageBlock,
                config: {
                    stretched: false,
                    withBorder: false,
                    withBackground: false,
                    aspectRatio: 'auto',
                    cropMode: 'cover'
                }
            },
            video: {
                class: VideoBlock,
                config: {
                    aspectRatio: '16-9',
                    controls: true,
                    autoplay: false,
                    loop: false,
                    muted: false
                }
            },
            downloads: {
                class: DownloadsBlock,
                config: {
                    defaultLayout: 'list',
                    showTitle: true
                }
            },
            // Inline-Tools für Rich-Text-Formatierung
            Marker: {
                class: Marker,
                shortcut: 'CMD+SHIFT+M'
            },
            inlineCode: {
                class: InlineCode,
                shortcut: 'CMD+SHIFT+C'
            },
            linkTool: {
                class: LinkTool,
                config: {
                    endpoint: 'http://localhost:8008/fetchUrl' // Optional: für automatische Metadaten
                }
            },
            rexLink: {
                class: REXLinkTool,
                shortcut: 'CMD+K'
            }
        };

        // Put dynamic tools first, then static tools
        const allTools = { ...dynamicTools, ...staticTools };
        
        // Filter tools if allowedTools is specified
        let tools = allTools;
        let initialBlock = 'paragraph'; // Standard-Block
        
        if (allowedTools) {
            console.log('=== TOOL FILTERING ACTIVE ===');
            console.log('Original tools:', Object.keys(allTools));
            console.log('Allowed tools string:', allowedTools);
            
            const allowedList = Array.isArray(allowedTools) ? allowedTools : allowedTools.split(',').map(s => s.trim());
            console.log('Allowed tools array:', allowedList);
            
            tools = {};
            allowedList.forEach(toolName => {
                if (allTools[toolName]) {
                    tools[toolName] = allTools[toolName];
                    console.log(`✓ Adding tool: ${toolName}`);
                } else {
                    console.warn(`✗ Tool "${toolName}" not found in available tools:`, Object.keys(allTools));
                }
            });
            
            // Zusätzliche Tools aus bestehenden Daten hinzufügen
            if (options.data && options.data.blocks && Array.isArray(options.data.blocks)) {
                const usedTools = new Set();
                options.data.blocks.forEach(block => {
                    if (block.type && !allowedList.includes(block.type)) {
                        if (allTools[block.type]) {
                            console.log(`⚠ Adding tool "${block.type}" for existing data (not in allowed list)`);
                            tools[block.type] = allTools[block.type];
                            usedTools.add(block.type);
                        } else {
                            console.warn(`⚠ Block type "${block.type}" in data not found in available tools`);
                        }
                    }
                });
                
                if (usedTools.size > 0) {
                    console.log('Added tools for existing data:', Array.from(usedTools));
                }
            }
            
            // Paragraph als Standard-Block verwenden, außer es ist nicht erlaubt
            if (!allowedList.includes('paragraph')) {
                console.log('✗ Paragraph not in allowed list');
                // Finde das erste verfügbare Tool als Fallback
                const availableTools = Object.keys(tools);
                initialBlock = availableTools.length > 0 ? availableTools[0] : 'header';
                console.log(`Using ${initialBlock} as initial block instead of paragraph`);
            } else {
                console.log('✓ Paragraph allowed');
            }
            
            console.log('Final filtered tools:', Object.keys(tools));
            console.log('=== END TOOL FILTERING ===');
        } else {
            console.log('No tool filtering - using all tools');
        }

        const defaultOptions = {
            onReady: function() {
                console.log('EditorJS is ready!');
                // Custom Enter-Handling für alle Blöcke
                EditorJSUtils.setupEnterHandling();
            },
            tools: tools,
            // Initial Block basierend auf verfügbaren Tools setzen
            defaultBlock: initialBlock,
            // Placeholder setzen
            placeholder: options.placeholder || 'Inhalt eingeben...'
        };
        
        // Merge user options with defaults
        const config = Object.assign({}, defaultOptions, options);
        
        return new EditorJS(config);
    },
    
    /**
     * Hilfsfunktion zum sicheren JSON-Parsen
     */
    parseJSON: function(str) {
        try {
            return JSON.parse(str);
        } catch (e) {
            console.error('JSON Parse Error:', e);
            return null;
        }
    },
    
    /**
     * Setup für verbessertes Enter-Verhalten
     */
    setupEnterHandling: function() {
        // Globaler Event Listener für alle contentEditable Elemente
        document.addEventListener('keydown', function(event) {
            const target = event.target;
            
            // Nur bei contentEditable Elementen in EditorJS
            if (target.contentEditable === 'true' && 
                target.closest('.codex-editor')) {
                
                // Prüfen ob das Element bereits eigene Enter-Handler hat
                // oder ein spezielles Verhalten benötigt
                const hasCustomHandler = target.closest('.cdx-alert') || 
                                        target.closest('.cdx-textimage') ||
                                        target.closest('.cdx-list') ||
                                        target.closest('.ce-block--selected .cdx-list') ||
                                        target.closest('[data-tool="list"]');
                
                // Nur für Standard-Paragraph-Blöcke ohne eigene Handler
                if (!hasCustomHandler && event.key === 'Enter' && !event.shiftKey) {
                    // Enter ohne Shift: Zeilenumbruch im aktuellen Block
                    event.preventDefault();
                    
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);
                        const br = document.createElement('br');
                        
                        range.deleteContents();
                        range.insertNode(br);
                        
                        // Cursor nach dem BR positionieren
                        range.setStartAfter(br);
                        range.setEndAfter(br);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                    
                    return false;
                }
                // Shift+Enter: Standard-Verhalten (neuer Block)
                // Listen: Standard EditorJS-Verhalten beibehalten
            }
        }, true);
    }
};

console.log('EditorJS Bundle loaded successfully');
