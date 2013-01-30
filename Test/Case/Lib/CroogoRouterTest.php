<?php

App::uses('CroogoTestCase', 'TestSuite');
App::uses('Router', 'Routing');
App::uses('CroogoRouter', 'Lib');

class CroogoRouterTest extends CroogoTestCase {

	public $fixtures = array(
		'plugin.settings.setting',
		'plugin.taxonomy.vocabulary',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		);

	public function testContentType() {
		$params = array(
			'url' => array(),
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'blog',
			);
		$result = Router::reverse($params);
		$this->assertEquals('/nodes/nodes/index/type:blog', $result);

		Router::$routes = array();
		CroogoRouter::contentType('blog');
		$result = Router::reverse($params);
		$this->assertEquals('/blog', $result);

		CroogoRouter::contentType('page');
		$params = array(
			'url' => array(),
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'page',
			);
		$result = Router::reverse($params);
		$this->assertEquals('/page', $result);
	}

	public function testRoutableContentTypes() {
		$Type = ClassRegistry::init('Taxonomy.Type');
		$type = $Type->create(array(
			'title' => 'Press Release',
			'alias' => 'press-release',
			'description' => '',
			));
		$Type->save($type);
		$type = $Type->findByAlias('press-release');
		CroogoRouter::routableContentTypes();

		$params = array(
			'url' => array(),
			'plugin' => 'nodes',
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'press-release',
			);
		$result = Router::reverse($params);
		$this->assertEquals('/nodes/nodes/index/type:press-release', $result);

		$type['Type']['params'] = 'routes=1';
		$Type->save($type);
		Router::$routes = array();
		CroogoRouter::routableContentTypes();

		$result = Router::reverse($params);
		$this->assertEquals('/press-release', $result);
	}

}