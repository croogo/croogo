<?php

namespace Croogo\Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\Event;

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
    public function startup(Event $event)
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

        $this->_registry->Cookie->configKey($this->getConfig('cookieName'), $this->getConfig('cookieConfig'));

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
    protected function _cookie($request)
    {
        $time = time();
        $username = $request->getData($this->_fields['username']);
        $hasher = $this->_registry->Auth->authenticationProvider()->passwordHasher();
        $data = json_encode([
            'hash' => $hasher->hash($username . $time),
            'time' => $time,
            'username' => $username,
        ]);

        $mac = hash_hmac('sha256', $data, Configure::read('Security.salt'));

        return compact('mac', 'data');
    }

    /**
     * onAdminLoginSuccessful
     *
     * @return bool
     */
    public function onAdminLoginSuccessful(Event $event)
    {
        $request = $event->getSubject()->request;
        $remember = $request->getData('remember');
        $expires = Configure::read('Access Control.autoLoginDuration');
        if (strtotime($expires) === false) {
            $expires = '+1 week';
        }
        if ($request->is('post') && $remember) {
            $data = $this->_cookie($request);
            $this->_registry->Cookie->write($this->getConfig('cookieName'), $data);
        }

        return true;
    }

    /**
     * onAdminLogoutSuccessful
     *
     * @return bool
     */
    public function onAdminLogoutSuccessful($event)
    {
        $this->_registry->Cookie->delete($this->getConfig('cookieName'));

        return true;
    }
}
