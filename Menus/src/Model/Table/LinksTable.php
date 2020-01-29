<?php

namespace Croogo\Menus\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Validation\Validator;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * Link
 *
 * @category Model
 * @package  Croogo.Menus.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LinksTable extends CroogoTable
{

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'Title cannot be empty.'));

        $validator
            ->add('link', 'custom', [
                'rule' => function ($value, $context) {
                    return !empty($value);
                },
                'message' => __d('croogo', 'Link cannot be empty.')
            ]);

        return $validator;
    }

    public function initialize(array $config)
    {
        $this->addBehavior('Tree');
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => ['menus']
        ]);
        $this->belongsTo('Menus', [
            'className' => 'Croogo/Menus.Menus',
        ]);
        $this->addBehavior('CounterCache', [
            'Menus' => ['link_count'],
        ]);

        $this->addBehavior('Timestamp');

        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Croogo/Core.Publishable');
        $this->addBehavior('Croogo/Core.Visibility');
        $this->addBehavior('Search.Search');

        $this->searchManager()
            ->add('menu_id', 'Search.Value', [
                'field' => 'menu_id'
            ])
            ->add('menuAlias', 'Search.Finder', [
                'finder' => 'filterByMenuAlias',
            ])
            ->add('title', 'Search.Like', [
                'field' => 'title',
                'before' => true,
                'after' => true
            ]);
    }

    protected function _initializeSchema(TableSchema $table)
    {
        $table->setColumnType('visibility_roles', 'encoded');
        $table->setColumnType('link', 'link');
        $table->setColumnType('params', 'params');

        return parent::_initializeSchema($table);
    }

    /**
     * Allow to change Tree scope to a specific menu
     *
     * @param int $menuId menu id
     * @return void
     */
    public function setTreeScope($menuId)
    {
        $settings = [
            'scope' => ['menu_id' => $menuId],
        ];
        if ($this->hasBehavior('Tree')) {
            $this->behaviors()
                ->get('Tree')
                ->setConfig($settings);
        } else {
            $this->addBehavior('Tree', $settings);
        }
    }

    /**
     * Calls TreeBehavior::recover when we are changing scope
     */
    public function afterSave(Event $event, Entity $entity, $options = [])
    {
        if ($entity->isNew()) {
            return;
        }
        if ($entity->isDirty('menu_id')) {
            $this->setTreeScope($entity->menu_id);
            $this->recover();
            $this->setTreeScope($entity->getOriginal('menu_id'));
            $this->recover();
        }
    }

    /**
     * Filters active links based on menu.alias
     */
    public function findFilterByMenuAlias(Query $query, array $options = [])
    {
        return $query
            ->innerJoinWith('Menus')
            ->where([
                $this->Menus->aliasField('alias') => $options['menuAlias'],
                $this->aliasField('status') => 1,
            ]);
    }

}
