<?php
use Cake\Event\EventManager;
use Croogo\Install\Middleware\InstallMiddleware;

EventManager::instance()
    ->on('Server.buildMiddleware', function ($event, $middlewareStack) {
        $middlewareStack->add(new InstallMiddleware());
    });
