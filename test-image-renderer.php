<?php

// Test für ImageBlock Rendering
require_once 'lib/EditorJSRenderer.php';

use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

// Test-JSON Daten (wie vom Benutzer bereitgestellt)
$testJson = '{
    "time": 1751903242711,
    "blocks": [
        {
            "id": "NKBUq7UFkI",
            "type": "ImageBlock",
            "data": {
                "imageFile": "bildschirmfoto_2025-06-10_um_13.39.36.png",
                "imageUrl": "/index.php?rex_media_type=rex_mediapool_detail&rex_media_file=bildschirmfoto_2025-06-10_um_13.39.36.png",
                "imageAlt": "",
                "caption": "dsdsf",
                "stretched": true,
                "withBorder": true,
                "withBackground": true,
                "aspectRatio": "16-9",
                "cropMode": "contain"
            }
        },
        {
            "id": "dtYE8WGwyE",
            "type": "TextImageBlock",
            "data": {
                "text": "xycyxcyxcyxcxyc yxysd",
                "imageFile": "a022cf31-82f6-440b-87aa-0954687bc665.png",
                "imageUrl": "/index.php?rex_media_type=rex_mediapool_detail&rex_media_file=a022cf31-82f6-440b-87aa-0954687bc665.png",
                "imageAlt": "",
                "caption": "",
                "layout": "left",
                "stretched": false
            }
        },
        {
            "id": "Z81E-ZmcqQ",
            "type": "paragraph",
            "data": {
                "text": ""
            }
        }
    ],
    "version": "2.31.0-rc.7"
}';

// Debug: Zeige was der Renderer macht
echo "<h1>EditorJS ImageBlock Debug</h1>\n";

echo "<h2>Teste einzelnen ImageBlock:</h2>\n";
$renderer = new EditorJsRenderer();

// Teste nur den ImageBlock
$imageBlockData = [
    "imageFile" => "bildschirmfoto_2025-06-10_um_13.39.36.png",
    "imageUrl" => "/index.php?rex_media_type=rex_mediapool_detail&rex_media_file=bildschirmfoto_2025-06-10_um_13.39.36.png",
    "imageAlt" => "",
    "caption" => "dsdsf",
    "stretched" => true,
    "withBorder" => true,
    "withBackground" => true,
    "aspectRatio" => "16-9",
    "cropMode" => "contain"
];

echo "<h3>ImageBlock Data:</h3>\n";
echo "<pre>" . print_r($imageBlockData, true) . "</pre>\n";

echo "<h3>Rendered ImageBlock HTML:</h3>\n";
$imageHtml = $renderer->renderImage($imageBlockData);
echo "<pre>" . htmlspecialchars($imageHtml) . "</pre>\n";

echo "<h3>Visual Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px;'>\n";
echo $imageHtml;
echo "</div>\n";

echo "<h1>EditorJS ImageBlock Test</h1>\n";
echo "<h2>Input JSON:</h2>\n";
echo "<pre>" . htmlspecialchars($testJson) . "</pre>\n";

echo "<h2>Rendered HTML:</h2>\n";

$renderer = new EditorJsRenderer();
$html = $renderer->render($testJson);

echo "<h3>HTML Code:</h3>\n";
echo "<pre>" . htmlspecialchars($html) . "</pre>\n";

echo "<h3>Rendered Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>\n";
echo $html;
echo "</div>\n";

?>
