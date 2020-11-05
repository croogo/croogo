<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $action
 * @var mixed $controller
 * @var mixed $id
 * @var mixed $plugin
 * @var mixed $prefix
 * @var mixed $status
 */
$url = [
    'prefix' => isset($prefix) ? $prefix : $this->getRequest()->getParam('prefix'),
    'plugin' => isset($plugin) ? $plugin : $this->getRequest()->getParam('plugin'),
    'controller' => isset($controller) ? $controller : $this->getRequest()->getParam('controller'),
    'action' => isset($action) ? $action : 'toggle',
    $id,
    $status,
];
echo $this->Html->status($status, $url);
