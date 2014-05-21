<?php

namespace Croogo\Blocks\Model;
App::uses('BlocksAppModel', 'Blocks.Model');

/**
 * Region
 *
 * @category Blocks.Model
 * @package  Croogo.Blocks.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Region extends BlocksAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Region';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Search.Searchable',
		'Croogo.Cached' => array(
			'groups' => array(
				'blocks',
			),
		),
		'Croogo.Trackable',
	);

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
		'title' => array('type' => 'like', 'field' => array('Region.title'))
	);

/**
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
	public $hasMany = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'region_id',
			'dependent' => false,
			'limit' => 3,
		),
	);

/**
 * Display fields for this model
 *
 * @var array
 */
	protected $_displayFields = array(
		'id',
		'title',
		'alias',
	);

/**
 * Find methods
 */
	public $findMethods = array(
		'active' => true,
	);

/**
 * Find Regions currently in use
 */
	protected function _findActive($state, $query, $results = array()) {
		if ($state == 'after') {
			if ($results) {
				$keyPath = '{n}.'. $this->alias . '.id';
				$valuePath = '{n}.'. $this->alias . '.alias';
				$results = Hash::combine($results, $keyPath, $valuePath);
			}
			return $results;
		}

		$query = Hash::merge($query, array(
			'recursive' => -1,
			'conditions' => array(
				$this->escapeField('block_count') . ' >' => '0',
			),
			'fields' => array(
				$this->escapeField(),
				$this->escapeField('alias'),
			),
			'cache' => array(
				'name' => 'regions',
				'config' => 'croogo_blocks',
			),
		));
		return $query;
	}

}
