<?php

namespace FriendsOfRedaxo\EditorJs;

/**
 * EditorJS Renderer für REDAXO
 * 
 * Konvertiert EditorJS JSON-Daten zu HTML für die Frontend-Ausgabe.
 * Unterstützt Standard-Blöcke und benutzerdefinierte Blöcke (Alert, TextImage).
 */

class EditorJsRenderer
{
    /** @var array */
    private $config;

    public function __construct()
    {
        $this->config = [
            'tools' => [
                // Standard EditorJS Tools
                'header' => [$this, 'renderHeader'],
                'paragraph' => [$this, 'renderParagraph'],
                'list' => [$this, 'renderList'],
                'quote' => [$this, 'renderQuote'],
                'delimiter' => [$this, 'renderDelimiter'],
                'code' => [$this, 'renderCode'],
                
                // Unsere benutzerdefinierten Blöcke
                'alert' => [$this, 'renderAlert'],
                'textimage' => [$this, 'renderTextImage'],
                'downloads' => [$this, 'renderDownloads'],
                'gallery' => [$this, 'renderGallery'],
                
                // Aliase für verschiedene Schreibweisen
                'AlertBlock' => [$this, 'renderAlert'],
                'TextImageBlock' => [$this, 'renderTextImage'],
                'ImageBlock' => [$this, 'renderImage'],
                'VideoBlock' => [$this, 'renderVideo'],
                'ImageGalleryBlock' => [$this, 'renderGallery']
            ]
        ];
    }

    /**
     * Hauptmethode: Konvertiert EditorJS JSON zu HTML
     * 
     * @param string|array $data EditorJS JSON-String oder Array
     * @return string HTML-Output
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

        if (isset($this->config['tools'][$type]) && is_callable($this->config['tools'][$type])) {
            return call_user_func($this->config['tools'][$type], $data);
        }

        // Fallback für unbekannte Blöcke
        return '<!-- Unbekannter Block-Typ: ' . htmlspecialchars($type) . ' -->';
    }

    /**
     * Rendert Header-Block
     */
    public function renderHeader(array $data): string
    {
        $level = (int) ($data['level'] ?? 2);
        $text = $data['text'] ?? '';
        
        if ($level < 1 || $level > 6) {
            $level = 2;
        }

        return "<h{$level}>" . htmlspecialchars($text) . "</h{$level}>\n";
    }

    /**
     * Rendert Paragraph-Block
     */
    public function renderParagraph(array $data): string
    {
        $text = $data['text'] ?? '';
        
        // Einfache HTML-Tags erlauben (strong, em, u, s, a)
        $allowedTags = '<strong><em><u><s><a>';
        $cleanText = strip_tags($text, $allowedTags);
        
        return "<p>{$cleanText}</p>\n";
    }

    /**
     * Rendert List-Block
     */
    public function renderList(array $data): string
    {
        $style = $data['style'] ?? 'unordered';
        $items = $data['items'] ?? [];
        
        $tag = $style === 'ordered' ? 'ol' : 'ul';
        $html = "<{$tag}>\n";
        
        foreach ($items as $item) {
            // EditorJS v2.31+ verwendet Objekt-Format für Listen-Items
            if (is_array($item) && isset($item['content'])) {
                $content = $item['content'];
            } else {
                // Fallback für alte String-basierte Listen
                $content = is_string($item) ? $item : '';
            }
            
            $cleanItem = strip_tags($content, '<strong><em><u><s><a>');
            $html .= "<li>{$cleanItem}</li>\n";
        }
        
        $html .= "</{$tag}>\n";
        
        return $html;
    }

    /**
     * Rendert Quote-Block
     */
    public function renderQuote(array $data): string
    {
        $text = $data['text'] ?? '';
        $caption = $data['caption'] ?? '';
        
        $cleanText = strip_tags($text, '<strong><em><u><s><a>');
        
        $html = "<blockquote>\n";
        $html .= "<p>{$cleanText}</p>\n";
        
        if ($caption) {
            $cleanCaption = strip_tags($caption, '<strong><em><u><s><a>');
            $html .= "<cite>{$cleanCaption}</cite>\n";
        }
        
        $html .= "</blockquote>\n";
        
        return $html;
    }

    /**
     * Rendert Delimiter-Block
     */
    public function renderDelimiter(array $data): string
    {
        return "<hr class=\"editorjs-delimiter\">\n";
    }

    /**
     * Rendert Code-Block
     */
    public function renderCode(array $data): string
    {
        $code = $data['code'] ?? '';
        
        return "<pre><code>" . htmlspecialchars($code) . "</code></pre>\n";
    }

    /**
     * Rendert unseren benutzerdefinierten Alert-Block
     */
    public function renderAlert(array $data): string
    {
        $type = $data['type'] ?? 'info';
        $title = $data['title'] ?? '';
        $message = $data['message'] ?? '';
        
        $html = "<div class=\"cdx-alert\" data-type=\"{$type}\">\n";
        
        if ($title) {
            $html .= "<div class=\"cdx-alert__title\">" . htmlspecialchars($title) . "</div>\n";
        }
        
        if ($message) {
            $html .= "<div class=\"cdx-alert__message\">" . htmlspecialchars($message) . "</div>\n";
        }
        
        $html .= "</div>\n";
        
        return $html;
    }

    /**
     * Rendert unseren benutzerdefinierten TextImage-Block
     */
    public function renderTextImage(array $data): string
    {
        $text = $data['text'] ?? '';
        $imageFile = $data['imageFile'] ?? '';
        $imageUrl = $data['imageUrl'] ?? '';
        $caption = $data['caption'] ?? '';
        $layout = $data['layout'] ?? 'left';
        
        // Bild-URL erstellen
        if ($imageFile && !$imageUrl) {
            if (function_exists('rex_url')) {
                $imageUrl = \rex_url::media($imageFile);
            } else {
                // Fallback für Demo: Placehold.co verwenden
                $imageUrl = "https://placehold.co/300x200/007bff/ffffff?text=" . urlencode($imageFile);
            }
        }
        
        // Fallback für Demo ohne Bild - spezifische Farben je Layout
        if (!$imageUrl) {
            $layouts = [
                'left' => 'https://placehold.co/300x200/007bff/ffffff?text=Bild+Links',
                'right' => 'https://placehold.co/300x200/28a745/ffffff?text=Bild+Rechts', 
                'top' => 'https://placehold.co/600x200/dc3545/ffffff?text=Bild+Oben'
            ];
            $imageUrl = $layouts[$layout] ?? $layouts['left'];
        }
        
        $html = "<div class=\"cdx-textimage\" data-layout=\"{$layout}\">\n";
        $html .= "<div class=\"cdx-textimage__container layout-{$layout} layout-{$layout}\">\n";
        
        // Bild-Wrapper (immer anzeigen)
        $html .= "<div class=\"cdx-textimage__image-wrapper\">\n";
        $html .= "<img src=\"{$imageUrl}\" alt=\"" . htmlspecialchars($imageFile ?: 'Demo Bild') . "\" class=\"cdx-textimage__image\">\n";
        
        if ($caption) {
            $html .= "<div class=\"cdx-textimage__caption\">" . htmlspecialchars($caption) . "</div>\n";
        }
        
        $html .= "</div>\n";
        
        // Text-Wrapper
        if ($text) {
            $html .= "<div class=\"cdx-textimage__text-wrapper\">\n";
            $html .= "<div class=\"cdx-textimage__text\">\n";
            
            // Text mit erlaubten HTML-Tags
            $allowedTags = '<p><h1><h2><h3><h4><h5><h6><strong><em><u><s><a><ul><ol><li><br>';
            $cleanText = strip_tags($text, $allowedTags);
            
            $html .= $cleanText;
            $html .= "</div>\n";
            $html .= "</div>\n";
        }
        
        $html .= "</div>\n";
        $html .= "</div>\n";
        
        return $html;
    }

    /**
     * Rendert unseren benutzerdefinierten Gallery-Block
     */
    public function renderGallery(array $data): string
    {
        $title = $data['title'] ?? 'Bildergalerie';
        $items = $data['items'] ?? [];
        $showTitle = $data['showTitle'] ?? true;
        $layout = $data['layout'] ?? 'grid';
        $imageSize = $data['imageSize'] ?? 'medium';
        $showCaptions = $data['showCaptions'] ?? true;
        $aspectRatio = $data['aspectRatio'] ?? '';
        $spacing = $data['spacing'] ?? 'normal';
        
        if (empty($items)) {
            return '';
        }
        
        $classes = ['cdx-gallery'];
        $dataAttrs = [
            'data-layout' => $layout,
            'data-image-size' => $imageSize,
            'data-spacing' => $spacing
        ];
        
        if ($aspectRatio) {
            $dataAttrs['data-aspect-ratio'] = $aspectRatio;
        }
        
        if ($showCaptions) {
            $classes[] = 'cdx-gallery--show-captions';
        }
        
        $classStr = implode(' ', $classes);
        $dataStr = implode(' ', array_map(function($k, $v) {
            return $k . '="' . htmlspecialchars($v) . '"';
        }, array_keys($dataAttrs), $dataAttrs));
        
        $html = "<div class=\"{$classStr}\" {$dataStr}>\n";
        
        // Titel anzeigen falls aktiviert
        if ($showTitle && !empty($title)) {
            $html .= "<h3 class=\"cdx-gallery__title\">" . htmlspecialchars($title) . "</h3>\n";
        }
        
        $html .= "<div class=\"cdx-gallery__container\">\n";
        
        foreach ($items as $item) {
            $imageFile = $item['imageFile'] ?? '';
            $imageUrl = $item['imageUrl'] ?? '';
            $itemTitle = $item['title'] ?? '';
            $description = $item['description'] ?? '';
            $alt = $item['alt'] ?? $itemTitle ?: $imageFile;
            
            if (empty($imageFile) && empty($imageUrl)) {
                continue;
            }
            
            // Bild-URL für REDAXO generieren
            if ($imageFile && !$imageUrl) {
                $imageUrl = \rex_url::media($imageFile);
            }
            
            $html .= "<div class=\"cdx-gallery__item-frontend\">\n";
            
            // Bild Container
            $html .= "<div class=\"cdx-gallery__image-container-frontend\">\n";
            $html .= "<img src=\"{$imageUrl}\" alt=\"" . htmlspecialchars($alt) . "\" class=\"cdx-gallery__image-frontend\" loading=\"lazy\" />\n";
            
            // Caption overlay falls aktiviert
            if ($showCaptions && (!empty($itemTitle) || !empty($description))) {
                $html .= "<div class=\"cdx-gallery__caption-frontend\">\n";
                if (!empty($itemTitle)) {
                    $html .= "<div class=\"cdx-gallery__caption-title\">" . htmlspecialchars($itemTitle) . "</div>\n";
                }
                if (!empty($description)) {
                    $html .= "<div class=\"cdx-gallery__caption-description\">" . htmlspecialchars($description) . "</div>\n";
                }
                $html .= "</div>\n";
            }
            
            $html .= "</div>\n";
            $html .= "</div>\n";
        }
        
        $html .= "</div>\n";
        $html .= "</div>\n";
        
        return $html;
    }

    /**
     * Rendert unseren benutzerdefinierten Downloads-Block
     */
    public function renderDownloads(array $data): string
    {
        $title = $data['title'] ?? 'Downloads';
        $items = $data['items'] ?? [];
        $showTitle = $data['showTitle'] ?? true;
        $layout = $data['layout'] ?? 'list';
        
        if (empty($items)) {
            return '';
        }
        
        $html = "<div class=\"cdx-downloads\" data-layout=\"{$layout}\">\n";
        
        // Titel anzeigen falls aktiviert
        if ($showTitle && !empty($title)) {
            $html .= "<h3 class=\"cdx-downloads__title\">" . htmlspecialchars($title) . "</h3>\n";
        }
        
        $html .= "<div class=\"cdx-downloads__container\">\n";
        
        foreach ($items as $item) {
            $file = $item['file'] ?? '';
            $itemTitle = $item['title'] ?? '';
            $description = $item['description'] ?? '';
            
            if (empty($file)) {
                continue;
            }
            
            // Media-URL für REDAXO generieren
            $fileUrl = \rex_url::media($file);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            $html .= "<div class=\"cdx-downloads__item\">\n";
            $html .= "<a href=\"{$fileUrl}\" class=\"cdx-downloads__link\" target=\"_blank\" rel=\"noopener\">\n";
            
            // Icon Container
            $html .= "<div class=\"cdx-downloads__icon\">\n";
            
            // Prüfen ob es ein Bild ist für Thumbnail
            if ($this->isImageFile($extension)) {
                $html .= "<img src=\"{$fileUrl}\" alt=\"" . htmlspecialchars($itemTitle ?: $file) . "\" class=\"cdx-downloads__thumb\" />\n";
            } else {
                $html .= $this->getFileIcon($extension) . "\n";
            }
            
            $html .= "</div>\n";
            
            // File Info
            $html .= "<div class=\"cdx-downloads__info\">\n";
            
            if (!empty($itemTitle)) {
                $html .= "<h4 class=\"cdx-downloads__file-title\">" . htmlspecialchars($itemTitle) . "</h4>\n";
            }
            
            $html .= "<div class=\"cdx-downloads__file-name\">" . htmlspecialchars($file) . "</div>\n";
            
            if (!empty($description)) {
                $html .= "<div class=\"cdx-downloads__description\">" . htmlspecialchars($description) . "</div>\n";
            }
            
            // Dateigröße falls verfügbar
            $filePath = \rex_path::media($file);
            if (file_exists($filePath)) {
                $fileSize = $this->formatFileSize(filesize($filePath));
                $html .= "<div class=\"cdx-downloads__file-size\">{$fileSize}</div>\n";
            }
            
            $html .= "</div>\n";
            $html .= "</a>\n";
            $html .= "</div>\n";
        }
        
        $html .= "</div>\n";
        $html .= "</div>\n";
        
        return $html;
    }

    /**
     * Rendert unseren benutzerdefinierten Image-Block
     */
    public function renderImage(array $data): string
    {
        $imageFile = $data['imageFile'] ?? '';
        $imageUrl = $data['imageUrl'] ?? '';
        $imageAlt = $data['imageAlt'] ?? '';
        $caption = $data['caption'] ?? '';
        $stretched = $data['stretched'] ?? false;
        $withBorder = $data['withBorder'] ?? false;
        $withBackground = $data['withBackground'] ?? false;
        $aspectRatio = $data['aspectRatio'] ?? '';
        $cropMode = $data['cropMode'] ?? 'contain';
        
        // Bild-URL erstellen
        if ($imageFile && !$imageUrl) {
            if (function_exists('rex_url')) {
                $imageUrl = \rex_url::media($imageFile);
            } else {
                // Fallback für Demo: Placehold.co verwenden
                $imageUrl = "https://placehold.co/600x400/007bff/ffffff?text=" . urlencode($imageFile);
            }
        }
        
        // Fallback für Demo ohne Bild
        if (!$imageUrl) {
            $imageUrl = 'https://placehold.co/600x400/007bff/ffffff?text=Bild+Block';
        }
        
        // CSS-Klassen zusammenstellen
        $classes = ['cdx-image'];
        
        if ($stretched) {
            $classes[] = 'cdx-image--stretched';
        }
        
        if ($withBorder) {
            $classes[] = 'cdx-image--with-border';
        }
        
        if ($withBackground) {
            $classes[] = 'cdx-image--with-background';
        }
        
        if ($aspectRatio) {
            $classes[] = 'cdx-image--aspect-' . str_replace(':', '-', $aspectRatio);
        }
        
        if ($cropMode) {
            $classes[] = 'cdx-image--crop-' . $cropMode;
        }
        
        $classString = implode(' ', $classes);
        
        $html = "<div class=\"{$classString}\">\n";
        $html .= "<div class=\"cdx-image__container\">\n";
        
        // Bild
        $html .= "<img src=\"{$imageUrl}\" alt=\"" . htmlspecialchars($imageAlt ?: $imageFile ?: 'Bild') . "\" class=\"cdx-image__image\">\n";
        
        // Caption falls vorhanden
        if ($caption) {
            $html .= "<div class=\"cdx-image__caption\">" . htmlspecialchars($caption) . "</div>\n";
        }
        
        $html .= "</div>\n";
        $html .= "</div>\n";
        
        return $html;
    }

    /**
     * Rendert unseren benutzerdefinierten Video-Block
     */
    public function renderVideo(array $data): string
    {
        $videoFile = $data['videoFile'] ?? '';
        $videoUrl = $data['videoUrl'] ?? '';
        $poster = $data['poster'] ?? '';
        $posterUrl = $data['posterUrl'] ?? '';
        $caption = $data['caption'] ?? '';
        $stretched = $data['stretched'] ?? false;
        $withBorder = $data['withBorder'] ?? false;
        $withBackground = $data['withBackground'] ?? false;
        $aspectRatio = $data['aspectRatio'] ?? '';
        $autoplay = $data['autoplay'] ?? false;
        $muted = $data['muted'] ?? false;
        $loop = $data['loop'] ?? false;
        $controls = $data['controls'] ?? true;
        
        // Video-URL erstellen
        if ($videoFile && !$videoUrl) {
            if (function_exists('rex_url')) {
                $videoUrl = \rex_url::media($videoFile);
            } else {
                // Fallback für Demo: Placehold.co verwenden
                $videoUrl = "/media/" . $videoFile;
            }
        }
        
        // Poster-URL erstellen falls vorhanden
        if ($poster && !$posterUrl) {
            if (function_exists('rex_url')) {
                $posterUrl = \rex_url::media($poster);
            } else {
                // Fallback für Demo
                $posterUrl = "/media/" . $poster;
            }
        }
        
        // Fallback für Demo ohne Video
        if (!$videoUrl) {
            return "<div class=\"cdx-video cdx-video--placeholder\">Video-Block: Keine Video-Datei angegeben</div>\n";
        }
        
        // CSS-Klassen zusammenstellen
        $classes = ['cdx-video'];
        
        if ($stretched) {
            $classes[] = 'cdx-video--stretched';
        }
        
        if ($withBorder) {
            $classes[] = 'cdx-video--with-border';
        }
        
        if ($withBackground) {
            $classes[] = 'cdx-video--with-background';
        }
        
        if ($aspectRatio) {
            $classes[] = 'cdx-video--aspect-' . str_replace(':', '-', $aspectRatio);
        }
        
        $classString = implode(' ', $classes);
        
        // Video-Attribute zusammenstellen
        $videoAttributes = ['class="cdx-video__video"'];
        
        if ($controls) {
            $videoAttributes[] = 'controls';
        }
        
        if ($autoplay) {
            $videoAttributes[] = 'autoplay';
        }
        
        if ($muted) {
            $videoAttributes[] = 'muted';
        }
        
        if ($loop) {
            $videoAttributes[] = 'loop';
        }
        
        if ($posterUrl) {
            $videoAttributes[] = 'poster="' . htmlspecialchars($posterUrl) . '"';
        }
        
        $videoAttributeString = implode(' ', $videoAttributes);
        
        // Video-Typ bestimmen
        $videoType = $this->getVideoType($videoFile);
        
        $html = "<div class=\"{$classString}\">\n";
        $html .= "<div class=\"cdx-video__container\">\n";
        
        // Video
        $html .= "<video {$videoAttributeString}>\n";
        $html .= "<source src=\"{$videoUrl}\" type=\"{$videoType}\">\n";
        $html .= "Ihr Browser unterstützt das Video-Element nicht.\n";
        $html .= "</video>\n";
        
        // Caption falls vorhanden
        if ($caption) {
            $html .= "<div class=\"cdx-video__caption\">" . htmlspecialchars($caption) . "</div>\n";
        }
        
        $html .= "</div>\n";
        $html .= "</div>\n";
        
        return $html;
    }
    
    /**
     * Bestimmt den MIME-Type für Video-Dateien
     */
    private function getVideoType(string $filename): string
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
     * Prüft ob eine Datei ein Bild ist
     */
    private function isImageFile(string $extension): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
        return in_array($extension, $imageExtensions);
    }
    
    /**
     * Gibt das passende Icon für einen Dateityp zurück
     */
    private function getFileIcon(string $extension): string
    {
        $iconMap = [
            // PDF
            'pdf' => '<i class="fa-solid fa-file-pdf" style="color: #dc3545;"></i>',
            
            // Documents
            'doc' => '<i class="fa-solid fa-file-word" style="color: #2c5aa0;"></i>',
            'docx' => '<i class="fa-solid fa-file-word" style="color: #2c5aa0;"></i>',
            'xls' => '<i class="fa-solid fa-file-excel" style="color: #1d6f42;"></i>',
            'xlsx' => '<i class="fa-solid fa-file-excel" style="color: #1d6f42;"></i>',
            'ppt' => '<i class="fa-solid fa-file-powerpoint" style="color: #d04423;"></i>',
            'pptx' => '<i class="fa-solid fa-file-powerpoint" style="color: #d04423;"></i>',
            
            // Archive
            'zip' => '<i class="fa-solid fa-file-zipper" style="color: #6c757d;"></i>',
            'rar' => '<i class="fa-solid fa-file-zipper" style="color: #6c757d;"></i>',
            '7z' => '<i class="fa-solid fa-file-zipper" style="color: #6c757d;"></i>',
            
            // Text
            'txt' => '<i class="fa-solid fa-file-lines" style="color: #6c757d;"></i>',
            'rtf' => '<i class="fa-solid fa-file-lines" style="color: #6c757d;"></i>',
            
            // Audio/Video
            'mp3' => '<i class="fa-solid fa-file-audio" style="color: #ff6b35;"></i>',
            'wav' => '<i class="fa-solid fa-file-audio" style="color: #ff6b35;"></i>',
            'mp4' => '<i class="fa-solid fa-file-video" style="color: #ff6b35;"></i>',
            'avi' => '<i class="fa-solid fa-file-video" style="color: #ff6b35;"></i>'
        ];
        
        return $iconMap[$extension] ?? '<i class="fa-solid fa-file" style="color: #6c757d;"></i>';
    }
    
    /**
     * Formatiert eine Dateigröße
     */
    private function formatFileSize(int $bytes): string
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
     * Statische Hilfsmethode für einfache Verwendung
     */
    public static function renderJSON(string $json): string
    {
        $renderer = new self();
        return $renderer->render($json);
    }

    /**
     * CSS für Frontend einbinden
     */
    public static function addFrontendCSS(): void
    {
        if (class_exists('\rex_addon')) {
            $addon = \rex_addon::get('editorjs');
            $cssFile = $addon->getAssetsUrl('css/editorjs-frontend.css');
            
            if (class_exists('\rex_view')) {
                \rex_view::addCssFile($cssFile);
            }
        }
    }
}
