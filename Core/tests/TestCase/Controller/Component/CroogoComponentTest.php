<?php

namespace Croogo\Core\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Croogo\Core\Controller\Component\CroogoComponent;
use Croogo\Core\TestSuite\TestCase;

class MockCroogoComponent extends CroogoComponent
{

    public function startup(Event $event)
    {
        $this->_controller = $event->getSubject();
    }
}

class CroogoComponentTest extends TestCase
{

    public $fixtures = [
//      'plugin.Croogo/Users.Aco',
//      'plugin.Croogo/Users.Aro',
//      'plugin.Croogo/Users.ArosAco',
//      'plugin.Croogo/Settings.Setting',
//      'plugin.Croogo/Menus.Menu',
//      'plugin.Croogo/Menus.Link',
//      'plugin.Croogo/Users.Role',
//      'plugin.Croogo/Taxonomy.Type',
//      'plugin.Croogo/Taxonomy.Vocabulary',
//      'plugin.Croogo/Taxonomy.TypesVocabulary',
//      'plugin.Croogo/Nodes.Node',
    ];

    public $component = null;
    public $controller = null;

    public function setUp()
    {
        parent::setUp();

        // Setup our component and fake test controller
        $this->controller = $this->getMockBuilder(Controller::class)
            ->setMethods(['redirect'])
            ->setConstructorArgs([new Request, new Response])
            ->getMock();

        $registry = new ComponentRegistry($this->controller);
        $this->component = new CroogoComponent($registry);

//      $this->Controller = new Controller(new Request(), new Response());
////        $this->Controller->constructClasses();
//      $this->Controller->Croogo = new MockCroogoComponent($this->Controller->components());
//      $this->Controller->components()->unload('Blocks');
//      $this->Controller->components()->unload('Menus');
//      $this->Controller->components()->set('Croogo', $this->Controller->Croogo);
//      $this->Controller->startupProcess();
    }

    public function testAddRemoveAcos()
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $Aco = ClassRegistry::init('Aco');

        $this->Controller->Croogo->addAco('CroogoTestController');
        $parent = $Aco->findByAlias('CroogoTestController');
        $this->assertNotEmpty($parent);

        $this->Controller->Croogo->addAco('CroogoTestController/index');
        $child = $Aco->findByParentId($parent['Aco']['id']);
        $this->assertNotEmpty($child);

        $this->Controller->Croogo->removeAco('CroogoTestController/index');
        $child = $Aco->findByParentId($parent['Aco']['id']);
        $this->assertEmpty($child);

        $this->Controller->Croogo->removeAco('CroogoTestController');
        $parent = $Aco->findByAlias('CroogoTestController');
        $this->assertEmpty($parent);
    }

    /**

     * redirectData
     *
     * @return array
     */
    public function redirectData()
    {
        return [
            ['croogo.org', 'croogo.org'],
            [['action' => 'index'], ['action' => 'edit', 1]],
            [['action' => 'edit', 1], ['action' => 'edit', 1], ['apply' => 'Apply']],
            [['action' => 'index', 1], ['action' => 'edit', 1], [], ['action' => 'index', 1]],
            [['action' => 'edit', 1], ['action' => 'edit', 1], ['apply' => 'Apply'], ['action' => 'index', 1]],
        ];
    }

    public function tearDown()
    {
        parent::tearDown();

        // Clean up after we're done
        unset($this->component, $this->controller);
    }
}
