<?php
declare(strict_types=1);

namespace Croogo\Core\Test\TestSuite;

use Cake\Cache\Engine\ArrayEngine;

/**
 * Cache engine that records every key it is asked to delete.
 *
 * Used to assert how often a cache configuration is invalidated.
 */
class DeleteSpyEngine extends ArrayEngine
{
    /**
     * Keys passed to delete() since the last reset
     *
     * @var array
     */
    public static $deletedKeys = [];

    /**
     * @param string $key Cache key
     * @return bool
     */
    public function delete(string $key): bool
    {
        static::$deletedKeys[] = $key;

        return parent::delete($key);
    }
}
