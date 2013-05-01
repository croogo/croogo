<?php

App::uses('BlocksAppModel', 'Blocks.Model');

/**
 * Block
 *
 * PHP version 5
 *
 * @category Blocks.Model
 * @package  Croogo.Blocks.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Block extends BlocksAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Block';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Croogo.Encoder',
		'Croogo.Ordered' => array(
			'field' => 'weight',
			'foreign_key' => false,
		),
		'Croogo.Cached' => array(
			'groups' => array(
				'blocks',
			),
		),
		'Croogo.Params',
		'Search.Searchable',
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
		'chooser' => array('type' => null),
		'title' => array('type' => 'like', 'field' => array('Block.title', 'Block.alias')),
		'region_id' => array('type' => 'value'),
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Region' => array(
			'className' => 'Blocks.Region',
			'foreignKey' => 'region_id',
			'counterCache' => true,
			'counterScope' => array('Block.status' => 1),
		),
	);

}
