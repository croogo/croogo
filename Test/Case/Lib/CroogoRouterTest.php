<?php

App::uses('CroogoTestCase', 'TestSuite');
App::uses('Router', 'Routing');
App::uses('CroogoRouter', 'Lib');

class CroogoRouterTest extends CroogoTestCase {

	public $fixtures = array(
		'setting',
		'type',
		'vocabulary',
		'types_vocabulary',
		);

	public function testContentType() {
		$params = array(
			'url' => array(),
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'blog',
			);
		$result = Router::reverse($params);
		$this->assertEquals('/nodes/index/type:blog', $result);

		CroogoRouter::contentType('blog');
		$result = Router::reverse($params);
		$this->assertEquals('/blog', $result);

		CroogoRouter::contentType('page');
		$params = array(
			'url' => array(),
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'page',
			);
		$result = Router::reverse($params);
		$this->assertEquals('/page', $result);
	}

	public function testRoutableContentTypes() {
		$Type = ClassRegistry::init('Type');
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
			'controller' => 'nodes',
			'action' => 'index',
			'type' => 'press-release',
			);
		$result = Router::reverse($params);
		$this->assertEquals('/nodes/index/type:press-release', $result);

		$type['Type']['params'] = 'routes=1';
		$Type->save($type);
		CroogoRouter::routableContentTypes();

		$result = Router::reverse($params);
		$this->assertEquals('/press-release', $result);
	}

}