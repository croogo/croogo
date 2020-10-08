<?php
declare(strict_types=1);

namespace Croogo\Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Event\EventInterface;
use Cake\Http\Cookie\Cookie;
use Cake\I18n\FrozenTime;
use Cake\Utility\Security;

/**
 * Provides "Remember me" feature (via CookieAuthenticate) by listening to
 * to Controller.Users.adminLoginSuccessful event and creating the appropriate
 * cookie.
 *
 * @category Component
 * @package  Croogo.Acl.Controller.Component
 * @since    1.5
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AutoLoginComponent extends Component
{

    protected $_defaultConfig = [
        'cookieName' => 'CAL',
        'cookieConfig' => [
            'expires' => '+1 day',
            'httpOnly' => true,
        ],
    ];

    /**
     * Controller instance
     *
     * @var \Cake\Controller\Controller
     */
    protected $_Controller;

    /**
     * User Model to use (retrieved from AuthComponent)
     */
    protected $_userModel;

    /**
     * Field setting (retrieved from AuthComponent)
     */
    protected $_fields;

    /**
     * Component startup
     */
    public function startup(EventInterface $event)
    {
        $this->_Controller = $controller = $event->getSubject();
        $controller->getEventManager()->on(
            'Controller.Users.adminLogoutSuccessful',
            [],
            [$this, 'onAdminLogoutSuccessful']
        );

        // skip autologin when mcrypt is not available
        if (!function_exists('mcrypt_decrypt') && !function_exists('openssl_decrypt')) {
            return;
        }

        $setting = $this->_registry->Auth->getConfig('authenticate.all');
        list(, $this->_userModel) = pluginSplit($setting['userModel']);
        $this->_fields = $setting['fields'];

        $controller->getEventManager()->on(
            'Controller.Users.adminLoginSuccessful',
            [$this, 'onAdminLoginSuccessful']
        );
    }

    /**
     * Prepare cookie data based on request
     *
     * @return array cookie data
     */
    protected function _cookie(ServerRequest $request)
    {
        $time = time();
        $username = $request->getData($this->_fields['username']);
        $hasher = $this->_registry->Auth->authenticationProvider()->passwordHasher();
        $data = json_encode([
            'hash' => $hasher->hash($username . $time),
            'time' => $time,
            'username' => $username,
        ]);

        $mac = hash_hmac('sha256', $data, Security::getSalt());

        return compact('mac', 'data');
    }

    /**
     * onAdminLoginSuccessful
     *
     * @return bool
     */
    public function onAdminLoginSuccessful(EventInterface $event)
    {
        /** @var \Cake\Controller\Controller */
        $controller = $event->getSubject();
        /** @var \Cake\Http\ServerRequest */
        $request = $controller->getRequest();
        $remember = $request->getData('remember');
        $expires = Configure::read('Access Control.autoLoginDuration');
        if (strtotime($expires) === false) {
            $expires = '+1 week';
        }
        if ($request->is('post') && $remember) {
            $data = $this->_cookie($request);
            $expiresAt = new FrozenTime($expires);
            $cookie = new Cookie($this->_config['cookieName'], $data, $expiresAt, null, null, null, true, 'Strict');
            $controller->setResponse($controller->getResponse()->withCookie($cookie));
        }

        return true;
    }

    /**
     * onAdminLogoutSuccessful
     *
     * @return bool
     */
    public function onAdminLogoutSuccessful(EventInterface $event)
    {
        /** @var \Cake\Controller\Controller */
        $controller = $event->getSubject();
        $controller->setResponse($controller->getResponse()->withExpiredCookie(new Cookie($this->_config['cookieName'])));

        return true;
    }
}
