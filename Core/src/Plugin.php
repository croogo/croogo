<?php

namespace Croogo\Core;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Utility\Security;

use function Croogo\Core\timerStart;
use function Croogo\Core\timerStop;

class Plugin extends BasePlugin
{

    public function bootstrap(PluginApplicationInterface $app)
    {
        parent::bootstrap($app);

        \Croogo\Core\timerStart('Croogo bootstrap');
        PluginManager::setup($app);
        PluginManager::croogoBootstrap($app);
        \Croogo\Core\timerStop('Croogo bootstrap');

        // Load Install plugin
        $salted = Security::getSalt() !== '__SALT__';
        if (!Configure::read('Croogo.installed') || !$salted) {
            $app->addPlugin('Croogo/Install', ['routes' => true, 'bootstrap' => true]);
        }
    }

}
