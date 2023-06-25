<?php
declare(strict_types=1);

namespace Croogo\Settings\Controller\Admin;

use Cake\Cache\Cache;

/**
 * Caches Controller
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
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
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class CachesController extends AppController
{

    public function index()
    {
        $caches = [];
        $configured = Cache::configured();
        if ($this->getRequest()->getquery('sort') === 'title') {
            sort($configured);
            if ($this->getRequest()->getQuery('direction') !== 'asc') {
                $configured = array_reverse($configured);
            }
        }
        foreach ($configured as $cache) {
            $engine = Cache::pool($cache);
            $caches[$cache] = $engine;
        }
        $this->set(compact('caches'));
    }

    public function clear()
    {
        $config = $this->getRequest()->getQuery('config') ?: 'all';
        if ($config === 'all') {
            $result = Cache::clearAll();
        } else {
            $result = Cache::clear(false, $config);
        }
        if ($result) {
            $this->Flash->success(__d('croogo', "Cache '%s' cleared", $config));
        } else {
            $this->Flash->warning(__d('croogo', 'Failed clearing cache'));
        }

        return $this->redirect($this->getRequest()->referer());
    }
}
