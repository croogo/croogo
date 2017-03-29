<?php

namespace Croogo\Settings\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * Language
 *
 * @category Model
 * @package  Croogo.Settings.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LanguagesTable extends CroogoTable
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
 * Initialize
 */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('ADmad/Sequence.Sequence', [
            'order' => 'weight',
        ]);
        $this->addBehavior('Search.Search');
        $this->addBehavior('Timestamp');
    }

    public function findActive(Query $query)
    {
        $query
            ->select(['id', 'alias', 'locale'])
            ->where(['status' => true])
            ->formatResults(function ($results) {
                $formatted = [];
                foreach ($results as $row) {
                    $formatted[$row->alias] = ['locale' => $row->locale];
                }
                return $formatted;
            });
        return $query;
    }

}
