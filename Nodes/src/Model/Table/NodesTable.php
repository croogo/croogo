<?php

namespace Croogo\Nodes\Model\Table;

use Cake\ORM\Query;
use Croogo\Core\Croogo;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Nodes\Model\Entity\Node;

class NodesTable extends CroogoTable
{

    public $filterArgs = [
        'q' => ['type' => 'query', 'method' => 'filterPublishedNodes'],
        'filter' => ['type' => 'query', 'method' => 'filterNodes'],
        'title' => ['type' => 'like'],
        'type' => ['type' => 'value'],
        'status' => ['type' => 'value'],
        'promote' => ['type' => 'value'],
    ];

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
        $this->addBehavior('Search.Searchable');

        $this->belongsTo('Croogo/Users.Users');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always',
                ],
            ],
        ]);
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

    /**
     * Find a single node by slug
     */
    public function findViewBySlug(Query $query, array $options = [])
    {
        $keys = ['slug' => null, 'type' => null, 'roleId' => null];
        $args = array_merge($keys, array_intersect_key($options, $keys));
        $options = array_diff_key($options, $args);

        $query->where([
            'slug' => $args['slug'],
            'type' => $args['type'],
        ]);
        $query->contain([
            'Taxonomies' => [
                'Terms',
                'Vocabularies',
            ],
            'Users',
        ]);
        $query->applyOptions([
            'cache' => [
                'name' => 'node_' . $args['roleId'] . '_' . $args['type'] . '_' . $args['slug'],
                'config' => 'nodes_view',
            ],
        ]);

        return $query;
    }

    public function findByAccess(Query $query, array $options = [])
    {
        $keys = ['roleId' => null];
        $args = array_merge($keys, array_intersect_key($options, $keys));

        return $query->andWhere([
            'OR' => [
                'visibility_roles' => '',
                'visibility_roles IS NULL',
                'visibility_roles LIKE' => '%"' . $args['roleId'] . '"%',
            ],
        ]);
    }

    public function findPublished(Query $query, array $options = [])
    {
        return $query->andWhere([
            $this->alias() . '.status IN' => $this->status(),
        ]);
    }
}
