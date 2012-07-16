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
 * Core helpers
 *
 * Extra supported callbacks, like beforeNodeInfo() and afterNodeInfo(),
 * won't be called for these helpers.
 *
 * @var array
 * @access public
 */
	public $coreHelpers = array(
		// CakePHP
		'Ajax',
		'Cache',
		'Form',
		'Html',
		'Javascript',
		'JqueryEngine',
		'Js',
		'MootoolsEngine',
		'Number',
		'Paginator',
		'PrototypeEngine',
		'Rss',
		'Session',
		'Text',
		'Time',
		'Xml',

		// Croogo
		'Filemanager',
		'Image',
		'Layout',
		'Recaptcha',
	);

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		$this->helpers[] = Configure::read('Site.acl_plugin') . '.' . Configure::read('Site.acl_plugin');
		parent::__construct($View, $settings);
	}

/**
 * Javascript variables
 *
 * Shows croogo.js file along with useful information like the applications's basePath, etc.
 *
 * Also merges Configure::read('Js') with the Croogo js variable.
 * So you can set javascript info anywhere like Configure::write('Js.my_var', 'my value'),
 * and you can access it like 'Croogo.my_var' in your javascript.
 *
 * @return string
 */
	public function js() {
		$croogo = array();
		if (isset($this->params['locale'])) {
			$croogo['basePath'] = Router::url('/' . $this->params['locale'] . '/');
		} else {
			$croogo['basePath'] = Router::url('/');
		}
		$croogo['params'] = array(
			'controller' => $this->params['controller'],
			'action' => $this->params['action'],
			'named' => $this->params['named'],
		);
		if (is_array(Configure::read('Js'))) {
			$croogo = Set::merge($croogo, Configure::read('Js'));
		}
		return $this->Html->scriptBlock('var Croogo = ' . $this->Js->object($croogo) . ';');
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
 * Display value from $item array
 *
 * @param $item array of values
 * @param $model string model alias
 * @param $field string field name
 * @param $options array
 * @return string
 */
	public function displayField($item, $model, $field, $options = array()) {
		extract(array_intersect_key($options, array(
			'type' => null,
			'url' => array(),
			'options' => array(),
			)
		));
		switch ($type) {
			case 'boolean':
				$out = $this->status($item[$model][$field]);
			break;
			default:
				$out = h($item[$model][$field]);
			break;
		}

		if (!empty($url)) {
			if (isset($url['pass'])) {
				$passVars = is_string($url['pass']) ?  array($url['pass']) : $url['pass'];
				foreach ($passVars as $passField) {
					$url[] = $item[$model][$passField];
				}
				unset($url['pass']);
			}

			if (isset($url['named'])) {
				$namedVars = is_string($url['named']) ?  array($url['named']) : $url['named'];
				foreach ($namedVars as $namedField) {
					$url[$namedField] = $item[$model][$namedField];
				}
				unset($url['named']);
			}

			$out = $this->Html->link($out, $url, $options);
		}
		return $out;
	}

/**
 * Show flash message
 *
 * @return string
 */
	public function sessionFlash() {
		$messages = $this->Session->read('Message');
		$output = '';
		if (is_array($messages)) {
			foreach (array_keys($messages) as $key) {
				$output .= $this->Session->flash($key);
			}
		}
		return $output;
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
			isset($this->_View->viewVars['node']['CustomFields']) &&
			count($this->_View->viewVars['node']['CustomFields']) > 0) {
			$metaForLayout = array();
			foreach ($this->_View->viewVars['node']['CustomFields'] as $key => $value) {
				if (strstr($key, 'meta_')) {
					$key = str_replace('meta_', '', $key);
					$metaForLayout[$key] = $value;
				}
			}
		}

		$metaForLayout = array_merge($_metaForLayout, $metaForLayout);

		$output = '';
		foreach ($metaForLayout as $name => $content) {
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
		if (isset($this->_View->viewVars['blocks_for_layout'][$regionAlias]) &&
			count($this->_View->viewVars['blocks_for_layout'][$regionAlias]) > 0) {
			return false;
		} else {
			return true;
		}
	}

/**
 * Show Blocks for a particular Region
 *
 * @param string $regionAlias Region alias
 * @param array $options
 * @return string
 */
	public function blocks($regionAlias, $options = array()) {
		$_options = array();
		$options = array_merge($_options, $options);

		$output = '';
		if (!$this->regionIsEmpty($regionAlias)) {
			$blocks = $this->_View->viewVars['blocks_for_layout'][$regionAlias];
			foreach ($blocks as $block) {
				$plugin = false;
				if ($block['Block']['element'] != null) {
					if (strstr($block['Block']['element'], '.')) {
						$pluginElement = explode('.', $block['Block']['element']);
						$plugin  = $pluginElement[0];
						$element = $pluginElement[1];
					} else {
						$element = $block['Block']['element'];
					}
				} else {
					$element = 'block';
				}
				if ($plugin) {
					$blockOutput = $this->_View->element($element, array('block' => $block), array('plugin' => $plugin));
				} else {
					$blockOutput = $this->_View->element($element, array('block' => $block));
				}
				$enclosure = isset($block['Params']['enclosure']) ? $block['Params']['enclosure'] === "true" : true;
				if ($element != 'block' && $enclosure) {
					$block['Block']['body'] = $blockOutput;
					$block['Block']['element'] = null;
					$output .= $this->_View->element('block', array('block' => $block));
				} else {
					$output .= $blockOutput;
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
			'tag' => 'ul',
			'tagAttributes' => array(),
			'selected' => 'selected',
			'dropdown' => false,
			'dropdownClass' => 'sf-menu',
			'element' => 'menu',
		);
		$options = array_merge($_options, $options);

		if (!isset($this->_View->viewVars['menus_for_layout'][$menuAlias])) {
			return false;
		}
		$menu = $this->_View->viewVars['menus_for_layout'][$menuAlias];
		$output = $this->_View->element($options['element'], array(
			'menu' => $menu,
			'options' => $options,
		));
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
		foreach ($links as $link) {
			$linkAttr = array(
				'id' => 'link-' . $link['Link']['id'],
				'rel' => $link['Link']['rel'],
				'target' => $link['Link']['target'],
				'title' => $link['Link']['description'],
				'class' => $link['Link']['class'],
			);

			foreach ($linkAttr as $attrKey => $attrValue) {
				if ($attrValue == null) {
					unset($linkAttr[$attrKey]);
				}
			}

			// if link is in the format: controller:contacts/action:view
			if (strstr($link['Link']['link'], 'controller:')) {
				$link['Link']['link'] = $this->linkStringToArray($link['Link']['link']);
			}

			// Remove locale part before comparing links
			if (!empty($this->params['locale'])) {
				$currentUrl = substr($this->_View->request->url, strlen($this->params['locale']));
			} else {
				$currentUrl = $this->_View->request->url;
			}

			if (Router::url($link['Link']['link']) == Router::url('/' . $currentUrl)) {
				if (!isset($linkAttr['class'])) {
					$linkAttr['class'] = '';
				}
				$linkAttr['class'] .= ' ' . $options['selected'];
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
		foreach ($link as $linkElement) {
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
 * Creates a special type of link for use in admin area.
 *
 * Clicking the link will automatically check a corresponding checkbox
 * where element id is equal to $url parameter and immediately submit the form
 * it's on.  This works in tandem with Admin.processLink() in javascript.
 */
	public function processLink($title, $url = null, $options = array(), $confirmMessage = false) {
		if (!empty($confirmMessage)) {
			$options['onclick'] = "if (confirm('$confirmMessage')) { Admin.processLink(this); } return false;";
		} else {
			$options['onclick'] = "Admin.processLink(this); return false;";
		}
		$output = $this->Html->link($title, $url, $options);
		return $output;
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
			'tagAttributes' => array(),
			'type' => null,
			'link' => true,
			'plugin' => false,
			'controller' => 'nodes',
			'action' => 'term',
			'element' => 'vocabulary',
		);
		$options = array_merge($_options, $options);

		$output = '';
		if (isset($this->_View->viewVars['vocabularies_for_layout'][$vocabularyAlias]['threaded'])) {
			$vocabulary = $this->_View->viewVars['vocabularies_for_layout'][$vocabularyAlias];
			$output .= $this->_View->element($options['element'], array(
				'vocabulary' => $vocabulary,
				'options' => $options,
			));
		}
		return $output;
	}

/**
 * Nested Terms
 *
 * @param array   $terms
 * @param array   $options
 * @param integer $depth
 */
	public function nestedTerms($terms, $options, $depth = 1) {
		$_options = array();
		$options = array_merge($_options, $options);

		$output = '';
		foreach ($terms as $term) {
			if ($options['link']) {
				$termAttr = array(
					'id' => 'term-' . $term['Term']['id'],
				);
				$termOutput = $this->Html->link($term['Term']['title'], array(
					'plugin' => $options['plugin'],
					'controller' => $options['controller'],
					'action' => $options['action'],
					'type' => $options['type'],
					'slug' => $term['Term']['slug'],
				), $termAttr);
			} else {
				$termOutput = $term['Term']['title'];
			}
			if (isset($term['children']) && count($term['children']) > 0) {
				$termOutput .= $this->nestedTerms($term['children'], $options, $depth + 1);
			}
			$termOutput = $this->Html->tag('li', $termOutput);
			$output .= $termOutput;
		}
		if ($output != null) {
			$output = $this->Html->tag($options['tag'], $output, $options['tagAttributes']);
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
			'link' => true,
			'plugin' => false,
			'controller' => 'nodes',
			'action' => 'view',
			'element' => 'node_list',
		);
		$options = array_merge($_options, $options);

		$output = '';
		if (isset($this->_View->viewVars['nodes_for_layout'][$alias])) {
			$nodes = $this->_View->viewVars['nodes_for_layout'][$alias];
			$output = $this->_View->element($options['element'], array(
				'alias' => $alias,
				'nodesList' => $this->_View->viewVars['nodes_for_layout'][$alias],
				'options' => $options,
			));
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
		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->_View, array('content' => &$content));
		$content = $this->filterElements($content);
		$content = $this->filterMenus($content);
		$content = $this->filterVocabularies($content);
		$content = $this->filterNodes($content);
		Croogo::dispatchEvent('Helper.Layout.afterFilter', $this->_View, array('content' => &$content));
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
		preg_match_all('/\[(element|e):([A-Za-z0-9_\-\/]*)(.*?)\]/i', $content, $tagMatches);
		$validOptions = array('plugin', 'cache', 'callbacks');
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))*.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$element = $tagMatches[2][$i];
			$data = $options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				if (in_array($attributes[1][$j], $validOptions)) {
					$options = Set::merge($options, array($attributes[1][$j] => $attributes[2][$j]));
				} else {
					$data[$attributes[1][$j]] = $attributes[2][$j];
				}
			}
			if (!empty($this->_View->viewVars['block'])) {
				$data['block'] = $this->_View->viewVars['block'];
			}
			$content = str_replace($tagMatches[0][$i], $this->_View->element($element, $data, $options), $content);
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
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$menuAlias = $tagMatches[2][$i];
			$options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
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
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$vocabularyAlias = $tagMatches[2][$i];
			$options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
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
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$alias = $tagMatches[2][$i];
			$options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				$options[$attributes[1][$j]] = $attributes[2][$j];
			}
			$content = str_replace($tagMatches[0][$i], $this->nodeList($alias,$options), $content);
		}
		return $content;
	}

/**
 * Meta field: with key/value fields
 *
 * @param string $key (optional) key
 * @param string $value (optional) value
 * @param integer $id (optional) ID of Meta
 * @param array $options (optional) options
 * @return string
 */
	public function metaField($key = '', $value = null, $id = null, $options = array()) {
		$_options = array(
			'key'   => array(
				'label'   => __('Key'),
				'value'   => $key,
			),
			'value' => array(
				'label'   => __('Value'),
				'value'   => $value,
			),
		);
		$options = Set::merge($_options, $options);
		$uuid = String::uuid();

		$fields  = '';
		if ($id != null) {
			$fields .= $this->Form->input('Meta.' . $uuid . '.id', array('type' => 'hidden', 'value' => $id));
			$this->Form->unlockField('Meta.' . $uuid . '.id');
		}
		$fields .= $this->Form->input('Meta.' . $uuid . '.key', $options['key']);
		$fields .= $this->Form->input('Meta.' . $uuid . '.value', $options['value']);
		$this->Form->unlockField('Meta.' . $uuid . '.key');
		$this->Form->unlockField('Meta.' . $uuid . '.value');
		$fields = $this->Html->tag('div', $fields, array('class' => 'fields'));

		$actions = $this->Html->link(
			__('Remove'),
			is_null($id) ? '#' : array('plugin' => null, 'controller' => 'nodes', 'action' => 'delete_meta', $id),
			array('class' => 'remove-meta', 'rel' => $id)
		);
		$actions = $this->Html->tag('div', $actions, array('class' => 'actions'));

		$output = $this->Html->tag('div', $actions . $fields, array('class' => 'meta'));
		return $output;
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
		$rowActions = Configure::read('Admin.rowActions.' . Inflector::camelize($this->params['controller']) . '/' . $this->params['action']);
		if (is_array($rowActions)) {
			foreach ($rowActions as $title => $link) {
				if ($output != '') {
					$output .= ' ';
				}
				$link = $this->linkStringToArray(str_replace(':id', $id, $link));
				$output .= $this->Html->link($title, $link);
			}
		}
		return $output;
	}

/**
 * Show tabs
 *
 * @return string
 */
	public function adminTabs($show = null) {
		if (!isset($this->adminTabs)) {
			$this->adminTabs = false;
		}

		$output = '';
		$tabs = Configure::read('Admin.tabs.' . Inflector::camelize($this->params['controller']) . '/' . $this->params['action']);
		if (is_array($tabs)) {
			foreach ($tabs as $title => $tab) {
				if (!isset($tab['options']['type']) || (isset($tab['options']['type']) && (in_array($this->_View->viewVars['typeAlias'], $tab['options']['type'])))) {
					$domId = strtolower(Inflector::singularize($this->params['controller'])) . '-' . strtolower(Inflector::slug($title, '-'));
					if ($this->adminTabs) {
						if (strstr($tab['element'], '.')) {
							$elementE = explode('.', $tab['element']);
							$plugin = $elementE['0'];
							$element = $elementE['1'];
						} else {
							$plugin = null;
						}
						$output .= '<div id="' . $domId . '">';
						$output .= $this->_View->element($element, array(), array(
							'plugin' => $plugin,
						));
						$output .= '</div>';
					} else {
						$output .= '<li><a href="#' . $domId . '">' . $title . '</a></li>';
					}
				}
			}
		}

		$this->adminTabs = true;
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
		$output .= $this->_View->element($options['element']);
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
		$output .= $this->_View->element($options['element']);
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
		$output .= $this->_View->element($options['element']);
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
		$output .= $this->_View->element($options['element']);
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
		foreach ($this->_View->helpers as $helper => $settings) {
			if (!is_string($helper) || in_array($helper, $this->coreHelpers)) {
				continue;
			}
			if (strstr($helper, '.')) {
				$helperE = explode('.', $helper);
				$helper = $helperE['1'];
			}
			if (isset($this->_View->{$helper}) && method_exists($this->_View->{$helper}, $methodName)) {
				$output .= $this->_View->{$helper}->$methodName();
			}
		}
		return $output;
	}

/** Generate Admin menus added by CroogoNav::add()
 *
 * @param array $menus
 * @param array $options
 * @return string menu html tags
 */
	public function adminMenus($menus, $options = array()) {
		$options = Set::merge(array(
			'children' => true,
			'htmlAttributes' => array(
				'class' => 'sf-menu',
			),
		), $options);

		$out = null;
		$sorted = Set::sort($menus, '{[a-z]+}.weight', 'ASC');
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Role');
			$this->Role->Behaviors->attach('Aliasable');
		}
		$currentRole = $this->Role->byId($this->getRoleId());
		$aclPlugin = Configure::read('Site.acl_plugin');
		foreach ($sorted as $menu) {
			if ($currentRole != 'admin' && !$this->{$aclPlugin}->linkIsAllowedByRoleId($this->getRoleId(), $menu['url'])) {
				continue;
			}

			if (empty($menu['htmlAttributes']['class'])) {
				$menuClass = Inflector::slug(strtolower('menu-' . $menu['title']), '-');
				$menu['htmlAttributes'] = Set::merge(array(
					'class' => $menuClass
					), $menu['htmlAttributes']);
			}
			$link = $this->Html->link($menu['title'], $menu['url'], $menu['htmlAttributes']);
			if (!empty($menu['children'])) {
				$children = $this->adminMenus($menu['children'], array(
					'children' => true,
					'htmlAttributes' => array('class' => false)
					));
				$out  .= $this->Html->tag('li', $link . $children);
			} else {
				$out  .= $this->Html->tag('li', $link);
			}
		}
		return $this->Html->tag('ul', $out, $options['htmlAttributes']);
	}

}
