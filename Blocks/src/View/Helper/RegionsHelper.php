<?php

namespace Croogo\Blocks\View\Helper;

use Cake\Log\LogTrait;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Croogo\Blocks\Model\Entity\Block;
use Croogo\Core\Croogo;

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
class RegionsHelper extends Helper
{

    use LogTrait;

/**
 * Region is empty
 *
 * returns true if Region has no Blocks.
 *
 * @param string $regionAlias Region alias
 * @return boolean
 */
    public function isEmpty($regionAlias)
    {
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
 * @param Block $block Block
 * @param array $options
 * @return string
 */
    public function block(Block $block, $options = [])
    {
        $output = '';

        $options = Hash::merge([
            'elementOptions' => [],
        ], $options);
        $elementOptions = $options['elementOptions'];

        $defaultElement = 'Croogo/Blocks.block';

        $element = $block->element;
        $exists = $this->_View->elementExists($element);
        $blockOutput = '';

        Croogo::dispatchEvent('Helper.Regions.beforeSetBlock', $this->_View, [
            'content' => &$block->body,
        ]);

        if ($exists) {
            $blockOutput = $this->_View->element($element, compact('block'), $elementOptions);
        } else {
            if (!empty($element)) {
                $this->log(sprintf(
                    'Missing element `%s` in block `%s` (%s)',
                    $block->element,
                    $block->alias,
                    $block->id
                ), LOG_WARNING);
            }
            $blockOutput = $this->_View->element($defaultElement, compact('block'), ['ignoreMissing' => true] + $elementOptions);
        }

        Croogo::dispatchEvent('Helper.Regions.afterSetBlock', $this->_View, [
            'content' => &$blockOutput,
        ]);

        $enclosure = isset($block['Params']['enclosure']) ? $block['Params']['enclosure'] === "true" : true;
        if ($exists && $element != $defaultElement && $enclosure) {
            $block->body = $blockOutput;
            $block->element = null;
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
    public function blocks($regionAlias, $options = [])
    {
        $output = '';
        if ($this->isEmpty($regionAlias)) {
            return $output;
        }

        $options = Hash::merge([
            'elementOptions' => [],
        ], $options);

        $defaultElement = 'Croogo/Blocks.block';
        $blocks = $this->_View->viewVars['blocks_for_layout'][$regionAlias];
        foreach ($blocks as $block) {
            $output .= $this->block($block, $options);
        }

        return $output;
    }
}
