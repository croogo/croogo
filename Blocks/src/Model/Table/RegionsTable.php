<?php

namespace Croogo\Blocks\Model\Table;

use Cake\ORM\Query;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * Region
 *
 * @category Blocks.Model
 * @package  Croogo.Blocks.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsTable extends CroogoTable
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
        'title' => ['type' => 'like', 'field' => ['Region.title']],
    ];

    /**
     * Display fields for this model
     *
     * @var array
     */
    protected $_displayFields = [
        'id',
        'title',
        'alias',
    ];

    /**
     * Find methods
     */
    public $findMethods = [
        'active' => true,
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->entityClass('Croogo/Blocks.Region');
        $this->addAssociations([
            'hasMany' => [
                'Blocks' => [
                    'className' => 'Croogo/Blocks.Blocks',
                    'foreignKey' => 'region_id',
                    'dependent' => false,
                    'limit' => 3,
                ],
            ],
        ]);

        $this->addBehavior('Search.Search');
//        $this->addBehavior('Croogo.Cached', [
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

        $this->searchManager()
            ->add('title', 'Search.Like', [
                'field' => $this->aliasField('title'),
                'before' => true,
                'after' => true,
            ]);
    }

    /**
     * Find Regions currently in use
     */
    public function findActive(Query $query)
    {
        return $query->where([
            'block_count >' => 0,
        ]);
    }
}
