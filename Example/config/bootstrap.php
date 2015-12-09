<?php
/**
 * Routes
 *
 * example_routes.php will be loaded in main app/config/routes.php file.
 */
namespace Croogo\Example\Config;

Croogo::hookRoutes('Example');

/**
 * Behavior
 *
 * This plugin's Example behavior will be attached whenever Node model is loaded.
 */
Croogo::hookBehavior('Node', 'Example.Example', []);

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
CroogoNav::add('sidebar', 'extensions.children.example', [
    'title' => 'Example',
    'url' => '#',
    'children' => [
        'example1' => [
            'title' => 'Example 1',
            'url' => [
                'admin' => true,
                'plugin' => 'example',
                'controller' => 'example',
                'action' => 'index',
            ],
        ],
        'example2' => [
            'title' => 'Example 2 with a title that won\'t fit in the sidebar',
            'url' => '#',
            'children' => [
                'example-2-1' => [
                    'title' => 'Example 2-1',
                    'url' => '#',
                    'children' => [
                        'example-2-1-1' => [
                            'title' => 'Example 2-1-1',
                            'url' => '#',
                            'children' => [
                                'example-2-1-1-1' => [
                                    'title' => 'Example 2-1-1-1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'example3' => [
            'title' => 'Chooser Example',
            'url' => [
                'admin' => true,
                'plugin' => 'example',
                'controller' => 'example',
                'action' => 'chooser',
            ],
        ],
        'example4' => [
            'title' => 'RTE Example',
            'url' => [
                'admin' => true,
                'plugin' => 'example',
                'controller' => 'example',
                'action' => 'rte_example',
            ],
        ],
    ],
]);

$Localization = new L10n();
Croogo::mergeConfig('Wysiwyg.actions', [
    'Example/admin_rte_example' => [
        [
            'elements' => 'ExampleBasic',
            'preset' => 'basic',
        ],
        [
            'elements' => 'ExampleStandard',
            'preset' => 'standard',
            'language' => 'ja',
        ],
        [
            'elements' => 'ExampleFull',
            'preset' => 'full',
            'language' => $Localization->map(Configure::read('Site.locale')),
        ],
        [
            'elements' => 'ExampleCustom',
            'toolbar' => [
                ['Format', 'Bold', 'Italic'],
                ['Copy', 'Paste'],
            ],
            'uiColor' => '#ffe79a',
            'language' => 'fr',
        ],
    ],
]);

/**
 * Admin row action
 *
 * When browsing the content list in admin panel (Content > List),
 * an extra link called 'Example' will be placed under 'Actions' column.
 */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');

/* Row action with link options */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Button with Icon', [
    'plugin:example/controller:example/action:index/:id' => [
        'options' => [
            'icon' => 'key',
            'button' => 'success',
        ],
    ],
]);

/* Row action with icon */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Icon Only', [
    'plugin:example/controller:example/action:index/:id' => [
        'title' => false,
        'options' => [
            'icon' => 'picture',
            'tooltip' => [
                'data-title' => 'A nice and simple action with tooltip',
                'data-placement' => 'left',
            ],
        ],
    ],
]);

/* Row action with confirm message */
Croogo::hookAdminRowAction('Nodes/admin_index', 'Reload Page', [
    'admin:true/plugin:nodes/controller:nodes/action:index' => [
        'title' => false,
        'options' => [
            'icon' => 'refresh',
            'tooltip' => 'Reload page',
        ],
        'confirmMessage' => 'Reload this page?',
    ],
]);

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
