<?php
/**
 * AclAros Controller
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
class AclArosController extends AclAppController {
	var $name = 'AclAros';
	var $uses = array('Acl.AclAro');

    function admin_index() {
        $this->pageTitle = __('Aros', true);

		$this->AclAro->recursive = 0;
		$this->set('aros', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __('Add Aro', true);

		if (!empty($this->data)) {
			$this->AclAro->create();
			if ($this->AclAro->save($this->data)) {
				$this->Session->setFlash(__('The Aro has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Aro could not be saved. Please, try again.', true));
			}
		}
    }
    
    function admin_edit($id = null) {
        $this->pageTitle = __('Edit Aro', true);

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Aro', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->AclAro->save($this->data)) {
				$this->Session->setFlash(__('The Aro has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Aro could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->AclAro->read(null, $id);
		}
	}

    function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Aro', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->AclAro->delete($id)) {
			$this->Session->setFlash(__('Aro deleted', true));
			$this->redirect(array('action' => 'index'));
		}
	}

}
?>