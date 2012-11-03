<?php
App::uses('ExtensionsInstaller', 'Extensions.Lib');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('CroogoTheme', 'Extensions.Lib');

/**
 * Install Shell
 *
 * Usage:
 *	./Console/croogo install plugin https://github.com/shama/myplugin/zipball/master
 *	./Console/croogo install plugin shama myplugin
 *
 * @category Shell
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class InstallShell extends AppShell {

/**
 * Tmp path to download extensions to
 *
 * @var string
 */
	public $tmpPath = TMP;

/**
 * ExtensionsInstaller
 *
 * @var ExtensionsInstaller
 */
	protected $_ExtensionsInstaller = null;

/**
 * CroogoPlugin class
 *
 * @var CroogoPlugin
 */
	protected $_CroogoPlugin = null;

/**
 * CroogoTheme class
 *
 * @var CroogoTheme
 */
	protected $_CroogoTheme = null;

/**
 * Init ExtensionsInstaller, CroogoPlugin, CroogoTheme
 *
 * @param type $stdout
 * @param type $stderr
 * @param type $stdin
 */
	public function __construct($stdout = null, $stderr = null, $stdin = null) {
		parent::__construct($stdout, $stderr, $stdin);
		$this->_ExtensionsInstaller = new ExtensionsInstaller();
		$this->_CroogoPlugin = new CroogoPlugin();
		$this->_CroogoTheme = new CroogoTheme();
	}

/**
 * 1. Detects URL or github user/repo or composer package
 * 2. Downloads
 * 3. Installs extension
 * 4. Activates extension
 *
 * Composer: ./Console/cake install plugin vendor/package
 * Github: ./Console/cake install plugin user repo
 * Url: ./Console/cake install plugin https://github.com/user/repo
 */
	public function main() {
		$type = $this->args[0];
		if (strpos($this->args[1], '/') !== false && preg_match('/http[s]*:\/\//i', $this->args[1]) === 0) {
			// Composer Install
			$ver = isset($this->args[2]) ? $this->args[2] : '*';
			$this->out(__('Installing with Composer...'));
			try {
				$result = $this->_ExtensionsInstaller->composerInstall(array(
					'package' => $this->args[1],
					'version' => $ver,
					'type' => $type,
				));
				if (!is_array($result)) {
					$this->err(__('Unexpected composerInstall return value'));
					return false;
				}
				if ($result['returnValue'] <> 0) {
					$this->err($result['output']);
					return false;
				}
				$ext = substr($this->args[1], strpos($this->args[1], '/') + 1);
				$ext = Inflector::camelize($ext);
				$shouldActivate = $this->{'_Croogo' . ucfirst($type)}->getData($ext);
				if ($shouldActivate !== false) {
					$result = $this->dispatchShell('ext', 'activate', $type, $ext, '--quiet');
					if ($result) {
						$this->out(__('Package installed and activated.'));
					} else {
						$this->err(__('Package installed but not activated.'));
					}
				}
			} catch (CakeException $e) {
				$this->err($e->getMessage());
			}
		} else {
			// Github / URL Install
			$url = '';
			if (count($this->args) == 2) {
				$url = $this->args[1];
			} elseif (count($this->args) == 3) {
				$url = 'http://github.com/' . $this->args[1] . '/' . $this->args[2];
			}
			if ($zip = $this->_download($url)) {
				if ($this->_install($type, $zip)) {
					if ($this->_activate($type, $zip)) {
						$this->out(__('Extension installed and activated.'));
					}
				}
			}
		}
	}

/**
 * Display help/options
 */
	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__('Download, Install & Activate Plugins & Themes'))
			->addArguments(array(
				'type' => array(
					'help' => __('Extension type'),
					'required' => true,
					'choices' => array('plugin', 'theme'),
				),
				'zip_url' => array(
					'help' => __('URL to zip file OR github user name'),
					'required' => true,
				),
				'github_package' => array(
					'help' => __('Github repo name'),
				),
			));
	}

/**
 * Activates an extension by calling ExtShell
 *
 * @param string $type Type of extension
 * @param string $zip Path to zip file
 * @return boolean
 */
	protected function _activate($type = null, $zip = null) {
		try {
			$ext = $this->_ExtensionsInstaller->{'get' . ucfirst($type) . 'Name'}($zip);
			$this->dispatchShell('ext', 'activate', $type, $ext, '--quiet');
			return true;
		} catch (CakeException $e) {
			$this->err($e->getMessage());
		}
		return false;
	}

/**
 * Extracts an extension
 *
 * @param string $type Type of extension
 * @param string $zip Path to zip file
 * @return boolean
 */
	protected function _install($type = null, $zip = null) {
		$this->out(__('Installing extension...'));
		try {
			$this->_ExtensionsInstaller->{'extract' . ucfirst($type)}($zip);
			return true;
		} catch (CakeException $e) {
			$this->err($e->getMessage());
		}
		return false;
	}

/**
 * Download an extension via CURL
 *
 * @param string $url URL of extension
 * @return string Path to zip file
 * @throws ConsoleException
 */
	protected function _download($url = null) {
		if (empty($url)) {
			throw new ConsoleException(__('Please specify a URL to a zipball extension'));
		}
		$url = $this->_githubUrl($url);
		$filename = uniqid('croogo_') . '.zip';
		$zip = $this->tmpPath . $filename;
		$this->out(__('Downloading extension to %s...', $zip));
		$res = $this->_shellExec('curl -L ' . $url . ' -o ' . $zip . ' 2>&1');
		return $res ? $zip : false;
	}

/**
 * If Github url return url to zip
 *
 * @param string $url
 * @return string
 */
	protected function _githubUrl($url = null) {
		if (strpos($url, 'github.com') === false) {
			return $url;
		}
		if (substr($url, -1) === '/') {
			$url = substr($url, 0, -1);
		}
		if (substr($url, -4) === '.git') {
			$url = substr($url, 0, -4);
		}
		$url = str_replace('git://', 'https://', $url);
		return $url . '/zipball/master';
	}

/**
 * Wrapper for shell_exec() method for testing
 */
	protected function _shellExec($cmd) {
		return shell_exec($cmd);
	}
}