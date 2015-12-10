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
 * Components
 *
 * @var array
 * @access public
 */
    public $components = [
        'Croogo/Core.BulkProcess',
        'Search.Prg' => [
            'presetForm' => [
                'paramType' => 'querystring',
            ],
            'commonProcess' => [
                'paramType' => 'querystring',
                'filterEmpty' => true,
            ],
        ],
    ];

/**
 * Preset Search Variables
 */
    public $presetVars = true;

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
            $this->Session->setFlash(__d('croogo', 'Invalid Message'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($this->request->data)) {
            if ($this->Message->save($this->request->data)) {
                $this->Session->setFlash(__d('croogo', 'The Message has been saved'), 'flash', ['class' => 'success']);
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Session->setFlash(__d('croogo', 'The Message could not be saved. Please, try again.'), 'flash', ['class' => 'error']);
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
            $this->Session->setFlash(__d('croogo', 'Invalid id for Message'), 'flash', ['class' => 'error']);
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Message->delete($id)) {
            $this->Session->setFlash(__d('croogo', 'Message deleted'), 'flash', ['class' => 'success']);
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
