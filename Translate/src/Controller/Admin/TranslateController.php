<?php
declare(strict_types=1);

namespace Croogo\Translate\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Translate Controller
 *
 * @category Translate.Controller
 * @package  Croogo.Translate.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class TranslateController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Croogo/Settings.Settings');
        $this->loadModel('Croogo/Settings.Languages');
    }

    /**
     * index
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $id = $this->getRequest()->getQuery('id');
        $modelAlias = $this->getRequest()->getQuery('model');
        if ($id == null) {
            $this->Flash->error(__d('croogo', 'Invalid ID.'));

            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $encoded = base64_encode($modelAlias);
        $config = Configure::read('Translate.models.' . $encoded);
        list($plugin, $model) = pluginSplit($modelAlias);

        if (!is_array($config)) {
            $this->Flash->error(__d('croogo', 'Invalid model.'));

            return $this->redirect([
                'plugin' => $plugin,
                'controller' => Inflector::pluralize($model),
                'action' => 'index',
            ]);
        }

        $Model = TableRegistry::getTableLocator()->get($modelAlias);
        $displayField = $Model->getDisplayField();
        $record = $Model->get($id);
        if (!isset($record->id)) {
            $this->Flash->error(__d('croogo', 'Invalid record.'));

            return $this->redirect([
                'plugin' => $plugin,
                'controller' => $model,
                'action' => 'index',
            ]);
        }
        $this->set('title_for_layout', sprintf(__d('croogo', 'Translations: %s'), $record->get($displayField)));

        $translations = $Model->find('translations')
            ->where([
                $Model->aliasField('id') => $id,
            ])->first();

        $languages = $this->Languages->find('list', [
            'keyField' => 'locale',
            'valueField' => function ($language) {
                return $language->get('label');
            },
            'conditions' => [
                $this->Languages->aliasField('status') => true,
            ],
        ])->toArray();

        $this->set(compact('translations', 'record', 'modelAlias', 'displayField', 'id', 'languages'));
    }

    /**
     * edit
     *
     * @return \Cake\Http\Response|void
     */
    public function editTranslation()
    {
        $id = $this->getRequest()->getQuery('id');
        $modelAlias = urldecode($this->getRequest()->getQuery('model'));
        $locale = $this->getRequest()->getQuery('locale');

        if (!$id && empty($this->getRequest()->getData())) {
            $this->Flash->error(__d('croogo', 'Invalid ID.'));

            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        if (!$locale) {
            $this->Flash->error(__d('croogo', 'Invalid locale'));

            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $encoded = base64_encode($modelAlias);
        $config = Configure::read('Translate.models.' . $encoded);
        list($plugin, $model) = pluginSplit($modelAlias);

        $language = $this->Languages->find()
            ->where([
                'locale' => $locale,
                'status' => 1,
            ])->first();
        if (!$language->id) {
            $this->Flash->error(__d('croogo', 'Invalid Language'));

            return $this->redirect([
                'plugin' => $plugin,
                'controller' => $model,
                'action' => 'index',
            ]);
        }

        $Model = TableRegistry::getTableLocator()->get($modelAlias);
        $displayField = $Model->getDisplayField();
        $record = $Model->get($id);
        if (!$record->id) {
            $this->Flash->error(__d('croogo', 'Invalid record.'));

            return $this->redirect([
                'plugin' => $plugin,
                'controller' => $model,
                'action' => 'index',
            ]);
        }

        $fields = $config['fields'];

        $entity = $Model
            ->find('translations', [
                'locales' => [$locale],
            ])
            ->where([$Model->aliasField('id') => $id])
            ->first();

        if (!empty($this->getRequest()->getData())) {
            $entity->_locale = $locale;
            $entity->setAccess('_translations', true);
            $entity = $Model->patchEntity($entity, $this->getRequest()->getData(), [
                'translations' => true,
            ]);
            if ($Model->save($entity)) {
                $this->Flash->success(__d('croogo', 'Record has been translated'));
                $redirect = [
                    'controller' => 'Translate',
                    'action' => 'index',
                    '?' => [
                        'id' => $id,
                        'model' => $modelAlias,
                        'locale' => $locale,
                    ],
                ];
                if ($this->getRequest()->getData('_apply') !== null) {
                    $redirect['action'] = 'editTranslation';
                }

                return $this->redirect($redirect);
            } else {
                $this->Flash->error(__d('croogo', 'Record could not be translated. Please, try again.'));
            }
        }
        $this->set(compact(
            'entity',
            'fields',
            'language',
            'model',
            'modelAlias',
            'displayField',
            'id',
            'locale'
        ));
    }

    /**
     * delete
     *
     * @param int $id
     * @param string $modelAlias
     * @param string $locale
     * @return \Cake\Http\Response|void
     */
    public function deleteTranslation($id = null, $modelAlias = null, $locale = null)
    {
        if ($locale == null || $id == null) {
            $this->Flash->error(__d('croogo', 'Invalid Locale or ID'));

            return $this->redirect([
                'plugin' => null,
                'controller' => Inflector::pluralize($modelAlias),
                'action' => 'index',
            ]);
        }

        $encoded = base64_encode($modelAlias);
        $config = Configure::read('Translate.models.' . $encoded);
        list($plugin, $model) = pluginSplit($modelAlias);

        if (!is_array($config)) {
            $this->Flash->error(__d('croogo', 'Invalid model.'));

            return $this->redirect([
                'plugin' => $plugin,
                'controller' => $model,
                'action' => 'index',
            ]);
        }

        $Model = TableRegistry::getTableLocator()->get($modelAlias);
        $record = $Model->get($id);
        if (!isset($record->id)) {
            $this->Flash->error(__d('croogo', 'Invalid record.'));

            return $this->redirect([
                'plugin' => $plugin,
                'controller' => $model,
                'action' => 'index',
            ]);
        }

        if ($Model->deleteTranslation($record, $locale)) {
            $this->Flash->success(__d('croogo', 'Translation for the locale deleted successfully.'));
        } else {
            $this->Flash->error(__d('croogo', 'Translation for the locale could not be deleted.'));
        }

        return $this->redirect([
            'action' => 'index',
            '?' => [
                'id' => $id,
                'model' => $modelAlias,
            ],
        ]);
    }
}
