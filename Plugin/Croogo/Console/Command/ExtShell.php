<?php

App::uses('AppShell', 'Console/Command');
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
 * @package  Croogo.Croogo.Console.Command
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
	public $uses = array('Settings.Setting');

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
		$method = $this->args[0];
		$type = $this->args[1];
		$ext = isset($args[2]) ? $args[2] : null;
		$force = isset($this->params['force']) ? $this->params['force'] : false;
		if ($type == 'theme') {
			$extensions = $this->_CroogoTheme->getThemes();
			$theme = Configure::read('Site.theme');
			$active = !empty($theme) ? $theme == 'default' : true;
		} elseif ($type == 'plugin') {
			$extensions = $this->_CroogoPlugin->getPlugins();
			if ($force) {
				$plugins = array_combine($p = App::objects('plugins'), $p);
				$extensions += $plugins;
			}
			$active = CakePlugin::loaded($ext);
		}
		if ($type == 'theme' && $method == 'deactivate') {
			$this->err(__d('croogo', 'Theme cannot be deactivated, instead activate another theme.'));
			return false;
		}
		if (!empty($ext) && !in_array($ext, $extensions) && !$active && !$force) {
			$this->err(__d('croogo', '%s "%s" not found.', ucfirst($type), $ext));
			return false;
		}
		switch ($method) {
			case 'list':
				$call = Inflector::pluralize($type);
				return $this->{$call}($ext);
			default:
				if (empty($ext)) {
					$this->err(__d('croogo', '%s name must be provided.', ucfirst($type)));
					return false;
				}
				return $this->{'_' . $method . ucfirst($type)}($ext);
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
					'choices' => array('list', 'activate', 'deactivate'),
				),
				'type' => array(
					'help' => __d('croogo', 'Extension type'),
					'required' => true,
					'choices' => array('plugin', 'theme'),
				),
				'extension' => array(
					'help' => __d('croogo', 'Name of extension'),
				),
			))
			->addOption('all', array(
				'short' => 'a',
				'boolean' => true,
				'help' => 'List all extensions',
			))
			->addOption('force', array(
				'short' => 'f',
				'boolean' => true,
				'help' => 'Force method operation even when plugin does not provide a `plugin.json` file.'
			));
	}

/**
 * Activate a plugin
 *
 * @param string $plugin
 * @return boolean
 */
	protected function _activatePlugin($plugin) {
		$result = $this->_CroogoPlugin->activate($plugin);
		if ($result === true) {
			$this->out(__d('croogo', 'Plugin "%s" activated successfully.', $plugin));
			return true;
		} elseif (is_string($result)) {
			$this->err($result);
		} else {
			$this->err(__d('croogo', 'Plugin "%s" could not be activated. Please, try again.', $plugin));
		}
		return false;
	}

/**
 * Deactivate a plugin
 *
 * @param string $plugin
 * @return boolean
 */
	protected function _deactivatePlugin($plugin) {
		$result = $this->_CroogoPlugin->deactivate($plugin);
		if ($result === true) {
			$this->out(__d('croogo', 'Plugin "%s" deactivated successfully.', $plugin));
			return true;
		} elseif (is_string($result)) {
			$this->err($result);
		} else {
			$this->err(__d('croogo', 'Plugin "%s" could not be deactivated. Please, try again.', $plugin));
		}
		if ($result !== true && isset($this->params['force'])) {
			$this->_CroogoPlugin->removeBootstrap($plugin);
		}
		return false;
	}

/**
 * Activate a theme
 *
 * @param string $theme Name of theme
 * @return boolean
 */
	protected function _activateTheme($theme) {
		if ($this->_CroogoTheme->activate($theme)) {
			$this->out(__d('croogo', 'Theme "%s" activated successfully.', $theme));
		} else {
			$this->err(__d('croogo', 'Theme "%s" activation failed.', $theme));
		}
		return true;
	}

/**
 * List plugins
 */
	public function plugins($plugin = null) {
		App::uses('CroogoPlugin', 'Extensions.Lib');
		$all = $this->params['all'];
		$plugins = $plugin == null ? App::objects('plugins') : array($plugin);
		$loaded = CakePlugin::loaded();
		$CroogoPlugin = new CroogoPlugin();
		$this->out(__d('croogo', 'Plugins:'), 2);
		$this->out(__d('croogo', '%-20s%-50s%s', __d('croogo', 'Plugin'), __d('croogo', 'Author'), __d('croogo', 'Status')));
		$this->out(str_repeat('-', 80));
		foreach ($plugins as $plugin) {
			$status = '<info>inactive</info>';
			if ($active = in_array($plugin, $loaded)) {
				$status = '<success>active</success>';
			}
			if (!$active && !$all) {
				continue;
			}
			$data = $CroogoPlugin->getPluginData($plugin);
			$author = isset($data['author']) ? $data['author'] : '';
			$this->out(__d('croogo', '%-20s%-50s%s', $plugin, $author, $status));
		}
	}

/**
 * List themes
 */
	public function themes($theme = null) {
		$CroogoTheme = new CroogoTheme();
		$all = $this->params['all'];
		$current = Configure::read('Site.theme');
		$themes = $theme == null ? $CroogoTheme->getThemes() : array($theme);
		$this->out("Themes:", 2);
		$default = empty($current) || $current == 'default';
		$this->out(__d('croogo', '%-20s%-50s%s', __d('croogo', 'Theme'), __d('croogo', 'Author'), __d('croogo', 'Status')));
		$this->out(str_repeat('-', 80));
		foreach ($themes as $theme) {
			$active = $theme == $current || $default && $theme == 'default';
			$status = $active ? '<success>active</success>' : '<info>inactive</info>';
			if (!$active && !$all) {
				continue;
			}
			$data = $CroogoTheme->getThemeData($theme);
			$author = isset($data['author']) ? $data['author'] : '';
			$this->out(__d('croogo', '%-20s%-50s%s', $theme, $author, $status));
		}
	}

}