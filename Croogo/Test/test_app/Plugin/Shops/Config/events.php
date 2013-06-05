<?php

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
