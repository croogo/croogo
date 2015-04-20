<?php

use Cake\Core\Configure;

$pluginPaths = Configure::read('plugins');
$pluginPaths['Croogo/Croogo'] = $pluginPaths['Croogo'] . 'Croogo' . DS;

Configure::write('plugins', $pluginPaths);
