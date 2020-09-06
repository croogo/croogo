<?php

namespace Croogo\Install;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Croogo\Install\Middleware\InstallMiddleware;

class Plugin extends BasePlugin
{

    public function middleware($queue): MiddlewareQueue
    {
        return $queue->add(new InstallMiddleware());
    }
}
