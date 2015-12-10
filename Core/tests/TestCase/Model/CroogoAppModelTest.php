<?php
namespace Croogo\Core\Test\TestCase\Model;

use App\Controller\Component\AuthComponent;
use App\Model\Model;
use App\Model\User;
use Cake\ORM\TableRegistry;
use Croogo\Model\CroogoAppModel;
use Croogo\Core\TestSuite\CroogoTestCase;
use Croogo\Users\Model\Table\UsersTable;

/**
 * CroogoAppModelTest file
 *
 * This file is to test the CroogoAppModel
 *
 * @category Test
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAppModelTest extends CroogoTestCase
{

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = [
//		'plugin.croogo\users.aco',
//		'plugin.croogo\users.aro',
//		'plugin.croogo\users.aros_aco',
//		'plugin.croogo\users.role',
//		'plugin.croogo\users.user',
//		'plugin.croogo\settings.setting',
    ];

/**
 * User instance
 *
 * @var TestUser
 */
    public $User;

/**
 * @var UsersTable
 */
    public $usersTable;

/**
 * setUp method
 *
 * @return void
 */
    public function setUp()
    {
        parent::setUp();

        $this->markTestIncomplete('This test needs to be ported to CakePHP 3.0');

//		$this->User = ClassRegistry::init('Users.User');
        $this->usersTable = TableRegistry::get('Croogo/Users.Users');
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->User);
    }

/**
 * testValidName
 */
    public function testValidName()
    {
        $this->assertTrue($this->usersTable->validName(['name' => 'Kyle']));
        $this->assertFalse($this->usersTable->validName(['name' => 'what%is@this#i*dont!even']));
    }

/**
 * testValidAlias
 */
    public function testValidAlias()
    {
        $this->assertTrue($this->usersTable->validAlias(['name' => 'Kyle']));
        $this->assertFalse($this->usersTable->validAlias(['name' => 'Not an Alias']));
    }
}
