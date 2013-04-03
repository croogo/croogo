<?php

/**
 * UpgradeTask
 *
 * @package  Console.Command.Task
 * @since    1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UpgradeTask extends AppShell {

/**
 * maps 1.4 controllers to the current plugin
 */
	protected $_controllerMap = array(
		'attachments' => 'file_manager',
		'filemanager' => 'file_manager',
		'contacts' => 'contacts',
		'messages' => 'contacts',
		'terms' => 'taxonomy',
		'vocabularies' => 'taxonomy',
		'types' => 'taxonomy',
		'comments' => 'comments',
		'acl_actions' => 'acl',
		'acl_permissions' => 'acl',
		'roles' => 'users',
		'users' => 'users',
		'nodes' => 'nodes',
		'regions' => 'blocks',
		'blocks' => 'blocks',
		'languages' => 'settings',
		'settings' => 'settings',
		'menus' => 'menus',
		'links' => 'menus',
	);

/**
 * Setting instance
 */
	public $Setting = null;

/**
 * Load Settings plugin and model
 */
	protected function _loadSettingsPlugin() {
		if (!CakePlugin::loaded('Settings')) {
			CakePlugin::load('Settings');
		}
		if (!$this->Setting) {
			$this->Setting = ClassRegistry::init('Settings.Setting');
		}
	}

/**
 * getOptionParser
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->addSubCommand('acl', array(
				'help' => __d('croogo', 'Upgrade ACL database for core controllers.'),
				'parser' => array(
					'description' => __d('croogo',
	'Upgrades the ACO hierarchy from 1.3/1.4 so it follows the default ' .
	'behavior in normal CakePHP applications. The primary difference is that ' .
	'plugin controllers now are stored underneath its own Plugin ACO record, ' .
	'whereas previous version assumes all Controllers belongs to the root ' .
	'\'controllers\' node.' . $this->nl(2) . '<warning>' .
	'Ensure that you have a backup of your aros, acos, and aros_acos table ' .
	'before upgrading.</warning>'
					),
				),
			))
			->addSubCommand('settings', array(
				'help' => __d('croogo', 'Create settings.json from database'),
			))
			->addSubCommand('bootstraps', array(
				'help' => __d('croogo', 'Update Hook.bootstrap settings'),
			))
			->addSubCommand('links', array(
				'help' => __d('croogo', 'Update Links in database'),
			))
			->addSubCommand('all', array(
				'help' => __d('croogo', 'Run all upgrade tasks'),
			));
	}

/**
 * convert settings.yml to settings.json
 */
	public function settings($keys = array()) {
		$this->_loadSettingsPlugin();
		if (file_exists(APP . 'Config' . DS . 'settings.json')) {
			$this->err(__d('croogo', '<warning>Config/settings.json already exist</warning>'));
		} else {
			$defaultPlugins = array(
				'Settings', 'Comments', 'Contacts', 'Nodes', 'Meta', 'Menus',
				'Users', 'Blocks', 'Taxonomy', 'FileManager', 'Ckeditor',
			);
			$Setting = $this->Setting;
			$setting = $Setting->findByKey('Hook.bootstraps');
			$plugins = explode(',', $setting['Setting']['value']);
			if (is_array($plugins)) {
				foreach ($plugins as $plugin) {
					if (!in_array($plugin, $defaultPlugins)) {
						$defaultPlugins[] = $plugin;
					}
				}
			}
			$Setting->write('Hook.bootstraps', join(',', $defaultPlugins));
			if ($version = file_get_contents(APP . 'VERSION.txt')) {
				$Setting->write('Croogo.version', $version);
			}
			$Setting->write('Access Control.multiRole', 0, array(
				'title' => 'Enable Multiple Roles',
				'input_type' => 'checkbox',
				'editable' => true,
			));
			$Setting->write('Access Control.rowLevel', 0, array(
				'title' => 'Row Level Access Control',
				'input_type' => 'checkbox',
				'editable' => true,
			));
			$Setting->write('Site.autoLoginDuration', '+1 week', array(
				'title' => '"Remember Me" Cookie Lifetime',
				'description' => 'Eg: +1 day, +1 week',
				'input_type' => 'text',
				'editable' => true,
			));
			$this->out(__d('croogo', '<success>Config/settings.yml created based on `settings` table</success>'));
		}
	}

/**
 * Upgrade ACL database
 */
	public function acl() {
		App::uses('AclUpgrade', 'Acl.Lib');
		if (!CakePlugin::loaded('Acl') || !class_exists('AclUpgrade')) {
			$this->err('AclUpgrade class not found or Acl plugin not loaded');
			$this->_stop();
		}
		$Upgrade = new AclUpgrade();
		if (($result = $Upgrade->upgrade()) !== true) {
			$this->err($result);
		} else {
			$this->out('<success>ACL Upgrade completed successfully</success>');
		}
	}

	public function links() {
		if (!CakePlugin::loaded('Menus')) {
			CakePlugin::load('Menus');
		}
		App::uses('View', 'View');
		App::uses('AppHelper', 'View/Helper');
		App::uses('MenusHelper', 'Menus.View/Helper');
		$Menus = new MenusHelper(new View());
		$Link = ClassRegistry::init('Menus.Link');
		$links = $Link->find('all', array('fields' => array('id', 'title', 'link')));

		$count = 0;
		foreach ($links as $link) {
			if (!strstr($link['Link']['link'], 'controller:')) {
				continue;
			}
			if (strstr($link['Link']['link'], 'plugin:')) {
				continue;
			}
			$url = $Menus->linkStringToArray($link['Link']['link']);
			if (isset($this->_controllerMap[$url['controller']])) {
				$url['plugin'] = $this->_controllerMap[$url['controller']];
				$linkString = $Menus->urlToLinkString($url);
				$Link->id = $link['Link']['id'];
				$this->out(__d('croogo', 'Updating Link %s', $Link->id));
				$this->warn(__d('croogo', '- %s', $link['Link']['link']));
				$this->success(__d('croogo', '+ %s', $linkString), 2);
				$Link->saveField('link', $linkString, false);
				$count++;
			}
		}
		$this->out(__d('croogo', 'Links updated: %d rows', $count));
	}

/**
 * Upgrade Hook.bootstraps
 */
	public function bootstraps() {
		$this->_loadSettingsPlugin();

		// activate/move Wysiwyg before Ckeditor/Tinymce
		$bootstraps = Configure::read('Hook.bootstraps');
		$plugins = array_flip(explode(',', $bootstraps));
		if (empty($plugins['Ckeditor']) && empty($plugins['Tinymce'])) {
			return;
		}
		foreach ($plugins as $plugin => &$value) {
			$value *= 10;
		}

		if (!empty($plugins['Ckeditor']) && !empty($plugins['Tinymce'])) {
			$editor = ($plugins['Ckeditor'] < $plugins['Tinymce']) ? $plugins['Ckeditor'] : $plugins['Tinymce'];
		} else if (!empty($plugins['Ckeditor'])) {
			$editor = $plugins['Ckeditor'];
		} else {
			$editor = $plugins['Tinymce'];
		}

		if (empty($plugins['Wysiwyg'])) {
			$plugins['Wysiwyg'] = $editor - 1;
		} else {
			if ($plugins['Wysiwyg'] >= $editor) {
				$plugins['Wysiwyg'] = $editor - 1;
			}
		}

		asort($plugins);
		$plugins = array_flip($plugins);
		$this->Setting->write('Hook.bootstraps', join(',', $plugins));

		$this->out(__d('croogo', 'Hook.bootstraps updated'));
	}

/**
 * Runs all available subcommands
 */
	public function all() {
		foreach ($this->OptionParser->subcommands() as $command) {
			$name = $command->name();
			if ($name === 'all') {
				continue;
			}
			$this->out(__d('croogo', 'Upgrade "%s"', $name));
			$this->$name();
		}
	}

	public function execute() {
		if (empty($this->args)) {
			return $this->out($this->OptionParser->help());
		}
		$commands = array_keys($this->OptionParser->subcommands('croogo'));
		$command = $this->args[0];
		if ($command[0] != '_' && in_array($command, $commands)) {
			return $this->{$command}();
		} else {
			$this->out(__d('croogo', 'Command not recognized'));
		}
	}

}
