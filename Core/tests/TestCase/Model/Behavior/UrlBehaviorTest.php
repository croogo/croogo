<?php
namespace Croogo\Core\Test\TestCase\Model\Behavior;

use Croogo\Core\TestSuite\CroogoTestCase;
use Nodes\Model\Node;
class UrlBehaviorTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.croogo\users.aco',
		'plugin.croogo\users.aro',
		'plugin.croogo\users.aros_aco',
//		'plugin.blocks.block',
//		'plugin.comments.comment',
//		'plugin.contacts.contact',
//		'plugin.translate.i18n',
		'plugin.croogo\settings.language',
//		'plugin.menus.link',
//		'plugin.menus.menu',
//		'plugin.contacts.message',
//		'plugin.croogo\nodes.node',
//		'plugin.meta.meta',
//		'plugin.taxonomy.model_taxonomy',
//		'plugin.blocks.region',
		'plugin.croogo\users.role',
		'plugin.croogo\settings.setting',
//		'plugin.taxonomy.taxonomy',
//		'plugin.taxonomy.term',
//		'plugin.taxonomy.type',
//		'plugin.taxonomy.types_vocabulary',
		'plugin.croogo\users.user',
//		'plugin.taxonomy.vocabulary',
	);

	public $Node = null;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
//		$this->Node = ClassRegistry::init('Nodes.Node');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Node);
//		ClassRegistry::flush();
	}

	public function testSingle() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$helloWorld = $this->Node->findBySlug('hello-world');
		$this->assertEqual($helloWorld['Node']['url'], array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'type' => 'blog',
			'slug' => 'hello-world',
		));
	}

	public function testMultiple() {
		$this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

		$result = $this->Node->find('all');
		$this->assertEqual($result['0']['Node']['url'], array(
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'view',
			'type' => $result['0']['Node']['type'],
			'slug' => $result['0']['Node']['slug'],
		));
	}

}
