<?php

App::uses('PhpReader', 'Configure');
App::uses('ConfigReaderInterface', 'Configure');

/**
 * DashboardsConfigReader
 *
 * @package  Croogo.Dashboards.Lib.Configure
 * @since    2.2
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class DashboardsConfigReader extends PhpReader implements ConfigReaderInterface
{

	protected $_settingKey = 'Dashboards';

/**
 * Reads a plugin dashboard setting and store them under $_settingKey
 *
 * @param string $key Configuration key name
 * @return array
 */
	public function read($key) {
		$config = parent::read($key);
		$defaults = array(
			'title' => false,
			'weight' => 9999,
			'element' => false,
			'cache' => true,
			'access' => array(),
			'column' => false,
			'collapsed' => false,
		);
		$settings = array();
		foreach ($config as $alias => $setting) {
			$alias = Inflector::slug($alias, '-');
			$setting = Hash::merge($defaults, $setting);
			$settings[$alias] = $setting;
		}
		$result = array($this->_settingKey => $settings);
		return $result;
	}

}
