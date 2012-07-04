<?php
/**
 * Translate Controller
 *
 * PHP version 5
 *
 * @category Translate.Controller
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateController extends TranslateAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Translate';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array(
		'Setting',
		'Language',
	);

/**
 * admin_index
 *
 * @param integer $id
 * @param string $modelAlias
 * @return void
 */
	public function admin_index($id = null, $modelAlias = null) {
		if ($id == null || $modelAlias == null) {
			$this->Session->setFlash(__('Invalid ID.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		if (!is_array(Configure::read('Translate.models.' . $modelAlias))) {
			$this->Session->setFlash(__('Invalid model.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		$model =& ClassRegistry::init($modelAlias);
		$record = $model->findById($id);
		if (!isset($record[$modelAlias]['id'])) {
			$this->Session->setFlash(__('Invalid record.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}
		$this->set('title_for_layout', sprintf(__('Translations: %s'), $record[$modelAlias][$model->displayField]));

		$runtimeModel =& $model->translateModel();
		$runtimeModelAlias = $runtimeModel->alias;
		$translations = $runtimeModel->find('all', array(
			'conditions' => array(
				$runtimeModelAlias . '.model' => $modelAlias,
				$runtimeModelAlias . '.foreign_key' => $id,
				$runtimeModelAlias . '.field' => 'title',
			),
		));

		$this->set(compact('runtimeModelAlias', 'translations', 'record', 'modelAlias', 'id'));
	}

/**
 * admin_edit
 *
 * @param integer $id
 * @param string $modelAlias
 * @return void
 */
	public function admin_edit($id = null, $modelAlias = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid ID.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		if (!isset($this->params['named']['locale'])) {
			$this->Session->setFlash(__('Invalid locale'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		$language = $this->Language->find('first', array(
			'conditions' => array(
				'Language.alias' => $this->params['named']['locale'],
				'Language.status' => 1,
			),
		));
		if (!isset($language['Language']['id'])) {
			$this->Session->setFlash(__('Invalid Language'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		$model =& ClassRegistry::init($modelAlias);
		$record = $model->findById($id);
		if (!isset($record[$modelAlias]['id'])) {
			$this->Session->setFlash(__('Invalid record.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}
		$this->set('title_for_layout', sprintf(__('Translate content: %s (%s)'), $language['Language']['title'], $language['Language']['native']));

		$model->id = $id;
		$model->locale = $this->params['named']['locale'];
		$fields = $model->getTranslationFields();
		if (!empty($this->request->data)) {
			if ($model->saveTranslation($this->request->data)) {
				$this->Session->setFlash(__('Record has been translated'), 'default', array('class' => 'success'));
				$this->redirect(array(
					'action' => 'index',
					$id,
					$modelAlias,
				));
			} else {
				$this->Session->setFlash(__('Record could not be translated. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $model->read(null, $id);
		}
		$this->set(compact('fields', 'language', 'modelAlias', 'id'));
	}

/**
 * admin_delete
 *
 * @param integer $id
 * @param string $modelAlias
 * @param string $locale
 * @return void
 */
	public function admin_delete($id = null, $modelAlias = null, $locale = null) {
		if ($locale == null || $id == null) {
			$this->Session->setFlash(__('Invalid Locale or ID'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		if (!is_array(Configure::read('Translate.models.' . $modelAlias))) {
			$this->Session->setFlash(__('Invalid model.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		$model =& ClassRegistry::init($modelAlias);
		$record = $model->findById($id);
		if (!isset($record[$modelAlias]['id'])) {
			$this->Session->setFlash(__('Invalid record.'), 'default', array('class' => 'error'));
			$this->redirect(array(
				'plugin' => null,
				'controller' => Inflector::pluralize($modelAlias),
				'action' => 'index',
			));
		}

		$runtimeModel =& $model->translateModel();
		$runtimeModelAlias = $runtimeModel->alias;
		if ($runtimeModel->deleteAll(array(
				$runtimeModelAlias . '.model' => $modelAlias,
				$runtimeModelAlias . '.foreign_key' => $id,
				$runtimeModelAlias . '.locale' => $locale,
			))) {
			$this->Session->setFlash(__('Translation for the locale deleted successfully.'), 'default', array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('Translation for the locale could not be deleted.'), 'default', array('class' => 'error'));
		}

		$this->redirect(array(
			'action' => 'index',
			$id,
			$modelAlias,
		));
	}

}
