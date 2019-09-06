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
        $this->loadComponent('Search.Prg', ['actions' => true]);
        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->loadComponent('Croogo/Core.Recaptcha', [
            'actions' => ['view']
        ]);

        $this->Prg->getConfig('actions', ['index', 'search', 'term']);
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
        if (!$this->request->getParam('type')) {
            $this->request->params['type'] = 'node';
        }

        $query = $this->Nodes->find('view', [
            'roleId' => $this->Croogo->roleId()
        ]);

        if (!$this->request->getQuery('limit')) {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        if ($this->request->getParam('type')) {
            $cacheKeys = ['type', $locale, $this->request->getParam('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
                ->where([
                    'Types.alias' => $this->request->getParam('type'),
                ])
                ->cache($cacheKey, 'nodes_index')
                ->firstOrFail();
            if (isset($type->params['nodes_per_page']) && !$this->request->getQuery('limit')) {
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
                $this->request->getQuery('type') .
                '_' . ($this->request->getQuery('page') ?: 1) .
                '_' .
                ($this->request->getQuery('limit') ? $this->request->getQuery('limit') : $limit);
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
        if (!$this->request->getParam('type')) {
            $this->request->setParam('type', 'post');
        }

        $locale = I18n::getLocale();
        $type = $this->request->getParam('type');
        $vocab = $this->request->getParam('vocab');
        $termSlug = $this->request->getParam('term');

        if (!$termSlug && $vocab) {
            $termSlug = $vocab;
            $vocab = null;
            $this->request->setParam('vocab', $vocab);
            $this->request->setParam('term', $termSlug);
        }

        if ($vocab) {
            $cacheKeys = ['vocab', $locale, $vocab];
            $cacheKey = implode('_', $cacheKeys);
            $vocabulary = $this->Nodes->Taxonomies->Vocabularies->find()
                ->where([
                    'Vocabularies.alias' => $this->request->getParam('vocab')
                ])
                ->cache($cacheKey, 'nodes_term')
                ->firstOrFail();
            $this->set(compact('vocabulary'));
        }

        $cacheKeys = ['term', $locale, $termSlug];
        $cacheKey = implode('_', $cacheKeys);
        $term = $this->Nodes->Taxonomies->Terms->find()
            ->where([
                'Terms.slug' => $termSlug,
            ])
            ->cache($cacheKey, 'nodes_term')
            ->firstOrFail();

        if ($this->request->getParam('limit')) {
            $limit = $this->request->getParam('limit');
        } else {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        $query = $this->Nodes
            ->find('published')
            ->find('withTerm', [
                'term' => $termSlug,
                'roleId' => $this->Croogo->roleId(),
            ]);

        if ($vocabulary) {
            $query->find('withVocabulary', ['vocab' => $vocabulary->alias]);
        }

        if ($this->request->getParam('type')) {
            $cacheKeys = ['type', $locale, $this->request->getParam('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
            ->where([
                'Types.alias' => $this->request->getParam('type'),
            ])
            ->cache($cacheKey, 'nodes_term')
            ->firstOrFail();

            if (isset($type->params['nodes_per_page']) && empty($this->request->getParam('limit'))) {
                $limit = $type->params['nodes_per_page'];
            }
            $query
                ->where([
                    $this->Nodes->aliasField('type') => $type->alias
                ]);
        }

        $this->paginate['limit'] = $limit;

        if ($this->usePaginationCache) {
            $cacheNamePrefix = implode('_', [
                'nodes_term',
                $this->Croogo->roleId(),
                $vocab,
                $termSlug,
                $locale
            ]);
            if (isset($type)) {
                $cacheNamePrefix .= '_' . $type->alias;
            }
            $this->paginate['page'] = $this->request->getParam('page') ?: 1;
            $cacheName = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit;
            $cacheConfig = 'nodes_term';
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

        $this->set(compact('term', 'type', 'nodes'));
        $camelizedType = Inflector::camelize($type->alias, '-');
        $this->Croogo->viewFallback([
            'term_' . $term->id,
            'term_' . $term->slug,
            $camelizedType . '/term_' . $term->slug,
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
            ->find('search', ['search' => $this->request->getQuery()]);

        if (!$this->request->getQuery('sort')) {
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
        if ($this->request->getQuery('q')) {
            $q = $this->request->getQuery('q');
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
            ->find('search', ['search' => $this->request->getQuery()]);

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
        if ($this->request->getParam('slug') && $this->request->getParam('type')) {
            $cacheKeys = ['type', $locale, $this->request->getParam('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
                ->cache($cacheKey, 'nodes_view')
                ->where([
                    'alias' => $this->request->getParam('type'),
                ])
                ->firstOrFail();
            $node = $this->Nodes
                ->find('viewBySlug', [
                    'slug' => $this->request->getParam('slug'),
                    'type' => $this->request->getParam('type'),
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
