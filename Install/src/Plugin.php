<?php

namespace Croogo\Install;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Croogo\Install\Middleware\InstallMiddleware;

class Plugin extends BasePlugin
{

    public function middleware($queue)
    {
        return $queue->add(new InstallMiddleware());
    }
}
