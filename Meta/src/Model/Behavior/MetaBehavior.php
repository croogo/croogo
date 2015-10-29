<?php

namespace Croogo\Meta\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * Meta Behavior
 *
 * @category Behavior
 * @package  Croogo.Meta.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaBehavior extends Behavior {

	public function initialize(array $config)
	{
		parent::initialize($config);

		if (!$this->_table->hasBehavior('Eav.Eav')) {
			$this->_table->addBehavior('Eav.Eav');
		}

		$this->_table->addColumn('meta-description', ['type' => 'string', 'bundle' => 'meta']);
		$this->_table->addColumn('meta-keywords', ['type' => 'string', 'bundle' => 'meta']);
	}
}
