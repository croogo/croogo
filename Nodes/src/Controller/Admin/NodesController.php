<?php

namespace Croogo\Nodes\Controller\Admin;

use Cake\Event\Event;

use Croogo\Croogo\Controller\Component\CroogoComponent;
use Croogo\Croogo\Croogo;
use Croogo\Nodes\Controller\NodesAppController;
use Croogo\Nodes\Model\Entity\Node;
use Croogo\Nodes\Model\Table\NodesTable;
use Croogo\Taxonomy\Controller\Component\TaxonomiesComponent;

/**
 * Nodes Controller
 *
 * @property NodesTable Nodes
 * @property CroogoComponent Croogo
 * @property TaxonomiesComponent Taxonomies
 * @category Nodes.Controller
 * @package  Croogo.Nodes
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesController extends NodesAppController {

	/**
	 * Controller name
	 *
	 * @var string
	 * @access public
	 */
	public $name = 'Nodes';

	/**
	 * Preset Variable Search
	 *
	 * @var array
	 * @access public
	 */
	public $presetVars = true;

	/**
	 * Models used by the Controller
	 *
	 * @var array
	 * @access public
	 */
	public $uses = array(
		'Nodes.Node',
	);

	/**
	 * afterConstruct
	 */
	public function afterConstruct() {
		parent::afterConstruct();
		$this->_setupAclComponent();
	}

	public function initialize() {
		parent::initialize();

		$this->loadComponent('Croogo/Croogo.BulkProcess');
		$this->loadComponent('Croogo/Croogo.Recaptcha');
		$this->loadComponent('Search.Prg', [
			'presetForm' => [
				'paramType' => 'querystring',
			],
			'commonProcess' => [
				'paramType' => 'querystring',
				'filterEmpty' => true,
			],
		]);
	}

	/**
	 * beforeFilter
	 *
	 * @return void
	 * @access public
	 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);

		if (isset($this->request->params['slug'])) {
			$this->request->params['named']['slug'] = $this->request->params['slug'];
		}
		if (isset($this->request->params['type'])) {
			$this->request->params['named']['type'] = $this->request->params['type'];
		}
		$this->Security->config('unlockedActions', 'admin_toggle');
	}

	/**
	 * Toggle Node status
	 *
	 * @param string $id Node id
	 * @param integer $status Current Node status
	 * @return void
	 */
	public function toggle($id = null, $status = null) {
		$this->Croogo->fieldToggle($this->Nodes, $id, $status);
	}

	/**
	 * Admin index
	 *
	 * @return void
	 * @access public
	 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Content'));
		$this->Prg->commonProcess();

		$this->paginate = [
			'order' => [
				'created' => 'DESC'
			],
			'contain' => [
				'Users'
			]
		];

		$findQuery = $this->Nodes->find('searchable', $this->Prg->parsedParams());

		$types = $this->Nodes->Taxonomies->Vocabularies->Types->find('all');
		$typeAliases = collection($types)->extract('alias');
		$findQuery->where(['type IN' => $typeAliases->toArray()]);

		$nodes = $this->paginate($findQuery);

		$nodeTypes = $this->Nodes->Taxonomies->Vocabularies->Types->find('list', [
			'keyField' => 'alias',
			'valueField' => 'title'
		])->toArray();
		$this->set(compact('nodes', 'types', 'typeAliases', 'nodeTypes'));

		if (isset($this->request->params['named']['links']) || isset($this->request->query['chooser'])) {
			$this->layout = 'Croogo/Croogo.admin_popup';
			$this->view = 'chooser';
		}
	}

	/**
	 * Admin create
	 *
	 * @return void
	 * @access public
	 */
	public function create() {
		$types = $this->Nodes->Taxonomies->Vocabularies->Types->find('all', array(
			'order' => array(
				'Types.alias' => 'ASC',
			),
		));
		$this->set(compact('types'));
	}

	/**
	 * Admin add
	 *
	 * @param string $typeAlias
	 * @return void
	 * @access public
	 */
	public function add($typeAlias = 'node') {
		$type = $this->Nodes->Taxonomies->Vocabularies->Types->findByAlias($typeAlias)->contain('Vocabularies')->first();
		if (!isset($type->alias)) {
			$this->Flash->error(__d('croogo', 'Content type does not exist.'));
			return $this->redirect(array('action' => 'create'));
		}

		/** @var Node $node */
		$node = $this->Nodes->newEntity([
			'type' => $type->alias,
			'user_id' => $this->Auth->user('id')
		]);

		if (!empty($this->request->data)) {
			$this->Nodes->patchEntity($node, $this->request->data);

			$node = $this->Nodes->saveNode($node, $typeAlias);
			if ($node) {
				Croogo::dispatchEvent('Controller.Nodes.afterAdd', $this, compact('node'));
				$this->Flash->success(__d('croogo', '%s has been saved', $type->title));
				$this->Croogo->redirect(['action' => 'edit', $node->id]);
			} else {
				$this->Flash->error(__d('croogo', '%s could not be saved. Please, try again.', $type->title));
			}
		} else {
			$this->Croogo->setReferer();
		}

		$this->set(compact('node'));

		$this->Nodes->removeBehavior('Tree');
		$this->Nodes->addBehavior('Tree', [
			'scope' => [
				'Nodes.type' => $node->type,
			],
		]);

		$this->_setCommonVariables($type);
	}

	/**
	 * Admin edit
	 *
	 * @param integer $id
	 * @return void
	 * @access public
	 */
	public function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid content'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		$node = $this->Nodes->get($id, [
			'contain' => [
				'Users'
			]
		]);
		$typeAlias = $node->type;
		$type = $this->Nodes->Taxonomies->Vocabularies->Types->findByAlias($typeAlias)->contain('Vocabularies')->first();

		if (!empty($this->request->data)) {
			$node = $this->Nodes->patchEntity($node, $this->request->data);
			if ($this->Nodes->saveNode($node, $typeAlias)) {
				Croogo::dispatchEvent('Controller.Nodes.afterEdit', $this, compact('data'));
				$this->Flash->success(__d('croogo', '%s has been saved', $type->title));
				$this->Croogo->redirect(array('action' => 'edit', $node->id));
			} else {
				$this->Flash->error(__d('croogo', '%s could not be saved. Please, try again.', $type['Type']['title']));
			}
		}
		if (empty($this->request->data)) {
			$this->Croogo->setReferer();
			$node->role = $this->Nodes->decodeData($node->visibility_roles);
//			$this->request->data = $node;
		}

		$this->set(compact('node'));

		$this->set('title_for_layout', __d('croogo', 'Edit %s: %s', $type->title, $node->title));
		$this->_setCommonVariables($type);
	}

	/**
	 * Admin update paths
	 *
	 * @return void
	 * @access public
	 */
	public function update_paths() {
		$Node = $this->{$this->modelClass};
		if ($Node->updateAllNodesPaths()) {
			$messageFlash = __d('croogo', 'Paths updated.');
			$class = 'success';
		} else {
			$messageFlash = __d('croogo', 'Something went wrong while updating paths.' . "\n" . 'Please try again');
			$class = 'error';
		}

		$this->Session->setFlash($messageFlash, 'default', compact('class'));
		return $this->redirect(array('action' => 'index'));
	}

	/**
	 * Admin delete
	 *
	 * @param integer $id
	 * @return void
	 * @access public
	 */
	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Node'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		$Node = $this->{$this->modelClass};
		if ($Node->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Node deleted'), 'default', array('class' => 'success'));
			return $this->redirect(array('action' => 'index'));
		}
	}

	/**
	 * Admin delete meta
	 *
	 * @param integer $id
	 * @return void
	 * @access public
	 * @deprecated Use MetaController::admin_delete_meta()
	 */
	public function delete_meta($id = null) {
		$success = false;
		$Node = $this->{$this->modelClass};
		if ($id != null && $Node->Meta->delete($id)) {
			$success = true;
		} else {
			if (!$Node->Meta->exists($id)) {
				$success = true;
			}
		}

		$success = array('success' => $success);
		$this->set(compact('success'));
		$this->set('_serialize', 'success');
	}

	/**
	 * Admin add meta
	 *
	 * @return void
	 * @access public
	 * @deprecated Use MetaController::admin_add_meta()
	 */
	public function add_meta() {
		$this->layout = 'ajax';
	}

	/**
	 * Admin process
	 *
	 * @return void
	 * @access public
	 */
	public function process() {
		$Node = $this->{$this->modelClass};
		list($action, $ids) = $this->BulkProcess->getRequestVars($Node->alias);

		$options = array(
			'multiple' => array('copy' => false),
			'messageMap' => array(
				'delete' => __d('croogo', 'Nodes deleted'),
				'publish' => __d('croogo', 'Nodes published'),
				'unpublish' => __d('croogo', 'Nodes unpublished'),
				'promote' => __d('croogo', 'Nodes promoted'),
				'unpromote' => __d('croogo', 'Nodes unpromoted'),
				'copy' => __d('croogo', 'Nodes copied'),
			),
		);
		return $this->BulkProcess->process($Node, $action, $ids, $options);
	}

	/**
	 * View Fallback
	 *
	 * @param mixed $views
	 * @return string
	 * @access protected
	 * @deprecated Use CroogoComponent::viewFallback()
	 */
	protected function _viewFallback($views) {
		return $this->Croogo->viewFallback($views);
	}

	/**
	 * Set common form variables to views
	 * @param array $type Type data
	 * @return void
	 */
	protected function _setCommonVariables($type) {
		if (isset($this->Taxonomies)) {
			$this->Taxonomies->prepareCommonData($type);
		}
//		$Node = $this->{$this->modelClass};
//		if (!empty($this->data[$Node->alias]['parent_id'])) {
//			$Node->id = $this->data[$Node->alias]['parent_id'];
//			$parentTitle = $Node->field('title');
//		}
//		$roles = $Node->User->Role->find('list');
//		$this->set(compact('parentTitle', 'roles'));
	}

}
