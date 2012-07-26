<?php

/**
 * Regions Helper
 *
 * PHP version 5
 *
 * @category Helper
 * @package  Croogo.Regions
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
		$_options = array();
		$options = array_merge($_options, $options);

		$output = '';
		if (!$this->isEmpty($regionAlias)) {
			$blocks = $this->_View->viewVars['blocks_for_layout'][$regionAlias];
			foreach ($blocks as $block) {
				$plugin = false;
				if ($block['Block']['element'] != null) {
					if (strstr($block['Block']['element'], '.')) {
						$pluginElement = explode('.', $block['Block']['element']);
						$plugin  = $pluginElement[0];
						$element = $pluginElement[1];
					} else {
						$element = $block['Block']['element'];
					}
				} else {
					$element = 'block';
				}
				if ($plugin) {
					$blockOutput = $this->_View->element($element, array('block' => $block), array('plugin' => $plugin));
				} else {
					$blockOutput = $this->_View->element($element, array('block' => $block), array('plugin' => 'blocks'));
				}
				$enclosure = isset($block['Params']['enclosure']) ? $block['Params']['enclosure'] === "true" : true;
				if ($element != 'block' && $enclosure) {
					$block['Block']['body'] = $blockOutput;
					$block['Block']['element'] = null;
					$output .= $this->_View->element('Blocks.block', array('block' => $block));
				} else {
					$output .= $blockOutput;
				}
			}
		}

		return $output;
	}

}
