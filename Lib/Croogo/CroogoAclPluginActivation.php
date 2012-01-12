<?php
/**
 * CroogoAclPluginActivation
 *
 * Acl plugin activation/deactivation
 *
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAclPluginActivation extends Object {

	public function beforeActivation($controller) {
		return true;
	}

	public function onActivation($controller) {
		$Setting = ClassRegistry::init('Setting');
		$setting = $Setting->find('first', array(
			'conditions' => array(
				'key' => 'Site.acl_plugin',
				),
			));

		if (empty($setting)) {
			$setting = $Setting->create(array(
				'key' => 'Site.acl_plugin',
				'value' => Configure::read('Site.acl_plugin'),
				'title' => 'Acl Plugin',
				'description' => 'CamelCased Acl Plugin name to use. Make sure that plugin name is correct and active.',
				'editable' => 1,
				'weight' => 5,
				));
			$Setting->save($setting);
		}
	}

	public function beforeDeactivation($controller) {
		if (!empty($controller->params['pass'][0])) {
			$plugin = $controller->params['pass'][0];
		} else {
			return false;
		}
		if (Configure::read('Site.acl_plugin') == $plugin) {
			// plugin in use
			return false;
		}
		$loaded = CakePlugin::loaded();
		$leftovers = array_diff($loaded, array('Acl'));
		foreach ($leftovers as $plugin) {
			// only allow deactivation when alternate Acl plugin is active
			if (preg_match('/^Acl/', $plugin)) {
				return true;
			}
		}
		return false;
	}

	public function onDeactivation($controller) {
	}

}
