<?php

namespace Croogo\Settings\Controller\Admin;

/**
 * Languages Controller
 *
 * FIXME: Port to 3.x
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LanguagesController extends AppController
{
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
        if ($this->Languages->moveUp($id, $step)) {
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
        if ($this->Languages->moveDown($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }

        return $this->redirect(['action' => 'index']);
    }

/**
 * Admin select
 *
 * @param int$id
 * @param string $modelAlias
 * @return void
 * @access public
 */
    public function select($id = null, $modelAlias = null)
    {
        if ($id == null ||
            $modelAlias == null) {
            return $this->redirect(['action' => 'index']);
        }

        $this->set('title_for_layout', __d('croogo', 'Select a language'));
        $languages = $this->Languages->find('all', [
            'conditions' => [
                'Language.status' => 1,
            ],
            'order' => 'Language.weight ASC',
        ]);
        $this->set(compact('id', 'modelAlias', 'languages'));
    }
}
