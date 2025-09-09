<?php
/**
 * Beispiel-Modul mit selektiver Tool-Auswahl
 * Demonstriert die Verwendung von data-editorjs-tools und neue Card/Video Features
 */

// Verschiedene Beispiele für Tool-Konfigurationen

echo '<h3>EditorJS mit nur Text-Tools (paragraph, header, list)</h3>';
echo '<div id="editor-text-only" data-editorjs-tools="paragraph,header,list" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[1]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>🆕 EditorJS mit Cards Section & Card Features</h3>';
echo '<div class="alert alert-info" style="margin-bottom: 10px;">';
echo '<strong>Cards Section:</strong> Container für mehrere Cards mit Grid-Layout | ';
echo '<strong>Card:</strong> Einzelne Card-Elemente';
echo '</div>';
echo '<div id="editor-cards" data-editorjs-tools="paragraph,cards,card,textimage" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[2]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>🎯 Cards Section Demo - <a href="cards-section-demo.php" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i> Vollständige Demo öffnen</a></h3>';
echo '<div class="alert alert-warning" style="margin-bottom: 10px;">';
echo '<i class="fa fa-lightbulb-o"></i> <strong>Tipp:</strong> Öffnen Sie die vollständige Cards Section Demo für erweiterte Funktionen und Beispiele.';
echo '</div>';
echo '<div id="editor-cards-section-preview" data-editorjs-tools="cards" style="border: 1px solid #ddd; min-height: 150px; padding: 10px; background: #f8f9fa;"></div>';
echo '<textarea name="REX_INPUT_VALUE[6]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>EditorJS mit Medien-Tools (paragraph, image, video, textimage, downloads, gallery)</h3>';
echo '<div id="editor-media" data-editorjs-tools="paragraph,image,video,textimage,downloads,gallery" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[3]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>EditorJS mit allen Tools (Standard)</h3>';
echo '<div id="editor-full" data-editorjs style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[4]" data-editorjs-data style="display: none;"></textarea>';

echo '<hr style="margin: 30px 0;">';

echo '<h3>EditorJS nur für Zitate und Code (quote, code, paragraph)</h3>';
echo '<div id="editor-minimal" data-editorjs-tools="quote,code,paragraph" style="border: 1px solid #ddd; min-height: 200px; padding: 10px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[5]" data-editorjs-data style="display: none;"></textarea>';

?>

<div class="alert alert-info" style="margin-top: 20px;">
    <h4><i class="fa fa-info-circle"></i> Neue Features testen</h4>
    <div class="row">
        <div class="col-md-6">
            <h5>🆕 Cards Section (Container)</h5>
            <ul class="list-unstyled">
                <li>• <strong>Container für mehrere Cards</strong></li>
                <li>• Grid-Layout mit 1-4 Spalten</li>
                <li>• Drag & Drop für Sortierung</li>
                <li>• Jede Card: Media + Titel + Text</li>
                <li>• ALT-Text Unterstützung mit Warnung</li>
                <li>• Video-Wiedergabe im Editor</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h5>🔄 Einzelne Card</h5>
            <ul class="list-unstyled">
                <li>• <strong>Einzelne Card-Elemente</strong></li>
                <li>• Vertikal/Horizontal Layout</li>
                <li>• Media (Bild/Video) + Content</li>
                <li>• Lightbox für Bilder</li>
                <li>• Video-Steuerung</li>
                <li>• REX Link Integration</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tool-Selection Demo loaded with new Card & Video features');
    
    // Event-Listener für Editor-Ready Events
    document.addEventListener('editorjs:ready', function(event) {
        console.log('Editor ready:', event.detail.container.id, 'Tools available:', Object.keys(event.detail.editor.configuration.tools));
        
        // Spezielle Hinweise für neue Features
        const container = event.detail.container;
        if (container.id === 'editor-cards') {
            console.log('🆕 Cards Section & Card Demo aktiv - probieren Sie die neuen Features!');
        }
    });
    
    // Hinweis-System für neue Features
    setTimeout(function() {
        const cardEditor = document.getElementById('editor-cards');
        if (cardEditor && !cardEditor.querySelector('.new-feature-hint')) {
            const hint = document.createElement('div');
            hint.className = 'new-feature-hint alert alert-success';
            hint.style.cssText = 'position: absolute; top: 10px; right: 10px; z-index: 1000; padding: 5px 10px; font-size: 12px; border-radius: 3px;';
            hint.innerHTML = '🆕 Cards Section & Card Features aktiv!';
            cardEditor.style.position = 'relative';
            cardEditor.appendChild(hint);
            
            setTimeout(() => hint.remove(), 3000);
        }
    }, 2000);
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
