<?php

namespace Croogo\Menus\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Croogo\Croogo\Model\Table\CroogoTable;

/**
 * Menu
 *
 * @property LinksTable Links
 * @category Model
 * @package  Croogo.Menus.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusTable extends CroogoTable {

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

	public function initialize(array $config) {
		parent::initialize($config);

//		$this->addBehavior('Croogo/Croogo.Cached', [
//			'groups' => [
//				'menus',
//			],
//		]);
		$this->addBehavior('Croogo/Croogo.Params');
		$this->addBehavior('Croogo/Croogo.Publishable');
//		$this->addBehavior('Croogo/Croogo.Trackable');
		$this->hasMany('Links', [
			'className' => 'Menus.Links',
			'order' => [
				'Links.lft' => 'ASC'
			],
		]);
	}

	/**
 * beforeDelete callback
 */
	public function beforeDelete(Event $event, Entity $entity, $options) {
		// Set tree scope for Links association
		$settings = array(
			'scope' => array($this->Links->alias() . '.menu_id' => $entity->id),
		);
		if ($this->Links->hasBehavior('Tree')) {
			$this->Links->behaviors()->get('Tree')->config($settings);
		} else {
			$this->Links->addBehavior('Tree', $settings);
		}
	}

}
