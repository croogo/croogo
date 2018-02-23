<?php

namespace Croogo\Nodes\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
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
     * Preset Variable Search
     *
     * @var array
     * @access public
     */
    public $presetVars = true;

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator', [
            'limit' => Configure::read('Reading.nodes_per_page'),
        ]);
        $this->loadComponent('Search.Prg');
        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->loadComponent('Croogo/Core.Recaptcha', [
            'actions' => ['view']
        ]);

        $this->Prg->config('actions', ['index', 'search', 'term']);
    }

    /**
     * Index
     *
     * @return void
     * @access public
     */
    public function index()
    {
        $locale = I18n::getLocale();
        if (!$this->request->param('type')) {
            $this->request->params['type'] = 'node';
        }

        $query = $this->Nodes->find('view', [
            'roleId' => $this->Croogo->roleId()
        ]);

        if (!$this->request->query('limit')) {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        if ($this->request->param('type')) {
            $cacheKeys = ['type', $locale, $this->request->param('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
                ->where([
                    'Types.alias' => $this->request->param('type'),
                ])
                ->cache($cacheKey, 'nodes_index')
                ->firstOrFail();
            if (isset($type->params['nodes_per_page']) && !$this->request->query('limit')) {
                $limit = $type->params['nodes_per_page'];
            }
            $query->andWhere([
                    $this->Nodes->aliasField('type') => $type->alias,
                ]);
        }

        if (isset($limit)) {
            $this->paginate['limit'] = $limit;
        }

        if ($this->usePaginationCache) {
            $cacheNamePrefix = 'nodes_index_' . $this->Croogo->roleId() . '_' . $locale;
            if (isset($type)) {
                $cacheNamePrefix .= '_' . $type->alias;
            }
            $cacheName = $cacheNamePrefix .
                '_' .
                $this->request->query('type') .
                '_' . ($this->request->query('page') ?: 1) .
                '_' .
                ($this->request->query('limit') ? $this->request->query('limit') : $limit);
            $cacheConfig = 'nodes_index';
            $query->cache($cacheName, $cacheConfig);
        }

        $query->orderDesc($query->newExpr()
            ->addCase([
                $query->newExpr()
                    ->isNotNull($this->Nodes->aliasField('publish_start'))
            ], [
                new IdentifierExpression($this->Nodes->aliasField('publish_start')),
                new IdentifierExpression($this->Nodes->aliasField('created')),
            ]));

        $nodes = $this->paginate($query);
        $this->set(compact('type', 'nodes'));
        if ($type) {
            $this->Croogo->viewFallback([
                Inflector::camelize($type->alias, '-') . '/index',
            ]);
        }
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
        $locale = I18n::getLocale();
        $cacheKeys = ['term', $locale, $this->request->param('slug')];
        $cacheKey = implode('_', $cacheKeys);
        $term = $this->Nodes->Taxonomies->Terms->find()
            ->where([
                'Terms.slug' => $this->request->param('slug')
            ])
            ->cache($cacheKey, 'nodes_term')
            ->firstOrFail();

        if (!$this->request->param('type')) {
            $this->request->param('type', 'node');
        }

        if ($this->request->param('limit')) {
            $limit = $this->request->param('limit');
        } else {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        $query = $this->Nodes->find('view', ['roleId' => $this->Croogo->roleId()]);

        if ($this->request->param('type')) {
            $cacheKeys = ['type', $locale, $this->request->param('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
            ->where([
                'Types.alias' => $this->request->param('type'),
            ])
            ->cache($cacheKey, 'nodes_term')
            ->firstOrFail();

            if (isset($type->params['nodes_per_page']) && empty($this->request->param('limit'))) {
                $limit = $type->params['nodes_per_page'];
            }
            $query
                ->where([
                    $this->Nodes->aliasField('type') => $type->alias
                ]);
        }

        $this->paginate['limit'] = $limit;

        if ($this->usePaginationCache) {
            $cacheNamePrefix = 'nodes_term_' .
                $this->Croogo->roleId() .
                '_' .
                $this->request->param('slug') .
                '_' .
                $locale;
            if (isset($type)) {
                $cacheNamePrefix .= '_' . $type->alias;
            }
            $this->paginate['page'] = $this->request->param('page') ?: 1;
            $cacheName = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit;
            $cacheConfig = 'nodes_term';
            $query->cache($cacheName, $cacheConfig);
        }

        $query->find('withTerm', ['term' => $term]);
        $query->orderDesc($query->newExpr()
            ->addCase([
                $query->newExpr()
                    ->isNotNull($this->Nodes->aliasField('publish_start'))
            ], [
                new IdentifierExpression($this->Nodes->aliasField('publish_start')),
                new IdentifierExpression($this->Nodes->aliasField('created')),
            ]));
        $nodes = $this->paginate($query);

        $this->set(compact('term', 'type', 'nodes'));
        $camelizedType = Inflector::camelize($type->alias, '-');
        $this->Croogo->viewFallback([
            'term_' . $term->id,
            'term_' . $term->slug,
            $camelizedType . '/term_' . $term['Term']['slug'],
            $camelizedType . '/term',
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

        $query = $this->Nodes
            ->find('published')
            ->find('promoted')
            ->find('byAccess', [
                'roleId' => $this->Croogo->roleId(),
            ])
            ->find('search', ['search' => $this->request->query]);

        if (!$this->request->query('sort')) {
            $query->order([
                $this->Nodes->aliasField('created') => 'desc',
            ]);
        }

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
        $Node = $this->Nodes;

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

        $criteria = $Node
            ->find('published')
            ->find('search', ['search' => $this->request->query]);

        $nodes = $this->paginate($criteria);
        $this->set(compact('q', 'nodes'));
        if (isset($type)) {
            $camelizedType = Inflector::camelize($type->alias, '-');
            $this->Croogo->viewFallback([
                $camelizedType . '/search',
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
        $locale = I18n::getLocale();
        if ($this->request->param('slug') && $this->request->param('type')) {
            $cacheKeys = ['type', $locale, $this->request->param('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
                ->cache($cacheKey, 'nodes_view')
                ->where([
                    'alias' => $this->request->param('type'),
                ])
                ->firstOrFail();
            $node = $this->Nodes->find('viewBySlug', [
                'slug' => $this->request->param('slug'),
                'type' => $this->request->param('type'),
                'roleId' => $this->Croogo->roleId(),
            ])
                ->firstOrFail();
        } elseif ($id == null) {
            throw new NotFoundException('No node with that ID exists.');
        } else {
            $node = $this->Nodes->find('viewById', [
                'id' => $id,
                'roleId' => $this->Croogo->roleId(),
            ])
            ->firstOrFail();
            $cacheKeys = ['type', $locale, $node->type];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
            ->where([
                'Types.alias' => $node->type,
            ])
            ->cache($cacheKey, 'nodes_view')
            ->firstOrFail();
        }

        if (!$node) {
            throw new NotFoundException('No node with that ID exists .');
        }

        $this->dispatchEvent('Controller.Nodes.view', compact('node', 'type'));

        $this->set('title_for_layout', $node->title);
        $this->set(compact('node', 'type'));

        $camelizedType = Inflector::camelize($type->alias, '-');
        $this->Croogo->viewFallback([
            'view/node_' . $node->id,
            'view/' . str_replace('-', '_', $node->slug),
            $camelizedType . '/view',
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
     *
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
