<?php
/**
 * REDAXO Modul-Beispiel mit eingeschränkten EditorJS-Tools
 * Demonstriert die Tool-Filterung und das Datenpersistierungs-Problem
 */

// Eingabe-Template (INPUT)
?>
<div class="rex-form-element">
    <h3>EditorJS mit eingeschränkten Tools (nur header, paragraph, list)</h3>
    <p>Dieses Beispiel zeigt ein REDAXO-Modul mit eingeschränkten Tools.</p>
    
    <!-- EditorJS Container mit Tool-Filterung -->
    <div id="editorjs-restricted" 
         class="editorjs" 
         data-editorjs-tools="header,paragraph,list"
         data-placeholder="Inhalt eingeben... (nur Überschriften, Absätze und Listen erlaubt)">
    </div>
    
    <!-- Verstecktes Datenfeld -->
    <textarea name="REX_INPUT_VALUE[1]" 
              data-editorjs-data 
              style="display: none;"><?= htmlspecialchars("REX_VALUE[1]"); ?></textarea>
</div>

<div class="rex-form-element">
    <h3>Test-Daten vorab laden</h3>
    <p>Diese Buttons füllen den Editor mit Test-Daten verschiedener Tool-Typen:</p>
    
    <button type="button" onclick="loadRestrictedTestData()">
        Erlaubte Tools laden (header, paragraph, list)
    </button>
    
    <button type="button" onclick="loadMixedTestData()">
        Gemischte Tools laden (inkl. nicht-erlaubte Tools)
    </button>
    
    <button type="button" onclick="clearEditor()">
        Editor leeren
    </button>
</div>

<script>
function loadRestrictedTestData() {
    const editor = window.editorInstances['editorjs-restricted'];
    if (editor) {
        editor.render({
            "time": Date.now(),
            "blocks": [
                {
                    "id": "test1",
                    "type": "header",
                    "data": {
                        "text": "Test Überschrift",
                        "level": 2
                    }
                },
                {
                    "id": "test2", 
                    "type": "paragraph",
                    "data": {
                        "text": "Dies ist ein Test-Absatz mit <strong>formatiertem Text</strong>."
                    }
                },
                {
                    "id": "test3",
                    "type": "list",
                    "data": {
                        "style": "unordered",
                        "items": [
                            "Erster Listenpunkt",
                            "Zweiter Listenpunkt", 
                            "Dritter Listenpunkt"
                        ]
                    }
                }
            ],
            "version": "2.22.2"
        });
    }
}

function loadMixedTestData() {
    const editor = window.editorInstances['editorjs-restricted'];
    if (editor) {
        editor.render({
            "time": Date.now(),
            "blocks": [
                {
                    "id": "test1",
                    "type": "header",
                    "data": {
                        "text": "Gemischte Test-Daten",
                        "level": 2
                    }
                },
                {
                    "id": "test2",
                    "type": "paragraph", 
                    "data": {
                        "text": "Dies ist ein erlaubter Absatz."
                    }
                },
                {
                    "id": "test3",
                    "type": "alert",
                    "data": {
                        "type": "warning",
                        "message": "Dies ist ein Alert-Block (normalerweise nicht erlaubt)"
                    }
                },
                {
                    "id": "test4",
                    "type": "list",
                    "data": {
                        "style": "ordered",
                        "items": [
                            "Erlaubter Listenpunkt",
                            "Noch ein erlaubter Punkt"
                        ]
                    }
                },
                {
                    "id": "test5",
                    "type": "quote",
                    "data": {
                        "text": "Dies ist ein Zitat-Block (normalerweise nicht erlaubt)",
                        "caption": "Autor"
                    }
                }
            ],
            "version": "2.22.2"
        });
    }
}

function clearEditor() {
    const editor = window.editorInstances['editorjs-restricted'];
    if (editor) {
        editor.render({
            "time": Date.now(),
            "blocks": [],
            "version": "2.22.2"
        });
    }
}
</script>

<?php
// Ausgabe-Template (OUTPUT)
if ("REX_VALUE[1]") {
    $data = json_decode("REX_VALUE[1]", true);
    if ($data && isset($data['blocks']) && !empty($data['blocks'])) {
        
        // EditorJS Renderer verwenden
        require_once __DIR__ . '/../lib/EditorJSRenderer.php';
        $renderer = new EditorJSRenderer();
        
        echo '<div class="editorjs-output">';
        echo $renderer->render("REX_VALUE[1]");
        echo '</div>';
        
        // Debug-Ausgabe in einem Details-Element
        echo '<details style="margin-top: 20px; border: 1px solid #ddd; padding: 10px;">';
        echo '<summary>Debug: Gespeicherte JSON-Daten</summary>';
        echo '<pre style="background: #f5f5f5; padding: 10px; font-size: 12px; overflow-x: auto;">';
        echo htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo '</pre>';
        echo '</details>';
    }
}
?>
