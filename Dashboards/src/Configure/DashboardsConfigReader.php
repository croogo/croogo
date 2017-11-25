<?php

namespace Croogo\Dashboards\Configure;

use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * DashboardsConfigReader
 *
 * @package  Croogo.Dashboards.Lib.Configure
 * @since    2.2
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsConfigReader extends PhpConfig implements ConfigEngineInterface
{

    protected $_settingKey = 'Dashboards';

/**
 * Reads a plugin dashboard setting and store them under $_settingKey
 *
 * @param string $key Configuration key name
 * @return array
 */
    public function read($key)
    {
        $config = parent::read($key);
        $defaults = [
            'title' => false,
            'weight' => 9999,
            'cell' => false,
            'arguments' => [],
            'cache' => true,
            'access' => [],
            'column' => false,
            'collapsed' => false,
        ];
        $settings = [];
        foreach ($config as $alias => $setting) {
            $alias = Inflector::slug($alias, '-');
            $setting = Hash::merge($defaults, $setting);
            $settings[$alias] = $setting;
        }
        $result = [$this->_settingKey => $settings];
        return $result;
    }

    /**
     * Dumps the configure data into source.
     *
     * @param string $key The identifier to write to.
     * @param array $data The data to dump.
     * @return bool True on success or false on failure.
     */
    public function dump($key, array $data)
    {
        // TODO: Implement dump() method.
    }
}
