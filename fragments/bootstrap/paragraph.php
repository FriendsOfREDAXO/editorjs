<?php
/**
 * Bootstrap Paragraph Fragment für EditorJS Paragraph Block
 * Rendert Absatz-Elemente mit Bootstrap 5 CSS-Klassen
 */

// Daten extrahieren
$text = $this->data['text'] ?? '';

// Einfache HTML-Tags erlauben (strong, em, u, s, a)
$allowedTags = '<strong><em><u><s><a><code><mark><small>';
$cleanText = strip_tags($text, $allowedTags);

// Leere Paragraphen vermeiden
if (trim($cleanText) === '') {
    return;
}
?>

<p class="mb-3"><?= $cleanText ?></p>