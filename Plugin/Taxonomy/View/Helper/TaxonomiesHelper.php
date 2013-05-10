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

	public $helpers = array(
		'Html',
	);

/**
 * constructor
 */
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view);
		$this->_setupEvents();
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
			$this->_adminTabs();
		}
	}

/**
 * Inject admin menu items
 */
	protected function _adminMenu() {
		if (empty($this->_View->viewVars['types_for_admin_layout'])) {
			$types = array();
		} else {
			$types = $this->_View->viewVars['types_for_admin_layout'];
		}
		foreach ($types as $t) {
			CroogoNav::add('content.children.create.children.' . $t['Type']['alias'], array(
				'title' => $t['Type']['title'],
				'url' => array(
					'plugin' => 'nodes',
					'admin' => true,
					'controller' => 'nodes',
					'action' => 'add',
					$t['Type']['alias'],
				),
			));
		};

		if (empty($this->_View->viewVars['vocabularies_for_admin_layout'])) {
			$vocabularies = array();
		} else {
			$vocabularies = $this->_View->viewVars['vocabularies_for_admin_layout'];
		}
		foreach ($vocabularies as $v) {
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
		};
	}

/**
 * Hook admin tabs when $taxonomy is set
 */
	protected function _adminTabs() {
		$controller = Inflector::camelize($this->request->params['controller']);
		if (empty($this->_View->viewVars['taxonomy']) || $controller == 'Terms') {
			return;
		}
		$title = __d('croogo', 'Terms');
		$element = 'Taxonomy.admin/terms_tab';
		Croogo::hookAdminTab("$controller/admin_add", $title, $element);
		Croogo::hookAdminTab("$controller/admin_edit", $title, $element);
	}

/**
 * Filter content for Vocabularies
 *
 * Replaces [vocabulary:vocabulary_alias] or [v:vocabulary_alias] with Terms list
 *
 * @param string $content
 * @return string
 */
	public function filter(&$content) {
		preg_match_all('/\[(vocabulary|v):([A-Za-z0-9_\-]*)(.*?)\]/i', $content, $tagMatches);
		for ($i = 0, $ii = count($tagMatches[1]); $i < $ii; $i++) {
			$regex = '/(\S+)=[\'"]?((?:.(?![\'"]?\s+(?:\S+)=|[>\'"]))+.)[\'"]?/i';
			preg_match_all($regex, $tagMatches[3][$i], $attributes);
			$vocabularyAlias = $tagMatches[2][$i];
			$options = array();
			for ($j = 0, $jj = count($attributes[0]); $j < $jj; $j++) {
				$options[$attributes[1][$j]] = $attributes[2][$j];
			}
			$content = str_replace($tagMatches[0][$i], $this->vocabulary($vocabularyAlias, $options), $content);
		}
		return $content;
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
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'term',
			'element' => 'Taxonomy.vocabulary',
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

}
