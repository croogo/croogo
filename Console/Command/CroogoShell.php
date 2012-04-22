<?php

App::uses('Security', 'Utility');

/**
 * Croogo Shell
 *
 * PHP version 5
 *
 * @category Shell
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoShell extends AppShell {

/**
 * Display help/options
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__('Croogo Utilities')
			)->addSubcommand('password', array(
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
				)
			);
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

}
