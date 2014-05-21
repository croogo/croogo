<?php

App::uses('Cache', 'Cache');

/**
 * CroogoCache
 *
 * Extended from Cake/Cache/Cache to add mappings between groups and configs.
 * Only useful with Cake < 2.4
 *
 * @package  Croogo.Croogo.Lib.Cache
 * @since    1.5.2
 * @author   rchavik
 * @license  MIT
 * @link     http://www.croogo.org
 */
class CroogoCache extends Cache {

/**
 * Groups to Config mapping
 */
	protected static $_groups = array();

/**
 * Configure cache config
 *
 * @throws CacheException
 */
	public static function config($name = null, $settings = array()) {
		if (version_compare(Configure::version(), '2.4', '>=')) {
			return parent::config($name, $settings);
		}

		$return = parent::config($name, $settings);
		foreach (self::$_config[$name]['groups'] as $group) {
			self::$_groups[$group][] = $name;
			sort(self::$_groups[$group]);
			self::$_groups[$group] = array_unique(self::$_groups[$group]);
		}
		return $return;
	}

/**
 * Returns an array of group -> config map
 *
 * @return array Array of group to config map
 * @throws CacheException
 */
	public static function groupConfigs($group = null) {
		if (version_compare(Configure::version(), '2.4', '>=')) {
			return parent::groupConfigs($group);
		}

		if ($group == null) {
			return self::$_groups;
		}
		if (isset(self::$_groups[$group])) {
			return array($group => self::$_groups[$group]);
		} else {
			throw new CacheException(sprintf('Invalid cache group %s', $group));
		}
	}

}
