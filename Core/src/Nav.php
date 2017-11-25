<?php

/**
 * Nav
 *
 * @package  Croogo.Croogo.Lib
 * @since    1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
namespace Croogo\Core;

use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use UnexpectedValueException;

class Nav
{

/**
 * Current active menu
 *
 * @see CroogoNav::activeMenu()
 */
    protected static $_activeMenu = 'sidebar';

/**
 * _items
 *
 * @var array
 */
    protected static $_items = ['sidebar' => []];

/**
 * _defaults
 *
 * @var array
 */
    protected static $_defaults = [
        'icon' => '',
        'title' => false,
        'url' => '#',
        'weight' => 9999,
        'before' => false,
        'after' => false,
        'access' => [],
        'children' => [],
        'htmlAttributes' => [],
    ];

/**
 * Getter/setter for activeMenu
 */
    public static function activeMenu($menu = null)
    {
        if ($menu === null) {
            $activeMenu = self::$_activeMenu;
        } else {
            $activeMenu = $menu;
        }

        if (!array_key_exists($activeMenu, self::$_items)) {
            self::$_items[$activeMenu] = [];
        }

        self::$_activeMenu = $activeMenu;

        return $activeMenu;
    }

    public static function menus()
    {
        return array_keys(self::$_items);
    }

/**
 * _setupOptions
 *
 * @param array $options
 * @return void
 */
    protected static function _setupOptions(&$options)
    {
        $options = self::_merge(self::$_defaults, $options);
        foreach ($options['children'] as &$child) {
            self::_setupOptions($child);
        }
    }

/**
 * Add a menu item
 *
 * @param string $menu dot separated path in the array.
 * @param array $path menu options array
 * @return void
 */
    public static function add($menu, $path, $options = [])
    {
        // Juggle argument for backward compatibility
        if (is_array($path)) {
            $options = $path;
            $path = $menu;
            $menu = self::activeMenu();
        } else {
            self::activeMenu($menu);
        }

        $pathE = explode('.', $path);
        $pathE = array_splice($pathE, 0, count($pathE) - 2);
        $parent = join('.', $pathE);
        if (!empty($parent) && !Hash::check(self::$_items[$menu], $parent)) {
            $title = Inflector::humanize(end($pathE));
            $o = ['title' => $title];
            self::_setupOptions($o);
            self::add($parent, $o);
        }
        self::_setupOptions($options);
        $current = Hash::extract(self::$_items[$menu], $path);
        if (!empty($current)) {
            self::_replace(self::$_items[$menu], $path, $options);
        } else {
            self::$_items[$menu] = Hash::insert(self::$_items[$menu], $path, $options);
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
    protected static function _replace(&$target, $path, $options)
    {
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
    protected static function _merge($firstArray, $secondArray)
    {
        if ($firstArray == $secondArray) {
            return $secondArray;
        }
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
    public static function remove($path)
    {
        self::$_items = Hash::remove(self::$_items, $path);
    }

/**
 * Clear all menus
 *
 * @return void
 */
    public static function clear($menu = 'sidebar')
    {
        if ($menu) {
            if (array_key_exists($menu, self::$_items)) {
                self::$_items[$menu] = [];
            } else {
                throw new UnexpectedValueException('Invalid menu: ' . $menu);
            }
        } else {
            self::$_items = [];
        }
    }

/**
 * Sets or returns menu data in array
 *
 * @param $items array if empty, the current menu is returned.
 * @return array
 * @throws UnexpectedValueException
 */
    public static function items($menu = 'sidebar', $items = null)
    {
        if (!is_string($menu)) {
            throw new UnexpectedValueException('Menu id is not a string');
        }
        if (!empty($items)) {
            self::$_items[$menu] = $items;
        }
        if (!array_key_exists($menu, self::$_items)) {
            Log::error('Invalid menu: ' . $menu);
            return [];
        }
        return self::$_items[$menu];
    }

    /**
     * Check menu existence by path
     * @param string $menu Menu name
     * @param string $path Path for Hash::extract()
     * @return boolean
     */
    public static function check($menu, $path) {
        if (!isset(static::$_items[$menu])) {
            return false;
        }
        return Hash::check(self::$_items[$menu], $path);
    }

/**
 * Gets default settings for menu items
 * @return array
 */
    public static function getDefaults()
    {
        return self::$_defaults;
    }
}
