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
			'Croogo.setupAdminData' => array(
				'callable' => 'onSetupAdminData',
			),
			'Controller.Links.setupLinkChooser' => array(
				'callable' => 'onSetupLinkChooser',
			),
		);
	}

/**
 * Setup admin data
 */
	public function onSetupAdminData($event) {
		CroogoNav::add('sidebar', 'content.children.content_types', array(
			'title' => __d('croogo', 'Content Types'),
			'url' => array(
				'plugin' => 'taxonomy',
				'admin' => true,
				'controller' => 'types',
				'action' => 'index',
			),
			'weight' => 30,
		));

		CroogoNav::add('sidebar', 'content.children.taxonomy', array(
			'title' => __d('croogo', 'Taxonomy'),
			'url' => array(
				'plugin' => 'taxonomy',
				'admin' => true,
				'controller' => 'vocabularies',
				'action' => 'index',
			),
			'weight' => 40,
			'children' => array(
				'list' => array(
					'title' => __d('croogo', 'List'),
						'url' => array(
						'plugin' => 'taxonomy',
						'admin' => true,
						'controller' => 'vocabularies',
						'action' => 'index',
					),
					'weight' => 10,
				),
				'add_new' => array(
					'title' => __d('croogo', 'Add new'),
					'url' => array(
						'plugin' => 'taxonomy',
						'admin' => true,
						'controller' => 'vocabularies',
						'action' => 'add',
					),
					'weight' => 20,
					'htmlAttributes' => array('class' => 'separator'),
				)
			)
		));

		$View = $event->subject;

		if (empty($View->viewVars['types_for_admin_layout'])) {
			$types = array();
		} else {
			$types = $View->viewVars['types_for_admin_layout'];
		}
		foreach ($types as $t) {
			CroogoNav::add('sidebar', 'content.children.create.children.' . $t['Type']['alias'], array(
				'title' => $t['Type']['title'],
				'url' => array(
					'plugin' => 'nodes',
					'admin' => true,
					'controller' => 'nodes',
					'action' => 'add',
					$t['Type']['alias'],
				),
			));
		};

		if (empty($View->viewVars['vocabularies_for_admin_layout'])) {
			$vocabularies = array();
		} else {
			$vocabularies = $View->viewVars['vocabularies_for_admin_layout'];
		}
		foreach ($vocabularies as $v) {
			$weight = 9999 + $v['Vocabulary']['weight'];
			CroogoNav::add('sidebar', 'content.children.taxonomy.children.' . $v['Vocabulary']['alias'], array(
				'title' => $v['Vocabulary']['title'],
				'url' => array(
					'plugin' => 'taxonomy',
					'admin' => true,
					'controller' => 'terms',
					'action' => 'index',
					$v['Vocabulary']['id'],
				),
				'weight' => $weight,
			));
		};
	}

/**
 * Setup Link chooser values
 *
 * @return void
 */
	public function onSetupLinkChooser($event) {
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
