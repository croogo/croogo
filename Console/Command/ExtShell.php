<?php
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('CroogoTheme', 'Extensions.Lib');

/**
 * Ext Shell
 *
 * Activate/Deactivate Plugins/Themes
 *	./Console/croogo ext activate plugin Example
 *	./Console/croogo ext activate theme minimal
 *	./Console/croogo ext deactivate plugin Example
 *	./Console/croogo ext deactivate theme
 *
 * @category Shell
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtShell extends AppShell {

/**
 * Models we use
 *
 * @var array
 */
	public $uses = array('Setting');

/**
 * CroogoPlugin class
 *
 * @var CroogoPlugin
 */
	protected $_CroogoPlugin = null;

/**
 * CroogoTheme class
 *
 * @var CroogoTheme
 */
	protected $_CroogoTheme = null;

/**
 * Controller
 *
 * @var Controller
 * @todo Remove this when PluginActivation dont need controllers
 */
	protected $_Controller = null;

/**
 * Initialize
 *
 * @param type $stdout
 * @param type $stderr
 * @param type $stdin
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->_CroogoPlugin = new CroogoPlugin();
		$this->_CroogoTheme = new CroogoTheme();
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->_Controller = new AppController($CakeRequest, $CakeResponse);
		$this->_Controller->constructClasses();
		$this->_Controller->startupProcess();
		$this->_CroogoPlugin->setController($this->_Controller);
		$this->initialize();
	}

/**
 * Call the appropriate command
 *
 * @return void
 */
	public function main() {
		$args = $this->args;
		$this->args = array_map('strtolower', $this->args);
		$activate = $this->args[0];
		$type = $this->args[1];
		$ext = isset($args[2]) ? $args[2] : null;
		if ($type == 'theme') {
			if ($activate == 'deactivate') {
				$this->_deactivateTheme();
				return true;
			}
			$extensions = $this->_CroogoTheme->getThemes();
		} elseif ($type == 'plugin') {
			$extensions = $this->_CroogoPlugin->getPlugins();
		}
		if (!in_array($ext, $extensions)) {
			$this->err(__('%s "%s" not found.', ucfirst($type), $ext));
			return false;
		}
		return $this->{'_' . $activate . ucfirst($type)}($ext);
	}

/**
 * Display help/options
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__('Activate Plugins & Themes'))
			->addArguments(array(
				'method' => array(
					'help' => __('Method to perform'),
					'required' => true,
					'choices' => array('activate', 'deactivate'),
				),
				'type' => array(
					'help' => __('Extension type'),
					'required' => true,
					'choices' => array('plugin', 'theme'),
				),
				'extension' => array(
					'help' => __('Name of extension'),
				),
			));
	}

/**
 * Activate a plugin
 *
 * @param string $plugin
 * @return boolean
 */
	protected function _activatePlugin($plugin = null) {
		$result = $this->_CroogoPlugin->activate($plugin);
		if ($result === true) {
			$this->out(__('Plugin "%s" activated successfully.', $plugin));
			return true;
		} elseif (is_string($result)) {
			$this->err($result);
		} else {
			$this->err(__('Plugin "%s" could not be activated. Please, try again.', $plugin));
		}
		return false;
	}

/**
 * Deactivate a plugin
 *
 * @param string $plugin
 * @return boolean
 */
	protected function _deactivatePlugin($plugin = null) {
		$result = $this->_CroogoPlugin->deactivate($plugin);
		if ($result === true) {
			$this->out(__('Plugin "%s" deactivated successfully.', $plugin));
			return true;
		} elseif (is_string($result)) {
			$this->err($result);
		} else {
			$this->err(__('Plugin "%s" could not be deactivated. Please, try again.', $plugin));
		}
		return false;
	}

/**
 * Activate a theme
 *
 * @param string $theme Name of theme
 * @return boolean
 */
	protected function _activateTheme($theme = null) {
		if ($r = $this->_CroogoTheme->activate($theme)) {
			if (is_null($theme)) {
				$this->out(__('Theme deactivated successfully.'));
			} else {
				$this->out(__('Theme "%s" activated successfully.', $theme));
			}
		} else {
			$this->err(__('Theme "%s" activation failed.', $theme));
		}
		return true;
	}

/**
 * Deactivate a theme (just reverts to default)
 *
 * @return boolean
 */
	protected function _deactivateTheme() {
		return $this->_activateTheme();
	}
}