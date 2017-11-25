<?php
$url = array(
    'prefix' => isset($prefix) ? $prefix : $this->request->params['prefix'],
    'plugin' => isset($plugin) ? $plugin : $this->request->params['plugin'],
    'controller' => isset($controller) ? $controller : $this->request->params['controller'],
    'action' => isset($action) ? $action : 'toggle',
    $id,
    $status,
);
echo $this->Html->status($status, $url);
