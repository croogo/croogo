<?php

namespace Croogo\Contacts\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Croogo\Core\Model\Table\CroogoTable;

/**
 * Contact
 *
 * @category Model
 * @package  Croogo.Contacts.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ContactsTable extends CroogoTable
{

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notBlank('title', __d('croogo', 'Title cannot be empty.'))
            ->notBlank('alias',  __d('croogo', 'Alias cannot be empty.'))
            ->email('email', __d('croogo', 'Not a valid email address.'));
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
        $this->displayField('title');
        $this->entityClass('Croogo/Contacts.Contact');
        $this->hasMany('Messages', [
            'className' => 'Croogo/Contacts.Messages',
            'foreignKey' => 'contact_id',
            'dependent' => false,
            'limit' => '3',
        ]);
        $this->addBehavior('Croogo/Core.Cached', [
            'groups' => ['contacts']
        ]);
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always',
                ],
            ],
        ]);
        $this->addBehavior('Search.Search');
    }

    /**
     * Display fields for this model
     *
     * @var array
     */
    protected $_displayFields = [
        'title',
        'alias',
        'email',
    ];
}
