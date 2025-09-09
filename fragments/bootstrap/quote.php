<?php
/**
 * Bootstrap Quote Fragment für EditorJS Quote Block
 * Rendert Zitat-Elemente mit Bootstrap 5 CSS-Klassen
 */

// Daten extrahieren
$text = $this->data['text'] ?? '';
$caption = $this->data['caption'] ?? '';

// HTML-Tags bereinigen
$cleanText = strip_tags($text, '<strong><em><u><s><a><br>');
$cleanCaption = strip_tags($caption, '<strong><em><u><s><a>');

// Leere Zitate vermeiden
if (trim($cleanText) === '') {
    return;
}
?>

<blockquote class="blockquote mb-4 p-3 bg-light border-start border-primary border-4 rounded">
    <p class="mb-0 fs-5 fst-italic text-dark"><?= $cleanText ?></p>
    
    <?php if ($cleanCaption): ?>
        <footer class="blockquote-footer mt-2">
            <cite title="Quelle"><?= $cleanCaption ?></cite>
        </footer>
    <?php endif; ?>
</blockquote>