<?php

namespace Croogo\Nodes\Event;

use Cake\Core\Plugin;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Croogo\Croogo\Croogo;
use Croogo\Comments\Model\Comment;

/**
 * Nodes Event Handler
 *
 * @category Event
 * @package  Croogo.Nodes.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesEventHandler implements EventListenerInterface {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Croogo.bootstrapComplete' => array(
				'callable' => 'onBootstrapComplete',
			),
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

		if (empty($View->viewVars['types_for_admin_layout'])) {
			$types = array();
		} else {
			$types = $View->viewVars['types_for_admin_layout'];
		}
		foreach ($types as $t) {
			if (!empty($t['Type']['plugin'])) {
				continue;
			}
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
	}

/**
 * onBootstrapComplete
 */
	public function onBootstrapComplete($event) {
		if (Plugin::loaded('Comments')) {
						Croogo::hookBehavior('Node', 'Comments.Commentable');
			Croogo::hookComponent('Nodes', 'Comments.Comments');
			Croogo::hookModelProperty('Comment', 'belongsTo', array(
				'Node' => array(
					'className' => 'Nodes.Node',
					'foreignKey' => 'foreign_key',
					'counterCache' => true,
					'counterScope' => array(
						'Comment.model' => 'Node',
						'Comment.status' => Comment::STATUS_APPROVED,
					),
				),
			));
		}
		if (Plugin::loaded('Croogo/Taxonomy')) {
			Croogo::hookBehavior('Croogo/Nodes.Nodes', 'Croogo/Taxonomy.Taxonomizable');
		}
		if (Plugin::loaded('Meta')) {
			Croogo::hookBehavior('Node', 'Meta.Meta');
		}
	}

/**
 * Setup Link chooser values
 *
 * @return void
 */
	public function onSetupLinkChooser($event) {
		$typesTable = TableRegistry::get('Croogo/Taxonomy.Types');
		$types = $typesTable->find('all', array(
			'fields' => array('alias', 'title', 'description'),
		));
		$linkChoosers = array();
		foreach ($types as $type) {
			$linkChoosers[$type->title] = array(
				'title' => $type->title,
				'description' => $type->description,
				'url' => array(
					'plugin' => 'Croogo/Nodes',
					'controller' => 'Nodes',
					'action' => 'index',
					'?' => array(
						'type' => $type->alias,
						'chooser' => 1,
						'KeepThis' => true,
						'TB_iframe' => true,
						'height' => 400,
						'width' => 600
					)
				)
			);
		}
		Croogo::mergeConfig('Menus.linkChoosers', $linkChoosers);
	}

}
