<?php
declare(strict_types=1);

namespace Croogo\Core\Routing\Route;

use Cake\Routing\Route\InflectedRoute as CakeInflectedRoute;
use Cake\Utility\Inflector;

/**
 * InflectedRoute z camelizacją akcji
 *
 * Jak Cake\Routing\Route\InflectedRoute (camelizuje controller/plugin), ale
 * dodatkowo camelizuje param `action` przy parsowaniu URL, np.
 * `reset_password` -> `resetPassword`, `create_file` -> `createFile`
 * (a `index` -> `index`). Przywraca zachowanie CakePHP 3, na którym opiera się
 * Croogo: akcje w URL są w formie underscore, a metody kontrolerów camelCase.
 *
 * @package Croogo.Core.Routing.Route
 */
class InflectedRoute extends CakeInflectedRoute
{
    /**
     * @inheritDoc
     */
    public function parse(string $url, string $method = ''): ?array
    {
        $params = parent::parse($url, $method);
        if ($params !== null && !empty($params['action'])) {
            $params['action'] = Inflector::variable($params['action']);
        }

        return $params;
    }
}
