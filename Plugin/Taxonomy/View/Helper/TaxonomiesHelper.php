<?php

/**
 * Taxonomies Helper
 *
 * PHP version 5
 *
 * @category Taxonomy.View/Helper
 * @package  Croogo.Taxonomy
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TaxonomiesHelper extends AppHelper {

/**
 * beforeRender
 */
	public function beforeRender($viewFile) {
		if (isset($this->request->params['admin'])) {
			$this->_adminMenu();
			$this->_adminTabs();
		}
	}

/**
 * Inject admin menu items
 */
	protected function _adminMenu() {
		$types = $this->_View->viewVars['types_for_admin_layout'];
		foreach ($types as $t):
			CroogoNav::add('content.children.create.children.' . $t['Type']['alias'], array(
				'title' => $t['Type']['title'],
				'url' => array(
					'plugin' => false,
					'admin' => true,
					'controller' => 'nodes',
					'action' => 'add',
					$t['Type']['alias'],
				),
			));
		endforeach;

		$vocabularies = $this->_View->viewVars['vocabularies_for_admin_layout'];
		foreach ($vocabularies as $v):
			$weight = 9999 + $v['Vocabulary']['weight'];
			CroogoNav::add('content.children.taxonomy.children.' . $v['Vocabulary']['alias'], array(
				'title' => $v['Vocabulary']['title'],
				'url' => array(
					'plugin' => 'taxonomy',
					'admin' => true,
					'controller' => 'terms',
					'action' => 'index',
					$v['Vocabulary']['id'],
				),
				'weight' => $weight,
			));
		endforeach;
	}

/**
 * Hook admin tabs when $taxonomy is set
 */
	protected function _adminTabs() {
		if (empty($this->_View->viewVars['taxonomy'])) {
			return;
		}
		$controller = Inflector::camelize($this->request->params['controller']);
		$title = __('Terms');
		$element = 'Taxonomy.admin/terms_tab';
		Croogo::hookAdminTab("$controller/admin_add", $title, $element);
		Croogo::hookAdminTab("$controller/admin_edit", $title, $element);
	}

}
