<?php
use Cake\Cache\Cache;
use Cake\Event\EventManager;
use Croogo\Install\Middleware\InstallMiddleware;

Cache::clearAll();
$configs = Cache::configured();
foreach ($configs as $config) {
    Cache::engine($config)->config('duration', 0);
}
Cache::disable();

EventManager::instance()
    ->on('Server.buildMiddleware', function ($event, $middlewareStack) {
        $middlewareStack->add(new InstallMiddleware());
    });
