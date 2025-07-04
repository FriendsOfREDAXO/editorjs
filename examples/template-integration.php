<?php
/**
 * Beispiel: EditorJS Integration in REDAXO Templates
 * 
 * Dieses Beispiel zeigt, wie Sie EditorJS-Felder in Ihren REDAXO-Templates verwenden.
 */

use FriendsOfRedaxo\EditorJs\EditorJsRenderer;

// Beispiel 1: Einfache Verwendung mit Metainfo-Feld
// ----------------------------------------------
// Annahme: Sie haben ein Metainfo-Feld "editorjs_content" vom Typ "textarea"

$editorjsContent = rex_article::getCurrent()->getValue('editorjs_content');

if ($editorjsContent) {
    // EditorJS Renderer automatisch laden (durch boot.php)
    $html = EditorJsRenderer::renderJSON($editorjsContent);
    echo $html;
}

// Beispiel 2: Verwendung in einem Modul
// ------------------------------------
/*
// INPUT (Modul-Eingabe):
<label for="REX_INPUT_VALUE[1]">EditorJS Content:</label>
<div id="editorjs-REX_INPUT_VALUE[1]" class="editorjs-field">
    <textarea name="REX_INPUT_VALUE[1]" id="REX_INPUT_VALUE[1]" style="display: none;">REX_VALUE[1]</textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof EditorJS !== 'undefined') {
        var initialData = REX_VALUE[1] ? JSON.parse(REX_VALUE[1]) : {blocks: []};
        
        var editor = new EditorJS({
            holder: 'editorjs-REX_INPUT_VALUE[1]',
            data: initialData,
            tools: {
                header: Header,
                paragraph: Paragraph,
                list: List,
                quote: Quote,
                delimiter: Delimiter,
                code: CodeTool,
                alert: AlertBlock,
                textimage: TextImageBlock
            },
            onChange: function() {
                editor.save().then(function(data) {
                    document.getElementById('REX_INPUT_VALUE[1]').value = JSON.stringify(data);
                });
            }
        });
    }
});
</script>

// OUTPUT (Modul-Ausgabe):
<?php
if (REX_VALUE[1]) {
    echo EditorJsRenderer::renderJSON(REX_VALUE[1]);
}
?>
*/

// Beispiel 3: Erweiterte Verwendung mit Fehlerbehandlung
// -----------------------------------------------------
function renderEditorJSContent($jsonData) {
    try {
        if (empty($jsonData)) {
            return '';
        }
        
        // JSON validieren
        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return '<div class="alert alert-warning">Ungültige EditorJS-Daten</div>';
        }
        
        // Renderer verwenden
        $renderer = new EditorJsRenderer();
        return $renderer->render($data);
        
    } catch (Exception $e) {
        // Fehler loggen
        rex_logger::logException($e);
        
        // Fallback anzeigen
        return '<div class="alert alert-danger">Fehler beim Rendern des Inhalts</div>';
    }
}

// Beispiel 4: Custom Renderer für spezielle Anforderungen
// ------------------------------------------------------
class CustomEditorJSRenderer extends EditorJsRenderer 
{
    public function __construct() {
        parent::__construct();
        
        // Eigene Block-Renderer hinzufügen würde hier eigene Logik erfordern
        // Dies ist nur ein Beispiel-Konzept
    }
    
    public function renderCustomBlock(array $data): string {
        // Eigene Block-Logik hier
        return '<div class="custom-block">' . htmlspecialchars($data['content'] ?? '') . '</div>';
    }
}

// Beispiel 5: Integration mit YForm
// --------------------------------
/*
// In einer YForm-Tabelle mit einem EditorJS-Feld:
$table = rex_yform_manager_table::get('rex_my_table');
$items = $table->query()->find();

foreach ($items as $item) {
    $content = $item->getValue('editorjs_field');
    if ($content) {
        echo '<article>';
        echo '<h2>' . htmlspecialchars($item->getValue('title')) . '</h2>';
        echo EditorJsRenderer::renderJSON($content);
        echo '</article>';
    }
}
*/

// Beispiel 6: API-Endpoint für JSON-zu-HTML Konvertierung
// -----------------------------------------------------
/*
// In einer API-Route:
rex_extension::register('OUTPUT_FILTER', function(rex_extension_point $ep) {
    if (rex_request::get('api') === 'editorjs-render') {
        $json = rex_post('json', 'string');
        
        if ($json) {
            $html = EditorJsRenderer::renderJSON($json);
            
            header('Content-Type: application/json');
            echo json_encode(['html' => $html]);
            exit;
        }
    }
});
*/

?>

<!-- Frontend CSS wird automatisch durch boot.php eingebunden -->
<!-- Oder manuell einbinden: -->
<?php EditorJsRenderer::addFrontendCSS(); ?>

<div class="container">
    <h1>Mein EditorJS Inhalt</h1>
    
    <?php
    // Inhalt rendern
    $content = rex_article::getCurrent()->getValue('content');
    if ($content) {
        echo renderEditorJSContent($content);
    }
    ?>
</div>
