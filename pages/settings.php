<?php
/**
 * EditorJS Settings Page
 * Framework-Auswahl und weitere Konfigurationen
 */

// Nur für Admins
if (!rex::getUser() || !rex::getUser()->isAdmin()) {
    throw new rex_exception('Keine Berechtigung!');
}

$addon = rex_addon::get('editorjs');
$message = '';

// Framework-Optionen
$frameworks = [
    'bootstrap' => 'Bootstrap 5',
    'uikit' => 'UIKit 3',
    'custom' => 'Eigene CSS-Klassen'
];

// Standardwerte
$currentFramework = $addon->getConfig('framework', 'bootstrap');
$enableLightbox = $addon->getConfig('enable_lightbox', true);
$enableLazyLoading = $addon->getConfig('enable_lazy_loading', true);
$customCssClasses = $addon->getConfig('custom_css_classes', []);

// Formular verarbeiten
if (rex_post('save', 'boolean')) {
    $framework = rex_post('framework', 'string', 'bootstrap');
    $lightbox = rex_post('enable_lightbox', 'boolean', false);
    $lazyLoading = rex_post('enable_lazy_loading', 'boolean', false);
    
    // Custom CSS Klassen
    $customClasses = [];
    $customClasses['card'] = rex_post('css_card', 'string', '');
    $customClasses['textimage'] = rex_post('css_textimage', 'string', '');
    $customClasses['gallery'] = rex_post('css_gallery', 'string', '');
    $customClasses['downloads'] = rex_post('css_downloads', 'string', '');
    
    // Konfiguration speichern
    $addon->setConfig('framework', $framework);
    $addon->setConfig('enable_lightbox', $lightbox);
    $addon->setConfig('enable_lazy_loading', $lazyLoading);
    $addon->setConfig('custom_css_classes', $customClasses);
    
    $message = rex_view::success('Einstellungen wurden gespeichert.');
    
    // Werte aktualisieren
    $currentFramework = $framework;
    $enableLightbox = $lightbox;
    $enableLazyLoading = $lazyLoading;
    $customCssClasses = $customClasses;
}

echo rex_view::title('EditorJS Einstellungen');

if ($message) {
    echo $message;
}

$content = '';

// Framework-Auswahl
$formElements = [];

$n = [];
$n['label'] = '<label for="framework">Frontend-Framework</label>';
$n['field'] = '<select class="form-control" id="framework" name="framework">';
foreach ($frameworks as $key => $label) {
    $selected = $key === $currentFramework ? ' selected="selected"' : '';
    $n['field'] .= '<option value="' . $key . '"' . $selected . '>' . $label . '</option>';
}
$n['field'] .= '</select>';
$n['note'] = 'Wählen Sie das Frontend-Framework für die Ausgabe der EditorJS-Blöcke.';
$formElements[] = $n;

// Lightbox aktivieren
$n = [];
$n['label'] = '<label for="enable_lightbox">Lightbox aktivieren</label>';
$checked = $enableLightbox ? ' checked="checked"' : '';
$n['field'] = '<input type="checkbox" id="enable_lightbox" name="enable_lightbox" value="1"' . $checked . '> Lightbox für Bilder und Galerien aktivieren';
$n['note'] = 'Aktiviert die Lightbox-Funktionalität für Bilder in Card- und TextImage-Blöcken sowie Galerien.';
$formElements[] = $n;

// Lazy Loading aktivieren
$n = [];
$n['label'] = '<label for="enable_lazy_loading">Lazy Loading aktivieren</label>';
$checked = $enableLazyLoading ? ' checked="checked"' : '';
$n['field'] = '<input type="checkbox" id="enable_lazy_loading" name="enable_lazy_loading" value="1"' . $checked . '> Lazy Loading für Bilder aktivieren';
$n['note'] = 'Lädt Bilder erst beim Scrollen nach, um die Seitenladezeit zu verbessern.';
$formElements[] = $n;

// Custom CSS Klassen (nur anzeigen wenn "custom" ausgewählt)
$customStyle = $currentFramework === 'custom' ? '' : ' style="display: none;"';

$n = [];
$n['label'] = '<label>Eigene CSS-Klassen</label>';
$n['field'] = '<div id="custom-css-section"' . $customStyle . '>
    <div class="form-group">
        <label for="css_card">Card-Block CSS-Klassen:</label>
        <input type="text" class="form-control" id="css_card" name="css_card" 
               value="' . htmlspecialchars($customCssClasses['card'] ?? '') . '" 
               placeholder="z.B. card custom-card">
    </div>
    <div class="form-group">
        <label for="css_textimage">TextImage-Block CSS-Klassen:</label>
        <input type="text" class="form-control" id="css_textimage" name="css_textimage" 
               value="' . htmlspecialchars($customCssClasses['textimage'] ?? '') . '" 
               placeholder="z.B. textimage-block row">
    </div>
    <div class="form-group">
        <label for="css_gallery">Gallery-Block CSS-Klassen:</label>
        <input type="text" class="form-control" id="css_gallery" name="css_gallery" 
               value="' . htmlspecialchars($customCssClasses['gallery'] ?? '') . '" 
               placeholder="z.B. gallery-grid row">
    </div>
    <div class="form-group">
        <label for="css_downloads">Downloads-Block CSS-Klassen:</label>
        <input type="text" class="form-control" id="css_downloads" name="css_downloads" 
               value="' . htmlspecialchars($customCssClasses['downloads'] ?? '') . '" 
               placeholder="z.B. downloads-list">
    </div>
</div>';
$n['note'] = 'Definieren Sie eigene CSS-Klassen für die verschiedenen EditorJS-Blöcke.';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

// Formular
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="1">Einstellungen speichern</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', 'Framework-Einstellungen', false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">';
echo $content;
echo '</form>';

// JavaScript für Custom CSS Anzeige
?>
<script>
jQuery(document).ready(function($) {
    $('#framework').change(function() {
        var framework = $(this).val();
        if (framework === 'custom') {
            $('#custom-css-section').show();
        } else {
            $('#custom-css-section').hide();
        }
    });
});
</script>

<?php
// Informationen zu den Frameworks
$info = '<h3>Framework-Informationen</h3>';

$info .= '<h4>Bootstrap 5</h4>';
$info .= '<p>Verwendet Bootstrap 5 CSS-Klassen für responsive und moderne Layouts. Lightbox wird über Bootstrap Modals realisiert.</p>';
$info .= '<ul>';
$info .= '<li><strong>Card-Block:</strong> Verwendet Bootstrap Card-Komponenten</li>';
$info .= '<li><strong>TextImage-Block:</strong> Nutzt Bootstrap Grid-System</li>';
$info .= '<li><strong>Gallery-Block:</strong> Responsive Grid mit Bootstrap Modal-Lightbox</li>';
$info .= '<li><strong>Downloads-Block:</strong> Bootstrap List-Group oder Card-Layout</li>';
$info .= '</ul>';

$info .= '<h4>UIKit 3</h4>';
$info .= '<p>Nutzt UIKit 3 CSS-Framework für ein modernes und flexibles Design. Lightbox wird über UIKit Lightbox-Komponente realisiert.</p>';
$info .= '<ul>';
$info .= '<li><strong>Card-Block:</strong> UIKit Card-Komponenten mit Overlays</li>';
$info .= '<li><strong>TextImage-Block:</strong> UIKit Grid-System mit Transitions</li>';
$info .= '<li><strong>Gallery-Block:</strong> UIKit Grid mit integrierter Lightbox</li>';
$info .= '<li><strong>Downloads-Block:</strong> UIKit List oder Card-Layout</li>';
$info .= '</ul>';

$info .= '<h4>Eigene CSS-Klassen</h4>';
$info .= '<p>Ermöglicht die Verwendung eigener CSS-Klassen für maximale Flexibilität. Sie müssen das entsprechende CSS selbst bereitstellen.</p>';

$fragment = new rex_fragment();
$fragment->setVar('title', 'Framework-Übersicht', false);
$fragment->setVar('body', $info, false);
echo $fragment->parse('core/page/section.php');

// Code-Beispiele
$examples = '<h3>Verwendung im Template</h3>';

$examples .= '<h4>Mit Fragment-Renderer (empfohlen)</h4>';
$examples .= '<pre><code class="language-php">&lt;?php
// EditorJS-Inhalt mit Fragment-Renderer ausgeben
use FriendsOfRedaxo\EditorJs\EditorJSFragmentRenderer;

$editorjs_content = rex_article::getCurrent()->getValue(\'editorjs_content\');

if ($editorjs_content) {
    $framework = rex_addon::get(\'editorjs\')->getConfig(\'framework\', \'bootstrap\');
    $renderer = new EditorJSFragmentRenderer($framework, [
        \'lightbox\' => rex_addon::get(\'editorjs\')->getConfig(\'enable_lightbox\', true),
        \'lazyLoading\' => rex_addon::get(\'editorjs\')->getConfig(\'enable_lazy_loading\', true)
    ]);
    
    echo $renderer->render($editorjs_content);
}
?&gt;</code></pre>';

$examples .= '<h4>Mit Standard-Renderer</h4>';
$examples .= '<pre><code class="language-php">&lt;?php
// Fallback mit Standard-Renderer
use FriendsOfRedaxo\EditorJs\EditorJSRenderer;

$editorjs_content = rex_article::getCurrent()->getValue(\'editorjs_content\');

if ($editorjs_content) {
    $renderer = new EditorJSRenderer();
    echo $renderer->render($editorjs_content);
}
?&gt;</code></pre>';

$examples .= '<h4>CSS und JavaScript einbinden</h4>';
$examples .= '<pre><code class="language-php">&lt;?php
// Im Template-Header (vor &lt;/head&gt;)
use FriendsOfRedaxo\EditorJs\EditorJSFragmentRenderer;

$framework = rex_addon::get(\'editorjs\')->getConfig(\'framework\', \'bootstrap\');
EditorJSFragmentRenderer::addFrameworkCSS($framework);
EditorJSFragmentRenderer::addFrameworkJS($framework);
?&gt;</code></pre>';

$fragment = new rex_fragment();
$fragment->setVar('title', 'Code-Beispiele', false);
$fragment->setVar('body', $examples, false);
echo $fragment->parse('core/page/section.php');
?>