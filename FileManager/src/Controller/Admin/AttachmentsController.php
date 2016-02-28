<?php

namespace Croogo\FileManager\Controller\Admin;

use Cake\Event\Event;
use Croogo\FileManager\Model\Entity\Attachment;

/**
 * Attachments Controller
 *
 * This file will take care of file uploads (with rich text editor integration).
 *
 * @category FileManager.Controller
 * @package  Croogo.FileManager.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AttachmentsController extends AppController
{

    /**
     * Models used by the Controller
     *
     * @var array
     * @access public
     */
    public $uses = ['FileManager.Attachment'];

    /**
     * Helpers used by the Controller
     *
     * @var array
     * @access public
     */
    public $helpers = ['Croogo/FileManager.FileManager', 'Text', 'Croogo/Core.Image'];

    public function initialize()
    {
        $this->loadComponent('Search.Prg', [
            'presetForm' => [
                'paramType' => 'querystring',
            ],
            'commonProcess' => [
                'paramType' => 'querystring',
                'filterEmpty' => true,
            ],
        ]);
        parent::initialize();
    }

    /**
     * Before executing controller actions
     *
     * @return void
     * @access public
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Attachments->addBehavior('Croogo/Core.Tree', [
            'scope' => [
                'type' => $this->Attachments->type,
            ],
        ]);
        $this->set('type', $this->Attachments->type);

        if ($this->action == 'admin_add') {
            $this->Security->csrfCheck = false;
        }
    }

    /**
     * Admin index
     *
     * @return void
     * @access public
     */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Attachments'));
        $this->Prg->commonProcess();

        $isChooser = false;
        if (isset($this->request->params['named']['links']) || $this->request->query('chooser')) {
            $isChooser = true;
        }

        $this->paginate = [
            'order' => [
                'created' => 'DESC',
            ],
        ];
        if ($isChooser) {
            if ($this->request->query['chooser_type'] == 'image') {
                $this->paginate['mime_type LIKE'] = 'image/%';
            } else {
                $this->paginate['mime_type NOT LIKE'] = 'image/%';
            }
        }

        $query = $this->Attachments->find('searchable', $this->Prg->parsedParams());
        $this->set('attachments', $this->paginate($query));
        $this->set('uploadsDir', $this->Attachments->uploadsDir);

        if ($isChooser) {
            $this->layout = 'admin_popup';
            $this->render('admin_chooser');
        }

    }

    /**
     * Admin add
     *
     * @return void
     * @access public
     */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Add Attachment'));

        if (isset($this->request->params['named']['editor'])) {
            $this->layout = 'admin_popup';
        }

        $attachment = $this->Attachments->newEntity($this->request->data);
        if ($this->request->is('post')) {
            if (empty($attachment)) {
                $attachment->errors(['file' => __d('croogo', 'Upload failed. Please ensure size does not exceed the server limit.')]);
                $this->set(compact('attachment'));
                return;
            }

            $attachment = $this->Attachments->save($attachment);
            if ($attachment) {
                $this->Flash->success(__d('croogo', 'The Attachment has been saved'));

                if (isset($this->request->params['named']['editor'])) {
                    return $this->redirect(['action' => 'browse']);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(__d('croogo', 'The Attachment could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('attachment'));
    }

    /**
     * Admin edit
     *
     * @param int $id
     * @return void
     * @access public
     */
    public function edit($id = null)
    {
        $this->set('title_for_layout', __d('croogo', 'Edit Attachment'));

        if (isset($this->request->params['named']['editor'])) {
            $this->layout = 'admin_popup';
        }

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__d('croogo', 'Invalid Attachment'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($this->request->data)) {
            $attachment = $this->Attachments->get($id);
            $attachment = $this->Attachments->patchEntity($attachment, $this->request->data);
            $attachment = $this->Attachments->save($attachment);
            if ($attachment) {
                $this->Flash->success(__d('croogo', 'The Attachment has been saved'));
                return $this->Croogo->redirect(['action' => 'edit', $attachment->id]);
            } else {
                $this->Flash->error(__d('croogo', 'The Attachment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $attachment = $this->Attachments->get($id);
            $this->set(compact('attachment'));
        }
    }

    /**
     * Admin delete
     *
     * @param int $id
     * @return void
     * @access public
     */
    public function delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Attachment'));
            return $this->redirect(['action' => 'index']);
        }

        $attachment = new Attachment(compact('id'), ['markNew' => false]);
        if ($this->Attachments->delete($attachment)) {
            $this->Flash->success(__d('croogo', 'Attachment deleted'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__d('croogo', 'Invalid id for Attachment'));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Admin browse
     *
     * @return void
     * @access public
     */
    public function browse()
    {
        $this->layout = 'admin_popup';
        $this->setAction('index');
    }
}
