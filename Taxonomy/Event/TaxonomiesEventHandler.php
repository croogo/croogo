<?php

App::uses('CakeEventListener', 'Event');

/**
 * Taxonomy Event Handler
 *
 * @category Event
 * @package  Croogo.Taxonomy.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesEventHandler implements CakeEventListener {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Controller.Links.setupLinkChooser' => array(
				'callable' => 'onSetupLinkChooser',
			),
		);
	}

/**
 * Setup Link chooser values
 *
 * @return void
 */
	public function onSetupLinkChooser($event){
		$this->Vocabulary = ClassRegistry::init('Taxonomy.Vocabulary');
		$vocabularies = $this->Vocabulary->find('all', array(
			'joins' => array(
				array(
					'table' => 'types_vocabularies',
					'alias' => 'TypesVocabulary',
					'conditions' => 'Vocabulary.id = TypesVocabulary.vocabulary_id'
				),
				array(
					'table' => 'types',
					'alias' => 'Type',
					'conditions' => 'TypesVocabulary.type_id = Type.id',
				),
			),
		));

		$linkChoosers = array();
		foreach ($vocabularies as $vocabulary) {
			foreach ($vocabulary['Type'] as $type) {
				$title = $type['title'] . ' ' . $vocabulary['Vocabulary']['title'];
				$linkChoosers[$title] = array(
					'description' => $vocabulary['Vocabulary']['description'],
					'url' => array(
						'plugin' => 'taxonomy',
						'controller' => 'terms',
						'action' => 'index',
						$vocabulary['Vocabulary']['id'],
						'?' => array(
							'type' => $type['alias'],
							'chooser' => 1,
							'KeepThis' => true,
							'TB_iframe' => true,
							'height' => 400,
							'width' => 600,
						),
					),
				);
			}
		}
		Croogo::mergeConfig('Menus.linkChoosers', $linkChoosers);
	}

}
