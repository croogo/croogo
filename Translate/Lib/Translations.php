<?php

/**
 * Translations
 *
 * @package  Croogo.Translate.Lib
 * @author   Rachman Chavik <rchavik@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class Translations {

/**
 * Read configured Translate.models and hook the appropriate behaviors
 */
	public static function translateModels() {
		$path ='admin:true/plugin:translate/controller:translate/action:index/:id/';
		foreach (Configure::read('Translate.models') as $model => $config) {
			Croogo::hookBehavior($model, 'Translate.CroogoTranslate', $config);
			Croogo::hookAdminRowAction(
				Inflector::pluralize($model) . '/admin_index',
				__d('croogo', 'Translate'), array(
				$path . $model => array(
					'title' => false,
					'options' => array(
						'icon' => 'translate',
						'data-title' => __d('croogo', 'Translate'),
					),
				))
			);
		}
	}

}
