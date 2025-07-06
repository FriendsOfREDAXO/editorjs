<?php

/** @var rex_addon $this */

// Basic assets laden
if (rex::isBackend() && is_object(rex::getUser())) {
    // EditorJS Bundle (enthält Core + alle Tools)
    rex_view::addJsFile($this->getAssetsUrl('js/editorjs.bundle.js'));
    
    // Bundle-CSS (automatisch generiert)
    rex_view::addCssFile($this->getAssetsUrl('css/editorjs.bundle.css'));
    
    // Auto-Initialisierung für Module
    rex_view::addJsFile($this->getAssetsUrl('js/editorjs-auto-init.js'));
    
    // Eigene Styles
    rex_view::addCssFile($this->getAssetsUrl('css/editorjs-basic.css'));
    
    // Medienpool-Integration (nach Redactor-Vorbild)
    if (rex_addon::get('mediapool')->isAvailable()) {
        rex_view::setJsProperty('editorjs_rex_media_getImageTypes', rex_media::getImageTypes());
    }
    
    // Bild-URL-Pfad konfigurieren
    $imageUrlPath = rex_url::media();
    if (rex_addon::get('media_manager')->isAvailable()) {
        $imageUrlPath = 'index.php?rex_media_type=rex_mediapool_detail&rex_media_file=';
        
        if (rex_addon::get('yrewrite')->isAvailable()) {
            $imageUrlPath = '/media/rex_mediapool_detail/';
        }
    }
    rex_view::setJsProperty('editorjs_imageUrlPath', $imageUrlPath);
}

// EditorJS Renderer-Klasse automatisch laden
spl_autoload_register(function ($class) {
    if ($class === 'FriendsOfRedaxo\\EditorJs\\EditorJsRenderer') {
        require_once __DIR__ . '/lib/EditorJSRenderer.php';
    }
});

// Extension Point für automatisches CSS im Frontend
rex_extension::register('OUTPUT_FILTER', function (rex_extension_point $ep) {
    $content = $ep->getSubject();
    
    // Prüfen ob EditorJS Renderer verwendet wird
    if (strpos($content, 'editorjs-') !== false) {
        $addon = rex_addon::get('editorjs');
        $cssUrl = $addon->getAssetsUrl('css/editorjs-frontend.css');
        
        // CSS vor </head> einfügen falls noch nicht vorhanden
        if (strpos($content, 'editorjs-frontend.css') === false) {
            $cssLink = '<link rel="stylesheet" href="' . $cssUrl . '">';
            $content = str_replace('</head>', $cssLink . "\n</head>", $content);
        }
    }
    
    return $content;
});
