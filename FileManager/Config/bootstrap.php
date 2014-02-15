<?php

Configure::write('Wysiwyg.attachmentBrowseUrl', array(
	'plugin' => 'file_manager',
	'controller' => 'attachments',
	'action' => 'browse',
));

Croogo::mergeConfig('Wysiwyg.actions', array(
	'Attachments/admin_browse',
));
