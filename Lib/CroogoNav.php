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

	protected static $_items = array();

	protected static $_defaults = array(
		'title' => false,
		'url' => '#',
		'weight' => 9999,
		'access' => array(),
		'children' => array(),
		'htmlAttributes' => array(),
		);

	protected static function _setupOptions(&$options) {
		$options = static::_merge(static::$_defaults, $options);
		foreach ($options['children'] as &$child) {
			static::_setupOptions($child);
		}
	}

	/**
	 * Add a menu item
	 *
	 * @param string $path dot separated path in the array.
	 * @param array $options menu options array
	 * @return void
	 */
	public static function add($path, $options) {
		$pathE = explode('.', $path);
		$pathE = array_splice($pathE, 0, count($pathE) - 2);
		$parent = join('.', $pathE);
		if (!empty($parent) && !Set::check(static::$_items, $parent)) {
			$title = Inflector::humanize(end($pathE));
			$o = array('title' => $title);
			static::_setupOptions($o);
			static::add($parent, $o);
		}
		static::_setupOptions($options);
		$current = Set::extract($path, static::$_items);
		if (!empty($current)) {
			static::_replace(static::$_items, $path, $options);
		} else {
			static::$_items = Set::insert(static::$_items, $path, $options);
		}
	}

	/**
	 * Replace a menu element
	 *
	 * @param array $target pointer to start of array
	 * @param string $path path to search for in dot separated format
	 * @param array $options data to replace with
	 * @return void
	 */
	protected static function _replace(&$target, $path, $options) {
		$pathE = explode('.', $path);
		$path = array_shift($pathE);
		$fragment = join ('.', $pathE);
		if (!empty($pathE)) {
			static::_replace($target[$path], $fragment, $options);
		} else {
			$target[$path] = static::_merge($target[$path], $options);
		}
	}

    /**
	 * Merge $arr1 and $arr2
	 *
	 * Similar to Set::merge, except duplicates are removed
	 * @param arram $arr1 array
	 * @param arram $arr2 array
	 * @return array
	 */
	protected static function _merge($arr1, $arr2) {
		$merged = Set::merge($arr1, $arr2);
		foreach ($merged as $key => $val) {
			if (is_array($val) && is_int(key($val))) {
				$merged[$key] = array_unique($val);
			}
		}
		return $merged;
	}

	/**
	 * Remove a menu item
	 *
	 * @param string $path dot separated path in the array.
	 * @return void
	 */
	public static function remove($path) {
		static::$_items = Set::remove(static::$_items, $path);
	}

	/**
	 * Clear all menus
	 *
	 * @return void
	 */
	public static function clear() {
		static::$_items = array();
	}

	/**
	 * Sets or returns menu data in array
	 *
	 * @param $items array if empty, the current menu is returned.
	 * @return array
	 */
	public static function items($items = null) {
		if (!empty($items)) {
			static::$_items = $items;
		}
		return static::$_items;
	}

    /**
     * Gets default settings for menu items
     * @return array
     */
	public static function getDefaults() {
	    return static::$_defaults;
	}

}