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
		'Form' => array('className' => 'CroogoForm'),
		'Html' => array('className' => 'CroogoHtml'),
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
	public function adminMenus($menus, $options = array(), $depth = 0) {
		$options = Set::merge(array(
			'children' => true,
			'htmlAttributes' => array(
				'class' => 'nav nav-stacked',
			),
		), $options);

		$aclPlugin = Configure::read('Site.acl_plugin');
		$userId = AuthComponent::user('id');
		if (empty($userId)) {
			return '';
		}

		$out = null;
		$sorted = Set::sort($menus, '{[a-z]+}.weight', 'ASC');
		if (empty($this->Role)) {
			$this->Role = ClassRegistry::init('Users.Role');
			$this->Role->Behaviors->attach('Aliasable');
		}
		$currentRole = $this->Role->byId($this->Layout->getRoleId());

		foreach ($sorted as $menu) {
			$htmlAttributes = $options['htmlAttributes'];
			if ($currentRole != 'admin' && !$this->{$aclPlugin}->linkIsAllowedByUserId($userId, $menu['url'])) {
				continue;
			}

			if (empty($menu['htmlAttributes']['class'])) {
				$menuClass = Inflector::slug(strtolower('menu-' . $menu['title']), '-');
				$menu['htmlAttributes'] = Set::merge(array(
					'class' => $menuClass
				), $menu['htmlAttributes']);
			}
			$title = '';
			if (empty($menu['icon'])) {
				$menu['htmlAttributes'] += array('icon' => 'white');
			} else {
				$menu['htmlAttributes'] += array('icon' => $menu['icon']);
			}
			$title .= '<span>' . $menu['title'] . '</span>';
			$children = '';
			if (!empty($menu['children'])) {
				$childClass = 'nav nav-stacked sub-nav ';
				$childClass .= ' submenu-' . Inflector::slug(strtolower($menu['title']), '-');
				if ($depth > 0) {
					$childClass .= ' dropdown-menu';
				}
				$children = $this->adminMenus($menu['children'], array(
					'children' => true,
					'htmlAttributes' => array('class' => $childClass),
				), $depth + 1);
				$menu['htmlAttributes']['class'] .= ' hasChild dropdown-close';
			}
			$menu['htmlAttributes']['class'] .= ' sidebar-item';

			$menuUrl = $this->url($menu['url']);
			if ($menuUrl == env('REQUEST_URI')) {
				if (isset($menu['htmlAttributes']['class'])) {
					$menu['htmlAttributes']['class'] .= ' current';
				} else {
					$menu['htmlAttributes']['class'] = 'current';
				}
			}
			$link = $this->Html->link($title, $menu['url'], $menu['htmlAttributes']);
			$liOptions = array();
			if (!empty($children) && $depth > 0) {
				$liOptions['class'] = ' dropdown-submenu';
			}
			$out  .= $this->Html->tag('li', $link . $children, $liOptions);
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
		$rowActions = Configure::read('Admin.rowActions.' . Inflector::camelize($this->params['controller']) . '/' . $this->params['action']);
		if (is_array($rowActions)) {
			foreach ($rowActions as $title => $link) {
				if ($output != '') {
					$output .= ' ';
				}
				$link = $this->Menus->linkStringToArray(str_replace(':id', $id, $link));
				$output .= $this->Html->link($title, $link, $options);
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
		if (is_array($url)) {
			$action = $url['action'];
			if (isset($options['class'])) {
				$options['class'] .= ' ' . $url['action'];
			} else {
				$options['class'] = $url['action'];
			}
		}
		if (isset($options['icon'])) {
			$options['iconInline'] = true;
		}

		if (!empty($options['rowAction'])) {
			$options['data-row-action'] = $options['rowAction'];
			unset($options['rowAction']);
			return $this->_bulkRowAction($title, $url, $options, $confirmMessage);
		}

		if (!empty($options['method']) && strcasecmp($options['method'], 'post') == 0) {
			$usePost = true;
			unset($options['method']);
		}

		if ($action == 'delete' || isset($usePost)) {
			return $this->Form->postLink($title, $url, $options, $confirmMessage);
		}

		return $this->Html->link($title, $url, $options, $confirmMessage);
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
			$options['iconInline'] = true;
		}
		$output = $this->Html->link($title, $url, $options);
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
						$output .= '<div id="' . $domId . '" class="tab-pane">';
						$output .= $this->_View->element($element, array(), array(
							'plugin' => $plugin,
						));
						$output .= '</div>';
					} else {
						$output .= '<li><a href="#' . $domId . '" data-toggle="tab">' . $title . '</a></li>';
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
		$allBoxes = Configure::read('Admin.boxes.' . Inflector::camelize($this->params['controller']) . '/' . $this->params['action']);
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
			$issetType = isset($box['options']['type']);
			$typeInTypeAlias = $issetType && in_array($this->_View->viewVars['typeAlias'], $box['options']['type']);
			if (!$issetType || $typeInTypeAlias) {
				list($plugin, $element) = pluginSplit($box['element']);
				$output .= $this->Html->beginBox($title);
				$output .= $this->_View->element(
					$element,
					array(),
					array('plugin' => $plugin)
				);
				$output .= $this->Html->endBox();
				$this->boxAlreadyPrinted[] = $title;
			}
		}

		return $output;
	}

	protected function _settingsInputCheckbox($setting, $label, $i) {
		$tooltip = array(
			'data-trigger' => 'hover',
			'data-placement' => 'right',
			'data-title' => $setting['Setting']['description'],
		);
		if ($setting['Setting']['value'] == 1) {
			$output = $this->Form->input("Setting.$i.value", array(
				'type' => $setting['Setting']['input_type'],
				'checked' => 'checked',
				'tooltip' => $tooltip,
				'label' => $label
			));
		} else {
			$output = $this->Form->input("Setting.$i.value", array(
				'type' => $setting['Setting']['input_type'],
				'tooltip' => $tooltip,
				'label' => $label
			));
		}
		return $output;
	}

	public function settingsInput($setting, $label, $i) {
		$output = '';
		$inputType = ($setting['Setting']['input_type'] != null) ? $setting['Setting']['input_type'] : 'text';
		if ($setting['Setting']['input_type'] == 'multiple') {
			$multiple = true;
			if (isset($setting['Params']['multiple'])) {
				$multiple = $setting['Params']['multiple'];
			};
			$selected = json_decode($setting['Setting']['value']);
			$options = json_decode($setting['Params']['options'], true);
			$output = $this->Form->input("Setting.$i.values", array(
				'label' => $setting['Setting']['title'],
				'multiple' => $multiple,
				'options' => $options,
				'selected' => $selected,
			));
		} elseif ($setting['Setting']['input_type'] == 'checkbox') {
			$output = $this->_settingsInputCheckbox($setting, $label, $i);
		} else {
			$output = $this->Form->input("Setting.$i.value", array(
				'type' => $inputType,
				'class' => 'span10',
				'value' => $setting['Setting']['value'],
				'help' => $setting['Setting']['description'],
				'label' => false,
				'placeholder' => $label,
			));
		}
		return $output;
	}

}
