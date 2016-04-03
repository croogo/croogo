<?php

namespace Croogo\Contacts\Controller\Admin;

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

/**
 * Preset Search Variables
 */
    public $presetVars = true;

    public function initialize()
    {
        parent::initialize();
        $this->_setupPrg();
        $this->loadCroogoComponents(['BulkProcess']);
    }

    /**
 * Admin index
 *
 * @return void
 * @access public
 */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Messages'));
        $this->Prg->commonProcess();

        $query = $this->Messages
            ->find('searchable', $this->Prg->parsedParams())
            ->contain(['Contacts']);
        $messages = $this->paginate($query);
        $contacts = $this->Messages->Contacts->find('list');
        $searchFields = ['contact_id', 'status' => [
            'label' => __d('croogo', 'Read'),
            'type' => 'hidden',
            'options' => $contacts->toArray()
        ]];
        $this->set(compact('messages', 'contacts', 'searchFields'));
    }

/**
 * Admin edit
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function edit($id = null)
    {
        $message = $this->Messages->get($id);

        if ($this->request->is(['post', 'put'])) {
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($this->Messages->save($message)) {
                $this->Flash->success(__d('croogo', 'The Message has been saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('croogo', 'The Message could not be saved. Please, try again.'));
            }
        }
        $this->set('message', $message);
    }

/**
 * Admin delete
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function delete($id = null)
    {
        $message = $this->Messages->get($id);
        if ($this->Message->delete($message)) {
            $this->Flash->success(__d('croogo', 'Message deleted'));
        } else {
            $this->Flash->error(__d('croogo', 'The Message could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
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
}
