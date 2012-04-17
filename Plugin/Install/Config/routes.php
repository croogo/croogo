<?php

$path = '/';
$url = array('plugin' => 'install', 'controller' => 'install');
if (file_exists(APP . 'Config' . DS . 'settings.yml')) {
	$request = Router::getRequest();
	if (!Configure::read('Install.secured') &&
		!is_null($request) &&
		strpos($request->url, 'finish') == false
		)
	{
		$path = '/*';
		$url['action'] = 'adminuser';
	}
}
CroogoRouter::connect($path, $url);
