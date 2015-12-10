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
 * Admin index
 *
 * @return void
 * @access public
 */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Languages'));

        $this->paginate = [
            'order' => [
                'weight' => 'ASC'
            ]
        ];

        $this->set('languages', $this->paginate());
    }

/**
 * Admin add
 *
 * @return void
 * @access public
 */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', "Add Language"));

        $language = $this->Languages->newEntity();

        if (!empty($this->request->data)) {
            $this->Languages->patchEntity($language, $this->request->data);

            if ($this->Languages->save($language)) {
                $this->Flash->success(__d('croogo', 'The Language has been saved'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('croogo', 'The Language could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('language'));
    }

/**
 * Admin edit
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function edit($id = null)
    {
        $this->set('title_for_layout', __d('croogo', "Edit Language"));

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__d('croogo', 'Invalid Language'));
            return $this->redirect(['action' => 'index']);
        }
        if (!empty($this->request->data)) {
            $language = $this->Languages->newEntity($this->request->data);
            if ($this->Languages->save($language)) {
                $this->Flash->success(__d('croogo', 'The Language has been saved'));
                return $this->Croogo->redirect(['action' => 'edit', $id]);
            } else {
                $this->Flash->error(__d('croogo', 'The Language could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $language = $this->Languages->get($id);
            $this->set(compact('language'));
        }
    }

/**
 * Admin delete
 *
 * @param int$id
 * @return void
 * @access public
 */
    public function delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__d('croogo', 'Invalid id for Language'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Languages->delete($id)) {
            $this->Flash->success(__d('croogo', 'Language deleted'));
            return $this->redirect(['action' => 'index']);
        }
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
