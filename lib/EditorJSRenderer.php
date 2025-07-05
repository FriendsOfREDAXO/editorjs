<?php

namespace FriendsOfRedaxo\EditorJs;

/**
 * EditorJS Renderer für REDAXO
 *
 * Konvertiert EditorJS JSON-Daten zu HTML für die Frontend-Ausgabe.
 * Unterstützt Standard-Blöcke und dynamisch gerenderte benutzerdefinierte Blöcke über Fragmente.
 */
class EditorJsRenderer
{
    /**
     * Hauptmethode: Konvertiert EditorJS JSON zu HTML
     *
     * @param string|array $data EditorJS JSON-String oder Array
     * @return string HTML-Ausgabe
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

        // Standard-Blöcke mit dedizierten Methoden behandeln
        switch ($type) {
            case 'header':
                return $this->renderHeader($data);
            case 'paragraph':
                return $this->renderParagraph($data);
            case 'list':
                return $this->renderList($data);
            case 'quote':
                return $this->renderQuote($data);
            case 'delimiter':
                return $this->renderDelimiter($data);
            case 'code':
                return $this->renderCode($data);
        }

        // Benutzerdefinierte Blöcke dynamisch über Fragmente rendern
        $fragmentPath = \rex_path::addon('editorjs', 'fragments/' . $type . '.fragment.php');

        if (file_exists($fragmentPath)) {
            $fragment = new \rex_fragment();
            $fragment->setVar('mode', 'output'); // Modus für Frontend-Rendering setzen
            $fragment->setVar('data', $data);     // Block-Daten an das Fragment übergeben
            return $fragment->parse($type . '.fragment.php');
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
            $cleanItem = strip_tags($item, '<strong><em><u><s><a>');
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
        $html = "<blockquote>\n<p>{$cleanText}</p>\n";
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
        return '<hr class="editorjs-delimiter">\n';
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
