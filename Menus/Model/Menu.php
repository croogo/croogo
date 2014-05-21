<?php

namespace Croogo\Menus\Model;

use Menus\Model\MenusAppModel;
/**
 * Menu
 *
 * @category Model
 * @package  Croogo.Menus.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Menu extends MenusAppModel {

/**
 * Model name
 *
 * @var string
 * @access public
 */
	public $name = 'Menu';

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
	public $actsAs = array(
		'Croogo.Cached' => array(
			'groups' => array(
				'menus',
			),
		),
		'Croogo.Params',
		'Croogo.Publishable',
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
 * Model associations: hasMany
 *
 * @var array
 * @access public
 */
	public $hasMany = array(
		'Link' => array(
			'className' => 'Menus.Link',
			'foreignKey' => 'menu_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => 'Link.lft ASC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => '',
		),
	);

/**
 * beforeDelete callback
 */
	public function beforeDelete($cascade = true) {
		// Set tree scope for Link association
		$settings = array(
			'scope' => array($this->Link->alias . '.menu_id' => $this->id),
		);
		if ($this->Link->Behaviors->loaded('Tree')) {
			$this->Link->Behaviors->Tree->setup($this->Link, $settings);
		} else {
			$this->Link->Behaviors->load('Tree', $settings);
		}
	}

}
