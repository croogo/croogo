<?php

namespace Croogo\Core\Panel;

use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use DebugKit\DebugPanel;

class ComponentsPanel extends DebugPanel
{

    public $plugin = 'Croogo/Core';

    protected $_loadedComponents = [];

    public function afterFilter(Event $event)
    {
        /* @var ComponentRegistry $componentRegistry */
        if (!isset($event->subject()->View)) {
            return;
        }

        $componentRegistry = $event->subject()->components();

        $loadedComponents = $componentRegistry->loaded();

        foreach ($loadedComponents as $name) {
            $component = $componentRegistry->get($name);
            $this->_loadedComponents[$this->_classNameConvert(get_class($component))] = json_decode(json_encode($component->config()), true);
        }
    }

    public function data()
    {
        return [
            'loaded' => $this->_loadedComponents
        ];
    }

    public function summary()
    {
        return count($this->_loadedComponents);
    }

    public function implementedEvents()
    {
        return [
            'Controller.shutdown' => 'afterFilter'
        ];
    }

    protected function _classNameConvert($className)
    {
        $path = explode('\\', substr($className, 0, -9));
        $pluginPath = array_diff($path, ['Controller', 'Component']);
        $name = array_pop($pluginPath);

        if (($pluginPath === ['Cake']) || ($pluginPath === explode('\\', Configure::read('App.namespace')))) {
            $pluginPath = [];
        }

        return implode('.', pluginSplit($name, true, implode('/', $pluginPath)));
    }
}
