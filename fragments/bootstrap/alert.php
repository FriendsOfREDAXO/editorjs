<?php
/**
 * Bootstrap Alert Fragment für EditorJS Alert Block
 * Rendert Alert-Elemente mit Bootstrap 5 CSS-Klassen
 */

// Daten extrahieren
$type = $this->data['type'] ?? 'info';
$title = $this->data['title'] ?? '';
$message = $this->data['message'] ?? '';

// Alert-Typ zu Bootstrap-Klasse mapping
$alertTypes = [
    'info' => 'alert-info',
    'success' => 'alert-success',
    'warning' => 'alert-warning',
    'danger' => 'alert-danger',
    'error' => 'alert-danger',
    'primary' => 'alert-primary',
    'secondary' => 'alert-secondary'
];

$alertClass = $alertTypes[$type] ?? 'alert-info';

// Icons für verschiedene Alert-Typen
$icons = [
    'info' => 'bi-info-circle-fill',
    'success' => 'bi-check-circle-fill',
    'warning' => 'bi-exclamation-triangle-fill',
    'danger' => 'bi-exclamation-circle-fill',
    'error' => 'bi-exclamation-circle-fill',
    'primary' => 'bi-info-circle-fill',
    'secondary' => 'bi-info-circle-fill'
];

$icon = $icons[$type] ?? 'bi-info-circle-fill';

// Leere Alerts vermeiden
if (trim($title) === '' && trim($message) === '') {
    return;
}
?>

<div class="alert <?= $alertClass ?> d-flex align-items-start mb-4" role="alert">
    <i class="bi <?= $icon ?> flex-shrink-0 me-3 mt-1"></i>
    <div class="flex-grow-1">
        <?php if ($title): ?>
            <h5 class="alert-heading mb-2"><?= htmlspecialchars($title) ?></h5>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
    </div>
</div>