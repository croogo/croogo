<?php

/**
 * Regions Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo.Blocks.View.Helper
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RegionsHelper extends AppHelper {

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
 * Show Blocks for a particular Region
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
		$elementOptions = $options['elementOptions'];

		$defaultElement = 'Blocks.block';
		$blocks = $this->_View->viewVars['blocks_for_layout'][$regionAlias];
		foreach ($blocks as $block) {
			$element = $block['Block']['element'];
			$exists = $this->_View->elementExists($element);
			$blockOutput = '';
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
			$enclosure = isset($block['Params']['enclosure']) ? $block['Params']['enclosure'] === "true" : true;
			if ($exists && $element != $defaultElement && $enclosure) {
				$block['Block']['body'] = $blockOutput;
				$block['Block']['element'] = null;
				$output .= $this->_View->element($defaultElement, compact('block'), $elementOptions);
			} else {
				$output .= $blockOutput;
			}
		}

		return $output;
	}

}
