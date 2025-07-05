<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('title'), 'Hilfe & Dokumentation');

?>

<div class="row">
    <div class="col-md-12">
        
        <!-- Übersicht -->
        <div class="panel panel-primary">
            <header class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> EditorJS Initialisierung</h3>
            </header>
            <div class="panel-body">
                <p>Das EditorJS-Addon bietet verschiedene Möglichkeiten zur Initialisierung von Editoren. Alle Methoden unterstützen automatische Erkennung und Tool-Filterung.</p>
                
                <div class="alert alert-success">
                    <strong><i class="fa fa-magic"></i> Automatische Initialisierung:</strong> 
                    Alle EditorJS-Instanzen werden automatisch erkannt und initialisiert. Sie müssen kein JavaScript schreiben!
                </div>
            </div>
        </div>

        <!-- Methode 1: Div + Textarea -->
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">1. Klassische Div + Textarea Initialisierung</h3>
            </header>
            <div class="panel-body">
                <p>Die traditionelle Methode mit einem separaten Container-Div und einem Textarea für die Datenspeicherung.</p>
                
                <h4>REDAXO-Modul Beispiel:</h4>
                <pre><code>&lt;div class="editorjs" data-placeholder="Artikel schreiben..."&gt;&lt;/div&gt;
&lt;textarea data-editorjs-data name="REX_INPUT_VALUE[1]" style="display: none;"&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;</code></pre>

                <h4>Mit Tool-Filterung:</h4>
                <pre><code>&lt;div class="editorjs" 
     data-placeholder="Einfacher Artikel..." 
     data-editorjs-tools="paragraph,header,list,quote"&gt;&lt;/div&gt;
&lt;textarea data-editorjs-data name="REX_INPUT_VALUE[1]" style="display: none;"&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;</code></pre>

                <h4>Eigenschaften:</h4>
                <ul>
                    <li>Container bekommt automatisch eine zufällige ID</li>
                    <li>Textarea wird über <code>data-editorjs-data</code> Attribut gefunden</li>
                    <li>Automatisches Speichern bei Änderungen</li>
                    <li>Unterstützt Form-Groups und verschiedene Suchstrategien</li>
                    <li><strong>Funktioniert zuverlässig</strong> - empfohlen für komplexe Module</li>
                </ul>
            </div>
        </div>

        <!-- Methode 2: Textarea Direkt -->
        <div class="panel panel-success">
            <header class="panel-heading">
                <h3 class="panel-title">2. Textarea-Direktinitialisierung <span class="label label-success">REPARIERT</span></h3>
            </header>
            <div class="panel-body">
                <p>Die einfachste Methode - wandelt ein Textarea automatisch in einen EditorJS-Editor um. Perfekt für bestehende Formulare!</p>
                
                <h4>REDAXO-Modul Beispiel:</h4>
                <pre><code>&lt;textarea data-editorjs-direct name="REX_INPUT_VALUE[1]" placeholder="Artikel schreiben..."&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;</code></pre>

                <h4>Mit Tool-Filterung:</h4>
                <pre><code>&lt;textarea data-editorjs-direct 
          data-editorjs-tools="paragraph,header,list"
          name="REX_INPUT_VALUE[1]" 
          placeholder="Einfacher Editor..."&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;</code></pre>

                <div class="alert alert-success">
                    <strong><i class="fa fa-check-circle"></i> Verbesserte Funktionalität:</strong>
                    <ul>
                        <li>Automatische Container-Erstellung vor dem Textarea</li>
                        <li>Robuste Datenverknüpfung zwischen Editor und Textarea</li>
                        <li>Zuverlässiges Laden und Speichern von Daten</li>
                        <li>Funktioniert auch nach PJAX-Navigation</li>
                        <li>Perfekt für bestehende Formulare</li>
                    </ul>
                </div>
                
                <h4>REDAXO-Modul Beispiel:</h4>
                <pre><code>&lt;textarea data-editorjs-direct name="REX_INPUT_VALUE[1]" placeholder="Artikel schreiben..."&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;</code></pre>

                <h4>Mit Tool-Filterung:</h4>
                <pre><code>&lt;textarea data-editorjs-direct 
          data-editorjs-tools="paragraph,header,list"
          name="REX_INPUT_VALUE[1]" 
          placeholder="Einfacher Editor..."&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;</code></pre>

                <div class="alert alert-danger">
                    <strong><i class="fa fa-exclamation-triangle"></i> WICHTIGER HINWEIS:</strong>
                    <p>Die Direktinitialisierung funktioniert derzeit <strong>NICHT KORREKT</strong>. Der Editor wird nach dem Speichern defekt geladen und Blöcke können nicht hinzugefügt werden.</p>
                    <p><strong>Verwenden Sie stattdessen die klassische Div + Textarea Methode!</strong></p>
                </div>

                <h4>Was passiert automatisch:</h4>
                <ol>
                    <li>Ein Container-Div wird vor dem Textarea erstellt</li>
                    <li>Das Textarea wird ausgeblendet und dient als Datenspeicher</li>
                    <li>Der Placeholder wird übernommen</li>
                    <li>Eine intelligente ID wird generiert</li>
                </ol>
            </div>
        </div>

        <!-- Tool-Filterung -->
        <div class="panel panel-info">
            <header class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-filter"></i> Tool-Filterung</h3>
            </header>
            <div class="panel-body">
                <p>Sie können per <code>data-editorjs-tools</code> Attribut festlegen, welche Tools in einem bestimmten Editor verfügbar sind.</p>
                
                <h4>Verfügbare Tools:</h4>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Standard-Tools:</strong>
                        <ul>
                            <li><code>paragraph</code> - Absätze (immer verfügbar)</li>
                            <li><code>header</code> - Überschriften (H2-H4)</li>
                            <li><code>list</code> - Aufzählungen & Listen</li>
                            <li><code>quote</code> - Zitate</li>
                            <li><code>delimiter</code> - Trennlinien</li>
                            <li><code>code</code> - Code-Blöcke</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Eigene Blöcke:</strong>
                        <ul>
                            <li><code>alert</code> - Alert-Boxen</li>
                            <li><code>textimage</code> - Text + Bild</li>
                            <li><code>image</code> - REDAXO Media</li>
                            <li><code>video</code> - Video-Blöcke</li>
                            <li><code>downloads</code> - Download-Listen</li>
                            <li><code>rexLink</code> - REDAXO Links</li>
                            <li>+ Dynamische Blöcke aus <code>/fragments/</code></li>
                        </ul>
                    </div>
                </div>

                <h4>Praktische Beispiele:</h4>

                <h5>1. Einfacher Text-Editor:</h5>
                <pre><code>&lt;div class="editorjs" data-editorjs-tools="paragraph,header,list"&gt;&lt;/div&gt;</code></pre>

                <h5>2. Blog-Editor mit Media:</h5>
                <pre><code>&lt;div class="editorjs" data-editorjs-tools="paragraph,header,list,quote,image,textimage"&gt;&lt;/div&gt;</code></pre>

                <h5>3. News-Editor mit Alerts:</h5>
                <pre><code>&lt;div class="editorjs" data-editorjs-tools="paragraph,header,alert,list,delimiter"&gt;&lt;/div&gt;</code></pre>

                <h5>4. Download-Seite:</h5>
                <pre><code>&lt;div class="editorjs" data-editorjs-tools="paragraph,header,downloads,alert"&gt;&lt;/div&gt;</code></pre>

                <h4>REDAXO-Modul Beispiele:</h4>

                <h5>Eingabe (Einfacher Artikel):</h5>
                <pre><code>&lt;div class="form-group"&gt;
    &lt;label&gt;Artikel Inhalt&lt;/label&gt;
    &lt;div class="editorjs" 
         data-placeholder="Artikel schreiben..."
         data-editorjs-tools="paragraph,header,list,quote"&gt;&lt;/div&gt;
    &lt;textarea data-editorjs-data name="REX_INPUT_VALUE[1]" style="display: none;"&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;
&lt;/div&gt;</code></pre>

                <h5>Eingabe (Vollständiger Artikel):</h5>
                <pre><code>&lt;div class="form-group"&gt;
    &lt;label&gt;Artikel Inhalt&lt;/label&gt;
    &lt;div class="editorjs" 
         data-placeholder="Vollständigen Artikel schreiben..."
         data-editorjs-tools="paragraph,header,list,quote,image,textimage,video,downloads,alert"&gt;&lt;/div&gt;
    &lt;textarea data-editorjs-data name="REX_INPUT_VALUE[1]" style="display: none;"&gt;REX_VALUE[id=1 output=html]&lt;/textarea&gt;
&lt;/div&gt;</code></pre>

                <h5>Ausgabe:</h5>
                <pre><code>&lt;?php
if ("REX_VALUE[id=1 output=html]") {
    $renderer = new EditorJSRenderer();
    echo $renderer->render("REX_VALUE[id=1 output=html]");
}
?&gt;</code></pre>

                <div class="alert alert-info">
                    <strong><i class="fa fa-lightbulb-o"></i> Hinweise zur Tool-Filterung:</strong>
                    <ul>
                        <li><strong>Komma-getrennt:</strong> Tools mit Komma trennen: <code>"paragraph,header,list"</code></li>
                        <li><strong>Paragraph:</strong> Wird automatisch hinzugefügt (auch wenn nicht angegeben)</li>
                        <li><strong>Bestehende Daten:</strong> Tools für bereits gespeicherte Blöcke werden automatisch hinzugefügt</li>
                        <li><strong>Unbekannte Tools:</strong> Werden ignoriert und in der Konsole gewarnt</li>
                        <li><strong>Container vs. Textarea:</strong> Attribut kann an beiden Elementen stehen</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Debugging -->
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bug"></i> Debugging & Troubleshooting</h3>
            </header>
            <div class="panel-body">
                
                <h4>Häufige Probleme:</h4>
                
                <p><strong>Problem:</strong> Editor wird nicht initialisiert</p>
                <p><strong>Lösung:</strong></p>
                <ul>
                    <li>Prüfen Sie die Browser-Konsole auf Fehlermeldungen</li>
                    <li>Stellen Sie sicher, dass das Textarea das Attribut <code>data-editorjs-data</code> hat</li>
                    <li>Verwenden Sie eindeutige IDs für Container</li>
                </ul>

                <p><strong>Problem:</strong> Daten werden nicht gespeichert</p>
                <p><strong>Lösung:</strong></p>
                <ul>
                    <li>Prüfen Sie, ob das Textarea korrekt verknüpft ist</li>
                    <li>Verwenden Sie die klassische Div+Textarea Methode statt Direktinitialisierung</li>
                    <li>Prüfen Sie die onChange-Events in der Browser-Konsole</li>
                </ul>

                <p><strong>Problem:</strong> Tool-Filterung funktioniert nicht</p>
                <p><strong>Lösung:</strong></p>
                <ul>
                    <li>Prüfen Sie die Schreibweise der Tool-Namen (case-sensitive)</li>
                    <li>Verwenden Sie Kommas zur Trennung: <code>"paragraph,header,list"</code></li>
                    <li>Prüfen Sie die Browser-Konsole auf Tool-Filterung-Logs</li>
                </ul>

                <h4>Debug-Befehle (Browser-Konsole):</h4>
                <pre><code>// Alle aktiven Editor-Instanzen anzeigen
console.log('Editoren:', window.editorInstances);

// Auto-Init Utilities anzeigen
console.log('Auto-Init:', EditorJSAutoInit);

// Alle Editoren neu laden (bei Problemen)
EditorJSAutoInit.forceReinit();

// Alle Editoren bereinigen
EditorJSAutoInit.cleanupAllEditors();</code></pre>

                <div class="alert alert-warning">
                    <h4><i class="fa fa-bug"></i> Live Debug-Panel</h4>
                    <p>Echtzeitübersicht aller aktiven EditorJS-Instanzen auf dieser Seite:</p>
                    <div id="debug-panel" style="border: 1px solid #ddd; padding: 10px; background: #f8f9fa; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;">
                        Lade Debug-Informationen...
                    </div>
                    <button onclick="updateDebugPanel()" class="btn btn-sm btn-info" style="margin-top: 5px;">Aktualisieren</button>
                    <button onclick="testAllEditors()" class="btn btn-sm btn-success">Alle Editoren testen</button>
                    <button onclick="EditorJSAutoInit.forceReinit()" class="btn btn-sm btn-warning">Neu initialisieren</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
pre {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 12px;
    font-size: 13px;
    margin: 10px 0;
}

code {
    background: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 12px;
}

.label-success {
    background-color: #5cb85c;
}

.label-warning {
    background-color: #f0ad4e;
}

.panel-heading .label {
    margin-left: 10px;
}
</style>

<script>
function updateDebugPanel() {
    const panel = document.getElementById('debug-panel');
    if (!panel) return;
    
    const instances = window.editorInstances || {};
    const autoInit = window.EditorJSAutoInit || {};
    
    let output = [];
    output.push(`=== EditorJS Debug-Panel (${new Date().toLocaleTimeString()}) ===`);
    output.push(`Globale Instanzen: ${Object.keys(instances).length}`);
    output.push(`AutoInit verfügbar: ${typeof autoInit === 'object' ? 'Ja' : 'Nein'}`);
    
    if (Object.keys(instances).length > 0) {
        output.push('\n--- Aktive Editor-Instanzen ---');
        Object.keys(instances).forEach(id => {
            const container = document.getElementById(id);
            const toolFilter = container?.dataset?.editorjsTools || 'Alle Tools';
            const initialized = container?.dataset?.editorjsInitialized || 'Unbekannt';
            output.push(`${id}: ${initialized} | Tools: ${toolFilter}`);
        });
    }
    
    // Alle EditorJS-Container auf der Seite finden
    const containers = document.querySelectorAll('[data-editorjs], .editorjs, textarea[data-editorjs-direct]');
    if (containers.length > 0) {
        output.push('\n--- Erkannte EditorJS-Container ---');
        containers.forEach((container, index) => {
            const id = container.id || `unnamed-${index}`;
            const type = container.tagName.toLowerCase();
            const tools = container.dataset.editorjsTools || 'Alle Tools';
            const initialized = container.dataset.editorjsInitialized || 'Nein';
            output.push(`${id} (${type}): Init=${initialized} | Tools=${tools}`);
        });
    }
    
    // Tool-Verfügbarkeit prüfen
    if (typeof EditorJSUtils !== 'undefined') {
        output.push('\n--- EditorJS Tools Status ---');
        output.push('EditorJSUtils: Verfügbar');
        
        // Verfügbare globale Tools prüfen
        const globalTools = ['EditorJS', 'Header', 'Paragraph', 'List', 'Quote', 'AlertBlock', 'TextImageBlock'];
        const availableTools = globalTools.filter(tool => typeof window[tool] !== 'undefined');
        output.push(`Globale Tools: ${availableTools.join(', ')}`);
    } else {
        output.push('\n--- EditorJS Tools Status ---');
        output.push('EditorJSUtils: NICHT VERFÜGBAR!');
    }
    
    panel.textContent = output.join('\n');
}

function testAllEditors() {
    const instances = window.editorInstances || {};
    
    if (Object.keys(instances).length === 0) {
        alert('Keine EditorJS-Instanzen gefunden!');
        return;
    }
    
    let results = [];
    
    Object.keys(instances).forEach(id => {
        const editor = instances[id];
        const container = document.getElementById(id);
        
        try {
            // Test: Editor-Objekt prüfen
            const hasAPI = typeof editor.saver === 'object';
            const hasBlocks = typeof editor.blocks === 'object';
            const toolFilter = container?.dataset?.editorjsTools || 'Keine';
            
            results.push(`✓ ${id}: API=${hasAPI ? 'OK' : 'FEHLER'}, Blocks=${hasBlocks ? 'OK' : 'FEHLER'}, Filter=${toolFilter}`);
            
            // Test: Speichern
            editor.saver.save().then(data => {
                console.log(`Editor ${id} Daten:`, data);
            }).catch(error => {
                console.error(`Editor ${id} Speicher-Fehler:`, error);
            });
            
        } catch (error) {
            results.push(`✗ ${id}: FEHLER - ${error.message}`);
        }
    });
    
    alert('Editor-Tests:\n\n' + results.join('\n') + '\n\nDetails in der Browser-Konsole.');
}

// Auto-Update alle 10 Sekunden
if (typeof window.debugPanelInterval === 'undefined') {
    updateDebugPanel(); // Initial
    window.debugPanelInterval = setInterval(updateDebugPanel, 10000);
}

// Bei Seitenwechsel aufräumen
document.addEventListener('pjax:start', function() {
    if (window.debugPanelInterval) {
        clearInterval(window.debugPanelInterval);
        window.debugPanelInterval = undefined;
    }
});
</script>
