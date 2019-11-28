<?php

namespace Croogo\Blocks\Controller\Admin;

use Cake\Event\Event;

/**
 * Blocks Controller
 *
 * @category Blocks.Controller
 * @package  Croogo.Blocks.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksController extends AppController
{
    public $paginate = [
        'order' => [
            'region_id' => 'asc',
            'weight' => 'asc',
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Croogo/Users.Roles');

        $this->_loadCroogoComponents(['BulkProcess']);
        $this->_setupPrg();

        $this->Crud->setConfig('actions.index', [
            'searchFields' => ['region_id', 'title']
        ]);
        $this->Crud->setConfig('actions.moveUp', [
            'className' => 'Croogo/Core.Admin/MoveUp'
        ]);
        $this->Crud->setConfig('actions.moveDown', [
            'className' => 'Croogo/Core.Admin/MoveDown'
        ]);

        if ($this->getRequest()->getParam('action') == 'toggle') {
            $this->Croogo->protectToggleAction();
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
        $Blocks = $this->Blocks;
        list($action, $ids) = $this->BulkProcess->getRequestVars($Blocks->alias());

        $options = [
            'messageMap' => [
                'delete' => __d('croogo', 'Blocks deleted successfully'),
                'publish' => __d('croogo', 'Blocks published successfully'),
                'unpublish' => __d('croogo', 'Blocks unpublished successfully'),
                'copy' => __d('croogo', 'Blocks copied successfully'),
            ],
        ];

        return $this->BulkProcess->process($Blocks, $action, $ids, $options);
    }

    public function beforePaginate(Event $event)
    {
        $query = $event->getSubject()->query;
        $query->contain([
            'Regions'
        ]);

        $this->set('regions', $this->Blocks->Regions->find('list'));
    }

    public function beforeCrudRender()
    {
        $this->set('roles', $this->Roles->find('list'));
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeRender' => 'beforeCrudRender',
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

    public function toggle()
    {
        return $this->Crud->execute();
    }
}
