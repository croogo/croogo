<?php

namespace Croogo\Blocks\Model\Table;

use Cake\Cache\Cache;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
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
     * Find methods
     */
    public $findMethods = [
        'published' => true,
    ];

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'Title cannot be empty.'))
            ->notBlank('alias', __d('croogo', 'Alias cannot be empty.'));
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add($rules->isUnique( ['alias'],
                __d('croogo', 'That alias is already taken')
            ));
        return $rules;
    }

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->setEntityClass('Croogo/Blocks.Block');

        $this->belongsTo('Regions', [
            'className' => 'Croogo/Blocks.Regions',
            'foreignKey' => 'region_id',
            'counterCache' => true,
            'counterScope' => ['Blocks.status >=' => Status::PUBLISHED],
        ]);

        $this->addBehavior('CounterCache', [
            'Regions' => ['block_count'],
        ]);
        $this->addBehavior('Croogo/Core.Publishable');
        $this->addBehavior('Croogo/Core.Visibility');
        $this->addBehavior('ADmad/Sequence.Sequence', [
            'order' => 'weight',
            'scope' => ['region_id'],
        ]);
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => [
                'blocks',
            ],
        ]);

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
            ->value('region_id')
            ->add('title', 'Search.Like', [
                'before' => true,
                'after' => true,
                'field' => $this->aliasField('title')
            ]);
    }

    protected function _initializeSchema(TableSchema $table)
    {
        $table->setColumnType('visibility_roles', 'encoded');
        $table->setColumnType('visibility_paths', 'encoded');
        $table->setColumnType('params', 'params');

        return parent::_initializeSchema($table);
    }

    public function afterSave()
    {
        Cache::clear(false, 'croogo_blocks');
    }

    public function findPublished(Query $query, array $options = [])
    {
        $options += ['roleId' => null];

        return $query->andWhere([
            $this->aliasField('status') . ' IN' => $this->status($options['roleId']),
        ]);
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
    public function findRegionPublished(Query $query, array $options = [])
    {
        $options += [
            'regionId' => null,
        ];

        return $query
            ->find('published', $options)
            ->find('byAccess', $options)
            ->where([
                'region_id IN' => $options['regionId']
            ]);
    }
}
