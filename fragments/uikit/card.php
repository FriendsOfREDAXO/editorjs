<?php
/**
 * UIKit3 Card Fragment für EditorJS Card Block
 * Rendert Card-Elemente mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren
$title = $this->data['title'] ?? '';
$text = $this->data['text'] ?? '';
$mediaFile = $this->data['mediaFile'] ?? '';
$mediaUrl = $this->data['mediaUrl'] ?? '';
$mediaType = $this->data['mediaType'] ?? 'image';
$mediaAlt = $this->data['mediaAlt'] ?? '';
$layout = $this->data['layout'] ?? 'vertical';
$gridColumns = $this->data['gridColumns'] ?? 2;
$aspectRatio = $this->data['aspectRatio'] ?? 'auto';
$lightbox = $this->data['lightbox'] ?? true;
$linkUrl = $this->data['linkUrl'] ?? '';
$linkTarget = $this->data['linkTarget'] ?? '_self';

// Video-Eigenschaften
$videoAutoplay = $this->data['videoAutoplay'] ?? false;
$videoMuted = $this->data['videoMuted'] ?? false;
$videoLoop = $this->data['videoLoop'] ?? false;
$videoControls = $this->data['videoControls'] ?? true;

// Media-URL generieren
if ($mediaFile && !$mediaUrl) {
    $mediaUrl = rex_url::media($mediaFile);
}

// UIKit-Klassen bestimmen
$cardClasses = ['uk-card', 'uk-card-default'];

// Layout-spezifische Klassen
if ($layout === 'horizontal') {
    $cardClasses[] = 'uk-card-horizontal';
    $cardClasses[] = 'uk-margin-medium-bottom';
} else {
    $cardClasses[] = 'uk-margin-medium-bottom';
}

// Aspect Ratio für UIKit
$aspectRatioAttr = '';
if ($aspectRatio !== 'auto') {
    $aspectRatioMap = [
        '16-9' => '16:9',
        '4-3' => '4:3', 
        '1-1' => '1:1',
        '3-2' => '3:2'
    ];
    $aspectRatioAttr = $aspectRatioMap[$aspectRatio] ?? '';
}

$cardClassString = implode(' ', $cardClasses);

// Content vorbereiten
ob_start();
?>

<div class="<?= $cardClassString ?>">
    <?php if ($layout === 'horizontal' && $mediaUrl): ?>
        <div class="uk-grid-collapse uk-child-width-1-2@s uk-margin-remove-top" uk-grid>
            <div>
    <?php endif; ?>
    
    <?php if ($mediaUrl): ?>
        <div class="uk-card-media-top<?= $layout === 'horizontal' ? ' uk-cover-container' : '' ?>">
            <?php if ($aspectRatioAttr && $layout !== 'horizontal'): ?>
                <div uk-height-viewport="offset-top: true; offset-bottom: 20" 
                     style="background-image: url('<?= htmlspecialchars($mediaUrl) ?>'); 
                            background-size: cover; 
                            background-position: center; 
                            aspect-ratio: <?= $aspectRatioAttr ?>;">
            <?php endif; ?>
            
            <?php if ($mediaType === 'video'): ?>
                <video class="uk-width-1-1<?= $layout === 'horizontal' ? ' uk-cover' : '' ?>" 
                       <?= $videoControls ? 'controls' : '' ?>
                       <?= $videoAutoplay ? 'autoplay' : '' ?>
                       <?= $videoMuted ? 'muted' : '' ?>
                       <?= $videoLoop ? 'loop' : '' ?>
                       <?= $layout === 'horizontal' ? 'uk-cover' : '' ?>>
                    <source src="<?= htmlspecialchars($mediaUrl) ?>" type="<?= $this->getVideoType($mediaFile) ?>">
                    Ihr Browser unterstützt das Video-Element nicht.
                </video>
            <?php else: ?>
                <?php if ($lightbox && !$linkUrl): ?>
                    <a href="<?= htmlspecialchars($mediaUrl) ?>" data-caption="<?= htmlspecialchars($title ?: $mediaAlt) ?>" uk-lightbox>
                <?php endif; ?>
                
                <img src="<?= htmlspecialchars($mediaUrl) ?>" 
                     alt="<?= htmlspecialchars($mediaAlt ?: $title ?: 'Card Image') ?>"
                     class="uk-width-1-1<?= $layout === 'horizontal' ? ' uk-cover' : '' ?>"
                     <?= $layout === 'horizontal' ? 'uk-cover' : '' ?>>
                
                <?php if ($lightbox && !$linkUrl): ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($aspectRatioAttr && $layout !== 'horizontal'): ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($layout === 'horizontal' && $mediaUrl): ?>
            </div>
            <div>
    <?php endif; ?>
    
    <?php if ($title || $text): ?>
        <div class="uk-card-body">
            <?php if ($title): ?>
                <h3 class="uk-card-title"><?= htmlspecialchars($title) ?></h3>
            <?php endif; ?>
            
            <?php if ($text): ?>
                <div class="uk-text-muted">
                    <?= $text ?>
                </div>
            <?php endif; ?>
            
            <?php if ($linkUrl): ?>
                <p class="uk-margin-top">
                    <a href="<?= htmlspecialchars($linkUrl) ?>" 
                       target="<?= htmlspecialchars($linkTarget) ?>" 
                       class="uk-button uk-button-text">
                        Weiterlesen <span uk-icon="arrow-right"></span>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($layout === 'horizontal' && $mediaUrl): ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$cardContent = ob_get_clean();

// Wenn Link vorhanden ist und kein Lightbox, Card als ganzes verlinken
if ($linkUrl && !$lightbox && !strpos($cardContent, 'uk-button')) {
    echo '<a href="' . htmlspecialchars($linkUrl) . '" target="' . htmlspecialchars($linkTarget) . '" class="uk-link-reset">';
    echo $cardContent;
    echo '</a>';
} else {
    echo $cardContent;
}
?>