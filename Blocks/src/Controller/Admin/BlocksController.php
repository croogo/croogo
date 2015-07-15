<?php

namespace Croogo\Blocks\Controller\Admin;

use Cake\Event\Event;
use Croogo\Blocks\Model\Entity\Block;
use Croogo\Core\Controller\Admin\Controller;

/**
 * Blocks Controller
 *
 * @category Blocks.Controller
 * @package  Croogo.Blocks.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksController extends Controller {

/**
 * Components
 *
 * @var array
 * @access public
 */
	public $components = array(
		'Croogo/Core.BulkProcess',
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);

/**
 * Preset Variables Search
 *
 * @var array
 * @access public
 */
	public $presetVars = true;

/**
 * afterConstruct
 * @see AppController::afterConstruct()
 */
	public function afterConstruct() {
		parent::afterConstruct();
		$this->_setupAclComponent();
	}

/**
 * Initialize
 */
	public function initialize() {
		parent::initialize();
		$this->loadModel('Croogo/Users.Roles');
	}

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter(Event $event) {
		parent::beforeFilter($event);
		$this->Security->config('unlockedActions', 'toggle');
	}

/**
 * Toggle Block status
 *
 * @param $id string Block id
 * @param $status integer Current Block status
 * @return void
 */
	public function toggle($id = null, $status = null) {
		$this->Croogo->fieldToggle($this->Blocks, $id, $status);
	}

/**
 * Admin index
 *
 * @return void
 * @access public
 * $searchField : Identify fields for search
 */
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Blocks'));
		$this->Prg->commonProcess();
		$searchFields = array('region_id', 'title');

		$this->paginate = [
			'order' => [
				'weight' => 'ASC',
			],
			'contain' => [
				'Regions',
			],
		];

		$query = $this->Blocks->find('searchable', $this->Prg->parsedParams());
		$this->set('blocks', $this->paginate($query));
		$this->set('regions', $this->Blocks->Regions->find('list'));
		$this->set('searchFields', $searchFields);
		if (isset($this->request->query['chooser'])) {
			$this->layout = 'admin_popup';
		}
	}

/**
 * Admin add
 *
 * @return void
 * @access public
 */
	public function add() {
		$this->set('title_for_layout', __d('croogo', 'Add Block'));

		$block = $this->Blocks->newEntity();
		if (!empty($this->request->data)) {
			$block = $this->Blocks->patchEntity($block, $this->request->data);
			$block = $this->Blocks->save($block);
			if ($block->id) {
				$this->Flash->success(__d('croogo', 'The Block has been saved'));
				$this->Croogo->redirect(array('action' => 'edit', $block->id));
			} else {
				$this->Flash->error(__d('croogo', 'The Block could not be saved. Please, try again.'));
			}
		}
		$regions = $this->Blocks->Regions->find('list');
		$roles = $this->Roles->find('list');
		$this->set(compact('block', 'regions', 'roles'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Block'));

		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Block'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			$block = $this->Blocks->get($id);
			$block = $this->Blocks->patchEntity($block, $this->request->data);
			if ($this->Blocks->save($block)) {
				$this->Flash->success(__d('croogo', 'The Block has been saved'));
				$this->Croogo->redirect(array('action' => 'edit', $id));
			} else {
				$this->Flash->error(__d('croogo', 'The Block could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$block = $this->Blocks->get($id, [
				'contain' => [
					'Regions',
				],
			]);
		}
		$this->set(compact('block'));
		$regions = $this->Blocks->Regions->find('list');
		$roles = $this->Roles->find('list');
		$this->set(compact('regions', 'roles'));
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function delete($id = null) {
		if (!$id) {
			$this->Flash->error(__d('croogo', 'Invalid id for Block'));
			return $this->redirect(array('action' => 'index'));
		}
		$block = new Block(['id' => $id], ['markNew' => false]);
		if ($this->Blocks->delete($block)) {
			$this->Flash->success(__d('croogo', 'Block deleted'));
			return $this->redirect(array('action' => 'index'));
		}
	}

/**
 * Admin moveup
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function moveup($id, $step = 1) {
		if ($this->Blocks->moveUp($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved up successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move up'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

/**
 * Admin movedown
 *
 * @param integer $id
 * @param integer $step
 * @return void
 * @access public
 */
	public function movedown($id, $step = 1) {
		if ($this->Blocks->moveDown($id, $step)) {
			$this->Session->setFlash(__d('croogo', 'Moved down successfully'), 'flash', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Could not move down'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

/**
 * Admin process
 *
 * @return void
 * @access public
 */
	public function process() {
		$Blocks = $this->Blocks;
		list($action, $ids) = $this->BulkProcess->getRequestVars($Blocks->alias());

		$options = array(
			'messageMap' => array(
				'delete' => __d('croogo', 'Blocks deleted'),
				'publish' => __d('croogo', 'Blocks published'),
				'unpublish' => __d('croogo', 'Blocks unpublished'),
				'copy' => __d('croogo', 'Blocks copied'),
			),
		);

		return $this->BulkProcess->process($Blocks, $action, $ids, $options);
	}

}
