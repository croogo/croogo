<?php

namespace Croogo\Extensions\Controller\Admin;

use Croogo\Extensions\CroogoTheme;
use Cake\Core\Configure;
use Croogo\Extensions\Exception\MissingThemeException;
use Croogo\Extensions\ExtensionsInstaller;

/**
 * Extensions Themes Controller
 *
 * @category Controller
 * @package  Croogo.Extensions.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExtensionsThemesController extends AppController {

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
	public function index() {
		$this->set('title_for_layout', __d('croogo', 'Themes'));

		$themes = $this->_CroogoTheme->getThemes();
		$themesData = array();
		foreach ($themes as $theme) {
			$themesData[$theme] = $this->_CroogoTheme->getData($theme);
		}

		$currentTheme = $this->_CroogoTheme->getData(Configure::read('Site.theme'));
		$this->set(compact('themes', 'themesData', 'currentTheme'));
	}

/**
 * admin_activate
 *
 * @param string $theme
 */
	public function activate($theme = null) {
        try {
            $this->_CroogoTheme->activate($theme);

            $this->Flash->success(__d('croogo', 'Theme activated.'));
        } catch (MissingThemeException $exception) {
            $this->Flash->error(__d('croogo', 'Theme activation failed: %s', $exception->getMessage()));
        }

		return $this->redirect(['action' => 'index']);
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
				$this->Session->setFlash(__d('croogo', 'Theme uploaded successfully.'), 'flash', array('class' => 'success'));
			} catch (CakeException $e) {
				$this->Session->setFlash($e->getMessage(), 'flash', array('class' => 'error'));
			}
			return $this->redirect(array('action' => 'index'));
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
			$this->Session->setFlash(__d('croogo', 'Invalid Theme.'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		if ($alias == 'default') {
			$this->Session->setFlash(__d('croogo', 'Default theme cannot be deleted.'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		} elseif ($alias == Configure::read('Site.theme')) {
			$this->Session->setFlash(__d('croogo', 'You cannot delete a theme that is currently active.'), 'flash', array('class' => 'error'));
			return $this->redirect(array('action' => 'index'));
		}

		$result = $this->_CroogoTheme->delete($alias);

		if ($result === true) {
			$this->Session->setFlash(__d('croogo', 'Theme deleted successfully.'), 'flash', array('class' => 'success'));
		} elseif (!empty($result[0])) {
			$this->Session->setFlash($result[0], 'flash', array('class' => 'error'));
		} else {
			$this->Session->setFlash(__d('croogo', 'An error occurred.'), 'flash', array('class' => 'error'));
		}

		return $this->redirect(array('action' => 'index'));
	}

}
