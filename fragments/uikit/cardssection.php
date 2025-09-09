<?php
/**
 * UIKit 3 Fragment für Cards Section Block
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

// Layout-Klassen für UIKit
$containerClass = '';
switch ($layout) {
    case 'grid':
        $containerClass = 'uk-grid uk-grid-match uk-child-width-1-1';
        if ($gridColumns >= 2) $containerClass .= ' uk-child-width-1-' . $gridColumns . '@m';
        if ($gridColumns >= 3) $containerClass .= ' uk-child-width-1-3@l';
        if ($gridColumns >= 4) $containerClass .= ' uk-child-width-1-4@xl';
        break;
    case 'list':
        $containerClass = 'uk-grid uk-grid-medium uk-child-width-1-1';
        break;
    case 'compact':
        $containerClass = 'uk-grid uk-grid-small uk-child-width-1-2@s uk-child-width-1-1';
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

<section class="cards-section uk-margin-large">
    <?php if ($showTitle && !empty(trim($title))): ?>
        <div class="uk-margin-medium">
            <h2 class="uk-heading-line uk-text-bold uk-text-primary">
                <span><?= rex_escape($title) ?></span>
            </h2>
        </div>
    <?php endif; ?>

    <div class="<?= $containerClass ?>" uk-grid>
        <?php foreach ($validItems as $index => $item): 
            $mediaUrl = $item['mediaUrl'] ?? '';
            $mediaType = $item['mediaType'] ?? '';
            $mediaAlt = $item['mediaAlt'] ?? '';
            $itemTitle = $item['title'] ?? '';
            $itemText = $item['text'] ?? '';
            
            if (empty($mediaUrl)) continue;
            
            // Video-Erkennung
            $isVideo = $mediaType === 'video' || preg_match('/\.(mp4|webm|ogg|avi|mov)$/i', $mediaUrl);
            $lightboxId = 'lightbox-' . md5($mediaUrl . $index);
        ?>
            <div>
                <div class="uk-card uk-card-default uk-card-hover uk-animation-toggle">
                    <div class="uk-card-media-top uk-overflow-hidden uk-position-relative">
                        <?php if ($isVideo): ?>
                            <video class="uk-width-1-1" style="height: 250px; object-fit: cover;" controls preload="metadata">
                                <source src="<?= rex_escape($mediaUrl) ?>" type="video/mp4">
                                <p class="uk-text-muted uk-padding-small">Ihr Browser unterstützt das Video-Element nicht.</p>
                            </video>
                        <?php else: ?>
                            <a href="#<?= $lightboxId ?>" uk-toggle>
                                <img src="<?= rex_escape($mediaUrl) ?>" 
                                     alt="<?= rex_escape($mediaAlt ?: $itemTitle) ?>" 
                                     class="uk-width-1-1 uk-animation-scale-up uk-animation-reverse uk-transform-origin-center"
                                     style="height: 250px; object-fit: cover;">
                                <div class="uk-position-center uk-overlay uk-overlay-primary uk-transition-fade">
                                    <span uk-icon="icon: expand; ratio: 2" class="uk-text-white"></span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($itemTitle) || !empty($itemText)): ?>
                        <div class="uk-card-body">
                            <?php if (!empty($itemTitle)): ?>
                                <h3 class="uk-card-title uk-margin-small-bottom uk-text-bold">
                                    <?= $itemTitle ?>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if (!empty($itemText)): ?>
                                <div class="uk-text-muted">
                                    <?= $itemText ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php // Lightbox Modal für Bilder ?>
                <?php if (!$isVideo): ?>
                    <div id="<?= $lightboxId ?>" class="uk-modal-full" uk-modal>
                        <div class="uk-modal-dialog uk-flex uk-justify-center uk-align-center" uk-height-viewport>
                            <button class="uk-modal-close-full uk-close-large uk-text-white" type="button" uk-close></button>
                            <img src="<?= rex_escape($mediaUrl) ?>" 
                                 alt="<?= rex_escape($mediaAlt ?: $itemTitle) ?>" 
                                 class="uk-width-auto uk-height-auto uk-max-width-90 uk-max-height-90">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
.cards-section .uk-card {
    transition: all 0.3s ease-in-out;
    border-radius: 12px;
    overflow: hidden;
}

.cards-section .uk-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.cards-section .uk-card-media-top img {
    transition: transform 0.3s ease-in-out;
}

.cards-section .uk-card:hover img {
    transform: scale(1.05);
}

.cards-section .uk-overlay {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.cards-section .uk-card:hover .uk-overlay {
    opacity: 1;
}

@media (max-width: 768px) {
    .cards-section .uk-card-media-top img,
    .cards-section .uk-card-media-top video {
        height: 200px !important;
    }
}
</style>