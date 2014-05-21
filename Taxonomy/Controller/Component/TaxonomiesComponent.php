<?php

namespace Croogo\Taxonomy\Controller\Component;
App::uses('Component', 'Controller');

/**
 * Taxonomies Component
 *
 * @category Component
 * @package  Croogo.Taxonomy.Controller.Component
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
		if (isset($controller->Taxonomy)) {
			$this->Taxonomy = $controller->Taxonomy;
		} else {
			$this->Taxonomy = ClassRegistry::init('Taxonomy.Taxonomy');
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
		$types = $this->Taxonomy->Vocabulary->Type->find('all', array(
			'conditions' => array(
				'Type.plugin' => null,
			),
			'order' => 'Type.alias ASC',
		));
		$this->controller->set('types_for_admin_layout', $types);

		// vocabularies
		$vocabularies = $this->Taxonomy->Vocabulary->find('all', array(
			'recursive' => '-1',
			'conditions' => array(
				'Vocabulary.plugin' => null,
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
		$types = $this->Taxonomy->Vocabulary->Type->find('all', array(
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
			$vocabulary = $this->Taxonomy->Vocabulary->find('first', array(
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
				$threaded = $this->Taxonomy->find('threaded', array(
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

/**
 * Prepare required taxonomy baseline data for use in views
 *
 * @param array $type Type data
 * @param array $options Options
 * @return void
 * @throws UnexpectedException
 */
	public function prepareCommonData($type, $options = array()) {
		$options = Hash::merge(array(
			'modelClass' => $this->controller->modelClass,
		), $options);
		$typeAlias = $type['Type']['alias'];
		$modelClass = $options['modelClass'];

		if (isset($this->controller->{$modelClass})) {
			$Model = $this->controller->{$modelClass};
		} else {
			throw new UnexpectedException(sprintf(
				'Model %s not found in controller %s',
				$model, $this->controller->name
			));
		}
		$Model->type = $typeAlias;
		$vocabularies = Hash::combine($type['Vocabulary'], '{n}.id', '{n}');
		$taxonomy = array();
		foreach ($type['Vocabulary'] as $vocabulary) {
			$vocabularyId = $vocabulary['id'];
			$taxonomy[$vocabularyId] = $Model->Taxonomy->getTree(
				$vocabulary['alias'],
				array('taxonomyId' => true)
			);
		}
		$this->controller->set(compact(
			'type', 'typeAlias', 'taxonomy', 'vocabularies'
		));
	}

}
