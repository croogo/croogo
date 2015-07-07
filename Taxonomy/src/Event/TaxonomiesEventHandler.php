<?php

namespace Croogo\Taxonomy\Event;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Croogo\Core\Croogo;
use Croogo\Core\Nav;

/**
 * Taxonomy Event Handler
 *
 * @category Event
 * @package  Croogo.Taxonomy.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesEventHandler implements EventListenerInterface {

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
		$View = $event->subject;

		if (empty($View->viewVars['vocabularies_for_admin_layout'])) {
			$vocabularies = array();
		} else {
			$vocabularies = $View->viewVars['vocabularies_for_admin_layout'];
		}
		foreach ($vocabularies as $v) {
			$weight = 9999 + $v->weight;
			Nav::add('sidebar', 'content.children.taxonomy.children.' . $v->alias, array(
				'title' => $v->title,
				'url' => array(
					'prefix' => 'admin',
					'plugin' => 'Croogo/Taxonomy',
					'controller' => 'Terms',
					'action' => 'index',
					$v->id,
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
		$vocabulariesTable = TableRegistry::get('Croogo/Taxonomy.Vocabularies');
		$vocabularies = $vocabulariesTable->find('all')->contain([
			'Types'
		]);

		$linkChoosers = array();
		foreach ($vocabularies as $vocabulary) {
			foreach ($vocabulary->types as $type) {
				$title = $type->title . ' ' . $vocabulary->title;
				$linkChoosers[$title] = array(
					'description' => $vocabulary->description,
					'url' => array(
						'plugin' => 'Croogo/Taxonomy',
						'controller' => 'Terms',
						'action' => 'index',
						$vocabulary->id,
						'?' => array(
							'type' => $type->alias,
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
		Croogo::mergeConfig('Croogo.linkChoosers', $linkChoosers);
	}

}
