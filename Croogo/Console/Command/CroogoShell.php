<?php

App::uses('AppShell', 'Console/Command');
App::uses('Security', 'Utility');
App::uses('CroogoJson', 'Croogo.Lib');

/**
 * Croogo Shell
 *
 * @category Shell
 * @package  Croogo.Croogo.Console.Command
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoShell extends AppShell {

	public $tasks = array(
		'Croogo.Upgrade',
	);

/**
 * Display help/options
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__d('croogo', 'Croogo Utilities'))
			->addSubCommand('make', array(
				'help' => __d('croogo', 'Compile/Generate CSS'),
			))
			->addSubCommand('upgrade', array(
				'help' => __d('croogo', 'Upgrade Croogo'),
				'parser' => $this->Upgrade->getOptionParser(),
			))
			->addSubcommand('password', array(
				'help' => 'Get hashed password',
				'parser' => array(
					'description' => 'Get hashed password',
					'arguments' => array(
						'password' => array(
							'required' => true,
							'help' => 'Password to hash',
						),
					),
				),
			));
		return $parser;
	}

/**
 * Get hashed password
 *
 * Usage: ./Console/cake croogo password myPasswordHere
 */
	public function password() {
		$value = trim($this->args['0']);
		$this->out(Security::hash($value, null, true));
	}

/**
 * Compile assets for admin ui
 */
	public function make() {
		App::uses('AssetGenerator', 'Install.Lib');
		if (!CakePlugin::loaded('Install')) {
			CakePlugin::load('Install');
		}
		$generator = new AssetGenerator();
		try {
			$generator->generate(array('clone' => true));
		} catch (Exception $e) {
			$this->err('<error>' . $e->getMessage() . '</error>');
		}
		CakePlugin::unload('Install');
	}

/**
 * Usage: ./Console/cake croogo aggregateManifestFile package.json
 *        ./Console/cake croogo aggregateManifestFile bower.json
 */
	public function aggregateManifestFile() {
		$jsonFileName = 'package.json';
		if (isset($this->args['0'])) {
			$jsonFileName = $this->args['0'];
		}

		$settingsPath = APP . 'Config/settings.json';
		if (!file_exists($settingsPath)) {
			$settingsPath = $settingsPath . '.install';
		}
		$settings = json_decode(file_get_contents($settingsPath), true);
		$plugins = explode(',', $settings['Hook']['bootstraps']);
		$plugins = array_merge(array('Croogo'), $plugins);

		$deps = array();
		foreach ($plugins as $plugin) {
			$pluginPath = APP . 'Plugin' . DS . $plugin;
			if (!file_exists($pluginPath)) {
				$pluginPath = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . $plugin;
				if (!file_exists($pluginPath)) {
					continue;
				}
			}

			$jsonPath = $pluginPath . '/' . $jsonFileName;
			if (file_exists($jsonPath)) {
				$json = json_decode(file_get_contents($jsonPath), true);
				if (isset($json['dependencies'])) {
					foreach ($json['dependencies'] as $dep => $version) {
						if (!isset($deps[$dep])) {
							$deps[$dep] = $version;
						} else {
							if ($version > $deps[$dep]) {
								$version = $version;
							} else {
								$version = $deps[$dep];
							}
							$deps[$dep] = $version;
						}
					}
				}
			}
		}

		$rootJsonFile = APP . $jsonFileName;
		$rootJson = json_decode(file_get_contents($rootJsonFile), true);
		$rootJson['devDependencies'] = $deps;
		file_put_contents($rootJsonFile, CroogoJson::stringify($rootJson));
		$this->out('File updated at: ' . $rootJsonFile);
	}

	public function cachePluginPaths() {
		$settingsPath = APP . 'Config/settings.json';
		if (!file_exists($settingsPath)) {
			$settingsPath = $settingsPath . '.install';
		}
		$settings = json_decode(file_get_contents($settingsPath), true);
		$plugins = explode(',', $settings['Hook']['bootstraps']);
		$plugins = array_merge(array('Croogo'), $plugins);

		$pluginPaths = array();
		foreach ($plugins as $plugin) {
			$pluginPath = APP . 'Plugin' . DS . $plugin;
			if (!file_exists($pluginPath)) {
				$pluginPath = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . $plugin;
				if (!file_exists($pluginPath)) {
					continue;
				}
			}

			$pluginPaths[$plugin] = $pluginPath;
		}

		$write = TMP . 'plugin_paths.json';
		touch($write);
		file_put_contents($write, json_encode($pluginPaths));
		$this->out('File written at: ' . $write);
	}

}
