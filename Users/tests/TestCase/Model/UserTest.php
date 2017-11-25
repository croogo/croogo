<?php
namespace Croogo\Users\Test\TestCase\Model;

use Cake\Controller\Component\AuthComponent;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Users\Model\User;

/**
 * TestUser
 *
 */
class UserTest extends CroogoTestCase
{

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = [
        'plugin.croogo/users.role',
        'plugin.croogo/users.user',
        'plugin.croogo/users.aco',
        'plugin.croogo/users.aro',
        'plugin.croogo/users.aros_aco',
    ];

/**
 * User instance
 *
 * @var TestUser
 */
    public $User;

/**
 * setUp method
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();
//		$this->User = ClassRegistry::init('TestUser');
//		$this->User->Aro->useDbConfig = 'test';
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->User->request, $this->User);
    }

/**
 * testPasswords method
 *
 * @return void
 */
    public function testPasswords()
    {
        $this->User->create([
            'username' => 'new_user',
            'name' => 'New User',
            'role_id' => 3,
            'email' => 'contact@croogo.org',
            'password' => 'password',
            'website' => 'http://croogo.org',
            'activation_key' => md5(uniqid()),
        ]);
        $this->User->save();
        $this->assertEmpty($this->User->validationErrors, 'Validation error: ' . print_r($this->User->validationErrors, true));
        $newUser = $this->User->read();
        $this->assertNotEqual($newUser, false);
        $this->assertNotEqual($newUser['User']['password'], 'password');
        $this->assertEqual($newUser['User']['password'], AuthComponent::password('password'));

        $newUser['User']['password'] = '123456';
        $this->User->id = $newUser['User']['id'];
        $this->User->save($newUser);
        $this->assertEmpty($this->User->validationErrors, 'Validation error: ' . print_r($this->User->validationErrors, true));
        $newUser = $this->User->read();
        $this->assertNotEqual($newUser['User']['password'], '123456');
        $this->assertEqual($newUser['User']['password'], AuthComponent::password('123456'));

        $oldPassword = $newUser['User']['password'];
        $newUser['User']['password'] = '';
        $this->User->id = $newUser['User']['id'];
        $this->User->save($newUser);
        $this->assertContains('Passwords must be at least 6 characters long.', print_r($this->User->validationErrors, true));
        $newUser = $this->User->read();
        $this->assertEqual($newUser['User']['password'], $oldPassword);
    }

/**
 * testValidIdenticalPassword method
 *
 * @return void
 */
    public function testValidIdenticalPassword()
    {
        $this->User->data['User'] = ['password' => '123456'];
        $this->assertTrue($this->User->validIdentical(['verify_password' => '123456']));
        $this->User->data['User'] = ['password' => '123456'];
        $this->assertContains('Passwords do not match. Please, try again.', $this->User->validIdentical(['verify_password' => 'other-value']));
    }

/**
 * testDeleteLastUser method
 *
 * @return void
 */
    public function testDeleteLastUser()
    {
        $this->User->create([
            'username' => 'new_user',
            'name' => 'Admin User',
            'role_id' => 1,
            'email' => 'contact@croogo.org',
            'password' => 'password',
            'website' => 'http://croogo.org',
            'activation_key' => md5(uniqid()),
            'status' => true,
        ]);
        $this->User->save();
        $newUser = $this->User->read();
        $this->User->deleteAll(['User.id !=' => $newUser['User']['id']]);
        $this->assertFalse($this->User->delete($newUser['User']['id']));
    }

/**
 * testDeleteAdminUser method
 *
 * @return void
 */
    public function testDeleteAdminUser()
    {
        $this->User->create([
            'username' => 'admin_user',
            'name' => 'Admin User',
            'role_id' => 1,
            'email' => 'contact@croogo.org',
            'password' => 'password',
            'website' => 'http://croogo.org',
            'activation_key' => md5(uniqid()),
            'status' => true,
        ]);
        $this->User->save();
        $newAdmin = $this->User->read();
        $this->User->create([
            'username' => 'another_adm',
            'name' => 'Another Admin',
            'role_id' => 1,
            'email' => 'another_adm@croogo.org',
            'password' => 'password',
            'website' => 'http://croogo.org',
            'activation_key' => md5(uniqid()),
            'status' => true,
        ]);
        $this->User->save();
        $anotherAdmin = $this->User->read();
        $this->User->deleteAll(['NOT' => ['User.id' => [$newAdmin['User']['id'], $anotherAdmin['User']['id']]]]);
        $this->assertTrue($this->User->delete($newAdmin['User']['id']));
    }

/**
 * testDisplayFields
 *
 * @return void
 */
    public function testDisplayFields()
    {
        $result = $this->User->displayFields();
        $expected = [
            'id' => [
                'label' => 'Id',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
            'username' => [
                'label' => 'Username',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
            'name' => [
                'label' => 'Name',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
            'email' => [
                'label' => 'Email',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
            'status' => [
                'label' => 'Status',
                'sort' => true,
                'type' => 'boolean',
                'url' => [],
                'options' => [],
            ],
            'Role.title' => [
                'label' => 'Role',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
        ];
        $this->assertEquals($expected, $result);

        $result = $this->User->displayFields([
            'one', 'two', 'three',
        ]);
        $expected = [
            'one' => [
                'label' => 'One',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
            'two' => [
                'label' => 'Two',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
            'three' => [
                'label' => 'Three',
                'sort' => true,
                'type' => 'text',
                'url' => [],
                'options' => [],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

/**
 * testEditFields
 *
 * @return void
 */
    public function testEditFields()
    {
        $result = $this->User->editFields();
        $expected = [
            'role_id' => [],
            'username' => [],
            'name' => [],
            'email' => [],
            'website' => [],
            'status' => [],
        ];
        $this->assertEquals($expected, $result);

        $result = $this->User->editFields([]);
        $expected = [
            'role_id' => [],
            'username' => [],
            'password' => [],
            'name' => [],
            'email' => [],
            'website' => [],
            'activation_key' => [],
            'image' => [],
            'bio' => [],
            'timezone' => [],
            'status' => [],
            'updated' => [],
            'created' => [],
            'updated_by' => [],
            'created_by' => [],
        ];
        $this->assertEquals($expected, $result);

        $expected = [
            'field' => [
                'label' => 'My Field',
                'type' => 'select',
                'options' => [1, 2, 3],
            ],
        ];
        $result = $this->User->editFields($expected);
        $this->assertEquals($expected, $result);
    }

/**
 * testDeleteAdminUsers
 */
    public function testDeleteAdminUsers()
    {
        // delete an admin
        $this->User->id = 2;
        $result = $this->User->delete();
        $this->assertTrue($result);

        // delete last remaining admin
        $this->User->id = 1;
        $result = $this->User->delete();
        $this->assertFalse($result);

        // delete normal user
        $this->User->id = 3;
        $result = $this->User->delete();
        $this->assertTrue($result);

        $count = $this->User->find('count');
        $this->assertEquals(1, $count);
    }

/**
 * testDeleteUsers
 */
    public function testDeleteUsers()
    {
        // delete normal user
        $this->User->id = 3;
        $result = $this->User->delete();
        $this->assertTrue($result);

        // delete an admin
        $this->User->id = 2;
        $result = $this->User->delete();
        $this->assertTrue($result);

        // delete last remaining admin
        $this->User->id = 1;
        $result = $this->User->delete();
        $this->assertFalse($result);

        $count = $this->User->find('count');
        $this->assertEquals(1, $count);
    }
}
