<?php

namespace Croogo\Settings\Controller\Admin;

use Cake\Cache\Cache;
use Croogo\Core\Event\EventManager;

/**
 * Caches Controller
 *
 * @category Settings.Controller
 * @package  Croogo.Settings
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CachesController extends AppController
{

    public function index()
    {
        $caches = [];
        $configured = Cache::configured();
        if ($this->request->query('sort') === 'title') {
            sort($configured);
            if ($this->request->query('direction') !== 'asc') {
                $configured = array_reverse($configured);
            }
        }
        foreach ($configured as $cache) {
            $engine = Cache::engine($cache);
            $caches[$cache] = $engine;
        }
        $this->set(compact('caches'));
    }

    public function clear()
    {
        $config = $this->request->query('config') ?: 'all';
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
        return $this->redirect($this->request->referer());
    }

}
