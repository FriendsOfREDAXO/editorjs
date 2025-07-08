<?php

// Test für VideoBlock Rendering
require_once 'lib/EditorJSRenderer.php';

use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

echo "<h1>EditorJS VideoBlock Test</h1>\n";

$renderer = new EditorJsRenderer();

// Test 1: Video mit allen Eigenschaften
echo "<h2>Test 1: Video mit allen Eigenschaften</h2>\n";
$videoBlockData1 = [
    "videoFile" => "demo-video.mp4",
    "videoUrl" => "/media/demo-video.mp4",
    "poster" => "video-poster.jpg",
    "posterUrl" => "/media/video-poster.jpg",
    "caption" => "Ein Beispiel-Video mit allen Features",
    "stretched" => true,
    "withBorder" => true,
    "withBackground" => true,
    "aspectRatio" => "16-9",
    "autoplay" => false,
    "muted" => false,
    "loop" => false,
    "controls" => true
];

echo "<h3>VideoBlock Data:</h3>\n";
echo "<pre>" . print_r($videoBlockData1, true) . "</pre>\n";

echo "<h3>Rendered VideoBlock HTML:</h3>\n";
$videoHtml1 = $renderer->renderVideo($videoBlockData1);
echo "<pre>" . htmlspecialchars($videoHtml1) . "</pre>\n";

echo "<h3>Visual Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>\n";
echo $videoHtml1;
echo "</div>\n";

// Test 2: Einfaches Video ohne Poster
echo "<h2>Test 2: Einfaches Video ohne Poster</h2>\n";
$videoBlockData2 = [
    "videoFile" => "simple-video.webm",
    "caption" => "Ein einfaches Video",
    "stretched" => false,
    "withBorder" => false,
    "withBackground" => false,
    "aspectRatio" => "",
    "autoplay" => false,
    "muted" => false,
    "loop" => true,
    "controls" => true
];

echo "<h3>VideoBlock Data:</h3>\n";
echo "<pre>" . print_r($videoBlockData2, true) . "</pre>\n";

echo "<h3>Rendered VideoBlock HTML:</h3>\n";
$videoHtml2 = $renderer->renderVideo($videoBlockData2);
echo "<pre>" . htmlspecialchars($videoHtml2) . "</pre>\n";

echo "<h3>Visual Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>\n";
echo $videoHtml2;
echo "</div>\n";

// Test 3: Video mit Autoplay und Muted
echo "<h2>Test 3: Video mit Autoplay und Muted</h2>\n";
$videoBlockData3 = [
    "videoFile" => "autoplay-video.mp4",
    "caption" => "Auto-Play Video (stumm)",
    "stretched" => true,
    "withBorder" => false,
    "withBackground" => true,
    "aspectRatio" => "4-3",
    "autoplay" => true,
    "muted" => true,
    "loop" => true,
    "controls" => false
];

echo "<h3>VideoBlock Data:</h3>\n";
echo "<pre>" . print_r($videoBlockData3, true) . "</pre>\n";

echo "<h3>Rendered VideoBlock HTML:</h3>\n";
$videoHtml3 = $renderer->renderVideo($videoBlockData3);
echo "<pre>" . htmlspecialchars($videoHtml3) . "</pre>\n";

echo "<h3>Visual Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>\n";
echo $videoHtml3;
echo "</div>\n";

// Test 4: Fehlerfall - kein Video
echo "<h2>Test 4: Fehlerfall - kein Video</h2>\n";
$videoBlockData4 = [
    "caption" => "Fehlerfall ohne Video-Datei"
];

echo "<h3>VideoBlock Data:</h3>\n";
echo "<pre>" . print_r($videoBlockData4, true) . "</pre>\n";

echo "<h3>Rendered VideoBlock HTML:</h3>\n";
$videoHtml4 = $renderer->renderVideo($videoBlockData4);
echo "<pre>" . htmlspecialchars($videoHtml4) . "</pre>\n";

echo "<h3>Visual Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>\n";
echo $videoHtml4;
echo "</div>\n";

// Test 5: JSON mit VideoBlock
echo "<h2>Test 5: Vollständiger JSON mit VideoBlock</h2>\n";
$testJson = '{
    "time": 1751903242711,
    "blocks": [
        {
            "id": "video123",
            "type": "VideoBlock",
            "data": {
                "videoFile": "presentation.mp4",
                "videoUrl": "/media/presentation.mp4",
                "poster": "presentation-thumb.jpg",
                "caption": "Präsentations-Video",
                "stretched": true,
                "withBorder": false,
                "withBackground": true,
                "aspectRatio": "16-9",
                "autoplay": false,
                "muted": false,
                "loop": false,
                "controls": true
            }
        }
    ],
    "version": "2.31.0-rc.7"
}';

echo "<h3>JSON Input:</h3>\n";
echo "<pre>" . htmlspecialchars($testJson) . "</pre>\n";

echo "<h3>Rendered HTML:</h3>\n";
$html = $renderer->render($testJson);
echo "<pre>" . htmlspecialchars($html) . "</pre>\n";

echo "<h3>Visual Result:</h3>\n";
echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>\n";
echo $html;
echo "</div>\n";

?>
