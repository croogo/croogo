<?php

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
 * initialize
 *
 * @param Controller $controller instance of controller
 */
	public function initialize(Controller $controller) {
		$this->controller = $controller;
		if (isset($controller->Block)) {
			$this->Block = $controller->Block;
		} else {
			$this->Block = ClassRegistry::init('Blocks.Block');
		}
	}

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		if (!isset($controller->request->params['admin']) && !isset($controller->request->params['requested'])) {
			$this->blocks();
		}
	}

/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$controller->set('blocks_for_layout', $this->blocksForLayout);
	}

/**
 * Blocks
 *
 * Blocks will be available in this variable in views: $blocks_for_layout
 *
 * @return void
 */
	public function blocks() {
		$regions = $this->Block->Region->find('list', array(
			'conditions' => array(
				'Region.block_count >' => '0',
			),
			'fields' => array(
				'Region.id',
				'Region.alias',
			),
			'cache' => array(
				'name' => 'regions',
				'config' => 'croogo_blocks',
			),
		));
		$roleId = $this->controller->Auth->user('role_id');
		foreach ($regions as $regionId => $regionAlias) {
			$this->blocksForLayout[$regionAlias] = array();
			$findOptions = array(
				'conditions' => array(
					'Block.status' => 1,
					'Block.region_id' => $regionId,
					'AND' => array(
						array(
							'OR' => array(
								'Block.visibility_roles' => '',
								'Block.visibility_roles LIKE' => '%"' . $roleId . '"%',
							),
						),
						array(
							'OR' => array(
								'Block.visibility_paths' => '',
								'Block.visibility_paths LIKE' => '%"' . $this->controller->request->here . '"%',
							),
						),
					),
				),
				'order' => array(
					'Block.weight' => 'ASC'
				),
				'cache' => array(
					'prefix' => 'blocks_' . $regionAlias . '_' . $roleId,
					'config' => 'croogo_blocks',
				),
				'recursive' => '-1',
			);
			$blocks = $this->Block->find('all', $findOptions);
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
		foreach ($blocks as $block) {
			$this->blocksData['menus'] = Hash::merge(
				$this->blocksData['menus'],
				$this->parseString('menu|m', $block['Block']['body'])
			);
			$this->blocksData['vocabularies'] = Hash::merge(
				$this->blocksData['vocabularies'],
				$this->parseString('vocabulary|v', $block['Block']['body'])
			);
			$this->blocksData['nodes'] = Hash::merge(
				$this->blocksData['nodes'],
				$this->parseString('node|n', $block['Block']['body'],
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
 * @param string $exp
 * @param string $text
 * @param array  $options
 * @return array
 */
	public function parseString($exp, $text, $options = array()) {
		$_options = array(
			'convertOptionsToArray' => false,
		);
		$options = array_merge($_options, $options);

		$output = array();
		preg_match_all('/\[(' . $exp . '):([A-Za-z0-9_\-]*)(.*?)\]/i', $text, $tagMatches);
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$alias = $tagMatches[2][$i];
			$aliasOptions = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				$aliasOptions[$attributes[1][$j]] = $attributes[2][$j];
			}
			if ($options['convertOptionsToArray']) {
				foreach ($aliasOptions as $optionKey => $optionValue) {
					if (!is_array($optionValue) && strpos($optionValue, ':') !== false) {
						$aliasOptions[$optionKey] = $this->stringToArray($optionValue);
					}
				}
			}
			$output[$alias] = $aliasOptions;
		}
		return $output;
	}

/**
 * Converts formatted string to array
 *
 * A string formatted like 'Node.type:blog;' will be converted to
 * array('Node.type' => 'blog');
 *
 * @param string $string in this format: Node.type:blog;Node.user_id:1;
 * @return array
 */
	public function stringToArray($string) {
		$string = explode(';', $string);
		$stringArr = array();
		foreach ($string as $stringElement) {
			if ($stringElement != null) {
				$stringElementE = explode(':', $stringElement);
				if (isset($stringElementE['1'])) {
					$stringArr[$stringElementE['0']] = $stringElementE['1'];
				} else {
					$stringArr[] = $stringElement;
				}
			}
		}
		return $stringArr;
	}

}
