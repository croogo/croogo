<?php
/**
 * Routes
 *
 * example_routes.php will be loaded in main app/config/routes.php file.
 */
namespace Croogo\Example\Config;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Croogo\Core\Croogo;
use Croogo\Core\Nav as CroogoNav;
use Croogo\Wysiwyg\Wysiwyg;

/**
 * Behavior
 *
 * This plugin's Example behavior will be attached whenever Node model is loaded.
 */
Croogo::hookBehavior('Croogo/Nodes.Nodes', 'Croogo/Example.Example', []);

/**
 * Component
 *
 * This plugin's Example component will be loaded in ALL controllers.
 */
Croogo::hookComponent('*', 'Croogo/Example.Example');

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
                'plugin' => 'Croogo/Example',
                'controller' => 'Example',
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
                'prefix' => 'admin',
                'plugin' => 'Croogo/Example',
                'controller' => 'Example',
                'action' => 'chooser',
            ],
        ],
        'example4' => [
            'title' => 'RTE Example',
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Croogo/Example',
                'controller' => 'Example',
                'action' => 'rte_example',
            ],
        ],
    ],
]);

Wysiwyg::setActions([
    'Croogo/Example.Admin/Example/rteExample' => [
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
            'language' => Configure::read('Site.locale'),
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
Croogo::hookAdminRowAction('Croogo/Nodes.Admin/Nodes/index', 'Example', 'prefix:admin/plugin:Croogo%2fExample/controller:Example/action:index/:id');

/* Row action with link options */
Croogo::hookAdminRowAction('Croogo/Nodes.Admin/Nodes/index', 'Button with Icon', [
    'plugin:Croogo%2fExample/controller:Example/action:index/:id' => [
        'options' => [
            'icon' => 'key',
            'button' => 'success',
        ],
    ],
]);

/* Row action with icon */
Croogo::hookAdminRowAction('Croogo/Nodes.Admin/Nodes/index', 'Icon Only', [
    'plugin:Croogo%2fExample/controller:Example/action:index/:id' => [
        'title' => false,
        'options' => [
            'icon' => 'picture-o',
            'tooltip' => [
                'data-title' => 'A nice and simple action with tooltip',
                'data-placement' => 'left',
            ],
        ],
    ],
]);

/* Row action with confirm message */
Croogo::hookAdminRowAction('Croogo/Nodes.Admin/Nodes/index', 'Reload Page', [
    'prefix:admin/plugin:Croogo%2fNodes/controller:Nodes/action:index' => [
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
Croogo::hookAdminTab('Admin/Nodes/add', 'Example', 'Croogo/Example.admin_tab_node');
Croogo::hookAdminTab('Admin/Nodes/edit', 'Example', 'Croogo/Example.admin_tab_node');
