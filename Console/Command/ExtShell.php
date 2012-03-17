<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('CroogoComponent', 'Controller/Component');

/**
 * Ext Shell
 *
 * Activate Plugins/Themes
 *	./Console/croogo ext plugin example
 *	./Console/croogo ext theme example
 *
 * Deactivate & Verbosely Activate
 *	./Console/croogo ext activate plugin example
 *	./Console/croogo ext deactivate plugin example
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
 * Croogo Component
 *
 * @var Component
 * @todo Move component functionality to a Lib/CroogoUtility to avoid this
 */
	public $Croogo = null;

/**
 * PluginActivation class
 *
 * @var object
 */
	protected $_PluginActivation = null;

/**
 * Initialize Croogo Component
 *
 * @param type $stdout
 * @param type $stderr
 * @param type $stdin
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$Collection = new ComponentCollection();
		$this->Croogo = new CroogoComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$Controller = new Controller($CakeRequest, $CakeResponse);
		$Controller->loadModel('Setting');
		$Controller->loadModel('Block');
		$Controller->loadModel('Link');
		$Controller->loadModel('Node');
		$this->Croogo->startup($Controller);
	}

/**
 * Call the appropriate command
 *
 * @return void
 */
	public function main() {
		$activate = ($this->args[0] == 'deactivate') ? $this->args[0] : 'activate';
		if (sizeof($this->args) > 2) {
			$this->args = array_slice($this->args, 1);
		}
		$this->{'_' . $activate . ucfirst($this->args[0])}($this->args[1]);
	}

/**
 * Display help/options
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__d('croogo', 'Croogo Extension Activation'));
		return $parser;
	}

/**
 * Activate a plugin
 *
 * @param string $plugin
 * @return boolean
 *
 * @todo Move this to a Lib
 */
	protected function _activatePlugin($plugin = null) {
		$pluginActivation = $this->_getPluginActivation($plugin);
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
			(isset($pluginActivation) && method_exists($pluginActivation, 'beforeDeactivation') && $pluginActivation->beforeDeactivation($this))) {
			$this->Croogo->removePluginBootstrap($plugin);
			if (isset($pluginActivation) && method_exists($pluginActivation, 'onDeactivation')) {
				$pluginActivation->onDeactivation($this);
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
 * @param string $theme
 * @return boolean
 */
	protected function _activateTheme($theme = null) {
		
	}

/**
 * Deactivate a theme
 *
 * @param string $theme
 * @return boolean
 */
	protected function _deactivateTheme($theme = null) {
		
	}
}