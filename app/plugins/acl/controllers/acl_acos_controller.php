<?php
/**
 * AclAcos Controller
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
class AclAcosController extends AclAppController {
	var $name = 'AclAcos';
	var $uses = array('Acl.AclAco');

    function admin_index() {
        $this->pageTitle = __('Acos', true);

		$this->AclAro->recursive = 0;
		$this->set('acos', $this->paginate());
    }

    function admin_add() {
        $this->pageTitle = __('Add Aco', true);

		if (!empty($this->data)) {
			$this->AclAco->create();
			if ($this->AclAco->save($this->data)) {
				$this->Session->setFlash(__('The Aco has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Aco could not be saved. Please, try again.', true));
			}
		}
    }
    
    function admin_edit($id = null) {
        $this->pageTitle = __('Edit Aco', true);

		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Aco', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->AclAco->save($this->data)) {
				$this->Session->setFlash(__('The Aco has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Aco could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->AclAco->read(null, $id);
		}
	}

    function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Aco', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->AclAco->delete($id)) {
			$this->Session->setFlash(__('Aco deleted', true));
			$this->redirect(array('action' => 'index'));
		}
	}

}
?>