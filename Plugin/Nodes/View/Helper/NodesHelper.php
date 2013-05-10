<?php
/**
 * Nodes Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo.Nodes
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesHelper extends AppHelper {

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
		'Croogo.Layout',
	);

/**
 * Current Node
 *
 * @var array
 * @access public
 */
	public $node = null;

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
 * Show nodes list
 *
 * @param string $alias Node query alias
 * @param array $options (optional)
 * @return string
 */
	public function nodeList($alias, $options = array()) {
		$_options = array(
			'link' => true,
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'element' => 'Nodes.node_list',
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
 * Filter content for Nodes
 *
 * Replaces [node:unique_name_for_query] or [n:unique_name_for_query] with Nodes list
 *
 * @param string $content
 * @return string
 */
	public function filter(&$content) {
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
 * Set current Node
 *
 * @param array $node
 * @return void
 */
	public function set($node) {
		$this->node = $node;
		$this->Layout->hook('afterSetNode');
	}

/**
 * Get value of a Node field
 *
 * @param string $field
 * @return string
 */
	public function field($field = 'id', $value = null) {
		$model = 'Node';
		if (strstr($field, '.')) {
			list($model, $field) = explode('.', $field);
		}

		if ($field && $value) {
			$this->node[$model][$field] = $value;
		} elseif (isset($this->node[$model][$field])) {
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
	public function info($options = array()) {
		$_options = array(
			'element' => 'Nodes.node_info',
		);
		$options = array_merge($_options, $options);

		$output = $this->Layout->hook('beforeNodeInfo');
		$output .= $this->_View->element($options['element']);
		$output .= $this->Layout->hook('afterNodeInfo');
		return $output;
	}

/**
 * Node excerpt (summary)
 *
 * @param array $options
 * @return string
 */
	public function excerpt($options = array()) {
		$_options = array(
			'element' => 'Nodes.node_excerpt',
		);
		$options = array_merge($_options, $options);

		$output = $this->Layout->hook('beforeNodeExcerpt');
		$output .= $this->_View->element($options['element']);
		$output .= $this->Layout->hook('afterNodeExcerpt');
		return $output;
	}

/**
 * Node body
 *
 * @param array $options
 * @return string
 */
	public function body($options = array()) {
		$_options = array(
			'element' => 'Nodes.node_body',
		);
		$options = array_merge($_options, $options);

		$output = $this->Layout->hook('beforeNodeBody');
		$output .= $this->_View->element($options['element']);
		$output .= $this->Layout->hook('afterNodeBody');
		return $output;
	}

/**
 * Node more info
 *
 * @param array $options
 * @return string
 */
	public function moreInfo($options = array()) {
		$_options = array(
			'element' => 'Nodes.node_more_info',
		);
		$options = array_merge($_options, $options);

		$output = $this->Layout->hook('beforeNodeMoreInfo');
		$output .= $this->_View->element($options['element']);
		$output .= $this->Layout->hook('afterNodeMoreInfo');
		return $output;
	}

}
