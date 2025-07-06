<?php
/**
 * Frontend-Template für Hero Block
 * Dieses Template zeigt, wie der Hero Block im Frontend gerendert werden kann
 */

// Hero Block Daten aus EditorJS
$heroData = $block['data'];

// Standardwerte setzen
$title = $heroData['title'] ?? 'Willkommen';
$subtitle = $heroData['subtitle'] ?? '';
$text = $heroData['text'] ?? '';
$backgroundImage = $heroData['backgroundImage'] ?? '';
$primaryButton = $heroData['primaryButton'] ?? ['text' => '', 'url' => ''];
$secondaryButton = $heroData['secondaryButton'] ?? ['text' => '', 'url' => ''];
$height = $heroData['height'] ?? 'medium';
$overlay = $heroData['overlay'] ?? 'dark';
$alignment = $heroData['alignment'] ?? 'center';

// CSS-Klassen generieren
$containerClasses = [
    'hero-container',
    'hero-container--' . $height,
    'hero-container--' . $alignment
];

$overlayClasses = [
    'hero-overlay',
    'hero-overlay--' . $overlay
];
?>

<section class="hero-section" id="hero-<?= uniqid() ?>">
    <div class="<?= implode(' ', $containerClasses) ?>">
        
        <?php if ($backgroundImage): ?>
        <div class="hero-background">
            <img src="<?= rex_url::media($backgroundImage) ?>" 
                 alt="<?= rex_escape($title) ?>" 
                 class="hero-background-image"
                 loading="lazy">
        </div>
        <?php endif; ?>
        
        <?php if ($overlay !== 'none'): ?>
        <div class="<?= implode(' ', $overlayClasses) ?>"></div>
        <?php endif; ?>
        
        <div class="hero-content">
            <?php if ($title): ?>
            <h1 class="hero-title">
                <?= $title ?>
            </h1>
            <?php endif; ?>
            
            <?php if ($subtitle): ?>
            <h2 class="hero-subtitle">
                <?= $subtitle ?>
            </h2>
            <?php endif; ?>
            
            <?php if ($text): ?>
            <div class="hero-text">
                <?= $text ?>
            </div>
            <?php endif; ?>
            
            <?php if ($primaryButton['text'] || $secondaryButton['text']): ?>
            <div class="hero-actions">
                <?php if ($primaryButton['text']): ?>
                <a href="<?= rex_escape($primaryButton['url']) ?>" 
                   class="hero-button hero-button--primary"
                   <?= strpos($primaryButton['url'], 'http') === 0 ? 'target="_blank" rel="noopener"' : '' ?>>
                    <?= rex_escape($primaryButton['text']) ?>
                </a>
                <?php endif; ?>
                
                <?php if ($secondaryButton['text']): ?>
                <a href="<?= rex_escape($secondaryButton['url']) ?>" 
                   class="hero-button hero-button--secondary"
                   <?= strpos($secondaryButton['url'], 'http') === 0 ? 'target="_blank" rel="noopener"' : '' ?>>
                    <?= rex_escape($secondaryButton['text']) ?>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Frontend Hero Styles - In separates CSS auslagern */
.hero-section {
    margin: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
    position: relative;
}

.hero-container {
    position: relative;
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow: hidden;
}

/* Höhen-Varianten */
.hero-container--small { min-height: 300px; }
.hero-container--medium { min-height: 400px; }
.hero-container--large { min-height: 500px; }
.hero-container--fullscreen { min-height: 100vh; }

/* Hintergrundbild */
.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-background-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hero-section:hover .hero-background-image {
    transform: scale(1.05);
}

/* Overlay */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
}

.hero-overlay--light { background: rgba(255, 255, 255, 0.3); }
.hero-overlay--dark { background: rgba(0, 0, 0, 0.4); }
.hero-overlay--gradient {
    background: linear-gradient(45deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.1) 50%, rgba(0, 0, 0, 0.3) 100%);
}

/* Content */
.hero-content {
    position: relative;
    z-index: 3;
    max-width: 800px;
    padding: 60px 40px;
    color: white;
    width: 100%;
}

/* Ausrichtung */
.hero-container--left .hero-content { text-align: left; }
.hero-container--center .hero-content { text-align: center; }
.hero-container--right .hero-content { text-align: right; }

/* Typography */
.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin: 0 0 1rem 0;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 0.8s ease-out;
}

.hero-subtitle {
    font-size: 1.5rem;
    font-weight: 400;
    line-height: 1.3;
    margin: 0 0 1.5rem 0;
    color: rgba(255, 255, 255, 0.9);
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.hero-text {
    font-size: 1.125rem;
    line-height: 1.6;
    margin: 0 0 2.5rem 0;
    color: rgba(255, 255, 255, 0.85);
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

/* Actions */
.hero-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    animation: fadeInUp 0.8s ease-out 0.6s both;
}

.hero-container--left .hero-actions { justify-content: flex-start; }
.hero-container--right .hero-actions { justify-content: flex-end; }

/* Buttons */
.hero-button {
    display: inline-flex;
    align-items: center;
    padding: 14px 28px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 50px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.hero-button--primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.hero-button--primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.hero-button--secondary {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.hero-button--secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
}

/* Animationen */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .hero-content { padding: 40px 20px; }
    .hero-title { font-size: 2.5rem; }
    .hero-subtitle { font-size: 1.25rem; }
    .hero-text { font-size: 1rem; }
    .hero-actions { 
        flex-direction: column; 
        gap: 0.75rem; 
    }
    .hero-button { 
        width: 100%; 
        max-width: 280px; 
        justify-content: center; 
    }
}

@media (max-width: 480px) {
    .hero-container--small { min-height: 250px; }
    .hero-container--medium { min-height: 300px; }
    .hero-container--large { min-height: 400px; }
    .hero-title { font-size: 2rem; }
    .hero-subtitle { font-size: 1.125rem; }
}
</style>
