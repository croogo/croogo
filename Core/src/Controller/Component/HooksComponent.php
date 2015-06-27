<?php

namespace Croogo\Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;

class HooksComponent extends ControllerPreparingComponent
{

	/**
	 * Default Components
	 *
	 * @var array
	 * @access public
	 */
	protected $_defaultComponents = array(
		'Croogo/Core.Croogo',
		'Croogo/Acl.Filter',
		'Security',
		'Acl.Acl',
		'Auth' => [
			'authenticate' => [
				'Form' => [
					'passwordHasher' => [
						'className' => 'Fallback',
						'hashers' => ['Default', 'Weak']
					]
				]
			]
		],
		'Flash',
		'RequestHandler',
	);

	public function prepareController(Controller $controller)
	{
		$event = new Event('Controller.afterConstruct', $controller);
		$controller->eventManager()->dispatch($event);

		$this->afterConstruct($event);

		$controller->_appComponents = [];
		$controller->_apiComponents = [];
	}

	public function implementedEvents()
	{
		return parent::implementedEvents() + [
			'Controller.afterConstruct' => 'afterConstruct',
		];
	}

	public function afterConstruct(Event $event)
	{
		/** @var Controller $controller */
		$controller = $event->subject();

		Croogo::applyHookProperties('Hook.controller_properties', $controller);
		$this->_setupComponents($controller);
	}

	/**
	 * Setup the components array
	 *
	 * @param void
	 * @return void
	 */
	protected function _setupComponents(Controller $controller) {
		$components = [];

		if ($controller->request && !$controller->request->is('api')) {
			$components = Hash::merge(
				$this->_defaultComponents,
				$controller->_appComponents
			);
		} else {
			$components = Hash::merge(
				[
					'Acl.Acl',
					'Auth',
					'Security',
					'Flash',
					'RequestHandler',
					'Croogo/Acl.AclFilter'
				],
				$controller->_apiComponents
			);

			$apiComponents = array();
			$priority = 8;
			foreach ($controller->_apiComponents as $component => $setting) {
				if (is_string($setting)) {
					$component = $setting;
					$setting = array();
				}
				$className = $component;
				list(, $apiComponent) = pluginSplit($component);
				$setting = Hash::merge(compact('className', 'priority'), $setting);
				$apiComponents[$apiComponent] = $setting;
			}
			$controller->_apiComponents = $apiComponents;
		}

		foreach ($components as $component => $config) {
			if (!is_array($config)) {
				$component = $config;
				$config = [];
			}

			$controller->loadComponent($component, $config);
		}
	}


}
