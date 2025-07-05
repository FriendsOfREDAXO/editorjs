<?php
/**
 * REDAXO Modul-Beispiel mit direkter EditorJS-Initialisierung
 * Zeigt die einfachste Methode - bestehende Textareas werden automatisch zu Editoren
 */

// Eingabe-Template (INPUT)
?>
<div class="form-group">
    <label>Einfachster EditorJS (Direktinitialisierung)</label>
    <p><small>Das Textarea wird automatisch in einen EditorJS-Editor umgewandelt. Keine zusätzlichen Divs erforderlich!</small></p>
    
    <!-- Einfachstes Beispiel: Nur das data-editorjs-direct Attribut hinzufügen -->
    <textarea data-editorjs-direct 
              name="REX_INPUT_VALUE[1]" 
              placeholder="Artikel schreiben..." 
              style="width: 100%; min-height: 200px;">REX_VALUE[id=1 output=html]</textarea>
</div>

<div class="form-group">
    <label>Editor mit Tool-Filterung (Direktinitialisierung)</label>
    <p><small>Nur bestimmte Tools sind verfügbar - perfekt für eingeschränkte Editoren.</small></p>
    
    <!-- Mit Tool-Filterung -->
    <textarea data-editorjs-direct 
              data-editorjs-tools="paragraph,header,list,quote"
              name="REX_INPUT_VALUE[2]" 
              placeholder="Einfachen Artikel schreiben..." 
              style="width: 100%; min-height: 200px;">REX_VALUE[id=2 output=html]</textarea>
</div>

<div class="form-group">
    <label>Vollständiger Editor (Direktinitialisierung)</label>
    <p><small>Alle Tools verfügbar - für komplexe Inhalte mit Media.</small></p>
    
    <!-- Alle Tools -->
    <textarea data-editorjs-direct 
              data-editorjs-tools="paragraph,header,list,quote,delimiter,code,alert,textimage,image,video,downloads"
              name="REX_INPUT_VALUE[3]" 
              placeholder="Vollständigen Artikel mit Media schreiben..." 
              style="width: 100%; min-height: 300px;">REX_VALUE[id=3 output=html]</textarea>
</div>

<div class="alert alert-success">
    <strong><i class="fa fa-magic"></i> Direktinitialisierung - So einfach geht's:</strong>
    <ul>
        <li><strong>1.</strong> Fügen Sie <code>data-editorjs-direct</code> zu einem bestehenden Textarea hinzu</li>
        <li><strong>2.</strong> Verwenden Sie <code>REX_VALUE[id=X output=html]</code> für die Daten</li>
        <li><strong>3.</strong> Optional: <code>data-editorjs-tools</code> für Tool-Filterung</li>
        <li><strong>4.</strong> Fertig! Das Textarea wird automatisch zum Editor</li>
    </ul>
</div>

<div class="alert alert-info">
    <strong><i class="fa fa-cogs"></i> Was passiert automatisch:</strong>
    <ol>
        <li>Ein Container-Div wird vor dem Textarea erstellt</li>
        <li>Das Textarea wird ausgeblendet und dient als Datenspeicher</li>
        <li>Der Placeholder wird übernommen</li>
        <li>Tool-Filterung wird vom Textarea übernommen</li>
        <li>Robuste Datenverknüpfung wird hergestellt</li>
        <li>Automatisches Speichern bei jeder Änderung</li>
    </ol>
</div>

<?php
// Ausgabe-Template (OUTPUT)

// Renderer laden
require_once __DIR__ . '/../lib/EditorJSRenderer.php';
$renderer = new EditorJSRenderer();

echo '<div class="editorjs-output">';

// Einfacher Editor (Feld 1)
if ("REX_VALUE[id=1 output=html]") {
    echo '<div class="content-section">';
    echo '<h2>Einfacher Inhalt:</h2>';
    echo $renderer->render("REX_VALUE[id=1 output=html]");
    echo '</div>';
}

// Eingeschränkter Editor (Feld 2)
if ("REX_VALUE[id=2 output=html]") {
    echo '<div class="content-section">';
    echo '<h2>Eingeschränkter Editor-Inhalt:</h2>';
    echo $renderer->render("REX_VALUE[id=2 output=html]");
    echo '</div>';
}

// Vollständiger Editor (Feld 3)
if ("REX_VALUE[id=3 output=html]") {
    echo '<div class="content-section">';
    echo '<h2>Vollständiger Editor-Inhalt:</h2>';
    echo $renderer->render("REX_VALUE[id=3 output=html]");
    echo '</div>';
}

echo '</div>';

// Debug-Informationen für Entwicklung
if (rex::isDebugMode()) {
    echo '<details style="margin-top: 20px; border: 1px solid #ddd; padding: 10px;">';
    echo '<summary>Debug: Direktinitialisierung Details</summary>';
    
    echo '<h4>Feld 1 (Einfach):</h4>';
    echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; word-break: break-all;">';
    echo htmlspecialchars("REX_VALUE[id=1 output=html]");
    echo '</pre>';
    
    echo '<h4>Feld 2 (Eingeschränkt):</h4>';
    echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; word-break: break-all;">';
    echo htmlspecialchars("REX_VALUE[id=2 output=html]");
    echo '</pre>';
    
    echo '<h4>Feld 3 (Vollständig):</h4>';
    echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; word-break: break-all;">';
    echo htmlspecialchars("REX_VALUE[id=3 output=html]");
    echo '</pre>';
    
    echo '</details>';
}
?>

<style>
.content-section {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    background: #fafafa;
}

.content-section h2 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #007cba;
    padding-bottom: 10px;
}
</style>
