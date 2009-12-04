<?php
/**
 * Croogo Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoComponent extends Object {
/**
 * Other components used by this component
 *
 * @var array
 * @access public
 */
    var $components = array(
        'Session',
        'Theme',
    );
/**
 * Hook components
 *
 * @var array
 * @access public
 */
    var $hooks = array();
/**
 * Role ID of current user
 *
 * Default is 3 (public)
 *
 * @var integer
 * @access public
 */
    var $roleId = 3;
/**
 * Menus for layout
 *
 * @var string
 * @access public
 */
    var $menus_for_layout = array();
 /**
 * Blocks for layout
 *
 * @var string
 * @access public
 */
    var $blocks_for_layout = array();
 /**
 * Vocabularies for layout
 *
 * @var string
 * @access public
 */
    var $vocabularies_for_layout = array();
 /**
 * Types for layout
 *
 * @var string
 * @access public
 */
    var $types_for_layout = array();
/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
    function startup(&$controller) {
        $this->__loadHooks();

        $this->controller =& $controller;
        App::import('Xml');

        if ($this->Session->check('Auth.User.id')) {
            $this->roleId = $this->Session->read('Auth.User.role_id');
        }

        if (!isset($this->controller->params['admin'])) {
            $this->blocks();
            $this->menus();
            $this->vocabularies();
            $this->types();
        }

        $this->hook('startup');
    }
/**
 * Load hooks as components
 *
 * @return void
 */
    function __loadHooks() {
        if (Configure::read('Hook.components')) {
            // Set hooks
            $hooks = Configure::read('Hook.components');
            $hooksE = explode(',', $hooks);

            foreach ($hooksE AS $hook) {
                if (strstr($hook, '.')) {
                    $hookE = explode('.', $hook);
                    $plugin = $hookE['0'];
                    $hookComponent = $hookE['1'];
                    $filePath = APP.'plugins'.DS.$plugin.DS.'controllers'.DS.'components'.DS.Inflector::underscore($hookComponent).'.php';
                } else {
                    $plugin = null;
                    $filePath = APP.'controllers'.DS.'components'.DS.Inflector::underscore($hook).'.php';
                }

                if (file_exists($filePath)) {
                    $this->hooks[] = $hook;
                }
            }

            // Set hooks as components
            foreach ($this->hooks AS $hook) {
                $componentName = $hook;
                if (strstr($hook, '.')) {
                    $hookE = explode('.', $hook);
                    $componentName = $hookE['0'];
                }
                $componentClassName = $componentName.'Component';

                $this->components[] = $hook;
                App::import('Component', $hook);
                $this->{$componentName} =& new $componentClassName;
            }
        }
    }
/**
 * Blocks
 *
 * Blocks will be available in this variable in views: $blocks_for_layout
 *
 * @return void
 */
    function blocks() {
        $regions = $this->controller->Block->Region->find('list', array(
            'conditions' => array(
                'Region.block_count >' => '0',
            ),
            'fields' => array(
                'Region.id',
                'Region.alias'
            ),
        ));
        foreach ($regions AS $regionId => $regionAlias) {
            $this->blocks_for_layout[$regionAlias] = array();
            $findOptions = array(
                'conditions' => array(
                    'Block.status' => 1,
                    'Block.region_id' => $regionId,
                    'AND' => array(
                        array(
                            'OR' => array(
                                'Block.visibility_roles' => '',
                                'Block.visibility_roles LIKE' => '%"' . $this->roleId . '"%',
                            ),
                        ),
                        array(
                            'OR' => array(
                                'Block.visibility_paths' => '',
                                'Block.visibility_paths LIKE' => '%"' . $this->controller->params['url']['url'] . '"%',
                                //'Block.visibility_paths LIKE' => '%"' . 'controller:' . $this->params['controller'] . '"%',
                                //'Block.visibility_paths LIKE' => '%"' . 'controller:' . $this->params['controller'] . '/' . 'action:' . $this->params['action'] . '"%',
                            ),
                        ),
                    ),
                ),
                'order' => array(
                    'Block.weight' => 'ASC'
                ),
                'recursive' => '-1',
            );
            $this->blocks_for_layout[$regionAlias] = $this->controller->Block->find('all', $findOptions);
        }
    }
/**
 * Menus
 *
 * Menus will be available in this variable in views: $menus_for_layout
 *
 * @return void
 */
    function menus() {
        if (Configure::read('Site.theme') == 'default' ||
            Configure::read('Site.theme') == null) {
            $themeXml =& new XML(WWW_ROOT . 'theme.xml');
        } else {
            $themeXml =& new XML(WWW_ROOT . 'themed' . DS . Configure::read('Site.theme') . DS . 'theme.xml');
        }
        $themeXmlArray = $themeXml->toArray(false);
        $menus = array();
        if (is_array($themeXmlArray['theme']['menus']['menu'])) {
            $menus = $themeXmlArray['theme']['menus']['menu'];
        } else {
            $menus = array($themeXmlArray['theme']['menus']['menu']);
        }

        // check for [menu:alias] in blocks
        foreach ($this->blocks_for_layout AS $regionAlias => $blocks) {
            foreach ($blocks AS $block) {
                preg_match_all('/\[(menu|m):([A-Za-z0-9_\-]*)(.*?)\]/i', $block['Block']['body'], $tagMatches);
                for($i=0; $i < count($tagMatches[1]); $i++){
                    $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
                    preg_match_all($regex, $tagMatches[3][$i], $attributes);
                    $menuAlias = $tagMatches[2][$i];
                    $options = array();
                    for($j=0; $j < count($attributes[0]); $j++){
                        $options[$attributes[1][$j]] = $attributes[2][$j];
                    }
                    if (!in_array($menuAlias, $menus)) {
                        $menus[] = $menuAlias;
                    }
                }
            }
        }

        foreach ($menus AS $menuAlias) {
            $menu = $this->controller->Link->Menu->find('first', array(
                'conditions' => array(
                    'Menu.status' => 1,
                    'Menu.alias' => $menuAlias,
                    'Menu.link_count >' => 0,
                ),
                'recursive' => '-1',
            ));
            if (isset($menu['Menu']['id'])) {
                $this->menus_for_layout[$menuAlias] = array();
                $this->menus_for_layout[$menuAlias]['Menu'] = $menu['Menu'];
                $findOptions = array(
                    'conditions' => array(
                        'Link.menu_id' => $menu['Menu']['id'],
                        'Link.status' => 1,
                        'AND' => array(
                            array(
                                'OR' => array(
                                    'Link.visibility_roles' => '',
                                    'Link.visibility_roles LIKE' => '%"' . $this->roleId . '"%',
                                ),
                            ),
                        ),
                    ),
                    'order' => array(
                        'Link.lft' => 'ASC',
                    ),
                    'recursive' => -1,
                );
                $links = $this->controller->Link->find('threaded', $findOptions);
                $this->menus_for_layout[$menuAlias]['threaded'] = $links;
            }
        }
    }
/**
 * Vocabularies
 *
 * Vocabularies will be available in this variable in views: $vocabularies_for_layout
 *
 * @return void
 */
    function vocabularies() {
        $vocabularies = array();

        // check for [vocabulary:alias] in blocks
        foreach ($this->blocks_for_layout AS $regionAlias => $blocks) {
            foreach ($blocks AS $block) {
                preg_match_all('/\[(vocabulary|v):([A-Za-z0-9_\-]*)(.*?)\]/i', $block['Block']['body'], $tagMatches);
                for($i=0; $i < count($tagMatches[1]); $i++){
                    $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
                    preg_match_all($regex, $tagMatches[3][$i], $attributes);
                    $vocabularyAlias = $tagMatches[2][$i];
                    $options = array();
                    for($j=0; $j < count($attributes[0]); $j++){
                        $options[$attributes[1][$j]] = $attributes[2][$j];
                    }
                    if (!in_array($vocabularyAlias, $vocabularies)) {
                        $vocabularies[$vocabularyAlias] = $options;
                    }
                }
            }
        }

        foreach ($vocabularies AS $vocabularyAlias => $options) {
            $vocabulary = $this->controller->Type->Vocabulary->find('first', array(
                'conditions' => array(
                    'Vocabulary.alias' => $vocabularyAlias,
                ),
                'recursive' => '-1',
            ));

            if (isset($vocabulary['Vocabulary']['id'])) {
                $terms = $this->controller->Type->Vocabulary->Term->find('list', array(
                    'conditions' => array(
                        'Term.vocabulary_id' => $vocabulary['Vocabulary']['id'],
                        'Term.status' => 1,
                    ),
                    'fields' => array(
                        'Term.slug',
                        'Term.title',
                    ),
                    'order' => 'Term.slug ASC',
                    'recursive' => '-1',
                ));
                $this->vocabularies_for_layout[$vocabularyAlias] = array();
                $this->vocabularies_for_layout[$vocabularyAlias]['Vocabulary'] = $vocabulary['Vocabulary'];
                $this->vocabularies_for_layout[$vocabularyAlias]['list'] = $terms;
            }
        }
    }
/**
 * Types
 *
 * Types will be available in this variable in views: $types_for_layout
 *
 * @return void
 */
    function types() {
        $types = $this->controller->Type->find('all', array(
            'recursive' => '-1',
        ));
        foreach ($types AS $type) {
            $alias = $type['Type']['alias'];
            $this->types_for_layout[$alias] = $type;
        }
    }
/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
    function beforeRender(&$controller) {
        $this->controller =& $controller;
        $this->controller->set('blocks_for_layout', $this->blocks_for_layout);
        $this->controller->set('menus_for_layout', $this->menus_for_layout);
        $this->controller->set('vocabularies_for_layout', $this->vocabularies_for_layout);
        $this->controller->set('types_for_layout', $this->types_for_layout);

        $this->hook('beforeRender');
    }
/**
 * Extracts parameters from 'filter' named parameter.
 *
 * @return array
 */
    function extractFilter() {
        $filter = explode(';', $this->controller->params['named']['filter']);
        $filterData = array();
        foreach ($filter AS $f) {
            $fData = explode(':', $f);
            $fKey = $fData['0'];
            if ($fKey != null) {
                $filterData[$fKey] = $fData['1'];
            }
        }
        return $filterData;
    }
/**
 * Get URL relative to the app
 *
 * @param array $url
 * @return array
 */
    function getRelativePath($url = '/') {
        if (is_array($url)) {
            $absoluteUrl = Router::url($url, true);
        } else {
            $absoluteUrl = Router::url('/' . $url, true);
        }
        $path = '/' . str_replace(Router::url('/', true), '', $absoluteUrl);
        return $path;
    }
/**
 * Hook
 *
 * Used for calling hook methods from other HookComponents
 *
 * @param string $methodName
 * @return void
 */
    function hook($methodName) {
        if (isset($this->controller->params['admin'])) {
            return;
        }

        foreach ($this->hooks AS $hook) {
            if (strstr($hook, '.')) {
                $hookE = explode('.', $hook);
                $hook = $hookE['1'];
            }

            if (method_exists($this->{$hook}, $methodName)) {
                $this->{$hook}->$methodName($this->controller);
            }
        }
    }
/**
 * Shutdown
 *
 * @param object $controller instance of controller
 * @return void
 */
    function shutdown(&$controller) {
        $this->hook('shutdown');
    }

}
?>