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
    public function onActivate(&$controller) {
        // ACL: set ACOs with permissions
        $controller->Croogo->addAco('Example'); // ExampleController
        $controller->Croogo->addAco('Example/admin_index'); // ExampleController::admin_index()
        $controller->Croogo->addAco('Example/index', array('registered', 'public')); // ExampleController::index()

        // Bootstrap: app/plugins/example/config/bootstrap.php will be loaded in app/config/bootstrap.php
        $controller->Croogo->addPluginBootstrap('example');

        // Routes: app/plugins/example/config/routes.php will be loaded in app/config/routes.php
        $controller->Croogo->addPluginRoutes('example');

        // Main menu: add an Example link
        $mainMenu = $controller->Link->Menu->findByAlias('main');
        $controller->Link->Behaviors->attach('Tree', array(
            'scope' => array(
                'Link.menu_id' => $mainMenu['Menu']['id'],
            ),
        ));
        $controller->Link->save(array(
            'menu_id' => $mainMenu['Menu']['id'],
            'title' => 'Example',
            'link' => 'plugin:example/controller:example/action:index',
            'status' => 1,
        ));
    }
/**
 * Called after deactivating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onDeactivate(&$controller) {
        // ACL: remove ACOs with permissions
        $controller->Croogo->removeAco('Example'); // ExampleController ACO and it's actions will be removed

        // Bootstrap: remove
        $controller->Croogo->removePluginBootstrap('example');

        // Routes: remove
        $controller->Croogo->removePluginRoutes('example');

        // Main menu: delete Example link
        $link = $controller->Link->find('first', array(
            'conditions' => array(
                'Menu.alias' => 'main',
                'Link.link' => 'plugin:example/controller:example/action:index',
            ),
        ));
        $controller->Link->Behaviors->attach('Tree', array(
            'scope' => array(
                'Link.menu_id' => $link['Link']['menu_id'],
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
    public function startup(&$controller) {
        $controller->set('exampleHookStartup', 'ExampleHook startup');
    }
/**
 * Called after the Controller::beforeRender(), after the view class is loaded, and before the
 * Controller::render()
 *
 * @param object $controller Controller with components to beforeRender
 * @return void
 */
    public function beforeRender(&$controller) {
        // Admin menu: admin_menu element of Example plugin will be shown in admin panel's navigation
        Configure::write('Admin.menus.example', 1);

        // Row actions (links beside Edit, Delete actions)
        //$modelAlias = Inflector::camelize(Inflector::singularize($controller->params['controller']));
        //Configure::write('Admin.rowActions.Example', 'plugin:example/controller:example/action:index/model:'.$modelAlias.'/:id');
        Configure::write('Admin.rowActions.Example', 'plugin:example/controller:example/action:index/:id');

        // set variables
        $controller->set('exampleHookBeforeRender', 'ExampleHook beforeRender');
    }
/**
 * Called after Controller::render() and before the output is printed to the browser.
 *
 * @param object $controller Controller with components to shutdown
 * @return void
 */
    public function shutdown(&$controller) {
    }
    
}
?>