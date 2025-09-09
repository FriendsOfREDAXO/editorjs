<?php
/**
 * Bootstrap Code Fragment für EditorJS Code Block
 * Rendert Code-Elemente mit Bootstrap 5 CSS-Klassen
 */

// Daten extrahieren
$code = $this->data['code'] ?? '';

// Leere Code-Blöcke vermeiden
if (trim($code) === '') {
    return;
}
?>

<div class="mb-4">
    <pre class="bg-dark text-light p-3 rounded overflow-auto"><code><?= htmlspecialchars($code) ?></code></pre>
</div>