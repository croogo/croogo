<?php

App::uses('AppModel', 'Model');

/**
 * Language
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo.Settings.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Language extends AppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Language';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Croogo.Ordered' => array('field' => 'weight', 'foreign_key' => null),
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

}
