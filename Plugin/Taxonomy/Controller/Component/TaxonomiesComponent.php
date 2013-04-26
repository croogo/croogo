<?php

/**
 * Taxonomies Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Taxonomy
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesComponent extends Component {

/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo.Croogo',
	);

/**
 * Types for layout
 *
 * @var string
 * @access public
 */
	public $typesForLayout = array();

/**
 * Vocabularies for layout
 *
 * @var string
 * @access public
 */
	public $vocabulariesForLayout = array();

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		$this->controller = $controller;
		if (isset($controller->Node)) {
			$this->Node = $controller->Node;
		} else {
			$this->Node = ClassRegistry::init('Nodes.Node');
		}

		if (!isset($this->controller->request->params['admin']) && !isset($this->controller->request->params['requested'])) {
			$this->types();
			$this->vocabularies();
		} else {
			$this->_adminData();
		}
	}

	public function beforeRender(Controller $controller) {
		$this->controller = $controller;
		$this->controller->set('types_for_layout', $this->typesForLayout);
		$this->controller->set('vocabularies_for_layout', $this->vocabulariesForLayout);
	}

/**
 * Set variables for admin layout
 *
 * @return void
 */
	protected function _adminData() {
		// types
		$types = $this->Node->Taxonomy->Vocabulary->Type->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Type.plugin LIKE' => '',
					'Type.plugin' => null,
				),
			),
			'order' => 'Type.alias ASC',
		));
		$this->controller->set('types_for_admin_layout', $types);

		// vocabularies
		$vocabularies = $this->Node->Taxonomy->Vocabulary->find('all', array(
			'recursive' => '-1',
			'conditions' => array(
				'OR' => array(
					'Vocabulary.plugin LIKE' => '',
					'Vocabulary.plugin' => null,
				),
			),
			'order' => 'Vocabulary.alias ASC',
		));
		$this->controller->set('vocabularies_for_admin_layout', $vocabularies);
	}

/**
 * Types
 *
 * Types will be available in this variable in views: $types_for_layout
 *
 * @return void
 */
	public function types() {
		$types = $this->Node->Taxonomy->Vocabulary->Type->find('all', array(
			'cache' => array(
				'name' => 'types',
				'config' => 'croogo_types',
			),
		));
		foreach ($types as $type) {
			$alias = $type['Type']['alias'];
			$this->typesForLayout[$alias] = $type;
		}
	}

/**
 * Vocabularies
 *
 * Vocabularies will be available in this variable in views: $vocabularies_for_layout
 *
 * @return void
 */
	public function vocabularies() {
		$vocabularies = array();
		$themeData = $this->Croogo->getThemeData(Configure::read('Site.theme'));
		if (isset($themeData['vocabularies']) && is_array($themeData['vocabularies'])) {
			$vocabularies = Hash::merge($vocabularies, $themeData['vocabularies']);
		}
		$vocabularies = Hash::merge($vocabularies, array_keys($this->controller->Blocks->blocksData['vocabularies']));
		$vocabularies = array_unique($vocabularies);
		foreach ($vocabularies as $vocabularyAlias) {
			$vocabulary = $this->Node->Taxonomy->Vocabulary->find('first', array(
				'conditions' => array(
					'Vocabulary.alias' => $vocabularyAlias,
				),
				'cache' => array(
					'name' => 'vocabulary_' . $vocabularyAlias,
					'config' => 'croogo_vocabularies',
				),
				'recursive' => '-1',
			));
			if (isset($vocabulary['Vocabulary']['id'])) {
				$threaded = $this->Node->Taxonomy->find('threaded', array(
					'conditions' => array(
						'Taxonomy.vocabulary_id' => $vocabulary['Vocabulary']['id'],
					),
					'contain' => array(
						'Term',
					),
					'cache' => array(
						'name' => 'vocabulary_threaded_' . $vocabularyAlias,
						'config' => 'croogo_vocabularies',
					),
					'order' => 'Taxonomy.lft ASC',
				));
				$this->vocabulariesForLayout[$vocabularyAlias] = array();
				$this->vocabulariesForLayout[$vocabularyAlias]['Vocabulary'] = $vocabulary['Vocabulary'];
				$this->vocabulariesForLayout[$vocabularyAlias]['threaded'] = $threaded;
			}
		}
	}

}
