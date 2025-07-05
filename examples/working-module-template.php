<?php
/**
 * Funktionierendes REDAXO Modul-Beispiel mit EditorJS
 * Verwendet die klassische Div + Textarea Methode (EMPFOHLEN)
 */

// Eingabe-Template (INPUT)
?>
<div class="form-group">
    <label>Artikel Inhalt (EditorJS - klassische Methode)</label>
    <p><small>Diese Methode funktioniert zuverlässig und ist für Produktionsumgebungen empfohlen.</small></p>
    
    <!-- EditorJS Container -->
    <div class="editorjs" 
         data-placeholder="Artikel schreiben..."
         style="border: 1px solid #ddd; min-height: 400px; padding: 10px;">
    </div>
    
    <!-- Verstecktes Datenfeld -->
    <textarea name="REX_INPUT_VALUE[1]" 
              data-editorjs-data 
              style="display: none;">REX_VALUE[id=1 output=html]</textarea>
</div>

<div class="form-group">
    <label>Einfacher Editor (nur Text-Tools)</label>
    <p><small>Beispiel mit Tool-Filterung - nur grundlegende Text-Tools verfügbar.</small></p>
    
    <!-- EditorJS Container mit Tool-Filterung -->
    <div class="editorjs" 
         data-placeholder="Einfachen Artikel schreiben..."
         data-editorjs-tools="paragraph,header,list,quote"
         style="border: 1px solid #ddd; min-height: 300px; padding: 10px;">
    </div>
    
    <!-- Verstecktes Datenfeld -->
    <textarea name="REX_INPUT_VALUE[2]" 
              data-editorjs-data 
              style="display: none;">REX_VALUE[id=2 output=html]</textarea>
</div>

<div class="form-group">
    <label>Vollständiger Editor (alle Tools)</label>
    <p><small>Beispiel mit allen verfügbaren Tools und eigenen Blöcken.</small></p>
    
    <!-- EditorJS Container mit allen Tools -->
    <div class="editorjs" 
         data-placeholder="Vollständigen Artikel mit Media schreiben..."
         data-editorjs-tools="paragraph,header,list,quote,delimiter,code,alert,textimage,image,video,downloads"
         style="border: 1px solid #ddd; min-height: 400px; padding: 10px;">
    </div>
    
    <!-- Verstecktes Datenfeld -->
    <textarea name="REX_INPUT_VALUE[3]" 
              data-editorjs-data 
              style="display: none;">REX_VALUE[id=3 output=html]</textarea>
</div>

<div class="alert alert-success">
    <strong><i class="fa fa-check"></i> Funktionierender Aufbau:</strong>
    <ul>
        <li><strong>Div mit Klasse "editorjs"</strong> - wird automatisch erkannt</li>
        <li><strong>Textarea mit "data-editorjs-data"</strong> - wird automatisch als Datenspeicher verknüpft</li>
        <li><strong>REX_VALUE[id=X output=html]</strong> - korrekte REDAXO-Syntax für JSON-Daten</li>
        <li><strong>Automatisches Speichern</strong> - Änderungen werden sofort im Textarea gespeichert</li>
    </ul>
</div>

<?php
// Ausgabe-Template (OUTPUT)

// Renderer nur einmal laden
require_once __DIR__ . '/../lib/EditorJSRenderer.php';
$renderer = new EditorJSRenderer();

echo '<div class="editorjs-output">';

// Hauptinhalt (Feld 1)
if ("REX_VALUE[id=1 output=html]") {
    echo '<div class="main-content">';
    echo '<h2>Hauptinhalt:</h2>';
    echo $renderer->render("REX_VALUE[id=1 output=html]");
    echo '</div>';
}

// Einfacher Inhalt (Feld 2)
if ("REX_VALUE[id=2 output=html]") {
    echo '<div class="simple-content">';
    echo '<h2>Einfacher Inhalt:</h2>';
    echo $renderer->render("REX_VALUE[id=2 output=html]");
    echo '</div>';
}

// Vollständiger Inhalt (Feld 3)
if ("REX_VALUE[id=3 output=html]") {
    echo '<div class="full-content">';
    echo '<h2>Vollständiger Inhalt:</h2>';
    echo $renderer->render("REX_VALUE[id=3 output=html]");
    echo '</div>';
}

echo '</div>';

// Debug-Ausgabe (nur für Entwicklung)
if (rex::isDebugMode()) {
    echo '<details style="margin-top: 20px; border: 1px solid #ddd; padding: 10px;">';
    echo '<summary>Debug: Gespeicherte JSON-Daten</summary>';
    
    echo '<h4>Feld 1 (Hauptinhalt):</h4>';
    echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; overflow-x: auto;">';
    echo htmlspecialchars("REX_VALUE[id=1 output=html]");
    echo '</pre>';
    
    echo '<h4>Feld 2 (Einfacher Inhalt):</h4>';
    echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; overflow-x: auto;">';
    echo htmlspecialchars("REX_VALUE[id=2 output=html]");
    echo '</pre>';
    
    echo '<h4>Feld 3 (Vollständiger Inhalt):</h4>';
    echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; overflow-x: auto;">';
    echo htmlspecialchars("REX_VALUE[id=3 output=html]");
    echo '</pre>';
    
    echo '</details>';
}
?>
