<?php

namespace Croogo\Nodes\Controller;

use Cake\Core\Configure;
use Cake\Database\Expression\IdentifierExpression;
use Cake\I18n\I18n;
use Cake\Network\Exception\NotFoundException;
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

    /**
     * @return void
     * @throws \Exception
     */
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
        if (!$this->getRequest()->getParam('type')) {
            $this->setRequest($this->getRequest()->withParam('type', 'node'));
        }

        $query = $this->Nodes->find('view', [
            'roleId' => $this->Croogo->roleId()
        ]);

        if (!$this->getRequest()->getQuery('limit')) {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        if ($this->getRequest()->getParam('type')) {
            $cacheKeys = ['type', $locale, $this->getRequest()->getParam('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
                ->where([
                    'Types.alias' => $this->getRequest()->getParam('type'),
                ])
                ->cache($cacheKey, 'nodes_index')
                ->firstOrFail();
            if (isset($type->params['nodes_per_page']) && !$this->getRequest()->getQuery('limit')) {
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
                md5(json_encode($this->getRequest()->getQuery()));
            $cacheConfig = 'nodes_index';
            $query->cache($cacheName, $cacheConfig);
        }

        $query->orderDesc($this->Nodes->aliasField('publish_start'));

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
     * @return void|\Cake\Network\Response
     *
     * @access public
     */
    public function term()
    {
        if (!$this->getRequest()->getParam('type')) {
            $this->getRequest()->setParam('type', 'post');
        }

        $locale = I18n::getLocale();
        $type = $this->getRequest()->getParam('type');
        $vocab = $this->getRequest()->getParam('vocab');
        $termSlug = $this->getRequest()->getParam('term');

        if (!$termSlug && $vocab) {
            $termSlug = $vocab;
            $vocab = null;
            $this->getRequest()->setParam('vocab', $vocab);
            $this->getRequest()->setParam('term', $termSlug);
        }

        if ($vocab) {
            $cacheKeys = ['vocab', $locale, $vocab];
            $cacheKey = implode('_', $cacheKeys);
            $vocabulary = $this->Nodes->Taxonomies->Vocabularies->find()
                ->where([
                    'Vocabularies.alias' => $this->getRequest()->getParam('vocab')
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

        if ($this->getRequest()->getParam('limit')) {
            $limit = $this->getRequest()->getParam('limit');
        } else {
            $limit = Configure::read('Reading.nodes_per_page');
        }

        $query = $this->Nodes
            ->find('published')
            ->find('withTerm', [
                'term' => $termSlug,
                'roleId' => $this->Croogo->roleId(),
            ]);

        if (isset($vocabulary)) {
            $query->find('withVocabulary', ['vocab' => $vocabulary->alias]);
        }

        if ($this->getRequest()->getParam('type')) {
            $cacheKeys = ['type', $locale, $this->getRequest()->getParam('type')];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
            ->where([
                'Types.alias' => $this->getRequest()->getParam('type'),
            ])
            ->cache($cacheKey, 'nodes_term')
            ->firstOrFail();

            if (isset($type->params['nodes_per_page']) && empty($this->getRequest()->getParam('limit'))) {
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
            $this->paginate['page'] = $this->getRequest()->getQuery('page') ?: 1;
            $cacheName = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $limit;
            $cacheConfig = 'nodes_term';
            $query->cache($cacheName, $cacheConfig);
        }

        $query->orderDesc($this->Nodes->aliasField('publish_start'));

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
            ->find('search', ['search' => $this->getRequest()->getQuery()]);

        if (!$this->getRequest()->getQuery('sort')) {
            $query->order([
                $this->Nodes->aliasField('publish_start') => 'desc',
            ]);
        }

        $this->set('nodes', $this->Paginator->paginate($query));
    }

    public function feed()
    {
        return $this->setAction('promoted');
    }

    /**
     * Search
     *
     * @param string $typeAlias The alias of the type
     *
     * @return null|\Cake\Network\Response
     */
    public function search($typeAlias = null)
    {
        $Node = $this->Nodes;

        $this->paginate = [
            'published',
            'roleId' => $this->Croogo->roleId(),
        ];

        $q = null;
        if ($this->getRequest()->getQuery('q')) {
            $q = $this->getRequest()->getQuery('q');
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
            ->find('search', ['search' => $this->getRequest()->getQuery()]);

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
     * @return void|\Cake\Network\Response
     *
     * @access public
     */
    public function view($id = null)
    {
        $locale = I18n::getLocale();
        $request = $this->getRequest();
        $paramSlug = $request->getParam('slug') ?: $request->getQuery('slug');
        $paramType = $request->getParam('type') ?: $request->getQuery('type');
        if ($paramSlug && $paramType) {
            $cacheKeys = ['type', $locale, $paramType];
            $cacheKey = implode('_', $cacheKeys);
            $type = $this->Nodes->Taxonomies->Vocabularies->Types->find()
                ->cache($cacheKey, 'nodes_view')
                ->where([
                    'alias' => $paramType,
                ])
                ->firstOrFail();
            $node = $this->Nodes
                ->find('viewBySlug', [
                    'slug' => $paramSlug,
                    'type' => $paramType,
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
        if (!empty($this->getRequest()->data[$Node->alias]['parent_id'])) {
            $Node->id = $this->getRequest()->data[$Node->alias]['parent_id'];
            $parentTitle = $Node->field('title');
        }
        $roles = $Node->User->Role->find('list');
        $this->set(compact('parentTitle', 'roles'));
    }
}
