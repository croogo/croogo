<?php

namespace Croogo\Menus\Model;
App::uses('MenusAppModel', 'Menus.Model');

/**
 * Link
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

/**
 * Allow to change Tree scope to a specific menu
 *
 * @param int $menuId menu id
 * @return void
 */
	public function setTreeScope($menuId) {
		$settings = array(
			'scope' => array($this->alias . '.menu_id' => $menuId),
		);
		if ($this->Behaviors->loaded('Tree')) {
			$this->Behaviors->Tree->setup($this, $settings);
		} else {
			$this->Behaviors->load('Tree', $settings);
		}
	}

/**
 * If we are moving between Menus, save original id so that Link::afterSave()
 * recover() can recover the tree
 *
 */
	public function beforeSave($options = array()) {
		if (!isset($this->data['Link']['menu_id']) || !isset($this->data['Link']['id'])) {
			return true;
		}
		$previousMenuId = $this->field('menu_id', array(
			$this->escapeField('id') => $this->data['Link']['id']
		));
		$hasMenuChanged = ($previousMenuId != $this->data['Link']['menu_id']);
		if ($hasMenuChanged) {
			$this->_previousMenuId = $previousMenuId;
		}

		return true;
	}

/**
 * Calls TreeBehavior::recover when we are changing scope
 */
	public function afterSave($created, $options = array()) {
		if ($created) {
			return;
		}
		if (isset($this->_previousMenuId)) {
			$this->setTreeScope($this->data['Link']['menu_id']);
			$this->recover();
			$this->setTreeScope($this->_previousMenuId);
			$this->recover();
			unset($this->_previousMenuId);
		}
	}

}
