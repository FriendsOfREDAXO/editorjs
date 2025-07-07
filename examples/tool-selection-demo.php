<?php
/**
 * Beispiel-Modul mit selektiver Tool-Auswahl
 * Demonstriert die Verwendung von data-editorjs-tools
 */

// Verschiedene Beispiele für Tool-Konfigurationen

echo '<h3>EditorJS mit nur Text-Tools (paragraph, header, list)</h3>';
echo '<div id="editor-text-only" data-editorjs-tools="paragraph,header,list" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[1]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>EditorJS mit Medien-Tools (paragraph, image, textimage, downloads)</h3>';
echo '<div id="editor-media" data-editorjs-tools="paragraph,image,textimage,downloads" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[2]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>EditorJS mit allen Tools (Standard)</h3>';
echo '<div id="editor-full" data-editorjs style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[3]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>EditorJS nur für Zitate und Code (quote, code, paragraph)</h3>';
echo '<div id="editor-minimal" data-editorjs-tools="quote,code,paragraph" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[4]" data-editorjs-data style="display: none;"></textarea>';

?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tool-Selection Demo loaded');
    
    // Event-Listener für Editor-Ready Events
    document.addEventListener('editorjs:ready', function(event) {
        console.log('Editor ready:', event.detail.container.id, 'Tools available:', Object.keys(event.detail.editor.configuration.tools));
    });
});
</script>

<style>
.codex-editor {
    border-radius: 4px;
}

.codex-editor__redactor {
    padding: 15px !important;
}

h3 {
    color: #333;
    margin-top: 0;
}
</style>
