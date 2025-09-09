<?php
/**
 * UIKit Header Fragment für EditorJS Header Block
 * Rendert Überschriften-Elemente mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren
$level = (int) ($this->data['level'] ?? 2);
$text = $this->data['text'] ?? '';

// Level validieren
if ($level < 1 || $level > 6) {
    $level = 2;
}

// UIKit-Klassen für Überschriften
$classes = [];
switch ($level) {
    case 1:
        $classes[] = 'uk-heading-large';
        $classes[] = 'uk-margin-medium';
        break;
    case 2:
        $classes[] = 'uk-heading-medium';
        $classes[] = 'uk-margin';
        break;
    case 3:
        $classes[] = 'uk-heading-small';
        $classes[] = 'uk-margin';
        break;
    case 4:
        $classes[] = 'uk-h4';
        $classes[] = 'uk-margin-small';
        break;
    case 5:
        $classes[] = 'uk-h5';
        $classes[] = 'uk-margin-small';
        break;
    case 6:
        $classes[] = 'uk-h6';
        $classes[] = 'uk-margin-small';
        break;
}

$classString = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
?>

<h<?= $level ?><?= $classString ?>><?= htmlspecialchars($text) ?></h<?= $level ?>>