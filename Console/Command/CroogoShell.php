<?php

App::uses('Security', 'Utility');

/**
 * Croogo Shell
 *
 * PHP version 5
 *
 * @category Shell
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoShell extends AppShell {

	public $tasks = array(
		'Upgrade',
	);

/**
 * Display help/options
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(__('Croogo Utilities')
			)->addSubCommand('make', array(
				'help' => __('Compile/Generate CSS'),
				)
			)->addSubCommand('upgrade', array(
				'help' => __('Upgrade Croogo'),
				'parser' => $this->Upgrade->getOptionParser(),
				)
			)->addSubcommand('password', array(
				'help' => 'Get hashed password',
				'parser' => array(
					'description' => 'Get hashed password',
					'arguments' => array(
						'password' => array(
							'required' => true,
							'help' => 'Password to hash',
							),
						),
					),
				)
			);
		return $parser;
	}

/**
 * Get hashed password
 *
 * Usage: ./Console/cake croogo password myPasswordHere
 */
	public function password() {
		$value = trim($this->args['0']);
		$this->out(Security::hash($value, null, true));
	}

/**
 * Compile assets for admin ui
 */
	public function make() {
		$this->_compileCss();
		$this->_compileJs();
		$this->_copyFonts();
	}

/**
 * Compile CSS files used by admin ui
 */
	protected function _compileCss() {
		$bootstrapPath = WWW_ROOT . 'bootstrap';
		if (!file_exists($bootstrapPath)) {
			$this->out('<info>Cloning Bootstrap...</info>');
			chdir(WWW_ROOT);
			exec('git clone git://github.com/twitter/bootstrap');
		}
		chdir($bootstrapPath);
		exec('git checkout -f v2.2.0');

		App::import('Vendor', 'Lessc', array(
			'file' => 'lessphp' . DS . 'lessc.inc.php',
		));
		$lessc = new lessc();
		$formatter = new lessc_formatter_lessjs();
		$formatter->compressColors = false;
		ini_set('precision', 16);
		$lessc->setFormatter($formatter);

		$files = array(
			'less' . DS . 'admin.less' => CSS . 'croogo-bootstrap.css',
			'less' . DS . 'admin-responsive.less' => CSS . 'croogo-bootstrap-responsive.css',
		);
		foreach ($files as $file => $output) {
			$out = str_replace(APP, '', $output);
			if ($lessc->compileFile(WWW_ROOT . $file, $output)) {
				$text = __('CSS : <success>%s</success>', $out);
			} else {
				$text = __('File <error>%s</error>', $out);
			}
			$this->out($text);
		}
	}

/**
 * Compile javascripts
 */
	protected function _compileJs() {
		$bootstrapPath = WWW_ROOT . 'bootstrap';
		$outputFile = 'croogo-bootstrap.js';
		chdir($bootstrapPath);
		$rc = exec('cat js/bootstrap-transition.js js/bootstrap-alert.js js/bootstrap-button.js js/bootstrap-carousel.js js/bootstrap-collapse.js js/bootstrap-dropdown.js js/bootstrap-modal.js js/bootstrap-tooltip.js js/bootstrap-popover.js js/bootstrap-scrollspy.js js/bootstrap-tab.js js/bootstrap-typeahead.js js/bootstrap-affix.js > ../js/' . $outputFile);
		if ($rc == 0) {
			$text = __('JS  : <success>webroot/js/%s</success>', $outputFile);
		} else {
			$text = __('File <error>%s</error>', $outputFile);
		}
		$this->out($text);
	}

/**
 * Copy font files used by admin ui
 */
	protected function _copyFonts() {
		$fontAwesomePath = WWW_ROOT . 'fontAwesome';
		if (!file_exists($fontAwesomePath)) {
			$this->out('<info>Cloning FontAwesome...</info>');
			exec('git clone git://github.com/FortAwesome/Font-Awesome ' . $fontAwesomePath);
		}
		chdir($fontAwesomePath);
		exec('git checkout -f master');
		$targetPath = WWW_ROOT . 'font' . DS;
		$Folder = new Folder($targetPath, true);
		$fontPath = WWW_ROOT . 'fontAwesome' . DS . 'font';
		$Folder = new Folder($fontPath);
		$files = $Folder->read();
		if (empty($files[1])) {
			$this->err('No font files found');
			$this->_stop();
		}
		foreach ($files[1] as $file) {
			$File = new File($fontPath . DS . $file);
			$newFile = $targetPath . $file;
			if ($File->copy($newFile)) {
				$text = __('Font: <success>%s</success>', $file);
			} else {
				$text = __('File: <error>%s</error>', $file);
			}
			$this->out($text);
		}
	}

}
