<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('title'), 'Debug: Data Loading');

// Test data to simulate saved content
$testData = [
    'blocks' => [
        [
            'type' => 'headline',
            'data' => [
                'text' => 'Test Headline mit Level H3',
                'level' => 'h3'
            ]
        ],
        [
            'type' => 'paragraph', 
            'data' => [
                'text' => 'This is a test paragraph that should load correctly.'
            ]
        ]
    ]
];

$savedJson = json_encode($testData);

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <header class="panel-heading">
                <h3 class="panel-title">Debug: Data Loading Test</h3>
            </header>
            <div class="panel-body">
                
                <h4>1. Test: Normal Textarea with pre-filled data</h4>
                <p>This should load the saved content correctly:</p>
                
                <div id="debug-editor-1" class="editorjs-field" style="min-height: 200px; border: 1px solid #ddd; padding: 10px;">
                    <textarea id="debug-data-1" style="display: none;" data-editorjs-data="1"><?= htmlspecialchars($savedJson) ?></textarea>
                </div>
                
                <h4>2. Test: Direct textarea initialization</h4>
                <p>This should also load the saved content:</p>
                
                <textarea data-editorjs-direct="1" id="debug-textarea-2" name="debug_content_2" placeholder="Direct textarea initialization" style="height: 100px; width: 100%;"><?= htmlspecialchars($savedJson) ?></textarea>
                
                <h4>3. Debug Information</h4>
                <div id="debug-info" style="background: #f5f5f5; padding: 10px; margin: 10px 0; font-family: monospace;">
                    <p><strong>Saved JSON Data:</strong></p>
                    <pre><?= htmlspecialchars($savedJson) ?></pre>
                </div>
                
                <h4>4. Debug Controls</h4>
                <button type="button" class="btn btn-info" onclick="debugGetData()">Get Current Editor Data</button>
                <button type="button" class="btn btn-warning" onclick="debugReloadData()">Reload Saved Data</button>
                <button type="button" class="btn btn-success" onclick="debugCheckEditors()">Check Editor Instances</button>
                
                <div id="debug-output" style="background: #eee; padding: 10px; margin: 10px 0; font-family: monospace; white-space: pre-wrap;"></div>
            </div>
        </div>
    </div>
</div>

<script>
function debugGetData() {
    const output = document.getElementById('debug-output');
    output.innerHTML = '';
    
    // Check both editors
    ['debug-editor-1', 'editorjs-debug-textarea-2'].forEach(editorId => {
        const editor = window.editorInstances[editorId];
        if (editor) {
            editor.save().then(data => {
                output.innerHTML += `\n=== ${editorId} ===\n` + JSON.stringify(data, null, 2) + '\n';
            }).catch(err => {
                output.innerHTML += `\nError getting data from ${editorId}: ${err}\n`;
            });
        } else {
            output.innerHTML += `\nNo editor instance found for ${editorId}\n`;
        }
    });
}

function debugReloadData() {
    const savedData = <?= $savedJson ?>;
    const output = document.getElementById('debug-output');
    output.innerHTML = 'Reloading data:\n' + JSON.stringify(savedData, null, 2);
    
    // Try to reload data in first editor
    const editor = window.editorInstances['debug-editor-1'];
    if (editor && editor.render) {
        editor.render(savedData).then(() => {
            output.innerHTML += '\n\nData reloaded successfully!';
        }).catch(err => {
            output.innerHTML += '\n\nError reloading data: ' + err;
        });
    } else {
        output.innerHTML += '\n\nNo editor instance found for reloading';
    }
}

function debugCheckEditors() {
    const output = document.getElementById('debug-output');
    output.innerHTML = 'Editor instances:\n';
    
    for (const [id, editor] of Object.entries(window.editorInstances || {})) {
        output.innerHTML += `- ${id}: ${editor ? 'exists' : 'null'}\n`;
        
        if (editor && editor.configuration) {
            output.innerHTML += `  Holder: ${editor.configuration.holder}\n`;
            output.innerHTML += `  Tools: ${Object.keys(editor.configuration.tools || {}).join(', ')}\n`;
        }
    }
    
    // Check textarea values
    const textarea1 = document.getElementById('debug-data-1');
    const textarea2 = document.getElementById('debug-textarea-2');
    
    output.innerHTML += '\nTextarea values:\n';
    output.innerHTML += `textarea1: ${textarea1 ? textarea1.value.length + ' chars' : 'not found'}\n`;
    output.innerHTML += `textarea2: ${textarea2 ? textarea2.value.length + ' chars' : 'not found'}\n`;
}

// Auto-check after page load
document.addEventListener('rex:ready', function() {
    setTimeout(debugCheckEditors, 2000);
});
</script>
