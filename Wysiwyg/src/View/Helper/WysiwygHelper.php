<?php

namespace Croogo\Wysiwyg\View\Helper;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\View\Helper;
use Cake\Core\App;

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
class WysiwygHelper extends Helper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = [
		'Html',
		'Url'
	];

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
			$this->Url->build(Configure::read('Wysiwyg.attachmentBrowseUrl'))
		);

		$namespace = 'Controller';
		$pluginPath = $this->request->param('plugin') . '.';
		$controller = $this->request->param('controller');

		if ($this->request->param('prefix')) {
			$prefixes = array_map(
				'Cake\Utility\Inflector::camelize',
				explode('/', $this->request->param('prefix'))
			);
			$namespace .= '/' . implode('/', $prefixes);
		}

		$actions = array();
		foreach (Configure::read('Wysiwyg.actions') as $key => $value) {
			if (is_string($value)) {
				$actions[] = $value;
			} else {
				$actions[] = $key;
			}
		}

		$currentAction = App::classname($pluginPath . $controller, $namespace, 'Controller') . '.' . $this->request->param('action');
		$included = in_array($currentAction, $actions, true);
		if ($included) {
			$this->Html->script('Croogo/Wysiwyg.wysiwyg', ['block' => true]);
		}
	}
}
