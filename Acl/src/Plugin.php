<?php
declare(strict_types=1);

namespace Croogo\Acl;

use Cake\Core\BasePlugin;
use Cake\Http\MiddlewareQueue;
use Croogo\Acl\Http\Middleware\SessionMiddleware;

class Plugin extends BasePlugin
{

    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue->add(new SessionMiddleware());
    }

}
