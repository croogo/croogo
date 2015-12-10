<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Collection\CollectionInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Croogo\Core\Link;

/**
 * Url Behavior
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UrlBehavior extends Behavior
{

    protected $_defaultConfig = [
        'url' => [],
        'fields' => [],
        'pass' => []
    ];

    public function beforeFind(Event $event, Query $query, $options)
    {
        $query->formatResults(function (CollectionInterface $results) {
            return $results->map(function (Entity $row) {
                // Base URL
                $url = $this->config('url');

                // Add named fields
                $fields = $this->config('fields');
                if (is_array($fields)) {
                    foreach ($fields as $field) {
                        if ($row->get($field)) {
                            $url[$field] = $row->get($field);
                        }
                    }
                }

                // Add passed fields
                $passed = $this->config('pass');
                if (is_array($passed)) {
                    foreach ($passed as $field) {
                        if ($row->get($field)) {
                            $url[] = $row->get($field);
                        }
                    }
                }

                $row->set('url', new Link($url));
                $row->dirty('url', false);

                return $row;
            });
        });
    }
}
