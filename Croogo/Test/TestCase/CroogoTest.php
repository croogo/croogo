<?php

namespace Croogo\Croogo\Test\TestCase;
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
	);

	public function testCrossPluginHooks() {
		CakePlugin::load(array('Shops', 'Suppliers'), array(
			'bootstrap' => true,
		));
		$Order = ClassRegistry::init('Shops.Order');
		$this->assertTrue($Order->monitored);
	}

/**
 * test Croogo::hookApiComponent
 */
	public function testHookApiComponent() {
		$hooks = Configure::read('Hook.controller_properties');
		Configure::write('Hook.controller_properties', array());

		Croogo::hookApiComponent('Example', 'Example.ExampleApi');
		Croogo::hookApiComponent('Example', array(
			'Users.UserApi' => array(
				'priority' => 2,
			),
		));

		$expected = array(
			'Example' => array(
				'_apiComponents' => array(
					'Example.ExampleApi' => array(
						'priority' => 8,
					),
					'Users.UserApi' => array(
						'priority' => 2,
					),
				),
			),
		);
		$result = Configure::read('Hook.controller_properties');
		$this->assertEquals($expected, $result);

		Configure::write('Hook.controller_properties', $hooks);
	}
}
