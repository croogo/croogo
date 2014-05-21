<?php

namespace Croogo\Shops\Config;
$config = array(
	'EventHandlers' => array(
		'Shops.ShopsNodesEventHandler',
		'Shops.ShopsEventHandler' => array(
			'options' => array(
				'priority' => 1,
			),
		),
	),
);
