<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Ckeditor Helper
 *
 * PHP version 5
 *
 * @category Ckeditor.Helper
 * @package  Croogo
 * @version  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CkeditorHelper extends AppHelper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Js',
	);

/**
 * Actions
 *
 * Format: ControllerName/action_name => settings
 *
 * @var array
 */
	public $actions = array();

/**
 * beforeRender
 *
 * @param string $viewFile
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->Html->script('/ckeditor/js/wysiwyg', array('inline' => false));

		if (is_array(Configure::read('Wysiwyg.actions'))) {
			$this->actions = Hash::merge($this->actions, Configure::read('Wysiwyg.actions'));
		}
		$action = Inflector::camelize($this->params['controller']) . '/' . $this->params['action'];
		if (Configure::read('Writing.wysiwyg') && isset($this->actions[$action])) {
			$this->Html->script('/ckeditor/js/ckeditor', array(
				'inline' => false,
			));
			
			$ckeditorActions = Configure::read('Wysiwyg.actions');
			if (isset($ckeditorActions[$action])) {
				$actionItems = $ckeditorActions[$action];
				$out = '$(document).ready(function() {';
				foreach ($actionItems as $actionItem) {
					$out .= "CKEDITOR.replace('" . $actionItem['elements'] . "', {filebrowserBrowseUrl: Croogo.Wysiwyg.attachmentsPath, filebrowserImageBrowseUrl: Croogo.Wysiwyg.attachmentsPath});";
				}
				$out .= '});';
				$this->Html->scriptBLock($out, array(
					'inline' => false,
				));
			}
		}
	}
}
