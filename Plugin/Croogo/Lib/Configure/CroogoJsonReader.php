<?php

App::uses('CroogoJson', 'Croogo.Lib');

/**
 * CroogoJsonReader
 *
 * @package  Croogo.Croogo.Lib.Configure
 * @since    1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoJsonReader implements ConfigReaderInterface {

/**
 * Default path to store file
 */
	protected $_path = null;

/**
 * __construct
 *
 */
	public function __construct($path = null) {
		if (!$path) {
			$path = APP . 'Config' . DS;
		}
		$this->_path = $path;
	}

/**
 * Read an json file and return the results as an array.
 *
 * @params $key string name key to read
 * @throws ConfigureException
 */
	public function read($key) {
		if (strpos($key, '..') !== false) {
			throw new ConfigureException(__d('croogo', 'Cannot load configuration files with ../ in them.'));
		}
		if (substr($key, -5) === '.json') {
			$key = substr($key, 0, -5);
		}
		list($plugin, $key) = pluginSplit($key);

		if ($plugin) {
			$file = App::pluginPath($plugin) . 'Config' . DS . $key;
		} else {
			$file = $this->_path . $key;
		}
		$file .= '.json';
		if (!is_file($file)) {
			if (!is_file(substr($file, 0, -4))) {
				throw new ConfigureException(__d('croogo', 'Could not load configuration files: %s or %s', $file, substr($file, 0, -4)));
			}
		}
		$config = json_decode(file_get_contents($file), true);
		return $config;
	}

/**
 * Dumps the state of Configure data into an json string.
 */
	public function dump($filename, $data) {
		$runtime = array(
			'routes' => '',
			'controller_properties' => '',
			'model_properties' => '',
		);
		if (isset($data['Hook'])) {
			$data['Hook'] = array_diff_key($data['Hook'], $runtime);
		}
		$options = 0;
		if (version_compare(PHP_VERSION, '5.3.3', '>=')) {
			$options |= JSON_NUMERIC_CHECK;
		}
		if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
			$options |= JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
		}
		$contents = CroogoJson::stringify($data, $options);
		return $this->_writeFile($this->_path . $filename, $contents);
	}

/**
 * _writeFile
 *
 */
	protected function _writeFile($file, $contents) {
		return file_put_contents($file, $contents);
	}

}
