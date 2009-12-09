<?php
/**
 * ExampleHook Component
 *
 * An example hook component for demonstrating hook system.
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleHookComponent extends Object {
/**
 * Called after activating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onActivate(&$controller) {
        // ACL: set ACOs with permissions
        $controller->Croogo->addAco('Example'); // ExampleController
        $controller->Croogo->addAco('Example/index', array('registered', 'public')); // ExampleController::index()

        // Admin menu: admin_menu element of Example plugin will be shown in admin panel's navigation
        $controller->Croogo->addAdminMenu('Example');

        // Main menu: add an Example link
        $mainMenu = $controller->Link->Menu->findByAlias('main');
        $controller->Link->save(array(
            'menu_id' => $mainMenu['Menu']['id'],
            'title' => 'Example',
            'link' => 'controller:example/action:index',
            'status' => 1,
        ));
    }
/**
 * Called after deactivating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onDeactivate(&$controller) {
        // ACL: remove ACOs with permissions
        $controller->Croogo->removeAco('Example'); // ExampleController ACO and it's actions will be removed

        // Admin menu: remove
        $controller->Croogo->removeAdminMenu('Example');

        // Main menu: delete Example link
        $link = $controller->Link->find('first', array(
            'conditions' => array(
                'Menu.alias' => 'main',
                'Link.link' => 'controller:example/action:index',
            ),
        ));
        if (isset($link['Link']['id'])) {
            $controller->Link->delete($link['Link']['id']);
        }
    }
/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param object $controller Controller with components to startup
 * @return void
 */
    function startup(&$controller) {
        $controller->set('exampleHookStartup', 'ExampleHook startup');
    }
/**
 * Called after the Controller::beforeRender(), after the view class is loaded, and before the
 * Controller::render()
 *
 * @param object $controller Controller with components to beforeRender
 * @return void
 */
    function beforeRender(&$controller) {
        $controller->set('exampleHookBeforeRender', 'ExampleHook beforeRender');
    }
/**
 * Called after Controller::render() and before the output is printed to the browser.
 *
 * @param object $controller Controller with components to shutdown
 * @return void
 */
    function shutdown(&$controller) {
    }
    
}
?>