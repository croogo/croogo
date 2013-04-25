<?php

/**
 * CroogoNav
 *
 * @package  Croogo.Croogo.Lib
 * @since    1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoNav extends Object {

/**
 * _items
 *
 * @var array
 */
	protected static $_items = array();

/**
 * _defaults
 *
 * @var array
 */
	protected static $_defaults = array(
		'icon' => false,
		'title' => false,
		'url' => '#',
		'weight' => 9999,
		'before' => false,
		'after' => false,
		'access' => array(),
		'children' => array(),
		'htmlAttributes' => array(),
	);

/**
 * _setupOptions
 *
 * @param array $options
 * @return void
 */
	protected static function _setupOptions(&$options) {
		$options = self::_merge(self::$_defaults, $options);
		foreach ($options['children'] as &$child) {
			self::_setupOptions($child);
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
		if (!empty($parent) && !Hash::check(self::$_items, $parent)) {
			$title = Inflector::humanize(end($pathE));
			$o = array('title' => $title);
			self::_setupOptions($o);
			self::add($parent, $o);
		}
		self::_setupOptions($options);
		$current = Hash::extract(self::$_items, $path);
		if (!empty($current)) {
			self::_replace(self::$_items, $path, $options);
		} else {
			self::$_items = Hash::insert(self::$_items, $path, $options);
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
		$fragment = join('.', $pathE);
		if (!empty($pathE)) {
			self::_replace($target[$path], $fragment, $options);
		} else {
			$target[$path] = self::_merge($target[$path], $options);
		}
	}

/**
 * Merge $firstArray with $secondArray
 *
 * Similar to Hash::merge, except duplicates are removed
 * @param array $firstArray
 * @param array $secondArray
 * @return array
 */
	protected static function _merge($firstArray, $secondArray) {
		$merged = Hash::merge($firstArray, $secondArray);
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
		self::$_items = Hash::remove(self::$_items, $path);
	}

/**
 * Clear all menus
 *
 * @return void
 */
	public static function clear() {
		self::$_items = array();
	}

/**
 * Sets or returns menu data in array
 *
 * @param $items array if empty, the current menu is returned.
 * @return array
 */
	public static function items($items = null) {
		if (!empty($items)) {
			self::$_items = $items;
		}
		return self::$_items;
	}

/**
 * Gets default settings for menu items
 * @return array
 */
	public static function getDefaults() {
		return self::$_defaults;
	}

}