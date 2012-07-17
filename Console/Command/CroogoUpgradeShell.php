<?php

/**
 * CroogoUpgradeShell
 *
 * @package  Console.Command
 * @since  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoUpgradeShell extends AppShell {

/**
 * getOptionParser
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->addSubCommand('settings', array(
				'help' => __('Create settings.json from database'),
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
			ClassRegistry::init('Settings.Setting')->updateJson();
			$this->out(__('<success>Config/settings.yml converted to settings.json</success>'));
		} else {
			$this->err(__('Config/settings.json already exist'));
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

}
