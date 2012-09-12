<?php

/**
 * Croogo Helper
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
class CroogoHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'Layout',
		'Menus.Menus',
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
		$currentRole = $this->Role->byId($this->Layout->getRoleId());
		$aclPlugin = Configure::read('Site.acl_plugin');
		$userId = AuthComponent::user('id');
		foreach ($sorted as $menu) {
			if ($currentRole != 'admin' && !$this->{$aclPlugin}->linkIsAllowedByUserId($userId, $menu['url'])) {
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
				$link = $this->Menus->linkStringToArray(str_replace(':id', $id, $link));
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
						list($plugin, $element) = pluginSplit($tab['element']);
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

}
