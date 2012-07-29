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
 * getOptionParser
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->addSubCommand('acl', array(
				'help' => __('Upgrade ACL database for core controllers.'),
				'parser' => array(
					'description' => __(
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
				'help' => __('Create settings.json from database'),
				))
			->addSubCommand('all', array(
				'help' => __('Run all upgrade tasks'),
				));
	}

/**
 * convert settings.yml to settings.json
 */
	public function settings($keys = array()) {
		if (!CakePlugin::loaded('Settings')) {
			CakePlugin::load('Settings');
		}
		if (file_exists(APP . 'Config' . DS . 'settings.json')) {
			$this->err(__('<warning>Config/settings.json already exist</warning>'));
		} else {
			ClassRegistry::init('Settings.Setting')->updateJson();
			$this->out(__('<success>Config/settings.yml created based on `settings` table</success>'));
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

/**
 * Runs all available subcommands
 */
	public function all() {
		foreach ($this->OptionParser->subcommands() as $command) {
			$name = $command->name();
			if ($name === 'all') {
				continue;
			}
			$this->out(__('Upgrade "%s"', $name));
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
			$this->out(__('Command not recognized'));
		}
	}

}
