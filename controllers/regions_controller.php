<?php
/**
 * Regions Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'Regions';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Region');

    public function admin_index() {
        $this->set('title_for_layout', __('Region', true));

        $this->Region->recursive = 0;
        $this->paginate['Region']['order'] = 'Region.title ASC';
        $this->set('regions', $this->paginate());
    }

    public function admin_add() {
        $this->set('title_for_layout', __('Add Region', true));

        if (!empty($this->data)) {
            $this->Region->create();
            if ($this->Region->save($this->data)) {
                $this->Session->setFlash(__('The Region has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Region could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
    }

    public function admin_edit($id = null) {
        $this->set('title_for_layout', __('Edit Region', true));

        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid Region', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->Region->save($this->data)) {
                $this->Session->setFlash(__('The Region has been saved', true), 'default', array('class' => 'success'));
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(__('The Region could not be saved. Please, try again.', true), 'default', array('class' => 'error'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Region->read(null, $id);
        }
    }

    public function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for Region', true), 'default', array('class' => 'error'));
            $this->redirect(array('action'=>'index'));
        }
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }
        if ($this->Region->delete($id)) {
            $this->Session->setFlash(__('Region deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action'=>'index'));
        }
    }

}
?>