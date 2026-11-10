<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Croogo\Core\Status;
use DateTime;

/**
 * Publishable Behavior
 *
 * Provides status and period filtering. Requires the following fields:
 *   - `status` integer value from `Status::statuses()
 *   - `publish_start` datetime indicates the start of publishing period
 *   - `publish_end` datetime indicates the end of publishing period
 *
 * @category Croogo.Model.Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @see      CroogoStatus
 */
class PublishableBehavior extends Behavior
{

    protected array $_defaultConfig = [
        'admin' => false,
        'fields' => [
            'publish_start' => 'publish_start',
            'publish_end' => 'publish_end',
        ],
    ];

    /**
     * Setup
     *
     * Valid options:
     *
     *   `admin`: Enable/disable date filtering for users with Admin roles
     *   `fields`: Specifies the physical field name to use
     *
     * @return void
     */
    /**
     * @var \Croogo\Core\Status
     */
    protected $_CroogoStatus;

    public function initialize(array $config): void
    {
        $this->_CroogoStatus = new Status();
    }

    /**
     * Get status for conditions in query based on current user's role id
     *
     * @return array Array of status
     */
    public function status($roleId = null, $statusType = 'publishing', $accessType = 'public')
    {
        return $this->_CroogoStatus->status($roleId, $statusType, $accessType);
    }

    /**
     * Filter records based on period
     *
     * @return array Options passed to Model::find()
     */
    public function beforeFind(\Cake\Event\EventInterface $event, \Cake\ORM\Query\SelectQuery $query, $options): void
    {
        // Cake 5.2: zwracanie wartosci z listenera deprecated; where() mutuje query in-place
        $table = $this->_table;
        $config = $this->getConfig();
        if (!empty($config['enabled'])) {
            return;
        }

        if ($config['admin'] === false && isset($_SESSION)) {
            // FIXME Avoid superglobals
            $roleId = Hash::get($_SESSION, 'Auth.User.role_id');
            if ($roleId == 1) {
                return;
            }
        }

        if (!$table->hasField($config['fields']['publish_start']) ||
            !$table->hasField($config['fields']['publish_end'])
        ) {
            return;
        }

        $date = isset($options['date']) ? $options['date'] : new DateTime();
        $start = $table->aliasField($config['fields']['publish_start']);
        $end = $table->aliasField($config['fields']['publish_end']);

        $query = $query->where([
            'OR' => [
                $start . ' IS' => null,
                [
                    $start . ' IS NOT' => null,
                    $start . ' <=' => $date,
                ],
            ],
        ]);

        $query->andWhere([
            'OR' => [
                $end . ' IS' => null,
                [
                    $end . ' IS NOT' => null,
                    $end . ' >=' => $date,
                ],
            ],
        ]);
    }

    /**
     * Populate publish_start
     */
    public function beforeMarshal(Event $event, $options = []): void
    {
        $data = $event->getData('data');
        if (array_key_exists('publish_start', $data)) {
            if (empty($data['publish_start'])) {
                $data['publish_start'] = new \DateTime();
            }
        }
    }

}
