<?php
/**
 * Bootstrap 5 Fragment für Cards Section Block
 * Rendern einer Cards Section mit mehreren Cards in verschiedenen Layouts
 */

// Standard-Fallback falls Daten nicht korrekt sind
if (!isset($data['items']) || !is_array($data['items'])) {
    return '';
}

$title = $data['title'] ?? 'Cards';
$items = $data['items'];
$layout = $data['layout'] ?? 'grid';
$gridColumns = $data['gridColumns'] ?? 3;
$showTitle = $data['showTitle'] ?? true;

// Layout-Klassen für Bootstrap
$containerClass = '';
$itemClass = '';

switch ($layout) {
    case 'grid':
        $containerClass = 'row g-4';
        switch ($gridColumns) {
            case 1: $itemClass = 'col-12'; break;
            case 2: $itemClass = 'col-md-6'; break;
            case 3: $itemClass = 'col-lg-4 col-md-6'; break;
            case 4: $itemClass = 'col-xl-3 col-lg-4 col-md-6'; break;
            default: $itemClass = 'col-lg-4 col-md-6';
        }
        break;
    case 'list':
        $containerClass = 'd-flex flex-column gap-4';
        $itemClass = '';
        break;
    case 'compact':
        $containerClass = 'row g-3';
        $itemClass = 'col-lg-6 col-md-12';
        break;
}

// Nur Items mit Media-Inhalt anzeigen
$validItems = array_filter($items, function($item) {
    return !empty($item['mediaUrl']);
});

if (empty($validItems)) {
    return '';
}
?>

<section class="cards-section mb-5">
    <?php if ($showTitle && !empty(trim($title))): ?>
        <div class="mb-4">
            <h2 class="h3 fw-bold text-primary"><?= rex_escape($title) ?></h2>
        </div>
    <?php endif; ?>

    <div class="<?= $containerClass ?>">
        <?php foreach ($validItems as $item): 
            $mediaUrl = $item['mediaUrl'] ?? '';
            $mediaType = $item['mediaType'] ?? '';
            $mediaAlt = $item['mediaAlt'] ?? '';
            $itemTitle = $item['title'] ?? '';
            $itemText = $item['text'] ?? '';
            
            if (empty($mediaUrl)) continue;
            
            // Video-Erkennung
            $isVideo = $mediaType === 'video' || preg_match('/\.(mp4|webm|ogg|avi|mov)$/i', $mediaUrl);
        ?>
            <div class="<?= $itemClass ?>">
                <div class="card h-100 shadow-sm border-0">
                    <div class="position-relative overflow-hidden">
                        <?php if ($isVideo): ?>
                            <video class="card-img-top" style="height: 250px; object-fit: cover;" controls preload="metadata">
                                <source src="<?= rex_escape($mediaUrl) ?>" type="video/mp4">
                                <p class="text-muted p-3">Ihr Browser unterstützt das Video-Element nicht.</p>
                            </video>
                        <?php else: ?>
                            <img src="<?= rex_escape($mediaUrl) ?>" 
                                 class="card-img-top" 
                                 alt="<?= rex_escape($mediaAlt ?: $itemTitle) ?>" 
                                 style="height: 250px; object-fit: cover; cursor: pointer;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#lightbox-<?= md5($mediaUrl) ?>">
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($itemTitle) || !empty($itemText)): ?>
                        <div class="card-body d-flex flex-column">
                            <?php if (!empty($itemTitle)): ?>
                                <h5 class="card-title fw-bold text-dark"><?= $itemTitle ?></h5>
                            <?php endif; ?>
                            
                            <?php if (!empty($itemText)): ?>
                                <div class="card-text text-muted flex-grow-1"><?= $itemText ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php // Lightbox Modal für Bilder ?>
                <?php if (!$isVideo): ?>
                    <div class="modal fade" id="lightbox-<?= md5($mediaUrl) ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content bg-transparent border-0">
                                <div class="modal-header border-0 pb-0">
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Schließen"></button>
                                </div>
                                <div class="modal-body text-center p-0">
                                    <img src="<?= rex_escape($mediaUrl) ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?= rex_escape($mediaAlt ?: $itemTitle) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
.cards-section .card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.cards-section .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.cards-section .card-img-top {
    transition: transform 0.3s ease-in-out;
}

.cards-section .card:hover .card-img-top {
    transform: scale(1.02);
}

.cards-section .modal-content {
    background: rgba(0, 0, 0, 0.9) !important;
}

@media (max-width: 768px) {
    .cards-section .card-img-top {
        height: 200px !important;
    }
}
</style>