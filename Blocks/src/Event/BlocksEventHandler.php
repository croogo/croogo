<?php

namespace Croogo\Blocks\Event;

use Cake\Cache\Cache;
use Cake\Datasource\ModelAwareTrait;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;
use Croogo\Core\Croogo;
use Croogo\Core\Utility\StringConverter;

/**
 * BlocksEventHandler
 *
 * @package  Croogo.Blocks.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksEventHandler implements EventListenerInterface
{
    use LocatorAwareTrait;
    use ModelAwareTrait;

    /**
     * BlocksEventHandler constructor.
     */
    public function __construct()
    {
        $this->modelFactory('Table', [$this->tableLocator(), 'get']);
    }

    /**
 * implementedEvents
 */
    public function implementedEvents()
    {
        return [
            'Helper.Nodes.beforeSetNode' => [
                'callable' => 'filterBlockShortcode',
            ],
            'Helper.Regions.beforeSetBlock' => [
                'callable' => 'filterBlockShortcode',
            ],
            'Helper.Regions.afterSetBlock' => [
                'callable' => 'filterBlockShortcode',
            ],

            'Controller.Blocks.afterPublish' => [
                'callable' => 'onAfterBulkProcess',
            ],
            'Controller.Blocks.afterUnpublish' => [
                'callable' => 'onAfterBulkProcess',
            ],
            'Controller.Blocks.afterDelete' => [
                'callable' => 'onAfterBulkProcess',
            ],
            'Controller.Blocks.afterCopy' => [
                'callable' => 'onAfterBulkProcess',
            ],

        ];
    }

/**
 * Filter block shortcode in node body, eg [block:snippet] and replace it with
 * the block content
 *
 * @param Event $event
 * @return void
 */
    public function filterBlockShortcode(Event $event)
    {
        $this->loadModel('Croogo/Blocks.Blocks');
        static $converter = null;
        if (!$converter) {
            $converter = new StringConverter();
        }

        $View = $event->subject;
        $body = null;
        $data = $event->getData();
        if (isset($data['content'])) {
            $body =& $data['content'];
        } elseif (isset($data['node'])) {
            $body =& $data['node']->body;
        }

        $parsed = $converter->parseString('block|b', $body, [
            'convertOptionsToArray' => true,
        ]);

        $regex = '/\[(block|b):([A-Za-z0-9_\-]*)(.*?)\]/i';
        foreach ($parsed as $blockAlias => $config) {
            $block = $this->Blocks->findByAlias($blockAlias)->first();
            if (!$block) {
                continue;
            }
            $block = $View->Regions->block($block);
            preg_match_all($regex, $body, $matches);
            if (isset($matches[2][0])) {
                $body = str_replace($matches[0][0], $block, $body);
            }
        }

        Croogo::dispatchEvent('Helper.Layout.beforeFilter', $View, [
            'content' => &$body,
            'options' => [],
        ]);
    }

/**
 * Clear Blocks related cache after bulk operation
 *
 * @param CakeEvent $event
 * @return void
 */
    public function onAfterBulkProcess($event)
    {
        Cache::clearGroup('blocks', 'croogo_blocks');
    }
}
