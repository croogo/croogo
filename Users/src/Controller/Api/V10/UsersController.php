<?php
declare(strict_types=1);

namespace Croogo\Users\Controller\Api\V10;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Security;
use Croogo\Core\Controller\Api\AppController;
use Firebase\JWT\JWT;

/**
 * Users Controller
 *
 * @property \Croogo\Users\Model\Table\UsersTable $Users
 * @property \Croogo\Core\Controller\Component\CroogoComponent $Croogo
 * @property \Croogo\Meta\Controller\Component\MetaComponent $Meta
 * @property \Croogo\Blocks\Controller\Component\BlocksComponent $BlocksHook
 * @property \Croogo\Acl\Controller\Component\FilterComponent $Filter
 * @property \Acl\Controller\Component\AclComponent $Acl
 * @property \Croogo\Core\Controller\Component\ThemeComponent $Theme
 * @property \Croogo\Acl\Controller\Component\AccessComponent $Access
 * @property \Croogo\Settings\Controller\Component\SettingsComponent $SettingsComponent
 * @property \Croogo\Nodes\Controller\Component\NodesComponent $NodesHook
 * @property \Croogo\Menus\Controller\Component\MenuComponent $Menu
 * @property \Croogo\Users\Controller\Component\LoggedInUserComponent $LoggedInUser
 * @property \Croogo\Taxonomy\Controller\Component\TaxonomyComponent $Taxonomy
 */
class UsersController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow('token');
    }

    public function lookup()
    {
        return $this->Crud->execute();
    }

    public function index()
    {
        return $this->Crud->execute();
    }

    protected function generateToken($user)
    {
        $payload = [
            'username' => $user['username'],
            'name' => $user['name'],
            'timezone' => $user['timezone'],
        ];

        $expiry = 10 * 24 * 3600; // 10days
        $buffer = 5 * 60; // 5mins for random
        $exp = time() + rand($expiry, $expiry + $buffer);

        return JWT::encode([
            'iss' => Configure::read('Site.title'),
            'sub' => $user['id'],
            'user' => $payload,
            'iat' => time(),
            'exp' => $exp,
        ], Security::getSalt());
    }

    public function token()
    {
        $user = $this->Auth->identify();
        if ($user) {
            $token = $this->generateToken($user);
        } else {
            throw new NotFoundException();
        }

        $this->set([
            'data' => [
                'token' => $token,
            ],
            '_serialize' => ['data'],
        ]);
    }
}
