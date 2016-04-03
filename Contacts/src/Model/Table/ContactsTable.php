<?php

namespace Croogo\Contacts\Model\Table;

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

/**
 * Behaviors used by the Model
 *
 * @var array
 * @access public
 */
    public $actsAs = [
        'Croogo.Cached' => [
            'groups' => [
                'contacts',
            ],
        ],
    ];

/**
 * Validation
 *
 * @var array
 * @access public
 */
    public $validate = [
        'title' => [
            'rule' => 'notEmpty',
            'message' => 'This field cannot be left blank.',
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
        'email' => [
            'rule' => 'email',
            'message' => 'Please provide a valid email address.',
        ],
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->entityClass('Croogo/Contacts.Contact');
        $this->addAssociations([
            'hasMany' => [
                'Message' => [
                    'className' => 'Croogo/Contacts.Messages',
                    'foreignKey' => 'contact_id',
                    'dependent' => false,
                    'conditions' => '',
                    'fields' => '',
                    'order' => '',
                    'limit' => '3',
                    'offset' => '',
                    'exclusive' => '',
                    'finderQuery' => '',
                    'counterQuery' => '',
                ],
            ],
        ]);
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);
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
