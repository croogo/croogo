<?php
declare(strict_types=1);

namespace Croogo\Core\Panel;

use Cake\Core\Plugin;
use DebugKit\DebugPanel;

class PluginsPanel extends DebugPanel
{

    public $plugin = 'Croogo/Core';

    public function data()
    {
        return [
            'loaded' => Plugin::loaded()
        ];
    }

    public function summary()
    {
        return count(Plugin::loaded());
    }
}
