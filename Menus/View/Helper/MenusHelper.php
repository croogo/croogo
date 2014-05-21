<?php

namespace Croogo\Menus\View\Helper;

use App\View\Helper\AppHelper;
use Croogo\Lib\Utility\StringConverter;
/**
 * Menus Helper
 *
 * @category Menus.View/Helper
 * @package  Croogo.Menus.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MenusHelper extends AppHelper {

	public $helpers = array(
		'Html',
	);

/**
 * constructor
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view);
		$this->_setupEvents();
		$this->_converter = new StringConverter();
	}

/**
 * setup events
 */
	protected function _setupEvents() {
		$events = array(
			'Helper.Layout.beforeFilter' => array(
				'callable' => 'filter', 'passParams' => true,
			),
		);
		$eventManager = $this->_View->getEventManager();
		foreach ($events as $name => $config) {
			$eventManager->attach(array($this, 'filter'), $name, $config);
		}
	}

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin']) && !$this->request->is('ajax')) {
			$this->_adminMenu();
		}
	}

/**
 * Inject admin menu items
 */
	protected function _adminMenu() {
		if (empty($this->_View->viewVars['menus_for_admin_layout'])) {
			return;
		}
		$menus = $this->_View->viewVars['menus_for_admin_layout'];
		foreach ($menus as $m) {
			$weight = 9999 + $m['Menu']['weight'];
			$htmlAttributes = $this->__isCurrentMenu($m['Menu']['id']) ? array('class' => 'current') : array();
			CroogoNav::add('sidebar', 'menus.children.' . $m['Menu']['alias'], array(
				'title' => $m['Menu']['title'],
				'url' => array(
					'plugin' => 'menus',
					'admin' => true,
					'controller' => 'links',
					'action' => 'index',
					'?' => array('menu_id' => $m['Menu']['id'])
				),
				'weight' => $weight,
				'htmlAttributes' => $htmlAttributes
			));
		};
	}

/**
 * Checks wether $id is the current active menu
 *
 * The value is checked against the menuId variable set in
 * LinksController::admin_add() and LinksController::admin_edit()
 *
 * @param string $id Menu id
 * @return bool True if $id is currently the active menu
 */
	private function __isCurrentMenu($id) {
		$currentMenuId = $this->_View->get('menuId');
		return $currentMenuId === $id;
	}

/**
 * Filter content for Menus
 *
 * Replaces [menu:menu_alias] or [m:menu_alias] with Menu list
 *
 * @param string $content
 * @return string
 */
	public function filter(&$content, $options = array()) {
		preg_match_all('/\[(menu|m):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$menuAlias = $tagMatches[2][$i];
			$options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				$options[$attributes[1][$j]] = $attributes[2][$j];
			}
			$content = str_replace($tagMatches[0][$i], $this->menu($menuAlias, $options), $content);
		}
		return $content;
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
			'element' => 'Menus.menu',
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
 * Merge Link options retrieved from Params behavior
 *
 * @param array $link Link data
 * @param string $param Parameter name
 * @param array $attributes Default options
 * @return string
 */
	protected function _mergeLinkParams($link, $param, $options = array()) {
		if (isset($link['Params'][$param])) {
			$options = array_merge($options, $link['Params'][$param]);
		}

		$booleans = array('true', 'false');
		foreach ($options as $key => $val) {
			if ($val == null) {
				unset($options[$key]);
			}
			if (is_string($val) && in_array(strtolower($val), $booleans)) {
				$options[$key] = (bool)$val;
			}
		}

		return $options;
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

			$linkAttr = $this->_mergeLinkParams($link, 'linkAttr', $linkAttr);

			// if link is in the format: controller:contacts/action:view
			if (strstr($link['Link']['link'], 'controller:')) {
				$link['Link']['link'] = $this->linkStringToArray($link['Link']['link']);
			}

			// Remove locale part before comparing links
			if (!empty($this->_View->request->params['locale'])) {
				$currentUrl = substr($this->_View->request->url, strlen($this->_View->request->params['locale'] . '/'));
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
			$liAttr = $this->_mergeLinkParams($link, 'liAttr');
			$linkOutput = $this->Html->tag('li', $linkOutput, $liAttr);
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
 * @param string|array $link link
 * @return array
 * @see Use StringConverter::linkStringToArray()
 */
	public function linkStringToArray($link) {
		return $this->_converter->linkStringToArray($link);
	}

/**
 * Converts array into string controller:abc/action:xyz/value1/value2
 *
 * @param array $url link
 * @return array
 * @see StringConverter::urlToLinkString()
 */
	public function urlToLinkString($url) {
		return $this->_converter->urlToLinkString($url);
	}

}
