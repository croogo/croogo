<?php

namespace Croogo\Blocks\Controller\Admin;

use Cake\Event\Event;
use Croogo\Blocks\Model\Entity\Block;

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
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Croogo/Users.Roles');

        $this->loadCroogoComponents(['BulkProcess']);
        $this->_setupPrg();

        $this->Crud->config('actions.index', [
            'searchFields' => ['region_id', 'title']
        ]);
    }

/**
 * Admin moveup
 *
 * @param int$id
 * @param int$step
 * @return void
 * @access public
 */
    public function moveup($id, $step = 1)
    {
        if ($this->Blocks->moveUp($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }

        return $this->redirect(['action' => 'index']);
    }

/**
 * Admin movedown
 *
 * @param int$id
 * @param int$step
 * @return void
 * @access public
 */
    public function movedown($id, $step = 1)
    {
        if ($this->Blocks->moveDown($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
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
        $Blocks = $this->Blocks;
        list($action, $ids) = $this->BulkProcess->getRequestVars($Blocks->alias());

        $options = [
            'messageMap' => [
                'delete' => __d('croogo', 'Blocks deleted'),
                'publish' => __d('croogo', 'Blocks published'),
                'unpublish' => __d('croogo', 'Blocks unpublished'),
                'copy' => __d('croogo', 'Blocks copied'),
            ],
        ];

        return $this->BulkProcess->process($Blocks, $action, $ids, $options);
    }

    public function beforePaginate(Event $event)
    {
        $query = $event->subject()->query;
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
        ];
    }
}
