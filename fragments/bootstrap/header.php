<?php
/**
 * Bootstrap Header Fragment für EditorJS Header Block
 * Rendert Überschriften-Elemente mit Bootstrap 5 CSS-Klassen
 */

// Daten extrahieren
$level = (int) ($this->data['level'] ?? 2);
$text = $this->data['text'] ?? '';

// Level validieren
if ($level < 1 || $level > 6) {
    $level = 2;
}

// Bootstrap-Klassen für Überschriften
$classes = [];
switch ($level) {
    case 1:
        $classes[] = 'display-4';
        $classes[] = 'fw-bold';
        $classes[] = 'mb-4';
        break;
    case 2:
        $classes[] = 'h2';
        $classes[] = 'fw-bold';
        $classes[] = 'mb-3';
        break;
    case 3:
        $classes[] = 'h3';
        $classes[] = 'mb-3';
        break;
    case 4:
        $classes[] = 'h4';
        $classes[] = 'mb-2';
        break;
    case 5:
        $classes[] = 'h5';
        $classes[] = 'mb-2';
        break;
    case 6:
        $classes[] = 'h6';
        $classes[] = 'mb-2';
        break;
}

$classString = !empty($classes) ? ' class="' . implode(' ', $classes) . '"' : '';
?>

<h<?= $level ?><?= $classString ?>><?= htmlspecialchars($text) ?></h<?= $level ?>>