<?php

$pluginManifest = array(
	'name'=> 'Example',
	'description'=> 'Example plugin for demonstrating hook system',

	'author'=> 'Author Name',
	'authorEmail'=> 'author@example.com',
	'authorUrl'=> 'http://example.com',

	'dependencies'=> array(
		'plugins'=> array(
			'acl',
			'extensions',
			),
		),
	);