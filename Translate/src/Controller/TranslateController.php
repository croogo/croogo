<?php

namespace Croogo\Translate\Controller;

/**
 * Translate Controller
 *
 * @category Translate.Controller
 * @package  Croogo.Translate.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TranslateController extends TranslateAppController
{

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
    public $uses = [
        'Settings.Setting',
        'Settings.Language',
    ];

/**
 * admin_index
 *
 * @param int$id
 * @param string $modelAlias
 * @return void
 */
    public function admin_index($id = null, $modelAlias = null)
    {
        if ($id == null || $modelAlias == null) {
            $this->Flash->error(__d('croogo', 'Invalid ID.'));
            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $config = Configure::read('Translate.models.' . $modelAlias);
        list($plugin, $modelAlias) = pluginSplit($config['translateModel']);

        if (!is_array($config)) {
            $this->Flash->error(__d('croogo', 'Invalid model.'));
            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $model = ClassRegistry::init($config['translateModel']);
        $displayField = $model->displayField;
        $record = $model->findById($id);
        if (!isset($record[$modelAlias]['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid record.'));
            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }
        $this->set('title_for_layout', sprintf(__d('croogo', 'Translations: %s'), $record[$modelAlias][$displayField]));

        $runtimeModel = $model->translateModel();
        $runtimeModelAlias = $runtimeModel->alias;
        $translations = $runtimeModel->find('all', [
            'conditions' => [
                $runtimeModelAlias . '.model' => $modelAlias,
                $runtimeModelAlias . '.foreign_key' => $id,
                $runtimeModelAlias . '.field' => 'title',
            ],
        ]);

        $languages = $this->Language->find('list', [
            'fields' => ['alias', 'native'],
            'conditions' => [
                $this->Language->escapeField('status') => true,
            ],
        ]);

        $this->set(compact('runtimeModelAlias', 'translations', 'record', 'modelAlias', 'displayField', 'id', 'languages'));
    }

/**
 * admin_edit
 *
 * @param int$id
 * @param string $modelAlias
 * @return void
 */
    public function admin_edit($id = null, $modelAlias = null)
    {
        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__d('croogo', 'Invalid ID.'));
            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        if (!isset($this->request->params['named']['locale'])) {
            $this->Flash->error(__d('croogo', 'Invalid locale'));
            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $config = Configure::read('Translate.models.' . $modelAlias);
        list($plugin, $modelAlias) = pluginSplit($config['translateModel']);

        $language = $this->Language->find('first', [
            'conditions' => [
                'Language.alias' => $this->request->params['named']['locale'],
                'Language.status' => 1,
            ],
        ]);
        if (!isset($language['Language']['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid Language'));
            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $model = ClassRegistry::init($config['translateModel']);
        $displayField = $model->displayField;
        $record = $model->findById($id);
        if (!isset($record[$modelAlias]['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid record.'));
            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }
        $this->set('title_for_layout', sprintf(__d('croogo', 'Translate content: %s (%s)'), $language['Language']['title'], $language['Language']['native']));

        $model->id = $id;
        $model->locale = $this->request->params['named']['locale'];
        $fields = $model->getTranslationFields();
        if (!empty($this->request->data)) {
            if ($model->saveTranslation($this->request->data)) {
                $this->Flash->success(__d('croogo', 'Record has been translated'));
                $redirect = [
                    'action' => 'index',
                    $id,
                    $modelAlias,
                ];
                if (isset($this->request->data['apply'])) {
                    $redirect['action'] = 'edit';
                    $redirect['locale'] = $model->locale;
                }
                return $this->redirect($redirect);
            } else {
                $this->Flash->error(__d('croogo', 'Record could not be translated. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $model->read(null, $id);
        }
        $this->set(compact('fields', 'language', 'modelAlias', 'displayField', 'id'));
    }

/**
 * admin_delete
 *
 * @param int$id
 * @param string $modelAlias
 * @param string $locale
 * @return void
 */
    public function admin_delete($id = null, $modelAlias = null, $locale = null)
    {
        if ($locale == null || $id == null) {
            $this->Flash->error(__d('croogo', 'Invalid Locale or ID'));
            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $config = Configure::read('Translate.models.' . $modelAlias);
        list($plugin, $modelAlias) = pluginSplit($config['translateModel']);

        if (!is_array($config)) {
            $this->Flash->error(__d('croogo', 'Invalid model.'));
            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $model = ClassRegistry::init($config['translateModel']);
        $record = $model->findById($id);
        if (!isset($record[$modelAlias]['id'])) {
            $this->Flash->error(__d('croogo', 'Invalid record.'));
            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $runtimeModel = $model->translateModel();
        $runtimeModelAlias = $runtimeModel->alias;
        if ($runtimeModel->deleteAll([
                $runtimeModelAlias . '.model' => $modelAlias,
                $runtimeModelAlias . '.foreign_key' => $id,
                $runtimeModelAlias . '.locale' => $locale,
            ])) {
            $this->Flash->success(__d('croogo', 'Translation for the locale deleted successfully.'));
        } else {
            $this->Flash->error(__d('croogo', 'Translation for the locale could not be deleted.'));
        }

        return $this->redirect([
            'action' => 'index',
            $id,
            $modelAlias,
        ]);
    }
}
