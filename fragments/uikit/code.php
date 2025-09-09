<?php
/**
 * UIKit Code Fragment für EditorJS Code Block
 * Rendert Code-Elemente mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren
$code = $this->data['code'] ?? '';

// Leere Code-Blöcke vermeiden
if (trim($code) === '') {
    return;
}
?>

<div class="uk-margin">
    <pre class="uk-background-muted uk-padding-small uk-border-rounded uk-overflow-auto"><code><?= htmlspecialchars($code) ?></code></pre>
</div>