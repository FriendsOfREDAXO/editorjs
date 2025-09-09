<?php
/**
 * UIKit Alert Fragment für EditorJS Alert Block
 * Rendert Alert-Elemente mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren
$type = $this->data['type'] ?? 'info';
$title = $this->data['title'] ?? '';
$message = $this->data['message'] ?? '';

// Alert-Typ zu UIKit-Klasse mapping
$alertTypes = [
    'info' => 'uk-alert-primary',
    'success' => 'uk-alert-success',
    'warning' => 'uk-alert-warning',
    'danger' => 'uk-alert-danger',
    'error' => 'uk-alert-danger',
    'primary' => 'uk-alert-primary',
    'secondary' => 'uk-alert-default'
];

$alertClass = $alertTypes[$type] ?? 'uk-alert-primary';

// Icons für verschiedene Alert-Typen (UIKit Icons)
$icons = [
    'info' => 'uk-icon-info',
    'success' => 'uk-icon-check',
    'warning' => 'uk-icon-warning',
    'danger' => 'uk-icon-ban',
    'error' => 'uk-icon-ban',
    'primary' => 'uk-icon-info',
    'secondary' => 'uk-icon-info'
];

$icon = $icons[$type] ?? 'uk-icon-info';

// Leere Alerts vermeiden
if (trim($title) === '' && trim($message) === '') {
    return;
}
?>

<div class="uk-alert <?= $alertClass ?> uk-margin" uk-alert>
    <div class="uk-flex uk-flex-top">
        <div class="uk-margin-small-right">
            <span uk-icon="icon: <?= str_replace('uk-icon-', '', $icon) ?>; ratio: 1.2"></span>
        </div>
        <div class="uk-flex-1">
            <?php if ($title): ?>
                <h5 class="uk-alert-title uk-margin-small-bottom"><?= htmlspecialchars($title) ?></h5>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>