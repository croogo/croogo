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

        $query = $this->Messages->find('searchable', $this->Prg->parsedParams());
        $messages = $this->paginate($query);
        $contacts = $this->Messages->Contacts->find('list');
        $searchFields = ['contact_id', 'status' => [
            'label' => __d('croogo', 'Read'),
            'type' => 'hidden',
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
        $this->set('title_for_layout', __d('croogo', 'Edit Message'));

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__d('croogo', 'Invalid Message'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($this->request->data)) {
            if ($this->Message->save($this->request->data)) {
                $this->Flash->success(__d('croogo', 'The Message has been saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('croogo', 'The Message could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Message->read(null, $id);
        }
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
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Message'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Message->delete($id)) {
            $this->Flash->success(__d('croogo', 'Message deleted'));
            return $this->redirect(['action' => 'index']);
        }
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
