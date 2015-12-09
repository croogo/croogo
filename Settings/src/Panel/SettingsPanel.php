<?php

namespace Croogo\Settings\Panel;

use Cake\Core\Configure;
use DebugKit\DebugPanel;

class SettingsPanel extends DebugPanel
{

    public $plugin = 'Croogo/Settings';

    public function data()
    {
        return [
            'settings' => Configure::read()
        ];
    }
}
