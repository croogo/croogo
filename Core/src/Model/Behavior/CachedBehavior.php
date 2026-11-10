<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Cache\Cache;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use InvalidArgumentException;

/**
 * Cached Behavior
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CachedBehavior extends Behavior
{
    protected array $_defaultConfig = [
        'groups' => []
    ];

    /**
     * afterSave callback
     * @return void
     */
    public function afterSave(): void
    {
        $this->_deleteCachedFiles();
    }

    /**
     * afterDelete callback
     *
     * @return void
     */
    public function afterDelete(): void
    {
        $this->_deleteCachedFiles();
    }

    /**
     * Delete cache files matching prefix
     *
     * @return void
     */
    protected function _deleteCachedFiles()
    {
        foreach ($this->getConfig('groups') as $group) {
            try {
                $configs = Cache::groupConfigs($group);
                foreach ($configs[$group] as $config) {
                    Cache::clearGroup($group, $config);
                }
            } catch (\Exception $e) {
                // Ignore invalid cache configs. Cake 4 rzuca
                // Cake\Cache\Exception\InvalidArgumentException (nie SPL) dla
                // niezdefiniowanej grupy — w Cake 3 zwracało pusto.
            }
        }
    }
}
