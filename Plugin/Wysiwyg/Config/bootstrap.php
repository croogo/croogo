<?php

/**
 * Configuration
 */
Configure::write('Wysiwyg.actions', array(
	'Nodes/admin_add' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
	'Nodes/admin_edit' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
	'Translate/admin_edit' => array(
		array(
			'elements' => 'NodeBody',
		),
	),
));

Croogo::hookHelper('*', 'Wysiwyg.Wysiwyg');