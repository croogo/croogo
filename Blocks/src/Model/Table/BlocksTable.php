<?php

namespace Croogo\Blocks\Model\Table;

use Cake\Cache\Cache;
use Cake\Database\Schema\Table as Schema;
use Cake\ORM\Query;
use Croogo\Core\Model\Table\CroogoTable;
use Croogo\Core\Status;

/**
 * Block
 *
 * @category Blocks.Model
 * @package  Croogo.Blocks.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksTable extends CroogoTable
{

    /**
     * Validation
     *
     * @var array
     * @access public
     */
    public $validate = [
        'title' => [
            'rule' => ['minLength', 1],
            'message' => 'Title cannot be empty.',
        ],
        'alias' => [
            'isUnique' => [
                'rule' => 'isUnique',
                'message' => 'This alias has already been taken.',
            ],
            'minLength' => [
                'rule' => ['minLength', 1],
                'message' => 'Alias cannot be empty.',
            ],
        ],
    ];

    /**
     * Filter search fields
     *
     * @var array
     * @access public
     */
    public $filterArgs = [
        'title' => ['type' => 'like', 'field' => ['title', 'alias']],
        'region_id' => ['type' => 'value'],
    ];

    /**
     * Find methods
     */
    public $findMethods = [
        'published' => true,
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->entityClass('Croogo/Blocks.Block');

        $this->belongsTo('Regions', [
            'className' => 'Croogo/Blocks.Regions',
            'foreignKey' => 'region_id',
            'counterCache' => true,
            'counterScope' => ['Blocks.status >=' => Status::PUBLISHED],
        ]);

        $this->addBehavior('CounterCache', [
            'Regions' => ['block_count']
        ]);
        $this->addBehavior('Croogo/Core.Publishable');
//        $this->addBehavior('Croogo/Core.Cached', [
//            'groups' => [
//                'blocks',
//            ],
//        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always',
                ],
            ],
        ]);
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Search.Search');
    }

    protected function _initializeSchema(Schema $table)
    {
        $table->columnType('visibility_roles', 'encoded');
        $table->columnType('visibility_paths', 'encoded');
        $table->columnType('params', 'params');

        return parent::_initializeSchema($table);
    }

    public function afterSave()
    {
        Cache::clear(false, 'croogo_blocks');
    }

    /**
     * Find Published blocks
     *
     * Query options:
     * - status Status
     * - regionId Region Id
     * - roleId Role Id
     * - cacheKey Cache key (optional)
     */
    public function findPublished(Query $query, array $options = [])
    {
        $status = isset($options['status']) ? $options['status'] : $this->status();
        $regionId = isset($options['regionId']) ? $options['regionId'] : null;
        $roleId = isset($options['roleId']) ? $options['roleId'] : 3;
        $cacheKey = isset($options['cacheKey']) ? $options['cacheKey'] : $regionId . '_' . $roleId;
        unset($options['status'], $options['regionId'], $options['roleId'], $options['cacheKey']);

        return $query->where([
            'status IN' => $status,
            'region_id' => $regionId,
            'AND' => [
                [
                    'OR' => [
                        'visibility_roles' => '',
                        'visibility_roles' . ' LIKE' => '%"' . $roleId . '"%',
                    ],
                ],
            ],
        ])
            ->order([
                'weight' => 'ASC',
            ]);
    }
}
