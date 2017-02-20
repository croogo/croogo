<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Trackable Behavior
 *
 * Populate `created_by` and `updated_by` fields from session data.
 *
 * @package  Croogo.Croogo.Model.Behavior
 * @since    1.6
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TrackableBehavior extends Behavior
{

/**
 * Default settings
 */
    protected $_defaults = [
        'userModel' => 'Croogo/Users.Users',
        'fields' => [
            'created_by' => 'created_by',
            'updated_by' => 'updated_by',
            ],
        ];

/**
 * Constructor
 */
    public function __construct(Table $table, array $config = [])
    {
        $config = Hash::merge($this->_defaults, $config);
        parent::__construct($table, $config);
    }

/**
 * Setup
 */
    public function initialize(array $config)
    {
        parent::initialize($config);
        if ($this->_hasTrackableFields()) {
            $this->_setupBelongsTo();
        }
    }

/**
 * Checks wether model has the required fields
 *
 * @return bool True if $model has the required fields
 */
    protected function _hasTrackableFields()
    {
        $fields = $this->config('fields');
        return
            $this->_table->hasField($fields['created_by']) &&
            $this->_table->hasField($fields['updated_by']);
    }

/**
 * Bind relationship on the fly
 */
    protected function _setupBelongsTo()
    {
        if ($this->_table->associations()->has('TrackableCreator')) {
            return;
        }

        $config = $this->config();
        $this->_table->addAssociations([
            'belongsTo' => [
                'TrackableCreator' => [
                    'className' => $config['userModel'],
                    'foreignKey' => $config['fields']['created_by'],
                ],
                'TrackableUpdater' => [
                    'className' => $config['userModel'],
                    'foreignKey' => $config['fields']['updated_by'],
                ],
            ],
        ]);
    }

/**
 * Fill the created_by and updated_by fields
 *
 * Note: Since shells do not have Sessions, created_by/updated_by fields
 * will not be populated. If a shell needs to populate these fields, you
 * can simulate a logged in user by setting `Trackable.Auth` config:
 *
 *   Configure::write('Trackable.User', array('id' => 1));
 *
 * Note that value stored in this variable overrides session data.
 */
    public function beforeSave(Event $event, $options = [])
    {
        if (!$this->_hasTrackableFields()) {
            return true;
        }
        $config = $this->config();

        $User = TableRegistry::get($config['userModel']);
        $userAlias = $User->alias();
        $userPk = $User->primaryKey();

        $user = Configure::read('Trackable.Auth.User');
        if (!$user && session_status() === \PHP_SESSION_ACTIVE) {
            $user = Hash::get($_SESSION, 'Auth.User');
        }

        if ($user && array_key_exists($userPk, $user)) {
            $userId = $user[$userPk];
        }

        if (empty($user) || empty($userId)) {
            return true;
        }

        $createdByField = $config['fields']['created_by'];
        $updatedByField = $config['fields']['updated_by'];

        $entity = $event->data['entity'];
        if (empty($entity->{$createdByField})) {
            if ($entity->isNew()) {
                $entity->{$createdByField} = $user[$userPk];
            }
        }
        $entity->{$updatedByField} = $userId;

        return true;
    }
}
