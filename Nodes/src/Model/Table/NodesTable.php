<?php

namespace Croogo\Nodes\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Croogo\Core\Croogo;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Nodes\Model\Entity\Node;

class NodesTable extends CroogoTable
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Tree');
        $this->addBehavior('Croogo/Core.BulkProcess', [
            'actionsMap' => [
                'promote' => 'bulkPromote',
                'unpromote' => 'bulkUnpromote',
            ],
        ]);
        $this->addBehavior('Croogo/Core.Publishable');
        $this->addBehavior('Croogo/Core.Url', [
            'url' => [
                'plugin' => 'Croogo/Nodes',
                'controller' => 'Nodes',
                'action' => 'view',
            ],
            'fields' => [
                'type',
                'slug',
            ],
        ]);
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Croogo/Core.Visibility');
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => ['nodes']
        ]);
        $this->addBehavior('Search.Search');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always',
                ],
            ],
        ]);

        $this->belongsTo('Croogo/Users.Users');
        $this->belongsTo('Parent', [
            'className' => 'Croogo/Nodes.Nodes',
            'foreignKey' => 'parent_id',
        ]);

        $this->belongsTo('Types', [
            'className' => 'Croogo/Nodes.Types',
            'foreignKey' => 'type',
            'bindingKey' => 'alias',
            'propertyName' => 'node_type',
        ]);

        $this->searchManager()
            ->add('q', 'Search.Finder', [
                'finder' => 'filterPublishedNodes'
            ])
            ->add('filter', 'Search.Finder', [
                'finder' => 'filterNodes'
            ])
            ->add('title', 'Search.Like', [
                'field' => $this->aliasField('title'),
                'before' => true,
                'after' => true
            ])
            ->add('slug', 'Search.Like', [
                'field' => $this->aliasField('slug'),
                'before' => true,
                'after' => true
            ])
            ->add('type', 'Search.Value', [
                'field' => $this->aliasField('type')
            ])
            ->add('status', 'Search.Value', [
                'field' => $this->aliasField('status')
            ])
            ->add('promote', 'Search.Value', [
                'field' => $this->aliasField('promote')
            ]);
    }

    protected function _initializeSchema(TableSchema $table)
    {
        $table->columnType('visibility_roles', 'encoded');

        return parent::_initializeSchema($table);
    }

    /**
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function findFilterNodes(Query $query, array $options = [])
    {
        if (!empty($options['filter'])) {
            $filter = '%' . $options['filter'] . '%';
            $query->andWhere([
                'or' => [
                    $this->aliasField('title') . ' like' => $filter,
                    $this->aliasField('excerpt') . ' like' => $filter,
                    $this->aliasField('body') . ' like' => $filter,
                    $this->aliasField('terms') . ' like' => $filter,
                ],
            ]);
        }

        return $query;
    }

    /**
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function findFilterPublishedNodes(Query $query, array $options = [])
    {
        return $query
            ->find('filterNodes', ['filter' => $options['q']])
            ->find('published');
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->notBlank('title', __d('croogo', 'Please supply a title.'));

        $validator->minLength('slug', 1, __d('croogo', 'Slug cannot be empty.'));

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->isUnique(['slug', 'type'], __d('croogo', 'The slug has already been taken.'));
        $rules->add(function (Node $node) {
            if (($node->type === '') || ($node->type === null)) {
                $node->type = 'node';
            }
            if ($node->type === 'node') {
                return true;
            }

            return (bool)TableRegistry::get('Croogo/Taxonomy.Types')
                ->findByAlias($node->type)
                ->count();
        }, 'validType', [
            'errorField' => 'type',
            'message' => 'Invalid type'
        ]);

        return parent::buildRules($rules);
    }

    /**
     * Create/update a Node record
     *
     * @param $node array Node data
     * @param $typeAlias string Node type alias
     * @return mixed see Model::saveAll()
     */
    public function saveNode(Node $node, $typeAlias = self::DEFAULT_TYPE)
    {
        //		$node = $this->formatNode($node, $typeAlias);
        $event = Croogo::dispatchEvent('Model.Node.beforeSaveNode', $this, compact('node', 'typeAlias'));
        if ($event->isStopped()) {
            return $event->result;
        }

        $result = $this->save($node);
        Croogo::dispatchEvent('Model.Node.afterSaveNode', $this, $event->data);

        return $result;
    }

    /**
     * Format data for saving
     *
     * @param array $data Node and related data, eg Taxonomy and Role
     * @param string $typeAlias string Node type alias
     * @return array formatted data
     * @throws InvalidArgumentException
     */
    public function formatNode($data, $typeAlias = self::DEFAULT_TYPE)
    {
        $roles = $type = [];

        if (!array_key_exists($this->alias, $data)) {
            $data = [$this->alias => $data];
        } else {
            $data = $data;
        }

        if (empty($data[$this->alias]['path'])) {
            $data[$this->alias]['path'] = $this->_getNodeRelativePath($data);
        }

        if (!array_key_exists('Role', $data) || empty($data['Role']['Role'])) {
            $roles = '';
        } else {
            $roles = $data['Role']['Role'];
        }

        $data[$this->alias]['visibility_roles'] = $this->encodeData($roles);

        return $data;
    }

    public function findView(Query $query, array $options = [])
    {
        if (isset($options['roleId'])) {
            $query->find('byAccess', [
                'roleId' => $options['roleId'],
            ]);
        }
        $query
            ->find('published', $options);

        return $query;
    }

    /**
     * Finds a slug/type pair
     *
     * @param \Cake\ORM\Query $query Query object
     * @param array $options Options
     * @return \Cake\ORM\Query
     */
    public function findBySlug(Query $query, array $options = [])
    {
        $defaults = ['slug' => null, 'type' => null];
        $options += $defaults;

        return $query->where([
            $this->aliasField('slug') => $options['slug'],
            $this->aliasField('type') => $options['type'],
        ]);
    }

    /**
     * Find a single node by slug
     */
    public function findViewBySlug(Query $query, array $options = [])
    {
        $defaults = ['slug' => null, 'type' => null, 'roleId' => null];
        $options += $defaults;
        $cacheKeys = [
            'node',
            I18n::getLocale(),
            $options['roleId'],
            $options['type'],
            $options['slug'],
        ];
        $cacheKey = implode('_', $cacheKeys);

        $query->find('view', $options)
            ->find('bySlug', $options)
            ->cache($cacheKey, 'nodes_view');

        return $query;
    }

    /**
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Finder options
     * @return \Cake\ORM\Query
     */
    public function findViewById(Query $query, array $options = [])
    {
        $defaults = ['id' => null, 'roleId' => null];
        $options += $defaults;
        $cacheKeys = [
            'node',
            I18n::getLocale(),
            $options['roleId'],
            $options['id'],
        ];
        $cacheKey = implode('_', $cacheKeys);
        $query->find('view', $options)
            ->where([
                $this->aliasField('id') => $options['id'],
            ])
            ->cache($cacheKey, 'nodes_view');

        return $query;
    }

    /**
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return \Cake\ORM\Query
     * @todo Extract this into a behaviour
     */
    public function findPublished(Query $query, array $options = [])
    {
        $options += ['roleId' => null];

        return $query
            ->andWhere([
                $this->aliasField('status') . ' IN' => $this->status($options['roleId']),
            ])
            ->contain([
                'Taxonomies' => [
                    'Terms',
                    'Vocabularies',
                ],
                'Users',
                'Types',
            ]);
    }

    public function findPromoted(Query $query)
    {
        return $query->andWhere([
            $this->aliasField('promote') => true,
        ]);
    }

    public function beforeSave(Event $event)
    {
        $node = $event->data()['entity'];
        $event = Croogo::dispatchEvent('Model.Node.beforeSaveNode', $this, [
            'node' => $node,
            'typeAlias' => $node->type
        ]);
        if ($event->isStopped()) {
            return $event->result;
        }
    }

    public function afterSave(Event $event)
    {
        $node = $event->data()['entity'];
        $event = Croogo::dispatchEvent('Model.Node.afterSaveNode', $this, [
            'node' => $node,
            'typeAlias' => $node->type
        ]);
        if ($event->isStopped()) {
            return $event->result;
        }
    }
}
