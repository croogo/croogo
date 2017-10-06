<?php

namespace Croogo\Acl\Controller\Admin;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Croogo\Acl\AclGenerator;

/**
 * AclActions Controller
 *
 * @category Controller
 * @package  Croogo.Acl
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ActionsController extends AppController
{

    private $Acos;

    public function initialize()
    {
        parent::initialize();

        $this->Acos = TableRegistry::get('Croogo/Acl.Acos');
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        if ($this->request->param('action') == 'generate' && $this->request->param('prefix') == 'admin') {
            $this->eventManager()->off($this->Csrf);
        }
    }

/**
 * admin_index
 */
    public function index($id = null)
    {
        if ($id == null) {
            $root = $this->Acos->node('controllers')->firstOrFail();
        } else {
            $root = $this->Acos->get($id);
        }

        $acos = $this->Acos->getChildren($root->id);
        $this->set(compact('acos'));
    }

/**
 * admin_add
 */
    public function add()
    {
        $aco = $this->Acos->newEntity();

        if ($this->request->is('post')) {
            $aco = $this->Acos->patchEntity($aco, $this->request->data());
            if ($this->request->data('parent_id') == null) {
                $aco->parent_id = 1;
                $acoType = 'controller';
            } else {
                $acoType = 'action';
            }

            if ($this->Acos->save($aco)) {
                $this->Flash->success(sprintf(__d('croogo', 'The %s has been saved'), $acoType));
                return $this->Croogo->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(sprintf(__d('croogo', 'The %s could not be saved. Please, try again.'), $acoType));
            }
        }

        $acos = $this->Acos->find('treeList', [ 'keyPath' => 'id', 'valuePath' => 'alias' ]);
        $this->set(compact('aco', 'acos'));
    }

/**
 * admin_edit
 *
 * @param int $id
 */
    public function edit($id = null)
    {
        $aco = $this->Acos->get($id);

        if ($this->request->is('put')) {
            $aco = $this->Acos->patchEntity($aco, $this->request->data());
            if ($this->request->data('parent_id') == null) {
                $aco->parent_id = 1;
                $acoType = 'controller';
            } else {
                $acoType = 'action';
            }

            if ($this->Acos->save($aco)) {
                $this->Flash->success(sprintf(__d('croogo', 'The %s has been saved'), $acoType));
                if (isset($this->request->data['_apply'])) {
                    return $this->redirect(['action' => 'edit', $id]);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error(sprintf(__d('croogo', 'The %s could not be saved. Please, try again.'), $acoType));
            }
        }

        $acos = $this->Acos->find('treeList', [ 'keyPath' => 'id', 'valuePath' => 'alias' ]);
        $this->set(compact('aco', 'acos'));
    }

/**
 * admin_delete
 *
 * @param int $id
 */
    public function delete($id = null)
    {
        $aco = $this->Acos->get($id);

        if ($this->Acos->delete($aco)) {
            $this->Flash->success(__d('croogo', 'Action deleted'));
            return $this->redirect(['action' => 'index']);
        }
    }

/**
 * admin_move
 *
 * @param int $id
 * @param string $direction
 * @param string $step
 */
    public function move($id, $direction = 'up', $step = '1')
    {
        $aco = $this->Acos->get($id);

        if ($direction == 'up') {
            if ($this->Acos->moveUp($aco)) {
                $this->Flash->success(__d('croogo', 'Action moved up'));
                return $this->redirect(['action' => 'index']);
            }
        } else {
            if ($this->Acos->moveDown($aco)) {
                $this->Flash->success(__d('croogo', 'Action moved down'));
                return $this->redirect(['action' => 'index']);
            }
        }
    }

/**
 * admin_generate
 */
    public function generate()
    {
        $AclExtras = new AclGenerator();
        $AclExtras->startup($this);
        if (isset($this->request->query['sync'])) {
            $AclExtras->acoSync();
        } else {
            $AclExtras->acoUpdate();
        }

        if (isset($this->request->query['permissions'])) {
            return $this->redirect(['plugin' => 'Croogo/Acl', 'controller' => 'Permissions', 'action' => 'index']);
        } else {
            return $this->redirect(['action' => 'index']);
        }
    }
}
