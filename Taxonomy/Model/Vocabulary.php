<?php

App::uses('TaxonomyAppModel', 'Taxonomy.Model');

/**
 * Vocabulary
 *
 * @category Taxonomy.Model
 * @package  Croogo.Taxonomy.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Vocabulary extends TaxonomyAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Vocabulary';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Croogo.Ordered' => array(
			'field' => 'weight',
			'foreign_key' => false,
		),
		'Croogo.Cached' => array(
			'groups' => array(
				'taxonomy',
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
	public $validate = array();

/**
 * Model associations: hasAndBelongsToMany
 *
 * @var array
 * @access public
 */
	public $hasAndBelongsToMany = array(
		'Type' => array(
			'className' => 'Taxonomy.Type',
			'joinTable' => 'types_vocabularies',
			'foreignKey' => 'vocabulary_id',
			'associationForeignKey' => 'type_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => '',
		),
	);

/**
 * Model associations: hasMany
 */
	public $hasMany = array(
		'Taxonomy' => array(
			'className' => 'Taxonomy.Taxonomy',
			'dependent' => true,
		),
	);

}
