<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * Params Behavior
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @since    1.3.1
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ParamsBehavior extends Behavior {

/**
 * Initialize
 * @param array $config
 */
	public function initialize(array $config) {
		parent::initialize($config);

		$this->_table->schema()->columnType('params', 'params');
	}
}
