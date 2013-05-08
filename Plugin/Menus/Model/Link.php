<?php

App::uses('MenusAppModel', 'Menus.Model');

/**
 * Link
 *
 * PHP version 5
 *
 * @category Model
 * @package  Croogo.Menus.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Link extends MenusAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Link';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Croogo.Encoder',
		'Tree',
		'Croogo.Cached' => array(
			'groups' => array(
				'menus',
			),
		),
		'Croogo.Params',
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
		'link' => array(
			'rule' => array('minLength', 1),
			'message' => 'Link cannot be empty.',
		),
	);

/**
 * Model associations: belongsTo
 *
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'Menu' => array(
			'className' => 'Menus.Menu',
			'counterCache' => true,
		)
	);

}
