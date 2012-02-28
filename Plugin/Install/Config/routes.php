<?php

$path = '/';
$url = array('plugin' => 'install' ,'controller' => 'install');
if (file_exists(APP . 'Config' . DS.'settings.yml')) {
    if (!Configure::read('Install.secured')) {
        $path = '/*';
        $url['action'] = 'finish';
    }
}
CroogoRouter::connect($path, $url);
