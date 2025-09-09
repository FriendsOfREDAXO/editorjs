<?php
/**
 * Bootstrap Card Fragment für EditorJS Card Block
 * Rendert Card-Elemente mit Bootstrap 5 CSS-Klassen
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

// Bootstrap-Klassen bestimmen
$cardClasses = ['card', 'h-100'];
$cardBodyClasses = ['card-body'];

// Layout-spezifische Klassen
if ($layout === 'horizontal') {
    $cardClasses[] = 'card-horizontal';
} elseif ($layout === 'grid') {
    // Grid wird vom übergeordneten Container gehandhabt
}

// Aspect Ratio Klassen
if ($aspectRatio !== 'auto') {
    $aspectRatioClass = 'ratio-' . str_replace(':', 'x', $aspectRatio);
} else {
    $aspectRatioClass = '';
}

$cardClassString = implode(' ', $cardClasses);
$cardBodyClassString = implode(' ', $cardBodyClasses);

// Content vorbereiten
ob_start();
?>

<div class="<?= $cardClassString ?>">
    <?php if ($mediaUrl): ?>
        <?php if ($layout === 'horizontal'): ?>
            <div class="row g-0">
                <div class="col-md-4">
        <?php endif; ?>
        
        <?php if ($aspectRatio !== 'auto'): ?>
            <div class="ratio <?= $aspectRatioClass ?>">
        <?php endif; ?>
        
        <?php if ($mediaType === 'video'): ?>
            <video class="card-img-top<?= $layout === 'horizontal' ? ' h-100' : '' ?>" 
                   <?= $videoControls ? 'controls' : '' ?>
                   <?= $videoAutoplay ? 'autoplay' : '' ?>
                   <?= $videoMuted ? 'muted' : '' ?>
                   <?= $videoLoop ? 'loop' : '' ?>
                   style="object-fit: cover;">
                <source src="<?= htmlspecialchars($mediaUrl) ?>" type="<?= $this->getVideoType($mediaFile) ?>">
                Ihr Browser unterstützt das Video-Element nicht.
            </video>
        <?php else: ?>
            <?php if ($lightbox && !$linkUrl): ?>
                <a href="<?= htmlspecialchars($mediaUrl) ?>" data-bs-toggle="modal" data-bs-target="#lightboxModal" 
                   data-bs-image="<?= htmlspecialchars($mediaUrl) ?>" 
                   data-bs-title="<?= htmlspecialchars($title ?: $mediaAlt) ?>">
            <?php endif; ?>
            
            <img src="<?= htmlspecialchars($mediaUrl) ?>" 
                 alt="<?= htmlspecialchars($mediaAlt ?: $title ?: 'Card Image') ?>"
                 class="card-img-top<?= $layout === 'horizontal' ? ' h-100' : '' ?>"
                 style="object-fit: cover;">
            
            <?php if ($lightbox && !$linkUrl): ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($aspectRatio !== 'auto'): ?>
            </div>
        <?php endif; ?>
        
        <?php if ($layout === 'horizontal'): ?>
                </div>
                <div class="col-md-8">
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($title || $text): ?>
        <div class="<?= $cardBodyClassString ?>">
            <?php if ($title): ?>
                <h5 class="card-title"><?= htmlspecialchars($title) ?></h5>
            <?php endif; ?>
            
            <?php if ($text): ?>
                <div class="card-text">
                    <?= $text ?>
                </div>
            <?php endif; ?>
            
            <?php if ($linkUrl): ?>
                <a href="<?= htmlspecialchars($linkUrl) ?>" 
                   target="<?= htmlspecialchars($linkTarget) ?>" 
                   class="btn btn-primary stretched-link">
                    Weiterlesen
                </a>
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

// Wenn Link vorhanden ist und kein Lightbox, Card als ganzes verlinken (außer bei stretched-link)
if ($linkUrl && !$lightbox && !strpos($cardContent, 'stretched-link')) {
    echo '<a href="' . htmlspecialchars($linkUrl) . '" target="' . htmlspecialchars($linkTarget) . '" class="text-decoration-none">';
    echo $cardContent;
    echo '</a>';
} else {
    echo $cardContent;
}
?>

<?php if ($lightbox && !$linkUrl): ?>
<!-- Bootstrap Lightbox Modal (nur einmal pro Seite einfügen) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox Modal erstellen falls nicht vorhanden
    if (!document.getElementById('lightboxModal')) {
        const modalHTML = `
            <div class="modal fade" id="lightboxModal" tabindex="-1" aria-labelledby="lightboxModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body p-0 text-center">
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-index-1050" 
                                    data-bs-dismiss="modal" aria-label="Close" style="z-index: 1050;"></button>
                            <img src="" alt="" class="img-fluid rounded" style="max-height: 90vh;">
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Event Listener für Lightbox-Links
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-bs-target="#lightboxModal"]');
            if (trigger) {
                const modal = document.getElementById('lightboxModal');
                const img = modal.querySelector('img');
                const imageSrc = trigger.getAttribute('data-bs-image');
                const imageTitle = trigger.getAttribute('data-bs-title');
                
                img.src = imageSrc;
                img.alt = imageTitle || '';
            }
        });
    }
});
</script>
<?php endif; ?>