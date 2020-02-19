<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use const PHP_SESSION_ACTIVE;

/**
 * Trackable Behavior
 *
 * Populate `created_by` and `modified_by` fields from session data.
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
            'modified_by' => 'modified_by',
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
        $fields = $this->getConfig('fields');

        return $this->_table->hasField($fields['created_by']) &&
            $this->_table->hasField($fields['modified_by']);
    }

    /**
     * Bind relationship on the fly
     */
    protected function _setupBelongsTo()
    {
        if ($this->_table->associations()->has('TrackableCreator')) {
            return;
        }

        $config = $this->getConfig();
        $this->_table->addAssociations([
            'belongsTo' => [
                'TrackableCreator' => [
                    'className' => $config['userModel'],
                    'foreignKey' => $config['fields']['created_by'],
                ],
                'TrackableUpdater' => [
                    'className' => $config['userModel'],
                    'foreignKey' => $config['fields']['modified_by'],
                ],
            ],
        ]);
    }

    /**
     * Fill the created_by and modified_by fields in an entity
     *
     */
    public function beforeSave(Event $event, $options = [])
    {
        if (!$this->_hasTrackableFields()) {
            return true;
        }

        list($userId, $createdByField, $modifiedByField) = $this->getFieldValues($event, $options);

        $entity = $event->getData('entity');

        if (empty($userId)) {
            return true;
        }

        if (empty($entity[$createdByField])) {
            if ($entity->isNew()) {
                $entity->{$createdByField} = $userId;
            }
        }
        $entity->{$modifiedByField} = $userId;

        return true;
    }


    /**
     * Fill the created_by and modified_by fields from request
     **/
    public function beforeMarshal(Event $event, $options = [])
    {
        if (!$this->_hasTrackableFields()) {
            return true;
        }

        list($userId, $createdByField, $modifiedByField) = $this->getFieldValues($event, $options);

        $data = $event->getData('data');
        if (empty($data[$createdByField])) {
            $data[$createdByField] = $userId;
        }
        $data[$modifiedByField] = $userId;

        return true;
    }

    /**
     * Get values for use during beforeSave and beforeMarshal
     *
     * Note: Since shells do not have Sessions, created_by/modified_by fields
     * will not be populated. If a shell needs to populate these fields, you
     * can simulate a logged in user by setting `Trackable.Auth` config:
     *
     *   Configure::write('Trackable.User', array('id' => 1));
     *
     * Note that value stored in this variable overrides session data.
     *
     * @return array Array of [userId, createdByField, modifiedByField]
     */
    private function getFieldValues(Event $event, $options = [])
    {
        $config = $this->getConfig();

        $User = TableRegistry::get($config['userModel']);
        $userAlias = $User->getAlias();
        $userPk = $User->getPrimaryKey();

        $user = Configure::read('Trackable.Auth.User');
        if (!$user && session_status() === PHP_SESSION_ACTIVE) {
            $user = Hash::get($_SESSION, 'Auth.User');
        }

        if ($user && array_key_exists($userPk, $user)) {
            $userId = $user[$userPk];
        } else {
            Log::error('Trackable cannot obtain userId for model: ' . $this->getTable()->getAlias());
            $userId = null;
        }

        $createdByField = $config['fields']['created_by'];
        $modifiedByField = $config['fields']['modified_by'];

        return [$userId, $createdByField, $modifiedByField];
    }
}
