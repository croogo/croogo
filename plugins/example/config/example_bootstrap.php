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
 * This plugin's ExampleHook behavior will be attached whenever Node model is loaded.
 */
    Croogo::hookBehavior('Node', 'Example.ExampleHook', array());
/**
 * Component
 *
 * This plugin's ExampleHook component will be loaded from AppController
 */
    Croogo::hookComponent('Example.ExampleHook');
/**
 * Helper
 *
 * This plugin's ExampleHook helper will be loaded inside LayoutHelper,
 * and the extra callbacks supported for hook helpers by Croogo will be called.
 */
    Croogo::hookHelper('Example.ExampleHook');
/**
 * Admin menu (navigation)
 *
 * This plugin's admin_menu element will be rendered in admin panel under Extensions menu.
 */
    Croogo::hookAdminMenu('Example');
/**
 * Admin row action
 *
 * When browsing the content list in admin panel (Content > List),
 * an extra link called 'Example' will be placed under 'Actions' column.
 */
    Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');
?>