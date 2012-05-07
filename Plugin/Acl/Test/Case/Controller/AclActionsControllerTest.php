<?php

App::uses('CroogoControllerTestCase', 'TestSuite');

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
		'app.aro',
		'app.aco',
		'app.aros_aco',
		'app.menu',
		'app.type',
		'app.types_vocabulary',
		'app.vocabulary',
		'app.setting',
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
			),
		));
		$AclActions->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnValue(2));
		$AclActions->Session
			->expects($this->once())
			->method('setFlash')
			->with(
				$this->matchesRegularExpression('/Created [0-9]+ new permissions/'),
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
