<?php

namespace Croogo\Menus\Controller\Admin;

use Cake\Event\Event;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Menus\Model\Table\MenusTable;

/**
 * Menus Controller
 *
 * @property CroogoComponent Croogo
 * @property MenusTable Menus
 * @category Controller
 * @package  Croogo.Menus.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusController extends AppController
{

    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Menus';

    /**
     * Toggle Link status
     *
     * @param $id string Link id
     * @param $status integer Current Link status
     * @return void
     */
    public function Toggle($id = null, $status = null)
    {
        $this->Croogo->fieldToggle($this->Menus, $id, $status);
    }

    /**
     * Admin index
     *
     * @return void
     * @access public
     */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Menus'));

        $this->paginate = [
            'order' => [
                'Menus.id' => 'ASC'
            ]
        ];
        $this->set('menus', $this->paginate());
    }

    /**
     * Admin add
     */
    public function add()
    {
        $menu = $this->Menus->newEntity();

        $this->set(compact('menu'));

        if (!$this->request->is('post')) {
            return;
        }

        $menu = $this->Menus->patchEntity($menu, $this->request->data);
        if (!$this->Menus->save($menu)) {
            $this->Flash->error(__d('croogo', 'The Menu could not be saved. Please, try again.'));

            return;
        }

        $this->Flash->success(__d('croogo', 'The Menu has been saved'));
        return $this->Croogo->redirect(['action' => 'edit', $menu->id]);
    }

    /**
     * Admin edit
     *
     * @param int $id ID to edit
     *
     * @return \Cake\Network\Response|void
     */
    public function edit($id = null)
    {
        $menu = $this->Menus->get($id);

        $this->set(compact('menu'));

        if (!$this->request->is('put')) {
            return;
        }

        $menu = $this->Menus->patchEntity($menu, $this->request->data);

        if (!$this->Menus->save($menu)) {
            $this->Flash->error(__d('croogo', 'The Menu could not be saved. Please, try again.'));

            return;
        }

        $this->Flash->success(__d('croogo', 'The Menu has been saved'));
        return $this->Croogo->redirect(['action' => 'edit', $menu->id]);
    }

    /**
     * Admin delete
     *
     * @param int $id
     *
     * @return \Cake\Network\Response|null|void
     */
    public function delete($id = null)
    {
        $menu = $this->Menus->get($id);

        if (!$this->Menus->delete($menu)) {
            $this->Flash->error(__d('croogo', 'The menu could not be deleted. Please, try again.'));

            return;
        }

        $this->Flash->success(__d('croogo', 'Menu deleted'));
        return $this->redirect(['action' => 'index']);
    }
}
