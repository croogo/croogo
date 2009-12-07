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
    var $name = 'ExtensionsHooks';
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
        APP::import('Core', 'Folder');
    }

    function admin_index() {
        $this->pageTitle = __('Hooks', true);

        $hooks = array();
        $plugins = Configure::listObjects('plugin');
        $folder =& new Folder;

        // Components
        $appComponents = Configure::listObjects('component', APP.'controllers'.DS.'components');
        $pluginComponents = array();
        foreach ($plugins AS $plugin) {
            $folder->path = APP.'plugins'.DS.Inflector::underscore($plugin).DS.'controllers'.DS.'components';
            $content = $folder->read();
            foreach ($content['1'] AS $componentFile) {
                $pluginComponents[] = $plugin.'.'.Inflector::camelize(str_replace('.php', '', $componentFile));
            }
        }
        $components = array_merge($appComponents, $pluginComponents);
        $i = 0;
        foreach ($components AS $component) {
            $components[$i] = $component.'Component';
            $i++;
        }

        // Helpers
        $appHelpers = Configure::listObjects('helper', APP.'views'.DS.'helpers');
        $pluginHelpers = array();
        foreach ($plugins AS $plugin) {
            $folder->path = APP.'plugins'.DS.Inflector::underscore($plugin).DS.'views'.DS.'helpers';
            $content = $folder->read();
            foreach ($content['1'] AS $helperFile) {
                $pluginHelpers[] = $plugin.'.'.Inflector::camelize(str_replace('.php', '', $helperFile));
            }
        }
        $helpers = array_merge($appHelpers, $pluginHelpers);
        $i = 0;
        foreach ($helpers AS $helper) {
            $helpers[$i] = $helper.'Helper';
            $i++;
        }

        // Get only hook helpers/components
        $items = array_merge($components, $helpers);
        foreach ($items AS $item) {
            if (strstr(Inflector::underscore($item), '_hook_component') ||
                strstr(Inflector::underscore($item), '_hook_helper')) {
                $hooks[] = $item;
            }
        }
        sort($hooks);

        // Configuration
        $siteHookComponents = explode(',', Configure::read('Hook.components'));
        $siteHookHelpers = explode(',', Configure::read('Hook.helpers'));
        //$siteHooks = array_merge($hookComponents, $hookHelpers);
        $siteHooks = array();
        foreach ($siteHookComponents AS $siteHookComponent) {
            if (!$siteHookComponent) continue;
            $siteHooks[] = $siteHookComponent.'Component';
        }
        foreach ($siteHookHelpers AS $siteHookHelper) {
            if (!$siteHookHelper) continue;
            $siteHooks[] = $siteHookHelper.'Helper';
        }

        $this->set(compact('hooks', 'siteHookComponents', 'siteHookHelpers', 'siteHooks'));
    }

    function admin_toggle($hook = null) {
        $this->layout = 'ajax';

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
        } else {
            $hookItems = explode(',', Configure::read($configureKey));
            if(array_search($hookTitle, $hookItems) !== false) {
                $k = array_search($hookTitle, $hookItems);
                unset($hookItems[$k]);
                $hookItems = implode(',', $hookItems);
                $this->Setting->write($configureKey, $hookItems);
                $status = 0;
            } else {
                $hookItems[] = $hookTitle;
                $hookItems = implode(',', $hookItems);
                $this->Setting->write($configureKey, $hookItems);
                $status = 1;
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

        $this->set(compact('status', 'hook', 'hookTitle'));
    }

}
?>