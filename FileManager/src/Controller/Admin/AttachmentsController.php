<?php

namespace Croogo\FileManager\Controller\Admin;

use Cake\Event\Event;

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

    public function initialize()
    {
        parent::initialize();

        $this->Crud->addListener('Crud.Api');

        $this->loadComponent('Search.Prg', ['actions' => 'index']);
        $this->loadComponent('Croogo/Core.BulkProcess');
        $this->viewBuilder()
            ->helpers(['Croogo/FileManager.FileManager', 'Croogo/Core.Image']);
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

        $this->set('type', $this->Attachments->type);

        if ($this->action == 'add') {
            $this->Security->csrfCheck = false;
        }

        $this->paginate = [
            'order' => [
                'created' => 'DESC',
            ],
        ];
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    /**
     * Admin index
     *
     * @return void
     * @access public
     */
    public function beforePaginate(Event $event)
    {
        if (isset($this->request->params['links']) || $this->request->query('chooser')) {
            if ($this->request->query['chooser_type'] == 'image') {
                $event->subject()->query->where(['mime_type LIKE' => 'image/%']);
            } else {
                $event->subject()->query->where(['mime_type NOT LIKE' => 'image/%']);
            }
            $this->Crud->action()
                ->view('chooser');
        }
        $this->set('uploadsDir', $this->Attachments->uploadsDir);
    }

    public function beforeCrudRedirect(Event $event)
    {
        if (isset($this->request->params['editor'])) {
            $event->subject()->url = $this->redirect(['action' => 'browse']);
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
        $this
            ->viewBuilder()
            ->layout('Croogo/Core.admin_popup');
        $this->Crud->action('index')->config('template', 'browse');
        return $this->Crud->execute('index');
    }

    /**
     * @return \Cake\Network\Response
     */
    public function add()
    {
        $this->Crud->action()
            ->config('api.success.data.entity', [
                'title',
                'path'
            ]);

        return $this->Crud->execute();
    }

    /**
     * Admin process
     *
     * @return void
     * @access public
     */
    public function process()
    {
        list($action, $ids) = $this->BulkProcess->getRequestVars($this->Attachments->alias());

        $options = [
            'multiple' => ['copy' => false],
            'messageMap' => [
                'delete' => __d('croogo', 'Attachments deleted'),
            ],
        ];
        $this->BulkProcess->process($this->Attachments, $action, $ids, $options);
    }
}
