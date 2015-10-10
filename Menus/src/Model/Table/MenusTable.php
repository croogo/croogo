<?php

namespace Croogo\Menus\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Croogo\Core\Model\Table\CroogoTable;

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
	public $validate = [
		'title' => [
			'rule' => ['minLength', 1],
			'message' => 'Title cannot be empty.',
		],
		'alias' => [
			'isUnique' => [
				'rule' => 'isUnique',
				'message' => 'This alias has already been taken.',
			],
			'minLength' => [
				'rule' => ['minLength', 1],
				'message' => 'Alias cannot be empty.',
			],
		],
	];

	public function initialize(array $config) {
		parent::initialize($config);

//		$this->addBehavior('Croogo/Core.Cached', [
//			'groups' => [
//				'menus',
//			],
//		]);
		$this->addBehavior('Croogo/Core.Publishable');
		$this->addBehavior('Croogo/Core.Trackable');

		$this->addBehavior('Timestamp', [
			'events' => [
				'Model.beforeSave' => [
					'created' => 'new',
					'updated' => 'always'
				]
			]
		]);
		$this->hasMany('Links', [
			'className' => 'Menus.Links',
			'order' => [
				'lft' => 'ASC'
			],
		]);
	}

/**
 * beforeDelete callback
 */
	public function beforeDelete(Event $event, Entity $entity, $options) {
		// Set tree scope for Links association
		$settings = [
			'scope' => [$this->Links->alias() . '.menu_id' => $entity->id],
		];
		if ($this->Links->hasBehavior('Tree')) {
			$this->Links->behaviors()->get('Tree')->config($settings);
		} else {
			$this->Links->addBehavior('Tree', $settings);
		}
	}

}
