<?php

App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

class TranslateControllerTest extends CroogoControllerTestCase {

	public $fixtures = array(
		'plugin.users.aro',
		'plugin.users.aco',
		'plugin.users.aros_aco',
		'plugin.comments.comment',
		'plugin.menus.menu',
		'plugin.meta.meta',
		'plugin.nodes.node',
		'plugin.settings.language',
		'plugin.settings.setting',
		'plugin.taxonomy.taxonomy',
		'plugin.taxonomy.nodes_taxonomy',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.taxonomy.vocabulary',
		'plugin.translate.i18n',
		'plugin.users.user',
		'plugin.users.role',
	);

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		CakePlugin::load('Translate', array('bootstrap' => true));
		$this->TranslateController = $this->generate('Translate.Translate', array(
			'methods' => array(
				'redirect',
			),
			'components' => array(
				'Auth' => array('user'),
				'Session',
				)
			)
		);
		$this->TranslateController->Auth
			->staticExpects($this->any())
			->method('user')
			->will($this->returnCallback(array($this, 'authUserCallback')));
		$this->TranslateController->Security->Session = $this->getMock('CakeSession');
	}

/**
 * test admin_index action
 */
	public function testAdminIndex() {
		$this->testAction('/admin/translate/translate/index/2/Node');
		$this->assertEquals(2, $this->vars['record']['Node']['id']);
		$this->assertEquals('Node', $this->vars['modelAlias']);
	}

/**
 * test admin_edit action with invalid language
 */
	public function testAdminEditWithBogusLanguage() {
		$this->expectFlashAndRedirect('Invalid Language', null, array(
			'params' => array(
				'class' => 'error',
			),
		));
		$this->testAction('/admin/translate/translate/edit/2/Node/locale:lol');
	}

/**
 * test admin_edit action
 */
	public function testAdminEdit() {
		$this->testAction('/admin/translate/translate/edit/2/Node/locale:eng');
		$this->assertEquals(2, $this->vars['id']);
		$this->assertEquals('Node', $this->vars['modelAlias']);
		$this->assertEquals(1, $this->vars['language']['Language']['id']);
		$this->assertEquals(2, $this->controller->request->data['Node']['id']);
	}

/**
 * test saving translation with admin_edit action
 */
	public function testSaveWithAdminEdit() {
		$this->expectFlashAndRedirect('Record has been translated');
		$this->testAction('/admin/translate/translate/edit/2/Node/locale:eng', array(
			'data' => array(
				'Node' => array(
					'id' => 2,
					'title' => 'Hello world [in English locale]',
					'slug' => 'hello-world-in-english-locale',
					'type' => 'blog',
				),
				'Role' => array(
					'Role' => array(),
				),
			),
		));
	}

}
