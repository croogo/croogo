<?php
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');
App::uses('CroogoPlugin', 'Lib');
App::uses('CroogoTheme', 'Lib');

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
 * PluginActivation class
 *
 * @var object
 */
	protected $_PluginActivation = null;

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
		$this->initialize();
	}

/**
 * Call the appropriate command
 *
 * @return void
 */
	public function main() {
		$this->args = array_map('strtolower', $this->args);
		$activate = $this->args[0];
		$type = $this->args[1];
		$ext = isset($this->args[2]) ? $this->args[2] : null;
		if ($activate == 'deactivate' && $type == 'theme') {
			$this->_deactivateTheme();
		} else {
			$this->{'_' . $activate . ucfirst($type)}($ext);
		}
	}

/**
 * Display help/options
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__d('croogo', 'Activate Plugins & Themes'))
			->addArguments(array(
				'method' => array(
					'help' => __d('croogo', 'Method to perform'),
					'required' => true,
					'choices' => array('activate', 'deactivate'),
				),
				'type' => array(
					'help' => __d('croogo', 'Extension type'),
					'required' => true,
					'choices' => array('plugin', 'theme'),
				),
				'extension' => array(
					'help' => __d('croogo', 'Name of extension'),
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
		$pluginActivation = $this->_getPluginActivation($plugin);
		if (!isset($pluginActivation) ||
			(isset($pluginActivation) && method_exists($pluginActivation, 'beforeActivation') && $pluginActivation->beforeActivation($this->_Controller))) {
			$pluginData = $this->_CroogoPlugin->getPluginData($plugin);
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
				$this->_CroogoPlugin->addPluginBootstrap($plugin);
				if (isset($pluginActivation) && method_exists($pluginActivation, 'onActivation')) {
					$pluginActivation->onActivation($this->_Controller);
				}
				$this->out(__d('croogo', 'Plugin activated successfully.'));
				return true;
			} else {
				$this->err(__d('croogo', 'Plugin "%s" depends on "%s" plugin.', $plugin, $missingPlugin));
			}
		} else {
			$this->err(__d('croogo', 'Plugin could not be activated. Please, try again.'));
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
		$pluginActivation = $this->_getPluginActivation($plugin);
		if (!isset($pluginActivation) ||
			(isset($pluginActivation) && method_exists($pluginActivation, 'beforeDeactivation') && $pluginActivation->beforeDeactivation($this->_Controller))) {
			$this->_CroogoPlugin->removePluginBootstrap($plugin);
			if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
				$pluginActivation->onDeactivation($this->_Controller);
			}
			$this->out(__d('croogo', 'Plugin deactivated successfully.'));
			return true;
		} else {
			$this->err(__d('croogo', 'Plugin could not be deactivated. Please, try again.'));
		}
		return false;
	}

/**
 * Get PluginActivation class
 *
 * @param string $plugin
 * @return object
 */
	protected function _getPluginActivation($plugin = null) {
		$plugin = Inflector::camelize($plugin);
		if (!isset($this->_PluginActivation)) {
			$className = $plugin . 'Activation';
			$configFile = APP . 'Plugin' . DS . $plugin . DS . 'Config' . DS . $className . '.php';
			if (file_exists($configFile) && include $configFile) {
				$this->_PluginActivation = new $className;
			}
		}
		return $this->_PluginActivation;
	}

/**
 * Activate a theme
 *
 * @param string $theme Name of theme
 * @return boolean
 */
	protected function _activateTheme($theme = null) {
		if ($theme == 'default') {
			$theme = null;
		}
		$siteTheme = $this->Setting->findByKey('Site.theme');
		$siteTheme['Setting']['value'] = $theme;
		$this->Setting->save($siteTheme);
		if (is_null($theme)) {
			$this->out(__d('croogo', 'Theme deactivated successfully.'));
		} else {
			$this->out(__d('croogo', 'Theme activated successfully.'));
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