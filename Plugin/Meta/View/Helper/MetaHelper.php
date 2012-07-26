<?php

/**
 * Meta Helper
 *
 * PHP version 5
 *
 * @category Meta.View/Helper
 * @package  Croogo.Meta
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaHelper extends AppHelper {

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin'])) {
			$this->_adminTabs();
		}
	}

/**
 * Hook admin tabs
 */
	protected function _adminTabs() {
		$controller = Inflector::camelize($this->request->params['controller']);
		$title = __('Custom Fields');
		$element = 'Meta.admin/meta_tab';
		Croogo::hookAdminTab("$controller/admin_add", $title, $element);
		Croogo::hookAdminTab("$controller/admin_edit", $title, $element);
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
 * Meta field: with key/value fields
 *
 * @param string $key (optional) key
 * @param string $value (optional) value
 * @param integer $id (optional) ID of Meta
 * @param array $options (optional) options
 * @return string
 */
	public function field($key = '', $value = null, $id = null, $options = array()) {
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
			is_null($id) ? '#' : array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'delete_meta', $id),
			array('class' => 'remove-meta', 'rel' => $id)
		);
		$actions = $this->Html->tag('div', $actions, array('class' => 'actions'));

		$output = $this->Html->tag('div', $actions . $fields, array('class' => 'meta'));
		return $output;
	}

}
