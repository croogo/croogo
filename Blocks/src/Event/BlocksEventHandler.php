<?php

namespace Croogo\Blocks\Event;

use Cake\Event\EventListener;
use Croogo\Lib\Utility\StringConverter;
/**
 * BlocksEventHandler
 *
 * @package  Croogo.Blocks.Event
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class BlocksEventHandler implements EventListener {

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
		);
	}

/**
 * Filter block shortcode in node body, eg [block:snippet] and replace it with
 * the block content
 *
 * @param Event $event
 * @return void
 */
	public function filterBlockShortcode($event) {
		static $converter = null;
		if (!$converter) {
			$converter = new StringConverter();
		}

		$View = $event->subject;
		$body = null;
		if (isset($event->data['content'])) {
			$body =& $event->data['content'];
		} elseif (isset($event->data['node'])) {
			$body =& $event->data['node'][key($event->data['node'])]['body'];
		}

		$parsed = $converter->parseString('block|b', $body, array(
			'convertOptionsToArray' => true,
		));

		$regex = '/\[(block|b):([A-Za-z0-9_\-]*)(.*?)\]/i';
		foreach ($parsed as $blockAlias => $config) {
			$path = '{s}.{n}.Block[alias=' . $blockAlias . ']';
			$block = Hash::extract($View->viewVars['blocks_for_layout'], $path);
			if (empty($block[0]['body'])) {
				continue;
			}
			preg_match_all($regex, $body, $matches);
			if (isset($matches[2][0])) {
				$replaceRegex = '/' . preg_quote($matches[0][0]) . '/';
				$body = preg_replace($replaceRegex, $block[0]['body'], $body);
			}
		}

		Croogo::dispatchEvent('Helper.Layout.beforeFilter', $View, array(
			'content' => &$body,
			'options' => array(),
		));
	}

}
