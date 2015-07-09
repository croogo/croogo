<?php

use Cake\Core\Configure;
use Croogo\Core\Croogo;

Configure::write('Wysiwyg.attachmentBrowseUrl', array(
	'prefix' => 'admin',
	'plugin' => 'Croogo/FileManager',
	'controller' => 'Attachments',
	'action' => 'browse',
));

Croogo::mergeConfig('Wysiwyg.actions', array(
	'Attachments/admin_browse',
));

Configure::write('FileManager', array(
	'editablePaths' => array(
		APP,
	),
	'deletablePaths' => array(
		APP . 'View' . DS . 'Themed' . DS,
		WWW_ROOT,
	),
));
