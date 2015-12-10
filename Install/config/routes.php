<?php

use Cake\Routing\Router;

$request = Router::getRequest();
if (strpos($request->url, 'install') === false) {
    $url = ['plugin' => 'install', 'controller' => 'install'];
    Router::redirect('/*', $url, ['status' => 307]);
}
