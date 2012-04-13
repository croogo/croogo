<?php
App::uses('CroogoTestCase', 'TestSuite');

class CroogoTest extends CroogoTestCase {

	public function testCrossPluginHooks() {
		CakePlugin::load(array('Shops', 'Suppliers'), array(
			'bootstrap' => true,
			));
		$Order = ClassRegistry::init('Shops.Order');
		$this->assertTrue($Order->monitored);
	}

}