<?php
$mode = $this->getVar('mode', 'output');
$data = $this->getVar('data', []);

// Define config at the top to make it available for all modes within this file scope
$config = [
    'title' => 'Test Headline Fragment',
    'icon' => '<svg width="18" height="18" viewBox="0 0 24 24"><path d="M3 12h18v2H3v-2zm0-6h18v2H3V6zm0 12h18v2H3v-2z"/></svg>',
    'inlineToolbar' => true,
    'fields' => [
        'text' => ['label' => 'Headline Text', 'type' => 'text'],
        'level' => [
            'label' => 'Level', 
            'type' => 'select', 
            'options' => [
                'h2' => 'H2',
                'h3' => 'H3', 
                'h4' => 'H4'
            ],
            'default' => 'h2'
        ]
    ]
];

// Part 1: CONFIG - Sent to the JS editor for setup
if ($mode === 'config') {
    echo json_encode($config);
    return;
}

// Part 2: EDITOR_VIEW - The HTML structure for the editor itself
if ($mode === 'editor_view') {
    // Use the config defined above
    $levelOptions = $config['fields']['level']['options'];
    $defaultLevel = $config['fields']['level']['default'] ?? 'h2';
    ?>
    <div class="editorjs-block-headline">
        <div data-field="text" contenteditable="true" data-placeholder="Enter a headline..."></div>
        <select data-field="level">
            <?php foreach ($levelOptions as $value => $label): ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $value === $defaultLevel ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    return;
}

// Part 3: OUTPUT - The final frontend rendering
$level = $data['level'] ?? 'h2';
// Sanitize the level to prevent arbitrary tag injection
if (!in_array($level, ['h2', 'h3', 'h4'])) {
    $level = 'h2';
}
$text = $data['text'] ?? '';
// Basic sanitization for the text
$text = strip_tags($text, '<br><strong><em><a>');

?>
<<?= $level; ?>><?= $text; ?></<?= $level; ?>>