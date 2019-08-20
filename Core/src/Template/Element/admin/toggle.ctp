<?php
$url = array(
    'prefix' => isset($prefix) ? $prefix : $this->request->getParam('prefix'),
    'plugin' => isset($plugin) ? $plugin : $this->request->getParam('plugin'),
    'controller' => isset($controller) ? $controller : $this->request->getParam('controller'),
    'action' => isset($action) ? $action : 'toggle',
    $id,
    $status,
);
echo $this->Html->status($status, $url);
