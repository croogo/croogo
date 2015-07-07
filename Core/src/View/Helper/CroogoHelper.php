<?php

namespace Croogo\Core\View\Helper;

use Cake\Controller\Component\AuthComponent;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;
use Croogo\Core\Croogo;
use Croogo\Core\Status;
/**
 * Croogo Helper
 *
 * @category Helper
 * @package  Croogo.Croogo.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoHelper extends Helper {

	public $helpers = array(
		'Form',
		'Html',
		'Url',
		'Croogo/Core.Layout',
		'Croogo/Core.Theme',
		'Croogo/Core.CroogoForm',
		'Croogo/Core.CroogoHtml',
		'Croogo/Menus.Menus',
	);

/**
 * Provides backward compatibility for deprecated methods
 */
	public function __call($method, $params) {
		if ($method == 'settingsInput') {
			if (!$this->_View->Helpers->loaded('SettingsForm')) {
				$this->_View->Helpers->load('Settings.SettingsForm');
			}
			$callable = array($this->_View->SettingsForm, 'input');
			return call_user_func_array($callable, $params);
		}
	}

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		$this->helpers[] = Configure::read('Site.acl_plugin') . '.' . Configure::read('Site.acl_plugin');
		parent::__construct($View, $settings);
		$this->_CroogoStatus = new Status();
	}

/**
 * Before Render callback
 */
	public function beforeRender($viewFile) {
		if ($this->request->param('prefix') === 'admin') {
			Croogo::dispatchEvent('Croogo.setupAdminData', $this->_View);
		}
	}

	public function statuses() {
		return $this->_CroogoStatus->statuses();
	}

/**
 * Convenience method to Html::script() for admin views
 *
 * This method does nothing if request is ajax or not in admin prefix.
 *
 * @see HtmlHelper::script()
 * @param $url string|array Javascript files to include
 * @param array|boolean Options or Html attributes
 * @return mixed String of <script /> tags or null
 */
	public function adminScript($url, $options = array()) {
		$options = Hash::merge(array('block' => 'scriptBottom'), $options);
		if ($this->request->is('ajax') || $this->request->param('prefix') !== 'admin') {
			return;
		}
		return $this->Html->script($url, $options);
	}

/** Generate Admin menus added by Nav::add()
 *
 * @param array $menus
 * @param array $options
 * @return string menu html tags
 */
	public function adminMenus($menus, $options = array(), $depth = 0) {
		$options = Hash::merge(array(
			'type' => 'sidebar',
			'children' => true,
			'htmlAttributes' => array(
				'class' => 'nav nav-stacked',
			),
		), $options);

//		$aclPlugin = Configure::read('Site.acl_plugin');
//		$userId = AuthComponent::user('id');
//		if (empty($userId)) {
//			return '';
//		}

		$sidebar = $options['type'] === 'sidebar';
		$htmlAttributes = $options['htmlAttributes'];
		$out = null;
		$sorted = Hash::sort($menus, '{s}.weight', 'ASC');
//		if (empty($this->Role)) {
//			$this->Role = ClassRegistry::init('Users.Role');
//			$this->Role->Behaviors->attach('Croogo.Aliasable');
//		}
//		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		foreach ($sorted as $menu) {
			if (isset($menu['separator'])) {
				$liOptions['class'] = 'divider';
				$out .= $this->Html->tag('li', null, $liOptions);
				continue;
			}
//			if ($currentRole != 'admin' && !$this->{$aclPlugin}->linkIsAllowedByUserId($userId, $menu['url'])) {
//				continue;
//			}

			if (empty($menu['htmlAttributes']['class'])) {
				$menuClass = Inflector::slug(strtolower('menu-' . $menu['title']), '-');
				$menu['htmlAttributes'] = Hash::merge(array(
					'class' => $menuClass
				), $menu['htmlAttributes']);
			}
			$title = '';
			if ($menu['icon'] === false) {
			} elseif (empty($menu['icon'])) {
				$menu['htmlAttributes'] += array('icon' => 'white');
			} else {
				$menu['htmlAttributes'] += array('icon' => $menu['icon']);
			}
			if ($sidebar) {
				$title .= '<span>' . $menu['title'] . '</span>';
			} else {
				$title .= $menu['title'];
			}
			$children = '';
			if (!empty($menu['children'])) {
				$childClass = '';
				if ($sidebar) {
					$childClass = 'nav nav-stacked sub-nav ';
					$childClass .= ' submenu-' . Inflector::slug(strtolower($menu['title']), '-');
					if ($depth > 0) {
						$childClass .= ' dropdown-menu';
					}
				} else {
					if ($depth == 0) {
						$childClass = 'dropdown-menu';
					}
				}
				$children = $this->adminMenus($menu['children'], array(
					'type' => $options['type'],
					'children' => true,
					'htmlAttributes' => array('class' => $childClass),
				), $depth + 1);

				$menu['htmlAttributes']['class'] .= ' hasChild dropdown-close';
			}
			$menu['htmlAttributes']['class'] .= ' sidebar-item';

			$menuUrl = $this->Url->build($menu['url']);
			if ($menuUrl == env('REQUEST_URI')) {
				if (isset($menu['htmlAttributes']['class'])) {
					$menu['htmlAttributes']['class'] .= ' current';
				} else {
					$menu['htmlAttributes']['class'] = 'current';
				}
			}

			if (!$sidebar && !empty($children)) {
				if ($depth == 0) {
					$title .= ' <b class="caret"></b>';
				}
				$menu['htmlAttributes']['class'] = 'dropdown-toggle';
				$menu['htmlAttributes']['data-toggle'] = 'dropdown';
			}

			if (isset($menu['before'])) {
				$title = $menu['before'] . $title;
			}

			if (isset($menu['after'])) {
				$title = $title . $menu['after'];
			}

			$menu['htmlAttributes']['escape'] = false;

			$link = $this->CroogoHtml->link($title, $menu['url'], $menu['htmlAttributes']);
			$liOptions = array();
			if ($sidebar && !empty($children) && $depth > 0) {
				$liOptions['class'] = ' dropdown-submenu';
			}
			if (!$sidebar && !empty($children)) {
				if ($depth > 0) {
					$liOptions['class'] = ' dropdown-submenu';
				} else {
					$liOptions['class'] = ' dropdown';
				}
			}
			$out .= $this->Html->tag('li', $link . $children, $liOptions);
		}

		if (!$sidebar && $depth > 0) {
			$htmlAttributes['class'] = 'dropdown-menu';
		}

		return $this->Html->tag('ul', $out, $htmlAttributes);
	}

/**
 * Show links under Actions column
 *
 * @param integer $id
 * @param array $options
 * @return string
 */
	public function adminRowActions($id, $options = array()) {
		$output = '';
		$rowActions = Configure::read('Admin.rowActions.' . Inflector::camelize($this->request->param('controller')) . '/' . $this->request->param('action'));
		if (is_array($rowActions)) {
			foreach ($rowActions as $title => $link) {
				$linkOptions = $options;
				$confirmMessage = false;
				if (is_array($link)) {
					$config = $link[key($link)];
					if (isset($config['options'])) {
						$linkOptions = Hash::merge($options, $config['options']);
					}
					if (isset($config['confirmMessage'])) {
						$confirmMessage = $config['confirmMessage'];
						unset($config['confirmMessage']);
					}
					if (isset($config['title'])) {
						$title = $config['title'];
					}
					$link = key($link);
				}
				$link = $this->Menus->linkStringToArray(str_replace(':id', $id, $link));
				$output .= $this->adminRowAction($title, $link, $linkOptions, $confirmMessage);
			}
		}
		return $output;
	}

/**
 * Show link under Actions column
 *
 * ### Options:
 *
 * - `method` - when 'POST' is specified, the FormHelper::postLink() will be
 *              used instead of HtmlHelper::link()
 * - `rowAction` when bulk submissions is used, defines which action to use.
 *
 * @param string $title The content to be wrapped by <a> tags.
 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
 * @param array $options Array of HTML attributes.
 * @param string $confirmMessage JavaScript confirmation message.
 * @return string An `<a />` element
 */
	public function adminRowAction($title, $url = null, $options = array(), $confirmMessage = false) {
		$action = false;
		$options = Hash::merge(array(
			'escapeTitle' => false,
			'escape' => true,
		), $options);

		if (!$confirmMessage) {
			$options['confirm'] = $confirmMessage;
		}

		if (is_array($url)) {
			$action = $url['action'];
			if (isset($options['class'])) {
				$options['class'] .= ' ' . $url['action'];
			} else {
				$options['class'] = $url['action'];
			}
		}
		if (isset($options['icon']) && empty($title)) {
			$options['iconInline'] = false;
		}

		if (!empty($options['rowAction'])) {
			$options['data-row-action'] = $options['rowAction'];
			unset($options['rowAction']);
			return $this->_bulkRowAction($title, $url, $options);
		}

		if (!empty($options['method']) && strcasecmp($options['method'], 'post') == 0) {
			$usePost = true;
			unset($options['method']);
		}

		if ($action == 'delete' || isset($usePost)) {
			$this->CroogoForm->_helperMap['Html']['class'] = 'Croogo/Core.CroogoHtml';
			$this->CroogoForm->Html = new CroogoHtmlHelper($this->_View);
			$postLink = $this->CroogoForm->postLink($title, $url, $options);
			$this->CroogoForm->_helperMap['Html']['class'] = 'Html';
			$this->CroogoForm->Html = new HtmlHelper($this->_View);
			return $postLink;
		}

		return $this->CroogoHtml->link($title, $url, $options);
	}

/**
 * Creates a special type of link for use in admin area.
 *
 * Clicking the link will automatically check a corresponding checkbox
 * where element id is equal to $url parameter and immediately submit the form
 * it's on.  This works in tandem with Admin.processLink() in javascript.
 */
	protected function _bulkRowAction($title, $url = null, $options = array(), $confirmMessage = false) {
		if (!empty($confirmMessage)) {
			$options['data-confirm-message'] = $confirmMessage;
		}
		if (isset($options['icon'])) {
			$options['iconInline'] = false;
		}
		$output = $this->CroogoHtml->link($title, $url, $options);
		return $output;
	}

/**
 * Create an action button
 *
 * @param string $title Button title
 * @param url|string $url URL
 * @param array $options Options array
 * @param string $confirmMessage Confirmation message
 * @return string
 */
	public function adminAction($title, $url, $options = array(), $confirmMessage = false) {
		$options = Hash::merge(array(
			'button' => 'default',
			'method' => 'get',
			'list' => false,
		), $options);
		if ($options['list'] === true) {
			$list = true;
			unset($options['list']);
		}
		if (strcasecmp($options['method'], 'post') == 0) {
			$options['block'] = 'scriptBottom';
			$out = $this->CroogoForm->postLink($title, $url, $options, $confirmMessage);
		} else {
			$out = $this->CroogoHtml->link($title, $url, $options, $confirmMessage);
		}
		if (isset($list)) {
			$out = $this->CroogoHtml->tag('li', $out);
		} else {
			$out = $this->CroogoHtml->div('btn-group', $out);
		}
		return $out;
	}

/**
 * Create a tab title/link
 */
	public function adminTab($title, $url, $options = array()) {
		return $this->CroogoHtml->tag('li',
			$this->CroogoHtml->link($title, $url, Hash::merge(array(
				'data-toggle' => 'tab',
				), $options)
			)
		);
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
		$actions = '';
		if ($this->request->param('prefix')) {
			$actions .= Inflector::camelize($this->request->param('prefix')) . '/';
		}
		$actions .= Inflector::camelize($this->request->param('controller')) . '/' . $this->request->param('action');
		$tabs = Configure::read('Admin.tabs.' . $actions);
		if (is_array($tabs)) {
			foreach ($tabs as $title => $tab) {
				$tab = Hash::merge(array(
					'options' => array(
						'linkOptions' => array(),
						'elementData' => array(),
						'elementOptions' => array(),
					),
				), $tab);

				if (!isset($tab['options']['type']) || (isset($tab['options']['type']) && (in_array($this->_View->viewVars['typeAlias'], $tab['options']['type'])))) {
					$domId = strtolower(Inflector::singularize($this->request->params['controller'])) . '-' . strtolower(Inflector::slug($title, '-'));
					if ($this->adminTabs) {
						$output .= '<div id="' . $domId . '" class="tab-pane">';
						$output .= $this->_View->element($tab['element'], $tab['options']['elementData'], $tab['options']['elementOptions']);
						$output .= '</div>';
					} else {
						$output .= $this->adminTab(__d('croogo', $title), '#' . $domId, $tab['options']['linkOptions']);
					}
				}
			}
		}

		$this->adminTabs = true;
		return $output;
	}

/**
 * Show Boxes
 *
 * @param array $boxNames
 */
	public function adminBoxes($boxName = null) {
		if (!isset($this->boxAlreadyPrinted)) {
			$this->boxAlreadyPrinted = array();
		}

		$output = '';
		$allBoxes = Configure::read('Admin.boxes.' . Inflector::camelize($this->request->param('controller')) . '/' . $this->request->param('action'));
		$allBoxes = empty($allBoxes) ? array() : $allBoxes;
		$boxNames = array();

		if (is_null($boxName)) {
			foreach ($allBoxes as $boxName => $value) {
				if (!in_array($boxName, $this->boxAlreadyPrinted)) {
					$boxNames[$boxName] = $allBoxes[$boxName];
				}
			}
		} elseif (!in_array($boxName, $this->boxAlreadyPrinted)) {
			if (array_key_exists($boxName, $allBoxes)) {
				$boxNames[$boxName] = $allBoxes[$boxName];
			}
		}

		foreach ($boxNames as $title => $box) {
			$box = Hash::merge(array(
				'options' => array(
					'linkOptions' => array(),
					'elementData' => array(),
					'elementOptions' => array(),
				),
			), $box);
			$issetType = isset($box['options']['type']);
			$typeInTypeAlias = $issetType && in_array($this->_View->viewVars['typeAlias'], $box['options']['type']);
			if (!$issetType || $typeInTypeAlias) {
				list($plugin, $element) = pluginSplit($box['element']);
				$elementOptions = Hash::merge(array(
					'plugin' => $plugin,
				), $box['options']['elementOptions']);
				$output .= $this->Html->beginBox($title);
				$output .= $this->_View->element(
					$element,
					$box['options']['elementData'],
					$elementOptions
				);
				$output .= $this->Html->endBox();
				$this->boxAlreadyPrinted[] = $title;
			}
		}

		return $output;
	}

	public function linkChooser($target)
	{
		$linkChooser = $this->_View->element('Croogo/Core.admin/modal', [
			'id' => 'link_choosers',
			'title' => __d('croogo', 'Choose Link'),
		]);
		if (!strstr($this->_View->fetch('page-footer'), $linkChooser)) {
			$this->_View->append('page-footer', $linkChooser);
		}

		return $this->CroogoHtml->link('', '#link_choosers', [
			'button' => 'default',
			'icon' => $this->Theme->getIcon('link'),
			'iconSize' => 'small',
			'data-title' => 'Link Chooser',
			'data-toggle' => 'modal',
			'data-remote' => $this->Url->build([
				'plugin' => 'Croogo/Core',
				'controller' => 'LinkChooser',
				'action' => 'linkChooser',
				'?' => [
					'target' => $target
				]
			]),
		]);
	}

}
