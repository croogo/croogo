<?php
App::uses('File', 'Utility');
App::uses('HttpSocket', 'Network/Http');

/**
 * Croogo Composer Wrapper
 *
 * @category Lib
 * @package  Extension
 * @since	 1.4
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link	 http://www.croogo.org
 */
class CroogoComposer {

/**
 * Path to APP
 *
 * @var string
 */
	public $appPath = APP;

	protected $_composerPath = null;

/**
 * Downloads composer if it doesn't exist
 *
 * @return boolean
 */
	public function getComposer() {
		$appComposer = $this->appPath . 'composer.phar';
		if (file_exists($appComposer)) {
			$this->_composerPath = $appComposer;
		} else {

			if (DS != '\\' && exec('which composer', $output, $found)) {
				if ($found == 0) {
					$this->_composerPath = $output[0];
					return true;
				}
			}

			$this->_shellExec('curl -s http://getcomposer.org/installer | php -- --install-dir=' . $this->appPath);
			$this->_composerPath = $appComposer;
		}
		return true;
	}

/**
 * Runs composer.phar
 *
 * @return boolean
 */
	public function runComposer() {
		$cmd = 'php ' . $this->_composerPath . ' ';
		if (file_exists($this->appPath . 'composer.lock')) {
			$cmd .= 'update';
		} else {
			$cmd .= 'install';
		}
		$this->_shellExec($cmd);
		return true;
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
		$json = $this->_stringifyJson($json) . "\n";
		$file->write($json);
		$file->close();
		return true;
	}

/**
 * Returns an array in a pretty json format
 *
 * @param array $json
 * @return string
 * @author http://recursive-design.com/blog/2008/03/11/format-json-with-php/
 */
	protected function _stringifyJson($json = array()) {
		$json = json_encode($json);
		$json = str_replace(array('\/', ':{', ':"'), array('/', ': {', ': "'), $json);
		$result			= '';
		$pos			= 0;
		$strLen			= strlen($json);
		$indentStr		= "\t";
		$newLine		= "\n";
		$prevChar		= '';
		$outOfQuotes	= true;
		for ($i = 0; $i <= $strLen; $i++) {
			$char = substr($json, $i, 1);
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
			$result .= $char;
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
			$prevChar = $char;
		}
		return $result;
	}

/**
 * Wrapper for shell_exec() method for testing
 */
	protected function _shellExec($cmd) {
		return shell_exec($cmd);
	}
	
}