<?php
/**
 * Test für Downloads Block Renderer
 * 
 * Testet die PHP-Darstellung des Downloads-Blocks mit verschiedenen Dateitypen
 */

// REDAXO-Pfad simulieren (normalerweise automatisch verfügbar)
if (!defined('REX_BACKEND')) {
    define('REX_BACKEND', false);
}

// EditorJS Renderer einbinden
require_once 'lib/EditorJSRenderer.php';

use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

// Mock-Funktionen für REDAXO falls nicht verfügbar
if (!function_exists('rex_url')) {
    class rex_url {
        public static function media($filename) {
            return "/media/{$filename}";
        }
    }
}

if (!function_exists('rex_path')) {
    class rex_path {
        public static function media($filename) {
            return __DIR__ . "/media/{$filename}";
        }
    }
}

// Test-Daten für Downloads-Block
$testData = [
    'time' => time(),
    'blocks' => [
        [
            'type' => 'downloads',
            'data' => [
                'title' => 'Wichtige Dokumente',
                'showTitle' => true,
                'layout' => 'list',
                'items' => [
                    [
                        'file' => 'handbuch.pdf',
                        'title' => 'Benutzerhandbuch',
                        'description' => 'Vollständiges Handbuch zur Nutzung der Software',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'preisliste.xlsx',
                        'title' => 'Aktuelle Preisliste',
                        'description' => 'Preise gültig ab Januar 2024',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'katalog.zip',
                        'title' => 'Produktkatalog',
                        'description' => 'Alle Produkte im Überblick als ZIP-Archiv',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'demo.mp4',
                        'title' => 'Demo-Video',
                        'description' => 'Kurze Produktdemonstration',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'screenshot.png',
                        'title' => 'Screenshot',
                        'description' => 'Bildschirmfoto der Anwendung',
                        'customIcon' => ''
                    ]
                ]
            ]
        ],
        [
            'type' => 'downloads',
            'data' => [
                'title' => 'Kompakt-Layout',
                'showTitle' => true,
                'layout' => 'compact',
                'items' => [
                    [
                        'file' => 'readme.txt',
                        'title' => 'Liesmich',
                        'description' => 'Wichtige Informationen',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'changelog.md',
                        'title' => 'Änderungsprotokoll',
                        'description' => 'Alle Änderungen im Überblick',
                        'customIcon' => ''
                    ]
                ]
            ]
        ],
        [
            'type' => 'downloads',
            'data' => [
                'title' => 'Raster-Layout',
                'showTitle' => true,
                'layout' => 'grid',
                'items' => [
                    [
                        'file' => 'bild1.jpg',
                        'title' => 'Produktbild 1',
                        'description' => 'Hochwertiges Produktbild',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'bild2.jpg',
                        'title' => 'Produktbild 2',
                        'description' => 'Weitere Ansicht',
                        'customIcon' => ''
                    ],
                    [
                        'file' => 'bild3.jpg',
                        'title' => 'Produktbild 3',
                        'description' => 'Detailansicht',
                        'customIcon' => ''
                    ]
                ]
            ]
        ]
    ]
];

// Renderer initialisieren
$renderer = new EditorJsRenderer();
$html = $renderer->render($testData);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloads Block Renderer Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/editorjs-frontend.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .test-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .test-info h3 {
            margin-top: 0;
            color: #0066cc;
        }
        .layout-section {
            margin-bottom: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid #eee;
        }
        .layout-section:last-child {
            border-bottom: none;
        }
        .layout-title {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #495057;
        }
        /* Additional frontend styles for better display */
        .cdx-downloads {
            margin: 20px 0;
        }
        .cdx-downloads__title {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        .cdx-downloads__container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .cdx-downloads[data-layout="grid"] .cdx-downloads__container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .cdx-downloads[data-layout="compact"] .cdx-downloads__item {
            padding: 10px;
        }
        .cdx-downloads__item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .cdx-downloads__item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.15);
        }
        .cdx-downloads__link {
            display: flex;
            align-items: center;
            padding: 15px;
            text-decoration: none;
            color: inherit;
            gap: 15px;
        }
        .cdx-downloads__icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 24px;
        }
        .cdx-downloads__thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
        .cdx-downloads__info {
            flex: 1;
        }
        .cdx-downloads__file-title {
            font-size: 1.1em;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #333;
        }
        .cdx-downloads__file-name {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        .cdx-downloads__description {
            font-size: 0.9em;
            color: #777;
            margin-bottom: 5px;
        }
        .cdx-downloads__file-size {
            font-size: 0.8em;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Downloads Block Renderer Test</h1>
        
        <div class="test-info">
            <h3>Test-Informationen:</h3>
            <p>Dieser Test zeigt die PHP-Darstellung des Downloads-Blocks mit verschiedenen Layouts und Dateitypen.</p>
            <ul>
                <li><strong>Liste:</strong> Vertikale Anordnung der Downloads</li>
                <li><strong>Kompakt:</strong> Kompakte Darstellung mit weniger Padding</li>
                <li><strong>Raster:</strong> Grid-Layout für bessere Übersicht</li>
            </ul>
        </div>

        <div class="layout-section">
            <div class="layout-title">Liste Layout (Standard)</div>
            <?php echo $html; ?>
        </div>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #eee;">
            <h3>Rohe JSON-Daten:</h3>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 12px; overflow-x: auto;">
<?php echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>
            </pre>
        </div>
    </div>
</body>
</html>
