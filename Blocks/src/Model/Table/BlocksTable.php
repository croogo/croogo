<?php

namespace Croogo\Blocks\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Core\Status;

/**
 * Block
 *
 * @category Blocks.Model
 * @package  Croogo.Blocks.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksTable extends CroogoTable {

/**
 * Validation
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'title' => array(
			'rule' => array('minLength', 1),
			'message' => 'Title cannot be empty.',
		),
		'alias' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'This alias has already been taken.',
			),
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Alias cannot be empty.',
			),
		),
	);

/**
 * Filter search fields
 *
 * @var array
 * @access public
 */
	public $filterArgs = array(
		'title' => array('type' => 'like', 'field' => array('title', 'alias')),
		'region_id' => array('type' => 'value'),
	);

/**
 * Find methods
 */
	public $findMethods = array(
		'published' => true,
	);

	public function initialize(array $config) {
		parent::initialize($config);
		$this->entityClass('Croogo/Blocks.Block');

		$this->belongsTo('Regions', [
			'className' => 'Croogo/Blocks.Regions',
			'foreignKey' => 'region_id',
			'counterCache' => true,
			'counterScope' => array('Blocks.status >=' => Status::PUBLISHED),
		]);

		$this->addBehavior('Croogo/Core.Publishable');
		/* TODO: Enable after behaviors have been updated to 3.x
		$this->addBehavior('Croogo/Core.Cached', [
			'groups' => [
				'blocks',
			]
		]);
		$this->addBehavior('Croogo/Core.Trackable');
		*/
		$this->addBehavior('Search.Searchable');
	}

	protected function _initializeSchema(Schema $table) {
		$table->columnType('visibility_roles', 'encoded');
		$table->columnType('visibility_paths', 'encoded');
		$table->columnType('params', 'params');

		return parent::_initializeSchema($table);
	}

/**
 * Find Published blocks
 *
 * Query options:
 * - status Status
 * - regionId Region Id
 * - roleId Role Id
 * - cacheKey Cache key (optional)
 */
	protected function _findPublished($state, $query, $results = array()) {
		if ($state === 'after') {
			return $results;
		}

		$status = isset($query['status']) ? $query['status'] : $this->status();
		$regionId = isset($query['regionId']) ? $query['regionId'] : null;
		$roleId = isset($query['roleId']) ? $query['roleId'] : 3;
		$cacheKey = isset($query['cacheKey']) ? $query['cacheKey'] : $regionId . '_' . $roleId;
		unset($query['status'], $query['regionId'], $query['roleId'], $query['cacheKey']);

		$visibilityRolesField = $this->escapeField('visibility_roles');

		$default = array(
			'conditions' => array(
				$this->escapeField('status') => $status,
				$this->escapeField('region_id') => $regionId,
				'AND' => array(
					array(
						'OR' => array(
							$visibilityRolesField => '',
							$visibilityRolesField . ' LIKE' => '%"' . $roleId . '"%',
						),
					),
				),
			),
			'order' => array(
				$this->escapeField('weight') => 'ASC'
			),
			'cache' => array(
				'prefix' => 'blocks_' . $cacheKey,
				'config' => 'croogo_blocks',
			),
			'recursive' => '-1',
		);

		$query = Hash::merge($query, $default);
		return $query;
	}

}
