<?php

App::uses('File', 'Utility');
App::uses('HttpSocket', 'Network/Http');
App::uses('CroogoJson', 'Croogo.Lib');

/**
 * Croogo Composer Wrapper
 *
 * @category Lib
 * @package  Croogo.Extensions.Lib
 * @since    1.4
 * @author   Kyle Robinson Young <kyle@dontkry.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoComposer {

/**
 * Path to APP
 *
 * @var string
 */
	public $appPath = APP;

	public $composerPath = null;

/**
 * Downloads composer if it doesn't exist
 *
 * @return boolean
 */
	public function getComposer() {
		$appComposer = $this->appPath . 'composer.phar';
		if (file_exists($appComposer)) {
			$this->composerPath = $appComposer;
		} else {

			if (DS != '\\' && exec('which composer', $output, $found)) {
				if ($found == 0) {
					$this->composerPath = $output[0];
					return true;
				}
			}

			$this->_shellExec('curl -s http://getcomposer.org/installer | php -- --install-dir=' . $this->appPath);
			$this->composerPath = $appComposer;
		}
		return true;
	}

/**
 * Runs composer.phar
 *
 * @return boolean
 */
	public function runComposer() {
		$cmd = 'php ' . $this->composerPath . ' ';
		if (file_exists($this->appPath . 'composer.lock')) {
			$cmd .= 'update';
		} else {
			$cmd .= 'install';
		}
		return $this->_shellExec($cmd);
	}

/**
 * setConfig
 *
 * @param array $requires
 * @return boolean
 */
	public function setConfig($requires = array()) {
		$filename = 'composer.json';
		if (file_exists($this->appPath . $filename)) {
			$file = new File($this->appPath . $filename);
			$json = json_decode($file->read(), true);
		} else {
			$file = new File($this->appPath . $filename, true);
			$json = array();
		}
		if (!isset($json['minimum-stability'])) {
			$json['minimum-stability'] = 'dev';
		}
		if (!isset($json['config']['vendor-dir'])) {
			$json['config']['vendor-dir'] = 'Vendor';
		}
		if (!isset($json['config']['bin-dir'])) {
			$json['config']['bin-dir'] = 'Vendor/bin';
		}
		if (!isset($json['require'])) {
			$json['require'] = array('composer/installers' => '*');
		}
		foreach ($requires as $pkg => $ver) {
			if (strpos($ver, '/') !== false) {
				$pkg = $ver;
				$ver = '*';
			}
			$json['require'][$pkg] = $ver;
		}
		$options = 0;
		if (version_compare(PHP_VERSION, '5.3.3', '>=')) {
			$options |= JSON_NUMERIC_CHECK;
		}
		if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
			$options |= JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
		}
		$json = CroogoJson::stringify($json, $options) . "\n";
		$file->write($json);
		$file->close();
		return true;
	}

/**
 * Wrapper for shell_exec() method for testing
 */
	protected function _shellExec($cmd) {
		$output = system($cmd, $returnValue);
		return compact('cmd', 'output', 'returnValue');
	}

}