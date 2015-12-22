<?php

namespace Croogo\Dashboards\Controller\Admin;

use Cake\Event\Event;

/**
 * DashboardsDashboards Controller
 *
 * @category Controller
 * @package  Croogo.Dashboards.Controller
 * @version  2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsDashboardsController extends AppController
{

    public $helpers = [
        'Croogo/Dashboards.Dashboards',
    ];

    /**
     * beforeFilter
     *
     * @return void
     * @access public
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

//		$this->Security->unlockedActions[] = 'admin_save';
//		$this->Security->unlockedActions[] = 'admin_toggle';
    }

    /**
     * Dashboard index
     *
     * @return void
     */
    public function index()
    {
        $dashboards = $this->paginate($this->DashboardsDashboards->find()->where([
            'user_id' => $this->Auth->user('id')
        ]));

        $this->set(compact('dashboards'));
    }

    /**
     * Admin dashboard
     *
     * @return void
     */
    public function dashboard()
    {
        $this->set('boxes_for_dashboard', $this->DashboardsDashboards->find('all')->select([
            'alias',
            'collapsed',
            'status',
            'column',
            'weight',
        ])->where([
            'user_id' => $this->Auth->user('id'),
        ])->order([
            'weight',
        ]));
    }

    /**
     * Saves dashboard setting
     *
     * @return void
     */
    public function admin_save()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            throw new CakeException('You must be logged in');
        }
        $data = Hash::insert($this->request->data['dashboard'], '{n}.user_id', $userId);
        $this->DashboardsDashboard->deleteAll(['user_id' => $userId]);
        $this->DashboardsDashboard->saveMany($data);
    }

    /**
     * Delete a dashboard
     *
     * @param int $id Dashboard id
     * @return void
     */
    public function admin_delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Dashboard'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->DashboardsDashboard->delete($id)) {
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
    public function admin_toggle($id = null, $status = null)
    {
        $this->Croogo->fieldToggle($this->{$this->modelClass}, $id, $status);
    }

    /**
     * Admin moveup
     *
     * @param int$idDashboard Id
     * @param int$stepStep
     * @return void
     */
    public function admin_moveup($id, $step = 1)
    {
        if ($this->DashboardsDashboard->moveUp($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Admin movedown
     *
     * @param int$idDashboard Id
     * @param int$stepStep
     * @return void
     */
    public function admin_movedown($id, $step = 1)
    {
        if ($this->DashboardsDashboard->moveDown($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
