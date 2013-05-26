<?php

App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

/**
 * AclActionsController Test
 */
class AclActionsControllerTest extends CroogoControllerTestCase {

/**
 * fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.aro',
		'plugin.users.aco',
		'plugin.users.aros_aco',
		'plugin.menus.menu',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.taxonomy.vocabulary',
		'plugin.settings.setting',
	);

/**
 * testGenerateActions
 *
 * @return void
 */
	public function testGenerateActions() {
		$AclActions = $this->generate('Acl.AclActions', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
				'Menus.Menus',
			),
		));
		$AclActions->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnValue(array('id' => 2, 'role_id' => 1)));
		$AclActions->Session
			->expects($this->any())
			->method('setFlash')
			->with(
				$this->matchesRegularExpression('/(Created Aco node:)|.*Aco Update Complete.*|(Skipped Aco node:)/'),
				$this->equalTo('default'),
				$this->anything(),
				$this->equalTo('flash')
			);
		$AclActions
			->expects($this->once())
			->method('redirect');
		$node = $AclActions->Acl->Aco->node('controllers/Nodes');
		$this->assertNotEmpty($node);
		$AclActions->Acl->Aco->removeFromTree($node[0]['Aco']['id']);
		$this->testAction('/admin/acl/acl_actions/generate');
	}

}
