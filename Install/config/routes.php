<?php

use Cake\Routing\Router;

$request = Router::getRequest();
if ($request && strpos($request->url, 'install') === false) {
    $url = ['plugin' => 'Croogo/Install', 'controller' => 'Install'];
    Router::redirect('/*', $url, ['status' => 307]);
}
