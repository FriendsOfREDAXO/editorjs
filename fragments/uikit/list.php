<?php
/**
 * UIKit List Fragment für EditorJS List Block
 * Rendert Listen-Elemente mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren
$style = $this->data['style'] ?? 'unordered';
$items = $this->data['items'] ?? [];

// Keine leeren Listen
if (empty($items)) {
    return;
}

$tag = $style === 'ordered' ? 'ol' : 'ul';
$classes = ['uk-margin'];

// Zusätzliche UIKit-Klassen je nach Listentyp
if ($style === 'unordered') {
    $classes[] = 'uk-list';
    $classes[] = 'uk-list-bullet';
} else {
    $classes[] = 'uk-list';
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
        <li><?= $cleanItem ?></li>
    <?php endforeach; ?>
</<?= $tag ?>>