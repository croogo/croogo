<?php

namespace Croogo\Wysiwyg;

use Cake\Core\Configure;
use Croogo\Core\Croogo;

class Wysiwyg {

    /**
     * Get an array of wysiwyg enabled actions
     */
    public static function getActions()
    {
        $results = [];
        $actions = Configure::read('Wysiwyg.actions');
        foreach ($actions as $key => $config) {
            $action = base64_decode($key);
            $results[$action] = $config;
        }
        return $results;
    }

    /**
     * Set a list of wysiwyg enabled actions
     */
    public static function setActions(array $config)
    {
        return Croogo::mergeConfig('Wysiwyg.actions', $config, true);
    }

}
