<?php

namespace Croogo\Core\View\Helper;
use Cake\ORM\Entity;
use Cake\View\Helper;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use Cake\Utility\Inflector;

/**
 * Layout Helper
 *
 * @category Helper
 * @package  Croogo.Croogo.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class LayoutHelper extends Helper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Croogo/Core.Croogo',
		'Croogo/Core.Theme',
		'Html',
		'Form',
		'Flash'
	);

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
 * Provides backward compatibility for deprecated methods
 */
//	public function __call($method, $params) {
//		$mapMethods = array(
//			'meta' => array('Meta.Meta', 'meta'),
//			'metaField' => array('Meta.Meta', 'field'),
//			'blocks' => array('Blocks.Regions', 'blocks'),
//			'regionIsEmpty' => array('Blocks.Regions', 'isEmpty'),
//			'linkStringToArray' => array('Menus.Menus', 'linkStringToArray'),
//			'menu' => array('Menus.Menus', 'menu'),
//			'nestedLinks' => array('Menus.Menus', 'nestedLinks'),
//			'nestedTerms' => array('Taxonomy.Taxonomies', 'nestedTerms'),
//			'vocabulary' => array('Taxonomy.Taxonomies', 'vocabulary'),
//			'node' => array('Nodes.Nodes', 'field'),
//			'nodeBody' => array('Nodes.Nodes', 'body'),
//			'nodeExcerpt' => array('Nodes.Nodes', 'excerpt'),
//			'nodeInfo' => array('Nodes.Nodes', 'info'),
//			'nodeList' => array('Nodes.Nodes', 'nodeList'),
//			'nodeMoreInfo' => array('Nodes.Nodes', 'moreInfo'),
//			'setNode' => array('Nodes.Nodes', 'set'),
//			'setNodeField' => array('Nodes.Nodes', 'field'),
//			'adminRowActions' => array('Croogo', 'adminRowActions'),
//			'adminTabs' => array('Croogo', 'adminTabs'),
//			'adminMenus' => array('Croogo', 'adminMenus'),
//		);
//
//		if (!isset($mapMethods[$method])) {
//			trigger_error(__d('croogo', 'Method %1$s::%2$s does not exist', get_class($this), $method), E_USER_WARNING);
//			return;
//		}
//
//		$mapped = $mapMethods[$method];
//		list($helper, $method) = $mapped;
//		list($plugin, $helper) = pluginSplit($helper, true);
//		if (!$this->{$helper}) {
//			if (!$this->_View->Helpers->loaded($helper)) {
//				$this->_View->Helpers->load($helper);
//			}
//			$this->{$helper} = $this->_View->{$helper};
//		}
//		return call_user_func_array(array($this->{$helper}, $method), $params);
//	}

/**
 * Provides backward compatibility for deprecated properties
 */
//	public function __get($name) {
//		switch ($name) {
//			case 'node':
//				return $this->_View->Nodes->node;
//			default:
//				return parent::__get($name);
//		}
//	}
//
///**
// * Provides backward compatibility for deprecated properties
// */
//	public function __set($name, $val) {
//		switch ($name) {
//			case 'node':
//				return $this->_View->Nodes->node = $val;
//			default:
//				return parent::__set($name, $val);
//		}
//	}

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
		$croogo = $this->_mergeThemeSettings();
		if ($this->request->param('locale')) {
			$croogo['basePath'] = Router::url('/' . $this->request->param('locale') . '/');
		} else {
			$croogo['basePath'] = Router::url('/');
		}
		$validKeys = array(
			'plugin' => null,
			'controller' => null,
			'action' => null,
			'prefix' => null,
			'named' => null,
		);
		$croogo['params'] = array_intersect_key(
			array_merge($validKeys, $this->request->params),
			$validKeys
		);
		if (is_array(Configure::read('Js'))) {
			$croogo = Hash::merge($croogo, Configure::read('Js'));
		}
		return $this->Html->scriptBlock('var Croogo = ' . json_encode($croogo) . ';');
	}

/**
 * Merge helper and prefix specific settings
 *
 * @param array $croogoSetting Croogo JS settings
 * @return array Merged settings
 */
	protected function _mergeThemeSettings($croogoSetting = array()) {
		$themeSettings = $this->Theme->settings();
		if (empty($themeSettings)) {
			return $croogoSetting;
		}
		$validKeys = array(
			'css' => null,
			'icons' => null,
			'iconDefaults' => null,
		);
		$croogoSetting['themeSettings'] = array_intersect_key(
			array_merge($validKeys, $themeSettings),
			$validKeys
		);

		if ($this->_View->helpers()->has('CroogoHtml')) {
			unset($validKeys['css']);
			$croogoSetting['themeSettings'] = Hash::merge(
				$croogoSetting['themeSettings'],
				array_intersect_key(
					array_merge($validKeys, $this->_View->CroogoHtml->config()),
					$validKeys
				)
			);
		}
		return $croogoSetting;
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
		$icons = $this->Theme->settings('icons');
		if (empty($icons)) {
			$icons = array('check-mark' => 'ok', 'x-mark' => 'remove');
		}
		if ($value == 1) {
			$icon = $icons['check-mark'];
			$class = 'green';
		} else {
			$icon = $icons['x-mark'];
			$class = 'red';
		}
		if (method_exists($this->CroogoHtml, 'icon')) {
			return $this->CroogoHtml->icon($icon, compact('class'));
		} else {
			if (empty($this->_View->CroogoHtml)) {
				$this->_View->Helpers->load('Croogo/Core.CroogoHtml');
			}
			return $this->_View->CroogoHtml->icon($icon, compact('class'));
		}
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
	public function displayField(Entity $item, $model, $field, $options = array()) {
		extract(array_intersect_key($options, array(
			'type' => null,
			'url' => array(),
			'options' => array(),
		)));
		switch ($type) {
			case 'boolean':
				$out = $this->status($item->{$field});
			break;
			default:
				$out = h((!isset($item->{$model})) ? $item->{$field} : $item->{$model}->{$field});
			break;
		}

		if (!empty($url)) {
			if (isset($url['pass'])) {
				$passVars = is_string($url['pass']) ? array($url['pass']) : $url['pass'];
				foreach ($passVars as $passField) {
					$url[] = $item->get($passField);
				}
				unset($url['pass']);
			}

			if (isset($url['named'])) {
				$namedVars = is_string($url['named']) ? array($url['named']) : $url['named'];
				foreach ($namedVars as $namedField) {
					$url[$namedField] = $item->get($namedField);
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
		return $this->Flash->render();
	}

/**
 * isLoggedIn
 *
 * if User is logged in
 *
 * @return boolean
 */
	public function isLoggedIn() {
		if ($this->request->session()->check('Auth.User.id')) {
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
			$roleId = $this->request->session()->read('Auth.User.role_id');
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
 *
 * @deprecated Will be removed in the future. See CroogoHelper::adminRowAction()
 */
	public function processLink($title, $url = null, $options = array(), $confirmMessage = false) {
		if (!empty($confirmMessage)) {
			$options['data-confirm-message'] = $confirmMessage;
		}
		if (isset($options['icon'])) {
			$options['iconInline'] = true;
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
	public function filter($content, $options = array()) {
		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $this->_View, array(
			'content' => &$content,
			'options' => $options,
		));
		$content = $this->filterElements($content, $options);
		Croogo::dispatchEvent('Helper.Layout.afterFilter', $this->_View, array(
			'content' => &$content,
			'options' => $options,
		));
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
	public function filterElements($content, $options = array()) {
		preg_match_all('/\[(element|e):([A-Za-z0-9_\-\/]*)(.*?)\]/i', $content, $tagMatches);
		$validOptions = array('plugin', 'cache', 'callbacks');
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/([\w-]+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))*.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$element = $tagMatches[2][$i];
			$data = $options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				if (in_array($attributes[1][$j], $validOptions)) {
					$options = Hash::merge($options, array($attributes[1][$j] => $attributes[2][$j]));
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
			list(, $helper) = pluginSplit($helper);
			if (isset($this->_View->{$helper}) && method_exists($this->_View->{$helper}, $methodName)) {
				$output .= $this->_View->{$helper}->$methodName();
			}
		}
		return $output;
	}

/**
 * Gets a value of view variables based on path
 *
 * @param string $name Variable name to retrieve from View::viewVars
 * @param string $path Extraction path following the Hash path syntax
 * @return array
 */
	public function valueOf($name, $path, $options = array()) {
		if (!isset($this->_View->viewVars[$name])) {
			$this->log(sprintf('Invalid viewVars "%s"', $name));
			return array();
		}
		$result = Hash::extract($this->_View->viewVars[$name], $path);
		$result = isset($result[0]) ? $result[0] : $result;
		return $result;
	}

/**
 * Compute default options for snippet()
 *
 * @param string $type Type
 * @return array Array of options
 */
	private function __snippetDefaults($type) {
		$varName = strtolower(Inflector::pluralize($type)) . '_for_layout';
		$modelAlias = Inflector::classify($type);
		$checkField = 'alias';
		$valueField = 'body';
		$filter = true;
		$format = '{s}.{n}.%s[%s=%s].%s';
		switch ($type) {
			case 'type':
				$valueField = 'description';
				$format = '{s}.%s[%s=%s].%s';
			break;
			case 'vocabulary':
				$valueField = 'title';
				$format = '{s}.%s[%s=%s].%s';
			break;
			case 'menu':
				$valueField = 'title';
				$format = '{s}.%s[%s=%s].%s';
			break;
			case 'node':
				$checkField = 'slug';
			break;
		}
		return compact('checkField', 'filter', 'format', 'modelAlias', 'valueField', 'varName');
	}

/**
 * Simple method to retrieve value from view variables using Hash path format
 *
 * Example:
 *
 *   // display the 'about' block
 *   echo $this->Layout->snippet('about');
 *   // display the 'hello world' node
 *   echo $this->Layout->snippet('hello-world', 'node');
 *
 * You can customize the return value by supplying a custom path:
 *   // display the 'main' menu array
 *   echo $this->Layout->snippet('main', 'menu', array(
 *       'format' => '{s}.%s[%s=%s].%s',
 *   ));
 *   // display the 'main' menu description field
 *   echo $this->Layout->snippet('main', 'menu', array(
 *       'valueField' => 'description',
 *       'format' => '{s}.%s[%s=%s].%s',
 *   ));
 *
 * Options:
 * - checkField Field name that will be checked against $name
 * - filter Filter view data. Defaults to true
 * - format Hash path format
 * - modelAlias Model alias
 * - valueField Field name that will be returned if data is found
 * - varName Variable name as it is stored in viewVars
 *
 * @param string $name Identifier
 * @param string $type String of `block`, `nodes`, `node`
 * @param array $options Options array
 * @return string
 */
	public function snippet($name, $type = 'block', $options = array()) {
		$options = array_merge($this->__snippetDefaults($type), $options);
		extract($options);
		$path = sprintf($format, $modelAlias, $checkField, $name, $valueField);
		$result = $this->valueOf($options['varName'], $path);
		if ($result) {
			if ($options['filter'] === true && is_string($result)) {
				return $this->filter($result, $options);
			} else {
				return $result;
			}
		} else {
			return null;
		}
	}

/**
 * Helper method to retrieve css settings as configured in theme.json
 *
 * @param string $class Name of class/configuration to retrieve
 * @return string
 * @deprecated Use ThemeHelper::css()
 */
	public function cssClass($class = null) {
		return $this->Theme->getCssClass($class);
	}

/**
 * Helper method to retrieve theme settings as configured in theme.json
 *
 * @param string $class Name of class/configuration to retrieve
 * @return string
 * @deprecated Use ThemeHelper::settings()
 */
	public function themeSetting($key) {
		return $this->Theme->settings($key);
	}

}
