<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Wysiwyg Helper
 *
 * PHP version 5
 *
 * @category Wysiwyg.Helper
 * @package  Croogo.Wysiwyg.View.Helper
 * @version  1.5
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class WysiwygHelper extends AppHelper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
	);

/**
 * beforeRender
 *
 * @param string $viewFile
 * @return void
 */
	public function beforeRender($viewFile) {
		Configure::write('Js.Wysiwyg.uploadsPath', Router::url('/uploads/'));
		Configure::write('Js.Wysiwyg.attachmentsPath', $this->Html->url(array(
			'plugin' => 'file_manager',
			'controller' => 'attachments',
			'action' => 'browse',
		)));

		$this->Html->script('/wysiwyg/js/wysiwyg', array('inline' => false));
	}
}
