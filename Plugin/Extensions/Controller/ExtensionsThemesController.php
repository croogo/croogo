<?php

App::uses('File', 'Utility');
App::uses('Folder', 'Utility');
App::uses('ExtensionsAppController', 'Extensions.Controller');
App::uses('ExtensionsInstaller', 'Extensions.Lib');
App::uses('CroogoTheme', 'Extensions.Lib');
App::uses('Sanitize', 'Utility');

/**
 * Extensions Themes Controller
 *
 * PHP version 5
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsThemesController extends ExtensionsAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'ExtensionsThemes';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array(
		'Settings.Setting',
		'Users.User',
	);

/**
 * CroogoTheme instance
 */
	protected $_CroogoTheme = false;

/**
 * Constructor
 */
	public function __construct($request = null, $response = null) {
		$this->_CroogoTheme = new CroogoTheme();
		parent::__construct($request, $response);
	}

/**
 * admin_index
 *
 * @return void
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Themes'));

		$themes = $this->_CroogoTheme->getThemes();
		$themesData = array();
		$themesData[] = $this->_CroogoTheme->getData();
		foreach ($themes as $theme) {
			$themesData[$theme] = $this->_CroogoTheme->getData($theme);
		}

		$currentTheme = $this->_CroogoTheme->getData(Configure::read('Site.theme'));
		$this->set(compact('themes', 'themesData', 'currentTheme'));
	}

/**
 * admin_activate
 *
 * @param string $alias
 * @return void
 */
	public function admin_activate($alias = null) {
		if ($this->_CroogoTheme->activate($alias)) {
			$this->Session->setFlash(__d('croogo', 'Theme activated.'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__d('croogo', 'Theme activation failed.'), 'default', array('class' => 'success'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_add
 *
 * @return void
 */
	public function admin_add() {
		$this->set('title_for_layout', __d('croogo', 'Upload a new theme'));

		if (!empty($this->request->data)) {
			$file = $this->request->data['Theme']['file'];
			unset($this->request->data['Theme']['file']);

			$Installer = new ExtensionsInstaller;
			try {
				$Installer->extractTheme($file['tmp_name']);
				$this->Session->setFlash(__d('croogo', 'Theme uploaded successfully.'), 'default', array('class' => 'success'));
			} catch (CakeException $e) {
				$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
			}
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * admin_editor
 *
 * @return void
 */
	public function admin_editor() {
		$this->set('title_for_layout', __d('croogo', 'Theme Editor'));
	}

/**
 * admin_save
 *
 * @return void
 */
	public function admin_save() {
	}

/**
 * admin_delete
 *
 * @param string $alias
 * @return void
 */
	public function admin_delete($alias = null) {
		if ($alias == null) {
			$this->Session->setFlash(__d('croogo', 'Invalid Theme.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($alias == 'default') {
			$this->Session->setFlash(__d('croogo', 'Default theme cannot be deleted.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		} elseif ($alias == Configure::read('Site.theme')) {
			$this->Session->setFlash(__d('croogo', 'You cannot delete a theme that is currently active.'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		$result = $this->_CroogoTheme->delete($alias);

		if ($result === true) {
			$this->Session->setFlash(__d('croogo', 'Theme deleted successfully.'), 'default', array('class' => 'success'));
		} elseif (!empty($result[0])) {
			$this->Session->setFlash($result[0], 'default', array('class' => 'error'));
		} else {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array('action' => 'index'));
	}

}
