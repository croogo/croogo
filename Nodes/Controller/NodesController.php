<?php

namespace Croogo\Nodes\Controller;
App::uses('NodesAppController', 'Nodes.Controller');
App::uses('Croogo', 'Lib');

/**
 * Nodes Controller
 *
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
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo.BulkProcess',
		'Croogo.Recaptcha',
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

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

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if (isset($this->request->params['slug'])) {
			$this->request->params['named']['slug'] = $this->request->params['slug'];
		}
		if (isset($this->request->params['type'])) {
			$this->request->params['named']['type'] = $this->request->params['type'];
		}
		$this->Security->unlockedActions[] = 'admin_toggle';
	}

/**
 * Toggle Node status
 *
 * @param string $id Node id
 * @param integer $status Current Node status
 * @return void
 */
	public function admin_toggle($id = null, $status = null) {
		$this->Croogo->fieldToggle($this->{$this->modelClass}, $id, $status);
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Content'));
		$this->Prg->commonProcess();

		$Node = $this->{$this->modelClass};
		$Node->recursive = 0;

		$alias = $this->modelClass;
		$this->paginate[$alias]['order'] = $Node->escapeField('created') . ' DESC';
		$this->paginate[$alias]['conditions'] = array();
		$this->paginate[$alias]['contain'] = array('User');

		$types = $Node->Taxonomy->Vocabulary->Type->find('all');
		$typeAliases = Hash::extract($types, '{n}.Type.alias');
		$this->paginate[$alias]['conditions'][$Node->escapeField('type')] = $typeAliases;

		$criteria = $Node->parseCriteria($this->Prg->parsedParams());
		$nodes = $this->paginate($criteria);
		$nodeTypes = $Node->Taxonomy->Vocabulary->Type->find('list', array(
			'fields' => array('Type.alias', 'Type.title')
			));
		$this->set(compact('nodes', 'types', 'typeAliases', 'nodeTypes'));

		if (isset($this->request->params['named']['links']) || isset($this->request->query['chooser'])) {
			$this->layout = 'admin_popup';
			$this->render('admin_chooser');
		}
	}

/**
 * Admin create
 *
 * @return void
 * @access public
 */
	public function admin_create() {
		$this->set('title_for_layout', __d('croogo', 'Create content'));

		$types = $this->{$this->modelClass}->Taxonomy->Vocabulary->Type->find('all', array(
			'order' => array(
				'Type.alias' => 'ASC',
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
	public function admin_add($typeAlias = 'node') {
		$Node = $this->{$this->modelClass};
		$type = $Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
		if (!isset($type['Type']['alias'])) {
			$this->Session->setFlash(__d('croogo', 'Content type does not exist.'));
			return $this->redirect(array('action' => 'create'));
		}

		if (!empty($this->request->data)) {
			if (isset($this->request->data[$Node->alias]['type'])) {
				$typeAlias = $this->request->data[$Node->alias]['type'];
				$Node->type = $typeAlias;
			}
			if ($Node->saveNode($this->request->data, $typeAlias)) {
				Croogo::dispatchEvent('Controller.Nodes.afterAdd', $this, array('data' => $this->request->data));
				$this->Session->setFlash(__d('croogo', '%s has been saved', $type['Type']['title']), 'default', array('class' => 'success'));
				$this->Croogo->redirect(array('action' => 'edit', $Node->id));
			} else {
				$this->Session->setFlash(__d('croogo', '%s could not be saved. Please, try again.', $type['Type']['title']), 'default', array('class' => 'error'));
			}
		} else {
			$this->Croogo->setReferer();
			$this->request->data[$Node->alias]['user_id'] = $this->Session->read('Auth.User.id');
		}

		$this->set('title_for_layout', __d('croogo', 'Create content: %s', $type['Type']['title']));
		$Node->type = $type['Type']['alias'];
		$Node->Behaviors->attach('Tree', array(
			'scope' => array(
				$Node->escapeField('type') => $Node->type,
			),
		));

		$this->_setCommonVariables($type);
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid content'), 'default', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		$Node = $this->{$this->modelClass};
		$Node->id = $id;
		$typeAlias = $Node->field('type');
		$type = $Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);

		if (!empty($this->request->data)) {
			if ($Node->saveNode($this->request->data, $typeAlias)) {
				Croogo::dispatchEvent('Controller.Nodes.afterEdit', $this, compact('data'));
				$this->Session->setFlash(__d('croogo', '%s has been saved', $type['Type']['title']), 'default', array('class' => 'success'));
				$this->Croogo->redirect(array('action' => 'edit', $Node->id));
			} else {
				$this->Session->setFlash(__d('croogo', '%s could not be saved. Please, try again.', $type['Type']['title']), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->Croogo->setReferer();
			$data = $Node->read(null, $id);
			$data['Role']['Role'] = $Node->decodeData($data[$Node->alias]['visibility_roles']);
			$this->request->data = $data;
		}

		$this->set('title_for_layout', __d('croogo', 'Edit %s: %s', $type['Type']['title'], $this->request->data[$Node->alias]['title']));
		$this->_setCommonVariables($type);
	}

/**
 * Admin update paths
 *
 * @return void
 * @access public
 */
	public function admin_update_paths() {
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
	public function admin_delete($id = null) {
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
	public function admin_delete_meta($id = null) {
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
	public function admin_add_meta() {
		$this->layout = 'ajax';
	}

/**
 * Admin process
 *
 * @return void
 * @access public
 */
	public function admin_process() {
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
 * Index
 *
 * @return void
 * @access public
 */
	public function index() {
		if (!isset($this->request->params['named']['type'])) {
			$this->request->params['named']['type'] = 'node';
		}

		$Node = $this->{$this->modelClass};
		$this->paginate[$Node->alias]['order'] = $Node->escapeField('created') . ' DESC';
		$visibilityRolesField = $Node->escapeField('visibility_roles');
		$this->paginate[$Node->alias]['conditions'] = array(
			$Node->escapeField('status') => $Node->status(),
			'OR' => array(
				$visibilityRolesField => '',
				$visibilityRolesField . ' LIKE' => '%"' . $this->Croogo->roleId() . '"%',
			),
		);

		if (isset($this->request->params['named']['limit'])) {
			$limit = $this->request->params['named']['limit'];
		} else {
			$limit = Configure::read('Reading.nodes_per_page');
		}

		$this->paginate[$Node->alias]['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);
		if (isset($this->request->params['named']['type'])) {
			$type = $Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->request->params['named']['type'],
				),
				'cache' => array(
					'name' => 'type_' . $this->request->params['named']['type'],
					'config' => 'nodes_index',
				),
			));
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__d('croogo', 'Invalid content type.'), 'default', array('class' => 'error'));
				return $this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page']) && empty($this->request->params['named']['limit'])) {
				$limit = $type['Params']['nodes_per_page'];
			}
			$this->paginate[$Node->alias]['conditions']['Node.type'] = $type['Type']['alias'];
			$this->set('title_for_layout', $type['Type']['title']);
		}

		$this->paginate[$Node->alias]['limit'] = $limit;

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_index_' . $this->Croogo->roleId() . '_' . Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_' . $type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix . '_' . $this->request->params['named']['type'] . '_' . $this->paginate['page'] . '_' . $limit;
			$cacheNamePaging = $cacheNamePrefix . '_' . $this->request->params['named']['type'] . '_' . $this->paginate['page'] . '_' . $limit . '_paging';
			$cacheConfig = 'nodes_index';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate($Node->alias);
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->request->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->request->params['paging'] = $paging;
			}
		} else {
			$nodes = $this->paginate($Node->alias);
		}

		$this->set(compact('type', 'nodes'));
		$this->Croogo->viewFallback(array(
			'index_' . $type['Type']['alias'],
		));
	}

/**
 * Term
 *
 * @return void
 * @access public
 */
	public function term() {
		$Node = $this->{$this->modelClass};
		$term = $Node->Taxonomy->Term->find('first', array(
			'conditions' => array(
				'Term.slug' => $this->request->params['named']['slug'],
			),
			'cache' => array(
				'name' => 'term_' . $this->request->params['named']['slug'],
				'config' => 'nodes_term',
			),
		));
		if (!isset($term['Term']['id'])) {
			$this->Session->setFlash(__d('croogo', 'Invalid Term.'), 'default', array('class' => 'error'));
			return $this->redirect('/');
		}

		if (!isset($this->request->params['named']['type'])) {
			$this->request->params['named']['type'] = 'node';
		}

		if (isset($this->request->params['named']['limit'])) {
			$limit = $this->request->params['named']['limit'];
		} else {
			$limit = Configure::read('Reading.nodes_per_page');
		}

		$this->paginate[$Node->alias]['order'] = $Node->escapeField('created') .' DESC';
		$visibilityRolesField = $Node->escapeField('visibility_roles');
		$this->paginate[$Node->alias]['conditions'] = array(
			$Node->escapeField('status') => $Node->status(),
			$Node->escapeField('terms') . ' LIKE' => '%"' . $this->request->params['named']['slug'] . '"%',
			'OR' => array(
				$visibilityRolesField => '',
				$visibilityRolesField . ' LIKE' => '%"' . $this->Croogo->roleId() . '"%',
			),
		);
		$this->paginate[$Node->alias]['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);
		if (isset($this->request->params['named']['type'])) {
			$type = $Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->request->params['named']['type'],
				),
				'cache' => array(
					'name' => 'type_' . $this->request->params['named']['type'],
					'config' => 'nodes_term',
				),
			));
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__d('croogo', 'Invalid content type.'), 'default', array('class' => 'error'));
				return $this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page']) && empty($this->request->params['named']['limit'])) {
				$limit = $type['Params']['nodes_per_page'];
			}
			$this->paginate[$Node->alias]['conditions'][$Node->escapeField('type')] = $type['Type']['alias'];
			$this->set('title_for_layout', $term['Term']['title']);
		}

		$this->paginate[$Node->alias]['limit'] = $limit;

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_term_' . $this->Croogo->roleId() . '_' . $this->request->params['named']['slug'] . '_' . Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_' . $type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit;
			$cacheNamePaging = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit . '_paging';
			$cacheConfig = 'nodes_term';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate($Node->alias);
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->request->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->request->params['paging'] = $paging;
			}
		} else {
			$nodes = $this->paginate($Node->alias);
		}

		$this->set(compact('term', 'type', 'nodes'));
		$this->Croogo->viewFallback(array(
			'term_' . $term['Term']['id'],
			'term_' . $type['Type']['alias'],
		));
	}

/**
 * Promoted
 *
 * @return void
 * @access public
 */
	public function promoted() {
		$Node = $this->{$this->modelClass};
		$this->set('title_for_layout', __d('croogo', 'Home'));

		$roleId = $this->Croogo->roleId();
		$this->paginate[$Node->alias]['type'] = 'promoted';
		$visibilityRolesField = $Node->escapeField('visibility_roles');
		$this->paginate[$Node->alias]['conditions'] = array(
			'OR' => array(
				$visibilityRolesField => '',
				$visibilityRolesField . ' LIKE' => '%"' . $roleId . '"%',
			),
		);

		if (isset($this->request->params['named']['limit'])) {
			$limit = $this->request->params['named']['limit'];
		} else {
			$limit = Configure::read('Reading.nodes_per_page');
		}

		if (isset($this->request->params['named']['type'])) {
			$type = $Node->Taxonomy->Vocabulary->Type->findByAlias($this->request->params['named']['type']);
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__d('croogo', 'Invalid content type.'), 'default', array('class' => 'error'));
				return $this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page']) && empty($this->request->params['named']['limit'])) {
				$limit = $type['Params']['nodes_per_page'];
			}
			$this->paginate[$Node->alias]['conditions'][$Node->escapeField('type')] = $type['Type']['alias'];
			$this->set('title_for_layout', $type['Type']['title']);
			$this->set(compact('type'));
		}

		$this->paginate[$Node->alias]['limit'] = $limit;

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_promoted_' . $this->Croogo->roleId() . '_' . Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_' . $type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit;
			$cacheNamePaging = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit . '_paging';
			$cacheConfig = 'nodes_promoted';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate($Node->alias);
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->request->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->request->params['paging'] = $paging;
			}
		} else {
			$nodes = $this->paginate($Node->alias);
		}
		$this->set(compact('nodes'));
	}

/**
 * Search
 *
 * @param string $typeAlias
 * @return void
 * @access public
 */
	public function search($typeAlias = null) {
		$this->Prg->commonProcess();

		$Node = $this->{$this->modelClass};

		$this->paginate = array(
			'published',
			'roleId' => $this->Croogo->roleId(),
		);

		$q = null;
		if (isset($this->request->query['q'])) {
			$q = $this->request->query['q'];
			$this->paginate['q'] = $q;
		}

		if ($typeAlias) {
			$type = $Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__d('croogo', 'Invalid content type.'), 'default', array('class' => 'error'));
				return $this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page'])) {
				$this->paginate['limit'] = $type['Params']['nodes_per_page'];
			}
			$this->paginate['typeAlias'] = $typeAlias;
		}

		$criteria = $Node->parseCriteria($this->Prg->parsedParams());
		$nodes = $this->paginate($criteria);
		$this->set(compact('q', 'nodes'));
		if ($typeAlias) {
			$this->Croogo->viewFallback(array(
				'search_' . $typeAlias,
			));
		}
	}

/**
 * View
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function view($id = null) {
		$Node = $this->{$this->modelClass};
		if (isset($this->request->params['named']['slug']) && isset($this->request->params['named']['type'])) {
			$Node->type = $this->request->params['named']['type'];
			$type = $Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $Node->type,
				),
				'cache' => array(
					'name' => 'type_' . $Node->type,
					'config' => 'nodes_view',
				),
			));
			$node = $Node->find('viewBySlug', array(
				'slug' => $this->request->params['named']['slug'],
				'type' => $this->request->params['named']['type'],
				'roleId' => $this->Croogo->roleId(),
			));
		} elseif ($id == null) {
			$this->Session->setFlash(__d('croogo', 'Invalid content'), 'default', array('class' => 'error'));
			return $this->redirect('/');
		} else {
			$node = $Node->find('viewById', array(
				'id' => $id,
				'roleId' => $this->Croogo->roleId,
			));
			$Node->type = $node[$Node->alias]['type'];
			$type = $Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $Node->type,
				),
				'cache' => array(
					'name' => 'type_' . $Node->type,
					'config' => 'nodes_view',
				),
			));
		}

		if (!isset($node[$Node->alias][$Node->primaryKey])) {
			$this->Session->setFlash(__d('croogo', 'Invalid content'), 'default', array('class' => 'error'));
			return $this->redirect('/');
		}

		$data = $node;
		$event = new CakeEvent('Controller.Nodes.view', $this, compact('data'));
		$this->getEventManager()->dispatch($event);

		$this->set('title_for_layout', $node[$Node->alias]['title']);
		$this->set(compact('node', 'type', 'comments'));
		$this->Croogo->viewFallback(array(
			'view_' . $type['Type']['alias'] . '_' . $node[$Node->alias]['slug'],
			'view_' . $node[$Node->alias][$Node->primaryKey],
			'view_' . $type['Type']['alias'],
		));
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
		$Node = $this->{$this->modelClass};
		if (!empty($this->data[$Node->alias]['parent_id'])) {
			$Node->id = $this->data[$Node->alias]['parent_id'];
			$parentTitle = $Node->field('title');
		}
		$roles = $Node->User->Role->find('list');
		$this->set(compact('parentTitle', 'roles'));
	}

}
