<?php

namespace Croogo\Acl\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\Behavior;
use Cake\ORM\Table;

/**
 * UserAro Behavior
 *
 * @category Behavior
 * @package  Croogo.Acl.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UserAroBehavior extends Behavior {

/**
 * Setup
 */
	public function initialize(array $config)
	{
		parent::initialize($config);

		$this->_setupMultirole($this->_table);
	}

/**
 * Enable Multiple Role, dynamically bind User Habtm Role
 */
	protected function _setupMultirole(Table $model) {
		if (!Configure::read('Access Control.multiRole')) {
			return;
		}
		$model->bindModel(array(
			'hasAndBelongsToMany' => array(
				'Role' => array(
					'className' => 'Users.Role',
					'unique' => 'keepExisting',
				),
			)
		), false);
	}


}
