<?php

namespace Croogo\Core\Test\TestCase\Controller;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Http\ServerRequest as Request;
use Croogo\Core\TestSuite\IntegrationTestCase;
use Cake\Utility\Hash;
use Croogo\Core\Controller\AppController;
use Croogo\Core\Croogo;

class TestAppController extends AppController
{

    public function admin_edit(): void
    {
    }

    public function admin_add(): void
    {
    }

    public function register(): void
    {
    }

    public function admin_index(): void
    {
    }

    public function admin_index_no_actions(): void
    {
    }
}

class CroogoAppControllerTest extends IntegrationTestCase
{

    protected array $fixtures = [
//      'plugin.Croogo/Settings.Setting',
//      'plugin.Croogo/Taxonomy.Type',
//      'plugin.Croogo/Nodes.Node',
    ];

    public function setUp(): void
    {
        parent::setUp();

//      $this->generate('TestApp', array(
//          'components' => array(
//              'Auth',
//              'Security',
//              'Acl.AclFilter',
//              'Blocks.Blocks',
//              'Menus.Menus',
//              'Taxonomy.Taxonomies',
//          )
//      ));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->controller);
    }

    /**
     * testRenderExistingView
     */
    public function testRenderExistingView(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->testAction('/admin/test_app/edit', [
            'return' => 'view',
        ]);
        $this->assertEquals('admin_edit', trim($result));
    }

    /**
     * testRenderAdminFormFallback
     */
    public function testRenderAdminFormFallback(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->testAction('/admin/test_app/add', [
            'return' => 'view',
        ]);
        $this->assertEquals('admin_form', trim($result));
    }

    /**
     * testRenderOverridenAdminFormWithTheme
     */
    public function testRenderOverridenAdminFormWithTheme(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $theme = 'Mytheme';
        $this->controller->theme = $theme;
        $filePath = App::themePath($theme) . 'TestApp' . DS . 'admin_edit.ctp';

        $expected = '<h1>I should be displayed</h1>';
        $File = new File($filePath, true, 0777);
        $File->write($expected);
        $File->close();

        $result = $this->testAction('/admin/test_app/edit', [
            'return' => 'contents',
        ]);

        $File->delete();
        $this->assertStringContainsString($expected, trim($result));
    }

    /**
     * testRenderNonEditView
     */
    public function testRenderNonEditView(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $result = $this->testAction('/test_app/register', [
            'return' => 'view',
        ]);
        $this->assertEquals('register', trim($result));
    }

    /**
     * testRenderDefaultActionsBlock
     */
    public function testRenderDefaultActionsBlock(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->controller->viewVars = [
            'displayFields' => [],
        ];
        $result = $this->testAction('/admin/test_app/index', [
            'return' => 'view',
        ]);
        $this->assertContains('btn-group', $result);
    }

    /**
     * testRenderNoActionsBlock
     */
    public function testRenderNoActionsBlock(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->controller->viewVars = [
            'displayFields' => [],
        ];
        $result = $this->testAction('/admin/test_app/index_no_actions', [
            'return' => 'view',
        ]);
        $this->assertNotContains('nav-buttons', $result);
    }

    /**
     * testSetFlashDefaults
     */
    public function testSetFlashDefaults(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->controller->viewVars = [
            'displayFields' => [],
        ];
        $this->controller->Session->setFlash('Terms & Conditions', 'flash');
        $result = $this->testAction('/admin/test_app/index', [
            'return' => 'contents',
        ]);
        $this->assertContains('Terms &amp; Conditions', $result);
    }

    /**
     * testSetFlashUnescaped
     */
    public function testSetFlashUnescaped(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->controller->viewVars = [
            'displayFields' => [],
        ];
        $this->controller->Session->setFlash('Terms & Conditions', 'flash', ['escape' => false]);
        $result = $this->testAction('/admin/test_app/index', [
            'return' => 'contents',
        ]);
        $this->assertContains('Terms & Conditions', $result);
    }

    /**
     * testPaginatorIsNotLoadedWithoutCache
     */
    public function testPaginatorIsNotLoadedWithoutCache(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->controller->viewVars = [
            'displayFields' => [],
        ];
        $this->assertFalse(in_array('Paginator', $this->controller->helpers));
        $this->controller->usePaginationCache = false;
        $result = $this->testAction('/admin/test_app/index', [
            'return' => 'view',
        ]);
        $this->assertFalse(in_array('Paginator', $this->controller->helpers));
    }

    /**
     * testPaginatorIsLoadedWithCache
     */
    public function testPaginatorIsLoadedWithCache(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $this->controller->viewVars = [
            'displayFields' => [],
        ];
        $this->assertFalse(in_array('Paginator', $this->controller->helpers));
        $this->controller->usePaginationCache = true;
        $result = $this->testAction('/admin/test_app/index', [
            'return' => 'view',
        ]);
        $this->assertTrue(in_array('Paginator', $this->controller->helpers));
    }

    /**
     * Test Setup Component
     */
    public function testSetupComponent(): void
    {
        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

        $request = new Request('/api/v1.0/users');
        $request->addParams([
            'api' => 'api',
            'prefix' => 'v1.0',
        ]);

        $controller = new TestAppController($request);
        $defaultComponents = $controller->components;
        $this->assertEmpty($controller->_apiComponents);

        $key = 'Hook.controller_properties.TestApp._apiComponents';
        Configure::write($key, ['BogusApi']);
        Croogo::hookApiComponent('TestApp', 'Example.ImaginaryApi');

        $expected = [
            'BogusApi' => [
                'className' => 'BogusApi',
                'priority' => 8,
            ],
            'ImaginaryApi' => [
                'className' => 'Example.ImaginaryApi',
                'priority' => 8,
            ],
        ];
        $controller = new TestAppController($request);
        $this->assertEquals($expected, $controller->_apiComponents);

        $merged = Hash::merge(
            $defaultComponents,
            ['BogusApi'],
            ['Example.ImaginaryApi' => ['priority' => 8]]
        );
        $this->assertEquals($merged, $controller->components);

        Configure::delete('Hook.controller_properties.TestApp');
    }
}
