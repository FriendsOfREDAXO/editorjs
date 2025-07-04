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
                    'Text-Bild-Block mit Medienpool'
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
        
        editor = new EditorJS({
            holder: 'editorjs-demo',
            placeholder: '<?= $this->i18n('placeholder') ?>',
            data: demoData,
            tools: {
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
            },
            onChange: function() {
                updateJSON();
            },
            onReady: function() {
                console.log('EditorJS ready!');
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