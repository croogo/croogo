<?php
$status = $status === true ? 1 : 0;
$url = array(
	'admin' => isset($admin) ? $admin : true,
	'plugin' => isset($plugin) ? $plugin : $this->request->params['plugin'],
	'controller' => isset($controller) ? $controller : $this->request->params['controller'],
	'action' => isset($action) ? $action : 'toggle',
	$id,
	$status,
);
echo $this->Html->status($status, $url);
