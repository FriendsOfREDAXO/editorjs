<?php
class rex_api_editorjs extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        $call = rex_get('call', 'string', '');

        if ($call === 'get_block_configs') {
            $this->getBlockConfigs();
        } else {
            rex_response::sendJson(['error' => 'Invalid API call'], 400);
        }
    }

    protected function getBlockConfigs()
    {
        $blockPath = rex_path::addon('editorjs', 'fragments/');
        $files = glob($blockPath . '*.fragment.php');
        $configs = [];

        foreach ($files as $file) {
            $fragmentFileName = basename($file);
            $blockName = basename($file, '.fragment.php');

            $configJson = $this->renderFragment($fragmentFileName, 'config');
            $editorViewHtml = $this->renderFragment($fragmentFileName, 'editor_view');

            $config = json_decode($configJson, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $configs[$blockName] = [
                    'config' => $config,
                    'editor_view' => $editorViewHtml,
                ];
            }
        }

        rex_response::cleanOutputBuffers();
        rex_response::sendJson($configs);
        exit;
    }

    protected function renderFragment($fragmentFileName, $mode)
    {
        ob_start();
        $fragment = new rex_fragment();
        $fragment->setVar('mode', $mode, false);
        echo $fragment->parse($fragmentFileName);
        return ob_get_clean();
    }
}
