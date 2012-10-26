<?php
App::uses('ExtensionsInstaller', 'Extensions.Lib');
App::uses('CroogoPlugin', 'Extensions.Lib');

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
class ExtensionsPluginsController extends ExtensionsAppController {

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
		'Settings.Setting',
		'Users.User',
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

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->_CroogoPlugin = new CroogoPlugin();
		$this->_CroogoPlugin->setController($this);
	}

/**
 * admin_index
 *
 * @return void
 */
	public function admin_index() {
		$this->set('title_for_layout', __('Plugins'));

		$pluginAliases = $this->_CroogoPlugin->getPlugins();
		$plugins = array();
		foreach ($pluginAliases as $pluginAlias) {
			$plugins[$pluginAlias] = $this->_CroogoPlugin->getData($pluginAlias);
		}
		$this->set('corePlugins', $this->corePlugins);
		$this->set(compact('plugins'));
	}

/**
 * admin_add
 *
 * @return void
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

/**
 * admin_delete
 *
 * @param string $plugin
 * @return void
 */
	public function admin_delete($plugin = null) {
		if (!$plugin) {
			$this->Session->setFlash(__('Invalid plugin'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->_CroogoPlugin->isActive($plugin)) {
			$this->Session->setFlash(__('You cannot delete a plugin that is currently active.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		$result = $this->_CroogoPlugin->delete($plugin);
		if ($result === true) {
			$this->Session->setFlash(__('Plugin "%s" deleted successfully.', $plugin), 'default', array('class' => 'success'));
		} elseif (!empty($result[0])) {
			$this->Session->setFlash($result[0], 'default', array('class' => 'error'));
		} else {
			$this->Session->setFlash(__('Plugin could not be deleted.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_toggle
 *
 * @param string $plugin
 * @return void
 */
	public function admin_toggle($plugin = null) {
		if (!$plugin) {
			$this->Session->setFlash(__('Invalid plugin'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($this->_CroogoPlugin->isActive($plugin)) {
			$result = $this->_CroogoPlugin->deactivate($plugin);
			if ($result === true) {
				$this->Session->setFlash(__('Plugin "%s" deactivated successfully.', $plugin), 'default', array('class' => 'success'));
			} elseif (is_string($result)) {
				$this->Session->setFlash($result, 'default', array('class' => 'error'));
			} else {
				$this->Session->setFlash(__('Plugin could not be deactivated. Please, try again.'), 'default', array('class' => 'error'));
			}
		} else {
			$result = $this->_CroogoPlugin->activate($plugin);
			if ($result === true) {
				$this->Session->setFlash(__('Plugin "%s" activated successfully.', $plugin), 'default', array('class' => 'success'));
			} elseif (is_string($result)) {
				$this->Session->setFlash($result, 'default', array('class' => 'error'));
			} else {
				$this->Session->setFlash(__('Plugin could not be activated. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		$this->redirect(array('action' => 'index'));
	}

/**
 * Migrate a plugin (database)
 *
 * @param type $plugin
 */
	public function admin_migrate($plugin = null) {
		if (!$plugin) {
			$this->Session->setFlash(__('Invalid plugin'), 'default', array('class' => 'error'));
		} elseif ($this->_CroogoPlugin->migrate($plugin)) {
			$this->Session->setFlash(__('Plugin "%s" migrated successfully.', $plugin), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(
				__('Plugin "%s" could not be migrated. Error: %s', $plugin, implode('<br />', $this->_CroogoPlugin->migrationErrors)),
				'default',
				array('class' => 'success')
			);
		}
		$this->redirect(array('action' => 'index'));
	}
}
