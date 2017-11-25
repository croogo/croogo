<?php

namespace Croogo\Example\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;

/**
 * Example Behavior
 *
 * @category Behavior
 * @package  Croogo
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleBehavior extends Behavior
{

/**
 * afterFind callback
 *
 * @param Model $model
 * @param array $results
 * @param bool $primary
 * @return array
 */
    public function beforeFind(Event $event, Query $query)
    {

        $query->formatResults(function ($results) {
            return $results->map(function($result) {
                if ($result instanceof Entity) {
                    $result->body .= '<p>[Modified by ExampleBehavior]</p>';
                }
                return $result;
            });
        });
    }

}
