<?php
/**
 * UIKit Downloads Fragment für EditorJS Downloads Block
 * Rendert Download-Listen mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren
$title = $this->data['title'] ?? 'Downloads';
$items = $this->data['items'] ?? [];
$showTitle = $this->data['showTitle'] ?? true;
$layout = $this->data['layout'] ?? 'list';

// Keine leeren Download-Listen
if (empty($items)) {
    return;
}

// File-Icon Helper-Funktion für UIKit
function getUIKitFileIcon($extension) {
    $iconMap = [
        'pdf' => ['icon' => 'file-pdf', 'color' => 'uk-text-danger'],
        'doc' => ['icon' => 'file-text', 'color' => 'uk-text-primary'],
        'docx' => ['icon' => 'file-text', 'color' => 'uk-text-primary'],
        'xls' => ['icon' => 'table', 'color' => 'uk-text-success'],
        'xlsx' => ['icon' => 'table', 'color' => 'uk-text-success'],
        'ppt' => ['icon' => 'file-text', 'color' => 'uk-text-warning'],
        'pptx' => ['icon' => 'file-text', 'color' => 'uk-text-warning'],
        'zip' => ['icon' => 'album', 'color' => 'uk-text-muted'],
        'rar' => ['icon' => 'album', 'color' => 'uk-text-muted'],
        '7z' => ['icon' => 'album', 'color' => 'uk-text-muted'],
        'txt' => ['icon' => 'file-text', 'color' => 'uk-text-muted'],
        'mp3' => ['icon' => 'play-circle', 'color' => 'uk-text-warning'],
        'wav' => ['icon' => 'play-circle', 'color' => 'uk-text-warning'],
        'mp4' => ['icon' => 'video-camera', 'color' => 'uk-text-warning'],
        'avi' => ['icon' => 'video-camera', 'color' => 'uk-text-warning'],
        'jpg' => ['icon' => 'image', 'color' => 'uk-text-primary'],
        'jpeg' => ['icon' => 'image', 'color' => 'uk-text-primary'],
        'png' => ['icon' => 'image', 'color' => 'uk-text-primary'],
        'gif' => ['icon' => 'image', 'color' => 'uk-text-primary']
    ];
    
    return $iconMap[$extension] ?? ['icon' => 'file', 'color' => 'uk-text-muted'];
}

function isImageFile($extension) {
    return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
}

function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}
?>

<div class="downloads-section uk-margin">
    <?php if ($showTitle && !empty($title)): ?>
        <h3 class="uk-heading-small uk-margin"><?= htmlspecialchars($title) ?></h3>
    <?php endif; ?>
    
    <?php if ($layout === 'grid'): ?>
        <div class="uk-grid-match uk-child-width-1-2@s uk-child-width-1-3@m" uk-grid>
    <?php endif; ?>
    
    <?php foreach ($items as $item): ?>
        <?php
        $file = $item['file'] ?? '';
        $itemTitle = $item['title'] ?? '';
        $description = $item['description'] ?? '';
        
        if (empty($file)) {
            continue;
        }
        
        // Media-URL für REDAXO generieren
        $fileUrl = rex_url::media($file);
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $iconData = getUIKitFileIcon($extension);
        
        // Dateigröße ermitteln
        $filePath = rex_path::media($file);
        $fileSize = file_exists($filePath) ? formatFileSize(filesize($filePath)) : '';
        ?>
        
        <?php if ($layout === 'grid'): ?>
            <div>
        <?php endif; ?>
        
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-border-rounded">
            <div class="uk-flex uk-flex-top">
                <div class="uk-margin-small-right uk-flex-none">
                    <?php if (isImageFile($extension)): ?>
                        <img src="<?= htmlspecialchars($fileUrl) ?>" 
                             alt="<?= htmlspecialchars($itemTitle ?: $file) ?>" 
                             class="uk-border-rounded" 
                             style="width: 48px; height: 48px; object-fit: cover;">
                    <?php else: ?>
                        <span uk-icon="icon: <?= $iconData['icon'] ?>; ratio: 2" 
                              class="<?= $iconData['color'] ?>"></span>
                    <?php endif; ?>
                </div>
                
                <div class="uk-flex-1 uk-width-expand">
                    <?php if (!empty($itemTitle)): ?>
                        <h6 class="uk-card-title uk-margin-remove-bottom uk-text-truncate">
                            <?= htmlspecialchars($itemTitle) ?>
                        </h6>
                    <?php endif; ?>
                    
                    <p class="uk-text-meta uk-margin-small-top uk-text-truncate">
                        <?= htmlspecialchars($file) ?>
                        <?php if ($fileSize): ?>
                            <span class="uk-text-muted">(<?= $fileSize ?>)</span>
                        <?php endif; ?>
                    </p>
                    
                    <?php if (!empty($description)): ?>
                        <p class="uk-text-small uk-margin-small"><?= htmlspecialchars($description) ?></p>
                    <?php endif; ?>
                    
                    <a href="<?= htmlspecialchars($fileUrl) ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="uk-button uk-button-primary uk-button-small">
                        <span uk-icon="icon: download; ratio: 0.8" class="uk-margin-small-right"></span>
                        Herunterladen
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($layout === 'grid'): ?>
            </div>
        <?php endif; ?>
        
    <?php endforeach; ?>
    
    <?php if ($layout === 'grid'): ?>
        </div>
    <?php endif; ?>
</div>