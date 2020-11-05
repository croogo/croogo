<?php
declare(strict_types=1);

namespace Croogo\Dashboards\Controller\Admin;

use Cake\Core\Exception\Exception;
use Cake\Event\EventInterface;
use Cake\Utility\Hash;

/**
 * Dashboards Controller
 *
 * @category Controller
 * @package  Croogo.Dashboards.Controller
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Dashboards\Model\Table\DashboardsTable $Dashboards
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class DashboardsController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if ($event->getSubject()->getRequest()->getParam('action') === 'save') {
            $this->components()->unload('Security');
        }
    }

    /**
     * {@inheritDoc}
     *
     * Load the dashboards helper
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $this->viewBuilder()->setHelpers([
            'Croogo/Dashboards.Dashboards',
        ]);
    }

    /**
     * Dashboard index
     *
     * @return void
     */
    public function index()
    {
        $query = $this->Dashboards->find()
            ->where([
                'user_id' => $this->Auth->user('id')
            ])
            ->order(['column' => 'asc', 'weight' => 'asc']);
        $dashboards = $this->paginate($query);

        $this->set(compact('dashboards'));
    }

    /**
     * Admin dashboard
     *
     * @return void
     */
    public function dashboard()
    {
        $boxesForDashboard = $this->Dashboards->find('all')->select([
            'id',
            'alias',
            'collapsed',
            'status',
            'column',
            'weight',
        ])->where([
            'user_id' => $this->Auth->user('id'),
        ])->order([
            'weight',
        ]);
        $this->set('boxes_for_dashboard', $boxesForDashboard);
    }

    /**
     * Saves dashboard setting
     *
     * @throws \Cake\Core\Exception\Exception
     * @return void
     */
    public function save()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            throw new Exception('You must be logged in');
        }
        $data = Hash::insert($this->getRequest()->getData('dashboard'), '{n}.user_id', $userId);
        $dashboardIds = array_filter(Hash::extract($data, '{n}.id'));
        $query = $this->Dashboards->find();
        if ($dashboardIds) {
            $query->where(['id IN' => $dashboardIds]);
        }
        $entities = $query->toArray();
        $patched = $this->Dashboards->patchEntities($entities, $data);
        $this->Dashboards->getConnection()->getDriver()->enableAutoQuoting();
        $results = $this->Dashboards->saveMany($patched);
        $this->set(compact('results'));
        $this->set('_serialize', 'results');
    }

    /**
     * Delete a dashboard
     *
     * @param int $id Dashboard id
     * @return \Cake\Http\Response|void
     */
    public function delete($id)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Dashboard'));

            return $this->redirect(['action' => 'index']);
        }
        $entity = $this->Dashboards->get($id);
        if ($this->Dashboards->delete($entity)) {
            $this->Flash->success(__d('croogo', 'Dashboard deleted'));

            return $this->redirect($this->referer());
        }
    }

    /**
     * Toggle dashboard status
     *
     * @param int $id Dashboard id
     * @param int $status Status
     * @return void
     */
    public function toggle($id = null, $status = null)
    {
        $this->Croogo->fieldToggle($this->Dashboards, $id, $status);
    }

    /**
     * Admin moveup
     *
     * @param int $id Dashboard Id
     * @param int $step Step
     * @return \Cake\Http\Response|void
     */
    public function moveUp($id, $step = 1)
    {
        $dashboard = $this->Dashboards->get($id);
        $dashboard->weight = $dashboard->weight - $step;
        if ($this->Dashboards->save($dashboard)) {
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Admin movedown
     *
     * @param int $id Dashboard Id
     * @param int $step Step
     * @return \Cake\Http\Response|void
     */
    public function moveDown($id, $step = 1)
    {
        $dashboard = $this->Dashboards->get($id);
        $dashboard->weight = $dashboard->weight + $step;
        if ($this->Dashboards->save($dashboard)) {
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
