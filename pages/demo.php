<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('title'), $this->i18n('demo'));

// Demo-Daten mit allen verfügbaren Blöcken
$demoData = [
    'blocks' => [
        [
            'type' => 'header',
            'data' => [
                'text' => 'EditorJS für REDAXO - Demo',
                'level' => 2
            ]
        ],
        [
            'type' => 'paragraph',
            'data' => [
                'text' => 'Willkommen zur EditorJS-Demo! Diese Seite zeigt alle verfügbaren Blöcke und deren Funktionen. Probieren Sie die verschiedenen Block-Typen aus.'
            ]
        ],
        [
            'type' => 'alert',
            'data' => [
                'type' => 'info',
                'title' => 'Info Alert-Block',
                'message' => 'Dies ist ein Info-Alert. Es gibt auch Warning, Error und Success-Varianten für verschiedene Anwendungsfälle.'
            ]
        ],
        [
            'type' => 'image',
            'data' => [
                'imageFile' => '',
                'imageUrl' => '',
                'caption' => 'Klicken Sie hier, um ein Bild aus dem REDAXO-Medienpool auszuwählen',
                'stretched' => false,
                'withBorder' => false,
                'withBackground' => false,
                'aspectRatio' => ''
            ]
        ],
        [
            'type' => 'video',
            'data' => [
                'videoFile' => '',
                'posterFile' => '',
                'caption' => 'Video-Block mit Poster-Bild',
                'autoplay' => false,
                'muted' => false,
                'loop' => false,
                'controls' => true
            ]
        ],
        [
            'type' => 'downloads',
            'data' => [
                'title' => 'Download-Bereich',
                'items' => [
                    [
                        'file' => '',
                        'title' => 'Beispiel-Download',
                        'description' => 'Beschreibung des Downloads'
                    ]
                ],
                'showTitle' => true,
                'layout' => 'list'
            ]
        ],
        [
            'type' => 'gallery',
            'data' => [
                'title' => 'Bildergalerie',
                'items' => [
                    [
                        'imageFile' => '',
                        'title' => 'Beispiel-Bild 1',
                        'description' => 'Beschreibung des ersten Bildes',
                        'alt' => 'Alt-Text für Barrierefreiheit'
                    ],
                    [
                        'imageFile' => '',
                        'title' => 'Beispiel-Bild 2',
                        'description' => 'Beschreibung des zweiten Bildes',
                        'alt' => 'Alt-Text für Barrierefreiheit'
                    ]
                ],
                'showTitle' => true,
                'layout' => 'grid',
                'imageSize' => 'medium',
                'showCaptions' => true
            ]
        ],
        [
            'type' => 'card',
            'data' => [
                'title' => 'Neuer Card-Block - Vielseitige Medien-Karten',
                'text' => '<p>Der <strong>Card-Block</strong> ist eine neue, vielseitige Komponente mit erweiterten Features:</p><ul><li><strong>Multi-Media Support:</strong> Automatische Erkennung von Bildern und Videos aus dem REDAXO Medienpool</li><li><strong>Lightbox-Integration:</strong> Klick-to-Zoom für Bilder mit Tastatursteuerung (ESC zum Schließen)</li><li><strong>Video-Steuerung:</strong> Vollständige Kontrolle über Autoplay, Muting, Loop und Custom Controls</li><li><strong>Flexible Layouts:</strong> Vertikal, horizontal und Grid-Layouts mit 1-4 Spalten</li><li><strong>Aspect Ratios:</strong> Konfigurierbare Seitenverhältnisse (auto, 16:9, 4:3, 1:1)</li><li><strong>REX Links:</strong> Integration mit REDAXO\'s internem Link-System</li></ul><p><em>Klicken Sie hier, um ein Medium aus dem Medienpool auszuwählen und die Features zu testen!</em></p>',
                'mediaFile' => '',
                'mediaUrl' => '',
                'mediaType' => 'image',
                'mediaAlt' => 'Card-Beispielbild - zeigt die vielseitigen Möglichkeiten',
                'layout' => 'vertical',
                'gridColumns' => 1,
                'aspectRatio' => '16-9',
                'lightbox' => true,
                'linkUrl' => '',
                'linkTarget' => '_self'
            ]
        ],
        [
            'type' => 'card',
            'data' => [
                'title' => 'Horizontal Layout Beispiel',
                'text' => '<p>Dieser Card zeigt das <strong>horizontale Layout</strong> mit Text neben dem Medium.</p><p>Perfekt für:</p><ul><li>Produktpresentationen</li><li>Teammitglieder</li><li>Feature-Highlights</li></ul>',
                'mediaFile' => '',
                'mediaUrl' => '',
                'mediaType' => 'image',
                'mediaAlt' => 'Horizontal Layout Beispiel',
                'layout' => 'horizontal',
                'gridColumns' => 2,
                'aspectRatio' => '4-3',
                'lightbox' => true,
                'linkUrl' => '',
                'linkTarget' => '_self'
            ]
        ],
        [
            'type' => 'textimage',
            'data' => [
                'text' => '<p><strong>Erweiterte Text+Bild/Video Funktionen</strong></p><p>Der TextImage Block wurde komplett überarbeitet und bietet jetzt:</p><ul><li><strong>🎥 Video-Support:</strong> Automatische Erkennung von Video-Dateien aus dem Medienpool</li><li><strong>🔍 Lightbox:</strong> Bildvergrößerung per Klick mit sanften Animationen</li><li><strong>⚙️ Video-Steuerung:</strong> Autoplay, Loop, Mute und Custom Control Optionen</li><li><strong>📱 Responsive Design:</strong> Mobile-optimiert mit Touch-Unterstützung</li><li><strong>🎨 Verbesserte UX:</strong> Moderne Benutzeroberfläche mit visuellen Feedback</li></ul><p>Bei Videos: Klicken Sie auf das Video-Symbol um Steuerungsoptionen zu konfigurieren.</p>',
                'mediaFile' => '',
                'mediaUrl' => '',
                'mediaType' => 'video',
                'mediaAlt' => 'TextImage-Beispiel mit Video-Support',
                'caption' => 'Unterstützt jetzt sowohl Bilder als auch Videos mit erweiterten Features',
                'layout' => 'right',
                'lightbox' => true,
                'videoAutoplay' => false,
                'videoMuted' => false,
                'videoLoop' => false,
                'videoControls' => true
            ]
        ],
        [
            'type' => 'paragraph',
            'data' => [
                'text' => '<strong>🎯 Neues Fragment-Rendering-System</strong><br>Alle EditorJS-Elemente unterstützen jetzt Framework-spezifische Ausgabe mit dem neuen Fragment-System. Bootstrap 5 und UIKit 3 werden vollständig unterstützt mit nativen Komponenten wie Modals, Alerts und responsiven Grids.'
            ]
        ],
        [
            'type' => 'card',
            'data' => [
                'title' => 'Grid Layout mit 3 Spalten',
                'text' => '<p>Beispiel für <strong>Grid-Layout</strong> mit mehreren Spalten:</p><ul><li>Ideal für Produkt-Kataloge</li><li>Team-Übersichten</li><li>Portfolio-Präsentationen</li></ul><p>Das Layout passt sich automatisch an verschiedene Bildschirmgrößen an.</p>',
                'mediaFile' => '',
                'mediaUrl' => '',
                'mediaType' => 'image',
                'mediaAlt' => 'Grid Layout Beispiel',
                'layout' => 'grid',
                'gridColumns' => 3,
                'aspectRatio' => '1-1',
                'lightbox' => true,
                'linkUrl' => '',
                'linkTarget' => '_self'
            ]
        ],
        [
            'type' => 'list',
            'data' => [
                'style' => 'unordered',
                'items' => [
                    '✨ Einfache und intuitive Bedienung mit Drag & Drop',
                    '🗂️ Vollständige REDAXO-Medienpool Integration',
                    '📱 Responsive Design für alle Geräte',
                    '🎨 Framework-spezifische Ausgabe (Bootstrap 5 & UIKit 3)',
                    '📦 Umfangreiche Block-Bibliothek mit Video/Lightbox Support'
                ]
            ]
        ],
        [
            'type' => 'delimiter'
        ],
        [
            'type' => 'header',
            'data' => [
                'text' => 'Praktische Anwendungsbeispiele',
                'level' => 3
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
                <h3 class="panel-title">EditorJS Demo</h3>
            </header>
            <div class="panel-body">
                <div id="editorjs-demo" class="editorjs-field" style="min-height: 400px; border: 1px solid #ddd; padding: 10px;">
                    <textarea style="display: none;" id="editorjs-data"><?= json_encode($demoData) ?></textarea>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <button type="button" class="btn btn-primary" onclick="loadDemoData()">Demo-Daten laden</button>
                    <button type="button" class="btn btn-default" onclick="clearEditor()">Editor leeren</button>
                    <button type="button" class="btn btn-info" onclick="showJSON()">JSON anzeigen</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">JSON-Ausgabe</h3>
            </header>
            <div class="panel-body">
                <pre id="json-output" style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #e9ecef;"><?= json_encode($demoData, JSON_PRETTY_PRINT) ?></pre>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">Verfügbare Blöcke</h3>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Standard EditorJS Blöcke</h4>
                        <ul>
                            <li><strong>Paragraph</strong> - Standard-Textabsätze</li>
                            <li><strong>Header</strong> - Überschriften (H1-H6)</li>
                            <li><strong>List</strong> - Nummerierte und Aufzählungslisten</li>
                            <li><strong>Quote</strong> - Zitate</li>
                            <li><strong>Code</strong> - Code-Blöcke</li>
                            <li><strong>Delimiter</strong> - Trennlinien</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>REDAXO-spezifische Blöcke</h4>
                        <ul>
                            <li><strong>Card Block</strong> - 🆕 Vielseitige Medien-Karten mit Lightbox</li>
                            <li><strong>TextImage Block</strong> - 🔄 Erweitert mit Video-Support und Lightbox</li>
                            <li><strong>Image Block</strong> - Einzelbilder mit Medienpool</li>
                            <li><strong>Video Block</strong> - Videos mit Poster-Bild und Steuerung</li>
                            <li><strong>Downloads Block</strong> - Multiple Downloads mit Drag & Drop</li>
                            <li><strong>Gallery Block</strong> - Bildergalerien mit verschiedenen Layouts</li>
                            <li><strong>Alert Block</strong> - Hinweis-Boxen (Info, Warning, Error, Success)</li>
                        </ul>
                        
                        <h4>🆕 Neue Features</h4>
                        <ul>
                            <li><strong>Video-Support:</strong> Automatische Erkennung & Steuerung</li>
                            <li><strong>Lightbox:</strong> Bildvergrößerung mit Tastatursteuerung</li>
                            <li><strong>Fragment-System:</strong> Bootstrap 5 & UIKit 3 Ausgabe</li>
                            <li><strong>Grid-Layouts:</strong> 1-4 Spalten für Cards</li>
                            <li><strong>Aspect Ratios:</strong> Konfigurierbare Seitenverhältnisse</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">🆕 Fragment-Rendering-System - Bootstrap & UIKit Integration</h3>
            </header>
            <div class="panel-body">
                <p class="lead">Das neue Fragment-System ermöglicht framework-spezifische Ausgabe aller EditorJS-Elemente mit nativen Bootstrap 5 und UIKit 3 Komponenten.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <h4>📋 PHP Integration</h4>
<pre><code>&lt;?php
use FriendsOfRedaxo\EditorJs\EditorJSFragmentRenderer;

// Framework-spezifischer Renderer
$framework = 'bootstrap'; // oder 'uikit'
$renderer = new EditorJSFragmentRenderer($framework, [
    'lightbox' => true,
    'lazyLoading' => true,
    'customClass' => 'my-custom-class'
]);

// EditorJS Content rendern
$content = rex_article::getCurrent()->getValue('content');
echo $renderer->render($content);
?&gt;</code></pre>
                    </div>
                    
                    <div class="col-md-6">
                        <h4>✨ Framework Features</h4>
                        <div class="row">
                            <div class="col-sm-6">
                                <h5>Bootstrap 5</h5>
                                <ul class="list-unstyled">
                                    <li>✓ Modal-Lightbox</li>
                                    <li>✓ Alert-Komponenten</li>
                                    <li>✓ Card-System</li>
                                    <li>✓ Responsive Grid</li>
                                    <li>✓ Button-Gruppen</li>
                                </ul>
                            </div>
                            <div class="col-sm-6">
                                <h5>UIKit 3</h5>
                                <ul class="list-unstyled">
                                    <li>✓ Native Lightbox</li>
                                    <li>✓ Alert-Komponenten</li>
                                    <li>✓ Card-System</li>
                                    <li>✓ Flex-Grid</li>
                                    <li>✓ Smooth Animations</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <h5><i class="fa fa-lightbulb-o"></i> Tipp: Framework-Konfiguration</h5>
                    <p>Das Framework kann über die <strong>EditorJS-Einstellungen</strong> im REDAXO Backend global konfiguriert werden oder direkt beim Renderer-Aufruf gesetzt werden.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <h4><i class="fa fa-info-circle"></i> Bedienung</h4>
    <div class="row">
        <div class="col-md-6">
            <h5>Tastaturkürzel</h5>
            <ul class="list-unstyled">
                <li><strong>Enter:</strong> Neue Zeile oder neuer Listenpunkt</li>
                <li><strong>Shift + Enter:</strong> Neuen Block erstellen</li>
                <li><strong>Tab:</strong> Einrücken in Listen</li>
                <li><strong>Cmd/Ctrl + B:</strong> Fett</li>
                <li><strong>Cmd/Ctrl + I:</strong> Kursiv</li>
                <li><strong>Cmd/Ctrl + K:</strong> REDAXO Link einfügen</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h5>Block-Funktionen</h5>
            <ul class="list-unstyled">
                <li><strong>Medienpool:</strong> Klick auf Bilder/Videos zur Auswahl</li>
                <li><strong>🆕 Lightbox:</strong> Klick auf Bilder zum Vergrößern (ESC schließt)</li>
                <li><strong>🆕 Video-Steuerung:</strong> Autoplay, Loop, Mute über Einstellungen</li>
                <li><strong>🆕 Grid-Layouts:</strong> Card-Spalten von 1-4 konfigurierbar</li>
                <li><strong>Drag & Drop:</strong> Downloads und Galerie-Bilder sortierbar</li>
                <li><strong>Einstellungen:</strong> Block-spezifische Optionen über Zahnrad-Symbol</li>
                <li><strong>🆕 Fragment-Ausgabe:</strong> Bootstrap/UIKit Framework-Integration</li>
                <li><strong>Auto-Save:</strong> Änderungen werden automatisch gespeichert</li>
            </ul>
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
        if (typeof ImageBlock === 'undefined') missingTools.push('ImageBlock');
        if (typeof VideoBlock === 'undefined') missingTools.push('VideoBlock');
        if (typeof DownloadsBlock === 'undefined') missingTools.push('DownloadsBlock');
        if (typeof ImageGalleryBlock === 'undefined') missingTools.push('ImageGalleryBlock');
        if (typeof TextImageBlock === 'undefined') missingTools.push('TextImageBlock');
        if (typeof CardBlock === 'undefined') missingTools.push('CardBlock');
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
            image: {
                class: ImageBlock,
                config: {
                    stretched: false,
                    withBorder: false,
                    withBackground: false,
                    aspectRatio: '',
                    cropMode: 'cover'
                }
            },
            video: {
                class: VideoBlock,
                config: {
                    autoplay: false,
                    muted: false,
                    loop: false,
                    controls: true
                }
            },
            downloads: {
                class: DownloadsBlock,
                config: {
                    defaultLayout: 'list'
                }
            },
            gallery: {
                class: ImageGalleryBlock,
                config: {
                    defaultLayout: 'grid',
                    defaultImageSize: 'medium'
                }
            },
            textimage: {
                class: TextImageBlock,
                inlineToolbar: true,
                config: {
                    defaultLayout: 'left'
                }
            },
            card: {
                class: CardBlock,
                inlineToolbar: true,
                config: {
                    defaultLayout: 'vertical'
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

        editor = new EditorJS({
            holder: 'editorjs-demo',
            placeholder: '<?= $this->i18n('placeholder') ?>',
            data: demoData,
            tools: tools,
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