<?php

namespace Croogo\Settings\Controller\Admin;

use Cake\Event\Event;

/**
 * Settings Controller
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->_setupPrg();
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

/**
 * Admin prefix
 *
 * @param string $prefix
 * @return void
 * @access public
 */
    public function prefix($prefix = null)
    {
        if ($this->request->is('post')) {
            foreach ($this->request->data() as $id => $value) {
                if ($id == '_apply') {
                    continue;
                }
                $setting = $this->Settings->get($id);
                $setting->value = $value;
                $this->Settings->save($setting);
            }
            $this->Flash->success(__d('croogo', 'Settings updated successfully'));
            return $this->redirect(['action' => 'prefix', $prefix]);
        }

        $settings = $this->Settings->find('all', [
            'order' => 'Settings.weight ASC',
            'conditions' => [
                'Settings.key LIKE' => $prefix . '.%',
                'Settings.editable' => 1,
            ],
        ]);

        if ($settings->count() == 0) {
            $this->Flash->error(__d('croogo', 'Invalid Setting key'));
        }

        $this->set(compact('prefix', 'settings'));
    }

/**
 * Admin moveup
 *
 * @param int $id
 * @param int $step
 * @return void
 * @access public
 */
    public function moveup($id, $step = 1)
    {
        if ($this->Setting->moveUp($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }

        if (!$redirect = $this->referer()) {
            $redirect = [
                'admin' => true,
                'plugin' => 'settings',
                'controller' => 'settings',
                'action' => 'index'
            ];
        }
        return $this->redirect($redirect);
    }

/**
 * Admin moveup
 *
 * @param int $id
 * @param int $step
 * @return void
 * @access public
 */
    public function movedown($id, $step = 1)
    {
        if ($this->Setting->moveDown($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }

        return $this->redirect(['admin' => true, 'controller' => 'settings', 'action' => 'index']);
    }
}
