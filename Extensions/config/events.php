<?php

namespace Croogo\Extensions\Config;
$config = array(
	'EventHandlers' => array(
		'Croogo/Extensions.ExtensionsEventHandler' => array(
			'options' => array(
				'priority' => 5,
			),
		),
		'Croogo/Extensions.HookableComponentEventHandler' => array(
			'options' => array(
				'priority' => 5,
			),
		),
	),
);
