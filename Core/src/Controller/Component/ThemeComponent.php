<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Configure;
use Croogo\Core\Croogo;
use Croogo\Extensions\CroogoTheme;

class ThemeComponent extends ControllerPreparingComponent
{

	public function prepareController(Controller $controller)
	{
		$this->_setupTheme($controller);
	}

	protected function _setupTheme(Controller $controller) {
		$prefix = isset($controller->request->params['prefix']) ? $controller->request->params['prefix'] : '';
		if ($prefix === 'admin') {
			$theme = Configure::read('Site.admin_theme');
			if ($theme) {
				App::build(array(
					'View/Helper' => array(App::themePath($theme) . 'Helper' . DS),
				));
			}
			$controller->layout = 'Croogo/Core.admin';
		} else {
			$theme = Configure::read('Site.theme');
		}
		$controller->theme = $theme;

		$croogoTheme = new CroogoTheme();
		$data = $croogoTheme->getData($theme);
		$settings = $data['settings'];

		if (empty($settings['prefixes']['admin']['helpers']['Croogo/Core.Croogo'])) {
			$controller->helpers[] = 'Croogo/Core.Croogo';
		}

		if (isset($settings['prefixes'][$prefix])) {
			foreach ($settings['prefixes'][$prefix]['helpers'] as $helper => $settings) {
				$controller->helpers[$helper] = $settings;
			}
		}
	}

}
