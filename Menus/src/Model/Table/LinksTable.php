<?php

namespace Croogo\Menus\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

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
class LinksTable extends CroogoTable {

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

	public function initialize(array $config) {
		parent::initialize($config);

		$this->addBehavior('Croogo/Croogo.Encoder');
		$this->addBehavior('Tree');
//		$this->addBehavior('Croogo/Croogo.Cached');
		$this->addBehavior('Croogo/Croogo.Params');
		$this->addBehavior('Croogo/Croogo.Publishable');
//		$this->addBehavior('Croogo/Croogo.Trackable');
		$this->belongsTo('Menus', [
			'className' => 'Croogo/Menus.Menus',
			'counterCache' => true,
		]);
	}


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
