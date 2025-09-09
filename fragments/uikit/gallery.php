<?php
/**
 * UIKit3 Gallery Fragment für EditorJS Gallery Block
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

// UIKit Grid-Klassen basierend auf Bildgröße
$gridClasses = [
    'small' => 'uk-child-width-1-6@xl uk-child-width-1-5@l uk-child-width-1-4@m uk-child-width-1-3@s uk-child-width-1-2',
    'medium' => 'uk-child-width-1-4@xl uk-child-width-1-3@l uk-child-width-1-2@m uk-child-width-1-1@s',
    'large' => 'uk-child-width-1-3@xl uk-child-width-1-2@l uk-child-width-1-1@m',
    'xlarge' => 'uk-child-width-1-2@xl uk-child-width-1-1@l'
];

$gridClass = $gridClasses[$imageSize] ?? $gridClasses['medium'];

// Spacing-Klassen für UIKit
$spacingClasses = [
    'small' => 'uk-grid-small',
    'normal' => 'uk-grid-medium', 
    'large' => 'uk-grid-large'
];

$spacingClass = $spacingClasses[$spacing] ?? $spacingClasses['normal'];

// Aspect Ratio für UIKit
$aspectRatioAttr = '';
if ($aspectRatio) {
    $aspectRatioMap = [
        '1-1' => '1:1',
        '4-3' => '4:3', 
        '16-9' => '16:9',
        '21-9' => '21:9'
    ];
    $aspectRatioAttr = $aspectRatioMap[$aspectRatio] ?? '';
}
?>

<div class="uk-margin-large-bottom">
    <?php if ($showTitle && !empty($title)): ?>
        <h3 class="uk-heading-line uk-text-center">
            <span><?= htmlspecialchars($title) ?></span>
        </h3>
    <?php endif; ?>
    
    <div class="uk-grid-match <?= $gridClass ?> <?= $spacingClass ?> uk-margin-medium-top" 
         uk-grid 
         uk-lightbox="animation: slide">
        
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
            
            <div>
                <div class="uk-inline-clip uk-transition-toggle uk-light" tabindex="0">
                    <?php if ($aspectRatioAttr): ?>
                        <div class="uk-cover-container uk-border-rounded" 
                             style="aspect-ratio: <?= $aspectRatioAttr ?>;">
                    <?php endif; ?>
                    
                    <a href="<?= htmlspecialchars($imageUrl) ?>" 
                       data-caption="<?= htmlspecialchars($itemTitle . ($description ? '<br>' . $description : '')) ?>">
                        
                        <img src="<?= htmlspecialchars($imageUrl) ?>" 
                             alt="<?= htmlspecialchars($alt) ?>"
                             class="uk-width-1-1 uk-border-rounded uk-transition-scale-up uk-transition-opaque<?= $aspectRatioAttr ? ' uk-cover' : '' ?>"
                             <?= $aspectRatioAttr ? 'uk-cover' : '' ?>
                             loading="lazy">
                        
                        <!-- Overlay mit Zoom-Icon -->
                        <div class="uk-position-center">
                            <span uk-icon="icon: search; ratio: 2" 
                                  class="uk-transition-fade uk-text-white uk-drop-shadow"></span>
                        </div>
                        
                        <!-- Overlay mit Verlauf für bessere Lesbarkeit -->
                        <div class="uk-overlay uk-overlay-primary uk-position-bottom uk-transition-slide-bottom-small">
                            <?php if ($showCaptions): ?>
                                <?php if (!empty($itemTitle)): ?>
                                    <h4 class="uk-margin-remove-bottom uk-text-white">
                                        <?= htmlspecialchars($itemTitle) ?>
                                    </h4>
                                <?php endif; ?>
                                <?php if (!empty($description)): ?>
                                    <p class="uk-text-small uk-margin-remove-top uk-text-white uk-text-muted">
                                        <?= htmlspecialchars($description) ?>
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </a>
                    
                    <?php if ($aspectRatioAttr): ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($showCaptions && (!empty($itemTitle) || !empty($description)) && !$aspectRatioAttr): ?>
                    <div class="uk-margin-small-top uk-text-center">
                        <?php if (!empty($itemTitle)): ?>
                            <div class="uk-text-bold uk-text-small"><?= htmlspecialchars($itemTitle) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($description)): ?>
                            <div class="uk-text-muted uk-text-meta"><?= htmlspecialchars($description) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* UIKit Gallery Enhancements */
.uk-lightbox-toolbar {
    background: rgba(0, 0, 0, 0.8);
}

.uk-lightbox-caption {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 15px;
    font-size: 14px;
    line-height: 1.4;
}

/* Hover-Effekte für bessere UX */
.uk-transition-toggle:hover .uk-transition-fade {
    opacity: 1;
}

.uk-transition-toggle .uk-transition-fade {
    opacity: 0;
}

.uk-transition-toggle:hover .uk-transition-slide-bottom-small {
    transform: translateY(0);
}

.uk-transition-toggle .uk-transition-slide-bottom-small {
    transform: translateY(100%);
}

/* Responsive Verbesserungen */
@media (max-width: 640px) {
    .uk-lightbox-caption {
        padding: 10px;
        font-size: 12px;
    }
}
</style>