<?php

namespace Croogo\Menus\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Cake\Event\Event;
use Cake\ORM\Entity;
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

	protected function _initializeSchema(Schema $table) {
		$table->columnType('visibility_roles', 'encoded');
		$table->columnType('link', 'link');

		return parent::_initializeSchema($table);
	}

	/**
 * Allow to change Tree scope to a specific menu
 *
 * @param int $menuId menu id
 * @return void
 */
	public function setTreeScope($menuId) {
		$settings = array(
			'scope' => [$this->alias() . '.menu_id' => $menuId],
		);
		if ($this->hasBehavior('Tree')) {
			$this->behaviors()->get('Tree')->config($settings);
		} else {
			$this->addBehavior('Tree', $settings);
		}
	}

/**
 * Calls TreeBehavior::recover when we are changing scope
 */
	public function afterSave(Event $event, Entity $entity, $options = array()) {
		if ($entity->isNew()) {
			return;
		}
		if ($entity->dirty('menu_id')) {
			$this->setTreeScope($entity->menu_id);
			$this->recover();
			$this->setTreeScope($entity->getOriginal('menu_id'));
			$this->recover();
		}
	}

}
