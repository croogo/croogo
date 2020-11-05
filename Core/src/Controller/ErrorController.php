<?php
declare(strict_types=1);

namespace Croogo\Core\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Routing\Router;

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.  This is based
 * on CakePHP's own CakeErrorController with the following differences:
 * - loads its own set of components and helpers
 * - aware of Site.theme and Site.admin_theme
 *
 * @category Controllers
 * @package  Croogo.Croogo.Controller
 * @version  1.0
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
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
class ErrorController extends \Cake\Controller\ErrorController implements HookableComponentInterface
{
    use HookableComponentTrait;

    public function initialize(): void
    {
        $this->_dispatchBeforeInitialize();

        $eventManager = $this->getEventManager();
        if (isset($this->Auth)) {
            $eventManager->off($this->Auth);
        }
        if (isset($this->Security)) {
            $eventManager->off($this->Security);
        }

        parent::initialize();
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if ($this->getRequest()->is('json')) {
            return;
        }
        $viewBuilder = $this->viewBuilder();
        $viewBuilder->setClassName('Croogo/Core.Croogo');
        if ($this->getRequest()->getParam('prefix') === 'Admin') {
            $adminTheme = Configure::read('Site.admin_theme');
            if ($adminTheme) {
                $viewBuilder->setTheme($adminTheme);
            }
            if (!$this->getRequest()->is('ajax')) {
                $viewBuilder->setLayout('admin_full');
            }
        } elseif (Configure::read('Site.theme')) {
            $viewBuilder->setTheme(Configure::read('Site.theme'));
        }
    }
}
