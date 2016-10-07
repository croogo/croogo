<?php

namespace Croogo\Contacts\Controller\Admin;

use Cake\Event\Event;

/**
 * Messages Controller
 *
 * @category Contacts.Controller
 * @package  Croogo.Contacts.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MessagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->_setupPrg();

        $this->_loadCroogoComponents(['BulkProcess']);
    }

/**
 * Admin process
 *
 * @return void
 * @access public
 */
    public function process()
    {
        $Message = $this->{$this->modelClass};
        list($action, $ids) = $this->BulkProcess->getRequestVars($Message->alias);

        $messageMap = [
            'delete' => __d('croogo', 'Messages deleted'),
            'read' => __d('croogo', 'Messages marked as read'),
            'unread' => __d('croogo', 'Messages marked as unread'),
        ];
        return $this->BulkProcess->process($Message, $action, $ids, $messageMap);
    }

    public function beforePaginate(Event $event)
    {
        $query = $event->subject()->query;

        $query->contain([
            'Contacts'
        ]);
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate'
        ];
    }
}
