<?php

namespace Croogo\Taxonomy\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Croogo\Core\Core\Exception\Exception;
use Croogo\Taxonomy\Model\Entity\Type;
use Croogo\Taxonomy\Model\Entity\Vocabulary;
use Croogo\Taxonomy\Model\Table\TaxonomiesTable;

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
		'Croogo/Core.Croogo',
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
	public function startup(Event $event) {
		$this->controller = $event->subject();
		if ((isset($this->controller->Taxonomies)) && ($this->controller->Taxonomies instanceof TaxonomiesTable)) {
			$this->Taxonomies = $this->controller->Taxonomies;
		} else {
			$this->Taxonomies = TableRegistry::get('Croogo/Taxonomy.Taxonomies');
		}

		if ($this->controller->request->param('prefix') != 'admin' && !isset($this->controller->request->params['requested'])) {
			$this->types();
			$this->vocabularies();
		} else {
			$this->_adminData();
		}
	}

	public function beforeRender(Event $event) {
		$this->controller = $event->subject();
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
		$types = $this->Taxonomies->Vocabularies->Types->find('all', array(
			'conditions' => array(
				'Types.plugin' => null,
			),
			'order' => 'Types.alias ASC',
		));
		$this->controller->set('types_for_admin_layout', $types);

		// vocabularies
		$vocabularies = $this->Taxonomies->Vocabularies->find('all', array(
			'conditions' => array(
				'Vocabularies.plugin IS NULL',
			),
			'order' => 'Vocabularies.alias ASC',
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
		$types = $this->Taxonomies->Vocabularies->Types->find('all');
		foreach ($types as $type) {
			$this->typesForLayout[$type->alias] = $type;
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
			$vocabulary = $this->Taxonomies->Vocabularies->find('first', array(
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
				$threaded = $this->Taxonomies->find('threaded', array(
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
 * @throws Exception
 */
	public function prepareCommonData(Type $type, $options = array()) {
		$options = Hash::merge(array(
			'modelClass' => $this->controller->modelClass,
		), $options);
		$typeAlias = $type->alias;
		list(, $modelClass) = pluginSplit($options['modelClass']);

		if (isset($this->controller->{$modelClass})) {
			$Model = $this->controller->{$modelClass};
		} else {
			throw new Exception(sprintf(
				'Model %s not found in controller %s',
				$modelClass, $this->controller->name
			));
		}
		$Model->type = $typeAlias;
		$vocabularies = [];
		collection($type->vocabularies)->each(function (Vocabulary $vocabulary) use (&$vocabularies) {
			$vocabularies[$vocabulary->id] = $vocabulary;
		});
		$taxonomy = array();
		foreach ($type->vocabularies as $vocabulary) {
			$vocabularyId = $vocabulary->id;
			$taxonomy[$vocabularyId] = $Model->Taxonomies->getTree(
				$vocabulary->alias,
				array('taxonomyId' => true)
			);
		}
		$this->controller->set(compact(
			'type', 'typeAlias', 'taxonomy', 'vocabularies'
		));
	}

}
