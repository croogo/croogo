<?php

class AssetGenerator extends Object {

/**
 * Compile CSS files used by admin ui
 *
 * @throws Exception
 */
	protected function _compileCss() {
		$bootstrapPath = WWW_ROOT . 'bootstrap';
		if (!file_exists($bootstrapPath)) {
			if (!$this->_clone) {
				throw new Exception('You don\'t have "bootstrap" directory in ' . WWW_ROOT);
			}
			CakeLog::info('Cloning Bootstrap...');
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
				$text = __('CSS : %s created', $out);
				CakeLog::info($text);
			} else {
				$text = __('CSS : %s failed', $out);
				CakeLog::error($text);
			}
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
			$text = __('JS  : webroot/js/%s created', $outputFile);
			CakeLog::info($text);
		} else {
			$text = __('JS  : %s failed', $outputFile);
			CakeLog::error($text);
		}
	}

/**
 * Copy font files used by admin ui
 *
 * @throws Exception
 */
	protected function _copyFonts() {
		$fontAwesomePath = WWW_ROOT . 'fontAwesome';
		if (!file_exists($fontAwesomePath)) {
			if (!$this->_clone) {
				throw new Exception('You don\'t have "fontAwesome" in ' . WWW_ROOT);
			}
			CakeLog::info('Cloning FontAwesome...</info>');
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
			CakeLog::error('No font files found');
			$this->_stop();
		}
		foreach ($files[1] as $file) {
			$File = new File($fontPath . DS . $file);
			$newFile = $targetPath . $file;
			if ($File->copy($newFile)) {
				$text = __('Font: %s copied', $file);
				CakeLog::info($text);
			} else {
				$text = __('File: %s not copied', $file);
				CakeLog::error($text);
			}
		}
	}

	public function generate($options = array()) {
		$options = array_merge(array(
			'clone' => false,
		), $options);
		$this->_clone = $options['clone'];
		$this->_compileCss();
		$this->_compileJs();
		$this->_copyFonts();
	}

}
