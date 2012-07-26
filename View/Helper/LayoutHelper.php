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
			$url = '/promoted.rss';
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
			$this->Role = ClassRegistry::init('Users.Role');
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
