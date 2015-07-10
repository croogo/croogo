<?php

namespace Croogo\Blocks\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Cake\ORM\Query;
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
		*/
		$this->addBehavior('Croogo/Core.Trackable');
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
	public function findPublished(Query $query, array $options = []) {
		$status = isset($options['status']) ? $options['status'] : $this->status();
		$regionId = isset($options['regionId']) ? $options['regionId'] : null;
		$roleId = isset($options['roleId']) ? $options['roleId'] : 3;
		$cacheKey = isset($options['cacheKey']) ? $options['cacheKey'] : $regionId . '_' . $roleId;
		unset($options['status'], $options['regionId'], $options['roleId'], $options['cacheKey']);

		return $query->where([
			'status' => $status,
			'region_id' => $regionId,
			'AND' => array(
				array(
					'OR' => array(
						'visibility_roles' => '',
						'visibility_roles' . ' LIKE' => '%"' . $roleId . '"%',
					),
				),
			),
		])->order([
			'weight' => 'ASC'
		])->applyOptions([
			'prefix' => 'blocks_' . $cacheKey,
			'config' => 'croogo_blocks',
		]);
	}

}
