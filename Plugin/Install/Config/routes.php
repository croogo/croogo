<?php

if (!Configure::read('Install.installed') || !Configure::read('Install.secured')) {
	$request = Router::getRequest();
	if (strpos($request->url, 'install') === false) {
		$url = array('plugin' => 'install' ,'controller' => 'install');
		Router::redirect('/*', $url, array('status' => 307));
	}
}
