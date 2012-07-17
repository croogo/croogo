<?php

App::uses('AclNode', 'Model');

/**
 * AclAro Model
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AclAro extends AclNode {

/**
 * name
 *
 * @var string
 */
	public $name = 'AclAro';

/**
 * useTable
 *
 * @var string
 */
	public $useTable = 'aros';

/**
 * alias
 */
	public $alias = 'Aro';

/**
 * Get a list of Role AROs
 *
 * @return array array of Aro.id indexed by Role.id
 */
	public function getRoles($roles) {
		$aros = $this->find('all', array(
			'conditions' => array(
				'Aro.model' => 'Role',
				'Aro.foreign_key' => array_keys($roles),
			),
		));
		return Set::combine($aros, '{n}.Aro.foreign_key', '{n}.Aro.id');
	}

}
