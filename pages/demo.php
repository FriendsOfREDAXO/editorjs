<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('title'), $this->i18n('demo'));



// Demo-Daten
$demoData = [
    'blocks' => [
        [
            'type' => 'header',
            'data' => [
                'text' => $this->i18n('welcome_title'),
                'level' => 2
            ]
        ],
        [
            'type' => 'paragraph',
            'data' => [
                'text' => $this->i18n('welcome_text')
            ]
        ],
        [
            'type' => 'alert',
            'data' => [
                'type' => 'info',
                'title' => 'Neuer Alert-Block!',
                'message' => 'Dies ist ein benutzerdefinierter Alert-Block. Sie können zwischen verschiedenen Typen wählen: Info, Warnung, Fehler und Erfolg.'
            ]
        ],
        [
            'type' => 'textimage',
            'data' => [
                'text' => '<p><strong>Text mit Bild</strong></p><p>Dieser Block kombiniert <em>formatierten Text</em> mit einem Bild aus dem REDAXO-Medienpool. Sie können das Layout über die Einstellungen ändern.</p><ul><li>Bild links vom Text</li><li>Bild rechts vom Text</li><li>Bild über dem Text</li></ul>',
                'imageFile' => '',
                'imageUrl' => '',
                'caption' => 'Klicken Sie hier, um ein Bild aus dem Medienpool auszuwählen',
                'layout' => 'left'
            ]
        ],
        [
            'type' => 'list',
            'data' => [
                'style' => 'unordered',
                'items' => [
                    $this->i18n('features_simple'),
                    $this->i18n('features_blocks'),
                    $this->i18n('features_json'),
                    'Benutzerdefinierte Blöcke (Alert-Block)',
                    'Text-Bild-Block mit Medienpool',
                    'Downloads-Repeater mit Medienpool-Integration'
                ]
            ]
        ],
        [
            'type' => 'downloads',
            'data' => [
                'title' => 'Beispiel Downloads',
                'showTitle' => true,
                'layout' => 'list',
                'items' => [
                    [
                        'file' => 'beispiel.pdf',
                        'title' => 'Produktkatalog 2024',
                        'description' => 'Unser aktueller Produktkatalog mit allen Neuheiten und Innovationen.',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'bild-beispiel.jpg',
                        'title' => 'Hochauflösendes Produktbild',
                        'description' => 'Professionelle Produktfotografie in bester Qualität.',
                        'customIcon' => ''
                    ]
                ]
            ]
        ]
    ]
];

?>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title"><?= $this->i18n('editor') ?></h3>
            </header>
            <div class="panel-body">
                <div id="editorjs-demo" class="editorjs-field" style="min-height: 400px; border: 1px solid #ddd; padding: 10px;">
                    <textarea style="display: none;" id="editorjs-data"><?= json_encode($demoData) ?></textarea>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <button type="button" class="btn btn-primary" onclick="loadDemoData()"><?= $this->i18n('demo_data_load') ?></button>
                    <button type="button" class="btn btn-default" onclick="clearEditor()"><?= $this->i18n('clear') ?></button>
                    <button type="button" class="btn btn-info" onclick="showJSON()"><?= $this->i18n('show_json') ?></button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title"><?= $this->i18n('json_output') ?></h3>
            </header>
            <div class="panel-body">
                <pre id="json-output" style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #e9ecef;"><?= json_encode($demoData, JSON_PRETTY_PRINT) ?></pre>
            </div>
        </div>
    </div>
</div>

<!-- Standard-Editoren Demos -->
<div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-edit"></i> Standard-Editor Demos</h3>
            </header>
            <div class="panel-body">
                <p>Verschiedene EditorJS-Instanzen mit allen verfügbaren Tools und dynamischen Blöcken.</p>
                
                <div class="row">
                    <!-- Einfacher Editor -->
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Einfacher Editor</h4>
                                <small>Standard EditorJS</small>
                            </div>
                            <div class="panel-body">
                                <textarea data-editorjs-direct 
                                          placeholder="Einfachen Text eingeben..."
                                          style="min-height: 150px;">{"blocks":[{"type":"paragraph","data":{"text":"Einfacher Editor mit allen verfügbaren Tools."}}]}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Blog-Editor -->
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Blog-Editor</h4>
                                <small>Mit dynamischen Blöcken</small>
                            </div>
                            <div class="panel-body">
                                <textarea data-editorjs-direct 
                                          placeholder="Blog-Artikel schreiben..."
                                          style="min-height: 150px;">{"blocks":[{"type":"header","data":{"text":"Blog-Artikel","level":2}},{"type":"paragraph","data":{"text":"Dieser Editor hat alle Tools inklusive dynamischer Blöcke."}}]}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Media-Editor -->
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Media-Editor</h4>
                                <small>Mit Media-Integration</small>
                            </div>
                            <div class="panel-body">
                                <textarea data-editorjs-direct 
                                          placeholder="Media-reichen Inhalt erstellen..."
                                          style="min-height: 150px;">{"blocks":[{"type":"header","data":{"text":"Media Content","level":2}},{"type":"paragraph","data":{"text":"Editor mit allen verfügbaren Tools und Media-Integration."}}]}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info" style="margin-top: 15px;">
                    <strong><i class="fa fa-lightbulb-o"></i> Tipps:</strong>
                    <ul>
                        <li>Öffnen Sie die Browser-Entwicklertools (F12) und schauen Sie in die Konsole für Debug-Informationen</li>
                        <li>Klicken Sie auf das "+" Symbol in den Editoren - Sie sehen alle verfügbaren Tools</li>
                        <li>Alle Standard-Tools und dynamische Blöcke aus <code>/fragments/</code> sind verfügbar</li>
                        <li>Die Auto-Initialisierung funktioniert auch bei PJAX-Navigation in REDAXO</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <h4><i class="fa fa-code"></i> Debug-Test</h4>
                    <p>Klicken Sie auf den Button unten, um alle aktiven Editor-Instanzen in der Browser-Konsole anzuzeigen:</p>
                    <button onclick="debugEditors()" class="btn btn-sm btn-success">Editoren in Konsole anzeigen</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <h4><i class="fa fa-info-circle"></i> Tastaturkürzel</h4>
    <ul class="list-unstyled">
        <li><strong>Enter:</strong> 
            <ul>
                <li>In Text-Blöcken: Neue Zeile (Zeilenumbruch)</li>
                <li>In Listen: Neuer Listenpunkt</li>
            </ul>
        </li>
        <li><strong>Shift + Enter:</strong> 
            <ul>
                <li>In Text-Blöcken: Neuen Block erstellen</li>
                <li>In Listen: Zeilenumbruch im aktuellen Listenpunkt</li>
            </ul>
        </li>
        <li><strong>Tab:</strong> Einrücken in Listen</li>
        <li><strong>Cmd/Ctrl + B:</strong> Fett</li>
        <li><strong>Cmd/Ctrl + I:</strong> Kursiv</li>
        <li><strong>Cmd/Ctrl + K:</strong> REDAXO Link einfügen</li>
    </ul>
    <p><small><strong>Hinweis:</strong> Das Enter-Verhalten passt sich automatisch an den Block-Typ an. In normalen Text-Bereichen erstellt <kbd>Enter</kbd> Zeilenumbrüche, in Listen neue Listenpunkte.</small></p>
</div>

<script>
var demoData = <?= json_encode($demoData) ?>;
var editor = null;

// Warten bis EditorJS geladen ist
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for EditorJS...');
    
    setTimeout(function() {
        if (typeof EditorJS !== 'undefined') {
            console.log('EditorJS found, initializing...');
            initEditor();
        } else {
            console.error('EditorJS not loaded');
            var errorMsg = 'EditorJS konnte nicht geladen werden. Bitte prüfen Sie die Browser-Konsole.';
            document.getElementById('editorjs-demo').innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
        }
    }, 1000);
});

function initEditor() {
    try {
        console.log('Initializing EditorJS with tools...');
        
        // Überprüfen ob alle Tools verfügbar sind
        var missingTools = [];
        if (typeof Header === 'undefined') missingTools.push('Header');
        if (typeof Paragraph === 'undefined') missingTools.push('Paragraph');
        if (typeof List === 'undefined') missingTools.push('List');
        if (typeof Quote === 'undefined') missingTools.push('Quote');
        if (typeof Delimiter === 'undefined') missingTools.push('Delimiter');
        if (typeof CodeTool === 'undefined') missingTools.push('CodeTool');
        if (typeof AlertBlock === 'undefined') missingTools.push('AlertBlock');
        if (typeof TextImageBlock === 'undefined') missingTools.push('TextImageBlock');
        if (typeof Marker === 'undefined') missingTools.push('Marker');
        if (typeof InlineCode === 'undefined') missingTools.push('InlineCode');
        if (typeof LinkTool === 'undefined') missingTools.push('LinkTool');
        if (typeof REXLinkTool === 'undefined') missingTools.push('REXLinkTool');
        
        if (missingTools.length > 0) {
            throw new Error('Fehlende Tools: ' + missingTools.join(', '));
        }
        
        // Tools-Objekt dynamisch aufbauen
        var tools = {
            header: {
                class: Header,
                config: {
                    placeholder: '<?= $this->i18n('header_placeholder') ?>',
                    levels: [2, 3, 4],
                    defaultLevel: 2
                }
            },
            paragraph: {
                class: Paragraph,
                inlineToolbar: true
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: '<?= $this->i18n('quote_placeholder') ?>',
                    captionPlaceholder: '<?= $this->i18n('author_placeholder') ?>'
                }
            },
            delimiter: {
                class: Delimiter
            },
            code: {
                class: CodeTool,
                config: {
                    placeholder: '<?= $this->i18n('code_placeholder') ?>'
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
                class: LinkTool
            },
            rexLink: {
                class: REXLinkTool,
                shortcut: 'CMD+K'
            }
        };

        // Downloads-Tool hinzufügen wenn verfügbar
        if (typeof DownloadsBlock !== 'undefined') {
            tools.downloads = {
                class: DownloadsBlock,
                config: {
                    defaultLayout: 'list',
                    showTitle: true
                }
            };
        }

        // Dynamische Blöcke laden und hinzufügen
        console.log('Loading dynamic blocks...');
        fetch('/index.php?rex-api-call=editorjs&call=get_block_configs')
            .then(response => response.json())
            .then(dynamicBlocks => {
                console.log('Dynamic blocks loaded:', dynamicBlocks);
                
                // Dynamische Blöcke zu den Tools hinzufügen
                for (const blockName in dynamicBlocks) {
                    if (dynamicBlocks.hasOwnProperty(blockName)) {
                        console.log(`Adding dynamic block: ${blockName}`);
                        tools[blockName] = {
                            class: createDynamicBlockClass(blockName, dynamicBlocks[blockName])
                        };
                    }
                }
                
                // Editor mit allen Tools (statisch + dynamisch) initialisieren
                initEditorWithTools(tools);
            })
            .catch(error => {
                console.warn('Could not load dynamic blocks, using static tools only:', error);
                // Fallback: Editor nur mit statischen Tools initialisieren
                initEditorWithTools(tools);
            });
    } catch (error) {
        console.error('Editor initialization failed:', error);
        document.getElementById('editorjs-demo').innerHTML = '<div class="alert alert-danger">Editor konnte nicht initialisiert werden: ' + error.message + '</div>';
    }
}

// Hilfsfunktion für die dynamische Block-Klassen-Erstellung (aus editorjs.js übernommen)
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

            // Initialize data fields from config
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
                if (this.data[fieldName] === undefined) {
                    this.data[fieldName] = '';
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
                if (fieldElement) {
                    const fieldType = blockInfo.config.fields[fieldName].type;
                    if (fieldType === 'text') {
                        fieldElement.contentEditable = !this.readOnly;
                        fieldElement.innerHTML = this.data[fieldName] || '';
                        fieldElement.addEventListener('blur', () => {
                            this.data[fieldName] = fieldElement.innerHTML;
                        });
                    } else if (fieldType === 'select') {
                        fieldElement.value = this.data[fieldName] || '';
                        fieldElement.addEventListener('change', () => {
                            this.data[fieldName] = fieldElement.value;
                        });
                    }
                }
            });

            return wrapper;
        }

        save() {
            const savedData = {};
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
                const fieldElement = this.nodes.wrapper.querySelector(`[data-field="${fieldName}"]`);
                if (fieldElement) {
                    const fieldType = blockInfo.config.fields[fieldName].type;
                     if (fieldType === 'text') {
                        savedData[fieldName] = fieldElement.innerHTML;
                    } else if (fieldType === 'select') {
                        savedData[fieldName] = fieldElement.value;
                    }
                }
            });
            this.data = savedData;
            return this.data;
        }

        static get sanitize() {
            const rules = {};
            Object.keys(blockInfo.config.fields).forEach(fieldName => {
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

// Separate Funktion für die eigentliche Editor-Initialisierung
function initEditorWithTools(tools) {
    try {
        editor = new EditorJS({
            holder: 'editorjs-demo',
            placeholder: '<?= $this->i18n('placeholder') ?>',
            data: demoData,
            tools: tools,
            onChange: function() {
                updateJSON();
            },
            onReady: function() {
                console.log('EditorJS ready with', Object.keys(tools).length, 'tools!');
            }
        });
    } catch (error) {
        console.error('Editor initialization failed:', error);
        document.getElementById('editorjs-demo').innerHTML = '<div class="alert alert-danger">Editor konnte nicht initialisiert werden: ' + error.message + '</div>';
    }
}

function loadDemoData() {
    if (editor) {
        editor.render(demoData);
        updateJSON();
    }
}

function clearEditor() {
    if (editor) {
        editor.render({blocks: []});
        updateJSON();
    }
}

function showJSON() {
    updateJSON();
}

function updateJSON() {
    if (editor) {
        editor.save().then(function(data) {
            document.getElementById('json-output').textContent = JSON.stringify(data, null, 2);
        }).catch(function(error) {
            console.error('Saving failed:', error);
        });
    }
}
</script>

<style>
/* Zusätzliche CSS für verbesserte Layout-Kontrolle */
.cdx-textimage .cdx-textimage__container.layout-right.layout-right {
    flex-direction: row-reverse !important;
}

.cdx-textimage .cdx-textimage__container.layout-left.layout-left {
    flex-direction: row !important;
}

.cdx-textimage .cdx-textimage__container.layout-top.layout-top {
    flex-direction: column !important;
}

/* Force repaint */
.cdx-textimage[data-layout="right"] .cdx-textimage__container {
    flex-direction: row-reverse !important;
    transform: translateZ(0);
}

.cdx-textimage[data-layout="left"] .cdx-textimage__container {
    flex-direction: row !important;
    transform: translateZ(0);
}

.cdx-textimage[data-layout="top"] .cdx-textimage__container {
    flex-direction: column !important;
    transform: translateZ(0);
}
</style>

<script>
// Debug-Funktion für Editor-Instanzen
function debugEditors() {
    console.log('=== Editor-Instanzen Debug ===');
    
    // Alle Editor-Instanzen durchgehen
    if (window.editorInstances) {
        Object.keys(window.editorInstances).forEach(function(editorId) {
            const editor = window.editorInstances[editorId];
            const container = document.getElementById(editorId);
            
            console.log(`Editor: ${editorId}`);
            console.log(`  - Verfügbare Tools:`, Object.keys(editor.configuration.tools));
            console.log(`  - Container Element:`, container);
            console.log(`  - Editor Instance:`, editor);
        });
    } else {
        console.log('Keine EditorJS-Instanzen gefunden.');
    }
    
    console.log('=== Ende Debug ===');
    alert('Editor-Debug ausgeführt. Schauen Sie in die Browser-Konsole für Details.');
}
</script>
            const editor = window.editorInstances[editorId];
            const container = document.getElementById(editorId);
            
            if (editor && editor.configuration && editor.configuration.tools) {
                const toolNames = Object.keys(editor.configuration.tools);
                const filterAttribute = container?.dataset?.editorjsTools || 
                                       container?.querySelector('textarea')?.dataset?.editorjsTools || 
                                       'keine Filterung';
                
                console.log(`Editor: ${editorId}`);
                console.log(`Filter: ${filterAttribute}`);
                console.log(`Verfügbare Tools: ${toolNames.join(', ')}`);
                console.log(`Anzahl Tools: ${toolNames.length}`);
                console.log('---');
            }
        });
    }
    
    // Zusätzlich alle Editoren mit Tool-Filter finden
    const filteredElements = document.querySelectorAll('[data-editorjs-tools]');
    console.log(`\nGefundene Elemente mit data-editorjs-tools: ${filteredElements.length}`);
    filteredElements.forEach(function(element) {
        console.log(`Element: ${element.tagName}#${element.id || 'keine-id'}`);
        console.log(`Filter: ${element.dataset.editorjsTools}`);
    });
    
    console.log('\n=== Ende Debug ===');
    alert('Debug-Informationen wurden in die Browser-Konsole ausgegeben. Öffnen Sie die Entwicklertools (F12) um sie zu sehen.');
}
</script>