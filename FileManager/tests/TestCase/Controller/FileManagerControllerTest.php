<?php

namespace Croogo\FileManager\Test\TestCase\Controller;

use Croogo\TestSuite\CroogoControllerTestCase;
use FileManager\Controller\FileManagerController;

/**
 * FileManager Controller Test
 *
 * @category Test
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerControllerTest extends CroogoControllerTestCase
{

/**
 * fixtures
 *
 * @var array
 */
    public $fixtures = [
        'plugin.users.aco',
        'plugin.users.aro',
        'plugin.users.aros_aco',
        'plugin.settings.setting',
        'plugin.users.role',
        'plugin.blocks.block',
        'plugin.nodes.node',
        'plugin.menus.menu',
        'plugin.menus.link',
        'plugin.taxonomy.type',
        'plugin.taxonomy.types_vocabulary',
        'plugin.taxonomy.vocabulary',
    ];

/**
 * tearDown
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->FileManager);
    }

/**
 * testAdminBrowseRestricted
 *
 * @return void
 */
    public function testAdminBrowseRestricted()
    {
        $url = '/admin/file_manager/file_manager/browse?path=' . urlencode(APP . '../../..');
        $request = new Request($url);
        $response = new Response();
        $this->FileManager = new FileManagerController($request, $response);
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_browse',
            'named' => [],
            'pass' => [],
            '?' => [
                'path' => APP . '../../..',
            ],
        ]);
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertContains('is restricted', $message);
    }

/**
 * testAdminBrowse
 *
 * @return void
 */
    public function testAdminBrowse()
    {
        $url = '/admin/file_manager/file_manager/browse?path=' . urlencode(APP);
        $request = new Request($url);
        $response = new Response();
        $this->FileManager = new FileManagerController($request, $response);
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_browse',
            'named' => [],
            'pass' => [],
            '?' => [
                'path' => APP,
            ],
        ]);
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertEmpty($message);
    }

/**
 * testAdminBrowseSubfolder
 *
 * @return void
 */
    public function testAdminBrowseSubfolder()
    {
        $url = '/admin/file_manager/file_manager/browse?path=' . urlencode(APP) . 'webroot';
        $request = new Request($url);
        $response = new Response();
        $this->FileManager = new FileManagerController($request, $response);
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_browse',
            'named' => [],
            'pass' => [],
            '?' => [
                'path' => APP . 'webroot',
            ],
        ]);
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertEmpty($message);
    }

/**
 * testAdminUploadRestricted
 *
 * @return void
 */
    public function testAdminUploadRestricted()
    {
        $url = '/admin/file_manager/file_manager/upload?path=' . urlencode(APP . '../../..');
        $request = new CakeRequest($url);
        $response = new CakeResponse();
        $this->FileManager = $this->getMock(
            'FileManagerController',
            ['redirect'],
            [$request, $response]
        );
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_upload',
            'named' => [],
            'pass' => [],
            '?' => [
                'path' => APP . '../../..',
            ],
        ]);
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertContains('is restricted', $message);
    }

/**
 * testAdminCreateFileRestricted
 *
 * @return void
 */
    public function testAdminCreateFileRestricted()
    {
        $url = '/admin/file_manager/file_manager/create_file?path=' . urlencode(APP . '../../..');
        $request = new CakeRequest($url);
        $response = new CakeResponse();
        $this->FileManager = $this->getMock(
            'FileManagerController',
            ['redirect'],
            [$request, $response]
        );
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_create_file',
            'named' => [],
            'pass' => [],
            '?' => [
                'path' => APP . '../../..',
            ],
        ]);
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertContains('is restricted', $message);
    }

/**
 * testAdminCreateDirectoryRestricted
 *
 * @return void
 */
    public function testAdminCreateDirectoryRestricted()
    {
        $url = '/admin/file_manager/file_manager/create_directory?path=' . urlencode(APP . '../../..');
        $request = new CakeRequest($url);
        $response = new CakeResponse();
        $this->FileManager = $this->getMock(
            'FileManagerController',
            ['redirect'],
            [$request, $response]
        );
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_create_directory',
            'named' => [],
            'pass' => [],
            '?' => [
                'path' => APP . '../../..',
            ],
        ]);
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertContains('is restricted', $message);
    }

/**
 * testAdminDeleteDirectoryRestricted
 *
 * @return void
 */
    public function testAdminDeleteDirectoryRestricted()
    {
        $url = '/admin/file_manager/file_manager/delete_directory';
        $request = new CakeRequest($url);
        $response = new CakeResponse();
        $this->FileManager = $this->getMock(
            'FileManagerController',
            ['redirect'],
            [$request, $response]
        );
        $this->FileManager->request->addParams([
            'prefix' => 'admin',
            'admin' => true,
            'plugin' => 'file_manager',
            'controller' => 'file_manager',
            'action' => 'admin_delete_directory',
            'named' => [],
            'pass' => [],
        ]);
        $this->FileManager->request->data['path'] = APP . '../../..';
        $this->FileManager->constructClasses();
        $this->FileManager->Components->unload('Croogo.Croogo');
        $this->FileManager->Session->write('Auth.User', [
            'id' => 1,
            'role_id' => 1,
            'username' => 'admin',
        ]);
        $this->FileManager->startupProcess();
        $this->FileManager->invokeAction($this->FileManager->request);
        $message = $this->FileManager->Session->read('Message.flash.message');
        $this->assertContains('is restricted', $message);
    }
}
