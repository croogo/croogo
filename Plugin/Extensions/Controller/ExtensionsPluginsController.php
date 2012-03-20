<?php
App::uses('ExtensionsInstaller', 'Extensions.Lib');

/**
 * Extensions Plugins Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsPluginsController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'ExtensionsPlugins';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array(
		'Setting',
		'User',
	);

/**
 * Core plugins
 *
 * @var array
 * @access public
 */
	public $corePlugins = array(
		'Acl',
		'Extensions',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		App::uses('File', 'Utility');
		APP::uses('Folder', 'Utility');
	}

	public function admin_index() {
		$this->set('title_for_layout', __('Plugins'));

		$pluginAliases = $this->Croogo->getPlugins();
		$plugins = array();
		foreach ($pluginAliases AS $pluginAlias) {
			$plugins[$pluginAlias] = $this->Croogo->getPluginData($pluginAlias);
		}
		$this->set('corePlugins', $this->corePlugins);
		$this->set(compact('plugins'));
	}

/**
 * admin_add
 */
	public function admin_add() {
		$this->set('title_for_layout', __('Upload a new plugin'));

		if (!empty($this->request->data)) {
			$file = $this->request->data['Plugin']['file'];
			unset($this->request->data['Plugin']['file']);
			
			$Installer = new ExtensionsInstaller;
			try {
				$Installer->extractPlugin($file['tmp_name']);
			} catch (CakeException $e) {
				$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
				$this->redirect(array('action' => 'add'));
			}
			$this->redirect(array('action' => 'index'));
		}
	}

	public function admin_delete($plugin = null) {
		if (!$plugin) {
			$this->Session->setFlash(__('Invalid plugin'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Croogo->pluginIsActive($plugin)) {
			$this->Session->setFlash(__('You cannot delete a plugin that is currently active.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		$folder =& new Folder;
		if ($folder->delete(APP . 'Plugin' . DS . $plugin)) {
			$this->Session->setFlash(__('Plugin deleted successfully.'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Plugin could not be deleted.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_toggle($plugin = null) {
		if (!$plugin) {
			$this->Session->setFlash(__('Invalid plugin'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		$className = $plugin . 'Activation';
		$configFile = APP . 'Plugin' .DS. $plugin .DS. 'Config' .DS. $className . '.php';
		if (file_exists($configFile) && include $configFile) {
			$pluginActivation = new $className;
		}

		if ($this->Croogo->pluginIsActive($plugin)) {
			if (!isset($pluginActivation) ||
				(isset($pluginActivation) && method_exists($pluginActivation, 'beforeDeactivation') && $pluginActivation->beforeDeactivation($this))) {
				$this->Croogo->removePluginBootstrap($plugin);
				if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
					$pluginActivation->onDeactivation($this);
				}
				$this->Session->setFlash(__('Plugin deactivated successfully.'), 'default', array('class' => 'success'));
			} else {
				$this->Session->setFlash(__('Plugin could not be deactivated. Please, try again.'), 'default', array('class' => 'error'));
			}
		} else {
			if (!isset($pluginActivation) ||
				(isset($pluginActivation) && method_exists($pluginActivation, 'beforeActivation') && $pluginActivation->beforeActivation($this))) {

				$pluginData = $this->Croogo->getPluginData($plugin);
				$dependencies = true;
				if (!empty($pluginData['dependencies']['plugins'])) {
					foreach ($pluginData['dependencies']['plugins'] as $requiredPlugin) {
						$requiredPlugin = ucfirst($requiredPlugin);
						if (!CakePlugin::loaded($requiredPlugin)) {
							$dependencies = false;
							$missingPlugin = $requiredPlugin;
							break;
						}
					}
				}

				if ($dependencies) {
					$this->Croogo->addPluginBootstrap($plugin);
					if (isset($pluginActivation) && method_exists($pluginActivation, 'onActivation')) {
						$pluginActivation->onActivation($this);
					}
					$this->Session->setFlash(__('Plugin activated successfully.'), 'default', array('class' => 'success'));
				} else {
					$this->Session->setFlash(__('Plugin "%s" depends on "%s" plugin.', $plugin, $missingPlugin), 'default', array('class' => 'error'));
				}
			} else {
				$this->Session->setFlash(__('Plugin could not be activated. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		$this->redirect(array('action' => 'index'));
	}

}
