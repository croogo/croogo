<?php

namespace Croogo\Core\Model\Behavior;

use Acl\Model\Behavior\AclBehavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * CroogoAcl Behavior
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAclBehavior extends AclBehavior {

/**
 * setup
 *
 * @param Model $model
 * @param array $config
 */
	public function __construct(Table $model, array $config = [])
	{
		parent::__construct($model, $config);

		if (isset($config[0])) {
			$config['type'] = $config[0];
			unset($config[0]);
		}

		$this->config($model->alias(), array_merge(array('type' => 'controlled'), $config));
		$this->config($model->alias() . '.type', strtolower($this->config($model->alias() . '.type')));

		$types = $this->_typeMaps[$this->config($model->alias() . '.type')];

		if (!is_array($types)) {
			$types = array($types);
		}
		foreach ($types as $type) {
			$model->{$type} = TableRegistry::get($type);
		}
	}
}
