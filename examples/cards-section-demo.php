<?php
/**
 * Cards Section Demo - Demonstriert die neue Cards Section Funktionalität
 * Zeigt wie man mehrere Cards in einem Container verwalten und in einem Grid anordnen kann
 */

echo '<div class="container-fluid" style="max-width: 1200px; margin: 0 auto; padding: 20px;">';

// Header
echo '<div class="alert alert-info">';
echo '<h2><i class="fa fa-th-large"></i> Cards Section Demo</h2>';
echo '<p>Diese Demo zeigt die neue <strong>Cards Section</strong> Funktionalität - ein Container für mehrere Cards mit Grid-Layout.</p>';
echo '</div>';

// Cards Section Demo
echo '<div class="row">';
echo '<div class="col-md-8">';
echo '<h3>🆕 Cards Section Editor</h3>';
echo '<p>Verwenden Sie die <strong>Cards Section</strong> um mehrere Cards zu verwalten:</p>';
echo '<div id="editor-cards-section" data-editorjs-tools="paragraph,cards" style="border: 1px solid #ddd; min-height: 400px; padding: 15px; background: #f8f9fa;"></div>';
echo '<textarea name="REX_INPUT_VALUE[1]" data-editorjs-data style="display: none;"></textarea>';
echo '</div>';

echo '<div class="col-md-4">';
echo '<div class="alert alert-success">';
echo '<h5><i class="fa fa-info-circle"></i> Funktionen testen</h5>';
echo '<ul class="list-unstyled">';
echo '<li>✅ <strong>Cards hinzufügen:</strong> Button "Card hinzufügen"</li>';
echo '<li>✅ <strong>Media auswählen:</strong> Bilder & Videos aus Medienpool</li>';
echo '<li>✅ <strong>Grid Layout:</strong> Einstellungen-Icon → 1-4 Spalten</li>';
echo '<li>✅ <strong>ALT-Text:</strong> Automatische ALT-Text Warnung</li>';
echo '<li>✅ <strong>Video-Wiedergabe:</strong> Videos direkt im Editor</li>';
echo '<li>✅ <strong>Sortierung:</strong> Drag & Drop (coming soon)</li>';
echo '</ul>';
echo '</div>';

echo '<div class="alert alert-warning">';
echo '<h6>🎯 Layout-Optionen</h6>';
echo '<ul class="list-unstyled small">';
echo '<li>• <strong>Grid:</strong> 1-4 Spalten Raster</li>';
echo '<li>• <strong>Liste:</strong> Vertikale Anordnung</li>';
echo '<li>• <strong>Kompakt:</strong> Minimales Design</li>';
echo '</ul>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<hr style="margin: 30px 0;">';

// Vergleich: Einzelne Card vs Cards Section
echo '<div class="row">';
echo '<div class="col-md-6">';
echo '<h4>📋 Einzelne Card</h4>';
echo '<p>Für einzelne Card-Elemente:</p>';
echo '<div id="editor-single-card" data-editorjs-tools="paragraph,card" style="border: 1px solid #ddd; min-height: 200px; padding: 15px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[2]" data-editorjs-data style="display: none;"></textarea>';
echo '</div>';

echo '<div class="col-md-6">';
echo '<h4>🔄 Kombiniert mit anderen Tools</h4>';
echo '<p>Cards Section mit Text-Elementen:</p>';
echo '<div id="editor-mixed" data-editorjs-tools="paragraph,header,cards,textimage" style="border: 1px solid #ddd; min-height: 200px; padding: 15px;"></div>';
echo '<textarea name="REX_INPUT_VALUE[3]" data-editorjs-data style="display: none;"></textarea>';
echo '</div>';
echo '</div>';

echo '</div>'; // .container-fluid

?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cards Section Demo loaded');
    
    // Erweiterte Event-Listener für Demo-Funktionalität
    document.addEventListener('editorjs:ready', function(event) {
        const container = event.detail.container;
        const editor = event.detail.editor;
        
        console.log('Editor ready:', container.id, 'Available tools:', Object.keys(editor.configuration.tools));
        
        // Spezielle Hinweise für Cards Section Demo
        if (container.id === 'editor-cards-section') {
            console.log('🆕 Cards Section Demo aktiv');
            
            // Demo-Daten für Cards Section laden falls leer
            setTimeout(function() {
                editor.save().then(function(data) {
                    if (!data.blocks || data.blocks.length === 0) {
                        // Demo-Inhalt einfügen
                        const demoData = {
                            time: Date.now(),
                            blocks: [
                                {
                                    type: "paragraph",
                                    data: {
                                        text: "Willkommen zur Cards Section Demo! Fügen Sie eine Cards Section hinzu um mehrere Cards zu verwalten:"
                                    }
                                }
                            ]
                        };
                        
                        editor.render(demoData);
                    }
                });
            }, 1000);
            
            // Hinweis-Banner für neue Features
            showFeatureHint(container, '🆕 Cards Section verfügbar! Klicken Sie auf + um eine Cards Section hinzuzufügen.');
        }
        
        // Video-Playback-Fix für Cards
        setTimeout(function() {
            fixVideoPlayback(container);
        }, 2000);
    });
    
    // Funktion für Feature-Hinweise
    function showFeatureHint(container, message) {
        if (!container.querySelector('.feature-hint')) {
            const hint = document.createElement('div');
            hint.className = 'feature-hint alert alert-info';
            hint.style.cssText = 'position: absolute; top: -40px; left: 0; right: 0; z-index: 1000; padding: 8px 12px; font-size: 13px; border-radius: 4px 4px 0 0; margin: 0;';
            hint.innerHTML = '<i class="fa fa-info-circle"></i> ' + message;
            
            container.style.position = 'relative';
            container.appendChild(hint);
            
            // Auto-remove nach 5 Sekunden
            setTimeout(() => {
                if (hint.parentNode) {
                    hint.remove();
                }
            }, 5000);
        }
    }
    
    // Fix für Video-Playback in Cards
    function fixVideoPlayback(container) {
        const videos = container.querySelectorAll('video');
        videos.forEach(video => {
            // Stelle sicher dass Videos abspielbar sind
            video.removeAttribute('autoplay');
            video.setAttribute('preload', 'metadata');
            video.setAttribute('controls', 'true');
            
            // Event-Listener für Playback-Probleme
            video.addEventListener('error', function(e) {
                console.warn('Video playback error:', e, video.src);
            });
            
            video.addEventListener('loadedmetadata', function() {
                console.log('Video loaded successfully:', video.src);
            });
        });
        
        // Beobachte neue Videos die dynamisch hinzugefügt werden
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        const newVideos = node.querySelectorAll ? node.querySelectorAll('video') : [];
                        newVideos.forEach(video => {
                            video.removeAttribute('autoplay');
                            video.setAttribute('preload', 'metadata');
                            video.setAttribute('controls', 'true');
                        });
                    }
                });
            });
        });
        
        observer.observe(container, {
            childList: true,
            subtree: true
        });
    }
});
</script>

<style>
/* Demo-spezifische Styles */
.feature-hint {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Card Section Video-Fix */
.cdx-cards-section video {
    width: 100%;
    height: auto;
    max-height: 200px;
    border-radius: 4px;
}

.codex-editor {
    border-radius: 6px;
}

.codex-editor__redactor {
    padding: 20px !important;
}

/* Verbesserte Sichtbarkeit für Card-Tools im Editor */
.cdx-cards-section__container {
    min-height: 100px;
    border: 2px dashed #e9ecef;
    border-radius: 6px;
    padding: 15px;
    background: rgba(0,123,255,0.02);
}

.cdx-cards-section__item {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cdx-cards-section[data-layout="grid"] .cdx-cards-section__container {
    border-style: solid;
    background: rgba(40,167,69,0.02);
}
</style>