<?php
/**
 * Nodes Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesController extends AppController {
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
		'Recaptcha',
	);

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array(
		'Node',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		if (isset($this->params['slug'])) {
			$this->params['named']['slug'] = $this->params['slug'];
		}
		if (isset($this->params['type'])) {
			$this->params['named']['type'] = $this->params['type'];
		}

		// CSRF Protection
		if (in_array($this->params['action'], array('admin_add', 'admin_edit'))) {
			$this->Security->validatePost = false;
		}
	}

	public function admin_index() {
		$this->set('title_for_layout', __('Content', true));

		$this->Node->recursive = 0;
		$this->paginate['Node']['order'] = 'Node.created DESC';
		$this->paginate['Node']['conditions'] = array();

		$types = $this->Node->Taxonomy->Vocabulary->Type->find('all');
		$typeAliases = Set::extract('/Type/alias', $types);
		$this->paginate['Node']['conditions']['Node.type'] = $typeAliases;

		if (isset($this->params['named']['filter'])) {
			$filters = $this->Croogo->extractFilter();
			foreach ($filters AS $filterKey => $filterValue) {
				if (strpos($filterKey, '.') === false) {
					$filterKey = 'Node.' . $filterKey;
				}
				$this->paginate['Node']['conditions'][$filterKey] = $filterValue;
			}
			$this->set('filters', $filters);
		}

		if (isset($this->params['named']['q'])) {
			App::import('Core', 'Sanitize');
			$q=Sanitize::clean($this->params['named']['q']);
			$this->paginate['Node']['conditions']['OR'] = array(
				'Node.title LIKE' => '%' . $q . '%',
				'Node.excerpt LIKE' => '%' . $q . '%',
				'Node.body LIKE' => '%' . $q . '%',
				'Node.terms LIKE' => '%"' . $q . '"%',
			);
		}

		$nodes = $this->paginate('Node');
		$this->set(compact('nodes', 'types', 'typeAliases'));

		if (isset($this->params['named']['links'])) {
			$this->layout = 'ajax';
			$this->render('admin_links');
		}
	}

	public function admin_create() {
		$this->set('title_for_layout', __('Create content', true));

		$types = $this->Node->Taxonomy->Vocabulary->Type->find('all', array(
			'order' => array(
				'Type.alias' => 'ASC',
			),
		));
		$this->set(compact('types'));
	}

	public function admin_add($typeAlias = 'node') {
		$type = $this->Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
		if (!isset($type['Type']['alias'])) {
			$this->Session->setFlash(__('Content type does not exist.', true));
			$this->redirect(array('action' => 'create'));
		}

		$this->set('title_for_layout', sprintf(__('Create content: %s', true), $type['Type']['title']));
		$this->Node->type = $type['Type']['alias'];
		$this->Node->Behaviors->attach('Tree', array(
			'scope' => array(
				'Node.type' => $this->Node->type,
			),
		));

		if (!empty($this->data)) {
			// CSRF Protection
			if ($this->params['_Token']['key'] != $this->data['Node']['token_key']) {
				$blackHoleCallback = $this->Security->blackHoleCallback;
				$this->$blackHoleCallback();
			}

			if (isset($this->data['TaxonomyData'])) {
				$this->data['Taxonomy'] = array(
					'Taxonomy' => array(),
				);
				foreach ($this->data['TaxonomyData'] AS $vocabularyId => $taxonomyIds) {
					if (is_array($taxonomyIds)) {
						$this->data['Taxonomy']['Taxonomy'] = array_merge($this->data['Taxonomy']['Taxonomy'], $taxonomyIds);
					}
				}
			}
			$this->Node->create();
			$this->data['Node']['path'] = $this->Croogo->getRelativePath(array(
				'admin' => false,
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $this->Node->type,
				'slug' => $this->data['Node']['slug'],
			));
			$this->data['Node']['visibility_roles'] = $this->Node->encodeData($this->data['Role']['Role']);
			if ($this->Node->saveWithMeta($this->data)) {
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $type['Type']['title']), 'default', array('class' => 'success'));
				if (isset($this->params['form']['apply'])) {
					$this->redirect(array('action'=>'edit', $this->Node->id));
				} else {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $type['Type']['title']), 'default', array('class' => 'error'));
			}
		} else {
			$this->data['Node']['user_id'] = $this->Session->read('Auth.User.id');
		}

		$nodes = $this->Node->generatetreelist();
		$roles   = $this->Node->User->Role->find('list');
		$users = $this->Node->User->find('list');
		$vocabularies = Set::combine($type['Vocabulary'], '{n}.id', '{n}');
		$taxonomy = array();
		foreach ($type['Vocabulary'] AS $vocabulary) {
			$vocabularyId = $vocabulary['id'];
			$taxonomy[$vocabularyId] = $this->Node->Taxonomy->getTree($vocabulary['alias'], array('taxonomyId' => true));
		}
		$this->set(compact('typeAlias', 'type', 'nodes', 'roles', 'vocabularies', 'taxonomy', 'users'));
	}

	public function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid content', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}

		$this->Node->id = $id;
		$typeAlias = $this->Node->field('type');

		$type = $this->Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
		if (!isset($type['Type']['alias'])) {
			$this->Session->setFlash(__('Content type does not exist.', true), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'create'));
		}

		$this->set('title_for_layout', sprintf(__('Edit content: %s', true), $type['Type']['title']));
		$this->Node->type = $type['Type']['alias'];
		$this->Node->Behaviors->attach('Tree', array('scope' => array('Node.type' => $this->Node->type)));

		if (!empty($this->data)) {
			// CSRF Protection
			if ($this->params['_Token']['key'] != $this->data['Node']['token_key']) {
				$blackHoleCallback = $this->Security->blackHoleCallback;
				$this->$blackHoleCallback();
			}

			if (isset($this->data['TaxonomyData'])) {
				$this->data['Taxonomy'] = array(
					'Taxonomy' => array(),
				);
				foreach ($this->data['TaxonomyData'] AS $vocabularyId => $taxonomyIds) {
					if (is_array($taxonomyIds)) {
						$this->data['Taxonomy']['Taxonomy'] = array_merge($this->data['Taxonomy']['Taxonomy'], $taxonomyIds);
					}
				}
			}
			$this->data['Node']['path'] = $this->Croogo->getRelativePath(array(
				'admin' => false,
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $this->Node->type,
				'slug' => $this->data['Node']['slug'],
			));
			$this->data['Node']['visibility_roles'] = $this->Node->encodeData($this->data['Role']['Role']);
			if ($this->Node->saveWithMeta($this->data)) {
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $type['Type']['title']), 'default', array('class' => 'success'));
				if (isset($this->params['form']['apply'])) {
					$this->redirect(array('action'=>'edit', $this->Node->id));
				} else {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(sprintf(__('%s could not be saved. Please, try again.', true), $type['Type']['title']), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->data)) {
			$data = $this->Node->read(null, $id);
			$data['Role']['Role'] = $this->Node->decodeData($data['Node']['visibility_roles']);
			$this->data = $data;
		}

		$nodes = $this->Node->generatetreelist();
		$roles   = $this->Node->User->Role->find('list');
		$users = $this->Node->User->find('list');
		$vocabularies = Set::combine($type['Vocabulary'], '{n}.id', '{n}');
		$taxonomy = array();
		foreach ($type['Vocabulary'] AS $vocabulary) {
			$vocabularyId = $vocabulary['id'];
			$taxonomy[$vocabularyId] = $this->Node->Taxonomy->getTree($vocabulary['alias'], array('taxonomyId' => true));
		}
		$this->set(compact('typeAlias', 'type', 'nodes', 'roles', 'vocabularies', 'taxonomy', 'users'));
	}

	public function admin_update_paths() {
		$types = $this->Node->Taxonomy->Vocabulary->Type->find('list', array(
			'fields' => array(
				'Type.id',
				'Type.alias',
			),
		));
		$typesAlias = array_values($types);

		$nodes = $this->Node->find('all', array(
			'conditions' => array(
				'Node.type' => $typesAlias,
			),
			'fields' => array(
				'Node.id',
				'Node.slug',
				'Node.type',
				'Node.path',
			),
			'recursive' => '-1',
		));
		foreach ($nodes AS $node) {
			$node['Node']['path'] = $this->Croogo->getRelativePath(array(
				'admin' => false,
				'controller' => 'nodes',
				'action' => 'view',
				'type' => $node['Node']['type'],
				'slug' => $node['Node']['slug'],
			));
			$this->Node->id = false;
			$this->Node->save($node);
		}

		$this->Session->setFlash(__('Paths updated.', true), 'default', array('class' => 'success'));
		$this->redirect(array('action' => 'index'));
	}

	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Node', true), 'default', array('class' => 'error'));
			$this->redirect(array('action'=>'index'));
		}
		if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
			$blackHoleCallback = $this->Security->blackHoleCallback;
			$this->$blackHoleCallback();
		}
		if ($this->Node->delete($id)) {
			$this->Session->setFlash(__('Node deleted', true), 'default', array('class' => 'success'));
			$this->redirect(array('action'=>'index'));
		}
	}

	public function admin_delete_meta($id = null) {
		$success = false;
		if ($id != null && $this->Node->Meta->delete($id)) {
			$success = true;
		}

		$this->set(compact('success'));
	}

	public function admin_add_meta() {
		$this->layout = 'ajax';
	}

	public function admin_process() {
		$action = $this->data['Node']['action'];
		$ids = array();
		foreach ($this->data['Node'] AS $id => $value) {
			if ($id != 'action' && $value['id'] == 1) {
				$ids[] = $id;
			}
		}

		if (count($ids) == 0 || $action == null) {
			$this->Session->setFlash(__('No items selected.', true), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($action == 'delete' &&
			$this->Node->deleteAll(array('Node.id' => $ids), true, true)) {
			$this->Session->setFlash(__('Nodes deleted.', true), 'default', array('class' => 'success'));
		} elseif ($action == 'publish' &&
			$this->Node->updateAll(array('Node.status' => 1), array('Node.id' => $ids))) {
			$this->Session->setFlash(__('Nodes published', true), 'default', array('class' => 'success'));
		} elseif ($action == 'unpublish' &&
			$this->Node->updateAll(array('Node.status' => 0), array('Node.id' => $ids))) {
			$this->Session->setFlash(__('Nodes unpublished', true), 'default', array('class' => 'success'));
		} elseif ($action == 'promote' &&
			$this->Node->updateAll(array('Node.promote' => 1), array('Node.id' => $ids))) {
			$this->Session->setFlash(__('Nodes promoted', true), 'default', array('class' => 'success'));
		} elseif ($action == 'unpromote' &&
			$this->Node->updateAll(array('Node.promote' => 0), array('Node.id' => $ids))) {
			$this->Session->setFlash(__('Nodes unpromoted', true), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('An error occurred.', true), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

	public function index() {
		if (!isset($this->params['named']['type'])) {
			$this->params['named']['type'] = 'node';
		}

		$this->paginate['Node']['order'] = 'Node.created DESC';
		$this->paginate['Node']['limit'] = Configure::read('Reading.nodes_per_page');
		$this->paginate['Node']['conditions'] = array(
			'Node.status' => 1,
			'OR' => array(
				'Node.visibility_roles' => '',
				'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
			),
		);
		$this->paginate['Node']['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);
		if (isset($this->params['named']['type'])) {
			$type = $this->Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->params['named']['type'],
				),
				'cache' => array(
					'name' => 'type_'.$this->params['named']['type'],
					'config' => 'nodes_index',
				),
			));
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__('Invalid content type.', true), 'default', array('class' => 'error'));
				$this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page'])) {
				$this->paginate['Node']['limit'] = $type['Params']['nodes_per_page'];
			}
			$this->paginate['Node']['conditions']['Node.type'] = $type['Type']['alias'];
			$this->set('title_for_layout', $type['Type']['title']);
		}

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_index_'.$this->Croogo->roleId.'_'.Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_'.$type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->params['named']['page']) ? $this->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix.'_'.$this->params['named']['type'].'_'.$this->paginate['page'].'_'.$this->paginate['Node']['limit'];
			$cacheNamePaging = $cacheNamePrefix.'_'.$this->params['named']['type'].'_'.$this->paginate['page'].'_'.$this->paginate['Node']['limit'].'_paging';
			$cacheConfig = 'nodes_index';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate('Node');
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->params['paging'] = $paging;
				$this->helpers[] = 'Paginator';
			}
		} else {
			$nodes = $this->paginate('Node');
		}

		$this->set(compact('type', 'nodes'));
		$this->__viewFallback(array(
			'index_' . $type['Type']['alias'],
		));
	}

	public function term() {
		$term = $this->Node->Taxonomy->Term->find('first', array(
			'conditions' => array(
				'Term.slug' => $this->params['named']['slug'],
			),
			'cache' => array(
				'name' => 'term_'.$this->params['named']['slug'],
				'config' => 'nodes_term',
			),
		));
		if (!isset($term['Term']['id'])) {
			$this->Session->setFlash(__('Invalid Term.', true), 'default', array('class' => 'error'));
			$this->redirect('/');
		}

		if (!isset($this->params['named']['type'])) {
			$this->params['named']['type'] = 'node';
		}

		$this->paginate['Node']['order'] = 'Node.created DESC';
		$this->paginate['Node']['limit'] = Configure::read('Reading.nodes_per_page');
		$this->paginate['Node']['conditions'] = array(
			'Node.status' => 1,
			'Node.terms LIKE' => '%"' . $this->params['named']['slug'] . '"%',
			'OR' => array(
				'Node.visibility_roles' => '',
				'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
			),
		);
		$this->paginate['Node']['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);
		if (isset($this->params['named']['type'])) {
			$type = $this->Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->params['named']['type'],
				),
				'cache' => array(
					'name' => 'type_'.$this->params['named']['type'],
					'config' => 'nodes_term',
				),
			));
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__('Invalid content type.', true), 'default', array('class' => 'error'));
				$this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page'])) {
				$this->paginate['Node']['limit'] = $type['Params']['nodes_per_page'];
			}
			$this->paginate['Node']['conditions']['Node.type'] = $type['Type']['alias'];
			$this->set('title_for_layout', $term['Term']['title']);
		}

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_term_'.$this->Croogo->roleId.'_'.$this->params['named']['slug'].'_'.Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_'.$type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->params['named']['page']) ? $this->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix.'_'.$this->paginate['page'].'_'.$this->paginate['Node']['limit'];
			$cacheNamePaging = $cacheNamePrefix.'_'.$this->paginate['page'].'_'.$this->paginate['Node']['limit'].'_paging';
			$cacheConfig = 'nodes_term';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate('Node');
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->params['paging'] = $paging;
				$this->helpers[] = 'Paginator';
			}
		} else {
			$nodes = $this->paginate('Node');
		}

		$this->set(compact('term', 'type', 'nodes'));
		$this->__viewFallback(array(
			'term_' . $term['Term']['id'],
			'term_' . $type['Type']['alias'],
		));
	}

	public function promoted() {
		$this->set('title_for_layout', __('Nodes', true));

		$this->paginate['Node']['order'] = 'Node.created DESC';
		$this->paginate['Node']['limit'] = Configure::read('Reading.nodes_per_page');
		$this->paginate['Node']['conditions'] = array(
			'Node.status' => 1,
			'Node.promote' => 1,
			'OR' => array(
				'Node.visibility_roles' => '',
				'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
			),
		);
		$this->paginate['Node']['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);

		if (isset($this->params['named']['type'])) {
			$type = $this->Node->Taxonomy->Vocabulary->Type->findByAlias($this->params['named']['type']);
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__('Invalid content type.', true), 'default', array('class' => 'error'));
				$this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page'])) {
				$this->paginate['Node']['limit'] = $type['Params']['nodes_per_page'];
			}
			$this->paginate['Node']['conditions']['Node.type'] = $type['Type']['alias'];
			$this->set('title_for_layout', $type['Type']['title']);
			$this->set(compact('type'));
		}

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_promoted_'.$this->Croogo->roleId.'_'.Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_'.$type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->params['named']['page']) ? $this->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix.'_'.$this->paginate['page'].'_'.$this->paginate['Node']['limit'];
			$cacheNamePaging = $cacheNamePrefix.'_'.$this->paginate['page'].'_'.$this->paginate['Node']['limit'].'_paging';
			$cacheConfig = 'nodes_promoted';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate('Node');
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->params['paging'] = $paging;
				$this->helpers[] = 'Paginator';
			}
		} else {
			$nodes = $this->paginate('Node');
		}
		$this->set(compact('nodes'));
	}

	public function search($typeAlias = null) {
		if (!isset($this->params['named']['q'])) {
			$this->redirect('/');
		}

		App::import('Core', 'Sanitize');
		$q = Sanitize::clean($this->params['named']['q']);
		$this->paginate['Node']['order'] = 'Node.created DESC';
		$this->paginate['Node']['limit'] = Configure::read('Reading.nodes_per_page');
		$this->paginate['Node']['conditions'] = array(
			'Node.status' => 1,
			'AND' => array(
				array(
					'OR' => array(
						'Node.title LIKE' => '%' . $q . '%',
						'Node.excerpt LIKE' => '%' . $q . '%',
						'Node.body LIKE' => '%' . $q . '%',
						'Node.terms LIKE' => '%"' . $q . '"%',
					),
				),
				array(
					'OR' => array(
						'Node.visibility_roles' => '',
						'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
					),
				),
			),
		);
		$this->paginate['Node']['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);
		if ($typeAlias) {
			$type = $this->Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__('Invalid content type.', true), 'default', array('class' => 'error'));
				$this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page'])) {
				$this->paginate['Node']['limit'] = $type['Params']['nodes_per_page'];
			}
			$this->paginate['Node']['conditions']['Node.type'] = $typeAlias;
		}

		$nodes = $this->paginate('Node');
		$this->set('title_for_layout', sprintf(__('Search Results: %s', true), $q));
		$this->set(compact('q', 'nodes'));
		if ($typeAlias) {
			$this->__viewFallback(array(
				'search_' . $typeAlias,
			));
		}
	}

	public function view($id = null) {
		if (isset($this->params['named']['slug']) && isset($this->params['named']['type'])) {
			$this->Node->type = $this->params['named']['type'];
			$type = $this->Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->Node->type,
				),
				'cache' => array(
					'name' => 'type_'.$this->Node->type,
					'config' => 'nodes_view',
				),
			));
			$node = $this->Node->find('first', array(
				'conditions' => array(
					'Node.slug' => $this->params['named']['slug'],
					'Node.type' => $this->params['named']['type'],
					'Node.status' => 1,
					'OR' => array(
						'Node.visibility_roles' => '',
						'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
					),
				),
				'contain' => array(
					'Meta',
					'Taxonomy' => array(
						'Term',
						'Vocabulary',
					),
					'User',
				),
				'cache' => array(
					'name' => 'node_' . $this->Croogo->roleId . '_' . $this->params['named']['type'] . '_' . $this->params['named']['slug'],
					'config' => 'nodes_view',
				),
			));
		} elseif ($id == null) {
			$this->Session->setFlash(__('Invalid content', true), 'default', array('class' => 'error'));
			$this->redirect('/');
		} else {
			$node = $this->Node->find('first', array(
				'conditions' => array(
					'Node.id' => $id,
					'Node.status' => 1,
					'OR' => array(
						'Node.visibility_roles' => '',
						'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
					),
				),
				'contain' => array(
					'Meta',
					'Taxonomy' => array(
						'Term',
						'Vocabulary',
					),
					'User',
				),
				'cache' => array(
					'name' => 'node_'.$this->Croogo->roleId.'_'.$id,
					'config' => 'nodes_view',
				),
			));
			$this->Node->type = $node['Node']['type'];
			$type = $this->Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->Node->type,
				),
				'cache' => array(
					'name' => 'type_'.$this->Node->type,
					'config' => 'nodes_view',
				),
			));
		}

		if (!isset($node['Node']['id'])) {
			$this->Session->setFlash(__('Invalid content', true), 'default', array('class' => 'error'));
			$this->redirect('/');
		}

		if ($node['Node']['comment_count'] > 0) {
			$comments = $this->Node->Comment->find('threaded', array(
				'conditions' => array(
					'Comment.node_id' => $node['Node']['id'],
					'Comment.status' => 1,
				),
				'contain' => array(
					'User',
				),
				'cache' => array(
					'name' => 'comment_node_'.$node['Node']['id'],
					'config' => 'nodes_view',
				),
			));
		} else {
			$comments = array();
		}

		$this->set('title_for_layout', $node['Node']['title']);
		$this->set(compact('node', 'type', 'comments'));
		$this->__viewFallback(array(
			'view_' . $node['Node']['id'],
			'view_' . $type['Type']['alias'],
		));
	}

	private function __viewFallback($views) {
		if (is_string($views)) {
			$views = array($views);
		}

		if ($this->theme) {
			foreach ($views AS $view) {
				$viewPath = APP.'views'.DS.'themed'.DS.$this->theme.DS.Inflector::underscore($this->name).DS.$view.$this->ext;
				if (file_exists($viewPath)) {
					return $this->render($view);
				}
			}
		}
	}

}
