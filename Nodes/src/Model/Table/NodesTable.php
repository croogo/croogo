<?php

namespace Croogo\Nodes\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Croogo\Core\Croogo;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Nodes\Model\Entity\Node;

class NodesTable extends CroogoTable
{
    public function initialize(array $config)
    {
        parent::initialize($config);

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

    /**
     * @param \Cake\Validation\Validator $validator Validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'Please supply a title.'));

        $validator
            ->minLength('slug', 1, __d('croogo', 'Slug cannot be empty.'));
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add([$this, 'isUniquePerType'], 'isUniquePerType', [
            'field' => 'slug',
            'message' => __d('croogo', 'The slug has already been taken.')
        ]);
        return parent::buildRules($rules);
    }

    /**
     * @param \Cake\ORM\Entity $entity Entity
     * @return bool
     */
    public function isUniquePerType(Entity $entity)
    {
        $conditions = [
            $this->aliasField('slug') => $entity->slug,
            $this->aliasField('type') => $entity->type
        ];
        if (!$entity->isNew()) {
            $conditions[$this->aliasField('id') . ' !='] = $entity->id;
        }
        return !$this->exists($conditions);
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
            ->find('published', $options)
            ->contain([
                'Taxonomies' => [
                    'Terms',
                    'Vocabularies',
                ],
                'Users',
            ]);
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
        $cacheKey = 'node_' . $options['roleId'] . '_' . $options['type'] . '_' . $options['slug'];

        $query
            ->find('view', $options)
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
        $cacheKey = 'node_' . $options['roleId'] . '_' . $options['id'];
        $query->find('view', $options)
            ->where([
                $this->aliasField('id') => $options['id'],
            ])
            ->cache($cacheKey, 'nodes_view');

        return $query;
    }

    public function findByAccess(Query $query, array $options = [])
    {
        $options += ['roleId' => null];
        $visibilityRolesField = $this->aliasField('visibility_roles');

        return $query->andWhere([
            'OR' => [
                $visibilityRolesField => '',
                $visibilityRolesField . ' IS NULL',
                $visibilityRolesField . ' LIKE' => '%"' . $options['roleId'] . '"%',
            ],
        ]);
    }

    public function findPublished(Query $query, array $options = [])
    {
        $options += ['roleId' => null];
        return $query->andWhere([
            $this->aliasField('status') . ' IN' => $this->status($options['roleId']),
        ]);
    }
}
