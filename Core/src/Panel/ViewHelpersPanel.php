<?php

namespace Croogo\Core\Panel;

use Cake\Event\Event;
use Cake\View\HelperRegistry;
use DebugKit\DebugPanel;

class ViewHelpersPanel extends DebugPanel
{

    public $plugin = 'Croogo/Core';

    protected $_loadedViewHelpers = [];

    public function afterFilter(Event $event)
    {
        /* @var HelperRegistry $helperRegistry */
        if (!isset($event->getSubject()->View)) {
            return;
        }

        $helperRegistry = $event->getSubject()->View->helpers();

        $viewHelperNames = $helperRegistry->loaded();

        foreach ($viewHelperNames as $name) {
            $this->_loadedViewHelpers[$name] = $helperRegistry->get($name)->config();
        }
    }

    public function data()
    {
        return [
            'loaded' => $this->_loadedViewHelpers
        ];
    }

    public function summary()
    {
        return count($this->_loadedViewHelpers);
    }

    public function implementedEvents()
    {
        return [
            'Controller.shutdown' => 'afterFilter'
        ];
    }
}
