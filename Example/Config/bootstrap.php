<?php
/**
 * Routes
 *
 * example_routes.php will be loaded in main app/config/routes.php file.
 */
Croogo::hookRoutes('Example');

/**
 * Behavior
 *
 * This plugin's Example behavior will be attached whenever Node model is loaded.
 */
Croogo::hookBehavior('Node', 'Example.Example', array());

/**
 * Component
 *
 * This plugin's Example component will be loaded in ALL controllers.
 */
Croogo::hookComponent('*', 'Example.Example');

/**
 * Helper
 *
 * This plugin's Example helper will be loaded via NodesController.
 */
Croogo::hookHelper('Nodes', 'Example.Example');

/**
 * Admin menu (navigation)
 */
CroogoNav::add('extensions.children.example', array(
	'title' => 'Example',
	'url' => '#',
	'children' => array(
		'example1' => array(
			'title' => 'Example 1',
			'url' => array(
				'admin' => true,
				'plugin' => 'example',
				'controller' => 'example',
				'action' => 'index',
			),
		),
		'example2' => array(
			'title' => 'Example 2 with a title that won\'t fit in the sidebar',
			'url' => '#',
			'children' => array(
				'example-2-1' => array(
					'title' => 'Example 2-1',
					'url' => '#',
					'children' => array(
						'example-2-1-1' => array(
							'title' => 'Example 2-1-1',
							'url' => '#',
							'children' => array(
								'example-2-1-1-1' => array(
									'title' => 'Example 2-1-1-1',
								),
							),
						),
					),
				),
			),
		),
		'example3' => array(
			'title' => 'Chooser Example',
			'url' => array(
				'admin' => true,
				'plugin' => 'example',
				'controller' => 'example',
				'action' => 'chooser',
			),
		),
		'example4' => array(
			'title' => 'RTE Example',
			'url' => array(
				'admin' => true,
				'plugin' => 'example',
				'controller' => 'example',
				'action' => 'rte_example',
			),
		),
	),
));

$Localization = new L10n();
Croogo::mergeConfig('Wysiwyg.actions', array(
	'Example/admin_rte_example' => array(
		array(
			'elements' => 'ExampleBasic',
			'preset' => 'basic',
		),
		array(
			'elements' => 'ExampleStandard',
			'preset' => 'standard',
			'language' => 'ja',
		),
		array(
			'elements' => 'ExampleFull',
			'preset' => 'full',
			'language' => $Localization->map(Configure::read('Site.locale')),
		),
		array(
			'elements' => 'ExampleCustom',
			'toolbar' => array(
				array('Format', 'Bold', 'Italic'),
				array('Copy', 'Paste'),
			),
			'uiColor' => '#ffe79a',
			'language' => 'fr',
		),
	),
));

/**
 * Admin row action
 *
 * When browsing the content list in admin panel (Content > List),
 * an extra link called 'Example' will be placed under 'Actions' column.
 */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');

/* Row action with link options */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Button with Icon', array(
	'plugin:example/controller:example/action:index/:id' => array(
		'options' => array(
			'icon' => 'key',
			'button' => 'success',
		),
	),
));

/* Row action with icon */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Icon Only', array(
	'plugin:example/controller:example/action:index/:id' => array(
		'title' => false,
		'options' => array(
			'icon' => 'picture',
			'tooltip' => array(
				'data-title' => 'A nice and simple action with tooltip',
				'data-placement' => 'left',
			),
		),
	),
));

/**
 * Admin tab
 *
 * When adding/editing Content (Nodes),
 * an extra tab with title 'Example' will be shown with markup generated from the plugin's admin_tab_node element.
 *
 * Useful for adding form extra form fields if necessary.
 */
Croogo::hookAdminTab('Nodes/admin_add', 'Example', 'example.admin_tab_node');
Croogo::hookAdminTab('Nodes/admin_edit', 'Example', 'example.admin_tab_node');
