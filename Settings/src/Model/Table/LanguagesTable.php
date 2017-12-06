<?php

namespace Croogo\Settings\Model\Table;

use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
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

        $likeOptions = [
            'before' => true,
            'after' => true,
        ];
        $this->searchManager()
            ->add('title', 'Search.Like', $likeOptions)
            ->add('alias', 'Search.Like', $likeOptions)
            ->add('locale', 'Search.Like', $likeOptions);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'Title cannot be empty.'))
            ->notBlank('native', __d('croogo', 'Native cannot be empty.'))
            ->notBlank('alias', __d('croogo', 'Alias cannot be empty.'))
            ->notBlank('locale', __d('croogo', 'Locale cannot be empty.'));
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules
            ->add($rules->isUnique(['locale'],
                __d('croogo', 'That locale is already taken')
            ))
            ->add($rules->isUnique( ['alias'],
                __d('croogo', 'That alias is already taken')
            ));
        return $rules;
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
