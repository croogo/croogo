<?php
/**
 * Themes Controller
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
class ThemesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    var $name = 'Themes';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    var $uses = array('Setting', 'User');

    function beforeFilter() {
        parent::beforeFilter();
        App::import('Core', 'File');
        App::import('Xml');
    }

    function admin_index() {
        $this->pageTitle = __('Themes', true);

        $themes = $this->Theme->getThemes();
        $themesData = array();
        $themesData[] = $this->Theme->getData();
        foreach ($themes AS $theme) {
            $themesData[] = $this->Theme->getData($theme);
        }

        $currentTheme = $this->Theme->getData(Configure::read('Site.theme'));
        $this->set(compact('themes', 'themesData', 'currentTheme'));
    }

    function admin_activate($alias = null) {
        if ($alias == 'default') {
            $alias = null;
        }

        $siteTheme = $this->Setting->findByKey('Site.theme');
        $siteTheme['Setting']['value'] = $alias;
        $this->Setting->save($siteTheme);
        $this->Session->setFlash(__('Theme activated.', true));

        $this->redirect(array('action' => 'index'));
    }

    function admin_add() {
        $this->pageTitle = __('Add a new theme', true);
    }

}
?>