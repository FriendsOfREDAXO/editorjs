<?php
/**
 * UIKit3 TextImage Fragment für EditorJS TextImage Block
 * Rendert Text+Bild/Video-Elemente mit UIKit 3 CSS-Klassen
 */

// Daten extrahieren (Rückwärtskompatibilität)
$text = $this->data['text'] ?? '';
$mediaFile = $this->data['mediaFile'] ?? $this->data['imageFile'] ?? '';
$mediaUrl = $this->data['mediaUrl'] ?? $this->data['imageUrl'] ?? '';
$mediaType = $this->data['mediaType'] ?? ($this->data['imageFile'] ? 'image' : $this->detectMediaType($mediaFile));
$mediaAlt = $this->data['mediaAlt'] ?? $this->data['imageAlt'] ?? '';
$caption = $this->data['caption'] ?? '';
$layout = $this->data['layout'] ?? 'left';
$lightbox = $this->data['lightbox'] ?? true;

// Video-Eigenschaften
$videoAutoplay = $this->data['videoAutoplay'] ?? false;
$videoMuted = $this->data['videoMuted'] ?? false;
$videoLoop = $this->data['videoLoop'] ?? false;
$videoControls = $this->data['videoControls'] ?? true;

// Media-URL generieren
if ($mediaFile && !$mediaUrl) {
    $mediaUrl = rex_url::media($mediaFile);
}

// UIKit Grid-Klassen bestimmen
$gridClasses = ['uk-grid', 'uk-grid-small', 'uk-margin-medium-bottom'];
$mediaClasses = ['uk-width-1-3@m'];
$textClasses = ['uk-width-2-3@m'];

// Layout-spezifische Anpassungen
switch ($layout) {
    case 'right':
        $gridClasses[] = 'uk-flex-row-reverse@m';
        break;
    case 'top':
        $mediaClasses = ['uk-width-1-1'];
        $textClasses = ['uk-width-1-1'];
        break;
    case 'left':
    default:
        // Standard-Layout
        break;
}

$gridClassString = implode(' ', $gridClasses);
$mediaClassString = implode(' ', $mediaClasses);
$textClassString = implode(' ', $textClasses);
?>

<div class="<?= $gridClassString ?>" uk-grid>
    <?php if ($mediaUrl): ?>
        <div class="<?= $mediaClassString ?>">
            <div class="uk-inline-clip uk-transition-toggle" tabindex="0">
                <?php if ($mediaType === 'video'): ?>
                    <div class="uk-inline">
                        <video class="uk-width-1-1 uk-border-rounded" 
                               <?= $videoControls ? 'controls' : '' ?>
                               <?= $videoAutoplay ? 'autoplay' : '' ?>
                               <?= $videoMuted ? 'muted' : '' ?>
                               <?= $videoLoop ? 'loop' : '' ?>>
                            <source src="<?= htmlspecialchars($mediaUrl) ?>" type="<?= $this->getVideoType($mediaFile) ?>">
                            Ihr Browser unterstützt das Video-Element nicht.
                        </video>
                    </div>
                <?php else: ?>
                    <?php if ($lightbox): ?>
                        <a href="<?= htmlspecialchars($mediaUrl) ?>" 
                           data-caption="<?= htmlspecialchars($caption ?: $mediaAlt) ?>" 
                           uk-lightbox>
                    <?php endif; ?>
                    
                    <img src="<?= htmlspecialchars($mediaUrl) ?>" 
                         alt="<?= htmlspecialchars($mediaAlt ?: 'Content Image') ?>"
                         class="uk-width-1-1 uk-border-rounded uk-transition-scale-up uk-transition-opaque">
                    
                    <?php if ($lightbox): ?>
                        <div class="uk-position-center">
                            <span uk-icon="icon: search; ratio: 2" class="uk-text-white uk-drop-shadow"></span>
                        </div>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($caption): ?>
                    <div class="uk-margin-small-top">
                        <div class="uk-text-meta uk-text-italic uk-text-small">
                            <?= htmlspecialchars($caption) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($text): ?>
        <div class="<?= $textClassString ?>">
            <div class="uk-text-justify">
                <?= $text ?>
            </div>
        </div>
    <?php endif; ?>
</div>