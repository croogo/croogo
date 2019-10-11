<?php

namespace Croogo\Comments\Controller\Admin;

use Cake\Event\Event;

/**
 * Comments Controller
 *
 * @category Controller
 * @package  Croogo.Comments.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CommentsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->_loadCroogoComponents(['Akismet', 'BulkProcess', 'Recaptcha']);
        $this->Crud->setConfig('actions.index', [
            'displayFields' => $this->Comments->displayFields()
        ]);
    }

    /**
     * Admin process
     *
     * @return void
     * @access public
     */
    public function process()
    {
        list($action, $ids) = $this->BulkProcess->getRequestVars($this->Comments->getAlias());

        $options = [
            'messageMap' => [
                'delete' => __d('croogo', 'Comments deleted'),
                'publish' => __d('croogo', 'Comments published'),
                'unpublish' => __d('croogo', 'Comments unpublished'),
            ]
        ];

        $this->BulkProcess->process($this->Comments, $action, $ids, $options);
    }

    public function beforePaginate(Event $event)
    {
        $query = $event->getSubject()->query;

        $query->find('relatedEntity');
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }
}
