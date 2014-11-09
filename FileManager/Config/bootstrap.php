<?php

Configure::write('Wysiwyg.attachmentBrowseUrl', array(
	'plugin' => 'file_manager',
	'controller' => 'attachments',
	'action' => 'browse',
));

Configure::write('FileManager.editablePaths', array(
	APP,
));

Configure::write('FileManager.deletablePaths', array(
	APP . 'View' . DS . 'Themed' . DS,
	WWW_ROOT,
));

Croogo::mergeConfig('Wysiwyg.actions', array(
	'Attachments/admin_browse',
));
