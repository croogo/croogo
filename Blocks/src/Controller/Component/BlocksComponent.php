<?php

namespace Croogo\Blocks\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

use Croogo\Core\Utility\StringConverter;
use Croogo\Core\Utility\VisibilityFilter;
/**
 * Blocks Component
 *
 * @package Croogo.Blocks.Controller.Component
 */
class BlocksComponent extends Component {

/**
 * Blocks for layout
 *
 * @var string
 * @access public
 */
	public $blocksForLayout = array();

/**
 * Blocks data: contains parsed value of bb-code like strings
 *
 * @var array
 * @access public
 */
	public $blocksData = array(
		'menus' => array(),
		'vocabularies' => array(),
		'nodes' => array(),
	);

/**
 * StringConverter instance
 */
	protected $_stringConverter = null;

/**
 * initialize
 *
 * @param Event $event
 */
	public function beforeFilter(Event $event) {
		$this->controller = $event->subject();
		$this->_stringConverter = new StringConverter();
		if (isset($this->controller->Blocks)) {
			$this->Blocks = $this->controller->Blocks;
		} else {
			$this->Blocks = TableRegistry::get('Croogo/Blocks.Blocks');
		}
	}

/**
 * Startup
 *
 * @param Event $event
 * @return void
 */
	public function startup(Event $event) {
		if (!isset($event->subject()->request->params['admin']) && !isset($event->subject()->request->params['requested'])) {
			$this->blocks();
		}
	}

/**
 * beforeRender
 *
 * @param object $event
 * @return void
 */
	public function beforeRender(Event $event) {
		$event->subject()->set('blocks_for_layout', $this->blocksForLayout);
	}

/**
 * Blocks
 *
 * Blocks will be available in this variable in views: $blocks_for_layout
 *
 * @return void
 */
	public function blocks() {
		$this->blocksForLayout = [];
		return;
		$regions = $this->Blocks->Regions->find('active');

		$alias = $this->Block->alias;
		$roleId = $this->controller->Croogo->roleId();
		$status = $this->Block->status();
		$request = $this->controller->request;
		$slug = Inflector::slug(strtolower($request->url));
		$Filter = new VisibilityFilter($request);
		foreach ($regions as $regionId => $regionAlias) {
			$cacheKey = $regionAlias . '_' . $roleId;
			$this->blocksForLayout[$regionAlias] = array();

			$visibilityCachePrefix = 'visibility_' .  $slug . '_' . $cacheKey;
			$blocks = Cache::read($visibilityCachePrefix, 'croogo_blocks');
			if ($blocks === false) {

				$blocks = $this->Block->find('published', array(
					'regionId' => $regionId,
					'roleId' => $roleId,
					'cacheKey' => $cacheKey,
				));
				foreach ($blocks as &$block) {
					$block[$alias]['visibility_paths'] = $this->Block->decodeData($block[$alias]['visibility_paths']);
				}

				$blocks = $Filter->remove($blocks, array(
					'model' => $alias,
					'field' => 'visibility_paths',
					'cache' => array(
						'prefix' => $visibilityCachePrefix,
						'config' => 'croogo_blocks',
					),
				));
				Cache::write($visibilityCachePrefix, $blocks, 'croogo_blocks');
			}
			$this->processBlocksData($blocks);
			$this->blocksForLayout[$regionAlias] = $blocks;
		}
	}

/**
 * Process blocks for bb-code like strings
 *
 * @param array $blocks
 * @return void
 */
	public function processBlocksData($blocks) {
		$converter = $this->_stringConverter;
		foreach ($blocks as $block) {
			$this->blocksData['menus'] = Hash::merge(
				$this->blocksData['menus'],
				$converter->parseString('menu|m', $block['Block']['body'])
			);
			$this->blocksData['vocabularies'] = Hash::merge(
				$this->blocksData['vocabularies'],
				$converter->parseString('vocabulary|v', $block['Block']['body'])
			);
			$this->blocksData['nodes'] = Hash::merge(
				$this->blocksData['nodes'],
				$converter->parseString('node|n', $block['Block']['body'],
				array('convertOptionsToArray' => true)
			));
		}
	}

/**
 * Parses bb-code like string.
 *
 * Example: string containing [menu:main option1="value"] will return an array like
 *
 * Array
 * (
 *     [main] => Array
 *         (
 *             [option1] => value
 *         )
 * )
 *
 * @deprecated Use StringConverter::parseString()
 * @see StringConverter::parseString()
 * @param string $exp
 * @param string $text
 * @param array  $options
 * @return array
 */
	public function parseString($exp, $text, $options = array()) {
		return $this->_stringConverter->parseString($exp, $text, $options);
	}

/**
 * Converts formatted string to array
 *
 * A string formatted like 'Node.type:blog;' will be converted to
 * array('Node.type' => 'blog');
 *
 * @deprecated Use StringConverter::stringToArray()
 * @see StringConverter::stringToArray()
 * @param string $string in this format: Node.type:blog;Node.user_id:1;
 * @return array
 */
	public function stringToArray($string) {
		return $this->_stringConverter->stringToArray($string);
	}

}
