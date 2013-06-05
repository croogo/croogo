<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoTest extends CroogoTestCase {

	public function testCrossPluginHooks() {
		CakePlugin::load(array('Shops', 'Suppliers'), array(
			'bootstrap' => true,
			));
		$Order = ClassRegistry::init('Shops.Order');
		$this->assertTrue($Order->monitored);
	}

}