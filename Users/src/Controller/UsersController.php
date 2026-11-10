<?php

namespace Croogo\Users\Controller;

use Croogo\Core\Croogo;
use Croogo\Users\Model\Table\UsersTable;

/**
 * Users Controller
 *
 * @property UsersTable Users
 * @category Controller
 * @package  Croogo.Users.Controller
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UsersController extends AppController
{

    /**
     * {inheritdoc}
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow(['logout']);
    }

    /**
     * Login
     *
     * @return \Cake\Http\Response|void
     * @access public
     */
    public function login()
    {
        return $this->redirect('/admin');
    }

    /**
     * Logout
     *
     * @access public
     */
    public function logout()
    {
        Croogo::dispatchEvent('Controller.Users.beforeLogout', $this);
        $this->getRequest()->getSession()->delete('Croogo.redirect');

        $this->Flash->success(__d('croogo', 'Log out successful.'), 'auth');

        $logoutUrl = $this->Auth->logout();
        Croogo::dispatchEvent('Controller.Users.afterLogout', $this);

        return $this->redirect($logoutUrl);
    }
}
