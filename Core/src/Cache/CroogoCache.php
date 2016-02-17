<?php

namespace Croogo\Core\Cache;

use Cake\Cache\Cache;
use Cake\Core\Configure;

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
class CroogoCache extends Cache
{

    /**
     * Groups to Config mapping
     */
    protected static $_groups = [];

    /**
     * Configure cache config
     *
     * @param string|array $key The name of the configuration, or an array of multiple configs.
     * @param array $config An array of name => configuration data for adapter.
     * @return array|null Null when adding configuration or an array of configuration data when reading.
     * @throws CacheException
     */
    public static function config($name = null, $settings = [])
    {
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
     * @param string|null $group group name or null to retrieve all group mappings
     * @return array map of group and all configuration that has the same group
     * @throws CacheException
     */
    public static function groupConfigs($group = null)
    {
        if (version_compare(Configure::version(), '2.4', '>=')) {
            return parent::groupConfigs($group);
        }

        if ($group == null) {
            return self::$_groups;
        }
        if (isset(self::$_groups[$group])) {
            return [$group => self::$_groups[$group]];
        } else {
            throw new CacheException(sprintf('Invalid cache group %s', $group));
        }
    }
}
