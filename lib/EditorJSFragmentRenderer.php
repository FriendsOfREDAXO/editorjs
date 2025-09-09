<?php

namespace FriendsOfRedaxo\EditorJs;

/**
 * EditorJS Fragment Renderer für REDAXO
 * 
 * Rendert EditorJS-Blöcke mit REDAXO Fragmenten für verschiedene Frameworks
 * (Bootstrap, UIKit3, etc.)
 */

class EditorJSFragmentRenderer
{
    /** @var string */
    private $framework;
    
    /** @var string */
    private $fragmentPath;
    
    /** @var array */
    private $config;

    /** @var array */
    private $supportedFrameworks = ['bootstrap', 'uikit'];

    public function __construct(string $framework = 'bootstrap', array $config = [])
    {
        $this->framework = in_array($framework, $this->supportedFrameworks) ? $framework : 'bootstrap';
        $this->config = array_merge($this->getDefaultConfig(), $config);
        
        // Fragment-Pfad bestimmen
        $addonPath = rex_addon::get('editorjs')->getPath();
        $this->fragmentPath = $addonPath . 'fragments/' . $this->framework . '/';
        
        // Fallback zu Standard-Renderer wenn Fragment-Ordner nicht existiert
        if (!is_dir($this->fragmentPath)) {
            $this->fragmentPath = null;
        }
    }

    /**
     * Standard-Konfiguration
     */
    private function getDefaultConfig(): array
    {
        return [
            'lightbox' => true,
            'lazyLoading' => true,
            'responsive' => true,
            'debug' => false
        ];
    }

    /**
     * Hauptmethode: Konvertiert EditorJS JSON zu HTML mit Fragmenten
     */
    public function render($data): string
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data) || !isset($data['blocks'])) {
            return '';
        }

        $html = '';
        foreach ($data['blocks'] as $block) {
            $html .= $this->renderBlock($block);
        }

        return $html;
    }

    /**
     * Rendert einen einzelnen Block
     */
    private function renderBlock(array $block): string
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];

        // Fragment-basiertes Rendering versuchen
        if ($this->fragmentPath) {
            $fragmentFile = $this->fragmentPath . $type . '.php';
            
            if (file_exists($fragmentFile)) {
                return $this->renderWithFragment($fragmentFile, $data);
            }
        }

        // Fallback zu Standard-Renderer
        $standardRenderer = new EditorJSRenderer();
        return $this->renderBlockFallback($block, $standardRenderer);
    }

    /**
     * Rendert mit REDAXO Fragment
     */
    private function renderWithFragment(string $fragmentFile, array $data): string
    {
        // Fragment-Kontext erstellen
        $fragment = new \stdClass();
        $fragment->data = $data;
        $fragment->config = $this->config;
        $fragment->framework = $this->framework;
        
        // Hilfsmethoden hinzufügen
        $fragment->getVideoType = function($filename) {
            return $this->getVideoType($filename);
        };
        
        $fragment->detectMediaType = function($filename) {
            return $this->detectMediaType($filename);
        };
        
        $fragment->formatFileSize = function($bytes) {
            return $this->formatFileSize($bytes);
        };

        // Fragment rendern
        ob_start();
        
        // Fragment-Variablen verfügbar machen
        $this->data = $data;
        $this->config = $this->config;
        $this->framework = $this->framework;
        
        try {
            include $fragmentFile;
        } catch (Exception $e) {
            if ($this->config['debug']) {
                return '<!-- Fragment Error: ' . htmlspecialchars($e->getMessage()) . ' -->';
            }
            // Fallback bei Fehlern
            $standardRenderer = new EditorJSRenderer();
            return $this->renderBlockFallback(['type' => basename($fragmentFile, '.php'), 'data' => $data], $standardRenderer);
        }
        
        return ob_get_clean();
    }

    /**
     * Fallback-Rendering mit Standard-Renderer
     */
    private function renderBlockFallback(array $block, EditorJSRenderer $renderer): string
    {
        $type = $block['type'] ?? '';
        $data = $block['data'] ?? [];

        // Direkt die entsprechende Render-Methode aufrufen
        switch ($type) {
            case 'header':
                return $renderer->renderHeader($data);
            case 'paragraph':
                return $renderer->renderParagraph($data);
            case 'list':
                return $renderer->renderList($data);
            case 'quote':
                return $renderer->renderQuote($data);
            case 'delimiter':
                return $renderer->renderDelimiter($data);
            case 'code':
                return $renderer->renderCode($data);
            case 'alert':
            case 'AlertBlock':
                return $renderer->renderAlert($data);
            case 'textimage':
            case 'TextImageBlock':
                return $renderer->renderTextImage($data);
            case 'downloads':
                return $renderer->renderDownloads($data);
            case 'gallery':
            case 'ImageGalleryBlock':
                return $renderer->renderGallery($data);
            case 'card':
            case 'CardBlock':
                return $renderer->renderCard($data);
            default:
                return '<!-- Unbekannter Block-Typ: ' . htmlspecialchars($type) . ' -->';
        }
    }

    /**
     * Bestimmt den MIME-Type für Video-Dateien
     */
    public function getVideoType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'ogv' => 'video/ogg',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'm4v' => 'video/x-m4v'
        ];
        
        return $mimeTypes[$extension] ?? 'video/mp4';
    }

    /**
     * Bestimmt den Medientyp basierend auf der Dateiendung
     */
    public function detectMediaType(string $filename): string
    {
        if (!$filename) return 'image';
        
        $videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv', 'flv', 'm4v'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($extension, $videoExtensions) ? 'video' : 'image';
    }

    /**
     * Formatiert eine Dateigröße
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Setzt das Framework
     */
    public function setFramework(string $framework): self
    {
        if (in_array($framework, $this->supportedFrameworks)) {
            $this->framework = $framework;
            
            $addonPath = rex_addon::get('editorjs')->getPath();
            $this->fragmentPath = $addonPath . 'fragments/' . $this->framework . '/';
            
            if (!is_dir($this->fragmentPath)) {
                $this->fragmentPath = null;
            }
        }
        
        return $this;
    }

    /**
     * Gibt das aktuelle Framework zurück
     */
    public function getFramework(): string
    {
        return $this->framework;
    }

    /**
     * Gibt die unterstützten Frameworks zurück
     */
    public function getSupportedFrameworks(): array
    {
        return $this->supportedFrameworks;
    }

    /**
     * Setzt Konfiguration
     */
    public function setConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Gibt die aktuelle Konfiguration zurück
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Statische Hilfsmethode für einfache Verwendung
     */
    public static function renderJSON(string $json, string $framework = 'bootstrap', array $config = []): string
    {
        $renderer = new self($framework, $config);
        return $renderer->render($json);
    }

    /**
     * Framework-spezifische CSS-Dateien einbinden
     */
    public static function addFrameworkCSS(string $framework = 'bootstrap'): void
    {
        if (!class_exists('\rex_addon')) {
            return;
        }
        
        $addon = \rex_addon::get('editorjs');
        
        // Framework-spezifische CSS-Dateien
        $cssFiles = [
            'bootstrap' => [
                $addon->getAssetsUrl('css/editorjs-bootstrap.css')
            ],
            'uikit' => [
                $addon->getAssetsUrl('css/editorjs-uikit.css')
            ]
        ];
        
        if (isset($cssFiles[$framework]) && class_exists('\rex_view')) {
            foreach ($cssFiles[$framework] as $cssFile) {
                if (file_exists(rex_path::addon('editorjs', 'assets/css/' . basename($cssFile)))) {
                    \rex_view::addCssFile($cssFile);
                }
            }
        }
    }

    /**
     * Framework-spezifische JavaScript-Dateien einbinden
     */
    public static function addFrameworkJS(string $framework = 'bootstrap'): void
    {
        if (!class_exists('\rex_addon')) {
            return;
        }
        
        $addon = \rex_addon::get('editorjs');
        
        // Framework-spezifische JS-Dateien
        $jsFiles = [
            'bootstrap' => [
                $addon->getAssetsUrl('js/editorjs-bootstrap.js')
            ],
            'uikit' => [
                $addon->getAssetsUrl('js/editorjs-uikit.js')
            ]
        ];
        
        if (isset($jsFiles[$framework]) && class_exists('\rex_view')) {
            foreach ($jsFiles[$framework] as $jsFile) {
                if (file_exists(rex_path::addon('editorjs', 'assets/js/' . basename($jsFile)))) {
                    \rex_view::addJsFile($jsFile);
                }
            }
        }
    }
}