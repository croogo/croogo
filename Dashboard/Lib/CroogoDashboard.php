<?php

/**
 * CroogoNav
 *
 * @package  Croogo.Dashboard.Lib
 * @since    2.2
 * @author   Walther Lalk <emailme@waltherlalk.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoDashboard {

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
		'icon' => '',
		'title' => false,
		'weight' => 9999,
		'element' => false,
		'cache' => true,
		'access' => array(),
		'full_width' => false,
		'collapsed' => false
	);

/**
 * _setupOptions
 *
 * @param array $options
 *
 * @return void
 */
	protected static function _setupOptions(&$options) {
		$options = self::_merge(self::$_defaults, $options);
	}

/**
 * Merge $firstArray with $secondArray
 *
 * Similar to Hash::merge, except duplicates are removed
 *
 * @param array $firstArray
 * @param array $secondArray
 *
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
 * Add a dashboard panel
 *
 * @param string $alias Alias for the dashboard element
 * @param array $options dashboard options array
 * @return void
 */
	public static function add($alias, $options = array()) {
		self::_setupOptions($options);
		self::$_items[$alias] = $options;
	}

/**
 * Remove a menu item
 *
 * @param string $alias dashboard element alias
 *
 * @return void
 */
	public static function remove($alias) {
		if (isset(self::$_items[$alias])) {
			unset(self::$_items[$alias]);
		}
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
 * Returns dashboard panels in array
 *
 * @param $alias string If null, all items returned
 *
 * @return array
 * @throws UnexpectedValueException
 */
	public static function items($alias = null) {
		if ($alias == null) {
			return self::$_items;
		}
		if (!array_key_exists($alias, self::$_items)) {
			CakeLog::error('Invalid dashboard panel: ' . $alias);
			return array();
		}
		return self::$_items[$alias];
	}

/**
 * Gets default settings for dashboard panels
 * @return array
 */
	public static function getDefaults() {
		return self::$_defaults;
	}

}