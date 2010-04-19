<?php
/**
 * Layout Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LayoutHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    public $helpers = array(
        'Html',
        'Form',
        'Session',
        'Js',
    );
/**
 * Current Node
 *
 * @var array
 * @access public
 */
    public $node = null;
/**
 * Hook helpers
 *
 * @var array
 * @access public
 */
    public $hooks = array();
/**
 * Constructor
 *
 * @param array $options options
 * @access public
 */
    public function __construct($options = array()) {
        $this->View =& ClassRegistry::getObject('view');
        $this->__loadHooks();

        return parent::__construct($options);
    }
/**
 * Load hooks as helpers
 *
 * @return void
 */
    private function __loadHooks() {
        if (Configure::read('Hook.helpers')) {
            // Set hooks
            $hooks = Configure::read('Hook.helpers');
            $hooksE = explode(',', $hooks);
            foreach ($hooksE AS $hook) {
                if (strstr($hook, '.')) {
                    $hookE = explode('.', $hook);
                    $plugin = $hookE['0'];
                    $hookHelper = $hookE['1'];
                    $filePath = APP.'plugins'.DS.Inflector::underscore($plugin).DS.'views'.DS.'helpers'.DS.Inflector::underscore($hookHelper).'.php';
                } else {
                    $plugin = null;
                    $filePath = APP.'views'.DS.'helpers'.DS.Inflector::underscore($hook).'.php';
                }

                if (file_exists($filePath)) {
                    $this->hooks[] = $hook;
                }
            }

            // Set hooks as helpers
            foreach ($this->hooks AS $hook) {
                $this->helpers[] = $hook;
            }
        }
    }
/**
 * Javascript variables
 *
 * @return string
 */
    public function js() {
        $output = $this->Html->script('croogo');

        $croogo = array();
        $croogo['basePath'] = Router::url('/');
        $croogo['params'] = array(
            'controller' => $this->params['controller'],
            'action' => $this->params['action'],
            'named' => $this->params['named'],
        );
        $output .= $this->Html->scriptBlock('$.extend(Croogo, ' . $this->Js->object($croogo) . ');');

        echo $output;
    }
/**
 * Status
 *
 * instead of 0/1, show tick/cross
 *
 * @param integer $value 0 or 1
 * @return string formatted img tag
 */
    public function status($value) {
        if ($value == 1) {
            $output = $this->Html->image('/img/icons/tick.png');
        } else {
            $output = $this->Html->image('/img/icons/cross.png');
        }
        return $output;
    }
/**
 * Show flash message
 *
 * @return void
 */
    public function sessionFlash() {
        $messages = $this->Session->read('Message');
        if( is_array($messages) ) {
            foreach(array_keys($messages) AS $key) {
                echo $this->Session->flash($key);
            }
        }
    }
/**
 * Meta tags
 *
 * @return string
 */
    public function meta($metaForLayout = array()) {
        $_metaForLayout = array();
        if (is_array(Configure::read('Meta'))) {
            $_metaForLayout = Configure::read('Meta');
        }

        if (count($metaForLayout) == 0 &&
            isset($this->View->viewVars['node']['CustomFields']) &&
            count($this->View->viewVars['node']['CustomFields']) > 0) {
            $metaForLayout = array();
            foreach ($this->View->viewVars['node']['CustomFields'] AS $key => $value) {
                if (strstr($key, 'meta_')) {
                    $key = str_replace('meta_', '', $key);
                    $metaForLayout[$key] = $value;
                }
            }
        }

        $metaForLayout = array_merge($_metaForLayout, $metaForLayout);

        $output = '';
        foreach ($metaForLayout AS $name => $content) {
            $output .= '<meta name="' . $name . '" content="' . $content . '" />';
        }

        return $output;
    }
/**
 * isLoggedIn
 *
 * if User is logged in
 *
 * @return boolean
 */
    public function isLoggedIn() {
        if ($this->Session->check('Auth.User.id')) {
            return true;
        } else {
            return false;
        }
    }
/**
 * Feed
 *
 * RSS feeds
 *
 * @param boolean $returnUrl if true, only the URL will be returned
 * @return string
 */
    public function feed($returnUrl = false) {
        if (Configure::read('Site.feed_url')) {
            $url = Configure::read('Site.feed_url');
        } else {
            /*$url = Router::url(array(
                'controller' => 'nodes',
                'action' => 'index',
                'type' => 'blog',
                'ext' => 'rss',
            ));*/
            $url = '/nodes/promoted.rss';
        }

        if ($returnUrl) {
            $output = $url;
        } else {
            $url = Router::url($url);
            $output = '<link href="' . $url . '" type="application/rss+xml" rel="alternate" title="RSS 2.0" />';
            return $output;
        }

        return $output;
    }
/**
 * Get Role ID
 *
 * @return integer
 */
    public function getRoleId() {
        if ($this->isLoggedIn()) {
            $roleId = $this->Session->read('Auth.User.role_id');
        } else {
            // Public
            $roleId = 3;
        }
        return $roleId;
    }
/**
 * Region is empty
 *
 * returns true if Region has no Blocks.
 *
 * @param string $regionAlias Region alias
 * @return boolean
 */
    public function regionIsEmpty($regionAlias) {
        if (isset($this->View->viewVars['blocks_for_layout'][$regionAlias]) &&
            count($this->View->viewVars['blocks_for_layout'][$regionAlias]) > 0) {
            return false;
        } else {
            return true;
        }
    }
/**
 * Show Blocks for a particular Region
 *
 * @param string $regionAlias Region alias
 * @param array $findOptions (optional)
 * @return string
 */
    public function blocks($regionAlias, $options = array()) {
        $_options = array();
        $options = array_merge($_options, $options);

        $output = '';
        if (!$this->regionIsEmpty($regionAlias)) {
            $blocks = $this->View->viewVars['blocks_for_layout'][$regionAlias];
            foreach ($blocks AS $block) {
                $plugin = false;
                if ($block['Block']['element'] != null) {
                    if (strstr($block['Block']['element'], '.')) {
                        $plugin_element = explode('.', $block['Block']['element']);
                        $plugin  = $plugin_element[0];
                        $element = $plugin_element[1];
                    } else {
                        $element = $block['Block']['element'];
                    }
                } else {
                    $element = 'block';
                }
                if ($plugin) {
                    $output .= $this->View->element($element, array('block' => $block, 'plugin' => $plugin));
                } else {
                    $output .= $this->View->element($element, array('block' => $block));
                }
            }
        }

        return $output;
    }
/**
 * Show Menu by Alias
 *
 * @param string $menuAlias Menu alias
 * @param array $options (optional)
 * @return string
 */
    public function menu($menuAlias, $options = array()) {
        $_options = array(
            'findOptions' => array(),
            'tag' => 'ul',
            'tagAttributes' => array(),
            'containerTag' => 'div',
            'containerTagAttr' => array(
                'class' => 'menu',
            ),
            'selected' => 'selected',
            'dropdown' => false,
            'dropdownClass' => 'sf-menu',
        );
        $options = array_merge($_options, $options);

        if (!isset($this->View->viewVars['menus_for_layout'][$menuAlias])) {
            return false;
        }

        $menu = $this->View->viewVars['menus_for_layout'][$menuAlias];

        $options['containerTagAttr']['id'] = 'menu-' . $this->View->viewVars['menus_for_layout'][$menuAlias]['Menu']['id'];
        $options['containerTagAttr']['class'] .= ' menu';

        $links = $this->View->viewVars['menus_for_layout'][$menuAlias]['threaded'];
        $linksList = $this->nestedLinks($links, $options);
        $output = $this->Html->tag($options['containerTag'], $linksList, $options['containerTagAttr']);

        return $output;
    }
/**
 * Nested Links
 *
 * @param array $links model output (threaded)
 * @param array $options (optional)
 * @param integer $depth depth level
 * @return string
 */
    public function nestedLinks($links, $options = array(), $depth = 1) {
        $_options = array();
        $options = array_merge($_options, $options);

        $output = '';
        foreach ($links AS $link) {
            $linkAttr = array(
                'id' => 'link-' . $link['Link']['id'],
                'rel' => $link['Link']['rel'],
                'target' => $link['Link']['target'],
                'title' => $link['Link']['description'],
            );

            foreach ($linkAttr AS $attrKey => $attrValue) {
                if ($attrValue == null) {
                    unset($linkAttr[$attrKey]);
                }
            }

            // if link is in the format: controller:contacts/action:view
            if (strstr($link['Link']['link'], 'controller:')) {
                $link['Link']['link'] = $this->linkStringToArray($link['Link']['link']);
            }

            if (Router::url($link['Link']['link']) == Router::url('/' . $this->params['url']['url'])) {
                $linkAttr['class'] = $options['selected'];
            }

            $linkOutput = $this->Html->link($link['Link']['title'], $link['Link']['link'], $linkAttr);
            if (isset($link['children']) && count($link['children']) > 0) {
                $linkOutput .= $this->nestedLinks($link['children'], $options, $depth + 1);
            }
            $linkOutput = $this->Html->tag('li', $linkOutput);
            $output .= $linkOutput;
        }
        if ($output != null) {
            $tagAttr = $options['tagAttributes'];
            if ($options['dropdown'] && $depth == 1) {
                $tagAttr['class'] = $options['dropdownClass'];
            }
            $output = $this->Html->tag($options['tag'], $output, $tagAttr);
        }

        return $output;
    }
/**
 * Converts strings like controller:abc/action:xyz/ to arrays
 *
 * @param string $link link
 * @return array
 */
    public function linkStringToArray($link) {
        $link = explode('/', $link);
        $linkArr = array();
        foreach ($link AS $linkElement) {
            if ($linkElement != null) {
                $linkElementE = explode(':', $linkElement);
                if (isset($linkElementE['1'])) {
                    $linkArr[$linkElementE['0']] = $linkElementE['1'];
                } else {
                    $linkArr[] = $linkElement;
                }
            }
        }
        if (!isset($linkArr['plugin'])) {
            $linkArr['plugin'] = false;
        }

        return $linkArr;
    }
/**
 * Show Vocabulary by Alias
 *
 * @param string $vocabularyAlias Vocabulary alias
 * @param array $options (optional)
 * @return string
 */
    public function vocabulary($vocabularyAlias, $options = array()) {
        $_options = array(
            'tag' => 'ul',
            'tagAttr' => array(),
            'containerTag' => 'div',
            'containerTagAttr' => array(
                'class' => 'vocabulary',
            ),
            'type' => null,
            'link' => true,
        );
        $options = array_merge($_options, $options);

        $output = '';
        if (isset($this->View->viewVars['vocabularies_for_layout'][$vocabularyAlias]['list'])) {
            $vocabulary = $this->View->viewVars['vocabularies_for_layout'][$vocabularyAlias];
            foreach ($vocabulary['list'] AS $termSlug => $termTitle) {
                if ($options['link']) {
                    $li = '<li>' . $this->Html->link($termTitle, array(
                        'plugin' => false,
                        'controller' => 'nodes',
                        'action' => 'term',
                        'type' => $options['type'],
                        'slug' => $termSlug,
                    )) . '</li>';
                } else {
                    $li = '<li>' . $termTitle . '</li>';
                }
                $output .= $li;
            }
            if ($output != '') {
                $options['containerTagAttr']['id'] = 'vocabulary-' . $vocabulary['Vocabulary']['id'];
                $output = $this->Html->tag($options['tag'], $output, $options['tagAttr']);
                $output = $this->Html->tag($options['containerTag'], $output, $options['containerTagAttr']);
            }
        }

        return $output;
    }
/**
 * Show nodes list
 *
 * @param string $alias Node query alias
 * @param array $options (optional)
 * @return string
 */
    public function nodeList($alias, $options = array()) {
        $_options = array(
            'tag' => 'ul',
            'tagAttr' => array(),
            'containerTag' => 'div',
            'containerTagAttr' => array(
                'class' => 'node-list',
            ),
            'link' => true,
        );
        $options = array_merge($_options, $options);

        $output = '';
        if (isset($this->View->viewVars['nodes_for_layout'][$alias])) {
            $nodes = $this->View->viewVars['nodes_for_layout'][$alias];
            foreach ($nodes AS $node) {
                if ($options['link']) {
                    $li = '<li>' . $this->Html->link($node['Node']['title'], array(
                        'plugin' => false,
                        'controller' => 'nodes',
                        'action' => 'view',
                        'type' => $node['Node']['type'],
                        'slug' => $node['Node']['slug'],
                    )) . '</li>';
                } else {
                    $li = '<li>' . $node['Node']['title'] . '</li>';
                }
                $output .= $li;
            }
            if ($output != '') {
                $options['containerTagAttr']['id'] = 'node-list-'.$alias;
                $output = $this->Html->tag($options['tag'], $output, $options['tagAttr']);
                $output = $this->Html->tag($options['containerTag'], $output, $options['containerTagAttr']);
            }
        }

        return $output;
    }
/**
 * Filter content
 *
 * Replaces bbcode-like element tags
 *
 * @param string $content content
 * @return string
 */
    public function filter($content) {
        $content = $this->filterElements($content);
        $content = $this->filterMenus($content);
        $content = $this->filterVocabularies($content);
        $content = $this->filterNodes($content);
        return $content;
    }
/**
 * Filter content for elements
 *
 * Original post by Stefan Zollinger: http://bakery.cakephp.org/articles/view/element-helper
 * [element:element_name] or [e:element_name]
 *
 * @param string $content
 * @return string
 */
    public function filterElements($content) {
        preg_match_all('/\[(element|e):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for ($i=0; $i < count($tagMatches[1]); $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $element = $tagMatches[2][$i];
            $options = array();
            for ($j=0; $j < count($attributes[0]); $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->View->element($element,$options), $content);
        }
        return $content;
    }
/**
 * Filter content for Menus
 *
 * Replaces [menu:menu_alias] or [m:menu_alias] with Menu list
 *
 * @param string $content
 * @return string
 */
    public function filterMenus($content) {
        preg_match_all('/\[(menu|m):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for ($i=0; $i < count($tagMatches[1]); $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $menuAlias = $tagMatches[2][$i];
            $options = array();
            for ($j=0; $j < count($attributes[0]); $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->menu($menuAlias,$options), $content);
        }
        return $content;
    }
/**
 * Filter content for Vocabularies
 *
 * Replaces [vocabulary:vocabulary_alias] or [v:vocabulary_alias] with Terms list
 *
 * @param string $content
 * @return string
 */
    public function filterVocabularies($content) {
        preg_match_all('/\[(vocabulary|v):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for ($i=0; $i < count($tagMatches[1]); $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $vocabularyAlias = $tagMatches[2][$i];
            $options = array();
            for ($j=0; $j < count($attributes[0]); $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->vocabulary($vocabularyAlias,$options), $content);
        }
        return $content;
    }
/**
 * Filter content for Nodes
 *
 * Replaces [node:unique_name_for_query] or [n:unique_name_for_query] with Nodes list
 *
 * @param string $content
 * @return string
 */
    public function filterNodes($content) {
        preg_match_all('/\[(node|n):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
        for ($i=0; $i < count($tagMatches[1]); $i++) {
            $regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
            preg_match_all($regex, $tagMatches[3][$i], $attributes);
            $alias = $tagMatches[2][$i];
            $options = array();
            for ($j=0; $j < count($attributes[0]); $j++) {
                $options[$attributes[1][$j]] = $attributes[2][$j];
            }
            $content = str_replace($tagMatches[0][$i], $this->nodeList($alias,$options), $content);
        }
        return $content;
    }
/**
 * Show links under Actions column
 *
 * @param integer $id
 * @param array $options
 * @return string
 */
    public function adminRowActions($id, $options = array()) {
        $_options = array();
        $options = array_merge($_options, $options);

        $output = '';
        if (Configure::read('Admin.rowActions')) {
            foreach (Configure::read('Admin.rowActions') AS $action => $link) {
                if ($output != '') {
                    $output .= ' ';
                }
                $link = $this->linkStringToArray(str_replace(':id', $id, $link));
                $output .= $this->Html->link($action, $link);
            }
        }
        return $output;
    }
/**
 * Set current Node
 *
 * @param array $node
 * @return void
 */
    public function setNode($node) {
        $this->node = $node;
        $this->hook('afterSetNode');
    }
/**
 * Set value of a field
 *
 * @param string $field
 * @param string $value
 * @return void
 */
    public function setNodeField($field, $value) {
        $model = 'Node';
        if (strstr($field, '.')) {
            $fieldE = explode('.', $field);
            $model = $fieldE['0'];
            $field = $fieldE['1'];
        }

        $this->node[$model][$field] = $value;
    }
/**
 * Get value of a Node field
 *
 * @param string $field
 * @return string
 */
    public function node($field = 'id') {
        $model = 'Node';
        if (strstr($field, '.')) {
            $fieldE = explode('.', $field);
            $model = $fieldE['0'];
            $field = $fieldE['1'];
        }

        if (isset($this->node[$model][$field])) {
            return $this->node[$model][$field];
        } else {
            return false;
        }
    }
/**
 * Node info
 *
 * @param array $options
 * @return string
 */
    public function nodeInfo($options = array()) {
        $_options = array(
            'element' => 'node_info',
        );
        $options = array_merge($_options, $options);

        $output  = $this->hook('beforeNodeInfo');
        $output .= $this->View->element($options['element']);
        $output .= $this->hook('afterNodeInfo');
        return $output;
    }
/**
 * Node excerpt (summary)
 *
 * @param array $options
 * @return string
 */
    public function nodeExcerpt($options = array()) {
        $_options = array(
            'element' => 'node_excerpt',
        );
        $options = array_merge($_options, $options);

        $output  = $this->hook('beforeNodeExcerpt');
        $output .= $this->View->element($options['element']);
        $output .= $this->hook('afterNodeExcerpt');
        return $output;
    }
/**
 * Node body
 *
 * @param array $options
 * @return string
 */
    public function nodeBody($options = array()) {
        $_options = array(
            'element' => 'node_body',
        );
        $options = array_merge($_options, $options);

        $output  = $this->hook('beforeNodeBody');
        $output .= $this->View->element($options['element']);
        $output .= $this->hook('afterNodeBody');
        return $output;
    }
/**
 * Node more info
 *
 * @param array $options
 * @return string
 */
    public function nodeMoreInfo($options = array()) {
        $_options = array(
            'element' => 'node_more_info',
        );
        $options = array_merge($_options, $options);

        $output  = $this->hook('beforeNodeMoreInfo');
        $output .= $this->View->element($options['element']);
        $output .= $this->hook('afterNodeMoreInfo');
        return $output;
    }
/**
 * Hook
 *
 * Used for calling hook methods from other HookHelpers
 *
 * @param string $methodName
 * @return string
 */
    public function hook($methodName) {
        $output = '';

        foreach ($this->hooks AS $hook) {
            if (strstr($hook, '.')) {
                $hookE = explode('.', $hook);
                $hook = $hookE['1'];
            }

            if (method_exists($this->{$hook}, $methodName)) {
                $output .= $this->{$hook}->$methodName();
            }
        }

        return $output;
    }

}
?>