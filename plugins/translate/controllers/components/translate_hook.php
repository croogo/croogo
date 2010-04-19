<?php
/**
 * TranslateHook Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateHookComponent extends Object {
/**
 * Models to be translated
 *
 * @var array
 * @access public
 */
    public $translateModels = array(
        'Node' => array(
            'title' => 'titleTranslation',
            'excerpt' => 'excerptTranslation',
            'body' => 'bodyTranslation',
        ),
        'Link' => array(
            'title' => 'titleTranslation',
        ),
        'Block' => array(
            'title' => 'titleTranslation',
            'body' => 'bodyTranslation',
        ),
    );
/**
 * Called after activating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onActivate(&$controller) {
        $controller->Croogo->addAco('Translate');
        $controller->Croogo->addAco('Translate/admin_index');
        $controller->Croogo->addAco('Translate/admin_edit');
        $controller->Croogo->addAco('Translate/admin_delete');
    }
/**
 * Called after deactivating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    public function onDeactivate(&$controller) {
        $controller->Croogo->removeAco('Translate');
    }
/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param object $controller Controller with components to startup
 * @return void
 */
    public function startup(&$controller) {
        foreach ($this->translateModels AS $translateModel => $fields) {
            if (isset($controller->{$translateModel})) {
                $controller->{$translateModel}->Behaviors->attach('CroogoTranslate', $fields);
            }
        }
    }
/**
 * Called after the Controller::beforeRender(), after the view class is loaded, and before the
 * Controller::render()
 *
 * @param object $controller Controller with components to beforeRender
 * @return void
 */
    public function beforeRender(&$controller) {
        $modelAliases = array_keys($this->translateModels);
        $singularCamelizedControllerName = Inflector::camelize(Inflector::singularize($controller->params['controller']));
        if (in_array($singularCamelizedControllerName, $modelAliases)) {
            Configure::write('Admin.rowActions.Translations', 'plugin:translate/controller:translate/action:index/:id/'.$singularCamelizedControllerName);
        }
    }
    
}
?>