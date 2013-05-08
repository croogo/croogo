<?php
/**
 * Translate Activation
 *
 * @package  Croogo.Translate
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateActivation {

/**
 * onActivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
	public function beforeActivation(&$controller) {
		return true;
	}

/**
 * Called after activating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
	public function onActivation(&$controller) {
		$controller->Croogo->addAco('Translate/Translate/admin_index');
		$controller->Croogo->addAco('Translate/Translate/admin_edit');
		$controller->Croogo->addAco('Translate/Translate/admin_delete');
		App::uses('CroogoPlugin', 'Extensions.Lib');
		$CroogoPlugin = new CroogoPlugin();
		$CroogoPlugin->migrate('Translate');
	}

/**
 * onDeactivate will be called if this returns true
 *
 * @param  object $controller Controller
 * @return boolean
 */
	public function beforeDeactivation(&$controller) {
		return true;
	}

/**
 * Called after deactivating the plugin in ExtensionsPluginsController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
	public function onDeactivation(&$controller) {
		$controller->Croogo->removeAco('Translate');
	}
}
