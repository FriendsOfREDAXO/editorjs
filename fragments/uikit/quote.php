<?php
/**
 * UIKit Quote Fragment für EditorJS Quote Block
 * Rendert Zitat-Elemente mit UIKit 3 CSS-Klassen
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

<blockquote class="uk-margin uk-card uk-card-default uk-card-body uk-border-rounded uk-box-shadow-small">
    <p class="uk-text-large uk-text-italic uk-margin-small-bottom"><?= $cleanText ?></p>
    
    <?php if ($cleanCaption): ?>
        <footer class="uk-text-meta">
            <cite>&mdash; <?= $cleanCaption ?></cite>
        </footer>
    <?php endif; ?>
</blockquote>