<?php

namespace Croogo\Nodes\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;

use Croogo\Core\Croogo;
use Croogo\Nodes\Model\Table\NodesTable;

/**
 * Nodes Controller
 *
 * @property NodesTable Nodes
 * @category Nodes.Controller
 * @package  Croogo.Nodes
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesController extends AppController
{

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
     * {@inheritDoc}
     */
    public function afterConstruct()
    {
        parent::afterConstruct();
        $this->_setupAclComponent();
    }

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator', [
            'limit' =>  Configure::read('Reading.nodes_per_page')
        ]);
        $this->loadComponent('Search.Prg');
        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->loadComponent('Croogo/Core.Recaptcha');
    }

    /**
     * beforeFilter
     *
     * @param Event $event The event to handle
     *
     * @return void
     *
     * @access public
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (isset($this->request->params['slug'])) {
            $this->request->params['named']['slug'] = $this->request->params['slug'];
        }
        if (isset($this->request->params['type'])) {
            $this->request->params['named']['type'] = $this->request->params['type'];
        }
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    public function index()
    {
        if (!isset($this->request->params['named']['type'])) {
            $this->request->params['named']['type'] = 'node';
        }

        $this->paginate = [
            'order' => $this->Nodes->escapeField('created') . ' DESC'
        ];

        $visibilityRolesField = $this->Nodes->escapeField('visibility_roles');
        $this->paginate($this->Nodes->find()->where([
            $this->Nodes->escapeField('status') => $this->Nodes->status(),
            'OR' => [
                $visibilityRolesField => '',
                $visibilityRolesField . ' LIKE' => '%"' . $this->Croogo->roleId() . '"%',
            ],
        ]));

        if (isset($this->request->params['named']['limit'])) {
            $limit = $this->request->params['named']['limit'];
        } else {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        $this->paginate['contain'] = [
            'Metas',
            'Taxonomies' => [
                'Terms',
                'Vocabularies',
            ],
            'Users',
        ];
//		if (isset($this->request->params['named']['type'])) {
//			$type = $Node->Taxonomy->Vocabulary->Type->find('first', array(
//				'conditions' => array(
//					'Type.alias' => $this->request->params['named']['type'],
//				),
//				'cache' => array(
//					'name' => 'type_' . $this->request->params['named']['type'],
//					'config' => 'nodes_index',
//				),
//			));
//			if (!isset($type['Type']['id'])) {
//				$this->Flash->error(__d('croogo', 'Invalid content type.'));
//				return $this->redirect('/');
//			}
//			if (isset($type['Params']['nodes_per_page']) && empty($this->request->params['named']['limit'])) {
//				$limit = $type['Params']['nodes_per_page'];
//			}
//			$this->paginate[$Node->alias]['conditions']['Nodes.type'] = $type['Type']['alias'];
//			$this->set('title_for_layout', $type['Type']['title']);
//		}

        $this->paginate['limit'] = $limit;

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
        $this->Croogo->viewFallback([
            'index_' . $type['Type']['alias'],
        ]);
    }

    /**
     * Term
     *
     * @return null|\Cake\Network\Response
     *
     * @access public
     */
    public function term()
    {
        $Node = $this->{$this->modelClass};
        $term = $Node->Taxonomy->Term->find('first', [
            'conditions' => [
                'Term.slug' => $this->request->params['named']['slug'],
            ],
            'cache' => [
                'name' => 'term_' . $this->request->params['named']['slug'],
                'config' => 'nodes_term',
            ],
        ]);
        if (!isset($term['Term']['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid Term.'));
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

        $this->paginate[$Node->alias]['order'] = $Node->escapeField('created') . ' DESC';
        $visibilityRolesField = $Node->escapeField('visibility_roles');
        $this->paginate[$Node->alias]['conditions'] = [
            $Node->escapeField('status') => $Node->status(),
            $Node->escapeField('terms') . ' LIKE' => '%"' . $this->request->params['named']['slug'] . '"%',
            'OR' => [
                $visibilityRolesField => '',
                $visibilityRolesField . ' LIKE' => '%"' . $this->Croogo->roleId() . '"%',
            ],
        ];
        $this->paginate[$Node->alias]['contain'] = [
            'Meta',
            'Taxonomy' => [
                'Term',
                'Vocabulary',
            ],
            'User',
        ];
        if (isset($this->request->params['named']['type'])) {
            $type = $Node->Taxonomy->Vocabulary->Type->find('first', [
                'conditions' => [
                    'Type.alias' => $this->request->params['named']['type'],
                ],
                'cache' => [
                    'name' => 'type_' . $this->request->params['named']['type'],
                    'config' => 'nodes_term',
                ],
            ]);
            if (!isset($type['Type']['id'])) {
                $this->Flash->error(__d('croogo', 'Invalid content type.'));
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
        $this->Croogo->viewFallback([
            'term_' . $term['Term']['id'],
            'term_' . $term['Term']['slug'],
            'term_' . $type['Type']['alias'] . '_' . $term['Term']['slug'],
            'term_' . $type['Type']['alias'],
        ]);
    }

    /**
     * Promoted
     *
     * @return void
     *
     * @access public
     */
    public function promoted()
    {
        $this->Prg->commonProcess();

        $query = $this->Nodes->find('published')
            ->find('visibilityRole', [
                'role_id' => $this->Croogo->roleId()
            ])
            ->find('searchable', $this->Prg->parsedParams());

        $this->set('nodes', $this->Paginator->paginate($query));
    }

    /**
     * Search
     *
     * @param string $typeAlias The alias of the type
     *
     * @return null|\Cake\Network\Response
     *
     * @access public
     */
    public function search($typeAlias = null)
    {
        $this->Prg->commonProcess();

        $Node = $this->{$this->modelClass};

        $this->paginate = [
            'published',
            'roleId' => $this->Croogo->roleId(),
        ];

        $q = null;
        if (isset($this->request->query['q'])) {
            $q = $this->request->query['q'];
            $this->paginate['q'] = $q;
        }

        if ($typeAlias) {
            $type = $Node->Taxonomy->Vocabulary->Type->findByAlias($typeAlias);
            if (!isset($type['Type']['id'])) {
                $this->Flash->error(__d('croogo', 'Invalid content type.'));
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
            $this->Croogo->viewFallback([
                'search_' . $typeAlias,
            ]);
        }
    }

    /**
     * View
     *
     * @param int $id The id of the node to view
     *
     * @return null|\Cake\Network\Response
     *
     * @access public
     */
    public function view($id = null)
    {
        if (isset($this->request->params['named']['slug']) && isset($this->request->params['named']['type'])) {
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find('all', [
                'cache' => [
                    'name' => 'type_' . $this->request->params['named']['type'],
                    'config' => 'nodes_view',
                ],
            ])->where([
                'alias' => $this->request->params['named']['type'],
            ])->first();
            $node = $this->Nodes->find('viewBySlug', [
                'slug' => $this->request->params['named']['slug'],
                'type' => $this->request->params['named']['type'],
                'roleId' => $this->Croogo->roleId(),
            ])
                ->find('byAccess', [
                    'roleId' => $this->Croogo->roleId()
                ])
                ->find('published')
                ->first();
        } elseif ($id == null) {
            $this->Flash->error(__d('croogo', 'Invalid content'));
            return $this->redirect('/');
        } else {
            $node = $Node->find('viewById', [
                'id' => $id,
                'roleId' => $this->Croogo->roleId,
            ]);
            $Node->type = $node[$Node->alias]['type'];
            $type = $Node->Taxonomy->Vocabulary->Type->find('first', [
                'conditions' => [
                    'Type.alias' => $Node->type,
                ],
                'cache' => [
                    'name' => 'type_' . $Node->type,
                    'config' => 'nodes_view',
                ],
            ]);
        }

        if (!$node) {
            $this->Flash->error(__d('croogo', 'Invalid content'));
            return $this->redirect('/');
        }

        $data = $node;
        $event = new Event('Controller.Nodes.view', $this, compact('data'));
        $this->eventManager()->dispatch($event);

        $this->set('title_for_layout', $node->title);
        $this->set(compact('node', 'type', 'comments'));
        $this->Croogo->viewFallback([
            'view_' . $type->alias . '_' . $node->slug,
            'view_' . $node->id,
            'view_' . $type->alias,
        ]);
    }

    /**
     * View Fallback
     *
     * @param mixed $views The fallback views
     * @return string
     * @access protected
     * @deprecated Use CroogoComponent::viewFallback()
     */
    protected function _viewFallback($views)
    {
        return $this->Croogo->viewFallback($views);
    }

    /**
     * Set common form variables to views
     * @param array $type Type data
     * @return void
     */
    protected function _setCommonVariables($type)
    {
        if (isset($this->Taxonomies)) {
            $this->Taxonomies->prepareCommonData($type);
        }
        $Node = $this->{$this->modelClass};
        if (!empty($this->request->data[$Node->alias]['parent_id'])) {
            $Node->id = $this->request->data[$Node->alias]['parent_id'];
            $parentTitle = $Node->field('title');
        }
        $roles = $Node->User->Role->find('list');
        $this->set(compact('parentTitle', 'roles'));
    }
}
