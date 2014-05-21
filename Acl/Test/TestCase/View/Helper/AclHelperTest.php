<?php

namespace Croogo\Acl\Test\TestCase\View\Helper;

use Acl\View\Helper\AclHelper;
use Cake\View\View;
use Croogo\TestSuite\CroogoTestCase;
class AclHelperTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.users.user',
		'plugin.users.role',
		'plugin.users.aro',
		'plugin.users.aco',
		'plugin.users.aros_aco',
	);

	public function setUp() {
		parent::setUp();
		$View = $this->getMock('View');
		$this->AclHelper = $this->getMock('AclHelper', null, array($View));
	}

/**
 * testLinkIsAllowedByRoleId
 */
	public function testLinkIsAllowedByRoleId() {
		Cache::clearGroup('acl', 'permissions');
		$resetUrl = array(
			'controller' => 'Users',
			'action' => 'reset',
		);
		$nodeViewUrl = array(
			'controller' => 'Nodes',
			'action' => 'view',
		);

		$result = $this->AclHelper->linkIsAllowedByRoleId(2, $nodeViewUrl);
		$this->assertTrue($result);

		// Public role must not have access to users/reset
		$result = $this->AclHelper->linkIsAllowedByRoleId(2, $resetUrl);
		$this->assertFalse($result);

		$result = $this->AclHelper->linkIsAllowedByRoleId(3, $nodeViewUrl);
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByRoleId(3, $resetUrl);
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByRoleId(3, '#');
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByRoleId(3, '/admin');
		$this->assertFalse($result);
	}

/**
 * testLinkIsAllowedByUserId
 */
	public function testLinkIsAllowedByUserId() {
		Cache::clearGroup('acl', 'permissions');
		$resetUrl = 'controllers/Users/reset';
		$nodeViewUrl = 'controllers/Nodes/view';

		$result = $this->AclHelper->linkIsAllowedByUserId(3, $nodeViewUrl);
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByUserId(3, $resetUrl);
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByUserId(2, $nodeViewUrl);
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByUserId(2, $resetUrl);
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByUserId(3, '#');
		$this->assertTrue($result);

		$result = $this->AclHelper->linkIsAllowedByUserId(3, '/admin');
		$this->assertFalse($result);
	}

}
