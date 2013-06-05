<?php

App::uses('AppShell', 'Console/Command');
App::uses('Security', 'Utility');

/**
 * Croogo Shell
 *
 * PHP version 5
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

}
