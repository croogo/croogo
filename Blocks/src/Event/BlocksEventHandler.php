<?php

namespace Croogo\Blocks\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Hash;
use Croogo\Croogo\Croogo;
use Croogo\Croogo\Utility\StringConverter;

/**
 * BlocksEventHandler
 *
 * @package  Croogo.Blocks.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksEventHandler implements EventListenerInterface {

/**
 * implementedEvents
 */
	public function implementedEvents() {
		return array(
			'Helper.Nodes.beforeSetNode' => array(
				'callable' => 'filterBlockShortcode',
			),
			'Helper.Regions.beforeSetBlock' => array(
				'callable' => 'filterBlockShortcode',
			),
			'Helper.Regions.afterSetBlock' => array(
				'callable' => 'filterBlockShortcode',
			),

			'Controller.Blocks.afterPublish' => array(
				'callable' => 'onAfterBulkProcess',
			),
			'Controller.Blocks.afterUnpublish' => array(
				'callable' => 'onAfterBulkProcess',
			),
			'Controller.Blocks.afterDelete' => array(
				'callable' => 'onAfterBulkProcess',
			),
			'Controller.Blocks.afterCopy' => array(
				'callable' => 'onAfterBulkProcess',
			),

		);
	}

/**
 * Filter block shortcode in node body, eg [block:snippet] and replace it with
 * the block content
 *
 * @param Event $event
 * @return void
 */
	public function filterBlockShortcode(Event $event) {
		static $converter = null;
		if (!$converter) {
			$converter = new StringConverter();
		}

		$View = $event->subject;
		$body = null;
		if (isset($event->data['content'])) {
			$body =& $event->data['content'];
		} elseif (isset($event->data['node'])) {
			$body =& $event->data['node']->body;
		}

		$parsed = $converter->parseString('block|b', $body, array(
			'convertOptionsToArray' => true,
		));

		$regex = '/\[(block|b):([A-Za-z0-9_\-]*)(.*?)\]/i';
		foreach ($parsed as $blockAlias => $config) {
			$block = $View->Regions->block($blockAlias);
			preg_match_all($regex, $body, $matches);
			if (isset($matches[2][0])) {
				$replaceRegex = '/' . preg_quote($matches[0][0]) . '/';
				$body = preg_replace($replaceRegex, $block, $body);
			}
		}

		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $View, array(
			'content' => &$body,
			'options' => array(),
		));
	}

/**
 * Clear Blocks related cache after bulk operation
 *
 * @param CakeEvent $event
 * @return void
 */
	public function onAfterBulkProcess($event) {
		Cache::clearGroup('blocks', 'croogo_blocks');
	}

}
