<?php
/**
 * UIKit Paragraph Fragment für EditorJS Paragraph Block
 * Rendert Absatz-Elemente mit UIKit 3 CSS-Klassen
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

<p class="uk-margin"><?= $cleanText ?></p>