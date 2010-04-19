<?php
/**
 * Extensions Hooks Controller
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
class ExtensionsHooksController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
    public $name = 'ExtensionsHooks';
/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
    public $uses = array('Setting', 'User');

    public function beforeFilter() {
        parent::beforeFilter();
        App::import('Core', 'File');
        APP::import('Core', 'Folder');
    }

    public function admin_index() {
        $this->set('title_for_layout', __('Hooks', true));

        $hooks = array();
        $hooks['App'] = $this->Croogo->getHooks();
        $plugins = $this->Croogo->getPlugins();
        foreach ($plugins AS $pluginAlias) {
            $camlizedPluginAlias = Inflector::camelize($pluginAlias);
            $hooks[$camlizedPluginAlias] = $this->Croogo->getHooks($pluginAlias);
        }
        $this->set(compact('hooks'));
    }

    public function admin_toggle($hook = null) {
        if (!isset($this->params['named']['token']) || ($this->params['named']['token'] != $this->params['_Token']['key'])) {
            $blackHoleCallback = $this->Security->blackHoleCallback;
            $this->$blackHoleCallback();
        }

        if (strstr(Inflector::underscore($hook), '_helper')) {
            $configureKey = 'Hook.helpers';
            $hookType = 'Helper';
        } else {
            $configureKey = 'Hook.components';
            $hookType = 'Component';
        }
        $hookTitle = str_replace($hookType, '', $hook);

        // toggle hook
        $status = 0;
        if (!Configure::read($configureKey)) {
            $this->Setting->write($configureKey, $hookTitle);
            $status = 1;
            $this->Session->setFlash(sprintf(__('%s activated successfully.', true), $hook));
        } else {
            $hookItems = explode(',', Configure::read($configureKey));
            if(array_search($hookTitle, $hookItems) !== false) {
                $k = array_search($hookTitle, $hookItems);
                unset($hookItems[$k]);
                $hookItems = implode(',', $hookItems);
                $this->Setting->write($configureKey, $hookItems);
                $status = 0;
                $this->Session->setFlash(sprintf(__('%s deactivated successfully.', true), $hook));
            } else {
                $hookItems[] = $hookTitle;
                $hookItems = implode(',', $hookItems);
                $this->Setting->write($configureKey, $hookItems);
                $status = 1;
                $this->Session->setFlash(sprintf(__('%s activated successfully.', true), $hook));
            }
        }

        // callback for activate/deactivate
        App::import($hookType, $hookTitle);
        if (strstr($hookTitle, '.')) {
            $hookTitleE = explode('.', $hookTitle);
            $hookTitle = $hookTitleE['1'];
        }
        if ($status == 1) {
            $method = 'onActivate';
        } else {
            $method = 'onDeactivate';
        }
        if ($hookType == 'Component') {
            if (isset($this->Croogo->{$hookTitle})) {
                $hookInstance = $this->Croogo->{$hookTitle};
            } else {
                $hookClassName = $hookTitle.'Component';
                $hookInstance =& new $hookClassName;
            }
        } else {
            $hookClassName = $hookTitle.'Helper';
            $hookInstance =& new $hookClassName;
        }
        if (method_exists($hookInstance, $method)) {
            $hookInstance->$method($this);
        }

        $this->redirect(array(
            'action' => 'index',
        ));
    }

}
?>