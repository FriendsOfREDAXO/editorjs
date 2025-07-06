/**
 * EditorJS Debug Tool
 * Zeigt alle verfügbaren Tools und deren Status an
 */

// Funktion um alle registrierten Tools anzuzeigen
function debugEditorJSTools() {
    console.group('🔍 EditorJS Tools Debug');
    
    console.log('📦 Verfügbare globale Klassen:');
    const availableClasses = [
        'EditorJS', 'Header', 'Paragraph', 'List', 'Quote', 'Delimiter', 
        'CodeTool', 'InlineCode', 'Marker', 'LinkTool', 'REXLinkTool',
        'AlertBlock', 'TextImageBlock', 'ImageBlock', 'DownloadsBlock', 
        'VideoBlock', 'HeroBlock', 'REXMediaTool'
    ];
    
    availableClasses.forEach(className => {
        const isAvailable = window[className] !== undefined;
        console.log(`${isAvailable ? '✅' : '❌'} ${className}:`, window[className]);
    });
    
    console.log('\n🛠️ Tool-Konfiguration prüfen:');
    if (window.EditorJSUtils && window.EditorJSUtils.createEditor) {
        console.log('✅ EditorJSUtils.createEditor ist verfügbar');
    } else {
        console.log('❌ EditorJSUtils.createEditor ist nicht verfügbar');
    }
    
    console.groupEnd();
}

// Debug nach dem Laden der Seite
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(debugEditorJSTools, 1000);
});

// Global verfügbar machen
window.debugEditorJSTools = debugEditorJSTools;
