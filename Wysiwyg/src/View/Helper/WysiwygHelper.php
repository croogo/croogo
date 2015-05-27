<?php

namespace Croogo\Wysiwyg\View\Helper;

use App\View\Helper\AppHelper;
/**
 * Wysiwyg Helper
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
		$uploadsPath = Configure::read('Wysiwyg.uploadsPath');
		if ($uploadsPath) {
			$uploadsPath = Router::url($uploadsPath);
		}
		Configure::write('Js.Wysiwyg.uploadsPath', $uploadsPath);
		Configure::write('Js.Wysiwyg.attachmentsPath',
			$this->Html->url(Configure::read('Wysiwyg.attachmentBrowseUrl'))
		);

		$this->Html->script('/wysiwyg/js/wysiwyg', array('inline' => false));
	}
}
