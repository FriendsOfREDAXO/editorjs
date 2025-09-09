<?php
/**
 * Bootstrap Downloads Fragment für EditorJS Downloads Block
 * Rendert Download-Listen mit Bootstrap 5 CSS-Klassen
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

// File-Icon Helper-Funktion
function getBootstrapFileIcon($extension) {
    $iconMap = [
        'pdf' => ['icon' => 'bi-file-earmark-pdf-fill', 'color' => 'text-danger'],
        'doc' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-primary'],
        'docx' => ['icon' => 'bi-file-earmark-word-fill', 'color' => 'text-primary'],
        'xls' => ['icon' => 'bi-file-earmark-excel-fill', 'color' => 'text-success'],
        'xlsx' => ['icon' => 'bi-file-earmark-excel-fill', 'color' => 'text-success'],
        'ppt' => ['icon' => 'bi-file-earmark-ppt-fill', 'color' => 'text-warning'],
        'pptx' => ['icon' => 'bi-file-earmark-ppt-fill', 'color' => 'text-warning'],
        'zip' => ['icon' => 'bi-file-earmark-zip-fill', 'color' => 'text-secondary'],
        'rar' => ['icon' => 'bi-file-earmark-zip-fill', 'color' => 'text-secondary'],
        '7z' => ['icon' => 'bi-file-earmark-zip-fill', 'color' => 'text-secondary'],
        'txt' => ['icon' => 'bi-file-earmark-text-fill', 'color' => 'text-secondary'],
        'mp3' => ['icon' => 'bi-file-earmark-music-fill', 'color' => 'text-warning'],
        'wav' => ['icon' => 'bi-file-earmark-music-fill', 'color' => 'text-warning'],
        'mp4' => ['icon' => 'bi-camera-video-fill', 'color' => 'text-warning'],
        'avi' => ['icon' => 'bi-camera-video-fill', 'color' => 'text-warning'],
        'jpg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-info'],
        'jpeg' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-info'],
        'png' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-info'],
        'gif' => ['icon' => 'bi-file-earmark-image-fill', 'color' => 'text-info']
    ];
    
    return $iconMap[$extension] ?? ['icon' => 'bi-file-earmark-fill', 'color' => 'text-secondary'];
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

<div class="downloads-section mb-4">
    <?php if ($showTitle && !empty($title)): ?>
        <h3 class="h4 mb-3"><?= htmlspecialchars($title) ?></h3>
    <?php endif; ?>
    
    <?php if ($layout === 'grid'): ?>
        <div class="row g-3">
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
        $iconData = getBootstrapFileIcon($extension);
        
        // Dateigröße ermitteln
        $filePath = rex_path::media($file);
        $fileSize = file_exists($filePath) ? formatFileSize(filesize($filePath)) : '';
        ?>
        
        <?php if ($layout === 'grid'): ?>
            <div class="col-md-6 col-lg-4">
        <?php endif; ?>
        
        <div class="card h-100 shadow-sm hover-shadow">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3 flex-shrink-0">
                        <?php if (isImageFile($extension)): ?>
                            <img src="<?= htmlspecialchars($fileUrl) ?>" 
                                 alt="<?= htmlspecialchars($itemTitle ?: $file) ?>" 
                                 class="rounded" 
                                 style="width: 48px; height: 48px; object-fit: cover;">
                        <?php else: ?>
                            <i class="bi <?= $iconData['icon'] ?> <?= $iconData['color'] ?> fs-1"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex-grow-1 min-w-0">
                        <?php if (!empty($itemTitle)): ?>
                            <h6 class="card-title mb-1 text-truncate"><?= htmlspecialchars($itemTitle) ?></h6>
                        <?php endif; ?>
                        
                        <p class="card-subtitle mb-2 text-muted small text-truncate">
                            <?= htmlspecialchars($file) ?>
                            <?php if ($fileSize): ?>
                                <span class="text-secondary">(<?= $fileSize ?>)</span>
                            <?php endif; ?>
                        </p>
                        
                        <?php if (!empty($description)): ?>
                            <p class="card-text small"><?= htmlspecialchars($description) ?></p>
                        <?php endif; ?>
                        
                        <a href="<?= htmlspecialchars($fileUrl) ?>" 
                           target="_blank" 
                           rel="noopener"
                           class="btn btn-outline-primary btn-sm stretched-link">
                            <i class="bi bi-download me-1"></i>
                            Herunterladen
                        </a>
                    </div>
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

<style>
.hover-shadow {
    transition: box-shadow 0.15s ease-in-out;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>