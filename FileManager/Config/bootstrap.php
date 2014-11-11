<?php

Configure::write('Wysiwyg.attachmentBrowseUrl', array(
	'plugin' => 'file_manager',
	'controller' => 'attachments',
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
