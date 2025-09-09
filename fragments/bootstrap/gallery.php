<?php
/**
 * Bootstrap Gallery Fragment für EditorJS Gallery Block
 */

$title = $this->data['title'] ?? 'Bildergalerie';
$items = $this->data['items'] ?? [];
$showTitle = $this->data['showTitle'] ?? true;
$layout = $this->data['layout'] ?? 'grid';
$imageSize = $this->data['imageSize'] ?? 'medium';
$showCaptions = $this->data['showCaptions'] ?? true;
$aspectRatio = $this->data['aspectRatio'] ?? '';
$spacing = $this->data['spacing'] ?? 'normal';

if (empty($items)) {
    return;
}

// Bootstrap-Spalten basierend auf Bildgröße
$colClasses = [
    'small' => 'col-lg-2 col-md-3 col-sm-4 col-6',
    'medium' => 'col-lg-3 col-md-4 col-sm-6 col-12',
    'large' => 'col-lg-4 col-md-6 col-12',
    'xlarge' => 'col-lg-6 col-12'
];

$colClass = $colClasses[$imageSize] ?? $colClasses['medium'];

// Spacing-Klassen
$spacingClasses = [
    'small' => 'g-2',
    'normal' => 'g-3', 
    'large' => 'g-4'
];

$spacingClass = $spacingClasses[$spacing] ?? $spacingClasses['normal'];

// Aspect Ratio für Bootstrap
$aspectRatioClass = '';
if ($aspectRatio) {
    $aspectRatioMap = [
        '1-1' => 'ratio-1x1',
        '4-3' => 'ratio-4x3', 
        '16-9' => 'ratio-16x9',
        '21-9' => 'ratio-21x9'
    ];
    $aspectRatioClass = $aspectRatioMap[$aspectRatio] ?? '';
}
?>

<div class="gallery-bootstrap mb-4">
    <?php if ($showTitle && !empty($title)): ?>
        <h3 class="gallery-title mb-3"><?= htmlspecialchars($title) ?></h3>
    <?php endif; ?>
    
    <div class="row <?= $spacingClass ?>">
        <?php foreach ($items as $index => $item): ?>
            <?php
            $imageFile = $item['imageFile'] ?? '';
            $imageUrl = $item['imageUrl'] ?? '';
            $itemTitle = $item['title'] ?? '';
            $description = $item['description'] ?? '';
            $alt = $item['alt'] ?? $itemTitle ?: $imageFile;
            
            if (empty($imageFile) && empty($imageUrl)) {
                continue;
            }
            
            // Bild-URL für REDAXO generieren
            if ($imageFile && !$imageUrl) {
                $imageUrl = rex_url::media($imageFile);
            }
            ?>
            
            <div class="<?= $colClass ?>">
                <div class="gallery-item h-100">
                    <?php if ($aspectRatioClass): ?>
                        <div class="ratio <?= $aspectRatioClass ?>">
                    <?php endif; ?>
                    
                    <a href="<?= htmlspecialchars($imageUrl) ?>" 
                       data-bs-toggle="modal" 
                       data-bs-target="#galleryModal" 
                       data-bs-slide-to="<?= $index ?>"
                       data-bs-image="<?= htmlspecialchars($imageUrl) ?>"
                       data-bs-title="<?= htmlspecialchars($itemTitle) ?>"
                       data-bs-description="<?= htmlspecialchars($description) ?>"
                       class="d-block text-decoration-none">
                        
                        <img src="<?= htmlspecialchars($imageUrl) ?>" 
                             alt="<?= htmlspecialchars($alt) ?>"
                             class="img-fluid rounded <?= $aspectRatioClass ? '' : 'w-100' ?>"
                             style="<?= $aspectRatioClass ? 'object-fit: cover; height: 100%;' : '' ?> cursor: pointer;"
                             loading="lazy">
                    </a>
                    
                    <?php if ($aspectRatioClass): ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($showCaptions && (!empty($itemTitle) || !empty($description))): ?>
                        <div class="gallery-caption mt-2">
                            <?php if (!empty($itemTitle)): ?>
                                <div class="fw-bold small"><?= htmlspecialchars($itemTitle) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($description)): ?>
                                <div class="text-muted small"><?= htmlspecialchars($description) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bootstrap Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                        data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
                
                <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <?php foreach ($items as $index => $item): ?>
                            <?php
                            $imageFile = $item['imageFile'] ?? '';
                            $imageUrl = $item['imageUrl'] ?? '';
                            $itemTitle = $item['title'] ?? '';
                            $description = $item['description'] ?? '';
                            
                            if (empty($imageFile) && empty($imageUrl)) {
                                continue;
                            }
                            
                            if ($imageFile && !$imageUrl) {
                                $imageUrl = rex_url::media($imageFile);
                            }
                            ?>
                            
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                     alt="<?= htmlspecialchars($itemTitle ?: $imageFile) ?>"
                                     class="d-block mx-auto img-fluid rounded"
                                     style="max-height: 90vh;">
                                
                                <?php if ($showCaptions && (!empty($itemTitle) || !empty($description))): ?>
                                    <div class="carousel-caption">
                                        <?php if (!empty($itemTitle)): ?>
                                            <h5><?= htmlspecialchars($itemTitle) ?></h5>
                                        <?php endif; ?>
                                        <?php if (!empty($description)): ?>
                                            <p><?= htmlspecialchars($description) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($items) > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gallery Modal Event Listener
    const galleryModal = document.getElementById('galleryModal');
    if (galleryModal) {
        galleryModal.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            const slideIndex = trigger.getAttribute('data-bs-slide-to');
            const carousel = bootstrap.Carousel.getInstance(document.getElementById('galleryCarousel')) || 
                           new bootstrap.Carousel(document.getElementById('galleryCarousel'));
            
            if (slideIndex) {
                carousel.to(parseInt(slideIndex));
            }
        });
    }
});
</script>