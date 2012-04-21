<?php

$request = Router::getRequest();
if (strpos($request->url, 'install') === false) {
	$url = array('plugin' => 'install' ,'controller' => 'install');
	Router::redirect('/*', $url, array('status' => 307));
}
