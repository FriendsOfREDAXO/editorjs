<?php

/** @var rex_addon $this */

use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

echo rex_view::title($this->i18n('title'), 'Frontend Renderer Demo');

// EditorJS Renderer laden
require_once $this->getPath('lib/EditorJSRenderer.php');

// Demo-Daten für den Renderer
$demoJSON = json_encode([
    'blocks' => [
        [
            'type' => 'header',
            'data' => [
                'text' => 'EditorJS Frontend Renderer Demo',
                'level' => 2
            ]
        ],
        [
            'type' => 'paragraph',
            'data' => [
                'text' => 'Diese Seite demonstriert, wie EditorJS JSON-Daten in HTML umgewandelt werden. Der Renderer unterstützt alle Standard-Blöcke sowie unsere benutzerdefinierten Blöcke.'
            ]
        ],
        [
            'type' => 'alert',
            'data' => [
                'type' => 'info',
                'title' => 'Info Alert',
                'message' => 'Dies ist ein Info-Alert, der aus EditorJS JSON gerendert wurde.'
            ]
        ],
        [
            'type' => 'alert',
            'data' => [
                'type' => 'warning',
                'title' => 'Warnung',
                'message' => 'Dies ist eine Warnung mit gelbem Hintergrund.'
            ]
        ],
        [
            'type' => 'alert',
            'data' => [
                'type' => 'error',
                'title' => 'Fehler',
                'message' => 'Dies ist ein Fehler-Alert mit rotem Hintergrund.'
            ]
        ],
        [
            'type' => 'alert',
            'data' => [
                'type' => 'success',
                'title' => 'Erfolg',
                'message' => 'Dies ist ein Erfolgs-Alert mit grünem Hintergrund.'
            ]
        ],
        [
            'type' => 'textimage',
            'data' => [
                'text' => '<p><strong>Text-Bild-Block</strong></p><p>Dieser Block zeigt, wie Text und Bilder kombiniert werden. <em>Formatierung</em> wird unterstützt.</p><ul><li>Bild links vom Text</li><li>Responsive Design</li><li>Bildunterschrift</li></ul>',
                'imageFile' => 'beispiel.jpg',
                'imageUrl' => 'https://placehold.co/300x200/007bff/ffffff?text=Demo+Bild+Links',
                'caption' => 'Ein Beispielbild aus dem Medienpool',
                'layout' => 'left'
            ]
        ],
        [
            'type' => 'textimage',
            'data' => [
                'text' => '<p><strong>Rechtes Layout</strong></p><p>Hier ist das Bild rechts vom Text positioniert.</p>',
                'imageFile' => 'beispiel2.jpg',
                'imageUrl' => 'https://placehold.co/300x200/28a745/ffffff?text=Bild+Rechts',
                'caption' => 'Bild rechts vom Text',
                'layout' => 'right'
            ]
        ],
        [
            'type' => 'textimage',
            'data' => [
                'text' => '<p><strong>Top Layout</strong></p><p>Bei diesem Layout ist das Bild über dem Text.</p>',
                'imageFile' => 'beispiel3.jpg',
                'imageUrl' => 'https://placehold.co/600x200/dc3545/ffffff?text=Bild+Oben',
                'caption' => 'Bild über dem Text',
                'layout' => 'top'
            ]
        ],
        [
            'type' => 'list',
            'data' => [
                'style' => 'unordered',
                'items' => [
                    'Unterstützte Standard-Blöcke: Header, Paragraph, List, Quote, Code, Delimiter',
                    'Benutzerdefinierte Blöcke: Alert, TextImage',
                    'Vollständig responsive',
                    'REDAXO-integriert'
                ]
            ]
        ],
        [
            'type' => 'quote',
            'data' => [
                'text' => 'EditorJS bietet eine saubere JSON-Ausgabe statt schwerem HTML-Markup.',
                'caption' => 'EditorJS Dokumentation'
            ]
        ],
        [
            'type' => 'code',
            'data' => [
                'code' => '// PHP Verwendung des Renderers\nuse FriendsOfRedaxo\\EditorJs\\EditorJsRenderer;\n$renderer = new EditorJsRenderer();\n$html = $renderer->render($jsonData);\necho $html;'
            ]
        ],
        [
            'type' => 'delimiter',
            'data' => []
        ],
        [
            'type' => 'paragraph',
            'data' => [
                'text' => 'Der Renderer ist fertig zur Verwendung in Ihren REDAXO-Projekten!'
            ]
        ]
    ]
]);

// Renderer erstellen und HTML generieren
$renderer = new EditorJsRenderer();
$renderedHTML = $renderer->render($demoJSON);

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">JSON Input</h3>
            </header>
            <div class="panel-body">
                <p><strong>Verwendung:</strong></p>
                <pre><code>// EditorJS Renderer laden
use FriendsOfRedaxo\EditorJs\EditorJsRenderer;
require_once rex_addon::get('editorjs')->getPath('lib/EditorJSRenderer.php');

// JSON zu HTML konvertieren
$renderer = new EditorJsRenderer();
$html = $renderer->render($jsonData);

// Oder statisch:
$html = EditorJsRenderer::renderJSON($jsonString);

// Frontend CSS einbinden
EditorJsRenderer::addFrontendCSS();</code></pre>
                
                <details>
                    <summary>JSON-Daten anzeigen (klicken zum aufklappen)</summary>
                    <pre style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #e9ecef; margin-top: 10px;"><?= htmlspecialchars(json_encode(json_decode($demoJSON), JSON_PRETTY_PRINT)) ?></pre>
                </details>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">HTML Output (Frontend-Vorschau)</h3>
            </header>
            <div class="panel-body">
                <!-- Frontend CSS einbinden -->
                <link rel="stylesheet" href="<?= rex_addon::get('editorjs')->getAssetsUrl('css/editorjs-frontend.css') ?>">
                
                <!-- Gerenderte HTML-Ausgabe -->
                <div style="border: 1px solid #ddd; padding: 20px; background: #fff;">
                    <?= $renderedHTML ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">Generierter HTML-Code</h3>
            </header>
            <div class="panel-body">
                <pre style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px; border: 1px solid #e9ecef;"><?= htmlspecialchars($renderedHTML) ?></pre>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h4>Integration in Templates</h4>
            <p>Um den Renderer in Ihren Templates zu verwenden:</p>
            <ol>
                <li>EditorJS-Feld in Ihrem Modul/Metainfo definieren</li>
                <li>JSON-Daten aus der Datenbank laden</li>
                <li>Renderer verwenden: <code>EditorJsRenderer::renderJSON($jsonData)</code></li>
                <li>Frontend-CSS einbinden: <code>EditorJsRenderer::addFrontendCSS()</code></li>
            </ol>
        </div>
    </div>
</div>
