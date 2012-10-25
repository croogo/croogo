<?php
App::uses('BlocksAppController', 'Blocks.Controller');

/**
 * Regions Controller
 *
 * PHP version 5
 *
 * @category Blocks.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsController extends BlocksAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Regions';

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Search.Prg'
	);

/**
 * Preset Variables Search
 *
 * @var array
 * @access public
 */
	public $presetVars = array(
		'title' => array('type' => 'value'),
	);

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Blocks.Region');

/**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __('Region'));
		$this->Prg->commonProcess();
		$searchFields = array('title');

		$this->Region->recursive = 0;
		$this->paginate['Region']['order'] = 'Region.title ASC';
		$this->set('regions', $this->paginate($this->Region->parseCriteria($this->passedArgs)));
		$this->set('displayFields', $this->Region->displayFields());
		$this->set('searchFields', $searchFields);
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		$this->set('title_for_layout', __('Add Region'));

		if (!empty($this->request->data)) {
			$this->Region->create();
			if ($this->Region->save($this->request->data)) {
				$this->Session->setFlash(__('The Region has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Region could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __('Edit Region'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid Region'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Region->save($this->request->data)) {
				$this->Session->setFlash(__('The Region has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Region could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Region->read(null, $id);
		}
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Region'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Region->delete($id)) {
			$this->Session->setFlash(__('Region deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

}
