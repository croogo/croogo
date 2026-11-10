<?php

namespace Croogo\Install;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Croogo\Install\Middleware\InstallMiddleware;

class InstallPlugin extends BasePlugin
{

    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue
    {
        return $middleware->add(new InstallMiddleware());
    }
}
