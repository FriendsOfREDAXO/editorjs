<?php
/**
 * Bootstrap TextImage Fragment für EditorJS TextImage Block
 * Rendert Text+Bild/Video-Elemente mit Bootstrap 5 CSS-Klassen
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

// Bootstrap-Klassen bestimmen
$containerClasses = ['row', 'align-items-start'];
$mediaColClasses = ['col-md-4'];
$textColClasses = ['col-md-8'];

// Layout-spezifische Anpassungen
switch ($layout) {
    case 'right':
        $containerClasses[] = 'flex-row-reverse';
        break;
    case 'top':
        $containerClasses = ['row'];
        $mediaColClasses = ['col-12', 'mb-3'];
        $textColClasses = ['col-12'];
        break;
    case 'left':
    default:
        // Standard-Layout
        break;
}

$containerClassString = implode(' ', $containerClasses);
$mediaColClassString = implode(' ', $mediaColClasses);
$textColClassString = implode(' ', $textColClasses);
?>

<div class="<?= $containerClassString ?> mb-4">
    <?php if ($mediaUrl): ?>
        <div class="<?= $mediaColClassString ?>">
            <div class="position-relative">
                <?php if ($mediaType === 'video'): ?>
                    <div class="ratio ratio-16x9">
                        <video class="rounded" 
                               <?= $videoControls ? 'controls' : '' ?>
                               <?= $videoAutoplay ? 'autoplay' : '' ?>
                               <?= $videoMuted ? 'muted' : '' ?>
                               <?= $videoLoop ? 'loop' : '' ?>
                               style="object-fit: cover;">
                            <source src="<?= htmlspecialchars($mediaUrl) ?>" type="<?= $this->getVideoType($mediaFile) ?>">
                            Ihr Browser unterstützt das Video-Element nicht.
                        </video>
                    </div>
                <?php else: ?>
                    <?php if ($lightbox): ?>
                        <a href="<?= htmlspecialchars($mediaUrl) ?>" 
                           data-bs-toggle="modal" 
                           data-bs-target="#lightboxModal" 
                           data-bs-image="<?= htmlspecialchars($mediaUrl) ?>" 
                           data-bs-title="<?= htmlspecialchars($caption ?: $mediaAlt) ?>"
                           class="d-block">
                    <?php endif; ?>
                    
                    <img src="<?= htmlspecialchars($mediaUrl) ?>" 
                         alt="<?= htmlspecialchars($mediaAlt ?: 'Content Image') ?>"
                         class="img-fluid rounded<?= $lightbox ? ' lightbox-trigger' : '' ?>"
                         style="<?= $lightbox ? 'cursor: pointer;' : '' ?>">
                    
                    <?php if ($lightbox): ?>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($caption): ?>
                    <div class="mt-2">
                        <small class="text-muted fst-italic"><?= htmlspecialchars($caption) ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($text): ?>
        <div class="<?= $textColClassString ?>">
            <div class="content">
                <?= $text ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($lightbox && $mediaType === 'image'): ?>
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
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
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