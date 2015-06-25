<?php

namespace Croogo\Blocks\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;
use Croogo\Croogo\Croogo;

/**
 * Regions Helper
 *
 * @category Helper
 * @package  Croogo.Blocks.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsHelper extends Helper {

/**
 * Region is empty
 *
 * returns true if Region has no Blocks.
 *
 * @param string $regionAlias Region alias
 * @return boolean
 */
	public function isEmpty($regionAlias) {
		if (isset($this->_View->viewVars['blocks_for_layout'][$regionAlias]) &&
			count($this->_View->viewVars['blocks_for_layout'][$regionAlias]) > 0) {
			return false;
		} else {
			return true;
		}
	}
	
/**
 * Show Block
 *
 * By default block is rendered using Blocks.block element. If `Block.element` is
 * set and exists, the render process will pass it through given element before wrapping
 * it inside the Blocks.block container. You disable the wrapping by setting
 * `enclosure=false` in the `params` field.
 *
 * @param string $blockAlias Block alias
 * @param array $options
 * @return string
 */
	public function block($blockAlias, $options = array()) {
		$output = '';
		if (!$blockAlias) {
			return $output;
		}

		$options = Hash::merge(array(
			'elementOptions' => array(),
		), $options);
		$elementOptions = $options['elementOptions'];

		$defaultElement = 'Blocks.block';
		$blocks = Hash::combine($this->_View->viewVars['blocks_for_layout'], '{s}.{n}.Block.alias', '{s}.{n}');
		if (!isset($blocks[$blockAlias])) {
			return $output;
		}
		$block = $blocks[$blockAlias];

		$element = $block['Block']['element'];
		$exists = $this->_View->elementExists($element);
		$blockOutput = '';

		Croogo::dispatchEvent('Helper.Regions.beforeSetBlock', $this->_View, array(
			'content' => &$block['Block']['body'],
		));

		if ($exists) {
			$blockOutput = $this->_View->element($element, compact('block'), $elementOptions);
		} else {
			if (!empty($element)) {
				$this->log(sprintf('Missing element `%s` in block `%s` (%s)',
					$block['Block']['element'],
					$block['Block']['alias'],
					$block['Block']['id']
				), LOG_WARNING);
			}
			$blockOutput = $this->_View->element($defaultElement, compact('block'), array('ignoreMissing' => true) + $elementOptions);
		}

		Croogo::dispatchEvent('Helper.Regions.afterSetBlock', $this->_View, array(
			'content' => &$blockOutput,
		));

		$enclosure = isset($block['Params']['enclosure']) ? $block['Params']['enclosure'] === "true" : true;
		if ($exists && $element != $defaultElement && $enclosure) {
			$block['Block']['body'] = $blockOutput;
			$block['Block']['element'] = null;
			$output .= $this->_View->element($defaultElement, compact('block'), $elementOptions);
		} else {
			$output .= $blockOutput;
		}

		return $output;
	}

/**
 * Show Blocks for a particular Region
 *
 * By default block are rendered using Blocks.block element. If `Block.element` is
 * set and exists, the render process will pass it through given element before wrapping
 * it inside the Blocks.block container. You disable the wrapping by setting
 * `enclosure=false` in the `params` field.
 *
 * @param string $regionAlias Region alias
 * @param array $options
 * @return string
 */
	public function blocks($regionAlias, $options = array()) {
		$output = '';
		if ($this->isEmpty($regionAlias)) {
			return $output;
		}

		$options = Hash::merge(array(
			'elementOptions' => array(),
		), $options);

		$defaultElement = 'Blocks.block';
		$blocks = $this->_View->viewVars['blocks_for_layout'][$regionAlias];
		foreach ($blocks as $block) {
			$output .= $this->block($block['Block']['alias'], $options);
		}

		return $output;
	}

}
