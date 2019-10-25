<?php

namespace Croogo\Settings\Controller\Admin;

use Cake\Event\Event;
use Cake\Utility\Inflector;
use Exception;

/**
 * Settings Controller
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class SettingsController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $this->_setupPrg();
    }

    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforeRedirect' => 'beforeCrudRedirect',
        ];
    }

    public function beforeCrudRedirect(Event $event)
    {
        if ($this->redirectToSelf($event)) {
            return;
        }
    }

    /**
     * Admin prefix
     *
     * @param string $prefix
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function prefix($prefix = null)
    {
        if ($this->getRequest()->is('post')) {
            try {
                $clearBackground = $this->getRequest()->getData('_clearbackground');
                if ($prefix == 'Theme' && $clearBackground) {
                    $bgImagePath = $this->Settings->find('search', ['search' => ['key' => 'Theme.bgImagePath']])->first()->value;
                    $fullpath = WWW_ROOT . $bgImagePath;
                    if (file_exists($fullpath)) {
                        unlink($fullpath);
                    }
                    $this->Settings->write('Theme.bgImagePath', '');
                    goto success;
                }

                foreach ($this->getRequest()->getData() as $inputName => $value) {
                    $id = str_replace('setting-', '', $inputName);
                    if (in_array($id, ['_apply', '_clearbackground'])) {
                        continue;
                    }
                    $setting = $this->Settings->get($id);

                    if (is_array($value)) {
                        if (array_key_exists('tmp_name', $value)) {
                            if (!empty($value['tmp_name'])) {
                                $value = $this->_handleUpload($setting, $value);
                            } else {
                                continue;
                            }
                        } else {
                            $value = json_encode($value);
                        }
                    }

                    $setting->value = $value;
                    $this->Settings->save($setting);
                }

success:
                $this->Flash->success(__d('croogo', 'Settings updated successfully'));
            } catch (Exception $e) {
                $this->Flash->error(__d('croogo', 'Settings cannot be updated: ' . $e->getMessage()));
            }

            return $this->redirect(['action' => 'prefix', $prefix]);
        }

        $settings = $this->Settings->find('all', [
            'order' => 'Settings.weight ASC',
            'conditions' => [
                'Settings.key LIKE' => $prefix . '.%',
                'Settings.editable' => 1,
            ],
        ]);

        if ($settings->count() == 0) {
            $this->Flash->error(__d('croogo', 'Invalid Setting key'));
        }

        $this->set(compact('prefix', 'settings'));
    }

    protected function _handleUpload($setting, $value)
    {
        $name = $value['name'];

        $currentBg = WWW_ROOT . $setting->value;
        if (file_exists($currentBg) && is_file($currentBg)) {
            unlink($currentBg);
        }

        $contentType = mime_content_type($value['tmp_name']);
        if (substr($contentType, 0, 5) !== 'image') {
            throw new Exception('Invalid file type');
        }

        $dotPosition = strripos($name, '.');
        $filename = strtolower(substr($name, 0, $dotPosition));
        $ext = strtolower(substr($name, $dotPosition + 1));

        $relativePath = DS . 'uploads' . DS .
            Inflector::slug($filename, '-') . '.' .
            $ext;
        $targetDir = WWW_ROOT . 'uploads';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777);
        }
        $targetFile = WWW_ROOT . $relativePath;
        move_uploaded_file($value['tmp_name'], $targetFile);
        $value = str_replace('\\', '/', $relativePath);

        return $value;
    }

    /**
     * Admin moveup
     *
     * @param int $id
     * @param int $step
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function moveup($id, $step = 1)
    {
        if ($this->Setting->moveUp($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved up successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move up'));
        }

        if (!$redirect = $this->referer()) {
            $redirect = [
                'admin' => true,
                'plugin' => 'Croogo/Settings',
                'controller' => 'Settings',
                'action' => 'index'
            ];
        }

        return $this->redirect($redirect);
    }

    /**
     * Admin moveup
     *
     * @param int $id
     * @param int $step
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function movedown($id, $step = 1)
    {
        if ($this->Setting->moveDown($id, $step)) {
            $this->Flash->success(__d('croogo', 'Moved down successfully'));
        } else {
            $this->Flash->error(__d('croogo', 'Could not move down'));
        }

        return $this->redirect(['admin' => true, 'controller' => 'Settings', 'action' => 'index']);
    }
}
