<?php
/**
 * CroogoNav
 *
 * @package  Croogo
 * @since  1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	 http://www.croogo.org
 */
class CroogoNav extends Object {

	protected static $items = array();

	protected static $_this = null;

	protected static $_defaults = array(
		'title' => false,
		'url' => array(),
		'weight' => 9999,
		'access' => array(),
		'children' => array(),
		'htmlAttributes' => array(),
		);

	function getInstance() {
		static $instance = null;
		if (!$instance) {
			$instance = new CroogoNav();
		}
		return $instance;
	}

	protected static function _setupOptions(&$options) {
		$instance = CroogoNav::getInstance();
		$options = Set::merge($instance::$_defaults, $options);
		foreach ($options['children'] as &$child) {
			$instance->_setupOptions($child);
		}
	}

	public static function add($path, $options) {
		$instance = CroogoNav::getInstance();
		$instance->_setupOptions($options);
		self::$items = Set::insert(self::$items, $path, $options);
	}

	public static function items() {
		$instance = CroogoNav::getInstance();
		return $instance::$items;
	}

}