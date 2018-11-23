<?php

namespace Croogo\Extensions\Controller\Admin;

use Cake\Core\Configure;
use Croogo\Extensions\CroogoTheme;
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
class ThemesController extends AppController
{

    /**
     * CroogoTheme instance
     * @var \Croogo\Extensions\CroogoTheme
     */
    protected $_CroogoTheme = false;

    /**
     * Constructor
     */
    public function initialize(array $config = [])
    {
        parent::initialize($config);
        $this->_CroogoTheme = new CroogoTheme();
    }

    /**
     * Admin index
     *
     * @return void
     */
    public function index()
    {
        $this->set('title_for_layout', __d('croogo', 'Themes'));

        $themes = $this->_CroogoTheme->getThemes();
        $themesData = [];
        foreach ($themes as $theme => $path) {
            $themesData[$theme] = $this->_CroogoTheme->getData($theme, $path);
        }

        $activeTheme = Configure::read('Site.theme');
        if (empty($activeTheme)) {
            $activeTheme = 'Croogo/Core';
        }
        $currentTheme = $this->_CroogoTheme->getData($activeTheme);
        $this->set(compact('themes', 'themesData', 'currentTheme'));
    }

    /**
     * Admin activate
     *
     * @param string $theme
     */
    public function activate($theme = null)
    {
        try {
            $this->_CroogoTheme->activate($theme);

            $this->Flash->success(__d('croogo', 'Theme activated.'));
        } catch (MissingThemeException $exception) {
            $this->Flash->error(__d('croogo', 'Theme activation failed: %s', $exception->getMessage()));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Admin add
     *
     * @return void
     */
    public function add()
    {
        $this->set('title_for_layout', __d('croogo', 'Upload a new theme'));

        if (!empty($this->request->data)) {
            $file = $this->request->data['file'];
            unset($this->request->data['file']);

            $Installer = new ExtensionsInstaller;
            try {
                $Installer->extractTheme($file['tmp_name']);
                $this->Flash->success(__d('croogo', 'Theme uploaded successfully.'));
            } catch (CakeException $e) {
                $this->Flash->error($e->getMessage());
            }

            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Admin editor
     *
     * @return void
     */
    public function editor()
    {
        $this->set('title_for_layout', __d('croogo', 'Theme Editor'));
    }

    /**
     * Admin save
     *
     * @return void
     */
    public function save()
    {
    }

    /**
     * Admin delete
     *
     * @param string $alias
     * @return void
     */
    public function delete($alias = null)
    {
        if ($alias == null) {
            $this->Flash->error(__d('croogo', 'Invalid Theme.'));

            return $this->redirect(['action' => 'index']);
        }

        if ($alias == 'Croogo/Core') {
            $this->Flash->error(__d('croogo', 'Default theme cannot be deleted.'));

            return $this->redirect(['action' => 'index']);
        } elseif ($alias == Configure::read('Site.theme')) {
            $this->Flash->error(__d('croogo', 'You cannot delete a theme that is currently active.'));

            return $this->redirect(['action' => 'index']);
        }

        $result = $this->_CroogoTheme->delete($alias);

        if ($result === true) {
            $this->Flash->success(__d('croogo', 'Theme deleted successfully.'));
        } elseif (!empty($result[0])) {
            $this->Flash->error($result[0]);
        } else {
            $this->Flash->error(__d('croogo', 'An error occurred.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
