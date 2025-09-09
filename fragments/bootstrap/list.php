<?php
/**
 * Bootstrap List Fragment für EditorJS List Block
 * Rendert Listen-Elemente mit Bootstrap 5 CSS-Klassen
 */

// Daten extrahieren
$style = $this->data['style'] ?? 'unordered';
$items = $this->data['items'] ?? [];

// Keine leeren Listen
if (empty($items)) {
    return;
}

$tag = $style === 'ordered' ? 'ol' : 'ul';
$classes = ['mb-3'];

// Zusätzliche Bootstrap-Klassen je nach Listentyp
if ($style === 'unordered') {
    $classes[] = 'list-unstyled';
    $classes[] = 'ps-3';
}

$classString = implode(' ', $classes);
?>

<<?= $tag ?> class="<?= $classString ?>">
    <?php foreach ($items as $item): ?>
        <?php
        // EditorJS v2.31+ verwendet Objekt-Format für Listen-Items
        if (is_array($item) && isset($item['content'])) {
            $content = $item['content'];
        } else {
            // Fallback für alte String-basierte Listen
            $content = is_string($item) ? $item : '';
        }
        
        $cleanItem = strip_tags($content, '<strong><em><u><s><a><code><mark>');
        
        // Leere Items überspringen
        if (trim($cleanItem) === '') {
            continue;
        }
        ?>
        <li class="<?= $style === 'unordered' ? 'mb-1' : '' ?>">
            <?php if ($style === 'unordered'): ?>
                <i class="bi bi-chevron-right text-primary me-2"></i>
            <?php endif; ?>
            <?= $cleanItem ?>
        </li>
    <?php endforeach; ?>
</<?= $tag ?>>